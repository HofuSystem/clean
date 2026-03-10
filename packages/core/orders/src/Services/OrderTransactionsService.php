<?php

namespace Core\Orders\Services;

use Core\Comments\Services\CommentingService;
use Core\Orders\Models\OrderTransaction;
use Core\Orders\DataResources\OrderTransactionsResource;

class OrderTransactionsService
{
    public function __construct(protected CommentingService $commentingService){}

    public function selectable($cols = [],$with = [],$scopes = []){
        $query = OrderTransaction::underMyControl()->select($cols)->with($with);
        foreach($scopes as $scope){
            $query = $query->$scope();
        } 
        return $query->get();
    }

    public function storeOrUpdate(array $data = [],$id = null){
        $recordData = array_filter($data,fn($key) => in_array($key, ['order_id','type','online_payment_method','amount','transaction_id','point_id','wallet_transaction_id','notes','translations']),ARRAY_FILTER_USE_KEY);
        $record     = OrderTransaction::updateOrCreate(['id' => $id],$recordData);
        
        
        return $record;
    }

    public function get(int $id){
        return  OrderTransaction::findOrFail($id);
    }

    public function delete(int $id,$final = false){
        $record             = OrderTransaction::underMyControl()->where('id',$id)->first();
        if($final){
            $record->forceDelete();
        }else{
            $record->delete();
        }
        return true;
    }

    public function dataTable($draw){

        $recordsTotal       = OrderTransaction::underMyControl()->count();
        $recordsFiltered    = OrderTransaction::underMyControl()->search()->count();
        $records            = OrderTransaction::underMyControl()->select(['id','order_id','type','online_payment_method','amount','transaction_id','point_id','wallet_transaction_id'])
        ->with(['order','point','walletTransaction'])
        ->search()->dataTable()->get();
        
        return [
            'draw'              => $draw,
            'recordsTotal'      => $recordsTotal,
            'recordsFiltered'   => $recordsFiltered,
            'data'              => OrderTransactionsResource::collection($records)
        ];
    }

    public function order(array $list,$orderBy='order'){
        foreach ($list as  $value) {
            OrderTransaction::underMyControl()->find($value['id'])->update([$orderBy=>$value['order']]);
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
         OrderTransaction::class,
         $id,
         $content,
         request()->user()->id,
         $parent_id
       );
    }
    public function totalCount(){
        return OrderTransaction::underMyControl()->count();
    }
    public function trashCount(){
        return OrderTransaction::underMyControl()->onlyTrashed()->count();
    }
    public static function updateOrderPaid($order){
        $transactions    = OrderTransaction::where('order_id',$order->id)->get();
        $paid            = $transactions->sum('amount');
        $hasBeenRefunded = $transactions->where('amount','<',0)->sum('amount');
        $pointsPaid      = $transactions->where('type','points')->where('amount','>',0)->sum('amount');
        $walletPaid      = $transactions->where('type','wallet')->where('amount','>',0)->sum('amount');
        $cashPaid        = $transactions->where('type','cash')->where('amount','>',0)->sum('amount');
        $cardPaid        = $transactions->where('type','card')->where('amount','>',0)->sum('amount');
        $order->update([
            'paid'                  => $paid,
            'points_amount_used'    => $pointsPaid,
            'wallet_amount_used'    => $walletPaid,
            'cash_amount_used'      => $cashPaid,
            'card_amount_used'      => $cardPaid,
            'has_been_refunded'     => abs($hasBeenRefunded)
        ]);
    }
    public function restore(int $id){
        $record = OrderTransaction::onlyTrashed()->findOrFail($id);
        $record->restore();
        return $record;
    }
}
