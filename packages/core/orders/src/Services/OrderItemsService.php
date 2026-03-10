<?php

namespace Core\Orders\Services;

use Core\Comments\Services\CommentingService;
use Core\Orders\Models\OrderItem;
use Core\Orders\DataResources\OrderItemsResource;

class OrderItemsService
{
    public function __construct(
        protected CommentingService $commentingService,
        protected OrderItemQtyUpdatesService $orderItemQtyUpdatesService,
        protected OrderHistoryService $orderHistoryService
    ){}

    public function selectable(string $key,string $value,$where =[]){
        $selected = ['id','quantity','width','height','order_id'];
        if(!in_array($key,[])){
            $selected[] = $key;
        }
        if(!in_array($value,[])){
            $selected[] = $value;
        }
        return OrderItem::select($selected)->where($where)->get();
    }

    public function storeOrUpdate(array $data = [],$id = null){
        $recordData = array_filter($data,fn($key) => in_array($key, ['order_id','product_id','product_data','product_price','quantity','width','height','add_by_admin','update_by_admin','is_picked','is_delivered','translations']),ARRAY_FILTER_USE_KEY);
        
        // Get old record if updating
        $oldRecord = $id ? OrderItem::find($id) : null;
        
        $record     = OrderItem::updateOrCreate(['id' => $id],$recordData);
        
        if(!isset($id)){
            //saving on create the related orderItemQtyUpdatesItems
            $orderItemQtyUpdatesItems            = $data['qtyUpdates'] ?? [];
            foreach ($orderItemQtyUpdatesItems as $index => $itemValues) {
                $itemValues['item_id'] = $record->id;
                $this->orderItemQtyUpdatesService->storeOrUpdate($itemValues,$itemValues['id'] ?? null);
            }
        }
        return $record;
    }

    public function get(int $id){
        return  OrderItem::findOrFail($id);
    }

    public function delete(int $id,$final = false){
        $record             = OrderItem::findOrFail($id);
        $productData        = json_decode($record->product_data, true);
        $productName        = $productData['name'] ?? $record->product?->name ?? 'Product #' . $record->product_id;
        
        if($final){
            $record->update([
                'final_delete' => true,
            ]);
            $record->delete();
        }else{
            $record->delete();
        }
        return true;
    }

    public function dataTable($draw){

        $recordsTotal       = OrderItem::count();
        $recordsFiltered    = OrderItem::search()->count();
        $records            = OrderItem::select(['id','order_id','product_id','product_price','quantity','height','width','add_by_admin','update_by_admin','is_picked','is_delivered'])
        ->with(['order','product'])
        ->search()->dataTable()->get();
        
        return [
            'draw'              => $draw,
            'recordsTotal'      => $recordsTotal,
            'recordsFiltered'   => $recordsFiltered,
            'data'              => OrderItemsResource::collection($records)
        ];
    }

    public function order(array $list,$orderBy='order'){
        foreach ($list as  $value) {
            OrderItem::find($value['id'])->update([$orderBy=>$value['order']]);
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
         OrderItem::class,
         $id,
         $content,
         request()->user()->id,
         $parent_id
       );
    }
    public function totalCount(){
        return OrderItem::count();
    }
    public function trashCount(){
        return OrderItem::onlyTrashed()->count();
    }
    public function restore(int $id){
        $record = OrderItem::onlyTrashed()->findOrFail($id);
        $record->restore();
        return $record;
    }
}
