<?php

namespace Core\Users\Services;

use Core\Comments\Services\CommentingService;
use Core\Users\Models\UserEditRequest;
use Core\Users\DataResources\UserEditRequestsResource;

class UserEditRequestsService
{
    public function __construct(protected CommentingService $commentingService){}

    public function selectable(string $key,string $value){
        $selected = ['id'];
        if(!in_array($key,[])){
            $selected[] = $key;
        }
        if(!in_array($value,[])){
            $selected[] = $value;
        }
        return UserEditRequest::select($selected)->get();
    }

    public function storeOrUpdate(array $data = [],$id = null){
        $recordData = array_filter($data,fn($key) => in_array($key, ['fullname','email','phone','user_id','translations']),ARRAY_FILTER_USE_KEY);
        $record     = UserEditRequest::updateOrCreate(['id' => $id],$recordData);
        
        
        return $record;
    }

    public function get(int $id){
        return  UserEditRequest::findOrFail($id);
    }

    public function delete(int $id,$final = false){
        $record             = UserEditRequest::findOrFail($id);
        if($final){
            $record->forceDelete();
        }else{
            $record->delete();
        }
        return true;
    }

    public function dataTable($draw){

        $recordsTotal       = UserEditRequest::count();
        $recordsFiltered    = UserEditRequest::search()->count();
        $records            = UserEditRequest::select(['id','fullname','email','phone','user_id'])
        ->with(['user'])
        ->search()->dataTable()->get();
        
        return [
            'draw'              => $draw,
            'recordsTotal'      => $recordsTotal,
            'recordsFiltered'   => $recordsFiltered,
            'data'              => UserEditRequestsResource::collection($records)
        ];
    }

    public function order(array $list,$orderBy='order'){
        foreach ($list as  $value) {
            UserEditRequest::find($value['id'])->update([$orderBy=>$value['order']]);
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
         UserEditRequest::class,
         $id,
         $content,
         request()->user()->id,
         $parent_id
       );
    }
    public function totalCount(){
        return UserEditRequest::count();
    }
    public function trashCount(){
        return UserEditRequest::onlyTrashed()->count();
    }   
    public function restore(int $id){
        $record = UserEditRequest::onlyTrashed()->findOrFail($id);
        $record->restore();
        return $record;
    }
}
