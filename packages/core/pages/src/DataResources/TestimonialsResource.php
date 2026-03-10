<?php
 
namespace Core\Pages\DataResources;
 
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Core\Admin\Helpers\DashboardDataTableFormatter;
class TestimonialsResource extends JsonResource
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
            "avatar"        => DashboardDataTableFormatter::mediaCenter($this->avatar),
            "title"         => DashboardDataTableFormatter::text($this->title),
            "body"          => DashboardDataTableFormatter::text($this->body),
            "location"      => DashboardDataTableFormatter::text($this->location),
            "actions"       => $this->actions,
            "select_switch" => $this->select_switch,
           
        ];
    }
}
