<?php
 
namespace Core\Categories\DataResources;
 
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Core\Admin\Helpers\DashboardDataTableFormatter;
class CategoryOffersResource extends JsonResource
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
            "name"          => DashboardDataTableFormatter::text($this->name),
            "price"         => DashboardDataTableFormatter::text($this->price),
            "sale_price"    => DashboardDataTableFormatter::text($this->sale_price),
            "image"         => DashboardDataTableFormatter::mediaCenter($this->image),
            "hours_num"     => DashboardDataTableFormatter::text($this->hours_num),
            "workers_num"   => DashboardDataTableFormatter::text($this->workers_num),
            "status"        => DashboardDataTableFormatter::text(trans($this->status)),
            "type"          => DashboardDataTableFormatter::text(trans($this->type)),
            "actions"       => $this->actions,
            "select_switch" => $this->select_switch,
           
        ];
    }
}
