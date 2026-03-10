<?php

namespace Core\Pages\Services;

use Core\Comments\Services\CommentingService;
use Core\Pages\Models\Business;
use Core\Pages\DataResources\BusinessesResource;

class BusinessesService
{
    public function __construct(protected CommentingService $commentingService){}

    public function selectable($cols = [],$with = [],$scopes = []){
        $query = Business::underMyControl()->select($cols)->with($with);
        foreach($scopes as $scope){
            $query = $query->$scope();
        }
        return $query->get();
    }

    public function storeOrUpdate(array $data = [],$id = null){
        $recordData = array_filter($data,fn($key) => in_array($key, ['icon','translations']),ARRAY_FILTER_USE_KEY);
        $record     = Business::updateOrCreate(['id' => $id],$recordData);


        return $record;
    }

    public function get(int $id){
        return  Business::findOrFail($id);
    }

    public function delete(int $id,$final = false){
        $record             = Business::underMyControl()->where('id',$id)->first();
        if($final){
            $record->forceDelete();
        }else{
            $record->delete();
        }
        return true;
    }

    public function dataTable($draw){

        $recordsTotal       = Business::underMyControl()->count();
        $recordsFiltered    = Business::underMyControl()->search()->count();
        $records            = Business::underMyControl()->select(['id'])
        ->search()->dataTable()->get();

        return [
            'draw'              => $draw,
            'recordsTotal'      => $recordsTotal,
            'recordsFiltered'   => $recordsFiltered,
            'data'              => BusinessesResource::collection($records)
        ];
    }

    public function order(array $list,$orderBy='order'){
        foreach ($list as  $value) {
            Business::underMyControl()->find($value['id'])->update([$orderBy=>$value['order']]);
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
         Business::class,
         $id,
         $content,
         request()->user()->id,
         $parent_id
       );
    }
    public function totalCount(){
        return Business::underMyControl()->count();
    }
    public function trashCount(){
        return Business::underMyControl()->onlyTrashed()->count();
    }
    public function restore(int $id){
        $record = Business::onlyTrashed()->findOrFail($id);
        $record->restore();
        return $record;
    }
}
