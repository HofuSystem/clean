<?php
 
namespace Core\Orders\DataResources;
 
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Core\Admin\Helpers\DashboardDataTableFormatter;
class OrderSchedulesResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {

        return [
            
            "id"               => $this->id,
            "client_id"        => DashboardDataTableFormatter::relations($this->client,"fullname","dashboard.users.show"),
            "type"             => DashboardDataTableFormatter::text($this->type),
            "receiver_day"     => DashboardDataTableFormatter::text($this->receiver_day),
            "receiver_date"    => DashboardDataTableFormatter::text($this->receiver_date),
            "receiver_time"    => DashboardDataTableFormatter::text($this->receiver_time),
            "receiver_to_time" => DashboardDataTableFormatter::text($this->receiver_to_time),
            "delivery_day"     => DashboardDataTableFormatter::text($this->delivery_day),
            "delivery_date"    => DashboardDataTableFormatter::text($this->delivery_date),
            "delivery_time"    => DashboardDataTableFormatter::text($this->delivery_time),
            "delivery_to_time" => DashboardDataTableFormatter::text($this->delivery_to_time),
            "actions"          => $this->actions,
            "select_switch"    => $this->select_switch,
           
        ];
    }
}
