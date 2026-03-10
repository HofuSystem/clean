<?php

namespace Core\Orders\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Core\Categories\DataResources\Api\CategoryTimeResource;
use Core\Categories\Models\CategoryDateTime;
use Core\Categories\Services\CategoryDateTimesService;
use Core\Orders\DataResources\Api\Client\Order\OrderDetailsResource;
use Core\Orders\DataResources\Api\Client\Order\OrderDetailsWithOutItemsResource;
use Core\Orders\DataResources\Api\Client\Order\OrderResource;
use Core\Orders\DataResources\Api\Client\Order\OrderWithOutItemsResource;
use Core\Orders\Helpers\OrderHelper;
use Core\Orders\Models\DeliveryPrice;
use Core\Orders\Models\Order;
use Core\Orders\Requests\Api\CreateOrderRequest;
use Core\Orders\Requests\Api\PayFastOrderRequest;
use Core\Orders\Requests\Api\UpdateOrderRequest;
use Core\Orders\Requests\UpdateStatusRequest;
use Core\Settings\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Core\Orders\Services\OrdersService;
use Core\PaymentGateways\Models\PaymentTransaction;
use Core\Settings\Models\Setting;
use Core\Settings\Services\SettingsService;
use Core\Users\Models\Address;
use Illuminate\Validation\ValidationException;
use Core\PaymentGateways\Services\MyFatoorahService;

class OrdersController extends Controller
{
    use ApiResponse;
    public function __construct(protected OrdersService $ordersService,protected CategoryDateTimesService $categoryDateTimesService,protected MyFatoorahService $myfatoorahService) {}

    public function myOrders(Request $request)
    {
        try {
            $orders =   Order::with(['orderRepresentatives'])
                ->when($request->type == 'clothes', function ($clothesOrderQuery) {
                    $clothesOrderQuery->with('items.product')->whereIn('type', ['clothes', 'fastorder', 'sales']);
                })->when($request->type != 'clothes', function ($notClothesOrderQuery) {
                    $notClothesOrderQuery->with('moreDatas')->whereNotIn('type', ['clothes', 'fastorder', 'sales']);
                })
                ->where('client_id', $request->user()->id)
                ->whereNotIn('status',['pending_payment','failed_payment','cancel_payment'])
                ->latest()->paginate(10);
            if (in_array($request->type, ['clothes', 'fastorder', 'services', 'sales'])) {
                return OrderResource::collection($orders)->additional(['status' => 'success', 'message' => '']);
            } else {
                return OrderWithOutItemsResource::collection($orders)->additional(['status' => 'success', 'message' => '']);
            }
        } catch (\Throwable $e) {
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'), [], ['status' => 'fail'], 422);
        }
    }
    public function myOrder(Request $request, $id)
    {
        try {
            $order = Order::with(['items.product', 'moreDatas'])
            ->whereNotIn('status',['pending_payment','failed_payment','cancel_payment'])
            ->findorFail($id);
            if (in_array($order->type, ['clothes', 'fastorder', 'services', 'sales'])) {
                return (new OrderDetailsResource($order))->additional(['status' => 'success', 'message' => '']);
            } else {
                return (new OrderDetailsWithOutItemsResource($order))->additional(['status' => 'success', 'message' => '']);
            }
        } catch (\Throwable $e) {
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'), [], ['status' => 'fail'], 422);
        }
    }

    public function createOrder(CreateOrderRequest $request)
    {
        try {
            DB::beginTransaction();
            $order = $this->ordersService->createOrder($request->validated(), $request->products);
            $data = [
                'id' => $order->id,
            ];
            if($order->status == 'pending_payment' && $order->pay_type == 'card'){
                $data['payment_url'] = $this->ordersService->createPaymentUrl($order->id, $order->card_amount_used, $request->all());
            }
            DB::commit();
            return $this->returnData(trans('order was created'), ['status' => 'success', 'data' => $data]);
        } catch (ValidationException $e) {
            DB::rollback();
            return $this->returnErrorMessage($e->getMessage(), $e->errors(), ['status' => 'fail', 'data' => null,], 422);
        } catch (\Throwable $e) {
            DB::rollback();
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'), [], ['status' => 'fail', 'data' => null,], 422);
        }
    }
    public function updateOrder(UpdateOrderRequest $request)
    {
        try {
            $address          = Address::findOrFail($request->address_id);
            //get free delivery order min value
            $freeDelivery     = SettingsService::getDataBaseSetting('free_delivery');
            $deliveryCharge   = SettingsService::getDataBaseSetting('delivery_charge');

            $deliveryPrice = DeliveryPrice::query()
            ->where(function ($categoryQuery) use ($request) {
                $categoryQuery->whereNull('category_id')
                    ->when(is_int($request->category_id), function ($categoryQuery) use ($request) {
                        $categoryQuery->OrWhere('category_id', $request->category_id);
                    })->when(is_array($request->category_id), function ($categoryQuery) use ($request) {
                        $categoryQuery->orWhereIn('category_id', $request->category_id);
                    });
            })->where(function ($cityQuery) use ($address) {
                $cityQuery->whereNull('city_id')
                    ->when(is_int($address->city_id), function ($cityQuery) use ($address) {
                        $cityQuery->OrWhere('city_id', $address->city_id);
                    })->when(is_array($address->city_id), function ($cityQuery) use ($address) {
                        $cityQuery->orWhereIn('city_id', $address->city_id);
                    });
            })->where(function ($districtQuery) use ($address) {
                $districtQuery->whereNull('district_id')
                    ->when(is_int($address->district_id), function ($districtQuery) use ($address) {
                        $districtQuery->OrWhere('district_id', $address->district_id);
                    })->when(is_array($address->district_id), function ($districtQuery) use ($address) {
                        $districtQuery->orWhereIn('district_id', $address->district_id);
                    });
            })
            ->orderBy('price', 'desc')
            ->first();
            if($deliveryPrice){
                $freeDelivery = $deliveryPrice->free_delivery;
                $deliveryCharge = $deliveryPrice->price;
            }
            if ($request->order_total >= $freeDelivery) {
                $deliveryCharge = 0;
            }

            $dateTimes      = CategoryDateTimesService::getDateTimes(OrderHelper::getOrderType($request->type),request('category_id'),$address);
            $dateTimes = CategoryDateTimesService::getDateTimesFormatted('all', $dateTimes);
            $deliveryDates  = CategoryDateTimesService::getDateTimes(OrderHelper::getOrderType($request->type),request('category_id'),$address);
            $deliveryDates = CategoryDateTimesService::getDateTimesFormatted('delivery', $deliveryDates);
            $receiverDates = CategoryDateTimesService::getDateTimes(OrderHelper::getOrderType($request->type),request('category_id'),$address);
            $receiverDates = CategoryDateTimesService::getDateTimesFormatted('receiver', $receiverDates);
            $data = [
                'points'          => $address->user?->points_balance ?? 0,
                'wallet'          => $address->user?->wallet ?? 0,
                'delivery_charge' => $deliveryCharge ?? 0,
                'free_charge'     => $deliveryCharge == 0 ? true : false,
                'dates'           => $dateTimes,
                'receiver_dates'  => $receiverDates,
                'delivery_dates'  => $deliveryDates,
            ];

            return $this->returnData(trans('order was created'), ['status' => 'success', 'data' => $data]);
        } catch (ValidationException $e) {
            return $this->returnErrorMessage($e->getMessage(), $e->errors(), ['status' => 'fail', 'data' => null,], 422);
        } catch (\Throwable $e) {
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'), [], ['status' => 'fail', 'data' => null,], 422);
        }
    }

    public function payFastOrder(PayFastOrderRequest $request, $orderId)
    {
        try {
            DB::beginTransaction();
            $this->ordersService->payFastOrder($orderId, $request->validated());
            DB::commit();
            return $this->returnData(trans('Payment Successful'), ['status' => 'success', 'data' => null]);
        } catch (ValidationException $e) {
            DB::rollback();
            return $this->returnErrorMessage($e->getMessage(), $e->errors(), ['status' => 'fail', 'data' => null,], 422);
        } catch (\Throwable $e) {
            DB::rollback();
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'), [], ['status' => 'fail', 'data' => null,], 422);
        }
    }
    public function payFastOrderV2(PayFastOrderRequest $request, $orderId)
    {
        try {
            DB::beginTransaction();
            $requestData = $request->all();
            $data = [
                'payment_url' => $this->ordersService->createPaymentUrl($orderId, $request->paid, $requestData,'fast_payment'),
            ];
            DB::commit();
            return $this->returnData(trans('Payment Successful'), ['status' => 'success', 'data' => $data]);
        } catch (ValidationException $e) {
            DB::rollback();
            return $this->returnErrorMessage($e->getMessage(), $e->errors(), ['status' => 'fail', 'data' => null,], 422);
        } catch (\Throwable $e) {
            DB::rollback();
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'), [], ['status' => 'fail', 'data' => null,], 422);
        }
    }
    public function updateStatus(UpdateStatusRequest $request, $orderId)
    {
        try {
            DB::beginTransaction();
            $order =$this->ordersService->updateStatus($orderId,$request->validated());
            DB::commit();
            if($order->status == 'pending'){
                return $this->returnData(trans('Payment Successful'), ['status' => 'success', 'data' => null]);
            }elseif($order->status == 'cancel_payment'){
                return $this->returnSuccessMessage(trans('Payment canceled'));
            }else{
                return $this->returnSuccessMessage(trans('Payment failed'));
            }
        } catch (ValidationException $e) {
            DB::rollback();
            return $this->returnErrorMessage($e->getMessage(), $e->errors(), ['status' => 'fail', 'data' => null,], 422);
        } catch (\Throwable $e) {
            DB::rollback();
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'), [], ['status' => 'fail', 'data' => null,], 422);
        }
    }
}
