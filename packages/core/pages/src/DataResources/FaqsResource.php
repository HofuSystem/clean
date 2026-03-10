<?php
 
namespace Core\Pages\DataResources;
 
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Core\Admin\Helpers\DashboardDataTableFormatter;
class FaqsResource extends JsonResource
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
            "question"      => DashboardDataTableFormatter::text($this->question),
            "answer"        => DashboardDataTableFormatter::text($this->answer),
            "actions"       => $this->actions,
            "select_switch" => $this->select_switch,
           
        ];
    }
}
