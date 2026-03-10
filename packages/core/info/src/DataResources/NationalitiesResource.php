<?php
 
namespace Core\Info\DataResources;
 
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Core\Admin\Helpers\DashboardDataTableFormatter;
class NationalitiesResource extends JsonResource
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
            "arranging"     => DashboardDataTableFormatter::text($this->arranging),
            "actions"       => $this->actions,
            "select_switch" => $this->select_switch,
           
        ];
    }
}
