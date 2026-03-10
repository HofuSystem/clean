<?php
 
namespace Core\Categories\DataResources;
 
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Core\Admin\Helpers\DashboardDataTableFormatter;
class CategoryTimesResource extends JsonResource
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
            "category_id"   => DashboardDataTableFormatter::relations($this->category,"name","dashboard.categories.show"),
            "day"           => DashboardDataTableFormatter::text($this->day),
            "from"          => DashboardDataTableFormatter::text($this->from),
            "to"            => DashboardDataTableFormatter::text($this->to),
            "actions"       => $this->actions,
            "select_switch" => $this->select_switch,
           
        ];
    }
}
