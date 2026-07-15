<?php

namespace Core\Financials\DataResources;

use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseProvidersResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'commercial_registration' => $this->commercial_registration,
            'tax_number' => $this->tax_number,
            'street_name' => $this->street_name,
            'building_no' => $this->building_no,
            'city_id' => $this->city_id,
            'city' => $this->city?->name,
            'district_id' => $this->district_id,
            'district' => $this->district?->name,
            'postal_code' => $this->postal_code,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'actions' => $this->actions,
            'select_switch'=> $this->select_switch,
        ];
    }
}
