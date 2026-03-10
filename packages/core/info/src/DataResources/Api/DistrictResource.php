<?php

namespace Core\Info\DataResources\Api;

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
            'id'       => $this->id,
            'name'     => $this->name,
            'slug'     => $this->slug,
            'lat'      => $this->lat,
            'lng'      => $this->lng,
            'city_id'  => $this->city_id,
            // Load map points only if the relation is loaded
            'mapPoints' => $this->relationLoaded('mapPoints')
                ? MapPointResource::collection($this->mapPoints)
                : [],
        ];
    }
}
