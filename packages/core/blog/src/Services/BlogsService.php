<?php

namespace Core\Blog\Services;

use Core\Comments\Services\CommentingService;
use Core\Blog\Models\Blog;
use Core\Blog\DataResources\BlogsResource;

class BlogsService
{
    public function __construct(protected CommentingService $commentingService){}

    public function selectable(string $key,string $value){
        $selected = ['id'];
        if(!in_array($key,["title","content","meta"])){
            $selected[] = $key;
        }
        if(!in_array($value,["title","content","meta"])){
            $selected[] = $value;
        }
        return Blog::select($selected)->get();
    }

    public function storeOrUpdate(array $data = [],$id = null){
        $recordData = array_filter($data,fn($key) => in_array($key, ['slug','image','gallery','category_id','status','published_at','translations']),ARRAY_FILTER_USE_KEY);
        $record     = Blog::updateOrCreate(['id' => $id],$recordData);


        return $record;
    }

    public function get(int $id){
        return  Blog::findOrFail($id);
    }

    public function delete(int $id,$final = false){
        $record             = Blog::findOrFail($id);
        if($final){
            $record->forceDelete();
        }else{
            $record->delete();
        }
        return true;
    }

    public function dataTable($draw){

        $recordsTotal       = Blog::count();
        $recordsFiltered    = Blog::search()->count();
        $records            = Blog::select(['id','image','gallery','category_id','status','published_at'])
        ->with(['category'])
        ->search()->dataTable()->get();

        return [
            'draw'              => $draw,
            'recordsTotal'      => $recordsTotal,
            'recordsFiltered'   => $recordsFiltered,
            'data'              => BlogsResource::collection($records)
        ];
    }

    public function order(array $list,$orderBy='order'){
        foreach ($list as  $value) {
            Blog::find($value['id'])->update([$orderBy=>$value['order']]);
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
         Blog::class,
         $id,
         $content,
         request()->user()->id,
         $parent_id
       );
    }
    public function totalCount(){
        return Blog::count();
    }
    public function trashCount(){
        return Blog::onlyTrashed()->count();
    }
    public function restore(int $id){
        $record = Blog::onlyTrashed()->findOrFail($id);
        $record->restore();
        return $record;
    }
}
