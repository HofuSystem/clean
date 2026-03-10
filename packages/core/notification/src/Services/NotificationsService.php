<?php

namespace Core\Notification\Services;

use Core\Comments\Services\CommentingService;
use Core\Notification\Models\Notification;
use Core\Notification\DataResources\NotificationsResource;

class NotificationsService
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
        return Notification::select($selected)->get();
    }

    public function storeOrUpdate(array $data = [],$id = null){
        $recordData = array_filter($data,fn($key) => in_array($key, ['types','for','for_data','title','body','media','sender_id','translations','register_from','register_to','orders_from','orders_to','orders_min','orders_max']),ARRAY_FILTER_USE_KEY);
        if(isset($recordData['for_data']) and !is_string($recordData['for_data'])){
            $recordData['for_data'] = json_encode($recordData['for_data']);
        }
        if(isset($recordData['types']) and !is_string($recordData['types'])){
            $recordData['types'] = json_encode($recordData['types']);
        }
        if(!isset($recordData['for_data'])){
            $recordData['for_data'] = json_encode([]);
        }
        $record     = Notification::updateOrCreate(['id' => $id],$recordData);

        return $record;
    }

    public function get(int $id){
        return  Notification::findOrFail($id);
    }

    public function delete(int $id,$final = false){
        $record             = Notification::findOrFail($id);
        if($final){
            $record->forceDelete();
        }else{
            $record->delete();
        }
        return true;
    }

    public function dataTable($draw){

        $recordsTotal       = Notification::count();
        $recordsFiltered    = Notification::search()->count();
        $records            = Notification::select(['id','types','for','title','body','media','sender_id','created_at'])
        ->with(['sender'])
        ->search()->dataTable()->get();
        
        return [
            'draw'              => $draw,
            'recordsTotal'      => $recordsTotal,
            'recordsFiltered'   => $recordsFiltered,
            'data'              => NotificationsResource::collection($records)
        ];
    }

    public function order(array $list,$orderBy='order'){
        foreach ($list as  $value) {
            Notification::find($value['id'])->update([$orderBy=>$value['order']]);
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
         Notification::class,
         $id,
         $content,
         request()->user()->id,
         $parent_id
       );
    }
    public function totalCount(){
        return Notification::count();
    }
    public function trashCount(){
        return Notification::onlyTrashed()->count();
    }
    public function restore(int $id){
        $record = Notification::onlyTrashed()->findOrFail($id);
        $record->restore();
        return $record;
    }
}
