<?php
 
namespace App\DataResources;
 
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Core\Admin\Helpers\DashboardDataTableFormatter;
class B2BOrderResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {

        $receiver = $this->orderRepresentatives->where('type', 'receiver')->first();
        $delivery = $this->orderRepresentatives->where('type', 'delivery')->first();

        $data = [
            'created_at' => '<span class="fw-bold">' . $this->created_at->translatedFormat('Y-M-d h:i a') . '</span>',
            'reference_id' => '<span class="fw-bold">' . $this->reference_id . '</span>',
            'customer_name' => '<span class="fw-bold">' . $this?->client?->fullname . '</span>',
            'customer_phone' => '<span class="fw-bold">' . $this?->client?->phone . '</span>',
            'note' => '<span class="fw-bold">' . $this->note . '</span>',
            'pickup_date' => $receiver ? '<div>' . $receiver->date . '</div><small class="text-muted">' . $receiver->time_12_hours_format . ' - ' . $receiver->to_time_12_hours_format . '</small>' : '-',
            'delivery_date' => $delivery ? '<div>' . $delivery->date . '</div><small class="text-muted">' . $delivery->time_12_hours_format . ' - ' . $delivery->to_time_12_hours_format . '</small>' : '-',
            'total' => '<span class="text-primary fw-bold">' . ($this->total_price ?? 0) . ' ' . __('client.SAR') . '</span>',
            'status' => '<span class="badge ' . ($this->status == 'finished' || $this->status == 'delivered' ? 'bg-success' : 'bg-warning') . '">' . __($this->status) . '</span>',
            'actions' => '<button class="order-details-btn btn btn-sm btn-outline-primary" data-order-id="' . $this->id . '"><i class="fas fa-eye me-1"></i> ' . __('client.order_details') . '</button>'
        ];
        return $data;

    }
}
