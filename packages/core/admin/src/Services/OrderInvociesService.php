<?php

namespace Core\Admin\Services;

use Core\Admin\Models\OrderInvocy;
use Core\Admin\DataResources\OrderInvociesResource;

class OrderInvociesService
{
    public function __construct(){}

    public function selectable(string $key,string $value){
        $selected = ['id'];
        if(!in_array($key,[])){
            $selected[] = $key;
        }
        if(!in_array($value,[])){
            $selected[] = $value;
        }
        return OrderInvocy::select($selected)->get();
    }

    public function storeOrUpdate(array $data = [],$id = null){
        $recordData = array_filter($data,fn($key) => in_array($key, ['invoice_num','data','order_id','user_id','translations']),ARRAY_FILTER_USE_KEY);
        $record     = OrderInvocy::updateOrCreate(['id' => $id],$recordData);
        
        
        return $record;
    }

    public function get(int $id){
        return  OrderInvocy::findOrFail($id);
    }

    public function delete(int $id,$final = false){
        $record             = OrderInvocy::findOrFail($id);
        if($final){
            $record->forceDelete();
        }else{
            $record->delete();
        }
        return true;
    }

    public function dataTable($draw){

        $recordsTotal       = OrderInvocy::count();
        $recordsFiltered    = OrderInvocy::search()->count();
        $records            = OrderInvocy::select(['id','invoice_num','data','order_id','user_id'])
        ->with(['order','user'])
        ->search()->dataTable()->get();
        
        return [
            'draw'              => $draw,
            'recordsTotal'      => $recordsTotal,
            'recordsFiltered'   => $recordsFiltered,
            'data'              => OrderInvociesResource::collection($records)
        ];
    }

    public function order(array $list,$orderBy='order'){
        foreach ($list as  $value) {
            OrderInvocy::find($value['id'])->update([$orderBy=>$value['order']]);
        }
    }
    public function import(array $items){
        foreach ($items as  $index => $item) {
            $items[$index] = $this->storeOrUpdate($item,$item['id'] ?? null);
        }
        return $items;
    }
}
