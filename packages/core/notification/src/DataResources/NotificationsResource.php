<?php
 
namespace Core\Notification\DataResources;
 
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Core\Admin\Helpers\DashboardDataTableFormatter;
class NotificationsResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {

        $data =  [
            
            "id"            => $this->id,
            "types"         => DashboardDataTableFormatter::text($this->types),
            "for"           => DashboardDataTableFormatter::text($this->for),
            "title"         => DashboardDataTableFormatter::text($this->title),
            "body"          => DashboardDataTableFormatter::text($this->body),
            "media"         => DashboardDataTableFormatter::mediaCenter($this->media),
            "sender_id"     => DashboardDataTableFormatter::relations($this->sender,"fullname","dashboard.users.show"),
            "created_at"   => $this->created_at?->format('Y-m-d h:i a'),
            "actions"       => $this->actions,
            "select_switch" => $this->select_switch,
           
        ];
        $for = $this->for;
        if($for == "users"){
            $count = $this->users()->count();
            $users = $this->users()->first();
            if($count == 1){
                $for = "<a href='".route('dashboard.users.edit', $users->id)."'>".$users->phone." - ".$users->fullname."</a>";
            }else{
                $for = $count . " " . trans('users');
            }
        }
        $data['for'] = $for;
        return $data;
    }
}
