<?php

namespace Core\Workers\Services;

use Core\Comments\Services\CommentingService;
use Core\Workers\Models\WorkerDay;
use Core\Workers\DataResources\WorkerDaysResource;

class WorkerDaysService
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
        return WorkerDay::select($selected)->get();
    }

    public function storeOrUpdate(array $data = [],$id = null){
        $recordData = array_filter($data,fn($key) => in_array($key, ['worker_id','date','type','translations']),ARRAY_FILTER_USE_KEY);
        $record     = WorkerDay::updateOrCreate(['id' => $id],$recordData);
        
        
        return $record;
    }

    public function get(int $id){
        return  WorkerDay::findOrFail($id);
    }

    public function delete(int $id,$final = false){
        $record             = WorkerDay::findOrFail($id);
        if($final){
            $record->forceDelete();
        }else{
            $record->delete();
        }
        return true;
    }

    public function dataTable($draw){

        $recordsTotal       = WorkerDay::count();
        $recordsFiltered    = WorkerDay::search()->count();
        $records            = WorkerDay::select(['id','worker_id','date','type'])
        ->with(['worker'])
        ->search()->dataTable()->get();
        
        return [
            'draw'              => $draw,
            'recordsTotal'      => $recordsTotal,
            'recordsFiltered'   => $recordsFiltered,
            'data'              => WorkerDaysResource::collection($records)
        ];
    }

    public function order(array $list,$orderBy='order'){
        foreach ($list as  $value) {
            WorkerDay::find($value['id'])->update([$orderBy=>$value['order']]);
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
         WorkerDay::class,
         $id,
         $content,
         request()->user()->id,
         $parent_id
       );
    }
    public function totalCount(){
        return WorkerDay::count();
    }
    public function trashCount(){
        return WorkerDay::onlyTrashed()->count();
    }
    public function restore(int $id){
        $record = WorkerDay::onlyTrashed()->findOrFail($id);
        $record->restore();
        return $record;
    }
}
