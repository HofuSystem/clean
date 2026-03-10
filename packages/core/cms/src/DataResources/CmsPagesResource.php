<?php
 
namespace Core\CMS\DataResources;
 
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Core\Admin\Helpers\DashboardDataTableFormatter;
class CmsPagesResource extends JsonResource
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
            "is_parent"     => DashboardDataTableFormatter::checkbox($this->is_parent),
            "actions"       => $this->actions,
            "select_switch" => $this->select_switch,
           
        ];
    }
}
