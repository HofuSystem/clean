<?php
 
namespace Core\Users\DataResources;
 
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Core\Admin\Helpers\DashboardDataTableFormatter;
class BansResource extends JsonResource
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
            "level"         => DashboardDataTableFormatter::text($this->level),
            "value"         => DashboardDataTableFormatter::text($this->value),
            "admin_id"      => DashboardDataTableFormatter::relations($this->admin,"fullname","dashboard.users.show"),
            "reason"        => DashboardDataTableFormatter::text($this->reason),
            "from"          => DashboardDataTableFormatter::text($this->from),
            "to"            => DashboardDataTableFormatter::text($this->to),
            "actions"       => $this->actions,
            "select_switch" => $this->select_switch,
           
        ];
    }
}
