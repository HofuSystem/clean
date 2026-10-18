<?php

namespace Core\Pages\Services;

use Core\Comments\Services\CommentingService;
use Core\Pages\Models\Page;
use Core\Pages\DataResources\PagesResource;

class PagesService
{
    public function __construct(protected CommentingService $commentingService,protected SectionsService $sectionsService){}

    public function selectable($cols = [],$with = [],$scopes = []){
        $query = Page::underMyControl()->select($cols)->with($with);
        foreach($scopes as $scope){
            $query = $query->$scope();
        } 
        return $query->get();
    }

    public function storeOrUpdate(array $data = [],$id = null){
        $recordData = array_filter($data,fn($key) => in_array($key, ['slug','image','is_active','translations']),ARRAY_FILTER_USE_KEY);
        $record     = Page::updateOrCreate(['id' => $id],$recordData);
        
        if(array_key_exists('sections', $data)){
            $sectionsItems = $data['sections'] ?? [];
            $savedIds = [];

            foreach ($sectionsItems as $index => $itemValues) {
                $sectionId = $itemValues['id'] ?? null;
                // Only update section ids that belong to this page. New ids are
                // created through the page relation so edits cannot move or edit
                // another page's section by submitting a crafted id.
                if ($sectionId) {
                    $existing = $record->sections()->whereKey($sectionId)->first();
                    if (!$existing) {
                        throw \Illuminate\Validation\ValidationException::withMessages([
                            'sections' => trans('Invalid section for this page'),
                        ]);
                    }
                }

                $itemValues['page_id'] = $record->id;
                $itemValues['order'] = $index;
                $section = $this->sectionsService->storeOrUpdate($itemValues, $sectionId);
                $savedIds[] = $section->id;
            }

            $sectionsToDelete = $record->sections();
            if ($savedIds) {
                $sectionsToDelete->whereNotIn('id', $savedIds);
            }
            $sectionsToDelete->delete();
        }
           
        return $record;
    }

    public function get(int $id){
        return  Page::findOrFail($id);
    }

    public function delete(int $id,$final = false){
        $record             = Page::underMyControl()->where('id',$id)->first();
        if($final){
            $record->forceDelete();
        }else{
            $record->delete();
        }
        return true;
    }

    public function dataTable($draw){

        $recordsTotal       = Page::underMyControl()->count();
        $recordsFiltered    = Page::underMyControl()->search()->count();
        $records            = Page::underMyControl()->with('translations')->select(['id','image','is_active'])
        ->search()->dataTable()->get();
        
        return [
            'draw'              => $draw,
            'recordsTotal'      => $recordsTotal,
            'recordsFiltered'   => $recordsFiltered,
            'data'              => PagesResource::collection($records)
        ];
    }

    public function order(array $list,$orderBy='order'){
        foreach ($list as  $value) {
            Page::underMyControl()->find($value['id'])->update([$orderBy=>$value['order']]);
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
         Page::class,
         $id,
         $content,
         request()->user()->id,
         $parent_id
       );
    }
    public function totalCount(){
        return Page::underMyControl()->count();
    }
    public function trashCount(){
        return Page::underMyControl()->onlyTrashed()->count();
    }
    public function restore(int $id){
        $record = Page::onlyTrashed()->findOrFail($id);
        $record->restore();
        return $record;
    }
}
