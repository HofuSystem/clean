<?php
 
namespace Core\Orders\DataResources;
 
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Core\Admin\Helpers\DashboardDataTableFormatter;
class OrderReportsResource extends JsonResource
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
            "order_id"         => DashboardDataTableFormatter::relations($this->order,"reference_id","dashboard.orders.show"),
            "user_id"          => DashboardDataTableFormatter::relations($this->user,"fullname","dashboard.users.show"),
            "report_reason_id" => DashboardDataTableFormatter::relations($this->reportReason,"name","dashboard.report-reasons.show"),
            "desc_location"    => DashboardDataTableFormatter::text($this->desc_location),
            "actions"          => $this->actions,
            "select_switch"    => $this->select_switch,
           
        ];
    }
}
