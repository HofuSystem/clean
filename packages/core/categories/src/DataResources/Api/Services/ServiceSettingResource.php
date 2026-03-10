<?php

namespace Core\Categories\DataResources\Api\Services;

use Core\Settings\Helpers\ToolHelper;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceSettingResource extends JsonResource
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
        $cityPrice  = $this->addonPrices->where('city_id',auth('api')->user()?->profile?->city_id)->first();
        if($cityPrice){
            $price  = $cityPrice->price;
            $cost   = $cityPrice->cost;
        }
        return [
            'id'            => $this->id ,
            'name'          => $this->name ,
            'price'         => ToolHelper::getPriceBasedOnCurrentWeekDay($price),
            'cost'          => $cost,
        ];
    }
}
