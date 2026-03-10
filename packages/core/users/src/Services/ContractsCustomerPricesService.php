<?php

namespace Core\Users\Services;

use Core\Comments\Services\CommentingService;
use Core\Users\Models\ContractsCustomerPrice;
use Core\Users\DataResources\ContractsCustomerPricesResource;

class ContractsCustomerPricesService
{
    public function __construct(protected CommentingService $commentingService){}

    public function selectable($cols = [],$with = [],$scopes = []){
        $query = ContractsCustomerPrice::underMyControl()->select($cols)->with($with);
        foreach($scopes as $scope){
            $query = $query->$scope();
        } 
        return $query->get();
    }

    public function storeOrUpdate(array $data = [],$id = null){
        $recordData = array_filter($data,fn($key) => in_array($key, ['contract_id','product_id','over_price','translations']),ARRAY_FILTER_USE_KEY);
        $record     = ContractsCustomerPrice::updateOrCreate(['id' => $id],$recordData);
        
        
        return $record;
    }

    public function get(int $id){
        return  ContractsCustomerPrice::findOrFail($id);
    }

    public function delete(int $id,$final = false){
        $record             = ContractsCustomerPrice::underMyControl()->where('id',$id)->first();
        if($final){
            $record->forceDelete();
        }else{
            $record->delete();
        }
        return true;
    }

    public function dataTable($draw){

        $recordsTotal       = ContractsCustomerPrice::underMyControl()->count();
        $recordsFiltered    = ContractsCustomerPrice::underMyControl()->search()->count();
        $records            = ContractsCustomerPrice::underMyControl()->select(['id','contract_id','product_id','over_price'])
        ->with(['contract','product'])
        ->search()->dataTable()->get();
        
        return [
            'draw'              => $draw,
            'recordsTotal'      => $recordsTotal,
            'recordsFiltered'   => $recordsFiltered,
            'data'              => ContractsCustomerPricesResource::collection($records)
        ];
    }

    public function order(array $list,$orderBy='order'){
        foreach ($list as  $value) {
            ContractsCustomerPrice::underMyControl()->find($value['id'])->update([$orderBy=>$value['order']]);
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
         ContractsCustomerPrice::class,
         $id,
         $content,
         request()->user()->id,
         $parent_id
       );
    }
    public function totalCount(){
        return ContractsCustomerPrice::underMyControl()->count();
    }
    public function trashCount(){
        return ContractsCustomerPrice::underMyControl()->onlyTrashed()->count();
    }
    public function restore(int $id){
        $record = ContractsCustomerPrice::onlyTrashed()->findOrFail($id);
        $record->restore();
        return $record;
    }
}

