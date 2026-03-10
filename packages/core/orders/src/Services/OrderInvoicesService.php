<?php

namespace Core\Orders\Services;

use Core\Comments\Services\CommentingService;
use Core\Orders\Models\OrderInvoice;
use Core\Orders\DataResources\OrderInvoicesResource;

class OrderInvoicesService
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
        return OrderInvoice::select($selected)->get();
    }

    public function storeOrUpdate(array $data = [],$id = null){
        $recordData = array_filter($data,fn($key) => in_array($key, ['invoice_num','data','order_id','user_id','translations']),ARRAY_FILTER_USE_KEY);
        $record     = OrderInvoice::updateOrCreate(['id' => $id],$recordData);
        
        
        return $record;
    }

    public function get(int $id){
        return  OrderInvoice::findOrFail($id);
    }

    public function delete(int $id,$final = false){
        $record             = OrderInvoice::findOrFail($id);
        if($final){
            $record->forceDelete();
        }else{
            $record->delete();
        }
        return true;
    }

    public function dataTable($draw){

        $recordsTotal       = OrderInvoice::count();
        $recordsFiltered    = OrderInvoice::search()->count();
        $records            = OrderInvoice::select(['id','invoice_num','order_id','user_id'])
        ->with(['order','user'])
        ->search()->dataTable()->get();
        
        return [
            'draw'              => $draw,
            'recordsTotal'      => $recordsTotal,
            'recordsFiltered'   => $recordsFiltered,
            'data'              => OrderInvoicesResource::collection($records)
        ];
    }

    public function order(array $list,$orderBy='order'){
        foreach ($list as  $value) {
            OrderInvoice::find($value['id'])->update([$orderBy=>$value['order']]);
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
         OrderInvoice::class,
         $id,
         $content,
         request()->user()->id,
         $parent_id
       );
    }
    public function totalCount(){
        return OrderInvoice::count();
    }
    public function trashCount(){
        return OrderInvoice::onlyTrashed()->count();
    }
    public function restore(int $id){
        $record = OrderInvoice::onlyTrashed()->findOrFail($id);
        $record->restore();
        return $record;
    }
}
