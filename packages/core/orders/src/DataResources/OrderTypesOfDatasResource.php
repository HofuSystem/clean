<?php
 
namespace Core\Orders\DataResources;
 
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Core\Admin\Helpers\DashboardDataTableFormatter;
class OrderTypesOfDatasResource extends JsonResource
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
            "order_id"      => DashboardDataTableFormatter::relations($this->order,"reference_id","dashboard.orders.show"),
            "key"           => DashboardDataTableFormatter::text($this->key),
            "value"         => DashboardDataTableFormatter::text($this->value),
            "actions"       => $this->actions,
            "select_switch" => $this->select_switch,
           
        ];
    }
}
