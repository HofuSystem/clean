<?php

namespace Core\Info\Services;

use Core\Comments\Services\CommentingService;
use Core\Info\Models\MapPoint;
use Core\Info\DataResources\MapPointsResource;

class MapPointsService
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
        return MapPoint::select($selected)->get();
    }

    public function storeOrUpdate(array $data = [],$id = null){
        $recordData = array_filter($data,fn($key) => in_array($key, ['lat','lng','district_id','translations']),ARRAY_FILTER_USE_KEY);
        $record     = MapPoint::updateOrCreate(['id' => $id],$recordData);
        
        
        return $record;
    }

    public function get(int $id){
        return  MapPoint::findOrFail($id);
    }

    public function delete(int $id,$final = false){
        $record             = MapPoint::findOrFail($id);
        if($final){
            $record->forceDelete();
        }else{
            $record->delete();
        }
        return true;
    }

    public function dataTable($draw){

        $recordsTotal       = MapPoint::count();
        $recordsFiltered    = MapPoint::search()->count();
        $records            = MapPoint::select(['id','lat','lng','district_id'])
        ->with(['district'])
        ->search()->dataTable()->get();
        
        return [
            'draw'              => $draw,
            'recordsTotal'      => $recordsTotal,
            'recordsFiltered'   => $recordsFiltered,
            'data'              => MapPointsResource::collection($records)
        ];
    }

    public function order(array $list,$orderBy='order'){
        foreach ($list as  $value) {
            MapPoint::find($value['id'])->update([$orderBy=>$value['order']]);
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
         MapPoint::class,
         $id,
         $content,
         request()->user()->id,
         $parent_id
       );
    }
    public function totalCount(){
        return MapPoint::count();
    }
    public function trashCount(){
        return MapPoint::onlyTrashed()->count();
    }
    public function restore(int $id){
        $record = MapPoint::onlyTrashed()->findOrFail($id);
        $record->restore();
        return $record;
    }
}
