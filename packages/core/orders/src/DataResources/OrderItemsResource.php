<?php
 
namespace Core\Orders\DataResources;
 
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Core\Admin\Helpers\DashboardDataTableFormatter;
class OrderItemsResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {

        return [
            
            "id"              => $this->id,
            "order_id"        => DashboardDataTableFormatter::relations($this->order,"reference_id","dashboard.orders.show"),
            "product_id"      => DashboardDataTableFormatter::relations($this->product,"name","dashboard.products.show"),
            "product_price"   => DashboardDataTableFormatter::text($this->product_price),
            "quantity"        => DashboardDataTableFormatter::text($this->quantity),
            "width"           => DashboardDataTableFormatter::text($this->width),
            "height"          => DashboardDataTableFormatter::text($this->height),
            "add_by_admin"    => DashboardDataTableFormatter::text($this->add_by_admin),
            "update_by_admin" => DashboardDataTableFormatter::text($this->update_by_admin),
            "is_picked"       => DashboardDataTableFormatter::checkbox($this->is_picked),
            "is_delivered"    => DashboardDataTableFormatter::checkbox($this->is_delivered),
            "actions"         => $this->actions,
            "select_switch"   => $this->select_switch,
           
        ];
    }
}
