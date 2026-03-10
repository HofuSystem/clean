<?php

namespace Core\Orders\Services;

use Core\Comments\Services\CommentingService;
use Core\Orders\Models\Cart;
use Core\Orders\DataResources\CartsResource;
use Core\Products\Models\Product;
use Core\Settings\Helpers\ToolHelper;

class CartsService
{
    public function __construct(protected CommentingService $commentingService){}

    public function selectable(string $key,string $value){
        $selected = ['id'];
        if(!in_array($key,[])){
            $selected[] = $key;
        }
        if(!in_array($value,[])){
            $selected[] = $value;
        }
        return Cart::select($selected)->get();
    }

    public function storeOrUpdate(array $data = [],$id = null){
        $recordData = array_filter($data,fn($key) => in_array($key, ['user_id','phone','data','translations']),ARRAY_FILTER_USE_KEY);
        $record     = Cart::find($id);
        if(!isset($record)){
            $record = Cart::where('user_id',$recordData['user_id'])->first();
        }
        $record     = Cart::updateOrCreate(['id'=>$record?->id],$recordData);

        return $record;
    }
    public function carHasNewData($userId,$data){
        try{
            $data = ToolHelper::isJson($data) ? json_decode($data, true) : $data;
            $cart = Cart::where('user_id', $userId)->firstOrNew();
            $oldData = ToolHelper::isJson($cart->data) ? json_decode($cart->data, true) : $cart->data;
            $oldData = ToolHelper::isJson($oldData) ? json_decode($oldData, true) : $oldData;
            $oldData = collect($oldData ?? [] )->keyBy('id')->map(function($item){return $item['quantity'] ?? 0;})->toArray();
            if(count($oldData) !== count($data)){
                return true;
            }
            foreach($data as $item){
                if($oldData[$item['id']] !== $item['quantity']){
                   return true; 
                }
            }
            return false;

        }catch(\Throwable $e){
            return true;
        }
    }

    public function get(int $id){
        return  Cart::findOrFail($id);
    }
    public function getOrderItems(Cart $cart){
        $cartItems      =  json_decode($cart->data);
        $cartItems      =  ToolHelper::isJson($cartItems) ? json_decode($cartItems) : $cartItems;
        $finalItems     =  [];
        foreach ($cartItems as $key => $cartItem) {
            $id         = \Str::random(10);
            $product    = Product::find($cartItem->id);
            if($product){
                $finalItems[$id] = [
                    "cartItemId"=>  $id,
                    "product"   =>  [
                       "id"                 =>  $product->id,
                       "sku"                =>  $product->sku,
                       "image"              =>  $product->image,
                       "name"               =>  $product->name,
                       "price"              =>  $product->price,
                       "type"               =>  $product->type,
                       "category"           =>  $product->category?->name,
                       "category_id"        =>  $product->category?->id,
                       "sub_category"       =>  $product->subCategory?->name,
                       "sub_category_id"    =>  $product->subCategory?->id
                    ],
                    "qty"=>$cartItem->quantity ?? 1
                ];
            }
        }
        return $finalItems;
    }

    public function delete(int $id,$final = false){
        $record             = Cart::findOrFail($id);
        if($final){
            $record->forceDelete();
        }else{
            $record->delete();
        }
        return true;
    }

    public function dataTable($draw){

        $recordsTotal       = Cart::count();
        $recordsFiltered    = Cart::search()->count();
        $records            = Cart::select(['id','user_id','phone','data','created_at','updated_at'])
        ->where(\DB::raw('LENGTH(data)'), '>', 6)
        ->whereHas('user')
        ->where('data','!=',null)
        ->where('data','!=','"[]"')
        ->with(['user'])
        ->search()->dataTable()->get();

        return [
            'draw'              => $draw,
            'recordsTotal'      => $recordsTotal,
            'recordsFiltered'   => $recordsFiltered,
            'data'              => CartsResource::collection($records)
        ];
    }

    public function order(array $list,$orderBy='order'){
        foreach ($list as  $value) {
            Cart::find($value['id'])->update([$orderBy=>$value['order']]);
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
         Cart::class,
         $id,
         $content,
         request()->user()->id,
         $parent_id
       );
    }
    public function totalCount(){
        return Cart::count();
    }
    public function trashCount(){
        return Cart::onlyTrashed()->count();
    }
    public function restore(int $id){
        $record = Cart::onlyTrashed()->findOrFail($id);
        $record->restore();
        return $record;
    }
}
