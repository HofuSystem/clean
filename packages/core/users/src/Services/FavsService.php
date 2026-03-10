<?php

namespace Core\Users\Services;

use Core\Comments\Services\CommentingService;
use Core\Users\Models\Fav;
use Core\Users\DataResources\FavsResource;

class FavsService
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
        return Fav::select($selected)->get();
    }

    public function storeOrUpdate(array $data = [],$id = null){
        $recordData = array_filter($data,fn($key) => in_array($key, ['favs_type','favs_id','user_id','translations']),ARRAY_FILTER_USE_KEY);
        $record     = Fav::updateOrCreate(['id' => $id],$recordData);
        return $record;
    }

    public function get(int $id){
        return  Fav::findOrFail($id);
    }

    public function delete(int $id,$final = false){
        $record             = Fav::findOrFail($id);
        if($final){
            $record->forceDelete();
        }else{
            $record->delete();
        }
        return true;
    }

    public function dataTable($draw){

        $recordsTotal       = Fav::count();
        $recordsFiltered    = Fav::search()->count();
        $records            = Fav::select(['id','favs_type','favs_id','user_id'])
        ->with(['user'])
        ->search()->dataTable()->get();

        return [
            'draw'              => $draw,
            'recordsTotal'      => $recordsTotal,
            'recordsFiltered'   => $recordsFiltered,
            'data'              => FavsResource::collection($records)
        ];
    }

    public function order(array $list,$orderBy='order'){
        foreach ($list as  $value) {
            Fav::find($value['id'])->update([$orderBy=>$value['order']]);
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
         Fav::class,
         $id,
         $content,
         request()->user()->id,
         $parent_id
       );
    }
    public function totalCount(){
        return Fav::count();
    }
    public function trashCount(){
        return Fav::onlyTrashed()->count();
    }
    public function restore(int $id){
        $record = Fav::onlyTrashed()->findOrFail($id);
        $record->restore();
        return $record;
    }
    
}
