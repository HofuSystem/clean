<?php

namespace Core\Categories\DataResources\Api;

use Core\Categories\Models\Category;
use Core\MediaCenter\Helpers\MediaCenterHelper;
use Core\Orders\Models\DeliveryPrice;
use Core\Settings\Services\SettingsService;
use Illuminate\Http\Resources\Json\JsonResource;

class SampleCategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $cities      = $this->cities->pluck('id')->toArray(); 
        $isAvailable = (($this->for_all_cities || in_array(auth('api')->user()?->profile?->city_id,$cities)) and $this->status == "active");
        $message     = (!$isAvailable) ? SettingsService::getDataBaseSetting('not_available_message_'.config('app.locale')) : null;


        return [
            'id'                => $this->id,
            'type'              => $this->type,
            'name'              => $this->name,
            'availability'      => ['is_available'=> $isAvailable,'message'=>$message],
            'image'             => MediaCenterHelper::getImagesUrl($this->image) ,
            'is_package'        => (boolean)  $this->is_package ,
        ];
    }
}
