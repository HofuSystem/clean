<?php
 
namespace Core\Admin\DataResources;
 
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Core\Admin\Helpers\DashboardDataTableFormatter;
class RoutesRecordsResource extends JsonResource
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
            "version"       => DashboardDataTableFormatter::text($this->version),
            "end_point"     => DashboardDataTableFormatter::text($this->end_point),
            "attributes"    => DashboardDataTableFormatter::text($this->attributes),
            "user_id"       => DashboardDataTableFormatter::relations($this->user,"fullname","dashboard.users.edit"),
            "ip_address"    => DashboardDataTableFormatter::text($this->ip_address),
            "headers"       => DashboardDataTableFormatter::text($this->headers),
            "method"        => DashboardDataTableFormatter::text($this->method),
            "created_at"    => $this->created_at->format('Y-m-d h:i:s a'),
            "actions"       => $this->actions,
            "select_switch" => $this->select_switch,
           
        ];
    }
}
