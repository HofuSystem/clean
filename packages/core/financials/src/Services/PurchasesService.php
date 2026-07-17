<?php

namespace Core\Financials\Services;

use Core\Financials\Models\Purchase;
use Illuminate\Validation\ValidationException;

class PurchasesService
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
        return Purchase::select($selected)->get();
    }

    public function storeOrUpdate(array $data = [], $id = null)
    {
        $allowed = ['item_id', 'provider_id', 'reference_id', 'value_before_tax', 'tax_value', 'value_after_tax', 'notes', 'attachment', 'collection_date', 'bank_transfer_files'];
        if (request()->hasFile('attachment')) {
            $data['attachment'] = request()->file('attachment')->store('purchases', 'public');
        }
        $recordData = array_filter($data, fn($key) => in_array($key, $allowed), ARRAY_FILTER_USE_KEY);
        $record = Purchase::updateOrCreate(['id' => $id], $recordData);
        return $record;
    }

    public function get($id)
    {
        return Purchase::where('id', $id)->firstOrFail();
    }

    public function delete(int $id, $final = false)
    {
        $record = Purchase::findOrFail($id);
        if ($final) {
            $record->forceDelete();
        } else {
            $record->delete();
        }
        return true;
    }

    public function restore(int $id)
    {
        $record = Purchase::withTrashed()->findOrFail($id);
        $record->restore();
        return true;
    }

    public function dataTable($draw)
    {
        $recordsTotal = Purchase::count();
        $recordsFiltered = Purchase::search()->count();
        $records = Purchase::search()->dataTable()->get();

        return [
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => \Core\Financials\DataResources\PurchasesResource::collection($records)
        ];
    }

    public function totalCount()
    {
        return Purchase::count();
    }

    public function trashCount()
    {
        return Purchase::onlyTrashed()->count();
    }

    public function import(array $items)
    {
        foreach ($items as $index => $item) {
            $items[$index] = $this->storeOrUpdate($item, $item['id'] ?? null);
        }
        return $items;
    }
}
