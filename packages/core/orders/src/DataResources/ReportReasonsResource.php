<?php
 
namespace Core\Orders\DataResources;
 
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Core\Admin\Helpers\DashboardDataTableFormatter;
class ReportReasonsResource extends JsonResource
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
            "desc"          => DashboardDataTableFormatter::text($this->desc),
            "ordering"      => DashboardDataTableFormatter::text($this->ordering),
            "actions"       => $this->actions,
            "select_switch" => $this->select_switch,
           
        ];
    }
}
