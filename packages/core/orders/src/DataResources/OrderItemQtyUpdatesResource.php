<?php
 
namespace Core\Orders\DataResources;
 
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Core\Admin\Helpers\DashboardDataTableFormatter;
class OrderItemQtyUpdatesResource extends JsonResource
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
            "item_id"       => DashboardDataTableFormatter::relations($this->item,"product_id","dashboard.order-items.show"),
            "from"          => DashboardDataTableFormatter::text($this->from),
            "to"            => DashboardDataTableFormatter::text($this->to),
            "updater_email" => DashboardDataTableFormatter::text($this->updater_email),
            "actions"       => $this->actions,
            "select_switch" => $this->select_switch,
           
        ];
    }
}
