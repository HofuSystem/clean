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
use Core\Categories\DataResources\Api\SampleCategoryResource;
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
use Core\Categories\DataResources\Api\Services\ServiceSettingResource;
use Core\Categories\DataResources\Api\Services\SubServiceResource;
use Core\Categories\DataResources\Api\ServicesCategoryResource;
use Core\Categories\DataResources\Api\SliderResource;
use Core\Categories\DataResources\CategoriesResource;
use Core\Categories\DataResources\CategorySettingsResource;
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
use Core\Products\Models\Product;
use Core\Products\Models\ProductSetting;
use Core\Categories\Models\CategorySetting;
use Core\Products\DataResources\Api\SimpleProductResource;
use Core\MediaCenter\Helpers\MediaCenterHelper;
use Core\Products\DataResources\Api\ProductServiceSettingResource;

class CategoriesController extends Controller
{
    use ApiResponse;
    public function __construct(protected CategoriesService $categoriesService)
    {
    }


    public function index(Request $request)
    {
        try {
            $cityId = $request->city_id ?? 'all';
            $locale = app()->getLocale();

            $slider = \Illuminate\Support\Facades\Cache::remember("home_slider_clothes_{$cityId}_{$locale}", 600, function () use ($request) {
                return Slider::with('city.translations', 'category.translations')
                    ->active()
                    ->where('type', 'clothes')
                    ->when($request->city_id, function ($q) use ($request) {
                        $q->where('city_id', $request->city_id);
                    })->latest()->get();
            });

            $clothesCategory = \Illuminate\Support\Facades\Cache::remember("home_categories_clothes_{$cityId}_{$locale}", 600, function () use ($request) {
                return Category::with('translations')
                    ->whereNull('parent_id')
                    ->where('type', 'clothes')
                    ->where('is_package', false)
                    ->active()
                    ->orderBy('sort', 'asc')
                    ->when($request->city_id, function ($q) use ($request) {
                        $q->where('city_id', $request->city_id);
                    })->get();
            });

            $economyBags = \Illuminate\Support\Facades\Cache::remember("home_economy_bags_{$locale}", 600, function () {
                return Category::with(['translations', 'products.translations', 'products.prices'])
                    ->active()
                    ->with([
                        'products' => function ($query) {
                            $query->active();
                        }
                    ])
                    ->where('type', 'clothes')
                    ->where('is_package', true)->get();
            });

            $notifications = collect();
            if (auth('api')->check()) {
                $userId = auth('api')->id();
                $nowStr = now()->format("Y-m-d H:i:s");
                $notifications = BannerNotification::active()
                    ->where('publish_date', '<=', $nowStr)
                    ->where('expired_date', '>=', $nowStr)
                    ->where(function ($q) use ($userId, $nowStr) {
                        $q->whereHas('users', function ($userNotificationQuery) use ($userId, $nowStr) {
                            $userNotificationQuery->where('users.id', $userId)
                                ->where(function ($subQ) use ($nowStr) {
                                    $subQ->where('users_notifications.next_vision_date', '<=', $nowStr)
                                         ->orWhereNull('users_notifications.next_vision_date');
                                });
                        })
                        ->orWhereDoesntHave('users', function ($userNotificationQuery) use ($userId) {
                            $userNotificationQuery->where('users.id', $userId);
                        });
                    })
                    ->get();
                foreach ($notifications as $notification) {
                    DB::table('users_notifications')->updateOrInsert([
                        'user_id' => $userId,
                        'notifications_type' => BannerNotification::class,
                        'notifications_id' => $notification->id,
                    ], [
                        'status' => 'sent',
                        'read_at' => $nowStr,
                        'next_vision_date' => now()->addHours($notification->next_vision_hour)->format("Y-m-d H:i:s"),
                    ]);
                }
            }
            $data = [
                'slider' => SliderResource::collection($slider),
                'clothes_category' => ClothesCategoryResource::collection($clothesCategory),
                'economy_bags' => PackageDetailsResource::collection($economyBags),
                'notifications' => BannerNotificationResource::collection($notifications),
            ];

            return $this->returnData(trans('categories are loaded'), ['data' => $data]);
        } catch (ValidationException $e) {
            return $this->returnErrorMessage($e->getMessage(), $e->errors(), ['status' => 'fail'], 422);
        } catch (ModelNotFoundException $e) {
            abort(404);
        } catch (\Throwable $e) {
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'), [], ['status' => 'fail'], 422);
        }
    }
    public function details(Request $request, $categoryId)
    {
        try {
            $locale = app()->getLocale();
            $cityId = auth('api')->user()?->profile?->city_id ?? ($request->city_id ?? 'all');
            // Note: userId removed from cache key — product data is the same for all users
            $cacheKey = "api_category_clothes_details_v3_{$categoryId}_{$cityId}_{$locale}";

            $data = \Illuminate\Support\Facades\Cache::remember($cacheKey, 600, function () use ($categoryId, $request) {
                $category = Category::with([
                    'translations',
                    'subCategories' => function ($query) {
                        $query->active()
                            ->orderBy('categories.sort', 'asc')
                            ->with([
                                'translations',
                                'cities',
                                'productsSub' => function ($query) {
                                    $query->active()
                                        ->with([
                                            'translations',
                                            'prices',
                                            'category.translations',
                                            'subCategory.translations',
                                            'favers',
                                            'productSettings' => function ($sq) {
                                                $sq->whereNull('parent_id')->active()->with('translations', 'productSettings.translations');
                                            }
                                        ]);
                                }
                            ]);
                    }
                ])
                ->where('type', 'clothes')
                ->where('is_package', false)
                ->active()
                ->findOrFail($categoryId);

                return [
                    'status' => 'success',
                    'data' => (new ClothesDetailsResource($category))->toArray($request)
                ];
            });

            return $this->returnData(trans('categories are loaded'), $data);
        } catch (ValidationException $e) {
            return $this->returnErrorMessage($e->getMessage(), $e->errors(), ['status' => 'fail'], 422);
        } catch (ModelNotFoundException $e) {
            abort(404);
        } catch (\Throwable $e) {
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'), [], ['status' => 'fail'], 422);
        }
    }
    public function packageDetails(Request $request, $categoryId)
    {
        try {
            $locale = app()->getLocale();
            $category = \Illuminate\Support\Facades\Cache::remember("api_package_details_{$categoryId}_{$locale}", 1800, function () use ($categoryId) {
                return Category::with(['translations', 'products.translations'])
                    ->active()
                    ->with([
                        'products' => function ($query) {
                            $query->active();
                        }
                    ])
                    ->where('type', 'clothes')
                    ->where('is_package', true)
                    ->findOrFail($categoryId);
            });
            $data = [
                'status' => 'success',
                'data' => new PackageDetailsResource($category)
            ];
            return $this->returnData(trans('categories are loaded'), $data);
        } catch (ValidationException $e) {
            return $this->returnErrorMessage($e->getMessage(), $e->errors(), ['status' => 'fail'], 422);
        } catch (ModelNotFoundException $e) {
            abort(404);
        } catch (\Throwable $e) {
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'), [], ['status' => 'fail'], 422);
        }
    }

    //services
    public function servicesIndex(Request $request)
    {
        try {
            $cityId = $request->city_id ?? 'all';
            $locale = app()->getLocale();

            $slider = \Illuminate\Support\Facades\Cache::remember("home_services_slider_{$cityId}_{$locale}", 600, function () use ($request) {
                return Slider::where('type', 'services')
                    ->active()
                    ->when($request->city_id, function ($q) use ($request) {
                        $q->where('city_id', $request->city_id);
                    })->latest()->get();
            });

            $servicesCategory = \Illuminate\Support\Facades\Cache::remember("home_services_categories_{$cityId}_{$locale}", 600, function () use ($request) {
                return Category::where('type', 'services')
                    ->active()
                    ->whereNull('parent_id')
                    ->orderBy('sort', 'asc')
                    ->when($request->city_id, function ($q) use ($request) {
                        $q->where('city_id', $request->city_id);
                    })->get();
            });

            $sales = \Illuminate\Support\Facades\Cache::remember("home_services_sales_{$locale}", 600, function () {
                return CategoryOffer::whereType('service_category_sale')->latest()->get();
            });

            $data = [
                'slider' => SliderResource::collection($slider),
                'services_category' => ServicesCategoryResource::collection($servicesCategory),
                'sales' => ServiceCategorySaleResource::collection($sales),
            ];
            return $this->returnData(trans('services are loaded'), ['data' => $data]);
        } catch (ValidationException $e) {
            return $this->returnErrorMessage($e->getMessage(), $e->errors(), ['status' => 'fail'], 422);
        } catch (ModelNotFoundException $e) {
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
            $category = \Illuminate\Support\Facades\Cache::remember("api_services_details_{$categoryId}_{$locale}", 1800, function () use ($categoryId) {
                return Category::with(['translations', 'products.translations', 'appFeatures.translations'])
                    ->active()
                    ->with([
                        'products' => function ($query) {
                            $query->active();
                        }
                    ])
                    ->where('type', 'services')
                    ->findOrFail($categoryId);
            });
            $data = [
                'status' => 'success',
                'data' => new ServicesDetailsResource($category)
            ];
            return $this->returnData(trans('categories are loaded'), $data);
        } catch (ValidationException $e) {
            return $this->returnErrorMessage($e->getMessage(), $e->errors(), ['status' => 'fail'], 422);
        } catch (ModelNotFoundException $e) {
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
            $cityId = $request->city_id ?? 'all';
            $locale = app()->getLocale();

            [$slider, $sales, $childs] = \Illuminate\Support\Facades\Cache::remember("home_maid_{$cityId}_{$locale}", 600, function () use ($request) {
                $slider = Slider::with(['city.translations', 'category.translations'])
                    ->where('type', 'maid')
                    ->when($request->city_id, function ($q) use ($request) {
                        $q->where('city_id', $request->city_id);
                    })->latest()->get();

                $sales = CategoryOffer::with('translations')
                    ->active()
                    ->whereType('home_maid_sale')->get();

                $childs = Category::with('translations')
                    ->whereNotNull('parent_id')
                    ->whereHas('parent', function ($parentQuery) {
                        $parentQuery->where('slug', 'maid-host');
                    })->get();

                return [$slider, $sales, $childs];
            });

            $data = [
                'slider'       => SliderResource::collection($slider),
                'sales'        => HomeMaidSaleResource::collection($sales),
                'sub_services' => SubServiceResource::collection($childs),
            ];

            return $this->returnData(trans('services are loaded'), ['data' => $data]);
        } catch (ValidationException $e) {
            return $this->returnErrorMessage($e->getMessage(), $e->errors(), ['status' => 'fail'], 422);
        } catch (ModelNotFoundException $e) {
            abort(404);
        } catch (\Throwable $e) {
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'), [], ['status' => 'fail'], 422);
        }
    }
    public function homeMaidDetails($serviceId)
    {
        try {
            $service = Category::with([
                'translations',
                'appFeatures.translations'
            ])
                ->findOrFail($serviceId);
            $data = [
                'status' => 'success',
                'data' => new CustomServiceDetailsResource($service),
            ];

            return $this->returnData(trans('services are loaded'), $data);
        } catch (ValidationException $e) {
            return $this->returnErrorMessage($e->getMessage(), $e->errors(), ['status' => 'fail'], 422);
        } catch (ModelNotFoundException $e) {
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
                ->where('slug', 'flexible-home-visit')
                ->firstOrFail();
            return (new FlexibleOrderDetailsResource($service))->additional(['status' => 'success', 'message' => '']);
        } catch (ValidationException $e) {
            return $this->returnErrorMessage($e->getMessage(), $e->errors(), ['status' => 'fail'], 422);
        } catch (ModelNotFoundException $e) {
            abort(404);
        } catch (\Throwable $e) {
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'), [], ['status' => 'fail'], 422);
        }
    }
    public function scheduledOrderDetails()
    {
        try {
            $service = Category::with('translations')
                ->where('slug', 'scheduled-visits')
                ->firstOrFail();
            return (new ScheduledOrderDetailsResource($service))->additional(['status' => 'success', 'message' => '']);
        } catch (ValidationException $e) {
            return $this->returnErrorMessage($e->getMessage(), $e->errors(), ['status' => 'fail'], 422);
        } catch (ModelNotFoundException $e) {
            abort(404);
        } catch (\Throwable $e) {
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'), [], ['status' => 'fail'], 422);
        }
    }
    public function monthlyPackagesOrderDetails()
    {
        try {
            $service = Category::with('translations')
                ->where('slug', 'resident-worker-packages')
                ->firstOrFail();
            return (new MonthlyPackagesOrderDetailsResource($service))->additional(['status' => 'success', 'message' => '']);
        } catch (ValidationException $e) {
            return $this->returnErrorMessage($e->getMessage(), $e->errors(), ['status' => 'fail'], 422);
        } catch (ModelNotFoundException $e) {
            abort(404);
        } catch (\Throwable $e) {
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'), [], ['status' => 'fail'], 422);
        }
    }
    public function saleOrderDetails($saleId)
    {
        try {
            $sale = CategoryOffer::active()->findOrFail($saleId);
            return (new SaleOrderDetailsResource($sale))->additional(['status' => 'success', 'message' => '']);
        } catch (ValidationException $e) {
            return $this->returnErrorMessage($e->getMessage(), $e->errors(), ['status' => 'fail'], 422);
        } catch (ModelNotFoundException $e) {
            abort(404);
        } catch (\Throwable $e) {
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'), [], ['status' => 'fail'], 422);
        }
    }

    //home maid
    public function hostIndex(Request $request)
    {
        try {
            $cityId = $request->city_id ?? 'all';
            $locale = app()->getLocale();

            [$slider, $careHost, $sales] = \Illuminate\Support\Facades\Cache::remember("home_host_{$cityId}_{$locale}", 600, function () use ($request) {
                $slider = Slider::where('type', 'host')
                    ->active()
                    ->when($request->city_id, function ($q) use ($request) {
                        $q->where('city_id', $request->city_id);
                    })
                    ->latest()->get();

                $careHost = Category::whereIn('slug', ['hospitality-services', 'care-service', 'selfcare-service'])
                    ->active()
                    ->with([
                        'translations',
                        'subCategories' => function ($query) {
                            $query->active()->with('translations');
                        }
                    ])
                    ->when($request->city_id, function ($q) use ($request) {
                        $q->where('city_id', $request->city_id);
                    })->get();

                $sales = CategoryOffer::active()->whereType('care_host_sale')->with('translations')->get();

                return [$slider, $careHost, $sales];
            });

            $data = [
                'slider'    => SliderResource::collection($slider),
                'care_host' => CareHostServiceResource::collection($careHost),
                'sales'     => HomeMaidSaleResource::collection($sales),
            ];
            return $this->returnData(trans('services are loaded'), ['data' => $data]);
        } catch (ValidationException $e) {
            return $this->returnErrorMessage($e->getMessage(), $e->errors(), ['status' => 'fail'], 422);
        } catch (ModelNotFoundException $e) {
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
                'data' => new CustomServiceDetailsResource($service),
            ];

            return $this->returnData(trans('services are loaded'), $data);
        } catch (ValidationException $e) {
            return $this->returnErrorMessage($e->getMessage(), $e->errors(), ['status' => 'fail'], 422);
        } catch (ModelNotFoundException $e) {
            abort(404);
        } catch (\Throwable $e) {
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'), [], ['status' => 'fail'], 422);
        }
    }

    public function hostOrderDetails($serviceId)
    {
        try {
            $service = Category::active()
                ->findOrFail($serviceId);
            return (new HostOrderDetailsResource($service))->additional(['status' => 'success', 'message' => '']);
        } catch (ValidationException $e) {
            return $this->returnErrorMessage($e->getMessage(), $e->errors(), ['status' => 'fail'], 422);
        } catch (ModelNotFoundException $e) {
            abort(404);
        } catch (\Throwable $e) {
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'), [], ['status' => 'fail'], 422);
        }
    }

    public function careOrderDetails($serviceId)
    {
        try {
            $service = Category::active()
                ->findOrFail($serviceId);
            return (new CareOrderDetailsResource($service))->additional(['status' => 'success', 'message' => '']);
        } catch (ValidationException $e) {
            return $this->returnErrorMessage($e->getMessage(), $e->errors(), ['status' => 'fail'], 422);
        } catch (ModelNotFoundException $e) {
            abort(404);
        } catch (\Throwable $e) {
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'), [], ['status' => 'fail'], 422);
        }
    }
    public function selfCareOrderDetails($serviceId)
    {
        try {
            $service = Category::active()->findOrFail($serviceId);
            return (new SelfCareOrderDetailsResource($service))->additional(['status' => 'success', 'message' => '']);
        } catch (ValidationException $e) {
            return $this->returnErrorMessage($e->getMessage(), $e->errors(), ['status' => 'fail'], 422);
        } catch (ModelNotFoundException $e) {
            abort(404);
        } catch (\Throwable $e) {
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'), [], ['status' => 'fail'], 422);
        }
    }
    public function saleHostOrderDetails($saleId)
    {
        try {
            $sale = CategoryOffer::active()->findOrFail($saleId);
            return (new SaleOrderDetailsResource($sale))->additional(['status' => 'success', 'message' => '']);
        } catch (ValidationException $e) {
            return $this->returnErrorMessage($e->getMessage(), $e->errors(), ['status' => 'fail'], 422);
        } catch (ModelNotFoundException $e) {
            abort(404);
        } catch (\Throwable $e) {
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'), [], ['status' => 'fail'], 422);
        }
    }

    public function flowersAndGiftsIndex(Request $request)
    {
        try {
            $cityId = $request->city_id ?? 'all';
            $locale = app()->getLocale();

            $data = \Illuminate\Support\Facades\Cache::remember("home_flowers_and_gifts_{$cityId}_{$locale}", 600, function () use ($request) {
                $category = Category::with('translations')->where('slug', 'gifts-and-flowers')->active()->first();

                if (!$category) {
                    return null;
                }

                $sliders = Slider::with('city.translations', 'category.translations')
                    ->active()
                    ->where('category_id', $category->id)
                    ->when($request->city_id, function ($q) use ($request) {
                        $q->where('city_id', $request->city_id);
                    })
                    ->latest()
                    ->get();

                $subCategories = Category::with('translations')
                    ->where('parent_id', $category->id)
                    ->active()
                    ->orderBy('sort', 'asc')
                    ->get();

                return [
                    'category' => SampleCategoryResource::make($category),
                    'sliders' => SliderResource::collection($sliders),
                    'sub_categories' => SampleCategoryResource::collection($subCategories),
                ];
            });

            if (!$data) {
                return $this->returnErrorMessage(trans('Category not found'), [], ['status' => 'fail'], 404);
            }

            return $this->returnData(trans('flowers and gifts loaded'), ['data' => $data]);
        } catch (ValidationException $e) {
            return $this->returnErrorMessage($e->getMessage(), $e->errors(), ['status' => 'fail'], 422);
        } catch (ModelNotFoundException $e) {
            abort(404);
        } catch (\Throwable $e) {
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'), [], ['status' => 'fail'], 422);
        }
    }

    public function flowersAndGiftsProducts(Request $request)
    {
        try {
            $mainCategory = Category::where('slug', 'gifts-and-flowers')->active()->first();

            if (!$mainCategory) {
                return $this->returnErrorMessage(trans('Category not found'), [], ['status' => 'fail'], 404);
            }

            $products = Product::with(['translations', 'prices'])
                ->active()
                ->when($request->sub_category_id, function ($q) use ($request) {
                    $q->where('sub_category_id', $request->sub_category_id);
                }, function ($q) use ($mainCategory) {
                    $q->where('category_id', $mainCategory->id);
                })
                ->where(function ($q) {
                    $q->where('display_as', 'main')->orWhereNull('display_as');
                })
                ->paginate();

            return SimpleProductResource::collection($products);
        } catch (ValidationException $e) {
            return $this->returnErrorMessage($e->getMessage(), $e->errors(), ['status' => 'fail'], 422);
        } catch (ModelNotFoundException $e) {
            abort(404);
        } catch (\Throwable $e) {
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'), [], ['status' => 'fail'], 422);
        }
    }

    public function flowersAndGiftsProductDetails(Request $request, $id)
    {
        try {
            $product = Product::active()->findOrFail($id);

            $productSettings = ProductSetting::whereNull('parent_id')
                ->active()
                ->where(function ($q) use ($product) {
                    $q->whereHas('products', function ($pq) use ($product) {
                        $pq->where('products.id', $product->id);
                    })
                    ->orWhere('general', true);
                })
                ->with([
                    'translations',
                    'addonPrices',
                    'productSettings' => function ($q) use ($product) {
                        $q->active()
                            ->where(function ($subQ) use ($product) {
                                $subQ->whereHas('products', function ($pq) use ($product) {
                                    $pq->where('products.id', $product->id);
                                })
                                ->orWhere('general', true);
                            })
                            ->with(['translations', 'addonPrices']);
                    }
                ])
                ->get();

            $addons = Product::active()
                ->where('display_as', 'addon')
                ->where('category_id', $product->category_id)
                ->get();

            $data = [
                'product' => new SimpleProductResource($product),
                'category_settings' => ProductServiceSettingResource::collection($productSettings),
                'customizations' => ProductServiceSettingResource::collection($productSettings),
                'addons' => SimpleProductResource::collection($addons),
            ];

            return $this->returnData(trans('product details loaded'), ['data' => $data]);
        } catch (ValidationException $e) {
            return $this->returnErrorMessage($e->getMessage(), $e->errors(), ['status' => 'fail'], 422);
        } catch (ModelNotFoundException $e) {
            abort(404);
        } catch (\Throwable $e) {
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'), [], ['status' => 'fail'], 422);
        }
    }

}
