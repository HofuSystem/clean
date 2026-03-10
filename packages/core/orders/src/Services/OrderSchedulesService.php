<?php

namespace Core\Orders\Services;

use Core\Comments\Services\CommentingService;
use Core\Orders\Models\OrderSchedule;
use Core\Orders\DataResources\OrderSchedulesResource;

class OrderSchedulesService
{
    public function __construct(protected CommentingService $commentingService){}

    public function selectable($cols = [],$with = [],$scopes = []){
        $query = OrderSchedule::underMyControl()->select($cols)->with($with);
        foreach($scopes as $scope){
            $query = $query->$scope();
        } 
        return $query->get();
    }

    public function storeOrUpdate(array $data = [],$id = null){
        $recordData = array_filter($data,fn($key) => in_array($key, ['client_id','type','receiver_day','receiver_date','receiver_time','receiver_to_time','delivery_day','delivery_date','delivery_time','delivery_to_time','receiver_address_id','delivery_address_id','note','translations']),ARRAY_FILTER_USE_KEY);
        $record     = OrderSchedule::updateOrCreate(['id' => $id],$recordData);
        
        
        return $record;
    }

    public function get(int $id){
        return  OrderSchedule::findOrFail($id);
    }

    public function delete(int $id,$final = false){
        $record             = OrderSchedule::underMyControl()->where('id',$id)->first();
        if($final){
            $record->forceDelete();
        }else{
            $record->delete();
        }
        return true;
    }

    public function dataTable($draw){

        $recordsTotal       = OrderSchedule::underMyControl()->count();
        $recordsFiltered    = OrderSchedule::underMyControl()->search()->count();
        $records            = OrderSchedule::underMyControl()->select(['id','client_id','type','receiver_day','receiver_date','receiver_time','receiver_to_time','delivery_day','delivery_date','delivery_time','delivery_to_time'])
        ->with(['client'])
        ->search()->dataTable()->get();
        
        return [
            'draw'              => $draw,
            'recordsTotal'      => $recordsTotal,
            'recordsFiltered'   => $recordsFiltered,
            'data'              => OrderSchedulesResource::collection($records)
        ];
    }

    public function order(array $list,$orderBy='order'){
        foreach ($list as  $value) {
            OrderSchedule::underMyControl()->find($value['id'])->update([$orderBy=>$value['order']]);
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
         OrderSchedule::class,
         $id,
         $content,
         request()->user()->id,
         $parent_id
       );
    }
    public function totalCount(){
        return OrderSchedule::underMyControl()->count();
    }
    public function trashCount(){
        return OrderSchedule::underMyControl()->onlyTrashed()->count();
    }
    public function restore(int $id){
        $record = OrderSchedule::onlyTrashed()->findOrFail($id);
        $record->restore();
        return $record;
    }
}
