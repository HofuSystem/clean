<?php
 
namespace Core\Users\DataResources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Core\Admin\Helpers\DashboardDataTableFormatter;
class PointsResource extends JsonResource
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
            "amount"        => DashboardDataTableFormatter::text($this->amount),
            "operation"     => DashboardDataTableFormatter::text($this->operation),
            "expire_at"     => $this->expire_at ? Carbon::parse($this->expire_at)->format('Y-m-d') : null,
            "actions"       => $this->actions,
            "select_switch" => $this->select_switch,
           
        ];
    }
}
