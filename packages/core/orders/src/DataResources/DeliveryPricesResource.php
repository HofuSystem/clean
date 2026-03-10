<?php
 
namespace Core\Orders\DataResources;
 
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Core\Admin\Helpers\DashboardDataTableFormatter;
class DeliveryPricesResource extends JsonResource
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
            "city_id"       => DashboardDataTableFormatter::relations($this->city,"name","dashboard.cities.show"),
            "district_id"   => DashboardDataTableFormatter::relations($this->district,"name","dashboard.districts.show"),
            "price"         => DashboardDataTableFormatter::text($this->price),
            "free_delivery" => DashboardDataTableFormatter::text($this->free_delivery),
            "actions"       => $this->actions,
            "select_switch" => $this->select_switch,
           
        ];
    }
}
