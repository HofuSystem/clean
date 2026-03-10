<?php

namespace Core\Notification\DataResources\Api;

use Carbon\Carbon;
use Core\MediaCenter\Helpers\MediaCenterHelper;
use Illuminate\Http\Resources\Json\JsonResource;

class BannerNotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'                => $this->id,
            'image'             => MediaCenterHelper::getImagesUrl($this->image) ,
            'publish_date'      => $this->publish_date      ? Carbon::parse($this->publish_date)->format("Y-m-d h:i:s") : '',
            'expired_date'      => $this->expired_date      ? Carbon::parse($this->expired_date)->format("Y-m-d h:i:s") : '',
            'last_vision_date'  => $this->last_vision_date  ? Carbon::parse($this->last_vision_date)->format("Y-m-d h:i:s") : '',
            'next_vision_date'  => $this->next_vision_date  ? Carbon::parse($this->next_vision_date)->format("Y-m-d h:i:s") : '',
            'next_vision_hour'  => $this->next_vision_hour,
        ];
    }
}
