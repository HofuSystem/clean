<?php

namespace Core\Categories\Controllers\Api;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Core\Categories\DataResources\Api\CategoryDetails\ClothesDetailsResource;
use Core\Categories\DataResources\Api\CategoryDetails\CustomServiceDetailsResource;
use Core\Categories\DataResources\Api\CategoryDetails\PackageDetailsResource;
use Core\Categories\DataResources\Api\CategoryDetails\ServicesDetailsResource;
use Core\Categories\DataResources\Api\CategoryTimeResource;
use Core\Categories\DataResources\Api\ClothesCategoryResource;
use Core\Categories\DataResources\Api\ServiceCategorySaleResource;
use Core\Categories\DataResources\Api\Services\CareHostServiceResource;
use Core\Categories\DataResources\Api\Services\CareOrderDetailsResource;
use Core\Categories\DataResources\Api\Services\FlexibleOrderDetailsResource;
use Core\Categories\DataResources\Api\Services\HomeMaidSaleResource;
use Core\Categories\DataResources\Api\Services\HostOrderDetailsResource;
use Core\Categories\DataResources\Api\Services\MonthlyPackagesOrderDetailsResource;
use Core\Categories\DataResources\Api\Services\SaleOrderDetailsResource;
use Core\Categories\DataResources\Api\Services\ScheduledOrderDetailsResource;
use Core\Categories\DataResources\Api\Services\SelfCareOrderDetailsResource;
use Core\Categories\DataResources\Api\Services\SubServiceResource;
use Core\Categories\DataResources\Api\ServicesCategoryResource;
use Core\Categories\DataResources\Api\SliderResource;
use Core\Categories\Models\Category;
use Core\Categories\Models\CategoryDateTime;
use Core\Categories\Models\CategoryOffer;
use Core\Categories\Models\Slider;
use Core\Notification\Models\UsersNotification;
use Core\Settings\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Core\Comments\DataResources\CommentResource;
use Core\Categories\Services\CategoriesService;
use Core\Notification\DataResources\Api\BannerNotificationResource;
use Core\Notification\Models\BannerNotification;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class CategoriesController extends Controller
{
    use ApiResponse;
    public function __construct(protected CategoriesService $categoriesService) {}


    public function index(Request $request)
    {
        try {
            $cityId = $request->city_id ?? (auth('api')->user()?->profile?->city_id ?? 'all');
            $locale = app()->getLocale();
            $cacheKey = "categories_index_{$cityId}_{$locale}";

            $data = Cache::tags(['categories_api'])->remember($cacheKey, now()->addHours(24), function () use ($request) {
                $slider = Slider::with('city.translations', 'category.translations')
                    ->active()
                    ->where('type', 'clothes')
                    ->when($request->city_id, function ($q) use ($request) {
                        $q->where('city_id', $request->city_id);
                    })->latest()->get();

                $clothesCategory = Category::with('translations')
                    ->whereNull('parent_id')
                    ->where('type', 'clothes')
                    ->where('is_package', false)
                    ->active()
                    ->orderBy('sort', 'asc')
                    ->when($request->city_id, function ($q) use ($request) {
                        $q->where('city_id', $request->city_id);
                    })->get();

                $economyBags = Category::with(['translations', 'products.translations', 'products.prices'])
                    ->active()
                    ->with(['products' => function ($query) {
                        $query->active();
                    }])
                    ->where('type', 'clothes')
                    ->where('is_package', true)->get();

                return [
                    'slider'                => json_decode(json_encode(SliderResource::collection($slider)),true),
                    'clothes_category'      => json_decode(json_encode(ClothesCategoryResource::collection($clothesCategory)),true),
                    'economy_bags'          => json_decode(json_encode(PackageDetailsResource::collection($economyBags)),true),
                ];
            });

            $userId = auth('api')->id();
            $notifications = BannerNotification::active()
                ->where('publish_date', '<=', now())
                ->where('expired_date', '>=', now())
                ->WhereHas('users', function ($userNotificationQuery) use ($userId) {
                    $userNotificationQuery->where('users.id', $userId)
                        ->where('users_notifications.next_vision_date', '<=', now()->format("Y-m-d H:i:s"))
                        ->orWhereNull('users_notifications.next_vision_date');
                })
                ->orWhereDoesntHave('users', function ($userNotificationQuery) use ($userId) {
                    $userNotificationQuery->where('users.id', $userId);
                })
                ->get();

            if ($notifications->isNotEmpty() && $userId) {
                // Optimize: Only update if not recently updated (buffer check in Redis)
                $notificationUpdateKey = "user_{$userId}_notification_update";
                if (!Cache::has($notificationUpdateKey)) {
                    foreach ($notifications as $notification) {
                        DB::table('users_notifications')->updateOrInsert([
                            'user_id' => $userId,
                            'notifications_type' => BannerNotification::class,
                            'notifications_id' => $notification->id,
                        ], [
                            'status' => 'sent',
                            'read_at' => now()->format("Y-m-d H:i:s"),
                            'next_vision_date' => now()->addHours($notification->next_vision_hour)->format("Y-m-d H:i:s"),
                        ]);
                    }
                    Cache::put($notificationUpdateKey, true, now()->addMinutes(5));
                }
            }

            $data['notifications'] = BannerNotificationResource::collection($notifications);

            return $this->returnData(trans('categories are loaded'), ['data' => $data]);
        } catch (ValidationException $e) {
            return $this->returnErrorMessage($e->getMessage(), $e->errors(), ['status' => 'fail'], 422);
        } catch (ModelNotFoundException  $e) {
            abort(404);
        } catch (\Throwable $e) {
            reprot($e);
            return $this->returnErrorMessage(trans('system Error please try again later'), [], ['status' => 'fail'], 422);
        }
    }
    public function details(Request $request, $categoryId)
    {
        try {
            $locale = app()->getLocale();
            $cacheKey = "categories_details_{$categoryId}_{$locale}";

            $data = Cache::tags(['categories_api'])->remember($cacheKey, now()->addHours(24), function () use ($categoryId) {
                return Category::with(['translations', 'subCategories.productsSub.translations'])
                    ->active()
                    ->with(['subCategories' => function ($query) {
                        $query->active()
                            ->orderBy('categories.sort', 'asc')
                            ->with(['productsSub' => function ($query) {
                                $query->active();
                            }]);
                    }])
                    ->where('type', 'clothes')
                    ->where('is_package', false)
                    ->findOrFail($categoryId);
            });

            return $this->returnData(trans('categories are loaded'), [
                'status' => 'success',
                'data'   => json_decode(json_encode(new ClothesDetailsResource($data)),true)
            ]);
        } catch (ValidationException $e) {
            return $this->returnErrorMessage($e->getMessage(), $e->errors(), ['status' => 'fail'], 422);
        } catch (ModelNotFoundException  $e) {
            abort(404);
        } catch (\Throwable $e) {
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'), [], ['status' => 'fail'], 422);
        }
    }
    public function packageDetails(Request $request, $categoryId)
    {
        try {

            $category = Category::with(['translations', 'products.translations'])
                ->active()
                ->with(['products' => function($query){
                    $query->active();
                }])
                ->where('type', 'clothes')
                ->where('is_package', true)
                ->findOrFail($categoryId);
            $data = [
                'status' => 'success',
                'data'   => json_decode(json_encode(new PackageDetailsResource($category)),true)
            ];
            return $this->returnData(trans('categories are loaded'), $data);
        } catch (ValidationException $e) {
            return $this->returnErrorMessage($e->getMessage(), $e->errors(), ['status' => 'fail'], 422);
        } catch (ModelNotFoundException  $e) {
           abort(404);
        }catch (\Throwable $e) {
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'), [], ['status' => 'fail'], 422);
        }
    }

    //services
    public function servicesIndex(Request $request)
    {
        try {
            $cityId = $request->city_id ?? (auth('api')->user()?->profile?->city_id ?? 'all');
            $locale = app()->getLocale();
            $cacheKey = "services_index_{$cityId}_{$locale}";

            $data = Cache::tags(['categories_api'])->remember($cacheKey, now()->addHours(24), function () use ($request) {
                $slider = Slider::where('type', 'services')
                    ->active()
                    ->when($request->city_id, function ($q) use ($request) {
                        $q->where('city_id', $request->city_id);
                    })->latest()->get();
                $servicesCategory = Category::where('type', 'services')
                    ->active()
                    ->whereNull('parent_id')
                    ->orderBy('sort', 'asc')
                    ->when($request->city_id, function ($q) use ($request) {
                        $q->where('city_id', $request->city_id);
                    })->get();
                $sales = CategoryOffer::whereType('service_category_sale')->latest()->get();

                return [
                    'slider'                => json_decode(json_encode(SliderResource::collection($slider)),true),
                    'services_category'     => json_decode(json_encode(ServicesCategoryResource::collection($servicesCategory)),true),
                    'sales'                 => json_decode(json_encode(ServiceCategorySaleResource::collection($sales)),true),
                ];
            });

            return $this->returnData(trans('services are loaded'), ['data' => $data]);
        } catch (ValidationException $e) {
            return $this->returnErrorMessage($e->getMessage(), $e->errors(), ['status' => 'fail'], 422);
        } catch (ModelNotFoundException  $e) {
            abort(404);
        } catch (\Throwable $e) {
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'), [], ['status' => 'fail'], 422);
        }
    }
    public function servicesDetails(Request $request, $categoryId)
    {
        try {
            $locale = app()->getLocale();
            $cacheKey = "services_details_{$categoryId}_{$locale}";

            $data = Cache::tags(['categories_api'])->remember($cacheKey, now()->addHours(24), function () use ($categoryId) {
                return Category::with(['translations', 'products.translations', 'appFeatures.translations'])
                    ->active()
                    ->with(['products' => function ($query) {
                        $query->active();
                    }])
                    ->where('type', 'services')
                    ->findOrFail($categoryId);
            });

            return $this->returnData(trans('categories are loaded'), [
                'status' => 'success',
                'data'   => json_decode(json_encode(new ServicesDetailsResource($data)),true)
            ]);
        } catch (ValidationException $e) {
            return $this->returnErrorMessage($e->getMessage(), $e->errors(), ['status' => 'fail'], 422);
        } catch (ModelNotFoundException  $e) {
            abort(404);
        } catch (\Throwable $e) {
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'), [], ['status' => 'fail'], 422);
        }
    }

    //home maid
    public function homeMaidIndex(Request $request)
    {
        try {
            $cityId = $request->city_id ?? (auth('api')->user()?->profile?->city_id ?? 'all');
            $locale = app()->getLocale();
            $cacheKey = "home_maid_index_{$cityId}_{$locale}";

            $data = Cache::tags(['categories_api'])->remember($cacheKey, now()->addHours(24), function () use ($request) {
                $slider = Slider::with(['city.translations', 'category.translations'])
                    ->where('type', 'maid')
                    ->when($request->city_id, function ($q) use ($request) {
                        $q->where('city_id', $request->city_id);
                    })->latest()->get();

                $sales  = CategoryOffer::with('translations')
                    ->active()
                    ->whereType('home_maid_sale')->get();
                $childs = Category::with('translations')
                    ->whereNotNull('parent_id')
                    ->whereHas('parent', function ($parentQuery) {
                        $parentQuery->where('slug', 'maid-host');
                    })->get();

                return [
                    'slider'        => json_decode(json_encode(SliderResource::collection($slider)->resolve()),true),
                    'sales'         => json_decode(json_encode(HomeMaidSaleResource::collection($sales)->resolve()),true),
                    'sub_services'  => json_decode(json_encode(SubServiceResource::collection($childs)->resolve()),true),
                ];
            });

            return $this->returnData(trans('services are loaded'), ['data' => $data]);
        } catch (ValidationException $e) {
            return $this->returnErrorMessage($e->getMessage(), $e->errors(), ['status' => 'fail'], 422);
        } catch (ModelNotFoundException  $e) {
            abort(404);
        } catch (\Throwable $e) {
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'), [], ['status' => 'fail'], 422);
        }
    }
    public function homeMaidDetails($serviceId)
    {
        try {
            $locale = app()->getLocale();
            $cacheKey = "home_maid_details_{$serviceId}_{$locale}";

            $data = Cache::tags(['categories_api'])->remember($cacheKey, now()->addHours(24), function () use ($serviceId) {
                return Category::with('translations')
                    ->findOrFail($serviceId);
            });

            return $this->returnData(trans('services are loaded'), [
                'status'   => 'success',
                'data'     => json_decode(json_encode(new CustomServiceDetailsResource($data)),true),
            ]);
        } catch (ValidationException $e) {
            return $this->returnErrorMessage($e->getMessage(), $e->errors(), ['status' => 'fail'], 422);
        } catch (ModelNotFoundException  $e) {
            abort(404);
        } catch (\Throwable $e) {
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'), [], ['status' => 'fail'], 422);
        }
    }
    public function flexibleOrderDetails()
    {
        try {
            $service = Category::with('translations')
                ->active()
                ->where('slug', 'flexible-home-visit')
                ->firstOrFail();
            return json_decode(json_encode(new FlexibleOrderDetailsResource($service)),true);
        } catch (ValidationException $e) {
            return $this->returnErrorMessage($e->getMessage(), $e->errors(), ['status' => 'fail'], 422);
        } catch (ModelNotFoundException  $e) {
           abort(404);
        }catch (\Throwable $e) {
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'), [], ['status' => 'fail'], 422);
        }
    }
    public function scheduledOrderDetails()
    {
        //dd('stop');
        try {
            $service = Category::with('translations')
                ->active()
                ->where('slug', 'scheduled-visits')
                ->firstOrFail();
            return json_decode(json_encode((new ScheduledOrderDetailsResource($service))->additional(['status' => 'success', 'message' => ''])),true);
        } catch (ValidationException $e) {
            return $this->returnErrorMessage($e->getMessage(), $e->errors(), ['status' => 'fail'], 422);
        } catch (ModelNotFoundException  $e) {
           abort(404);
        }catch (\Throwable $e) {
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'), [], ['status' => 'fail'], 422);
        }
    }
    public function monthlyPackagesOrderDetails()
    {
        try {
            $service = Category::with('translations')
                ->active()
                ->where('slug', 'resident-worker-packages')
                ->firstOrFail();
            return json_decode(json_encode((new MonthlyPackagesOrderDetailsResource($service))->additional(['status' => 'success', 'message' => ''])),true);
        } catch (ValidationException $e) {
            return $this->returnErrorMessage($e->getMessage(), $e->errors(), ['status' => 'fail'], 422);
        } catch (ModelNotFoundException  $e) {
           abort(404);
        }catch (\Throwable $e) {
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'), [], ['status' => 'fail'], 422);
        }
    }
    public function saleOrderDetails($saleId)
    {
        try {
            $sale = CategoryOffer::active()->findOrFail($saleId);
            return json_decode(json_encode((new SaleOrderDetailsResource($sale))->additional(['status' => 'success', 'message' => ''])),true);
        } catch (ValidationException $e) {
            return $this->returnErrorMessage($e->getMessage(), $e->errors(), ['status' => 'fail'], 422);
        } catch (ModelNotFoundException  $e) {
           abort(404);
        }catch (\Throwable $e) {
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'), [], ['status' => 'fail'], 422);
        }
    }

    //home maid
    public function hostIndex(Request $request)
    {
        try {
            $cityId = $request->city_id ?? (auth('api')->user()?->profile?->city_id ?? 'all');
            $locale = app()->getLocale();
            $cacheKey = "host_index_{$cityId}_{$locale}";

            $data = Cache::tags(['categories_api'])->remember($cacheKey, now()->addHours(24), function () use ($request) {
                $slider = Slider::where('type', 'host')
                    ->active()
                    ->when($request->city_id, function ($q) use ($request) {
                        $q->where('city_id', $request->city_id);
                    })
                    ->latest()->get();

                $sales = CategoryOffer::whereType('host_care_sale')
                    ->active()
                    ->where('sale_price', '!=', 'null')
                    ->when($request->city_id, function ($q) use ($request) {
                        $q->where('city_id', $request->city_id);
                    })->get();

                $careHost = Category::whereIn('slug', ['hospitality-services', 'care-service', 'selfcare-service'])
                    ->active()
                    ->with(['subCategories' => function ($query) {
                        $query->active();
                    }])
                    ->when($request->city_id, function ($q) use ($request) {
                        $q->where('city_id', $request->city_id);
                    })->get();

                $extraSales = CategoryOffer::active()->whereType('care_host_sale')->get();

                return [
                    'slider'        => json_decode(json_encode(SliderResource::collection($slider),true)),
                    'care_host'     =>  json_decode(json_encode(CareHostServiceResource::collection($careHost),true)),
                    'sales'         =>  json_decode(json_encode(HomeMaidSaleResource::collection($extraSales),true)),
                ];
            });

            return $this->returnData(trans('services are loaded'), ['data' => $data]);
        } catch (ValidationException $e) {
            return $this->returnErrorMessage($e->getMessage(), $e->errors(), ['status' => 'fail'], 422);
        } catch (ModelNotFoundException  $e) {
            abort(404);
        } catch (\Throwable $e) {
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'), [], ['status' => 'fail'], 422);
        }
    }
    public function hostDetails($serviceId)
    {
        try {
            $service = Category::with('translations')
            ->active()
            ->findOrFail($serviceId);
            $data = [
                'data'     => json_decode(json_encode(new CustomServiceDetailsResource($service),true)),
            ];

            return $this->returnData(trans('services are loaded'), $data);
        } catch (ValidationException $e) {
            return $this->returnErrorMessage($e->getMessage(), $e->errors(), ['status' => 'fail'], 422);
        } catch (ModelNotFoundException  $e) {
           abort(404);
        }catch (\Throwable $e) {
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'), [], ['status' => 'fail'], 422);
        }
    }

    public function hostOrderDetails($serviceId)
    {
        try {
            $service = Category::active()
                ->findOrFail($serviceId);
            return json_decode(json_encode((new HostOrderDetailsResource($service))->additional(['status' => 'success', 'message' => ''])),true);
        } catch (ValidationException $e) {
            return $this->returnErrorMessage($e->getMessage(), $e->errors(), ['status' => 'fail'], 422);
        } catch (ModelNotFoundException  $e) {
           abort(404);
        }catch (\Throwable $e) {
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'), [], ['status' => 'fail'], 422);
        }
    }

    public function careOrderDetails($serviceId)
    {
        try {
            $service = Category::active()
                ->findOrFail($serviceId);
            return json_decode(json_encode((new CareOrderDetailsResource($service))->additional(['status' => 'success', 'message' => ''])),true);
        } catch (ValidationException $e) {
            return $this->returnErrorMessage($e->getMessage(), $e->errors(), ['status' => 'fail'], 422);
        } catch (ModelNotFoundException  $e) {
           abort(404);
        }catch (\Throwable $e) {
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'), [], ['status' => 'fail'], 422);
        }
    }
    public function selfCareOrderDetails($serviceId)
    {
        try {
            $service = Category::active()->findOrFail($serviceId);
            return json_decode(json_encode((new SelfCareOrderDetailsResource($service))->additional(['status' => 'success', 'message' => ''])),true);
        } catch (ValidationException $e) {
            return $this->returnErrorMessage($e->getMessage(), $e->errors(), ['status' => 'fail'], 422);
        } catch (ModelNotFoundException  $e) {
           abort(404);
        }catch (\Throwable $e) {
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'), [], ['status' => 'fail'], 422);
        }
    }
    public function saleHostOrderDetails($saleId)
    {
        try {
            $sale = CategoryOffer::active()->findOrFail($saleId);
            return json_decode(json_encode((new SaleOrderDetailsResource($sale))->additional(['status' => 'success', 'message' => ''])),true);
        } catch (ValidationException $e) {
            return $this->returnErrorMessage($e->getMessage(), $e->errors(), ['status' => 'fail'], 422);
        } catch (ModelNotFoundException  $e) {
           abort(404);
        }catch (\Throwable $e) {
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'), [], ['status' => 'fail'], 422);
        }
    }


}
