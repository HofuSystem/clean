<?php
 
namespace Core\Users\DataResources;
 
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Core\Admin\Helpers\DashboardDataTableFormatter;
class ProfilesResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {

        return [
            
            "id"              => $this->id,
            "country_id"      => DashboardDataTableFormatter::relations($this->country,"name","dashboard.countries.show"),
            "city_id"         => DashboardDataTableFormatter::relations($this->city,"name","dashboard.cities.show"),
            "district_id"     => DashboardDataTableFormatter::relations($this->district,"name","dashboard.districts.show"),
            "other_city_name" => DashboardDataTableFormatter::text($this->other_city_name),
            "user_id"         => DashboardDataTableFormatter::relations($this->user,"fullname","dashboard.users.show"),
            "bio"             => DashboardDataTableFormatter::text($this->bio),
            "lat"             => DashboardDataTableFormatter::text($this->lat),
            "lng"             => DashboardDataTableFormatter::text($this->lng),
            "actions"         => $this->actions,
            "select_switch"   => $this->select_switch,
           
        ];
    }
}
