<?php

namespace Core\Pages\Services;

use Core\Comments\Services\CommentingService;
use Core\Pages\Models\Faq;
use Core\Pages\DataResources\FaqsResource;

class FaqsService
{
    public function __construct(protected CommentingService $commentingService){}

    public function selectable($cols = [],$with = [],$scopes = []){
        $query = Faq::underMyControl()->select($cols)->with($with);
        foreach($scopes as $scope){
            $query = $query->$scope();
        } 
        return $query->get();
    }

    public function storeOrUpdate(array $data = [],$id = null){
        $recordData = array_filter($data,fn($key) => in_array($key, ['translations']),ARRAY_FILTER_USE_KEY);
        $record     = Faq::updateOrCreate(['id' => $id],$recordData);
        
        
        return $record;
    }

    public function get(int $id){
        return  Faq::findOrFail($id);
    }

    public function delete(int $id,$final = false){
        $record             = Faq::underMyControl()->where('id',$id)->first();
        if($final){
            $record->forceDelete();
        }else{
            $record->delete();
        }
        return true;
    }

    public function dataTable($draw){

        $recordsTotal       = Faq::underMyControl()->count();
        $recordsFiltered    = Faq::underMyControl()->search()->count();
        $records            = Faq::underMyControl()->select(['id'])
        ->search()->dataTable()->get();
        
        return [
            'draw'              => $draw,
            'recordsTotal'      => $recordsTotal,
            'recordsFiltered'   => $recordsFiltered,
            'data'              => FaqsResource::collection($records)
        ];
    }

    public function order(array $list,$orderBy='order'){
        foreach ($list as  $value) {
            Faq::underMyControl()->find($value['id'])->update([$orderBy=>$value['order']]);
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
         Faq::class,
         $id,
         $content,
         request()->user()->id,
         $parent_id
       );
    }
    public function totalCount(){
        return Faq::underMyControl()->count();
    }
    public function trashCount(){
        return Faq::underMyControl()->onlyTrashed()->count();
    }
    public function restore(int $id){
        $record = Faq::onlyTrashed()->findOrFail($id);
        $record->restore();
        return $record;
    }
}
