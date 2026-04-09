<?php

namespace Core\B2B\DataResources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Core\Admin\Helpers\DashboardDataTableFormatter;

class B2BFinancialsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'company'      => DashboardDataTableFormatter::text($this->company?->fullname),
            'reference_id' => DashboardDataTableFormatter::text($this->reference_id),
            'amount'       => DashboardDataTableFormatter::text($this->amount),
            'type'         => DashboardDataTableFormatter::text($this->type),
            'collection_date' => DashboardDataTableFormatter::text($this->collection_date?->format('Y-m-d')),
            'note'         => DashboardDataTableFormatter::text($this->note),
            'created_at'   => DashboardDataTableFormatter::text($this->created_at?->format('Y-m-d')),
            'actions'      => $this->actions,
            'select_switch'=> $this->select_switch,
        ];
    }
}
