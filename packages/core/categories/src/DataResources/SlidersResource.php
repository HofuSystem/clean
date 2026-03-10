<?php

namespace Core\Categories\DataResources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Core\Admin\Helpers\DashboardDataTableFormatter;
class SlidersResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [

            "id" => $this->id,
            "image" => DashboardDataTableFormatter::mediaCenter($this->{'image_' . config('app.locale')}),
            "category_id" => DashboardDataTableFormatter::relations($this->category, "name", "dashboard.categories.show"),
            "type" => DashboardDataTableFormatter::text(trans($this->type)),
            "status" => DashboardDataTableFormatter::text(trans($this->status)),
            "city_id" => DashboardDataTableFormatter::relations($this->city, "name", "dashboard.cities.show"),
            "link" => DashboardDataTableFormatter::text($this->link),
            "actions" => $this->actions,
            "select_switch" => $this->select_switch,

        ];
    }
}
