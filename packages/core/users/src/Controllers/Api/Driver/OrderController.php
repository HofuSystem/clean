<?php

namespace Core\Users\Controllers\Api\Driver;

use App\Http\Controllers\Controller;
use Core\Notification\Models\Notification;
use Core\Orders\Models\Order;
use Core\Orders\Models\OrderReport;
use Core\Orders\Models\OrderRepresentative;
use Core\Orders\Support\OrderPaymentMath;
use Core\Settings\Services\SettingsService;
use Core\Settings\Traits\ApiResponse;
use Core\Users\DataResources\Driver\OrderDetailsResource;
use Core\Users\DataResources\Driver\OrderResource;
use Core\Users\Requests\Api\OrderReportRequest;
use Illuminate\Http\Request;


/**
 * @group 2. Driver App
 * @subgroup Orders
 */
class OrderController extends Controller
{
    public const EAGER_LOADS = [
        'orderRepresentatives.address.city.translations',
        'orderRepresentatives.address.district.translations',
        'client.profile.city.translations',
        'client.profile.district.translations',
        'company',
        'coupon',
        'items.product.category.translations',
        'items.product.subCategory.translations',
        'items.product.prices',
        'items.product.translations',
        'transactions',
        'reports'
    ];

    use ApiResponse;
    public function index(Request $request)
    {
        $orders = Order::with(self::EAGER_LOADS)
            ->where('is_admin_accepted', true)
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
        $order = Order::with(self::EAGER_LOADS)->find($order_id);
        
        if (!$order) {
            return $this->returnErrorMessage(trans('order_not_found'), [], [], 404);
        }

        $order->load(self::EAGER_LOADS);
        return $this->returnData('data', ['data' =>  new OrderDetailsResource($order)]);
    }

    // first step to clothes
    // first driver accept order
    public function receiving_driver_accept($order_id)
    {
        $order = Order::where('status', 'pending')->hasRepresentatives('receiver', auth('api')->id())->find($order_id);
        if (! $order) {
            return $this->returnErrorMessage(trans('order_stage_required', ['status' => trans('pending')]), [], [], 422);
        }
        $order->update(['status' => 'receiving_driver_accepted', 'order_status_times' => ['receiving_driver_accepted' => [date("Y-m-d H:i"), auth('api')->user()->phone]]]);


        $senderData = ['id' => auth('api')->user()->id, 'fullname' => auth('api')->user()->fullname, 'phone' => (string)auth('api')->user()->phone, 'image' => (string)auth('api')->user()->avatarUrl];
        $clientLang = $order->client?->default_language ?: 'ar';
        $data = [
            'key'               => "order",
            'key_id'            => $order->id,
            'status'            => $order->status,
            'title'             => trans('title_first_driver_accepte_order', ['order_id' => $order->reference_id], $clientLang),
            'body'              => trans('body_first_driver_accepte_order', ['order_id' => $order->reference_id, 'driver_name' => auth('api')->user()->fullname], $clientLang),
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

        $order->load(self::EAGER_LOADS);
        return $this->returnData(trans('order_has_been_successfully_accepted'), ['data' =>  new OrderDetailsResource($order)]);
    }

    // second step to clothes
    public function delivered_to_admin($order_id)
    {
        $order = Order::where('status', 'receiving_driver_accepted')->hasRepresentatives('receiver', auth('api')->id())->find($order_id);
        if (! $order) {
            return $this->returnErrorMessage(trans('order_stage_required', ['status' => trans('receiving_driver_accepted')]), [], [], 422);
        }
        $order->update(['status' => 'order_has_been_delivered_to_admin', 'order_status_times' => ['order_has_been_delivered_to_admin' => [date("Y-m-d H:i"), auth('api')->user()->phone]]]);
        $order->items()->update(['is_picked' => true]);
        $senderData = ['id' => auth('api')->user()->id, 'fullname' => auth('api')->user()->fullname, 'phone' => (string)auth('api')->user()->phone, 'image' => (string)auth('api')->user()->avatarUrl];
        $clientLang = $order->client?->default_language ?: 'ar';
        $data = [
            'key'               => "order",
            'key_id'            => $order->id,
            'status'            => $order->status,
            'title'             => trans('title_order_has_been_delivered_to_admin', ['order_id' => $order->reference_id], $clientLang),
            'body'              => trans('body_order_has_been_delivered_to_admin', ['order_id' => $order->reference_id, 'driver_name' => auth('api')->user()->fullname], $clientLang),
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
        $order->load(self::EAGER_LOADS);
        return $this->returnData(trans('order_has_been_successfully_delivered'), ['data' =>  new OrderDetailsResource($order)]);
    }

    // 3 step to clothes
    //  استلام مندوب التوصبل للطلب
    public function ready_to_delivered($order_id)
    {
        $order = Order::hasRepresentatives('delivery', auth('api')->id())
            ->where('status', 'order_has_been_delivered_to_admin')->find($order_id);
        if (! $order) {
            return $this->returnErrorMessage(trans('order_stage_required', ['status' => trans('order_has_been_delivered_to_admin')]), [], [], 422);
        }
        $order->update(['status' => 'ready_to_delivered', 'order_status_times' => ['ready_to_delivered' => [date("Y-m-d H:i"), auth('api')->user()->phone]]]);

        $senderData = ['id' => auth('api')->user()->id, 'fullname' => auth('api')->user()->fullname, 'phone' => (string)auth('api')->user()->phone, 'image' => (string)auth('api')->user()->avatarUrl];
        $clientLang = $order->client?->default_language ?: 'ar';

        $data = [
            'key'               => "order",
            'key_id'            => $order->id,
            'status'            => $order->status,
            'title'             => trans('title_ready_to_delivered', ['order_id' => $order->reference_id], $clientLang),
            'body'              => trans('body_ready_to_delivered', [
                'order_id' => $order->reference_id,
                'driver_name' => auth('api')->user()->fullname,
                'amount' => number_format(OrderPaymentMath::remainingToCollect($order->total_price, $order->paid), 2, '.', ''),
            ], $clientLang),
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
        $order->load(self::EAGER_LOADS);
        return $this->returnData(trans('order_has_been_successfully_accepted'), ['data' =>  new OrderDetailsResource($order)]);
    }

    public function in_the_way($order_id)
    {
        $order = Order::hasRepresentatives('delivery', auth('api')->id())
            ->where('status', 'ready_to_delivered')->find($order_id);
        if (! $order) {
            return $this->returnErrorMessage(trans('order_stage_required', ['status' => trans('ready_to_delivered')]), [], [], 422);
        }
        $order->update(['status' => 'in_the_way', 'order_status_times' => ['in_the_way' => [date("Y-m-d H:i"), auth('api')->user()->phone]]]);
        $senderData = ['id' => auth('api')->user()->id, 'fullname' => auth('api')->user()->fullname, 'phone' => (string)auth('api')->user()->phone, 'image' => (string)auth('api')->user()->avatarUrl];
        $clientLang = $order->client?->default_language ?: 'ar';

        $data = [
            'key'               => "order",
            'key_id'            => $order->id,
            'status'            => $order->status,
            'title'             => trans('title_in_the_way', ['order_id' => $order->reference_id], $clientLang),
            'body'              => trans('body_in_the_way', ['order_id' => $order->reference_id, 'driver_name' => auth('api')->user()->fullname], $clientLang),
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
        $order->load(self::EAGER_LOADS);
        return $this->returnData(trans('order_has_been_successfully_change_status'), ['data' =>  new OrderDetailsResource($order)]);
    }

    public function delivered($order_id)
    {
        $order = Order::hasRepresentatives('delivery', auth('api')->id())
            ->where('status', 'in_the_way')->find($order_id);
        if (! $order) {
            return $this->returnErrorMessage(trans('order_stage_required', ['status' => trans('in_the_way')]), [], [], 422);
        }
        $order->update(['status' => 'delivered', 'order_status_times' => ['delivered' => [date("Y-m-d H:i"), auth('api')->user()->phone]]]);
        $senderData = ['id' => auth('api')->user()->id, 'fullname' => auth('api')->user()->fullname, 'phone' => (string)auth('api')->user()->phone, 'image' => (string)auth('api')->user()->avatarUrl];
        $clientLang = $order->client?->default_language ?: 'ar';
        $data = [
            'key'               => "order",
            'key_id'            => $order->id,
            'status'            => $order->status,
            'title'             => trans('title_delivered', ['order_id' => $order->reference_id], $clientLang),
            'body'              => trans('body_delivered', ['order_id' => $order->reference_id, 'driver_name' => auth('api')->user()->fullname], $clientLang),
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
        $order->load(self::EAGER_LOADS);
        return $this->returnData(trans('order_has_been_successfully_delivered'), ['data' =>  new OrderDetailsResource($order)]);
    }

    public function finished($order_id)
    {
        $order = Order::hasRepresentatives('delivery', auth('api')->id())->where('status', 'delivered')->find($order_id);
        if (! $order) {
            return $this->returnErrorMessage(trans('order_stage_required', ['status' => trans('delivered')]), [], [], 422);
        }
        app(\Core\Orders\Services\OrdersService::class)->completeOrderFinancials($order->id);
        $order->update(['status' => 'finished', 'order_status_times' => ['finished' => [date("Y-m-d H:i"), auth('api')->user()->phone]]]);

        $senderData = ['id' => auth('api')->user()->id, 'fullname' => auth('api')->user()->fullname, 'phone' => (string)auth('api')->user()->phone, 'image' => (string)auth('api')->user()->avatarUrl];
        $clientLang = $order->client?->default_language ?: 'ar';
        $data = [
            'key'               => "order",
            'key_id'            => $order->id,
            'status'            => $order->status,
            'title'             => trans('title_finished', ['order_id' => $order->reference_id], $clientLang),
            'body'              => trans('body_finished', ['order_id' => $order->reference_id, 'driver_name' => auth('api')->user()->fullname], $clientLang),
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
        $order->load(self::EAGER_LOADS);
        return $this->returnData(trans('order_has_been_successfully_finished'), ['data' =>  new OrderDetailsResource($order)]);
    }

    public function order_report($order_id, OrderReportRequest $request)
    {
        $assigned = Order::hasRepresentatives(['receiver', 'delivery'], auth('api')->id())->whereKey($order_id)->exists();
        if (! $assigned) { return $this->returnErrorMessage(trans('You are not assigned to this order'), [], [], 403); }
        if ($request->type == 'report') {
            $order = Order::FindOrFail($order_id);
            $previousStatus = $order->status === 'issue'
                ? $order->status_before_issue
                : $order->status;
            $issueData = [
                'status' => 'issue',
                'driver_has_problem' => true,
                'order_status_times' => ['issue' => [date("Y-m-d H:i"), auth('api')->user()->phone,$request->report_reason_id]],
            ];
            if ($order->status !== 'issue') {
                $issueData['status_before_issue'] = $order->status;
            }
            $order->update($issueData);
            $report = OrderReport::create($request->validated() + ['user_id' => auth('api')->id(), 'order_id' => $order->id]);
            $failureEvent = in_array($previousStatus, ['pending', 'receiving_driver_accepted'], true)
                ? 'pickup_failed'
                : 'delivery_failed';
            app(\Core\Orders\Services\AnalyticsOutboxService::class)->record($failureEvent, $order, [
                'fail_reason' => (string) ($report->reportReason?->name ?? $report->report_reason_id),
            ], 'report:' . $report->id);
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
        $order = Order::hasRepresentatives(['receiver', 'delivery'], auth('api')->id())->findOrFail($order_id);
        $order->update(['get_money_method' => $request->pay_type_method]);
        return $this->returnSuccessMessage(trans('send successfully'));
    }
}
