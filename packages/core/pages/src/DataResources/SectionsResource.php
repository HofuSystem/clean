<?php
 
namespace Core\Pages\DataResources;
 
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Core\Admin\Helpers\DashboardDataTableFormatter;
class SectionsResource extends JsonResource
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
            "small_title"   => DashboardDataTableFormatter::text($this->small_title),
            "description"   => DashboardDataTableFormatter::text($this->description),
            "images"        => DashboardDataTableFormatter::mediaCenter($this->images),
            "video"         => DashboardDataTableFormatter::text($this->video),
            "template"      => DashboardDataTableFormatter::text($this->template),
            "page_id"       => DashboardDataTableFormatter::relations($this->page,"title","dashboard.pages.show"),
            "order"         => DashboardDataTableFormatter::text($this->order),
            "actions"       => $this->actions,
            "select_switch" => $this->select_switch,
           
        ];
    }
}
