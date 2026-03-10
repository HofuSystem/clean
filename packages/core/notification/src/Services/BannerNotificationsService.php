<?php

namespace Core\Notification\Services;

use Core\Comments\Services\CommentingService;
use Core\Notification\Models\BannerNotification;
use Core\Notification\DataResources\BannerNotificationsResource;

class BannerNotificationsService
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
        return BannerNotification::select($selected)->get();
    }

    public function storeOrUpdate(array $data = [],$id = null){
        $recordData = array_filter($data,fn($key) => in_array($key, ['image','publish_date','expired_date','next_vision_hour','status','translations']),ARRAY_FILTER_USE_KEY);
        $record     = BannerNotification::updateOrCreate(['id' => $id],$recordData);
        
        
        return $record;
    }

    public function get(int $id){
        return  BannerNotification::findOrFail($id);
    }

    public function delete(int $id,$final = false){
        $record             = BannerNotification::findOrFail($id);
        if($final){
            $record->forceDelete();
        }else{
            $record->delete();
        }
        return true;
    }

    public function dataTable($draw){

        $recordsTotal       = BannerNotification::count();
        $recordsFiltered    = BannerNotification::search()->count();
        $records            = BannerNotification::select(['id','image','publish_date','expired_date','next_vision_hour','status'])
        ->search()->dataTable()->get();
        
        return [
            'draw'              => $draw,
            'recordsTotal'      => $recordsTotal,
            'recordsFiltered'   => $recordsFiltered,
            'data'              => BannerNotificationsResource::collection($records)
        ];
    }

    public function order(array $list,$orderBy='order'){
        foreach ($list as  $value) {
            BannerNotification::find($value['id'])->update([$orderBy=>$value['order']]);
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
         BannerNotification::class,
         $id,
         $content,
         request()->user()->id,
         $parent_id
       );
    }
    public function totalCount(){
        return BannerNotification::count();
    }
    public function trashCount(){
        return BannerNotification::onlyTrashed()->count();
    }
    public function restore(int $id){
        $record = BannerNotification::onlyTrashed()->findOrFail($id);
        $record->restore();
        return $record;
    }
}
