<?php

namespace Core\Users\Services;

use Core\Comments\Services\CommentingService;
use Core\Users\Models\Point;
use Core\Users\DataResources\PointsResource;
use Core\Users\Models\User;

class PointsService
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
        return Point::select($selected)->get();
    }

    public function storeOrUpdate(array $data = [],$id = null){
        $recordData = array_filter($data,fn($key) => in_array($key, ['title','amount','operation','expire_at','user_id','translations']),ARRAY_FILTER_USE_KEY);
        $record     = Point::updateOrCreate(['id' => $id],$recordData);
        
        
        return $record;
    }

    public function get(int $id){
        return  Point::findOrFail($id);
    }

    public function delete(int $id,$final = false){
        $record             = Point::findOrFail($id);
        if($final){
            $record->forceDelete();
        }else{
            $record->delete();
        }
        return true;
    }

    public function dataTable($draw){

        $recordsTotal       = Point::count();
        $recordsFiltered    = Point::search()->count();
        $records            = Point::select(['id','title','amount','operation','expire_at'])
        ->search()->dataTable()->get();
        
        return [
            'draw'              => $draw,
            'recordsTotal'      => $recordsTotal,
            'recordsFiltered'   => $recordsFiltered,
            'data'              => PointsResource::collection($records)
        ];
    }

    public function order(array $list,$orderBy='order'){
        foreach ($list as  $value) {
            Point::find($value['id'])->update([$orderBy=>$value['order']]);
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
         Point::class,
         $id,
         $content,
         request()->user()->id,
         $parent_id
       );
    }
    public function totalCount(){
        return Point::count();
    }
    public function trashCount(){
        return Point::onlyTrashed()->count();
    }
    public function restore(int $id){
        $record = Point::onlyTrashed()->findOrFail($id);
        $record->restore();
        return $record;
    }
    public static function updateUserPoints($userId){
        $depositPoints = Point::where('user_id', $userId)->where('operation', 'deposit')->sum('amount');
        $withdrawPoints = Point::where('user_id', $userId)->where('operation', 'withdraw')->sum('amount');
        $user = User::find($userId);
        $currentPoints = $depositPoints - $withdrawPoints; 
        if ($user) {
            $user->update(['points_balance' =>$currentPoints]);
        }
        return $currentPoints;
    }
}
