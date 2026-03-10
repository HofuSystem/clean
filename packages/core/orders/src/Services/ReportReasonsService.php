<?php

namespace Core\Orders\Services;

use Core\Comments\Services\CommentingService;
use Core\Orders\Models\ReportReason;
use Core\Orders\DataResources\ReportReasonsResource;

class ReportReasonsService
{
    public function __construct(protected CommentingService $commentingService){}

    public function selectable(string $key,string $value){
        $selected = ['id'];
        if(!in_array($key,["name","desc"])){
            $selected[] = $key;
        }
        if(!in_array($value,["name","desc"])){
            $selected[] = $value;
        }
        return ReportReason::select($selected)->get();
    }

    public function storeOrUpdate(array $data = [],$id = null){
        $recordData = array_filter($data,fn($key) => in_array($key, ['ordering','translations']),ARRAY_FILTER_USE_KEY);
        $record     = ReportReason::updateOrCreate(['id' => $id],$recordData);
        
        
        return $record;
    }

    public function get(int $id){
        return  ReportReason::findOrFail($id);
    }

    public function delete(int $id,$final = false){
        $record             = ReportReason::findOrFail($id);
        if($final){
            $record->forceDelete();
        }else{
            $record->delete();
        }
        return true;
    }

    public function dataTable($draw){

        $recordsTotal       = ReportReason::count();
        $recordsFiltered    = ReportReason::search()->count();
        $records            = ReportReason::select(['id','ordering'])
        ->search()->dataTable()->get();
        
        return [
            'draw'              => $draw,
            'recordsTotal'      => $recordsTotal,
            'recordsFiltered'   => $recordsFiltered,
            'data'              => ReportReasonsResource::collection($records)
        ];
    }

    public function order(array $list,$orderBy='order'){
        foreach ($list as  $value) {
            ReportReason::find($value['id'])->update([$orderBy=>$value['order']]);
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
         ReportReason::class,
         $id,
         $content,
         request()->user()->id,
         $parent_id
       );
    }
    public function totalCount(){
        return ReportReason::count();
    }
    public function trashCount(){
        return ReportReason::onlyTrashed()->count();
    }
    public function restore(int $id){
        $record = ReportReason::onlyTrashed()->findOrFail($id);
        $record->restore();
        return $record;
    }
}
