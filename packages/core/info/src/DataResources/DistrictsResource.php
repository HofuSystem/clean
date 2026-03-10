<?php
 
namespace Core\Info\DataResources;
 
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Core\Admin\Helpers\DashboardDataTableFormatter;
class DistrictsResource extends JsonResource
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
            "slug"          => DashboardDataTableFormatter::text($this->slug),
            "name"          => DashboardDataTableFormatter::text($this->name),
            "lat"           => DashboardDataTableFormatter::text($this->lat),
            "lng"           => DashboardDataTableFormatter::text($this->lng),
            "postal_code"   => DashboardDataTableFormatter::text($this->postal_code),
            "city_id"       => DashboardDataTableFormatter::relations($this->city,"name","dashboard.cities.edit"),
            "actions"       => $this->actions,
            "select_switch" => $this->select_switch,
           
        ];
    }
}
