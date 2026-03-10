<?php
 
namespace Core\Comments\DataResources;
 
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Core\Admin\Helpers\DashboardDataTableFormatter;
class CommentResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {

        return [
            "id"  => $this->id,
            "content"  => $this->content,
            "parent_id"  => $this->parent_id,
            "user"  => [
                'name' => $this->user->fullname,
                'email' => $this->user->email,
                'avatar' => $this->user->avatarUrl,
            ],
           
        ];
    }
}
