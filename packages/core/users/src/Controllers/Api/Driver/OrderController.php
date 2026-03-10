<?php

namespace Core\Users\Controllers\Api\Driver;

use App\Http\Controllers\Controller;
use Core\Notification\Models\Notification;
use Core\Orders\Models\Order;
use Core\Orders\Models\OrderReport;
use Core\Orders\Models\OrderRepresentative;
use Core\Settings\Services\SettingsService;
use Core\Settings\Traits\ApiResponse;
use Core\Users\DataResources\Driver\OrderDetailsResource;
use Core\Users\DataResources\Driver\OrderResource;
use Core\Users\Models\Point;
use Core\Users\Requests\Api\OrderReportRequest;
use Core\Wallet\Models\WalletTransaction;
use Illuminate\Http\Request;


class OrderController extends Controller
{
    use ApiResponse;
    public function index(Request $request)
    {
        $orders = Order::where('is_admin_accepted', true)
            ->when($request->status_type == 'receipt', function ($q) use ($request) {
                $q->hasRepresentatives('receiver', auth('api')->id())
                    ->whereIn('orders.type', ['clothes', 'fastorder'])
                    ->whereIn('orders.status', ['pending', 'receiving_driver_accepted', 'order_has_been_delivered_to_admin'])
                    ->whereDoesntHave('orderRepresentatives', function ($q) use ($request) {
                        $q->where('type', 'delivery')
                        ->has('representative');
                    })
                    ->leftJoin('order_representatives as or_receiver', function ($join) {
                        $join->on('orders.id', '=', 'or_receiver.order_id')
                            ->where('or_receiver.type', '=', 'receiver')
                            ->where('or_receiver.representative_id', '=', auth('api')->id());
                    })
                    ->orderBy('or_receiver.date', 'asc')
                    ->orderBy('or_receiver.time', 'asc')
                    ->select('orders.*');
            })->when($request->status_type == 'delivery', function ($q) use ($request) {
                $q->hasRepresentatives('delivery', auth('api')->id())
                    ->whereIn('orders.type', ['clothes', 'sales', 'fastorder'])
                    ->whereIn('orders.status', ['order_has_been_delivered_to_admin', 'ready_to_delivered', 'in_the_way', 'delivered', 'finished'])
                    ->leftJoin('order_representatives as or_delivery', function ($join) {
                        $join->on('orders.id', '=', 'or_delivery.order_id')
                            ->where('or_delivery.type', '=', 'delivery')
                            ->where('or_delivery.representative_id', '=', auth('api')->id());
                    })
                    ->whereNot(function ($query) {
                        $query->where('orders.status', '=', 'finished')
                            ->where('or_delivery.date', '<', now()->subDays(2)->toDateString());
                    })
                    ->orderBy('or_delivery.date', 'asc')
                    ->orderBy('or_delivery.time', 'asc')
                    ->select('orders.*');
            })->when(!$request->status_type, function ($q) {
                $q->latest();
            })->paginate(10);
            
        return $this->returnData('data', OrderResource::collection($orders)->response()->getData(true));
    }

    public function show($order_id)
    {
        $order = Order::with('transactions')->findOrFail($order_id);
        return $this->returnData('data', ['data' =>  new OrderDetailsResource($order)]);
    }

    // first step to clothes
    // first driver accept order
    public function receiving_driver_accept($order_id)
    {
        $order = Order::where('status', 'pending')->hasRepresentatives('receiver', auth('api')->id())->find($order_id);
        if (! $order) {
            return $this->returnErrorMessage(' يجب ان يكون الطلب جديد وتم الموافقه عليه من الاداره', [], [], 422);
        }
        $order->update(['status' => 'receiving_driver_accepted', 'order_status_times' => ['receiving_driver_accepted' => [date("Y-m-d H:i"), auth('api')->user()->phone]]]);


        $senderData = ['id' => auth('api')->user()->id, 'fullname' => auth('api')->user()->fullname, 'phone' => (string)auth('api')->user()->phone, 'image' => (string)auth('api')->user()->avatarUrl];
        $data = [
            'key'               => "order",
            'key_id'            => $order->id,
            'status'            => $order->status,
            'title'             => trans('title_first_driver_accepte_order', ['order_id' => $order->id], 'ar'),
            'body'              => trans('body_first_driver_accepte_order', ['order_id' => $order->id, 'driver_name' => auth('api')->user()->fullname], 'ar'),
            'order_id'          => $order->id,
            'order_driver_type' => $order->status == 'pending' || $order->status == 'receiving_driver_accepted' ? 'receipt' : 'delivery',
            'sender_data'       => $senderData,
            'reference'         => $order->reference_id,
        ];
        $notifyTypes       = SettingsService::getDataBaseSetting('notify_client_using');
        if (isset($notifyTypes) and !empty($notifyTypes)) {
            Notification::create([
                'types'     => json_encode($notifyTypes),
                'for'       => 'users',
                'for_data'  => json_encode([$order->client_id]),
                'payload'   => json_encode($data),
                'title'     => $data['title'],
                'body'      => $data['body'],
                'sender_id' => auth('api')->user()->id,
                'order_id'  => $order->id,
            ]);
        }

        return $this->returnData(trans('order_has_been_successfully_accepted'), ['data' =>  new OrderDetailsResource($order)]);
    }

    // second step to clothes
    public function delivered_to_admin($order_id)
    {
        $order = Order::where('status', 'receiving_driver_accepted')->hasRepresentatives('receiver', auth('api')->id())->find($order_id);
        if (! $order) {
            return $this->returnErrorMessage(' يجب ان يكون الطلب جديد وتم الموافقه عليه من الاداره', [], [], 422);
        }
        $order->update(['status' => 'order_has_been_delivered_to_admin', 'order_status_times' => ['order_has_been_delivered_to_admin' => [date("Y-m-d H:i"), auth('api')->user()->phone]]]);
        $order->items()->update(['is_picked' => true]);
        $senderData = ['id' => auth('api')->user()->id, 'fullname' => auth('api')->user()->fullname, 'phone' => (string)auth('api')->user()->phone, 'image' => (string)auth('api')->user()->avatarUrl];
        $data = [
            'key'               => "order",
            'key_id'            => $order->id,
            'status'            => $order->status,
            'title'             => trans('title_order_has_been_delivered_to_admin', ['order_id' => $order->id], 'ar'),
            'body'              => trans('body_order_has_been_delivered_to_admin', ['order_id' => $order->id, 'driver_name' => auth('api')->user()->fullname], 'ar'),
            'order_id'          => $order->id,
            'order_driver_type' => $order->status == 'pending' || $order->status == 'receiving_driver_accepted' ? 'receipt' : 'delivery',
            'sender_data'       => $senderData,
        ];
        $notifyTypes       = SettingsService::getDataBaseSetting('notify_client_using');
        if (isset($notifyTypes) and !empty($notifyTypes)) {
            Notification::create([
                'types'     => json_encode($notifyTypes),
                'for'       => 'users',
                'for_data'  => json_encode([$order->client_id]),
                'payload'   => json_encode($data),
                'title'     => $data['title'],
                'body'      => $data['body'],
                'sender_id' => auth('api')->user()->id,
                'order_id'  => $order->id,
            ]);
        }
        return $this->returnData(trans('order_has_been_successfully_delivered'), ['data' =>  new OrderDetailsResource($order)]);
    }

    // 3 step to clothes
    //  استلام مندوب التوصبل للطلب
    public function ready_to_delivered($order_id)
    {
        $order = Order::hasRepresentatives('delivery', auth('api')->id())->find($order_id);
        if (! $order) {
            return $this->returnErrorMessage(' يجب ان يكون الطلب جديد وتم الموافقه عليه من الاداره', [], [], 422);
        }
        $order->update(['status' => 'ready_to_delivered', 'order_status_times' => ['ready_to_delivered' => [date("Y-m-d H:i"), auth('api')->user()->phone]]]);

        $senderData = ['id' => auth('api')->user()->id, 'fullname' => auth('api')->user()->fullname, 'phone' => (string)auth('api')->user()->phone, 'image' => (string)auth('api')->user()->avatarUrl];

        $data = [
            'key'               => "order",
            'key_id'            => $order->id,
            'status'            => $order->status,
            'title'             => trans('title_in_the_way', ['order_id' => $order->id], 'ar'),
            'body'              => trans('body_in_the_way', ['order_id' => $order->id, 'driver_name' => auth('api')->user()->fullname], 'ar'),
            'order_id'          => $order->id,
            'order_driver_type' => $order->status == 'pending' || $order->status == 'receiving_driver_accepted' ? 'receipt' : 'delivery',
            'sender_data'       => $senderData,
        ];
        $notifyTypes       = SettingsService::getDataBaseSetting('notify_client_using');
        if (isset($notifyTypes) and !empty($notifyTypes)) {
            Notification::create([
                'types'     => json_encode($notifyTypes),
                'for'       => 'users',
                'for_data'  => json_encode([$order->client_id]),
                'payload'   => json_encode($data),
                'title'     => $data['title'],
                'body'      => $data['body'],
                'sender_id' => auth('api')->user()->id,
                'order_id'  => $order->id,
            ]);
        }
        return $this->returnData(trans('order_has_been_successfully_accepted'), ['data' =>  new OrderDetailsResource($order)]);
    }

    public function in_the_way($order_id)
    {
        $order = Order::hasRepresentatives('delivery', auth('api')->id())->find($order_id);
        if (! $order) {
            return $this->returnErrorMessage(' يجب ان يكون الطلب جديد وتم الموافقه عليه من الاداره', [], [], 422);
        }
        $order->update(['status' => 'in_the_way', 'order_status_times' => ['in_the_way' => [date("Y-m-d H:i"), auth('api')->user()->phone]]]);
        $senderData = ['id' => auth('api')->user()->id, 'fullname' => auth('api')->user()->fullname, 'phone' => (string)auth('api')->user()->phone, 'image' => (string)auth('api')->user()->avatarUrl];

        $data = [
            'key'               => "order",
            'key_id'            => $order->id,
            'status'            => $order->status,
            'title'             => trans('title_ready_to_pay', ['order_id' => $order->id], 'ar'),
            'body'              => trans('body_ready_to_pay', ['order_id' => $order->id, 'driver_name' => auth('api')->user()->fullname], 'ar'),
            'order_id'          => $order->id,
            'order_driver_type' => $order->status == 'pending' || $order->status == 'receiving_driver_accepted' ? 'receipt' : 'delivery',
            'sender_data'       => $senderData,
        ];
        $notifyTypes       = SettingsService::getDataBaseSetting('notify_client_using');
        if (isset($notifyTypes) and !empty($notifyTypes)) {
            Notification::create([
                'types'     => json_encode($notifyTypes),
                'for'       => 'users',
                'for_data'  => json_encode([$order->client_id]),
                'payload'   => json_encode($data),
                'title'     => $data['title'],
                'body'      => $data['body'],
                'sender_id' => auth('api')->user()->id,
                'order_id'  => $order->id,
            ]);
        }
        return $this->returnData(trans('order_has_been_successfully_change_status'), ['data' =>  new OrderDetailsResource($order)]);
    }

    public function delivered($order_id)
    {
        $order = Order::hasRepresentatives('delivery', auth('api')->id())->find($order_id);
        if (! $order) {
            return $this->returnErrorMessage(' يجب ان يكون الطلب جديد وتم الموافقه عليه من الاداره', [], [], 422);
        }
        $order->update(['status' => 'delivered', 'order_status_times' => ['delivered' => [date("Y-m-d H:i"), auth('api')->user()->phone]]]);
        $senderData = ['id' => auth('api')->user()->id, 'fullname' => auth('api')->user()->fullname, 'phone' => (string)auth('api')->user()->phone, 'image' => (string)auth('api')->user()->avatarUrl];
        $data = [
            'key'               => "order",
            'key_id'            => $order->id,
            'status'            => $order->status,
            'title'             => trans('title_delivered', ['order_id' => $order->id], 'ar'),
            'body'              => trans('body_delivered', ['order_id' => $order->id, 'driver_name' => auth('api')->user()->fullname], 'ar'),
            'order_id'          => $order->id,
            'order_driver_type' => $order->status == 'pending' || $order->status == 'receiving_driver_accepted' ? 'receipt' : 'delivery',
            'sender_data'       => $senderData,
        ];
        $notifyTypes       = SettingsService::getDataBaseSetting('notify_client_using');
        if (isset($notifyTypes) and !empty($notifyTypes)) {
            Notification::create([
                'types'     => json_encode($notifyTypes),
                'for'       => 'users',
                'for_data'  => json_encode([$order->client_id]),
                'payload'   => json_encode($data),
                'title'     => $data['title'],
                'body'      => $data['body'],
                'sender_id' => auth('api')->user()->id,
                'order_id'  => $order->id,
            ]);
        }
        if($order->paid > $order->total_price){
            $remainingAmount = $order->paid - $order->total_price;
            $walletModel = WalletTransaction::create([
                'title'             => "remaining amount for order : " . $order->reference_id,
                'amount'            => round($remainingAmount),
                'type'              => 'deposit',
                'status'            => 'accepted',
                'transaction_type'  => 'remaining_amount',
                'user_id'           => $order->client_id,
                'order_id'          => $order->id,
            ]);
            $order->transactions()->create([
                'type'                  => 'wallet',
                'amount'                => -$remainingAmount,
                'wallet_transaction_id' => $walletModel->id,
                'notes'                 => 'remaining amount for order : ' . $order->reference_id,
            ]);
            $data = [
                'key'               => "order",
                'key_id'            => $order->id,
                'status'            => $order->status,
                'title'             => trans('title_remaining_amount', ['order_id' => $order->id,'amount' => $order->paid - $order->total_price], 'ar'),
                'body'              => trans('body_remaining_amount', ['order_id' => $order->id, 'amount' => $order->paid - $order->total_price], 'ar'),
                'order_id'          => $order->id,
                'order_driver_type' => $order->status == 'pending' || $order->status == 'receiving_driver_accepted' ? 'receipt' : 'delivery',
                'sender_data'       => $senderData,
            ];
            if (isset($notifyTypes) and !empty($notifyTypes)) {
                Notification::create([
                    'types'     => json_encode($notifyTypes),
                    'for'       => 'users',
                    'for_data'  => json_encode([$order->client_id]),
                    'payload'   => json_encode($data),
                    'title'     => $data['title'],
                    'body'      => $data['body'],
                    'sender_id' => auth('api')->user()->id,
                    'order_id'  => $order->id,
                ]);
            }
        }
        return $this->returnData(trans('order_has_been_successfully_delivered'), ['data' =>  new OrderDetailsResource($order)]);
    }

    public function finished($order_id)
    {
        $order = Order::hasRepresentatives('delivery', auth('api')->id())->where('status', 'delivered')->find($order_id);
        if (! $order) {
            return $this->returnErrorMessage(' يجب ان يكون الطلب جديد وتم الموافقه عليه من الاداره', [], [], 422);
        }
        $order->update(['status' => 'finished', 'order_status_times' => ['finished' => [date("Y-m-d H:i"), auth('api')->user()->phone]]]);

        $order->items()->update(['is_delivered' => true]);
        $firstOrder = Order::where('client_id', $order->client_id)
            ->orderBy('created_at', 'asc')
            ->first();
        if ($firstOrder->id == $order->id and $order->client->registerBy) {
            $referralUser     = $order->client->registerBy;
            $pointReferral    = SettingsService::getDataBaseSetting('referral_points');
            $referralUser->update(['earned_referral_points' => ($referralUser->earned_referral_points + $pointReferral)]);
            if ($pointReferral > 0) {
                Point::create([
                    'title'     => "referral gift points for user : " . $order->client->fullname,
                    'amount'    => round($pointReferral),
                    'operation' => 'deposit',
                    'user_id'   => $referralUser->id,
                ]);
            }
            $pointRiyals    = SettingsService::getDataBaseSetting('referral_riyals');
            $referralUser->update(['earned_referral_riyals' => ($referralUser->earned_referral_riyals + $pointRiyals)]);
            if ($pointRiyals > 0) {
                WalletTransaction::create([
                    'title'     => "referral gift riyals for user : " . $order->client->fullname,
                    'amount'    => round($pointRiyals),
                    'type'      => 'deposit',
                    'status'    => 'accepted',
                    'user_id'   => $referralUser->id,
                    'transaction_type' => 'promotional_add',
                    'order_id'        => $order->id
                ]);
            }
        }

        if ($order->cashback > 0) {
            $cashbackPercentage = $order->cashback;
            $cashbackAmount = $order->total_price * $cashbackPercentage / 100;
            WalletTransaction::create([
                'title'             => "cashback : " . $order->client->fullname,
                'amount'            => round($cashbackAmount),
                'type'              => 'deposit',
                'status'            => 'accepted',
                'transaction_type'  => 'cashback',
                'user_id'           => $order->client_id,
                'expired_at'        => now()->addDays(3),
                'order_id'          => $order->id
            ]);
        }
        $senderData = ['id' => auth('api')->user()->id, 'fullname' => auth('api')->user()->fullname, 'phone' => (string)auth('api')->user()->phone, 'image' => (string)auth('api')->user()->avatarUrl];
        $data = [
            'key'               => "order",
            'key_id'            => $order->id,
            'status'            => $order->status,
            'title'             => trans('title_finished', ['order_id' => $order->id], 'ar'),
            'body'              => trans('body_finished', ['order_id' => $order->id, 'driver_name' => auth('api')->user()->fullname], 'ar'),
            'order_id'          => $order->id,
            'order_driver_type' => $order->status == 'pending' || $order->status == 'receiving_driver_accepted' ? 'receipt' : 'delivery',
            'sender_data'       => $senderData,
        ];
        $notifyTypes       = SettingsService::getDataBaseSetting('notify_client_using');
        if (isset($notifyTypes) and !empty($notifyTypes)) {
            Notification::create([
                'types'     => json_encode($notifyTypes),
                'for'       => 'users',
                'for_data'  => json_encode([$order->client_id]),
                'payload'   => json_encode($data),
                'title'     => $data['title'],
                'body'      => $data['body'],
                'sender_id' => auth('api')->user()->id,
                'order_id'  => $order->id,
            ]);
        }
        $pointsPerSpentRiyal       = SettingsService::getDataBaseSetting('points_per_spent_riyal');
        if ($pointsPerSpentRiyal) {
            Point::create([
                'title'     => "for order : " . $order->reference_id,
                'amount'    => round($order->order_price * $pointsPerSpentRiyal),
                'operation' => 'deposit',
                'user_id'   => $order->client_id,
            ]);
            Notification::create([
                'types'    => json_encode(['apps']),
                'for'      => 'users',
                'for_data' => json_encode([$order->client_id]),
                'payload'  => json_encode([
                    'key'       => "order",
                    'key_id'    => $order->id,
                    'status'    => $order->status,
                    'title'     => trans('title_point_per_spent_riyal', ['order_id' => $order->id, 'points' => round($order->order_price * $pointsPerSpentRiyal)], 'ar'),
                    'body'      => trans('body_point_per_spent_riyal', ['order_id' => $order->id, 'points' => round($order->order_price * $pointsPerSpentRiyal), 'technical_name' => auth('api')->user()->fullname], 'ar'),
                ]),
                'title'     => trans('title_point_per_spent_riyal', ['order_id' => $order->id, 'points' => round($order->order_price * $pointsPerSpentRiyal)], 'ar'),
                'body'      => trans('body_point_per_spent_riyal', ['order_id' => $order->id, 'points' => round($order->order_price * $pointsPerSpentRiyal), 'technical_name' => auth('api')->user()->fullname], 'ar'),
                'sender_id' => auth('api')->user()->id,
                'order_id'  => $order->id,
            ]);
        }
        if($order->paid < $order->total_price){
            $cashAmount = $order->total_price - $order->paid;
            $order->transactions()->create([
                'type'                  => 'cash',
                'amount'                => $cashAmount,
                'notes'                 => 'cash amount for order : ' . $order->reference_id,
            ]);
        }

        return $this->returnData(trans('order_has_been_successfully_finished'), ['data' =>  new OrderDetailsResource($order)]);
    }

    public function order_report($order_id, OrderReportRequest $request)
    {
        if ($request->type == 'report') {
            $order = Order::FindOrFail($order_id);
            $order->update(['status' => 'issue', 'driver_has_problem' => true, 'order_status_times' => ['issue' => [date("Y-m-d H:i"), auth('api')->user()->phone,$request->report_reason_id]]]);
            OrderReport::create($request->validated() + ['user_id' => auth('api')->id(), 'order_id' => $order->id]);
        } else {
            $orderRep = OrderRepresentative::has('address')->where('order_id', $order_id)
                ->where('representative_id', auth('api')->id())->first();
            if ($orderRep) {
                $orderRep->address->updateQuietly(['description' => $request->desc_location]);
            }
        }
        return $this->returnSuccessMessage(trans('send successfully'));
    }


    public function get_money_method($order_id, Request $request)
    {
        $order = Order::FindOrFail($order_id);
        $order->update(['get_money_method' => $request->pay_type_method]);
        return $this->returnSuccessMessage(trans('send successfully'));
    }
}
