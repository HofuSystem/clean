<?php

namespace Core\Financials\Services;

use Core\Financials\Models\Financial;
use Core\Financials\DataResources\FinancialsResource;

class FinancialsService
{
    public function getNextOwedRefrence()
    {
        $prefix = 'CN-'.date('Ymd');
        $lastRefrence = Financial::where('type', 'owed')
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
        $allowed = ['company_id', 'user_id', 'reference_id', 'amount', 'type', 'collection_date', 'note', 'attachment'];
        if (request()->hasFile('attachment')) {
            $data['attachment'] = request()->file('attachment')->store('financials', 'public');
        }
        $recordData = array_filter($data, fn($key) => in_array($key, $allowed), ARRAY_FILTER_USE_KEY);
        if (array_key_exists('company_id', $recordData) && empty($recordData['company_id'])) {
            $recordData['company_id'] = null;
        }
        if (array_key_exists('user_id', $recordData) && empty($recordData['user_id'])) {
            $recordData['user_id'] = null;
        }
        return Financial::updateOrCreate(['id' => $id], $recordData);
    }

    public function get(int $id)
    {
        return Financial::findOrFail($id);
    }

    public function delete(int $id, $final = false)
    {
        $record = Financial::findOrFail($id);
        $final ? $record->forceDelete() : $record->delete();
        return true;
    }

    public function dataTable($draw)
    {
        $recordsTotal    = Financial::underMyControl()->count();
        $recordsFiltered = Financial::underMyControl()->search()->count();
        $records         = Financial::underMyControl()
            ->with(['company', 'user'])
            ->search()->dataTable()->get();

        return [
            'draw'            => $draw,
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data'            => FinancialsResource::collection($records),
        ];
    }

    public function restore(int $id)
    {
        $record = Financial::withTrashed()->findOrFail($id);
        $record->restore();
        return true;
    }

    public function totalCount()
    {
        return Financial::underMyControl()->count();
    }

    public function trashCount()
    {
        return Financial::underMyControl()->onlyTrashed()->count();
    }
}
