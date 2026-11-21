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
            "app_version"   => DashboardDataTableFormatter::text($this->app_version ?? ''),
            "os_version"    => DashboardDataTableFormatter::text($this->os_version ?? ''),
            "app_context"   => DashboardDataTableFormatter::text($this->app_context ? trans($this->app_context) : ''),
            "token_status"  => DashboardDataTableFormatter::text($this->token_status ? trans($this->token_status) : ''),
            "last_seen_at"  => $this->last_seen_at?->format('Y-m-d H:i'),
            "actions"       => $this->actions,
            "select_switch" => $this->select_switch,
           
        ];
    }
}
