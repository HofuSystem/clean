<?php

namespace Core\Categories\DataResources\Api;

use Core\Categories\DataResources\Api\Services\SubServiceResource;
use Core\Info\DataResources\Api\CityResource;
use Core\MediaCenter\Helpers\MediaCenterHelper;
use Illuminate\Http\Resources\Json\JsonResource;

class SliderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // Build the UUID redirect URL if a sliderView record exists
        $linkUrl = null;
        if ($this->currentSliderView) {
            $linkUrl = route('slider.redirect', ['uuid' => $this->currentSliderView->uuid]);
        }

        return [
            'id' => $this->id,
            'image' => MediaCenterHelper::getImagesUrl($this->image_ar),
            'image_en' => MediaCenterHelper::getImagesUrl($this->image_en),
            'type' => $this->type,
            'link' => $linkUrl,
            'city' => new CityResource($this->city),
            'category' => new SampleCategoryResource($this->category),
            'service' => new SubServiceResource($this->category),
            'created_at' => $this->created_at?->format('y-m-d'),
        ];
    }
}
