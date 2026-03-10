<?php

namespace Core\Info\DataResources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class MapPointResource extends JsonResource
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
            'id'    =>  $this->id,
            'lat'   =>  $this->lat,
            'lng'   =>  $this->lng,
        ];
    }
}
