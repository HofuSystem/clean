<?php

namespace Core\Products\DataResources\Api;

use Core\MediaCenter\Helpers\MediaCenterHelper;
use Core\Settings\Helpers\ToolHelper;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductServiceSettingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $price      = $this->addon_price;
        $cost       = $this->cost;
        $cityId     = auth('api')->user()?->profile?->city_id ?? request('city_id');
        $cityPrice  = $this->addonPrices->where('city_id', $cityId)->first();
        if($cityPrice){
            $price  = $cityPrice->price;
            $cost   = $cityPrice->cost;
        }
        return [
            'id'            => $this->id ,
            'name'          => $this->name ,
            'description'   => $this->description ?? null ,
            'slug'          => $this->slug ,
            'icon'          => isset($this->icon) ? MediaCenterHelper::getImageUrl($this->icon) : null ,
            'color'         => $this->color ,
            'price'         => ToolHelper::getPriceBasedOnCurrentWeekDay($price),
            'cost'          => $cost,
            'sub_settings'  => ProductServiceSettingResource::collection($this->productSettings),
        ];
    }
}
