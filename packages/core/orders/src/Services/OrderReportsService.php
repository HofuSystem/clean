<?php

namespace Core\Orders\Services;

use Core\Comments\Services\CommentingService;
use Core\Orders\Models\OrderReport;
use Core\Orders\DataResources\OrderReportsResource;

class OrderReportsService
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
        return OrderReport::select($selected)->get();
    }

    public function storeOrUpdate(array $data = [],$id = null){
        $recordData = array_filter($data,fn($key) => in_array($key, ['order_id','user_id','report_reason_id','desc_location','translations']),ARRAY_FILTER_USE_KEY);
        $record     = OrderReport::updateOrCreate(['id' => $id],$recordData);
        
        
        return $record;
    }

    public function get(int $id){
        return  OrderReport::findOrFail($id);
    }

    public function delete(int $id,$final = false){
        $record             = OrderReport::findOrFail($id);
        if($final){
            $record->forceDelete();
        }else{
            $record->delete();
        }
        return true;
    }

    public function dataTable($draw){

        $recordsTotal       = OrderReport::count();
        $recordsFiltered    = OrderReport::search()->count();
        $records            = OrderReport::select(['id','order_id','user_id','report_reason_id','desc_location'])
        ->with(['order','user','reportReason'])
        ->search()->dataTable()->get();
        
        return [
            'draw'              => $draw,
            'recordsTotal'      => $recordsTotal,
            'recordsFiltered'   => $recordsFiltered,
            'data'              => OrderReportsResource::collection($records)
        ];
    }

    public function order(array $list,$orderBy='order'){
        foreach ($list as  $value) {
            OrderReport::find($value['id'])->update([$orderBy=>$value['order']]);
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
         OrderReport::class,
         $id,
         $content,
         request()->user()->id,
         $parent_id
       );
    }
    public function totalCount(){
        return OrderReport::count();
    }
    public function trashCount(){
        return OrderReport::onlyTrashed()->count();
    }
    public function restore(int $id){
        $record = OrderReport::onlyTrashed()->findOrFail($id);
        $record->restore();
        return $record;
    }
}
