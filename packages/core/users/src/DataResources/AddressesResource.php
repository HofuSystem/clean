<?php

namespace Core\Users\DataResources;

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
            "name"          => DashboardDataTableFormatter::text($this->name),
            "location"      => DashboardDataTableFormatter::text($this->location),
            "lat"           => DashboardDataTableFormatter::text($this->lat),
            "lng"           => DashboardDataTableFormatter::text($this->lng),
            "city_id"       => DashboardDataTableFormatter::relations($this->city,"name","dashboard.cities.show"),
            "district_id"   => DashboardDataTableFormatter::relations($this->district,"name","dashboard.districts.show"),
            'is_default'    => DashboardDataTableFormatter::text($this->is_default),
            "status"        => DashboardDataTableFormatter::text($this->status),
            "actions"       => $this->actions,
            "select_switch" => $this->select_switch,
        ];
    }
}
