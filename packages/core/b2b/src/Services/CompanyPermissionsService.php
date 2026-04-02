<?php

namespace Core\B2B\Services;

use Core\B2B\Models\CompanyPermission;
use Core\B2B\DataResources\CompanyPermissionsResource;

class CompanyPermissionsService
{
    public function selectable($cols = [], $with = [], $scopes = [])
    {
        $query = CompanyPermission::select($cols)->with($with);
        foreach ($scopes as $scope) {
            $query = $query->$scope();
        }
        return $query->get();
    }

    public function storeOrUpdate(array $data = [], $id = null)
    {
        return CompanyPermission::updateOrCreate(['id' => $id], $data);
    }

    public function get(int $id)
    {
        return CompanyPermission::findOrFail($id);
    }

    public function delete(int $id, $final = false)
    {
        $record = CompanyPermission::findOrFail($id);
        $final ? $record->forceDelete() : $record->delete();
        return true;
    }

    public function dataTable($draw)
    {
        $recordsTotal    = CompanyPermission::count();
        $recordsFiltered = CompanyPermission::search()->count();
        $records         = CompanyPermission::search()->dataTable()->get();

        return [
            'draw'            => $draw,
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data'            => CompanyPermissionsResource::collection($records),
        ];
    }
}
