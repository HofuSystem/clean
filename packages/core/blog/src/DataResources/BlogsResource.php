<?php
 
namespace Core\Blog\DataResources;
 
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Core\Admin\Helpers\DashboardDataTableFormatter;
class BlogsResource extends JsonResource
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
            "image"         => DashboardDataTableFormatter::mediaCenter($this->image),
            "gallery"       => DashboardDataTableFormatter::mediaCenter($this->gallery),
            "content"       => DashboardDataTableFormatter::text($this->content),
            "category_id"   => DashboardDataTableFormatter::relations($this->category,"title","dashboard.blog-categories.show"),
            "status"        => DashboardDataTableFormatter::text(trans($this->status)),
            "published_at"  => DashboardDataTableFormatter::text($this->published_at),
            "actions"       => $this->actions,
            "select_switch" => $this->select_switch,
           
        ];
    }
}
