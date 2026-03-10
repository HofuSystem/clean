<?php

namespace Core\Orders\DataResources\Api\Client\Order;

use Core\Products\DataResources\Api\SimpleProductResource;
use Core\Products\DataResources\Api\TestProductResource;
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
        $data =[
            'id'                    => $this->id ,
            'price'                 => $this->product_price,
            'quantity'              => (int)$this->quantity,
            'total_price'           => $this->total_price,
            'products'              => $this->product ? new SimpleProductResource($this->product) : new TestProductResource($this),
            'original_quantity'     => $originalQuantity,
            
            'is_deleted'            => $this->deleted_at != null,
            'is_added'              => $this->add_by_admin != null,
            'is_updated'            => $this->update_by_admin != null,
        ];
        if($this->width and $this->height){
            $data['carpet_size'] = $this->width * $this->height;
            $data['width']       = $this->width;
            $data['height']      = $this->height;
            $data['total']       = $this->total_price;
        }
        return $data;
    }
}
