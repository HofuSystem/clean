<?php

namespace Core\Pages\Services;

use Core\Comments\Services\CommentingService;
use Core\Pages\Models\Comparison;
use Core\Pages\DataResources\ComparisonsResource;

class ComparisonsService
{
    public function __construct(protected CommentingService $commentingService){}

    public function selectable($cols = [],$with = [],$scopes = []){
        $query = Comparison::underMyControl()->select($cols)->with($with);
        foreach($scopes as $scope){
            $query = $query->$scope();
        } 
        return $query->get();
    }

    public function storeOrUpdate(array $data = [],$id = null){
        $recordData = array_filter($data,fn($key) => in_array($key, ['order','is_active','translations']),ARRAY_FILTER_USE_KEY);
        $record     = Comparison::updateOrCreate(['id' => $id],$recordData);
        
        
        return $record;
    }

    public function get(int $id){
        return  Comparison::findOrFail($id);
    }

    public function delete(int $id,$final = false){
        $record             = Comparison::underMyControl()->where('id',$id)->first();
        if($final){
            $record->forceDelete();
        }else{
            $record->delete();
        }
        return true;
    }

    public function dataTable($draw){

        $recordsTotal       = Comparison::underMyControl()->count();
        $recordsFiltered    = Comparison::underMyControl()->search()->count();
        $records            = Comparison::underMyControl()->select(['id','order','is_active'])
        ->search()->dataTable()->get();
        
        return [
            'draw'              => $draw,
            'recordsTotal'      => $recordsTotal,
            'recordsFiltered'   => $recordsFiltered,
            'data'              => ComparisonsResource::collection($records)
        ];
    }

    public function order(array $list,$orderBy='order'){
        foreach ($list as  $value) {
            Comparison::underMyControl()->find($value['id'])->update([$orderBy=>$value['order']]);
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
         Comparison::class,
         $id,
         $content,
         request()->user()->id,
         $parent_id
       );
    }
    public function totalCount(){
        return Comparison::underMyControl()->count();
    }
    public function trashCount(){
        return Comparison::underMyControl()->onlyTrashed()->count();
    }
    public function restore(int $id){
        $record = Comparison::onlyTrashed()->findOrFail($id);
        $record->restore();
        return $record;
    }
}

