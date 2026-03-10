<?php
 
namespace Core\Categories\DataResources;
 
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Core\Admin\Helpers\DashboardDataTableFormatter;
class CategoryTypesResource extends JsonResource
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
            "intro"         => DashboardDataTableFormatter::text($this->intro),
            "desc"          => DashboardDataTableFormatter::text($this->desc),
            "category_id"   => DashboardDataTableFormatter::relations($this->category,"name","dashboard.categories.show"),
            "hour_price"    => DashboardDataTableFormatter::text($this->hour_price),
            "status"        => DashboardDataTableFormatter::text(trans($this->status)),
            "actions"       => $this->actions,
            "select_switch" => $this->select_switch,
           
        ];
    }
}
