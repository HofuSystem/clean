<?php

namespace Core\Pages\Services;

use Core\Comments\Services\CommentingService;
use Core\Pages\Models\Reason;
use Core\Pages\DataResources\ReasonsResource;

class ReasonsService
{
    public function __construct(protected CommentingService $commentingService){}

    public function selectable($cols = [],$with = [],$scopes = []){
        $query = Reason::underMyControl()->select($cols)->with($with);
        foreach($scopes as $scope){
            $query = $query->$scope();
        }
        return $query->get();
    }

    public function storeOrUpdate(array $data = [],$id = null){
        $recordData = array_filter($data,fn($key) => in_array($key, ['translations']),ARRAY_FILTER_USE_KEY);
        $record     = Reason::updateOrCreate(['id' => $id],$recordData);


        return $record;
    }

    public function get(int $id){
        return  Reason::findOrFail($id);
    }

    public function delete(int $id,$final = false){
        $record             = Reason::underMyControl()->where('id',$id)->first();
        if($final){
            $record->forceDelete();
        }else{
            $record->delete();
        }
        return true;
    }

    public function dataTable($draw){

        $recordsTotal       = Reason::underMyControl()->count();
        $recordsFiltered    = Reason::underMyControl()->search()->count();
        $records            = Reason::underMyControl()->select(['id'])
        ->search()->dataTable()->get();

        return [
            'draw'              => $draw,
            'recordsTotal'      => $recordsTotal,
            'recordsFiltered'   => $recordsFiltered,
            'data'              => ReasonsResource::collection($records)
        ];
    }

    public function order(array $list,$orderBy='order'){
        foreach ($list as  $value) {
            Reason::underMyControl()->find($value['id'])->update([$orderBy=>$value['order']]);
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
         Reason::class,
         $id,
         $content,
         request()->user()->id,
         $parent_id
       );
    }
    public function totalCount(){
        return Reason::underMyControl()->count();
    }
    public function trashCount(){
        return Reason::underMyControl()->onlyTrashed()->count();
    }
    public function restore(int $id){
        $record = Reason::onlyTrashed()->findOrFail($id);
        $record->restore();
        return $record;
    }
}
