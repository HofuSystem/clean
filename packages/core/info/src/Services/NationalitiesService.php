<?php

namespace Core\Info\Services;

use Core\Comments\Services\CommentingService;
use Core\Info\Models\Nationality;
use Core\Info\DataResources\NationalitiesResource;

class NationalitiesService
{
    public function __construct(protected CommentingService $commentingService){}

    public function selectable(string $key,string $value){
        $selected = ['id'];
        if(!in_array($key,["name"])){
            $selected[] = $key;
        }
        if(!in_array($value,["name"])){
            $selected[] = $value;
        }
        return Nationality::select($selected)->get();
    }

    public function storeOrUpdate(array $data = [],$id = null){
        $recordData = array_filter($data,fn($key) => in_array($key, ['arranging','translations']),ARRAY_FILTER_USE_KEY);
        $record     = Nationality::updateOrCreate(['id' => $id],$recordData);
        
        
        return $record;
    }

    public function get(int $id){
        return  Nationality::findOrFail($id);
    }

    public function delete(int $id,$final = false){
        $record             = Nationality::findOrFail($id);
        if($final){
            $record->forceDelete();
        }else{
            $record->delete();
        }
        return true;
    }

    public function dataTable($draw){

        $recordsTotal       = Nationality::count();
        $recordsFiltered    = Nationality::search()->count();
        $records            = Nationality::select(['id','arranging'])
        ->search()->dataTable()->get();
        
        return [
            'draw'              => $draw,
            'recordsTotal'      => $recordsTotal,
            'recordsFiltered'   => $recordsFiltered,
            'data'              => NationalitiesResource::collection($records)
        ];
    }

    public function order(array $list,$orderBy='order'){
        foreach ($list as  $value) {
            Nationality::find($value['id'])->update([$orderBy=>$value['order']]);
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
         Nationality::class,
         $id,
         $content,
         request()->user()->id,
         $parent_id
       );
    }
    public function totalCount(){
        return Nationality::count();
    }
    public function trashCount(){
        return Nationality::onlyTrashed()->count();
    }
    public function restore(int $id){
        $record = Nationality::onlyTrashed()->findOrFail($id);
        $record->restore();
        return $record;
    }
}
