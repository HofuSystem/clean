<?php

namespace Core\Users\DataResources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Core\Admin\Helpers\DashboardDataTableFormatter;
class AddressesResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {

        return [
            "id"            => $this->id,
            "name"          => $this->name,
            "location"      => $this->location,
            "lat"           => $this->lat,
            "lng"           => $this->lng,
            "city_id"       => $this->city?->id,
            "city_name"     => $this->city?->name,
            "district_id"   => $this->district?->id,
            "district_name" => $this->district?->name,
            'is_default'    => $this->is_default,
        ];
    }
}
