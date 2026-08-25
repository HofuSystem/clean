<?php

namespace Core\Orders\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Core\Categories\DataResources\Api\CategoryTimeResource;
use Core\Categories\Models\Category;
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
use Core\Orders\Requests\Api\UpdateOrderFlowersRequest;
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
    public function __construct(protected OrdersService $ordersService, protected CategoryDateTimesService $categoryDateTimesService, protected MyFatoorahService $myfatoorahService)
    {
    }
    public function myOrders(Request $request)
    {
        try {
            $orders = Order::with([
                'orderRepresentatives',
                'items' => function ($q) {
                    $q->withTrashed()->where('final_delete', false)->with(['product.category.translations', 'qtyUpdates']);
                },
                'coupon.gift',
                'city.translations',
                'district.translations',
                'moreDatas'
            ])
            ->where('client_id', $request->user()->id)
            ->whereNotIn('status', ['pending_payment', 'failed_payment', 'cancel_payment'])
            ->when($request->type, function ($query) use ($request) {
                if ($request->type == 'clothes') {
                    $query->whereIn('type', ['clothes', 'fastorder']);
                } elseif ($request->type == 'services') {
                    $query->where('type', 'services');
                } elseif ($request->type == 'sales') {
                    $query->where('type', 'sales');
                } else {
                    $query->where('type', $request->type);
                }
            })
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
            $order = Order::with([
                'items.product.translations',
                'items.product.category.translations',
                'items.product.subCategory.translations',
                'transactions',
                'client.profile',
                'orderRepresentatives',
                'moreDatas',
                'city',
                'district'
            ])
                ->whereNotIn('status', ['pending_payment', 'failed_payment', 'cancel_payment'])
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
            if ($order->status == 'pending_payment' && $order->pay_type == 'card') {
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
            $address = Address::findOrFail($request->address_id);
            //get free delivery order min value
            $freeDelivery = SettingsService::getDataBaseSetting('free_delivery');
            $deliveryCharge = SettingsService::getDataBaseSetting('delivery_charge');

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
            if ($deliveryPrice) {
                $freeDelivery = $deliveryPrice->free_delivery;
                $deliveryCharge = $deliveryPrice->price;
            }
            if ($request->order_total >= $freeDelivery) {
                $deliveryCharge = 0;
            }

            $dateTimes = CategoryDateTimesService::getDateTimes(OrderHelper::getOrderType($request->type), request('category_id'), $address);
            $dateTimes = CategoryDateTimesService::getDateTimesFormatted('all', $dateTimes);
            $deliveryDates = CategoryDateTimesService::getDateTimes(OrderHelper::getOrderType($request->type), request('category_id'), $address);
            $deliveryDates = CategoryDateTimesService::getDateTimesFormatted('delivery', $deliveryDates);
            $receiverDates = CategoryDateTimesService::getDateTimes(OrderHelper::getOrderType($request->type), request('category_id'), $address);
            $receiverDates = CategoryDateTimesService::getDateTimesFormatted('receiver', $receiverDates);

            // Coverage Info Calculation
            $city = $address->city;
            $district = $address->district;

            // إذا المدينة مش موجودة بالسيستم أو city_id = null → غير مغطاة
            $cityExists = $address->city_id && $city !== null;
            $districtExists = $address->district_id ? $district !== null : true;

            $cityStatus = $city?->status ?? 'not-active';
            $districtStatus = $district?->status ?? 'not-active';
            $hasDates = !empty($dateTimes);
            $isCovered = false;
            $coverageStatus = 'covered';

            if (!$cityExists || !$districtExists) {
                // المدينة أو الحي مش مسجلين بالسيستم → غير مغطاة
                $coverageStatus = 'not_covered_yet';
            } elseif ($cityStatus === 'paused' || $districtStatus === 'paused') {
                $coverageStatus = 'temporarily_stopped';
            } elseif ($cityStatus === 'not-active' || $districtStatus === 'not-active') {
                $hasOrderHistory = false;
                if ($address->district_id) {
                    $hasOrderHistory = \Core\Orders\Models\Order::where('district_id', $address->district_id)->exists();
                } elseif ($address->city_id) {
                    $hasOrderHistory = \Core\Orders\Models\Order::where('city_id', $address->city_id)->exists();
                }
                $coverageStatus = $hasOrderHistory ? 'temporarily_stopped' : 'not_covered_yet';
            } elseif (!$hasDates) {
                $hasOrderHistory = false;
                if ($address->district_id) {
                    $hasOrderHistory = \Core\Orders\Models\Order::where('district_id', $address->district_id)->exists();
                } elseif ($address->city_id) {
                    $hasOrderHistory = \Core\Orders\Models\Order::where('city_id', $address->city_id)->exists();
                }
                $coverageStatus = $hasOrderHistory ? 'temporarily_stopped' : 'not_covered_yet';
            } else {
                $isCovered = true;
                $coverageStatus = 'covered';
            }

            $lang = app()->getLocale() == 'en' ? 'en' : 'ar';
            if ($coverageStatus === 'temporarily_stopped') {
                $title = SettingsService::getDataBaseSetting('coverage_paused_title_' . $lang) 
                    ?? ($lang === 'en' ? 'We will be back soon!' : 'نظبط أمورنا ونرجع لكم!');
                $message = SettingsService::getDataBaseSetting('coverage_paused_message_' . $lang) 
                    ?? ($lang === 'en' ? 'We temporarily paused order reception in your area for service enhancement.. We will be back soon!' : 'أوقفنا استقبال الطلبات مؤقتاً في منطقتك لتطوير الخدمة.. راجعين لكم قريب!');
                $buttonText = SettingsService::getDataBaseSetting('coverage_paused_button_' . $lang) 
                    ?? ($lang === 'en' ? 'Notify me when back!' : 'علموني إذا رجعتوا!');
                $actionType = 'notify_on_resume';
            } elseif ($coverageStatus === 'not_covered_yet') {
                $title = SettingsService::getDataBaseSetting('coverage_not_reached_title_' . $lang) 
                    ?? ($lang === 'en' ? 'Welcome! We haven\'t reached your area yet' : 'يا هلا بك! لسا ما وصلنا لكم');
                $message = SettingsService::getDataBaseSetting('coverage_not_reached_message_' . $lang) 
                    ?? ($lang === 'en' ? 'We would love to serve you, but we haven\'t covered your area yet. We are expanding soon!' : 'ودنا نخدمك اليوم قبل بكرة بس منطقتك لسا ما غطيناها، خطتنا نتوسع وقريب بنطرق بابك.');
                $buttonText = SettingsService::getDataBaseSetting('coverage_not_reached_button_' . $lang) 
                    ?? ($lang === 'en' ? 'Notify me when available!' : 'علموني إذا وصلتول!');
                $actionType = 'notify_on_expansion';
            } else {
                $title = null;
                $message = null;
                $buttonText = null;
                $actionType = null;
            }

            $coverageInfo = [
                'is_covered'    => $isCovered,
                'status'        => $coverageStatus,
                'city_id'       => $address->city_id,
                'district_id'   => $address->district_id,
                'title'         => $title,
                'message'       => $message,
                'button_text'   => $buttonText,
                'action_type'   => $actionType,
            ];

            $data = [
                'points' => $address->user?->points_balance ?? 0,
                'wallet' => $address->user?->wallet ?? 0,
                'delivery_charge' => $deliveryCharge ?? 0,
                'free_charge' => $deliveryCharge == 0 ? true : false,
                'dates' => $dateTimes,
                'receiver_dates' => $receiverDates,
                'delivery_dates' => $deliveryDates,
                'coverage_info' => $coverageInfo,
            ];

            return $this->returnData(trans('order was created'), ['status' => 'success', 'data' => $data]);
        } catch (ValidationException $e) {
            return $this->returnErrorMessage($e->getMessage(), $e->errors(), ['status' => 'fail', 'data' => null,], 422);
        } catch (\Throwable $e) {
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'), [], ['status' => 'fail', 'data' => null,], 422);
        }
    }
    public function updateOrderFlowers(UpdateOrderFlowersRequest $request)
    {
        try {
            $address = (object) [
                'city_id' => $request->city_id,
                'district_id' => $request->district_id
            ];

            //get free delivery order min value
            $freeDelivery = SettingsService::getDataBaseSetting('free_delivery');
            $deliveryCharge = SettingsService::getDataBaseSetting('delivery_charge');
            $category = Category::where('slug', 'gifts-and-flowers')->first();

            $deliveryPrice = DeliveryPrice::query()
                ->where(function ($categoryQuery) use ($category) {
                    $categoryQuery->whereNull('category_id')
                        ->when(is_int($category->id), function ($categoryQuery) use ($category) {
                            $categoryQuery->OrWhere('category_id', $category->id);
                        });
                })->where(function ($cityQuery) use ($address) {
                    $cityQuery->whereNull('city_id')
                        ->when(is_int($address->city_id), function ($cityQuery) use ($address) {
                            $cityQuery->OrWhere('city_id', $address->city_id);
                        });
                })->where(function ($districtQuery) use ($address) {
                    $districtQuery->whereNull('district_id')
                        ->when(is_int($address->district_id), function ($districtQuery) use ($address) {
                            $districtQuery->OrWhere('district_id', $address->district_id);
                    });
                })
                ->orderBy('price', 'desc')
                ->first();
            if ($deliveryPrice) {
                $freeDelivery = $deliveryPrice->free_delivery;
                $deliveryCharge = $deliveryPrice->price;
            }
            if ($request->order_total >= $freeDelivery) {
                $deliveryCharge = 0;
            }

            $deliveryDates = CategoryDateTimesService::getDateTimes('sales', $category->id, $address);
            $deliveryDates = CategoryDateTimesService::getDateTimesFormatted('delivery', $deliveryDates);


            $user = auth('api')->user() ?? auth('sanctum')->user() ?? request()->user();
            $data = [
                'points' => $user?->points_balance ?? 0,
                'wallet' => $user?->wallet ?? 0,
                'delivery_charge' => $deliveryCharge ?? 0,
                'free_charge' => $deliveryCharge == 0 ? true : false,
                'delivery_dates' => $deliveryDates,
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
                'payment_url' => $this->ordersService->createPaymentUrl($orderId, $request->paid, $requestData, 'fast_payment'),
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
            $order = $this->ordersService->updateStatus($orderId, $request->validated());
            DB::commit();
            if ($order->status == 'pending') {
                return $this->returnData(trans('Payment Successful'), ['status' => 'success', 'data' => null]);
            } elseif ($order->status == 'cancel_payment') {
                return $this->returnSuccessMessage(trans('Payment canceled'));
            } else {
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