<?php

namespace Core\Blog\Services;

use Core\Comments\Services\CommentingService;
use Core\Blog\Models\BlogCategory;
use Core\Blog\DataResources\BlogCategoriesResource;

class BlogCategoriesService
{
    public function __construct(protected CommentingService $commentingService){}

    public function selectable(string $key,string $value){
        $selected = ['id'];
        if(!in_array($key,["title"])){
            $selected[] = $key;
        }
        if(!in_array($value,["title"])){
            $selected[] = $value;
        }
        return BlogCategory::select($selected)->get();
    }

    public function storeOrUpdate(array $data = [],$id = null){
        $recordData = array_filter($data,fn($key) => in_array($key, ['parent_id','status','translations']),ARRAY_FILTER_USE_KEY);
        $record     = BlogCategory::updateOrCreate(['id' => $id],$recordData);
        
        
        return $record;
    }

    public function get(int $id){
        return  BlogCategory::findOrFail($id);
    }

    public function delete(int $id,$final = false){
        $record             = BlogCategory::findOrFail($id);
        if($final){
            $record->forceDelete();
        }else{
            $record->delete();
        }
        return true;
    }

    public function dataTable($draw){

        $recordsTotal       = BlogCategory::count();
        $recordsFiltered    = BlogCategory::search()->count();
        $records            = BlogCategory::select(['id','parent_id','status'])
        ->with(['parent'])
        ->search()->dataTable()->get();
        
        return [
            'draw'              => $draw,
            'recordsTotal'      => $recordsTotal,
            'recordsFiltered'   => $recordsFiltered,
            'data'              => BlogCategoriesResource::collection($records)
        ];
    }

    public function order(array $list,$orderBy='order'){
        foreach ($list as  $value) {
            BlogCategory::find($value['id'])->update([$orderBy=>$value['order']]);
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
         BlogCategory::class,
         $id,
         $content,
         request()->user()->id,
         $parent_id
       );
    }
    public function totalCount(){
        return BlogCategory::count();
    }
    public function trashCount(){
        return BlogCategory::onlyTrashed()->count();
    }
    public function restore(int $id){
        $record = BlogCategory::onlyTrashed()->findOrFail($id);
        $record->restore();
        return $record;
    }
}
