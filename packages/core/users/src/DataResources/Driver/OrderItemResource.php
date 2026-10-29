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
            'variant_id'            => $this->product_variant_id,
            'base_pieces_count'     => $this->package_base_pieces_count,
            'extra_pieces_count'    => (int) ($this->extra_pieces_count ?? 0),
            'pieces_per_unit'       => $this->package_pieces_per_unit,
            'total_pieces_count'    => $this->package_total_pieces,
            'base_package_price'    => $this->package_base_price,
            'extra_piece_price'     => $this->package_extra_piece_price,
            'extra_pieces_total'    => $this->package_extra_pieces_total,

            'is_deleted'            => $this->deleted_at != null,
            'is_added'              => $this->add_by_admin != null,
            'is_updated'            => $this->update_by_admin != null,
            'customizations'        => $this->customizations,
            'card_note'             => $this->card_note,
        ];

        return $data;
    }
}
