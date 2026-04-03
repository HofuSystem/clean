<?php

namespace Core\B2B\DataResources\Api;

use Core\MediaCenter\Helpers\MediaCenterHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Core\Admin\Helpers\DashboardDataTableFormatter;
use Core\Info\DataResources\Api\CityResource;
use Core\Info\DataResources\Api\DistrictResource;
use Core\Info\DataResources\Api\TestCityResource;


class SimpleCompaniesResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'        => $this->id,
            'fullname'  => $this->fullname,
            'phone'     => (string)$this->phone,
            'image'     => MediaCenterHelper::getImagesUrl(value: $this->image),
            'city'      => optional($this?->owner?->profile)->city_id != null ? new CityResource($this->owner->profile->city) : new TestCityResource($this),
            'district'  => optional($this?->owner?->profile)->district_id ? new DistrictResource($this->owner->profile->district) :  new TestCityResource($this),
        ];
    }
}
