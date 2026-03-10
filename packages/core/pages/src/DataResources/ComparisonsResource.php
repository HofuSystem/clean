<?php
 
namespace Core\Pages\DataResources;
 
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Core\Admin\Helpers\DashboardDataTableFormatter;

class ComparisonsResource extends JsonResource
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
            "point"         => DashboardDataTableFormatter::text($this->point),
            "us_text"       => DashboardDataTableFormatter::text($this->us_text),
            "them_text"     => DashboardDataTableFormatter::text($this->them_text),
            "order"         => DashboardDataTableFormatter::text($this->order),
            "actions"       => $this->actions,
            "select_switch" => $this->select_switch,
           
        ];
    }
}

