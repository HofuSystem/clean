<?php
 
namespace Core\Pages\DataResources;
 
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Core\Admin\Helpers\DashboardDataTableFormatter;
class FeaturesResource extends JsonResource
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
            "title"         => DashboardDataTableFormatter::text($this->title),
            "description"   => DashboardDataTableFormatter::text($this->description),
            "icon"          => DashboardDataTableFormatter::text($this->icon),
            "section"       => DashboardDataTableFormatter::text($this->section),
            "is_active"     => DashboardDataTableFormatter::checkbox($this->is_active),
            "actions"       => $this->actions,
            "select_switch" => $this->select_switch,
           
        ];
    }
}
