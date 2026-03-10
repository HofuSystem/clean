<?php

namespace Core\Pages\Services;

use Core\Comments\Services\CommentingService;
use Core\Pages\Models\WorkStep;
use Core\Pages\DataResources\WorkStepsResource;

class WorkStepsService
{
    public function __construct(protected CommentingService $commentingService){}

    public function selectable($cols = [],$with = [],$scopes = []){
        $query = WorkStep::underMyControl()->select($cols)->with($with);
        foreach($scopes as $scope){
            $query = $query->$scope();
        } 
        return $query->get();
    }

    public function storeOrUpdate(array $data = [],$id = null){
        $recordData = array_filter($data,fn($key) => in_array($key, ['icon','order','is_active','translations']),ARRAY_FILTER_USE_KEY);
        $record     = WorkStep::updateOrCreate(['id' => $id],$recordData);
        
        
        return $record;
    }

    public function get(int $id){
        return  WorkStep::findOrFail($id);
    }

    public function delete(int $id,$final = false){
        $record             = WorkStep::underMyControl()->where('id',$id)->first();
        if($final){
            $record->forceDelete();
        }else{
            $record->delete();
        }
        return true;
    }

    public function dataTable($draw){

        $recordsTotal       = WorkStep::underMyControl()->count();
        $recordsFiltered    = WorkStep::underMyControl()->search()->count();
        $records            = WorkStep::underMyControl()->select(['id','icon','order','is_active'])
        ->search()->dataTable()->get();
        
        return [
            'draw'              => $draw,
            'recordsTotal'      => $recordsTotal,
            'recordsFiltered'   => $recordsFiltered,
            'data'              => WorkStepsResource::collection($records)
        ];
    }

    public function order(array $list,$orderBy='order'){
        foreach ($list as  $value) {
            WorkStep::underMyControl()->find($value['id'])->update([$orderBy=>$value['order']]);
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
         WorkStep::class,
         $id,
         $content,
         request()->user()->id,
         $parent_id
       );
    }
    public function totalCount(){
        return WorkStep::underMyControl()->count();
    }
    public function trashCount(){
        return WorkStep::underMyControl()->onlyTrashed()->count();
    }
    public function restore(int $id){
        $record = WorkStep::onlyTrashed()->findOrFail($id);
        $record->restore();
        return $record;
    }
}

