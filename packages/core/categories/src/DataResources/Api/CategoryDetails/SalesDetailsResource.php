<?php

namespace Core\Categories\DataResources\Api\CategoryDetails;

use Illuminate\Http\Resources\Json\JsonResource;
use Core\Orders\Models\DeliveryPrice;
use Core\Products\DataResources\Api\SimpleProductResource;

class SalesDetailsResource extends JsonResource
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
            'id'                => $this->id ,
            'type'              => $this->type ,
            'name'              => $this->name ,
            'image'             => $this->clothes_category_icon ,
            'products'          => SimpleProductResource::collection($this->products),
        ] ;
    }
}
