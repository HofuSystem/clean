<?php

namespace Core\Categories\Services;

use Core\Comments\Services\CommentingService;
use Core\Categories\Models\Price;
use Core\Categories\DataResources\PricesResource;

class PricesService
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
        return Price::select($selected)->get();
    }

    public function storeOrUpdate(array $data = [],$id = null){
        $recordData = array_filter($data,fn($key) => in_array($key, ['priceable_type','priceable_id','city_id','price','cost','translations']),ARRAY_FILTER_USE_KEY);
        $record     = Price::updateOrCreate(['id' => $id],$recordData);
        
        
        return $record;
    }

    public function get(int $id){
        return  Price::findOrFail($id);
    }

    public function delete(int $id,$final = false){
        $record             = Price::findOrFail($id);
        if($final){
            $record->forceDelete();
        }else{
            $record->delete();
        }
        return true;
    }

    public function dataTable($draw){

        $recordsTotal       = Price::count();
        $recordsFiltered    = Price::search()->count();
        $records            = Price::select(['id','priceable_type','priceable_id','city_id','price'])
        ->with(['city'])
        ->search()->dataTable()->get();
        
        return [
            'draw'              => $draw,
            'recordsTotal'      => $recordsTotal,
            'recordsFiltered'   => $recordsFiltered,
            'data'              => PricesResource::collection($records)
        ];
    }

    public function order(array $list,$orderBy='order'){
        foreach ($list as  $value) {
            Price::find($value['id'])->update([$orderBy=>$value['order']]);
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
         Price::class,
         $id,
         $content,
         request()->user()->id,
         $parent_id
       );
    }
    public function totalCount(){
        return Price::count();
    }
    public function trashCount(){
        return Price::onlyTrashed()->count();
    }
    public function restore(int $id){
        $record = Price::onlyTrashed()->findOrFail($id);
        $record->restore();
        return $record;
    }
}
