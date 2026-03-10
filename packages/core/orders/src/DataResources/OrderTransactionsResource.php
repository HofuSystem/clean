<?php
 
namespace Core\Orders\DataResources;
 
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Core\Admin\Helpers\DashboardDataTableFormatter;
class OrderTransactionsResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {

        return [
            
            "id"                    => $this->id,
            "order_id"              => DashboardDataTableFormatter::relations($this->order,"phone","dashboard.orders.show"),
            "type"                  => DashboardDataTableFormatter::text($this->type),
            "online_payment_method" => DashboardDataTableFormatter::text($this->online_payment_method),
            "amount"                => DashboardDataTableFormatter::text($this->amount),
            "transaction_id"        => DashboardDataTableFormatter::text($this->transaction_id),
            "point_id"              => DashboardDataTableFormatter::relations($this->point,"id","dashboard.coupons.show"),
            "wallet_transaction_id" => DashboardDataTableFormatter::relations($this->walletTransaction,"type","dashboard.wallet-transactions.show"),
            "actions"               => $this->actions,
            "select_switch"         => $this->select_switch,
           
        ];
    }
}
