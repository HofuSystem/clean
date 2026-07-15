<?php

namespace Core\Financials\DataResources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Core\Admin\Helpers\DashboardDataTableFormatter;

class FinancialsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'company_id'   => $this->company_id,
            'user_id'      => $this->user_id,
            'company'      => DashboardDataTableFormatter::text($this->company?->fullname),
            'user'         => DashboardDataTableFormatter::text($this->user?->fullname),
            'reference_id' => DashboardDataTableFormatter::text($this->reference_id),
            'amount'       => DashboardDataTableFormatter::text($this->amount),
            'type'         => $this->type == 'owed' ? '<span class="text-danger fw-bolder">' . trans('Add Owed (Credit)') . '</span>' : '<span class="text-success fw-bolder">' . trans('Add Paid (Debit)') . '</span>',
            'collection_date' => DashboardDataTableFormatter::text($this->collection_date?->format('Y-m-d')),
            'note'         => DashboardDataTableFormatter::text($this->note),
            'created_at'   => DashboardDataTableFormatter::text($this->created_at?->format('Y-m-d')),
            'actions'      => $this->actions,
            'select_switch'=> $this->select_switch,
        ];
    }
}
