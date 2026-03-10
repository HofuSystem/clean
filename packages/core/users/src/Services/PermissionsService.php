<?php

namespace Core\Users\Services;

use Core\Comments\Services\CommentingService;
use Core\Users\Models\Permission;
use Core\Users\DataResources\PermissionsResource;

class PermissionsService
{
    public function __construct(protected CommentingService $commentingService){}

    public function selectable(string $key,string $value){
        $selected = ['id','name'];
        if(!in_array($key,["title"])){
            $selected[] = $key;
        }
        if(!in_array($value,["title"])){
            $selected[] = $value;
        }
        return Permission::with(['translations'])->select($selected)->get();
    }

    public function storeOrUpdate(array $data = [],$id = null){
        $recordData = array_filter($data,fn($key) => in_array($key, ['name','translations']),ARRAY_FILTER_USE_KEY);
        $record     = Permission::updateOrCreate(['id' => $id],$recordData);
        
        
        return $record;
    }

    public function get(int $id){
        return  Permission::findOrFail($id);
    }

    public function delete(int $id,$final = false){
        $record             = Permission::findOrFail($id);
        if($final){
            $record->forceDelete();
        }else{
            $record->delete();
        }
        return true;
    }

    public function dataTable($draw){

        $recordsTotal       = Permission::count();
        $recordsFiltered    = Permission::search()->count();
        $records            = Permission::select(['id','name'])
        ->search()->dataTable()->get();
        
        return [
            'draw'              => $draw,
            'recordsTotal'      => $recordsTotal,
            'recordsFiltered'   => $recordsFiltered,
            'data'              => PermissionsResource::collection($records)
        ];
    }

    public function order(array $list,$orderBy='order'){
        foreach ($list as  $value) {
            Permission::find($value['id'])->update([$orderBy=>$value['order']]);
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
         Permission::class,
         $id,
         $content,
         request()->user()->id,
         $parent_id
       );
    }
    public function totalCount(){
        return Permission::count();
    }
    public function trashCount(){
        return Permission::onlyTrashed()->count();
    }
    public function restore(int $id){
        $record = Permission::onlyTrashed()->findOrFail($id);
        $record->restore();
        return $record;
    }
}
