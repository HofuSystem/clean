<?php
 
namespace Core\Info\DataResources;
 
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Core\Admin\Helpers\DashboardDataTableFormatter;
class MapPointsResource extends JsonResource
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
            "lat"           => DashboardDataTableFormatter::text($this->lat),
            "lng"           => DashboardDataTableFormatter::text($this->lng),
            "district_id"   => DashboardDataTableFormatter::relations($this->district,"name","dashboard.districts.edit"),
            "actions"       => $this->actions,
            "select_switch" => $this->select_switch,
           
        ];
    }
}
