<?php

namespace Core\Users\Services;

use Core\Comments\Services\CommentingService;
use Core\Users\Models\Address;
use Core\Users\DataResources\AddressesResource;

class AddressesService
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
        return Address::select($selected)->get();
    }

    public function storeOrUpdate(array $data = [],$id = null){
        $recordData = array_filter($data,fn($key) => in_array($key, ['location','name','status','lat','lng','city_id','district_id','is_default','user_id','translations']),ARRAY_FILTER_USE_KEY);
        $record     = Address::updateOrCreate(['id' => $id],$recordData);
        return $record;
    }

    public function get(int $id){
        return  Address::findOrFail($id);
    }

    public function delete(int $id,$final = false){
        $record             = Address::findOrFail($id);
        if($final){
            $record->forceDelete();
        }else{
            $record->delete();
        }
        return true;
    }

    public function dataTable($draw){

        $recordsTotal       = Address::count();
        $recordsFiltered    = Address::search()->count();
        $records            = Address::select(['id','name','location','lat','lng','city_id','district_id','is_default','status'])
        ->with(['city','district'])
        ->search()->dataTable()->get();

        return [
            'draw'              => $draw,
            'recordsTotal'      => $recordsTotal,
            'recordsFiltered'   => $recordsFiltered,
            'data'              => AddressesResource::collection($records)
        ];
    }

    public function order(array $list,$orderBy='order'){
        foreach ($list as  $value) {
            Address::find($value['id'])->update([$orderBy=>$value['order']]);
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
         Address::class,
         $id,
         $content,
         request()->user()->id,
         $parent_id
       );
    }
    public function totalCount(){
        return Address::count();
    }
    public function trashCount(){
        return Address::onlyTrashed()->count();
    }
    public function restore(int $id){
        $record = Address::onlyTrashed()->findOrFail($id);
        $record->restore();
        return $record;
    }
}
