<?php

namespace Core\Categories\Services;

use Core\Comments\Services\CommentingService;
use Core\Categories\Models\CategoryType;
use Core\Categories\DataResources\CategoryTypesResource;
use Core\Categories\Models\CategoryOffer;
use Core\Settings\Helpers\ToolHelper;

class CategoryTypesService
{
    public function __construct(protected CommentingService $commentingService){}

    public function selectable(string $key,string $value){
        $selected = ['id'];
        if(!in_array($key,["name","intro","desc"])){
            $selected[] = $key;
        }
        if(!in_array($value,["name","intro","desc"])){
            $selected[] = $value;
        }
        return CategoryType::select($selected)->get();
    }

    public function storeOrUpdate(array $data = [],$id = null){
        $recordData         = array_filter($data,fn($key) => in_array($key, ['category_id','hour_price','status','translations']),ARRAY_FILTER_USE_KEY);
        $slug               = \Str::slug($data['translations']['en']['name']);
        $slug               = ToolHelper::generateUniqueSlug(CategoryType::class,$slug,$id);
        $recordData['slug'] = $slug;
        $record             = CategoryType::updateOrCreate(['id' => $id],$recordData);
        
        
        return $record;
    }

    public function get(int $id){
        return  CategoryType::findOrFail($id);
    }

    public function delete(int $id,$final = false){
        $record             = CategoryType::findOrFail($id);
        if($final){
            $record->forceDelete();
        }else{
            $record->delete();
        }
        return true;
    }

    public function dataTable($draw){

        $recordsTotal       = CategoryType::count();
        $recordsFiltered    = CategoryType::search()->count();
        $records            = CategoryType::select(['id','slug','category_id','hour_price','status'])
        ->with(['category'])
        ->search()->dataTable()->get();
        
        return [
            'draw'              => $draw,
            'recordsTotal'      => $recordsTotal,
            'recordsFiltered'   => $recordsFiltered,
            'data'              => CategoryTypesResource::collection($records)
        ];
    }

    public function order(array $list,$orderBy='order'){
        foreach ($list as  $value) {
            CategoryType::find($value['id'])->update([$orderBy=>$value['order']]);
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
         CategoryType::class,
         $id,
         $content,
         request()->user()->id,
         $parent_id
       );
    }
    public function totalCount(){
        return CategoryType::count();
    }
    public function trashCount(){
        return CategoryType::onlyTrashed()->count();
    }
    public function restore(int $id){
        $record = CategoryType::onlyTrashed()->findOrFail($id);
        $record->restore();
        return $record;
    }
}
