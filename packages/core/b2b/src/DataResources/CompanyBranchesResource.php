<?php

namespace Core\B2B\DataResources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Core\Admin\Helpers\DashboardDataTableFormatter;

class CompanyBranchesResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'name'         => DashboardDataTableFormatter::text($this->name),
            'location'     => DashboardDataTableFormatter::text($this->location),
            'lat'          => DashboardDataTableFormatter::text($this->lat),
            'lng'          => DashboardDataTableFormatter::text($this->lng),
            'city'         => DashboardDataTableFormatter::text($this->city?->name),
            'district'     => DashboardDataTableFormatter::text($this->district?->name),
            'user'         => DashboardDataTableFormatter::text($this->user?->fullname),
            'is_default'   => DashboardDataTableFormatter::checkbox($this->is_default),
            'company'      => DashboardDataTableFormatter::text($this->company?->fullname),
            'actions'      => $this->actions,
            'select_switch'=> $this->select_switch,
        ];
    }
}
