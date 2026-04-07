<?php

namespace Core\B2B\DataResources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Core\Admin\Helpers\DashboardDataTableFormatter;

class CompaniesResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'fullname'         => DashboardDataTableFormatter::text($this->fullname),
            'line_of_business' => DashboardDataTableFormatter::text($this->line_of_business),
            'email'            => DashboardDataTableFormatter::text($this->email),
            'phone'        => DashboardDataTableFormatter::text($this->phone),
            'avatar'       => DashboardDataTableFormatter::mediaCenter($this->image),
            'owner'        => DashboardDataTableFormatter::text($this->owner?->fullname),
            'bank_account_number' => DashboardDataTableFormatter::text($this->bank_account_number),
            'iban'                => DashboardDataTableFormatter::text($this->iban),
            'actions'      => $this->actions,
            'select_switch'=> $this->select_switch,
        ];
    }
}
