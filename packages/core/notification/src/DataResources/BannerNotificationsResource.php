<?php
 
namespace Core\Notification\DataResources;
 
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Core\Admin\Helpers\DashboardDataTableFormatter;
class BannerNotificationsResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {

        return [
            
            "id"               => $this->id,
            "image"            => DashboardDataTableFormatter::mediaCenter($this->image),
            "publish_date"     => DashboardDataTableFormatter::text($this->publish_date),
            "expired_date"     => DashboardDataTableFormatter::text($this->expired_date),
            "next_vision_hour" => DashboardDataTableFormatter::text($this->next_vision_hour),
            "status"           => DashboardDataTableFormatter::text($this->status),
            "actions"          => $this->actions,
            "select_switch"    => $this->select_switch,
           
        ];
    }
}
