<?php
 
namespace Core\Categories\DataResources;
 
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Core\Admin\Helpers\DashboardDataTableFormatter;
class CategoryDateTimesResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {

        return [
            
            "type"          => trans($this->type),
            "category_id"   => DashboardDataTableFormatter::relations($this->category,"name","dashboard.categories.show"),
            "city_id"       => DashboardDataTableFormatter::relations($this->city,"name","dashboard.cities.show"),
            "date"          => $this->date,
            "count"         => $this->count,
            "actions"       => $this->actions,
            "select_switch" => $this->select_switch,
           
        ];
    }
}
