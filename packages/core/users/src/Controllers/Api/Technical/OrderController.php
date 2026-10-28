<?php

namespace Core\Users\Controllers\Api\Technical;

use App\Http\Controllers\Controller;
use Core\Notification\Models\Notification;
use Core\Orders\DataResources\Api\Client\Order\OrderDetailsWithOutItemsResource;
use Illuminate\Http\Request;
use Core\Orders\Models\Order;
use Core\Orders\Models\OrderReport;
use Core\Orders\Models\OrderRepresentative;
use Core\Settings\Services\SettingsService;
use Core\Settings\Traits\ApiResponse;
use Core\Users\DataResources\Driver\OrderDetailsResource;
use Core\Users\DataResources\Driver\OrderResource;
use Core\Users\Requests\Api\OrderReportRequest;

/**
 * @group 3. Technician App
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
            ->whereIn('type', ['services', 'maidflex', 'maidscheduled', 'maidPackage', 'maidoffer', 'host', 'care', 'selfcare'])
            ->hasRepresentatives('technical', auth('api')->id())
            ->latest()
            ->paginate(10);
        return $this->returnData('data', OrderResource::collection($orders)->response()->getData(true));

    }

    public function show($order_id)
    {
        $order = Order::with(self::EAGER_LOADS)->find($order_id);
        
        if (!$order) {
            return $this->returnErrorMessage(trans('order_not_found'), [], [], 404);
        }

        if($order->type=='services'){
            $order = new OrderDetailsResource($order);
        }else{
            $order = new OrderDetailsWithOutItemsResource($order);
        }
        return $this->returnData('data', ['data' =>  $order]);
    }


    public function technical_accept($order_id)
    {
        $order = Order::where('status', 'pending')->hasRepresentatives('technical',auth('api')->id())->find($order_id);
        if (! $order) {
            return $this->returnErrorMessage(trans('order_stage_required', ['status' => trans('pending')]), [], [], 422);
        }
        $order->update(['status' => 'technical_accepted', 'order_status_times' => ['technical_accepted' => [date("Y-m-d H:i"), auth('api')->user()->phone]]]);
        $senderData = ['id' => auth('api')->user()->id, 'fullname' => auth('api')->user()->fullname, 'phone' => (string)auth('api')->user()->phone, 'image' => (string)auth('api')->user()->avatarUrl];
        $clientLang = $order->client?->default_language ?: 'ar';
        $data = [
            'key'               => "order",
            'key_id'            => $order->id,
            'status'            => $order->status,
            'title'             => trans('title_technical_deliverd_order', ['order_id' => $order->reference_id], $clientLang),
            'body'              => trans('body_technical_deliverd_order', ['order_id' => $order->reference_id, 'technical_name' => auth('api')->user()->fullname], $clientLang),
            'order_id'          => $order->id,
            'order_driver_type' => 'technical',
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
        $order = Order::where('status', 'technical_accepted')->hasRepresentatives('technical',auth('api')->id())->find($order_id);
        if (! $order) {
            return $this->returnErrorMessage(trans('order_stage_required', ['status' => trans('technical_accepted')]), [], [], 422);
        }
        $order->update(['status' => 'in_the_way', 'order_status_times' => ['in_the_way' => [date("Y-m-d H:i"), auth('api')->user()->phone]]]);
        $senderData = ['id' => auth('api')->user()->id, 'fullname' => auth('api')->user()->fullname, 'phone' => (string)auth('api')->user()->phone, 'image' => (string)auth('api')->user()->avatarUrl];
        $clientLang = $order->client?->default_language ?: 'ar';
        $data = [
            'key'               => "order",
            'key_id'            => $order->id,
            'status'            => $order->status,
            'title'             => trans('title_technical_in_the_way', ['order_id' => $order->reference_id], $clientLang),
            'body'              => trans('body_technical_in_the_way', ['order_id' => $order->reference_id, 'technical_name' => auth('api')->user()->fullname], $clientLang),
            'order_id'          => $order->id,
            'order_driver_type' => 'technical',
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

    public function started($order_id)
    {
        $order = Order::where('status', 'in_the_way')->hasRepresentatives('technical',auth('api')->id())->find($order_id);
        if (! $order) {
            return $this->returnErrorMessage(trans('order_stage_required', ['status' => trans('in_the_way')]), [], [], 422);
        }
        $order->update(['status' => 'started', 'order_status_times' => ['started' => [date("Y-m-d H:i"), auth('api')->user()->phone]]]);

        $senderData = ['id' => auth('api')->user()->id, 'fullname' => auth('api')->user()->fullname, 'phone' => (string)auth('api')->user()->phone, 'image' => (string)auth('api')->user()->avatarUrl];
        $clientLang = $order->client?->default_language ?: 'ar';
        $data = [
            'key'               => "order",
            'key_id'            => $order->id,
            'status'            => $order->status,
            'title'             => trans('title_technical_in_the_way', ['order_id' => $order->reference_id], $clientLang),
            'body'              => trans('body_technical_in_the_way', ['order_id' => $order->reference_id, 'technical_name' => auth('api')->user()->fullname], $clientLang),
            'order_id'          => $order->id,
            'order_driver_type' => 'technical',
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

    public function finished($order_id)
    {
        $order = Order::where('status', 'started')->hasRepresentatives('technical',auth('api')->id())->find($order_id);
        if (! $order) {
            return $this->returnErrorMessage(trans('order_stage_required', ['status' => trans('started')]), [], [], 422);
        }
        app(\Core\Orders\Services\OrdersService::class)->completeOrderFinancials($order->id);
        $order->update(['status' => 'finished', 'order_status_times' => ['finished' => [date("Y-m-d H:i"), auth('api')->user()->phone]]]);

        $senderData = ['id' => auth('api')->user()->id, 'fullname' => auth('api')->user()->fullname, 'phone' => (string)auth('api')->user()->phone, 'image' => (string)auth('api')->user()->avatarUrl];
        $clientLang = $order->client?->default_language ?: 'ar';
        $data = [
            'key' => "order",
            'key_id' => $order->id,
            'status' => $order->status,
            // 'main_order_type' => $this->order->type,
            'title' => trans('title_technical_finished', ['order_id' => $order->reference_id], $clientLang),
            'body' => trans('body_technical_finished', ['order_id' => $order->reference_id, 'technical_name' => auth('api')->user()->fullname], $clientLang),
            'order_id' => $order->id,
            'order_driver_type' => 'technical',
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
        $assigned = Order::hasRepresentatives('technical', auth('api')->id())->whereKey($order_id)->exists();
        if (! $assigned) { return $this->returnErrorMessage(trans('You are not assigned to this order'), [], [], 403); }
        if($request->type == 'report'){
            $order = Order::FindOrFail($order_id);
            $issueData = [
                'status' => 'issue',
                'driver_has_problem' => true,
                'order_status_times' => ['issue' => [date("Y-m-d H:i"), auth('api')->user()->phone]],
            ];
            if ($order->status !== 'issue') {
                $issueData['status_before_issue'] = $order->status;
            }
            $order->update($issueData);
            OrderReport::create($request->validated() + ['user_id' => auth('api')->id(), 'order_id' => $order->id]);
        }else{
            $orderRep = OrderRepresentative::has('address')->where('order_id',$order_id)
                ->where('representative_id',auth('api')->id())->first();
            if($orderRep){
                $orderRep->address->updateQuietly(['description' => $request->desc_location]);
            }
        }
        return $this->returnSuccessMessage(trans('send successfully'));
    }

    public function get_money_method($order_id, Request $request)
    {
        $order = Order::hasRepresentatives('technical', auth('api')->id())->findOrFail($order_id);
        $order->update(['get_money_method' => $request->pay_type_method]);
        return $this->returnSuccessMessage(trans('send successfully'));
    }
}
