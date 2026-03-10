<?php

namespace Core\CMS\DataResources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Core\Admin\Helpers\DashboardDataTableFormatter;
class CmsPageDetailsResource extends JsonResource
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
            "intro"          => DashboardDataTableFormatter::text($this->intro),
            "description"   => DashboardDataTableFormatter::text($this->description),
            "image"         => DashboardDataTableFormatter::mediaCenter($this->image),
            "mobile_image"  => DashboardDataTableFormatter::mediaCenter($this->mobile_image),
            "icon"          => DashboardDataTableFormatter::mediaCenter($this->icon),
            "video"         => DashboardDataTableFormatter::text($this->video),
            "link"          => DashboardDataTableFormatter::text($this->link),
            "actions"       => $this->actions,
            "select_switch" => $this->select_switch,
        ];
    }
}
