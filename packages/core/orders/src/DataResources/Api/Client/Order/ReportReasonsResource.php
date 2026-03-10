<?php
 
namespace Core\Orders\DataResources\Api\Client\Order;
 
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Core\Admin\Helpers\DashboardDataTableFormatter;
class ReportReasonsResource extends JsonResource
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
            "name"          => $this->name,
        ];
    }
}
