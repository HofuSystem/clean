<?php

namespace Core\Info\Services;

use Core\Comments\Services\CommentingService;
use Core\Info\Models\City;
use Core\Info\DataResources\CitiesResource;
use Core\Settings\Helpers\ToolHelper;

class CitiesService
{
    public function __construct(protected CommentingService $commentingService,protected DistrictsService $districtsService){}

    public function selectable(string $key,string $value){
        $selected = ['id'];
        if(!in_array($key,["name"])){
            $selected[] = $key;
        }
        if(!in_array($value,["name"])){
            $selected[] = $value;
        }
        return City::select($selected)->get();
    }

    public function storeOrUpdate(array $data = [],$id = null){
        $recordData = array_filter($data,fn($key) => in_array($key, ['lat','lng','postal_code','image','delivery_price','status','country_id','translations']),ARRAY_FILTER_USE_KEY);
        $slug               = \Str::slug($data['translations']['en']['name']);
        $slug               = ToolHelper::generateUniqueSlug(City::class,$slug,$id);
        $recordData['slug'] = $slug;
        $record     = City::updateOrCreate(['id' => $id],$recordData);
        
        if(!isset($id)){
            //saving on create the related districtsItems
            $districtsItems            = $data['districts'] ?? [];
            foreach ($districtsItems as $index => $itemValues) {
                $itemValues['city_id'] = $record->id;
                $this->districtsService->storeOrUpdate($itemValues,$itemValues['id'] ?? null);
            }
        }
           
        return $record;
    }

    public function get(int $id){
        return  City::findOrFail($id);
    }

    public function delete(int $id,$final = false){
        $record             = City::findOrFail($id);
        if($final){
            $record->forceDelete();
        }else{
            $record->delete();
        }
        return true;
    }

    public function dataTable($draw){

        $recordsTotal       = City::count();
        $recordsFiltered    = City::search()->count();
        $records            = City::select(['id','slug','postal_code','image','delivery_price','status','country_id'])
        ->with(['country'])
        ->search()->dataTable()->get();
        
        return [
            'draw'              => $draw,
            'recordsTotal'      => $recordsTotal,
            'recordsFiltered'   => $recordsFiltered,
            'data'              => CitiesResource::collection($records)
        ];
    }

    public function order(array $list,$orderBy='order'){
        foreach ($list as  $value) {
            City::find($value['id'])->update([$orderBy=>$value['order']]);
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
         City::class,
         $id,
         $content,
         request()->user()->id,
         $parent_id
       );
    }
    public function totalCount(){
        return City::count();
    }
    public function trashCount(){
        return City::onlyTrashed()->count();
    }
    public function restore(int $id){
        $record = City::onlyTrashed()->findOrFail($id);
        $record->restore();
        return $record;
    }
}
