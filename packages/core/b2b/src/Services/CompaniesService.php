<?php

namespace Core\B2B\Services;

use Core\B2B\Models\Company;
use Core\B2B\DataResources\CompaniesResource;

class CompaniesService
{
    public function selectable($cols = [], $with = [], $scopes = [])
    {
        $query = Company::underMyControl()->select($cols)->with($with);
        foreach ($scopes as $scope) {
            $query = $query->$scope();
        }
        return $query->get();
    }

    public function storeOrUpdate(array $data = [], $id = null)
    {
        $allowed = ['fullname', 'line_of_business', 'email', 'phone', 'image', 'owner_id'];
        $recordData = array_filter($data, fn($key) => in_array($key, $allowed), ARRAY_FILTER_USE_KEY);
        return Company::updateOrCreate(['id' => $id], $recordData);
    }

    public function get(int $id)
    {
        return Company::with('branches', 'owner')->findOrFail($id);
    }

    public function delete(int $id, $final = false)
    {
        $record = Company::underMyControl()->where('id', $id)->first();
        $final ? $record->forceDelete() : $record->delete();
        return true;
    }

    public function dataTable($draw)
    {
        $recordsTotal    = Company::underMyControl()->count();
        $recordsFiltered = Company::underMyControl()->search()->count();
        $records         = Company::underMyControl()
            ->select(['id', 'fullname', 'email', 'phone', 'image', 'owner_id'])
            ->with('owner')
            ->search()->dataTable()->get();

        return [
            'draw'            => $draw,
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data'            => CompaniesResource::collection($records),
        ];
    }

    public function totalCount()
    {
        return Company::underMyControl()->count();
    }

    public function trashCount()
    {
        return Company::underMyControl()->onlyTrashed()->count();
    }

    public function restore(int $id)
    {
        $record = Company::onlyTrashed()->findOrFail($id);
        $record->restore();
        return $record;
    }
}
