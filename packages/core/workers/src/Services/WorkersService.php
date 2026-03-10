<?php

namespace Core\Workers\Services;

use Core\Comments\Services\CommentingService;
use Core\Workers\Models\Worker;
use Core\Workers\DataResources\WorkersResource;
use Core\Categories\Services\CategoriesService;
use Core\Users\Services\UsersService;

class WorkersService
{
    public function __construct(protected CommentingService $commentingService,protected CategoriesService $categoriesService,protected UsersService $usersService,protected WorkerDaysService $workerDaysService){}

    public function selectable(string $key,string $value){
        $selected = ['id'];
        if(!in_array($key,[])){
            $selected[] = $key;
        }
        if(!in_array($value,[])){
            $selected[] = $value;
        }
        return Worker::select($selected)->get();
    }

    public function storeOrUpdate(array $data = [],$id = null){
        $recordData = array_filter($data,fn($key) => in_array($key, ['image','name','phone','email','years_experience','address','birth_date','hour_price','gender','status','identity','nationality_id','leader_id','city_id','translations']),ARRAY_FILTER_USE_KEY);
        $record     = Worker::updateOrCreate(['id' => $id],$recordData);
        $record->categories()->sync($data['categories'] ?? []);
        
        if(!isset($id)){
            //saving on create the related workerDaysItems
            $workerDaysItems            = $data['workdays'] ?? [];
            foreach ($workerDaysItems as $index => $itemValues) {
                $itemValues['worker_id'] = $record->id;
                $this->workerDaysService->storeOrUpdate($itemValues,$itemValues['id'] ?? null);
            }
        }
           
        return $record;
    }

    public function get(int $id){
        return  Worker::findOrFail($id);
    }

    public function delete(int $id,$final = false){
        $record             = Worker::findOrFail($id);
        if($final){
            $record->forceDelete();
        }else{
            $record->delete();
        }
        return true;
    }

    public function dataTable($draw){

        $recordsTotal       = Worker::count();
        $recordsFiltered    = Worker::search()->count();
        $records            = Worker::select(['id','image','name','phone','email','years_experience','address','birth_date','hour_price','gender','status','identity','nationality_id','city_id','leader_id'])
        ->with(['nationality','city','categories','leader','workdays'])
        ->search()->dataTable()->get();
        
        return [
            'draw'              => $draw,
            'recordsTotal'      => $recordsTotal,
            'recordsFiltered'   => $recordsFiltered,
            'data'              => WorkersResource::collection($records)
        ];
    }

    public function order(array $list,$orderBy='order'){
        foreach ($list as  $value) {
            Worker::find($value['id'])->update([$orderBy=>$value['order']]);
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
         Worker::class,
         $id,
         $content,
         request()->user()->id,
         $parent_id
       );
    }
    public function totalCount(){
        return Worker::count();
    }
    public function trashCount(){
        return Worker::onlyTrashed()->count();
    }
    public function restore(int $id){
        $record = Worker::onlyTrashed()->findOrFail($id);
        $record->restore();
        return $record;
    }
}
