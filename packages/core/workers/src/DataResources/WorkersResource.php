<?php
 
namespace Core\Workers\DataResources;
 
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Core\Admin\Helpers\DashboardDataTableFormatter;
class WorkersResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            
            "id"               => $this->id,
            "image"            => DashboardDataTableFormatter::mediaCenter($this->image),
            "name"             => DashboardDataTableFormatter::text($this->name),
            "phone"            => DashboardDataTableFormatter::text($this->phone),
            "email"            => DashboardDataTableFormatter::text($this->email),
            "years_experience" => DashboardDataTableFormatter::text($this->years_experience),
            "address"          => DashboardDataTableFormatter::text($this->address),
            "birth_date"       => DashboardDataTableFormatter::text($this->birth_date),
            "hour_price"       => DashboardDataTableFormatter::text($this->hour_price),
            "gender"           => DashboardDataTableFormatter::text($this->gender),
            "status"           => DashboardDataTableFormatter::text($this->status),
            "identity"         => DashboardDataTableFormatter::text($this->identity),
            "nationality_id"   => DashboardDataTableFormatter::relations($this->nationality,"name","dashboard.nationalities.show"),
            "city_id"          => DashboardDataTableFormatter::relations($this->city,"name","dashboard.cities.show"),
            "categories"       => DashboardDataTableFormatter::relations($this->categories,"name","dashboard.categories.show"),
            "leader_id"        => DashboardDataTableFormatter::relation($this->leader,"fullname","dashboard.users.show"),
            "workdays"         => DashboardDataTableFormatter::relations($this->workdays,"date","dashboard.worker-days.show"),
            "actions"          => $this->actions,
            "select_switch"    => $this->select_switch,
           
        ];
    }
}
