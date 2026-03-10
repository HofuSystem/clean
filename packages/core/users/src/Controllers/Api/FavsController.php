<?php

namespace Core\Users\Controllers\Api;


use App\Http\Controllers\Controller;
use Core\Categories\DataResources\Api\CategoryDetails\CustomServiceDetailsResource;
use Core\Categories\DataResources\Api\CategoryDetails\ServicesDetailsResource;
use Core\Categories\Models\Category;
use Core\Products\DataResources\Api\SimpleProductResource;
use Core\Products\Models\Product;
use Core\Settings\Traits\ApiResponse;
use Core\Users\Models\Fav;
use Core\Users\Requests\Api\FavRequest;
use Core\Users\Services\FavsService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;


class FavsController extends Controller
{
    use ApiResponse;

    public function __construct(protected FavsService $favsService ){}

    public function index()
    {
        try {
            $products   = Product::with('translations')->whereHas('favers',function($faversQuery){
                $faversQuery->where('user_id',auth()->id());
            })->get();
            $categories   = Category::with('translations')->whereIn('type',['clothes','sales','services'])->whereHas('favers',function($faversQuery){
                $faversQuery->where('user_id',auth()->id());
            })->get();
            $services   = Category::with('translations')->WhereNotIn('type',['clothes','sales','services'])->whereHas('favers',function($faversQuery){
                $faversQuery->where('user_id',auth()->id());
            })->get();
            $data = [
                'products'      => SimpleProductResource::collection($products),
                'categories'    => ServicesDetailsResource::collection($categories),
                'services'      => CustomServiceDetailsResource::collection($services),
            ];
            return $this->returnData(trans('favs'),['data'=>$data]);
        }catch(ValidationException $e){
            DB::rollback();
            return $this->returnErrorMessage($e->getMessage(),$e->errors(),[],422);
        } catch (\Throwable $e) {
            DB::rollback();
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],[],422);
        }
    }

    public function store(FavRequest $request)
    {
        $data = $request->validated();
        try {
            DB::beginTransaction();
            $favData = [
                'user_id'       => $request->user()?->id,
                'favs_type'     => $data['type'] == 'product' ? Product::class : Category::class,
                'favs_id'       => $data['type'] == 'product' ? $data['product_id'] : $data['category_id'],
            ];
            $fav = Fav::where('user_id',$favData['user_id'])
            ->where('favs_type',$favData['favs_type'])
            ->where('favs_id',$favData['favs_id'])
            ->first();
            $message = null;
            if($fav){
                $fav->forceDelete();
                $message = trans('fav delete successfully');
            }else{
                $record = $this->favsService->storeOrUpdate($favData);
                $message = trans('fav add successfully');
            }
            DB::commit();
            return $this->returnData($message,['data'=>$record ?? null]);
        }catch(ValidationException $e){
            DB::rollback();
            return $this->returnErrorMessage($e->getMessage(),$e->errors(),[],422);
        } catch (\Throwable $e) {
            DB::rollback();
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],[],422);
        }
    }


}
