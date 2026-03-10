<?php

namespace Core\Coupons\Services;

use Core\Comments\Services\CommentingService;
use Core\Coupons\Models\Coupon;
use Core\Coupons\DataResources\CouponsResource;
use Core\Products\Services\ProductsService;
use Core\Categories\Services\CategoriesService;
use Core\Users\Services\UsersService;
use Core\Users\Services\RolesService;

class CouponsService
{
    public function __construct(protected CommentingService $commentingService,protected ProductsService $productsService,protected CategoriesService $categoriesService,protected UsersService $usersService,protected RolesService $rolesService){}

    public function selectable(string $key,string $value){
        $selected = ['id'];
        if(!in_array($key,["title"])){
            $selected[] = $key;
        }
        if(!in_array($value,["title"])){
            $selected[] = $value;
        }
        return Coupon::select($selected)->get();
    }
    public function findMatching(
        string | null   $applying     = 'auto',
        string | null   $code         = null,
        string | int    $userId       = null,
        string | null   $orderType    = null,
        string | array  $productsIds  = null,
        string | float  $orderValue   = null,
    ){
        return Coupon::findMatching($applying,$code,$userId,$orderType,$productsIds,$orderValue);
    }
    public function storeOrUpdate(array $data = [],$id = null){
        $recordData = array_filter($data,fn($key) => in_array($key, ['status','applying','code','max_use','max_use_per_user','payment_method','start_at','end_at','order_type','all_products','all_users','order_minimum','order_maximum','type','value','max_value','translations']),ARRAY_FILTER_USE_KEY);
        $record     = Coupon::updateOrCreate(['id' => $id],$recordData);
        $record->products()->sync($data['products'] ?? []);
        $record->categories()->sync($data['categories'] ?? []);
        $record->users()->sync($data['users'] ?? []);
        $record->roles()->sync($data['roles'] ?? []);
        
        
        return $record;
    }

    public function get(int $id){
        return  Coupon::findOrFail($id);
    }

    public function delete(int $id,$final = false){
        $record             = Coupon::findOrFail($id);
        if($final){
            $record->forceDelete();
        }else{
            $record->delete();
        }
        return true;
    }

    public function dataTable($draw){

        $recordsTotal       = Coupon::count();
        $recordsFiltered    = Coupon::search()->count();
        $records            = Coupon::select(['id','status','applying','code','start_at','end_at','type','value','max_value'])
        ->withCount('orders')
        ->search()->dataTable()->get();
        return [
            'draw'              => $draw,
            'recordsTotal'      => $recordsTotal,
            'recordsFiltered'   => $recordsFiltered,
            'data'              => CouponsResource::collection($records)
        ];
    }

    public function order(array $list,$orderBy='order'){
        foreach ($list as  $value) {
            Coupon::find($value['id'])->update([$orderBy=>$value['order']]);
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
         Coupon::class,
         $id,
         $content,
         request()->user()->id,
         $parent_id
       );
    }
    public function totalCount(){
        return Coupon::count();
    }
    public function trashCount(){
        return Coupon::onlyTrashed()->count();
    }
    public function restore(int $id){
        $record = Coupon::onlyTrashed()->findOrFail($id);
        $record->restore();
        return $record;
    }
}
