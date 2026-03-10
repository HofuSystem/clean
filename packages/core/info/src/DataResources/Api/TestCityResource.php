<?php

namespace Core\Info\DataResources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class TestCityResource extends JsonResource
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
            'id' => 0,
            'name' => 'الرياض',
            'image' => '',
            'is_active' => true,
            // 'delivery_price' => (double)$this->delivery_price,
        ];
    }
}
