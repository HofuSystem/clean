<?php

namespace Core\Users\Services;

use Core\Comments\Services\CommentingService;
use Core\Users\Models\Profile;
use Core\Users\DataResources\ProfilesResource;

class ProfilesService
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
        return Profile::select($selected)->get();
    }

    public function storeOrUpdate(array $data = [],$id = null){
        $recordData = array_filter($data,fn($key) => in_array($key, ['country_id','city_id','district_id','other_city_name','user_id','bio','lat','lng','translations']),ARRAY_FILTER_USE_KEY);
        $record     = Profile::updateOrCreate(['id' => $id],$recordData);
        
        
        return $record;
    }

    public function get(int $id){
        return  Profile::findOrFail($id);
    }

    public function delete(int $id,$final = false){
        $record             = Profile::findOrFail($id);
        if($final){
            $record->forceDelete();
        }else{
            $record->delete();
        }
        return true;
    }

    public function dataTable($draw){

        $recordsTotal       = Profile::count();
        $recordsFiltered    = Profile::search()->count();
        $records            = Profile::select(['id','country_id','city_id','district_id','other_city_name','user_id','bio','lat','lng'])
        ->with(['country','city','district','user'])
        ->search()->dataTable()->get();
        
        return [
            'draw'              => $draw,
            'recordsTotal'      => $recordsTotal,
            'recordsFiltered'   => $recordsFiltered,
            'data'              => ProfilesResource::collection($records)
        ];
    }

    public function order(array $list,$orderBy='order'){
        foreach ($list as  $value) {
            Profile::find($value['id'])->update([$orderBy=>$value['order']]);
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
         Profile::class,
         $id,
         $content,
         request()->user()->id,
         $parent_id
       );
    }
    public function totalCount(){
        return Profile::count();
    }
    public function trashCount(){
        return Profile::onlyTrashed()->count();
    }
    public function restore(int $id){
        $record = Profile::onlyTrashed()->findOrFail($id);
        $record->restore();
        return $record;
    }
}
