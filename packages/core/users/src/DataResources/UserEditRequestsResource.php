<?php
 
namespace Core\Users\DataResources;
 
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Core\Admin\Helpers\DashboardDataTableFormatter;
class UserEditRequestsResource extends JsonResource
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
            "fullname"      => DashboardDataTableFormatter::text($this->fullname),
            "email"         => DashboardDataTableFormatter::text($this->email),
            "phone"         => DashboardDataTableFormatter::text($this->phone),
            "user_id"       => DashboardDataTableFormatter::relations($this->user,"fullname","dashboard.users.show"),
            "actions"       => $this->actions,
            "select_switch" => $this->select_switch,
           
        ];
    }
}
