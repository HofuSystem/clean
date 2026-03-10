<?php

namespace Core\Users\DataResources\Driver;

use Core\Products\DataResources\SimpleProductResource;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $originalQuantity   = $this->qtyUpdates()->first()?->from;

        $data =  [
            'id'                    => $this->id ,
            'products'              => $this->product ? new SimpleProductResource($this->product) : '',
            'quantity'              => (int)$this->quantity,
            'original_quantity'     => $originalQuantity,

            'is_deleted'            => $this->deleted_at != null,
            'is_added'              => $this->add_by_admin != null,
            'is_updated'            => $this->update_by_admin != null,
        ];

        return $data;
    }
}
