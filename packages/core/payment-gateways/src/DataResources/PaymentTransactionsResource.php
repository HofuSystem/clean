<?php
 
namespace Core\PaymentGateways\DataResources;
 
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Core\Admin\Helpers\DashboardDataTableFormatter;
class PaymentTransactionsResource extends JsonResource
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
            "transaction_id"   => DashboardDataTableFormatter::text($this->transaction_id),
            "for"              => DashboardDataTableFormatter::text($this->for),
            "status"           => DashboardDataTableFormatter::text($this->status),
            "request_data"     => DashboardDataTableFormatter::text($this->request_data),
            "payment_method"   => DashboardDataTableFormatter::text($this->payment_method),
            "payment_data"     => DashboardDataTableFormatter::text($this->payment_data),
            "payment_response" => DashboardDataTableFormatter::text($this->payment_response),
            "actions"          => $this->actions,
            "select_switch"    => $this->select_switch,
           
        ];
    }
}
