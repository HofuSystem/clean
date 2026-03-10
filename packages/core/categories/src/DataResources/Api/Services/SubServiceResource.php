<?php

namespace Core\Categories\DataResources\Api\Services;

use Core\Categories\Models\Category;
use Core\MediaCenter\Helpers\MediaCenterHelper;
use Core\Settings\Services\SettingsService;
use Core\Users\Models\Fav;
use Illuminate\Http\Resources\Json\JsonResource;

class SubServiceResource extends JsonResource
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
            'id'                => $this->id ,
            'parent_id'         => $this->parent_id,
            'name'              => $this->name ,
            'intro'             => $this->intro ,
            'desc'              => (string)$this->desc_mobile,
            'image'             => MediaCenterHelper::getImagesUrl($this->image) ,
            'availability'      => ['is_available'=> $isAvailable,'message'=>$message],
            'is_fav'            => Fav::where('favs_type',Category::class)->where('favs_id',$this->id)->where('user_id',auth('api')->user()?->id)->exists(),
        ];
    }
}
