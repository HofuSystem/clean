<?php
 
namespace Core\Users\DataResources;
 
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Core\Admin\Helpers\DashboardDataTableFormatter;
class ContractsResource extends JsonResource
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
            "months_count" => DashboardDataTableFormatter::text($this->months_count),
            "month_fees"    => DashboardDataTableFormatter::text($this->month_fees),
            "unlimited_days" => DashboardDataTableFormatter::text($this->unlimited_days),
            "number_of_days" => DashboardDataTableFormatter::text($this->number_of_days),
            "contract"      => DashboardDataTableFormatter::mediaCenter($this->contract),
            "start_date"    => DashboardDataTableFormatter::text($this->start_date),
            "end_date"      => DashboardDataTableFormatter::text($this->end_date),
            "actions"       => $this->actions,
            "select_switch" => $this->select_switch,
           
        ];
    }
}
