<?php

namespace Core\Categories\DataResources\Api;

use Core\MediaCenter\Helpers\MediaCenterHelper;
use Core\Settings\Helpers\ToolHelper;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceCategorySaleResource extends JsonResource
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
            'id'            => $this->id ,
            'name'          => $this->name ,
            'desc'          => (string)$this->desc_mobile,
            'price'         => ToolHelper::getPriceBasedOnCurrentWeekDay($this->price),
            'sale_price'    => ToolHelper::getPriceBasedOnCurrentWeekDay($this->sale_price),
            'image'         => MediaCenterHelper::getImagesUrl($this->image) ,
        ];
    }
}
