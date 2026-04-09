<?php

namespace Core\B2B\Services;

use Core\B2B\Models\B2BFinancial;
use Core\B2B\DataResources\B2BFinancialsResource;

class B2BFinancialsService
{
    public function getNextOwedRefrence()
    {
        $prefix = 'CN-'.date('Ymd');
        $lastRefrence = B2BFinancial::where('type', 'owed')
        ->where('reference_id', 'like', $prefix.'%')
        ->orderBy('reference_id', 'desc')->first();
        if ($lastRefrence) {
            $invoiceOrderNumber = (int) substr($lastRefrence->reference_id, -5);
            return $prefix.'-'. str_pad($invoiceOrderNumber + 1, 5, '0', STR_PAD_LEFT);
        }
        return $prefix.'-'. str_pad(1, 5, '0', STR_PAD_LEFT);
    }
    public function storeOrUpdate(array $data = [], $id = null)
    {
        $allowed = ['company_id', 'reference_id', 'amount', 'type', 'collection_date', 'note', 'attachment'];
        if (request()->hasFile('attachment')) {
            $data['attachment'] = request()->file('attachment')->store('financials', 'public');
        }
        $recordData = array_filter($data, fn($key) => in_array($key, $allowed), ARRAY_FILTER_USE_KEY);
        return B2BFinancial::updateOrCreate(['id' => $id], $recordData);
    }

    public function get(int $id)
    {
        return B2BFinancial::findOrFail($id);
    }

    public function delete(int $id, $final = false)
    {
        $record = B2BFinancial::findOrFail($id);
        $final ? $record->forceDelete() : $record->delete();
        return true;
    }

    public function dataTable($draw)
    {
        $recordsTotal    = B2BFinancial::underMyControl()->count();
        $recordsFiltered = B2BFinancial::underMyControl()->search()->count();
        $records         = B2BFinancial::underMyControl()
            ->with('company')
            ->search()->dataTable()->get();

        return [
            'draw'            => $draw,
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data'            => B2BFinancialsResource::collection($records),
        ];
    }
}
