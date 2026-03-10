<?php

namespace Core\Orders\Services;

use Core\Comments\Services\CommentingService;
use Core\Orders\Models\OrderTypesOfDatum;
use Core\Orders\DataResources\OrderTypesOfDatasResource;

class OrderTypesOfDatasService
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
        return OrderTypesOfDatum::select($selected)->get();
    }

    public function storeOrUpdate(array $data = [],$id = null){
        $recordData = array_filter($data,fn($key) => in_array($key, ['order_id','key','value','translations']),ARRAY_FILTER_USE_KEY);
        $record     = OrderTypesOfDatum::updateOrCreate(['id' => $id],$recordData);
        
        
        return $record;
    }

    public function get(int $id){
        return  OrderTypesOfDatum::findOrFail($id);
    }

    public function delete(int $id,$final = false){
        $record             = OrderTypesOfDatum::findOrFail($id);
        if($final){
            $record->forceDelete();
        }else{
            $record->delete();
        }
        return true;
    }

    public function dataTable($draw){

        $recordsTotal       = OrderTypesOfDatum::count();
        $recordsFiltered    = OrderTypesOfDatum::search()->count();
        $records            = OrderTypesOfDatum::select(['id','order_id','key','value'])
        ->with(['order'])
        ->search()->dataTable()->get();
        
        return [
            'draw'              => $draw,
            'recordsTotal'      => $recordsTotal,
            'recordsFiltered'   => $recordsFiltered,
            'data'              => OrderTypesOfDatasResource::collection($records)
        ];
    }

    public function order(array $list,$orderBy='order'){
        foreach ($list as  $value) {
            OrderTypesOfDatum::find($value['id'])->update([$orderBy=>$value['order']]);
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
         OrderTypesOfDatum::class,
         $id,
         $content,
         request()->user()->id,
         $parent_id
       );
    }
    public function totalCount(){
        return OrderTypesOfDatum::count();
    }
    public function trashCount(){
        return OrderTypesOfDatum::onlyTrashed()->count();
    }
    public function restore(int $id){
        $record = OrderTypesOfDatum::onlyTrashed()->findOrFail($id);
        $record->restore();
        return $record;
    }
}
