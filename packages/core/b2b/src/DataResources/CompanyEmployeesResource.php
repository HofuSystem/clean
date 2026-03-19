<?php

namespace Core\B2B\DataResources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Core\Admin\Helpers\DashboardDataTableFormatter;

class CompanyEmployeesResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'user'           => DashboardDataTableFormatter::text($this->user?->fullname),
            'company'        => DashboardDataTableFormatter::text($this->company?->fullname),
            'permission'     => DashboardDataTableFormatter::text($this->permission?->name),
            'branch'         => DashboardDataTableFormatter::text($this->branch?->name),
            'actions'        => $this->actions,
            'select_switch'  => $this->select_switch,
        ];
    }
}
