<?php

namespace Core\Categories\Services;

use Core\Comments\Services\CommentingService;
use Core\Categories\Models\CategoryOffer;
use Core\Categories\DataResources\CategoryOffersResource;

class CategoryOffersService
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
        return CategoryOffer::select($selected)->get();
    }

    public function storeOrUpdate(array $data = [],$id = null){
        $recordData = array_filter($data,fn($key) => in_array($key, ['price','sale_price','image','hours_num','workers_num','status','type','translations']),ARRAY_FILTER_USE_KEY);
        $record     = CategoryOffer::updateOrCreate(['id' => $id],$recordData);
        
        
        return $record;
    }

    public function get(int $id){
        return  CategoryOffer::findOrFail($id);
    }

    public function delete(int $id,$final = false){
        $record             = CategoryOffer::findOrFail($id);
        if($final){
            $record->forceDelete();
        }else{
            $record->delete();
        }
        return true;
    }

    public function dataTable($draw){

        $recordsTotal       = CategoryOffer::count();
        $recordsFiltered    = CategoryOffer::search()->count();
        $records            = CategoryOffer::select(['id','price','sale_price','image','hours_num','workers_num','status','type'])
        ->search()->dataTable()->get();
        
        return [
            'draw'              => $draw,
            'recordsTotal'      => $recordsTotal,
            'recordsFiltered'   => $recordsFiltered,
            'data'              => CategoryOffersResource::collection($records)
        ];
    }

    public function order(array $list,$orderBy='order'){
        foreach ($list as  $value) {
            CategoryOffer::find($value['id'])->update([$orderBy=>$value['order']]);
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
         CategoryOffer::class,
         $id,
         $content,
         request()->user()->id,
         $parent_id
       );
    }
    public function totalCount(){
        return CategoryOffer::count();
    }
    public function trashCount(){
        return CategoryOffer::onlyTrashed()->count();
    }
    public function restore(int $id){
        $record = CategoryOffer::onlyTrashed()->findOrFail($id);
        $record->restore();
        return $record;
    }
}
