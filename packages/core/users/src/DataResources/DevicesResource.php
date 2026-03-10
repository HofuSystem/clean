<?php
 
namespace Core\Users\DataResources;
 
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Core\Admin\Helpers\DashboardDataTableFormatter;
class DevicesResource extends JsonResource
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
            "device_token"  => DashboardDataTableFormatter::text($this->device_token),
            "type"          => DashboardDataTableFormatter::text($this->type),
            "user_id"       => DashboardDataTableFormatter::relations($this->user,"fullname","dashboard.users.show"),
            "actions"       => $this->actions,
            "select_switch" => $this->select_switch,
           
        ];
    }
}
