<?php

namespace Core\Orders\Services;

use Core\Comments\Services\CommentingService;
use Core\Orders\Models\DeliveryPrice;
use Core\Orders\DataResources\DeliveryPricesResource;

class DeliveryPricesService
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
        return DeliveryPrice::select($selected)->get();
    }

    public function storeOrUpdate(array $data = [],$id = null){
        $recordData = array_filter($data,fn($key) => in_array($key, ['category_id','city_id','district_id','price','free_delivery','translations']),ARRAY_FILTER_USE_KEY);
        $record     = DeliveryPrice::updateOrCreate(['id' => $id],$recordData);
        
        
        return $record;
    }

    public function get(int $id){
        return  DeliveryPrice::findOrFail($id);
    }

    public function delete(int $id,$final = false){
        $record             = DeliveryPrice::findOrFail($id);
        if($final){
            $record->forceDelete();
        }else{
            $record->delete();
        }
        return true;
    }

    public function dataTable($draw){

        $recordsTotal       = DeliveryPrice::count();
        $recordsFiltered    = DeliveryPrice::search()->count();
        $records            = DeliveryPrice::select(['id','city_id','district_id','category_id','price','free_delivery'])
        ->with(['city','district','category'])
        ->search()->dataTable()->get();
        
        return [
            'draw'              => $draw,
            'recordsTotal'      => $recordsTotal,
            'recordsFiltered'   => $recordsFiltered,
            'data'              => DeliveryPricesResource::collection($records)
        ];
    }

    public function order(array $list,$orderBy='order'){
        foreach ($list as  $value) {
            DeliveryPrice::find($value['id'])->update([$orderBy=>$value['order']]);
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
         DeliveryPrice::class,
         $id,
         $content,
         request()->user()->id,
         $parent_id
       );
    }
    public function totalCount(){
        return DeliveryPrice::count();
    }
    public function trashCount(){
        return DeliveryPrice::onlyTrashed()->count();
    }
    public function restore(int $id){
        $record = DeliveryPrice::onlyTrashed()->findOrFail($id);
        $record->restore();
        return $record;
    }
}
