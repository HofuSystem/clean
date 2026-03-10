<?php

namespace Core\Info\DataResources\Api;

use Core\MediaCenter\Helpers\MediaCenterHelper;
use Illuminate\Http\Resources\Json\JsonResource;

class CountryResource extends JsonResource
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
            'id'            => (string)$this->id,
            'name'          => $this->name,
            'nationality'   => $this->nationality,
            'key'           => $this->phonecode,
            'flag'          => MediaCenterHelper::getImagesUrl($this->image),
        ];
    }
}
