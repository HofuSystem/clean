<?php

namespace Core\B2B\Services;

use Core\B2B\Models\CompanyEmployee;
use Core\B2B\DataResources\CompanyEmployeesResource;

class CompanyEmployeesService
{
    public function selectable($cols = [], $with = [], $scopes = [])
    {
        $query = CompanyEmployee::select($cols)->with($with);
        foreach ($scopes as $scope) {
            $query = $query->$scope();
        }
        return $query->get();
    }

    public function storeOrUpdate(array $data = [], $id = null)
    {
        return CompanyEmployee::updateOrCreate(['id' => $id], $data);
    }

    public function get(int $id)
    {
        return CompanyEmployee::findOrFail($id);
    }

    public function delete(int $id, $final = false)
    {
        $record = CompanyEmployee::findOrFail($id);
        $final ? $record->forceDelete() : $record->delete();
        return true;
    }

    public function dataTable($draw)
    {
        $recordsTotal    = CompanyEmployee::count();
        $recordsFiltered = CompanyEmployee::search()->count();
        $records         = CompanyEmployee::with(['user', 'company', 'permission', 'branch'])->search()->dataTable()->get();

        return [
            'draw'            => $draw,
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data'            => CompanyEmployeesResource::collection($records),
        ];
    }
}
