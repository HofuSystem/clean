<?php

namespace Core\Orders\Services;

use Core\Comments\Services\CommentingService;
use Core\Orders\Models\OrderRepresentative;
use Core\Orders\DataResources\OrderRepresentativesResource;

class OrderRepresentativesService
{
    public function __construct(
        protected CommentingService $commentingService,
        protected OrderItemsService $orderItemsService,
        protected OrderHistoryService $orderHistoryService
    ){}

    public function selectable(string $key,string $value){
        $selected = ['id'];
        if(!in_array($key,[])){
            $selected[] = $key;
        }
        if(!in_array($value,[])){
            $selected[] = $value;
        }
        return OrderRepresentative::select($selected)->get();
    }

    public function storeOrUpdate(array $data = [],$id = null){
        $recordData = array_filter($data,fn($key) => in_array($key, ['order_id','representative_id','type','date','time','to_time','lat','lng','location','has_problem','for_all_items','translations']),ARRAY_FILTER_USE_KEY);
        
        // Get old record if updating
        $oldRecord = $id ? OrderRepresentative::find($id) : null;
        
        $record     = OrderRepresentative::updateOrCreate(['id' => $id],$recordData);
        $record->items()->sync($data['items'] ?? []);
        

        return $record;
    }

    public function get(int $id){
        return  OrderRepresentative::findOrFail($id);
    }

    public function delete(int $id,$final = false){
        $record             = OrderRepresentative::findOrFail($id);
        if($final){
            $record->forceDelete();
        }else{
            $record->delete();
        }
        return true;
    }

    public function dataTable($draw){

        $recordsTotal       = OrderRepresentative::count();
        $recordsFiltered    = OrderRepresentative::search()->count();
        $records            = OrderRepresentative::select(['id','order_id','representative_id','type','date','time','to_time','location','has_problem'])
        ->with(['order','representative'])
        ->search()->dataTable()->get();
        
        return [
            'draw'              => $draw,
            'recordsTotal'      => $recordsTotal,
            'recordsFiltered'   => $recordsFiltered,
            'data'              => OrderRepresentativesResource::collection($records)
        ];
    }

    public function order(array $list,$orderBy='order'){
        foreach ($list as  $value) {
            OrderRepresentative::find($value['id'])->update([$orderBy=>$value['order']]);
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
         OrderRepresentative::class,
         $id,
         $content,
         request()->user()->id,
         $parent_id
       );
    }
    public function totalCount(){
        return OrderRepresentative::count();
    }
    public function trashCount(){
        return OrderRepresentative::onlyTrashed()->count();
    }
    public function restore(int $id){
        $record = OrderRepresentative::onlyTrashed()->findOrFail($id);
        $record->restore();
        return $record;
    }
}
