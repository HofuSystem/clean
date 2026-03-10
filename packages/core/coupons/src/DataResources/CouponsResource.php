<?php
 
namespace Core\Coupons\DataResources;
 
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Core\Admin\Helpers\DashboardDataTableFormatter;
class CouponsResource extends JsonResource
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
            "title"         => DashboardDataTableFormatter::text($this->title),
            "status"        => DashboardDataTableFormatter::text(trans($this->status)),
            "applying"      => DashboardDataTableFormatter::text($this->applying),
            "code"          => DashboardDataTableFormatter::text($this->code),
            "start_at"      => DashboardDataTableFormatter::text($this->start_at),
            "end_at"        => DashboardDataTableFormatter::text($this->end_at),
            "type"          => DashboardDataTableFormatter::text($this->type),
            "value"         => DashboardDataTableFormatter::text($this->value),
            "max_value"     => DashboardDataTableFormatter::text($this->max_value),
            "actions"       => $this->actions,
            "select_switch" => $this->select_switch,
            "orders_count"  => DashboardDataTableFormatter::text($this->orders_count),
        ];
    }
}
