<?php
 
namespace Core\Workers\DataResources;
 
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Core\Admin\Helpers\DashboardDataTableFormatter;
class WorkerDaysResource extends JsonResource
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
            "worker_id"     => DashboardDataTableFormatter::relations($this->worker,"name","dashboard.workers.show"),
            "date"          => DashboardDataTableFormatter::text($this->date),
            "type"          => DashboardDataTableFormatter::text($this->type),
            "actions"       => $this->actions,
            "select_switch" => $this->select_switch,
           
        ];
    }
}
