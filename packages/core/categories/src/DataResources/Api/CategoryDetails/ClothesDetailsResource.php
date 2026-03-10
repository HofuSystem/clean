<?php

namespace Core\Categories\DataResources\Api\CategoryDetails;

use Core\Categories\DataResources\Api\SubCategoryResource;
use Core\MediaCenter\Helpers\MediaCenterHelper;
use Core\Orders\Models\DeliveryPrice;
use Illuminate\Http\Resources\Json\JsonResource;

class ClothesDetailsResource extends JsonResource
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
        
        
        return[
            'id'                => $this->id ,
            'type'              => $this->type ,
            'name'              => $this->name ,
            'name_ar'           => $this->translate('ar')->name,
            'name_en'           => $this->translate('en')->name,
            'image'             => MediaCenterHelper::getImagesUrl($this->image) ,
            'is_package'        => (boolean)$this->is_package ,
            'sub_category'      => SubCategoryResource::collection($this->subCategories)
        ];
    }
}
