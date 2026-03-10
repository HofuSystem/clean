<?php

namespace Core\CMS\Services;

use Core\Comments\Services\CommentingService;
use Core\CMS\Models\CmsPageDetail;
use Core\CMS\DataResources\CmsPageDetailsResource;

class CmsPageDetailsService
{
    public function __construct(protected CommentingService $commentingService){}

    public function selectable(string $key,string $value){
        $selected = ['id'];
        if(!in_array($key,["name","description","intro","point"])){
            $selected[] = $key;
        }
        if(!in_array($value,["name","description","intro","point"])){
            $selected[] = $value;
        }
        return CmsPageDetail::select($selected)->get();
    }

    public function storeOrUpdate(array $data = [],$id = null){
        $recordData = array_filter($data,fn($key) => in_array($key, ['image','tablet_image','mobile_image','icon','video','link','cms_pages_id','translations']),ARRAY_FILTER_USE_KEY);
        $record     = CmsPageDetail::updateOrCreate(['id' => $id],$recordData);
        
        
        return $record;
    }

    public function get(int $id){
        return  CmsPageDetail::findOrFail($id);
    }

    public function delete(int $id,$final = false){
        $record             = CmsPageDetail::findOrFail($id);
        if($final){
            $record->forceDelete();
        }else{
            $record->delete();
        }
        return true;
    }

    public function dataTable($draw){

        $recordsTotal       = CmsPageDetail::count();
        $recordsFiltered    = CmsPageDetail::search()->count();
        $records            = CmsPageDetail::select(['id','image','mobile_image','icon','video','link','cms_pages_id'])
        ->with(['cmsPages'])
        ->search()->dataTable()->get();
        
        return [
            'draw'              => $draw,
            'recordsTotal'      => $recordsTotal,
            'recordsFiltered'   => $recordsFiltered,
            'data'              => CmsPageDetailsResource::collection($records)
        ];
    }

    public function order(array $list,$orderBy='order'){
        foreach ($list as  $value) {
            CmsPageDetail::find($value['id'])->update([$orderBy=>$value['order']]);
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
         CmsPageDetail::class,
         $id,
         $content,
         request()->user()->id,
         $parent_id
       );
    }
    public function totalCount(){
        return CmsPageDetail::count();
    }
    public function trashCount(){
        return CmsPageDetail::onlyTrashed()->count();
    }
    public function restore(int $id){
        $record = CmsPageDetail::onlyTrashed()->findOrFail($id);
        $record->restore();
        return $record;
    }
}
