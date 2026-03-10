<?php

namespace Core\PaymentGateways\Services;

use Core\Comments\Services\CommentingService;
use Core\PaymentGateways\Models\PaymentTransaction;
use Core\PaymentGateways\DataResources\PaymentTransactionsResource;

class PaymentTransactionsService
{
    public function __construct(protected CommentingService $commentingService){}

    public function selectable($cols = [],$with = [],$scopes = []){
        $query = PaymentTransaction::underMyControl()->select($cols)->with($with);
        foreach($scopes as $scope){
            $query = $query->$scope();
        } 
        return $query->get();
    }

    public function storeOrUpdate(array $data = [],$id = null){
        $recordData = array_filter($data,fn($key) => in_array($key, ['transaction_id','for','status','request_data','payment_method','payment_data','payment_response','translations']),ARRAY_FILTER_USE_KEY);
        $record     = PaymentTransaction::updateOrCreate(['id' => $id],$recordData);
        
        
        return $record;
    }

    public function get(int $id){
        return  PaymentTransaction::findOrFail($id);
    }

    public function delete(int $id,$final = false){
        $record             = PaymentTransaction::underMyControl()->where('id',$id)->first();
        if($final){
            $record->forceDelete();
        }else{
            $record->delete();
        }
        return true;
    }

    public function dataTable($draw){

        $recordsTotal       = PaymentTransaction::underMyControl()->count();
        $recordsFiltered    = PaymentTransaction::underMyControl()->search()->count();
        $records            = PaymentTransaction::underMyControl()->select(['id','transaction_id','for','status','request_data','payment_method','payment_data','payment_response'])
        ->search()->dataTable()->get();
        
        return [
            'draw'              => $draw,
            'recordsTotal'      => $recordsTotal,
            'recordsFiltered'   => $recordsFiltered,
            'data'              => PaymentTransactionsResource::collection($records)
        ];
    }

    public function order(array $list,$orderBy='order'){
        foreach ($list as  $value) {
            PaymentTransaction::underMyControl()->find($value['id'])->update([$orderBy=>$value['order']]);
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
         PaymentTransaction::class,
         $id,
         $content,
         request()->user()->id,
         $parent_id
       );
    }
    public function totalCount(){
        return PaymentTransaction::underMyControl()->count();
    }
    public function trashCount(){
        return PaymentTransaction::underMyControl()->onlyTrashed()->count();
    }
    public function restore(int $id){
        $record = PaymentTransaction::onlyTrashed()->findOrFail($id);
        $record->restore();
        return $record;
    }
}
