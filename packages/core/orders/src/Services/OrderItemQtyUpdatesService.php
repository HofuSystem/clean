<?php

namespace Core\Orders\Services;

use Core\Comments\Services\CommentingService;
use Core\Orders\Models\OrderItemQtyUpdate;
use Core\Orders\DataResources\OrderItemQtyUpdatesResource;

class OrderItemQtyUpdatesService
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
        return OrderItemQtyUpdate::select($selected)->get();
    }

    public function storeOrUpdate(array $data = [],$id = null){
        $recordData = array_filter($data,fn($key) => in_array($key, ['item_id','from','to','updater_email','translations']),ARRAY_FILTER_USE_KEY);
        $record     = OrderItemQtyUpdate::updateOrCreate(['id' => $id],$recordData);
        
        
        return $record;
    }

    public function get(int $id){
        return  OrderItemQtyUpdate::findOrFail($id);
    }

    public function delete(int $id,$final = false){
        $record             = OrderItemQtyUpdate::findOrFail($id);
        if($final){
            $record->forceDelete();
        }else{
            $record->delete();
        }
        return true;
    }

    public function dataTable($draw){

        $recordsTotal       = OrderItemQtyUpdate::count();
        $recordsFiltered    = OrderItemQtyUpdate::search()->count();
        $records            = OrderItemQtyUpdate::select(['id','item_id','from','to','updater_email'])
        ->with(['item'])
        ->search()->dataTable()->get();
        
        return [
            'draw'              => $draw,
            'recordsTotal'      => $recordsTotal,
            'recordsFiltered'   => $recordsFiltered,
            'data'              => OrderItemQtyUpdatesResource::collection($records)
        ];
    }

    public function order(array $list,$orderBy='order'){
        foreach ($list as  $value) {
            OrderItemQtyUpdate::find($value['id'])->update([$orderBy=>$value['order']]);
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
         OrderItemQtyUpdate::class,
         $id,
         $content,
         request()->user()->id,
         $parent_id
       );
    }
    public function totalCount(){
        return OrderItemQtyUpdate::count();
    }
    public function trashCount(){
        return OrderItemQtyUpdate::onlyTrashed()->count();
    }
    public function restore(int $id){
        $record = OrderItemQtyUpdate::onlyTrashed()->findOrFail($id);
        $record->restore();
        return $record;
    }
}
