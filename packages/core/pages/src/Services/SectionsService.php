<?php

namespace Core\Pages\Services;

use Core\Comments\Services\CommentingService;
use Core\Pages\Models\Section;
use Core\Pages\DataResources\SectionsResource;

class SectionsService
{
    public function __construct(protected CommentingService $commentingService){}

    public function selectable($cols = [],$with = [],$scopes = []){
        $query = Section::underMyControl()->select($cols)->with($with);
        foreach($scopes as $scope){
            $query = $query->$scope();
        } 
        return $query->get();
    }

    public function storeOrUpdate(array $data = [],$id = null){
        $recordData = array_filter($data,fn($key) => in_array($key, ['images','video','template','page_id','order','translations']),ARRAY_FILTER_USE_KEY);
        $record     = Section::updateOrCreate(['id' => $id],$recordData);
        
        
        return $record;
    }

    public function get(int $id){
        return  Section::findOrFail($id);
    }

    public function delete(int $id,$final = false){
        $record             = Section::underMyControl()->where('id',$id)->first();
        if($final){
            $record->forceDelete();
        }else{
            $record->delete();
        }
        return true;
    }

    public function dataTable($draw){

        $recordsTotal       = Section::underMyControl()->count();
        $recordsFiltered    = Section::underMyControl()->search()->count();
        $records            = Section::underMyControl()->select(['id','images','video','template','page_id','order'])
        ->with(['page'])
        ->search()->dataTable()->get();
        
        return [
            'draw'              => $draw,
            'recordsTotal'      => $recordsTotal,
            'recordsFiltered'   => $recordsFiltered,
            'data'              => SectionsResource::collection($records)
        ];
    }

    public function order(array $list,$orderBy='order'){
        foreach ($list as  $value) {
            Section::underMyControl()->find($value['id'])->update([$orderBy=>$value['order']]);
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
         Section::class,
         $id,
         $content,
         request()->user()->id,
         $parent_id
       );
    }
    public function totalCount(){
        return Section::underMyControl()->count();
    }
    public function trashCount(){
        return Section::underMyControl()->onlyTrashed()->count();
    }
    public function restore(int $id){
        $record = Section::onlyTrashed()->findOrFail($id);
        $record->restore();
        return $record;
    }
}
