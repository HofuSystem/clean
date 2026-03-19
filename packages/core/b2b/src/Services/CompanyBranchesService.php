<?php

namespace Core\B2B\Services;

use Core\B2B\Models\CompanyBranch;
use Core\B2B\DataResources\CompanyBranchesResource;

class CompanyBranchesService
{
    public function storeOrUpdate(array $data = [], $id = null)
    {
        $allowed = ['name', 'location', 'lat', 'lng', 'city_id', 'district_id', 'user_id', 'is_active','is_default', 'company_id'];
        $recordData = array_filter($data, fn($key) => in_array($key, $allowed), ARRAY_FILTER_USE_KEY);
        return CompanyBranch::updateOrCreate(['id' => $id], $recordData);
    }

    public function get(int $id)
    {
        return CompanyBranch::findOrFail($id);
    }

    public function delete(int $id, $final = false)
    {
        $record = CompanyBranch::findOrFail($id);
        $final ? $record->forceDelete() : $record->delete();
        return true;
    }

    public function dataTable($draw)
    {
        $recordsTotal    = CompanyBranch::underMyControl()->count();
        $recordsFiltered = CompanyBranch::underMyControl()->search()->count();
        $records         = CompanyBranch::underMyControl()
            ->select(['id', 'name', 'location', 'lat', 'lng', 'city_id', 'district_id', 'user_id', 'is_default', 'company_id'])
            ->with('company', 'city', 'district', 'user')
            ->search()->dataTable()->get();

        return [
            'draw'            => $draw,
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data'            => CompanyBranchesResource::collection($records),
        ];
    }

    public function totalCount()
    {
        return CompanyBranch::underMyControl()->count();
    }

    public function trashCount()
    {
        return CompanyBranch::underMyControl()->onlyTrashed()->count();
    }
}
