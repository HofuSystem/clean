<?php

namespace Core\Products\Services;

use Core\Comments\Services\CommentingService;
use Core\Products\Models\Product;
use Core\Products\DataResources\ProductsResource;
use Core\Categories\Services\PricesService;
use Core\Products\DataResources\ProductCardResource;
use Core\Settings\Helpers\ToolHelper;
use Core\Users\Models\Contract;
use Illuminate\Support\Facades\DB;

class ProductsService
{
    protected static $currentContract;
    public function __construct(protected CommentingService $commentingService,protected PricesService $pricesService){}
    function getProductsCard($type = null,$user = null)
    {
        if($user){
            ProductsService::setCurrentContract($user);
            ProductCardResource::$cityId = $user?->profile?->city_id;
            ProductCardResource::$user   = $user;
        }
       return ProductCardResource::collection(Product::with(['translations','category.translations','subCategory.translations','prices','contractsPrices','contractCustomerPrices'])->get());
    }
    public function selectable(string $key,string $value,array $selected = [],$with = []){
        $selected[] = 'id';
        if(!in_array($key,["name","desc"])){
            $selected[] = $key;
        }
        if(!in_array($value,["name","desc"])){
            $selected[] = $value;
        }
        return Product::select($selected)->with($with)->get();
    }

    public function storeOrUpdate(array $data = [],$id = null){
        $recordData = array_filter($data,fn($key) => in_array($key, ['image','type','sku','is_package','category_id','sub_category_id','price','cost','quantity','status','translations']),ARRAY_FILTER_USE_KEY);
        $record     = Product::updateOrCreate(['id' => $id],$recordData);

        if(!isset($id)){
            //saving on create the related pricesItems
            $pricesItems            = $data['prices'] ?? [];
            foreach ($pricesItems as $index => $itemValues) {
                $itemValues['priceable_id']     = $record->id;
                $itemValues['priceable_type']   = Product::class;
                $this->pricesService->storeOrUpdate($itemValues,$itemValues['id'] ?? null);
            }
        }

        return $record;
    }

    public function get(int $id){
        return  Product::findOrFail($id);
    }

    public function delete(int $id,$final = false){
        $record             = Product::findOrFail($id);
        if($final){
            $record->forceDelete();
        }else{
            $record->delete();
        }
        return true;
    }

    public function dataTable($draw){

        $recordsTotal       = Product::count();
        $recordsFiltered    = Product::search()->count();
        $records            = Product::with(['category','subCategory'])->search()->dataTable()->get();
        return [
            'draw'              => $draw,
            'recordsTotal'      => $recordsTotal,
            'recordsFiltered'   => $recordsFiltered,
            'data'              => ProductsResource::collection($records)
        ];
    }

    public function order(array $list,$orderBy='order'){
        foreach ($list as  $value) {
            Product::find($value['id'])->update([$orderBy=>$value['order']]);
        }
    }
    public function import(array $items){
        foreach ($items as  $index => $item) {
            $items[$index] = $this->storeOrUpdate($item,$item['id'] ?? null);
        }
        return $items;
    }
    public function comment(int $id,string $content,int | null $parent_id){
       return $this->commentingService->comment(
         Product::class,
         $id,
         $content,
         request()->user()->id,
         $parent_id
       );
    }
    public function totalCount(){
        return Product::count();
    }
    public function trashCount(){
        return Product::onlyTrashed()->count();
    }
    public function restore(int $id){
        $record = Product::onlyTrashed()->findOrFail($id);
        $record->restore();
        return $record;
    }
    public function bestSales($request){
        $to = now()->format('Y-m-d');
        if ($request->to) {
            $to = $request->to;
        }
        //dd($to,$request->from);
        $query = Product::when($request->from, function ($q) use ($request, $to) {
            return $q->whereBetween('orders.created_at', [$request->from, $to]);
        })  ->where('products.status','active')
            ->where('orders.status','finished')
            ->select('products.id', 'products.name', DB::raw('SUM(order_items.quantity) as total_quantity'))
            ->join('order_items', 'order_items.product_id', '=', 'products.id')
            ->groupBy('products.id', 'products.name')
            ->orderByDesc(DB::raw('SUM(order_items.quantity)'));

        $top_sales_products = $query->whereIn('products.category_id',['10','11','12'])->take(12)->get();

        $top_sales_package_products = $query->whereIn('products.category_id',['13'])->where('is_package',true)->take(12)->get();

  
        return view('dashboard.best_sales',['top_sales_products'=>$top_sales_products,'top_sales_package_products'=>$top_sales_package_products]);
    }
    public static function setCurrentContract($user){
        if(!$user){
            return null;
        }
        $userContract = Contract::with(['contractPrices','contractCustomerPrices'])
        ->where('client_id',$user->id)
        ->currentActive()
        ->first();
        if($userContract){
            self::$currentContract = $userContract;
        }else{
            if(!$user->operator){
               return null;
            }
            $contract = Contract::with(['contractPrices','contractCustomerPrices'])
                ->where('client_id',$user->operator->id)
                ->currentActive()
                ->first();
            if($contract){
                self::$currentContract = $contract;
            }
        }
        return self::$currentContract;
    }
    public static function getCurrentContract(){
        return self::$currentContract;
    }
    public static function getProductData($user,$product){
        $price      = $product->price;
        $cost       = $product->cost;
        if(self::$currentContract){
            if(self::$currentContract->client_id == $user->id){
                $contractPrice = self::$currentContract->contractPrices->where('product_id',$product->id)->first();
                if($contractPrice){
                    $price = $contractPrice->price;
                    $cost = $contractPrice->cost;
                }
            }else{
                $contractCustomerPrice = self::$currentContract->contractCustomerPrices->where('product_id',$product->id)->first();
                if($contractCustomerPrice){
                    $price  +=  $contractCustomerPrice->over_price;
                    $cost   += $contractCustomerPrice->over_price;
                }
            }
        }else{
            $outOfContractPriceData = self::getProductOutOfContractPriceData($product,$user?->profile?->city_id);
            if($outOfContractPriceData){
                $price = $outOfContractPriceData['price'];
                $cost = $outOfContractPriceData['cost'];
            }
        }
        return [
            'price' => $price,
            'cost' => $cost,
        ];
    }
    public static function getProductOutOfContractPriceData($product,$cityId){
        $price = $product->price;
        $cost = $product->cost;
        $priceCityData = isset($product->prices) ? $product->prices->where('city_id',$cityId)->first() : null;
        if($priceCityData){
            $price = $priceCityData->price;
            $cost = $priceCityData->cost;
        }
        return [
            'price' => ToolHelper::getPriceBasedOnCurrentWeekDay($price),
            'cost' => $cost,
        ];
    }
}
