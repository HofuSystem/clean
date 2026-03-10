<?php
 
namespace Core\Info\DataResources;
 
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Core\Admin\Helpers\DashboardDataTableFormatter;
class CountriesResource extends JsonResource
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
            "slug"          => DashboardDataTableFormatter::text($this->slug),
            "name"          => DashboardDataTableFormatter::text($this->name),
            "phonecode"     => DashboardDataTableFormatter::text($this->phonecode),
            "short_name"    => DashboardDataTableFormatter::text($this->short_name),
            "flag"          => DashboardDataTableFormatter::text($this->flag),
            "actions"       => $this->actions,
            "select_switch" => $this->select_switch,
           
        ];
    }
}
