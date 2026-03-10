<?php

namespace Core\Pages\Services;

use Core\Comments\Services\CommentingService;
use Core\Pages\Models\ContactRequest;
use Core\Pages\DataResources\ContactRequestsResource;

class ContactRequestsService
{
    public function __construct(protected CommentingService $commentingService){}

    public function selectable($cols = [],$with = [],$scopes = []){
        $query = ContactRequest::underMyControl()->select($cols)->with($with);
        foreach($scopes as $scope){
            $query = $query->$scope();
        }
        return $query->get();
    }

    public function storeOrUpdate(array $data = [],$id = null){
        $recordData = array_filter($data,fn($key) => in_array($key, ['name','phone','email','type','notes','translations']),ARRAY_FILTER_USE_KEY);
        $record     = ContactRequest::updateOrCreate(['id' => $id],$recordData);


        return $record;
    }

    public function get(int $id){
        return  ContactRequest::findOrFail($id);
    }

    public function delete(int $id,$final = false){
        $record             = ContactRequest::underMyControl()->where('id',$id)->first();
        if($final){
            $record->forceDelete();
        }else{
            $record->delete();
        }
        return true;
    }

    public function dataTable($draw){

        $recordsTotal       = ContactRequest::underMyControl()->count();
        $recordsFiltered    = ContactRequest::underMyControl()->search()->count();
        $records            = ContactRequest::underMyControl()->select(['id','name','phone','email','type','notes'])
        ->search()->dataTable()->get();

        return [
            'draw'              => $draw,
            'recordsTotal'      => $recordsTotal,
            'recordsFiltered'   => $recordsFiltered,
            'data'              => ContactRequestsResource::collection($records)
        ];
    }

    public function order(array $list,$orderBy='order'){
        foreach ($list as  $value) {
            ContactRequest::underMyControl()->find($value['id'])->update([$orderBy=>$value['order']]);
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
         ContactRequest::class,
         $id,
         $content,
         request()->user()->id,
         $parent_id
       );
    }
    public function totalCount(){
        return ContactRequest::underMyControl()->count();
    }
    public function trashCount(){
        return ContactRequest::underMyControl()->onlyTrashed()->count();
    }
    public function restore(int $id){
        $record = ContactRequest::onlyTrashed()->findOrFail($id);
        $record->restore();
        return $record;
    }
}
