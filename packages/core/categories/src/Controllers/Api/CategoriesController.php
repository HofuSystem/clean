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

class CategoriesController extends Controller
{
    use ApiResponse;
    public function __construct(protected CategoriesService $categoriesService) {}


    public function index(Request $request)
    {
        try {
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

            $notifications = BannerNotification::active()
                ->where('publish_date', '<=', now())
                ->where('expired_date', '>=', now())
                ->WhereHas('users', function ($userNotificationQuery) {
                    $userNotificationQuery->where('users.id', auth('api')->id())
                        ->where('users_notifications.next_vision_date', '<=', now()->format("Y-m-d h:i:s"))
                        ->orWhereNull('users_notifications.next_vision_date');
                })
                ->orWhereDoesntHave('users', function ($userNotificationQuery) {
                    $userNotificationQuery->where('users.id', auth('api')->id());
                })
                ->get();
                foreach($notifications as $notification){
                    DB::table('users_notifications')->updateOrInsert([
                        'user_id' => auth('api')->id(),
                        'notifications_type' => BannerNotification::class,
                        'notifications_id' => $notification->id,
                    ], [
                        'status' => 'sent',
                        'read_at' => now()->format("Y-m-d h:i:s"),
                        'next_vision_date' => now()->addHours($notification->next_vision_hour)->format("Y-m-d h:i:s"),
                    ]);
                }
            $data = [
                'slider'                => SliderResource::collection($slider),
                'clothes_category'      => ClothesCategoryResource::collection($clothesCategory),
                'economy_bags'          => PackageDetailsResource::collection($economyBags),
                'notifications'         => BannerNotificationResource::collection($notifications),
            ];

            return $this->returnData(trans('categories are loaded'), ['data' => $data]);
        } catch (ValidationException $e) {
            return $this->returnErrorMessage($e->getMessage(), $e->errors(), ['status' => 'fail'], 422);
        } catch (ModelNotFoundException  $e) {
           abort(404);
        }catch (\Throwable $e) {
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'), [], ['status' => 'fail'], 422);
        }
    }
    public function details(Request $request, $categoryId)
    {
        try {
            $category = Category::with(['translations', 'subCategories.productsSub.translations'])
                ->active()
                ->with(['subCategories'=>function($query){
                    $query->active()
                    ->orderBy('categories.sort', 'asc')
                    ->with(['productsSub' => function($query){
                        $query->active();
                    }]);
                }])
                ->where('type', 'clothes')
                ->where('is_package', false)
                ->findOrFail($categoryId);
            $data = [
                'status' => 'success',
                'data'   => new ClothesDetailsResource($category)
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
                'data'   => new PackageDetailsResource($category)
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
            $slider             = Slider::where('type', 'services')
            ->active()
            ->when($request->city_id, function ($q) use ($request) {
                $q->where('city_id', $request->city_id);
            })->latest()->get();
            $servicesCategory   = Category::where('type', 'services')
            ->active()
            ->whereNull('parent_id')
            ->orderBy('sort', 'asc')
            ->when($request->city_id, function ($q) use ($request) {
                $q->where('city_id', $request->city_id);
            })->get();
            $sales                      = CategoryOffer::whereType('service_category_sale')->latest()->get();
            $data = [
                'slider'                => SliderResource::collection($slider),
                'services_category'     => ServicesCategoryResource::collection($servicesCategory),
                'sales'                 => ServiceCategorySaleResource::collection($sales),
            ];
            return $this->returnData(trans('services are loaded'), ['data' => $data]);
        } catch (ValidationException $e) {
            return $this->returnErrorMessage($e->getMessage(), $e->errors(), ['status' => 'fail'], 422);
        } catch (ModelNotFoundException  $e) {
           abort(404);
        }catch (\Throwable $e) {
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'), [], ['status' => 'fail'], 422);
        }
    }
    public function servicesDetails(Request $request, $categoryId)
    {
        try {
            $category = Category::with(['translations', 'products.translations'])
                ->active()
                ->with(['products' => function ($query) {
                    $query->active();
                }])
                ->where('type', 'services')
                ->findOrFail($categoryId);
            $data = [
                'status' => 'success',
                'data'   => new ServicesDetailsResource($category)
            ];
            return $this->returnData(trans('categories are loaded'), $data);
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
            $data = [
                'slider'        => SliderResource::collection($slider),
                'sales'         => HomeMaidSaleResource::collection($sales),
                'sub_services'  => SubServiceResource::collection($childs),
            ];

            return $this->returnData(trans('services are loaded'), ['data' => $data]);
        } catch (ValidationException $e) {
            return $this->returnErrorMessage($e->getMessage(), $e->errors(), ['status' => 'fail'], 422);
        } catch (ModelNotFoundException  $e) {
           abort(404);
        }catch (\Throwable $e) {
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'), [], ['status' => 'fail'], 422);
        }
    }
    public function homeMaidDetails($serviceId)
    {
        try {
            $service = Category::with('translations')
            ->findOrFail($serviceId);
            $data = [
                'status'   => 'success',
                'data'     => new CustomServiceDetailsResource($service),
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
    public function flexibleOrderDetails()
    {
        try {
            $service = Category::with('translations')
                ->active()
                ->where('slug', 'flexible-home-visit')
                ->firstOrFail();
            return (new FlexibleOrderDetailsResource($service))->additional(['status' => 'success', 'message' => '']);
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
            return (new ScheduledOrderDetailsResource($service))->additional(['status' => 'success', 'message' => '']);
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
            return (new MonthlyPackagesOrderDetailsResource($service))->additional(['status' => 'success', 'message' => '']);
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
            return (new SaleOrderDetailsResource($sale))->additional(['status' => 'success', 'message' => '']);
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

            $sales = CategoryOffer::active()->whereType('care_host_sale')->get();

            $data = [
                'slider'        => SliderResource::collection($slider),
                'care_host'     => CareHostServiceResource::collection($careHost),
                'sales'         => HomeMaidSaleResource::collection($sales),
            ];
            return $this->returnData(trans('services are loaded'), ['data' => $data]);
        } catch (ValidationException $e) {
            return $this->returnErrorMessage($e->getMessage(), $e->errors(), ['status' => 'fail'], 422);
        } catch (ModelNotFoundException  $e) {
           abort(404);
        }catch (\Throwable $e) {
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
                'data'     => new CustomServiceDetailsResource($service),
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
            return (new HostOrderDetailsResource($service))->additional(['status' => 'success', 'message' => '']);
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
            return (new CareOrderDetailsResource($service))->additional(['status' => 'success', 'message' => '']);
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
            return (new SelfCareOrderDetailsResource($service))->additional(['status' => 'success', 'message' => '']);
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
            return (new SaleOrderDetailsResource($sale))->additional(['status' => 'success', 'message' => '']);
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
