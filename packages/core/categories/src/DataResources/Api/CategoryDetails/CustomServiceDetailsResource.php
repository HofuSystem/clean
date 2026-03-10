<?php

namespace Core\Categories\DataResources\Api\CategoryDetails;

use Core\Categories\Models\Category;
use Core\Settings\Services\SettingsService;
use Core\Users\Models\Fav;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomServiceDetailsResource extends JsonResource
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
        return[
            'id'            => $this->id ,
            'name'          => $this->name ,
            'intro'         => $this->intro ,
            'desc'          => $this->desc_mobile ,
            'is_fav'        => Fav::where('favs_type',Category::class)->where('favs_id',$this->id)->where('user_id',auth('api')->user()?->id)->exists(),
            'availability'  => ['is_available'=> $isAvailable,'message'=>$message],
            'image'         => $this->custom_service_image ,
        ];
    }
}
