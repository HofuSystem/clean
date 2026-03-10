<?php

namespace Core\Info\DataResources\Api;

use Core\MediaCenter\Helpers\MediaCenterHelper;
use Illuminate\Http\Resources\Json\JsonResource;

class CityResource extends JsonResource
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
            'name'      => $this->name,
            'name_ar'   => $this->translate('ar')->name,
            'name_en'   => $this->translate('en')->name,
            'image'     => MediaCenterHelper::getImagesUrl($this->image),
            'is_active' => (boolean)($this->status == 'active'),
        ];
    }
}
