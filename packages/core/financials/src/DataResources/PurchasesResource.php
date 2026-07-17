<?php

namespace Core\Financials\DataResources;

use Illuminate\Http\Resources\Json\JsonResource;

class PurchasesResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'reference_id' => $this->reference_id,
            'item_id' => $this->item_id,
            'item' => $this->item?->name,
            'provider_id' => $this->provider_id,
            'provider' => $this->provider?->name,
            'value_before_tax' => $this->value_before_tax,
            'tax_value' => $this->tax_value,
            'value_after_tax' => $this->value_after_tax,
            'notes' => $this->notes,
            'attachment' => $this->attachment,
            'collection_date' => $this->collection_date?->format('Y-m-d'),
            'bank_transfer_files' => $this->bank_transfer_files,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'actions' => $this->actions,
            'select_switch'=> $this->select_switch,
        ];
    }
}
