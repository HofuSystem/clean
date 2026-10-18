<?php

namespace Core\Blog\Services;

use Core\Comments\Services\CommentingService;
use Core\Blog\Models\Blog;
use Core\Blog\Models\BlogSlugRedirect;
use Core\Blog\DataResources\BlogsResource;
use Illuminate\Support\Facades\DB;

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
        $record = DB::transaction(function () use ($id, $recordData) {
            $previousSlug = $id ? Blog::whereKey($id)->value('slug') : null;
            $record = Blog::updateOrCreate(['id' => $id], $recordData);
            $newSlug = $record->slug;

            if ($previousSlug && $previousSlug !== $newSlug) {
                BlogSlugRedirect::where('new_slug', $previousSlug)->update([
                    'new_slug' => $newSlug,
                    'updated_at' => now(),
                ]);
                BlogSlugRedirect::updateOrCreate(
                    ['old_slug' => $previousSlug],
                    ['new_slug' => $newSlug]
                );
            }

            // Reusing a historical slug makes the live article canonical again.
            BlogSlugRedirect::where('old_slug', $newSlug)->delete();

            return $record;
        });

        return $record;
    }

    public function get(int $id){
        return  Blog::findOrFail($id);
    }

    public function delete(int $id,$final = false){
        DB::transaction(function () use ($id, $final) {
            $record = Blog::findOrFail($id);
            if ($final) {
                BlogSlugRedirect::where('new_slug', $record->slug)->update([
                    'new_slug' => null,
                    'updated_at' => now(),
                ]);
                BlogSlugRedirect::updateOrCreate(
                    ['old_slug' => $record->slug],
                    ['new_slug' => null]
                );
                $record->forceDelete();
            } else {
                $record->delete();
            }
        });
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
