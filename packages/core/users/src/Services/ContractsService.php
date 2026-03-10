<?php

namespace Core\Users\Services;

use Core\Comments\Services\CommentingService;
use Core\Users\Models\Contract;
use Core\Users\DataResources\ContractsResource;

class ContractsService
{
    public function __construct(protected CommentingService $commentingService, protected ContractsPricesService $contractsPricesService, protected ContractsCustomerPricesService $contractsCustomerPricesService)
    {
    }

    public function selectable($cols = [], $with = [], $scopes = [])
    {
        $query = Contract::underMyControl()->select($cols)->with($with);
        foreach ($scopes as $scope) {
            $query = $query->$scope();
        }
        return $query->get();
    }

    public function storeOrUpdate(array $data = [], $id = null)
    {
        $recordData = array_filter($data, fn($key) => in_array($key, ['title', 'months_count', 'month_fees', 'max_allowed_over_price', 'unlimited_days', 'number_of_days', 'contract', 'start_date', 'end_date', 'client_id', 'translations', 'commercial_registration', 'tax_number']), ARRAY_FILTER_USE_KEY);
        $record = Contract::updateOrCreate(['id' => $id], $recordData);

        if (!isset($id)) {
            //saving on create the related contractsPricesItems
            $contractsPricesItems = $data['contractPrices'] ?? [];
            foreach ($contractsPricesItems as $index => $itemValues) {
                $itemValues['contract_id'] = $record->id;
                $this->contractsPricesService->storeOrUpdate($itemValues, $itemValues['id'] ?? null);
            }

            //saving on create the related contractCustomerPricesItems
            $contractsCustomerPricesItems = $data['contractCustomerPrices'] ?? [];
            foreach ($contractsCustomerPricesItems as $index => $itemValues) {
                $itemValues['contract_id'] = $record->id;
                $this->contractsCustomerPricesService->storeOrUpdate($itemValues, $itemValues['id'] ?? null);
            }
        }

        return $record;
    }

    public function get(int $id)
    {
        return Contract::findOrFail($id);
    }

    public function delete(int $id, $final = false)
    {
        $record = Contract::underMyControl()->where('id', $id)->first();
        if ($final) {
            $record->forceDelete();
        } else {
            $record->delete();
        }
        return true;
    }

    public function dataTable($draw)
    {

        $recordsTotal = Contract::underMyControl()->count();
        $recordsFiltered = Contract::underMyControl()->search()->count();
        $records = Contract::underMyControl()->select(['id', 'title', 'months_count', 'month_fees', 'unlimited_days', 'number_of_days', 'contract', 'start_date', 'end_date'])
            ->search()->dataTable()->get();

        return [
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => ContractsResource::collection($records)
        ];
    }

    public function order(array $list, $orderBy = 'order')
    {
        foreach ($list as $value) {
            Contract::underMyControl()->find($value['id'])->update([$orderBy => $value['order']]);
        }
    }
    public function import(array $items)
    {
        foreach ($items as $index => $item) {
            $items[$index] = $this->storeOrUpdate($item, $item['id'] ?? null);
        }
        return $items;
    }
    public function comment(int $id, string $content, int|null $parent_id)
    {
        return $this->commentingService->comment(
            Contract::class,
            $id,
            $content,
            request()->user()->id,
            $parent_id
        );
    }
    public function totalCount()
    {
        return Contract::underMyControl()->count();
    }
    public function trashCount()
    {
        return Contract::underMyControl()->onlyTrashed()->count();
    }
    public function restore(int $id)
    {
        $record = Contract::onlyTrashed()->findOrFail($id);
        $record->restore();
        return $record;
    }
}
