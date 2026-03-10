<?php

namespace Core\CMS\Services;

use Core\Comments\Services\CommentingService;
use Core\CMS\Models\CmsPage;
use Core\CMS\DataResources\CmsPagesResource;

class CmsPagesService
{
    public function __construct(protected CommentingService $commentingService,protected CmsPageDetailsService $cmsPageDetailsService){}

    public function selectable(string $key,string $value){
        $selected = ['id'];
        if(!in_array($key,["name"])){
            $selected[] = $key;
        }
        if(!in_array($value,["name"])){
            $selected[] = $value;
        }
        return CmsPage::select($selected)->get();
    }

    public function storeOrUpdate(array $data = [],$id = null){
        $recordData = array_filter($data,fn($key) => in_array($key, ['slug','is_parent','is_multi_upload','have_point','have_name','have_description','have_intro','have_image','have_tablet_image','have_mobile_image','have_icon','have_video','have_link','translations']),ARRAY_FILTER_USE_KEY);
        $record     = CmsPage::updateOrCreate(['id' => $id],$recordData);

        if(!isset($id)){
            //saving on create the related cmsPageDetailsItems
            $cmsPageDetailsItems            = $data['details'] ?? [];
            foreach ($cmsPageDetailsItems as $index => $itemValues) {
                $itemValues['cms_pages_id'] = $record->id;
                $this->cmsPageDetailsService->storeOrUpdate($itemValues,$itemValues['id'] ?? null);
            }
        }

        return $record;
    }

    public function get(int $id){
        return  CmsPage::findOrFail($id);
    }
    public function getBySlug($slug){
        return  CmsPage::where('slug',$slug)->firstOrFail();
    }

    public function delete(int $id,$final = false){
        $record             = CmsPage::findOrFail($id);
        if($final){
            $record->forceDelete();
        }else{
            $record->delete();
        }
        return true;
    }

    public function dataTable($draw){

        $recordsTotal       = CmsPage::count();
        $recordsFiltered    = CmsPage::search()->count();
        $records            = CmsPage::select(['id','slug','is_parent'])
        ->search()->dataTable()->get();

        return [
            'draw'              => $draw,
            'recordsTotal'      => $recordsTotal,
            'recordsFiltered'   => $recordsFiltered,
            'data'              => CmsPagesResource::collection($records)
        ];
    }

    public function order(array $list,$orderBy='order'){
        foreach ($list as  $value) {
            CmsPage::find($value['id'])->update([$orderBy=>$value['order']]);
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
         CmsPage::class,
         $id,
         $content,
         request()->user()->id,
         $parent_id
       );
    }
    public function totalCount(){
        return CmsPage::count();
    }
    public function trashCount(){
        return CmsPage::onlyTrashed()->count();
    }
    public function restore(int $id){
        $record = CmsPage::onlyTrashed()->findOrFail($id);
        $record->restore();
        return $record;
    }
}
