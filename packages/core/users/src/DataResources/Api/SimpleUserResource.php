<?php

namespace Core\Users\DataResources\Api;

use Core\Info\DataResources\Api\CityResource;
use Core\Info\DataResources\Api\DistrictResource;
use Core\Info\DataResources\Api\TestCityResource;
use Core\MediaCenter\Helpers\MediaCenterHelper;
use Illuminate\Http\Resources\Json\JsonResource;

class SimpleUserResource extends JsonResource
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
            'id'        => $this->id,
            'fullname'  => $this->fullname,
            'phone'     => (string)$this->phone,
            'image'     => MediaCenterHelper::getImagesUrl(value: $this->image),
            'city'      => optional($this->profile)->city_id != null ? new CityResource($this->profile->city) : new TestCityResource($this),
            'district'  => optional($this->profile)->district_id ? new DistrictResource($this->profile->district) :  new TestCityResource($this),
        ];
    }
}
