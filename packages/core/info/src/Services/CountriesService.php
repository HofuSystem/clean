<?php

namespace Core\Info\Services;

use Core\Comments\Services\CommentingService;
use Core\Info\Models\Country;
use Core\Info\DataResources\CountriesResource;
use Core\Settings\Helpers\ToolHelper;

class CountriesService
{
    public function __construct(protected CommentingService $commentingService,protected CitiesService $citiesService){}

    public function selectable(string $key,string $value){
        $selected = ['id'];
        if(!in_array($key,["name"])){
            $selected[] = $key;
        }
        if(!in_array($value,["name"])){
            $selected[] = $value;
        }
        return Country::select($selected)->get();
    }

    public function storeOrUpdate(array $data = [],$id = null){
        $recordData = array_filter($data,fn($key) => in_array($key, ['phonecode','short_name','flag','translations']),ARRAY_FILTER_USE_KEY);
        $slug               = \Str::slug($data['translations']['en']['name']);
        $slug               = ToolHelper::generateUniqueSlug(Country::class,$slug,$id);
        $recordData['slug'] = $slug;
        $record     = Country::updateOrCreate(['id' => $id],$recordData);
        
        if(!isset($id)){
            //saving on create the related citiesItems
            $citiesItems            = $data['cities'] ?? [];
            foreach ($citiesItems as $index => $itemValues) {
                $itemValues['country_id'] = $record->id;
                $this->citiesService->storeOrUpdate($itemValues,$itemValues['id'] ?? null);
            }
        }
           
        return $record;
    }

    public function get(int $id){
        return  Country::findOrFail($id);
    }

    public function delete(int $id,$final = false){
        $record             = Country::findOrFail($id);
        if($final){
            $record->forceDelete();
        }else{
            $record->delete();
        }
        return true;
    }

    public function dataTable($draw){

        $recordsTotal       = Country::count();
        $recordsFiltered    = Country::search()->count();
        $records            = Country::select(['id','slug','phonecode','short_name','flag'])
        ->search()->dataTable()->get();
        
        return [
            'draw'              => $draw,
            'recordsTotal'      => $recordsTotal,
            'recordsFiltered'   => $recordsFiltered,
            'data'              => CountriesResource::collection($records)
        ];
    }

    public function order(array $list,$orderBy='order'){
        foreach ($list as  $value) {
            Country::find($value['id'])->update([$orderBy=>$value['order']]);
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
         Country::class,
         $id,
         $content,
         request()->user()->id,
         $parent_id
       );
    }
    public function totalCount(){
        return Country::count();
    }
    public function trashCount(){
        return Country::onlyTrashed()->count();
    }
    public function restore(int $id){
        $record = Country::onlyTrashed()->findOrFail($id);
        $record->restore();
        return $record;
    }
}
