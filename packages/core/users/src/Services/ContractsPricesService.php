<?php

namespace Core\Users\Services;

use Core\Comments\Services\CommentingService;
use Core\Users\Models\ContractsPrice;
use Core\Users\DataResources\ContractsPricesResource;

class ContractsPricesService
{
    public function __construct(protected CommentingService $commentingService){}

    public function selectable($cols = [],$with = [],$scopes = []){
        $query = ContractsPrice::underMyControl()->select($cols)->with($with);
        foreach($scopes as $scope){
            $query = $query->$scope();
        } 
        return $query->get();
    }

    public function storeOrUpdate(array $data = [],$id = null){
        $recordData = array_filter($data,fn($key) => in_array($key, ['contract_id','product_id','price','translations']),ARRAY_FILTER_USE_KEY);
        $record     = ContractsPrice::updateOrCreate(['id' => $id],$recordData);
        
        
        return $record;
    }

    public function get(int $id){
        return  ContractsPrice::findOrFail($id);
    }

    public function delete(int $id,$final = false){
        $record             = ContractsPrice::underMyControl()->where('id',$id)->first();
        if($final){
            $record->forceDelete();
        }else{
            $record->delete();
        }
        return true;
    }

    public function dataTable($draw){

        $recordsTotal       = ContractsPrice::underMyControl()->count();
        $recordsFiltered    = ContractsPrice::underMyControl()->search()->count();
        $records            = ContractsPrice::underMyControl()->select(['id','contract_id','product_id','price'])
        ->with(['contract','product'])
        ->search()->dataTable()->get();
        
        return [
            'draw'              => $draw,
            'recordsTotal'      => $recordsTotal,
            'recordsFiltered'   => $recordsFiltered,
            'data'              => ContractsPricesResource::collection($records)
        ];
    }

    public function order(array $list,$orderBy='order'){
        foreach ($list as  $value) {
            ContractsPrice::underMyControl()->find($value['id'])->update([$orderBy=>$value['order']]);
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
         ContractsPrice::class,
         $id,
         $content,
         request()->user()->id,
         $parent_id
       );
    }
    public function totalCount(){
        return ContractsPrice::underMyControl()->count();
    }
    public function trashCount(){
        return ContractsPrice::underMyControl()->onlyTrashed()->count();
    }
    public function restore(int $id){
        $record = ContractsPrice::onlyTrashed()->findOrFail($id);
        $record->restore();
        return $record;
    }
}
