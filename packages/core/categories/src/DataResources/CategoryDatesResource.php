<?php
 
namespace Core\Categories\DataResources;
 
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Core\Admin\Helpers\DashboardDataTableFormatter;
class CategoryDatesResource extends JsonResource
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
            "type"          => DashboardDataTableFormatter::text(trans($this->type)),
            "category_id"   => DashboardDataTableFormatter::relations($this->category,"name","dashboard.categories.show"),
            "date"          => DashboardDataTableFormatter::text($this->date),
            "actions"       => $this->actions,
            "select_switch" => $this->select_switch,
           
        ];
    }
}
