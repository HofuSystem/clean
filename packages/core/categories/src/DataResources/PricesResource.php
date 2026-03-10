<?php
 
namespace Core\Categories\DataResources;
 
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Core\Admin\Helpers\DashboardDataTableFormatter;
class PricesResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {

        return [
            
            "id"             => $this->id,
            "priceable_type" => DashboardDataTableFormatter::text($this->priceable_type),
            "priceable_id"   => DashboardDataTableFormatter::text($this->priceable_id),
            "city_id"        => DashboardDataTableFormatter::relations($this->city,"name","dashboard.cities.show"),
            "price"          => DashboardDataTableFormatter::text($this->price),
            "actions"        => $this->actions,
            "select_switch"  => $this->select_switch,
           
        ];
    }
}
