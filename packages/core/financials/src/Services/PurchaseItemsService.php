<?php

namespace Core\Financials\Services;

use Core\Financials\Models\PurchaseItem;
use Illuminate\Validation\ValidationException;

class PurchaseItemsService
{
    public function selectable(string $key, string $value)
    {
        $selected = ['id'];
        if (!in_array($key, [])) {
            $selected[] = $key;
        }
        if (!in_array($value, [])) {
            $selected[] = $value;
        }
        return PurchaseItem::select($selected)->get();
    }

    public function storeOrUpdate(array $data = [], $id = null)
    {
        $recordData = array_filter($data, fn($key) => in_array($key, ['name']), ARRAY_FILTER_USE_KEY);
        $record = PurchaseItem::updateOrCreate(['id' => $id], $recordData);
        return $record;
    }

    public function get($id)
    {
        return PurchaseItem::where('id', $id)->firstOrFail();
    }

    public function delete(int $id, $final = false)
    {
        $record = PurchaseItem::findOrFail($id);
        if ($final) {
            $record->forceDelete();
        } else {
            $record->delete();
        }
        return true;
    }

    public function restore(int $id)
    {
        $record = PurchaseItem::withTrashed()->findOrFail($id);
        $record->restore();
        return true;
    }

    public function dataTable($draw)
    {
        $recordsTotal = PurchaseItem::count();
        $recordsFiltered = PurchaseItem::search()->count();
        $records = PurchaseItem::search()->dataTable()->get();

        return [
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => \Core\Financials\DataResources\PurchaseItemsResource::collection($records)
        ];
    }

    public function totalCount()
    {
        return PurchaseItem::count();
    }

    public function trashCount()
    {
        return PurchaseItem::onlyTrashed()->count();
    }

    public function import(array $items)
    {
        foreach ($items as $index => $item) {
            $items[$index] = $this->storeOrUpdate($item, $item['id'] ?? null);
        }
        return $items;
    }
}
