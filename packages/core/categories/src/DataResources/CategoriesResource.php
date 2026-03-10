<?php
 
namespace Core\Categories\DataResources;
 
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Core\Admin\Helpers\DashboardDataTableFormatter;
use Core\Orders\Models\DeliveryPrice;

class CategoriesResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        $categoryId     = $this->id; 
        $deliveryPrice  = DeliveryPrice::where(function($categoryQuery)use($categoryId){
            $categoryQuery->where('category_id',$categoryId)->orWhereNull('category_id'); 
        })->where(function($cityQuery){
            $cityQuery->where('city_id',auth('api')->user()?->profile?->city_id)->orWhereNull('city_id'); 
        })->where(function($districtQuery){
            $districtQuery->where('district_id',auth('api')->user()?->profile?->city_id)->orWhereNull('district_id'); 
        })->orderBy('category_id')->first();
        return [
            
            "id"             => $this->id,
            "name"           => DashboardDataTableFormatter::text($this->name),
            "image"          => DashboardDataTableFormatter::mediaCenter($this->image),
            "desc"           => DashboardDataTableFormatter::text($this->desc),
            "type"           => DashboardDataTableFormatter::text(trans($this->type)),
            "delivery_price" => DashboardDataTableFormatter::text($deliveryPrice?->price),
            "sort"           => DashboardDataTableFormatter::text($this->sort),
            "is_package"     => DashboardDataTableFormatter::checkbox($this->is_package),
            "status"         => DashboardDataTableFormatter::text(trans($this->status)),
            "parent_id"      => DashboardDataTableFormatter::relations($this->parent,"name","dashboard.categories.show"),
            "cities"         => DashboardDataTableFormatter::relations($this->cities,"name","dashboard.cities.show"),
            "for_all_cities" => DashboardDataTableFormatter::checkbox($this->for_all_cities),
            "actions"        => $this->actions,
            "select_switch"  => $this->select_switch,
           
        ];
    }
}
