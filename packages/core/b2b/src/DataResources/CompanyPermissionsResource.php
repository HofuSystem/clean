<?php

namespace Core\B2B\DataResources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Core\Admin\Helpers\DashboardDataTableFormatter;

class CompanyPermissionsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'name'           => DashboardDataTableFormatter::text($this->name),
            'description'    => DashboardDataTableFormatter::text($this->description),
            'slug'           => DashboardDataTableFormatter::text($this->slug),
            'actions'        => $this->actions,
            'select_switch'  => $this->select_switch,
        ];
    }
}
