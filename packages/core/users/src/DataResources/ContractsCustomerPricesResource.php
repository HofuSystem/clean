<?php

namespace Core\Users\DataResources;

use Illuminate\Http\Resources\Json\JsonResource;

class ContractsCustomerPricesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id'                => $this->id,
            'contract_id'       => $this->contract_id,
            'contract'          => $this->contract?->title,
            'product_id'        => $this->product_id,
            'product'           => $this->product?->name,
            'over_price'        => $this->over_price,
            'created_at'        => $this->created_at?->format('Y-m-d'),
            'updated_at'        => $this->updated_at?->format('Y-m-d'),
            'actions'           => $this->actions,
            'items_actions'     => $this->items_actions,
            'show_actions'      => $this->show_actions,
        ];
    }
}

