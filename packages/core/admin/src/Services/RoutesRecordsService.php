<?php

namespace Core\Admin\Services;

use Core\Comments\Services\CommentingService;
use Core\Admin\Models\RoutesRecord;
use Core\Admin\DataResources\RoutesRecordsResource;

class RoutesRecordsService
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
        return RoutesRecord::select($selected)->get();
    }

    public function storeOrUpdate(array $data = [],$id = null){
        $recordData = array_filter($data,fn($key) => in_array($key, ['version','end_point','attributes','user_id','ip_address','translations','headers','method']),ARRAY_FILTER_USE_KEY);
        $record     = RoutesRecord::updateOrCreate(['id' => $id],$recordData);
        
        
        return $record;
    }

    public function get(int $id){
        return  RoutesRecord::findOrFail($id);
    }

    public function delete(int $id,$final = false){
        $record             = RoutesRecord::findOrFail($id);
        if($final){
            $record->forceDelete();
        }else{
            $record->delete();
        }
        return true;
    }

    public function dataTable($draw){

        $recordsTotal       = RoutesRecord::count();
        $recordsFiltered    = RoutesRecord::search()->count();
        $records            = RoutesRecord::select(['id','version','end_point','attributes','user_id','ip_address','created_at','headers','method'])
        ->with(['user','ipAddress'])
        ->search()->dataTable()->get();
        
        return [
            'draw'              => $draw,
            'recordsTotal'      => $recordsTotal,
            'recordsFiltered'   => $recordsFiltered,
            'data'              => RoutesRecordsResource::collection($records)
        ];
    }

    public function order(array $list,$orderBy='order'){
        foreach ($list as  $value) {
            RoutesRecord::find($value['id'])->update([$orderBy=>$value['order']]);
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
         RoutesRecord::class,
         $id,
         $content,
         request()->user()->id,
         $parent_id
       );
    }
    public function totalCount(){
        return RoutesRecord::count();
    }
    public function trashCount(){
        return RoutesRecord::onlyTrashed()->count();
    }
    public function restore(int $id){
        $record = RoutesRecord::onlyTrashed()->findOrFail($id);
        $record->restore();
        return $record;
    }
}
