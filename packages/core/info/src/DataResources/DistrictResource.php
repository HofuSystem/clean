<?php

namespace Core\Info\DataResources;

use Illuminate\Http\Resources\Json\JsonResource;

class DistrictResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id'=>$this->id,
            'slug'=>$this->slug,
            'name'=>$this->name,
            'lat'=>$this->lat,
            'lng'=>$this->lng,
            'city_id'=>$this->city_id
        ];
    }
}
