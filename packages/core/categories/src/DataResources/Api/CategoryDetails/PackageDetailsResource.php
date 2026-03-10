<?php

namespace Core\Categories\DataResources\Api\CategoryDetails;

use Core\MediaCenter\Helpers\MediaCenterHelper;
use Core\Orders\Models\DeliveryPrice;
use Core\Products\DataResources\Api\SimpleProductResource;
use Illuminate\Http\Resources\Json\JsonResource;

class PackageDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $categoryId     = $this->id; 
 
        return [
            'id'              => $this->id ,
            'type'            => $this->type ,
            'name'            => $this->name ,
            'image'           => MediaCenterHelper::getImagesUrl($this->image) ,
            'is_package'      => (boolean)$this->is_package ,
            'products'        => SimpleProductResource::collection($this->products),
        ];
    }
}
