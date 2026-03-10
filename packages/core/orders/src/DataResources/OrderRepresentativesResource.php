<?php
 
namespace Core\Orders\DataResources;
 
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Core\Admin\Helpers\DashboardDataTableFormatter;
class OrderRepresentativesResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {

        return [
            
            "id"                => $this->id,
            "order_id"          => DashboardDataTableFormatter::relations($this->order,"reference_id","dashboard.orders.show"),
            "representative_id" => DashboardDataTableFormatter::relations($this->representative,"fullname","dashboard.users.show"),
            "type"              => DashboardDataTableFormatter::text($this->type),
            "date"              => DashboardDataTableFormatter::text($this->date),
            "time"              => DashboardDataTableFormatter::text($this->time),
            "to_time"           => DashboardDataTableFormatter::text($this->to_time),
            "location"          => DashboardDataTableFormatter::text($this->location),
            "has_problem"       => DashboardDataTableFormatter::checkbox($this->has_problem),
            "actions"           => $this->actions,
            "select_switch"     => $this->select_switch,
           
        ];
    }
}
