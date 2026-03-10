<?php
 
namespace Core\Info\DataResources;
 
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Core\Admin\Helpers\DashboardDataTableFormatter;
class CitiesResource extends JsonResource
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
            "slug"           => DashboardDataTableFormatter::text($this->slug),
            "name"           => DashboardDataTableFormatter::text($this->name),
            "postal_code"    => DashboardDataTableFormatter::text($this->postal_code),
            "image"          => DashboardDataTableFormatter::mediaCenter($this->image),
            "delivery_price" => DashboardDataTableFormatter::text($this->delivery_price),
            "status"         => DashboardDataTableFormatter::text(trans($this->status)),
            "country_id"     => DashboardDataTableFormatter::relations($this->country,"name","dashboard.countries.show"),
            "actions"        => $this->actions,
            "select_switch"  => $this->select_switch,
           
        ];
    }
}
