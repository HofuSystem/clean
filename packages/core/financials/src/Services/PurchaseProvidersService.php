<?php

namespace Core\Financials\Services;

use Core\Financials\Models\PurchaseProvider;
use Illuminate\Validation\ValidationException;

class PurchaseProvidersService
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
        return PurchaseProvider::select($selected)->get();
    }

    public function storeOrUpdate(array $data = [], $id = null)
    {
        $recordData = array_filter($data, fn($key) => in_array($key, ['name', 'commercial_registration', 'tax_number', 'street_name', 'building_no', 'city_id', 'district_id', 'postal_code']), ARRAY_FILTER_USE_KEY);
        $record = PurchaseProvider::updateOrCreate(['id' => $id], $recordData);
        return $record;
    }

    public function get($id)
    {
        return PurchaseProvider::where('id', $id)->firstOrFail();
    }

    public function delete(int $id, $final = false)
    {
        $record = PurchaseProvider::findOrFail($id);
        if ($final) {
            $record->forceDelete();
        } else {
            $record->delete();
        }
        return true;
    }

    public function restore(int $id)
    {
        $record = PurchaseProvider::withTrashed()->findOrFail($id);
        $record->restore();
        return true;
    }

    public function dataTable($draw)
    {
        $recordsTotal = PurchaseProvider::count();
        $recordsFiltered = PurchaseProvider::search()->count();
        $records = PurchaseProvider::search()->dataTable()->get();

        return [
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => \Core\Financials\DataResources\PurchaseProvidersResource::collection($records)
        ];
    }

    public function totalCount()
    {
        return PurchaseProvider::count();
    }

    public function trashCount()
    {
        return PurchaseProvider::onlyTrashed()->count();
    }

    public function import(array $items)
    {
        foreach ($items as $index => $item) {
            $items[$index] = $this->storeOrUpdate($item, $item['id'] ?? null);
        }
        return $items;
    }
}
