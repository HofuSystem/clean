<?php

namespace Core\Coupons\Services;

use Core\Coupons\Models\Gift;
use Core\Coupons\DataResources\GiftsResource;


class GiftsService
{
    public function storeOrUpdate(array $data = [], $id = null)
    {
        $recordData = array_filter($data, fn($key) => in_array($key, [
            'status', 'from', 'to', 'coupon_code', 'order_type', 
            'register_from', 'register_to', 'orders_from', 'orders_to', 
            'orders_min', 'orders_max', 'translations',
            'type', 'value', 'max_value'
        ]), ARRAY_FILTER_USE_KEY);

        
        $record = Gift::updateOrCreate(['id' => $id], $recordData);
        
        return $record;
    }

    public function get(int $id)
    {
        return Gift::findOrFail($id);
    }

    public function delete(int $id, $final = false)
    {
        $record = Gift::findOrFail($id);
        if ($final) {
            $record->forceDelete();
        } else {
            $record->delete();
        }
        return true;
    }

    public function dataTable($draw)
    {
        $recordsTotal    = Gift::count();
        $recordsFiltered = Gift::search()->count();
        $records         = Gift::select([
            'id', 'status', 'from', 'to', 'coupon_code', 'order_type'
        ])->search()->dataTable()->get();

        return [
            'draw'            => $draw,
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data'            => GiftsResource::collection($records)

        ];
    }

    public function totalCount()
    {
        return Gift::count();
    }

    public function trashCount()
    {
        return Gift::onlyTrashed()->count();
    }

    public function restore(int $id)
    {
        $record = Gift::onlyTrashed()->findOrFail($id);
        $record->restore();
        return $record;
    }
}
