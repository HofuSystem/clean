<?php

namespace Core\Info\Services;

use Core\Comments\Services\CommentingService;
use Core\Info\Models\District;
use Core\Info\DataResources\DistrictsResource;
use Core\Settings\Helpers\ToolHelper;

class DistrictsService
{
    public function __construct(protected CommentingService $commentingService){}

    public function selectable(string $key,string $value){
        $selected = ['id','city_id'];
        if(!in_array($key,["name"])){
            $selected[] = $key;
        }
        if(!in_array($value,["name"])){
            $selected[] = $value;
        }
        return District::select($selected)->get();
    }

    public function storeOrUpdate(array $data = [],$id = null,$coordinates = []){
        $recordData = array_filter($data,fn($key) => in_array($key, ['slug','lat','lng','postal_code','city_id','translations']),ARRAY_FILTER_USE_KEY);
        $record     = District::updateOrCreate(['id' => $id],$recordData);
        if($id){
            $record->mapPoints()->delete();
        }
        foreach($coordinates ?? [] as $coordinate){
            $record->mapPoints()->create($coordinate);
        }
       
           
        return $record;
    }

    public function get(int $id){
        return  District::findOrFail($id);
    }

    public function delete(int $id,$final = false){
        $record             = District::findOrFail($id);
        if($final){
            $record->forceDelete();
        }else{
            $record->delete();
        }
        return true;
    }

    public function dataTable($draw){

        $recordsTotal       = District::count();
        $recordsFiltered    = District::search()->count();
        $records            = District::select(['id','slug','lat','lng','postal_code','city_id'])
        ->with(['city'])
        ->search()->dataTable()->get();
        
        return [
            'draw'              => $draw,
            'recordsTotal'      => $recordsTotal,
            'recordsFiltered'   => $recordsFiltered,
            'data'              => DistrictsResource::collection($records)
        ];
    }

    public function order(array $list,$orderBy='order'){
        foreach ($list as  $value) {
            District::find($value['id'])->update([$orderBy=>$value['order']]);
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
         District::class,
         $id,
         $content,
         request()->user()->id,
         $parent_id
       );
    }
    public function totalCount(){
        return District::count();
    }
    public function trashCount(){
        return District::onlyTrashed()->count();
    }
    public function restore(int $id){
        $record = District::onlyTrashed()->findOrFail($id);
        $record->restore();
        return $record;
    }
}
