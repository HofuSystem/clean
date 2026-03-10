<?php
 
namespace Core\Wallet\DataResources;
 
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Core\Admin\Helpers\DashboardDataTableFormatter;
use Carbon\Carbon;

class WalletTransactionsResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            
            "id"             => $this->id,
            "type"           => DashboardDataTableFormatter::text($this->transaction_type ?? $this->type),
            "amount"         => DashboardDataTableFormatter::text($this->amount),
            "wallet_before"  => DashboardDataTableFormatter::text($this->wallet_before),
            "wallet_after"   => DashboardDataTableFormatter::text($this->wallet_after),
            "current_wallet" => (isset($this?->user?->wallet) and $this?->user?->wallet > 0) ? "(".$this?->user?->wallet.") ".trans("SAR") : "",
            "status"         => DashboardDataTableFormatter::text($this->status),
            "transaction_id" => DashboardDataTableFormatter::text($this->transaction_id),
            "bank_name"      => DashboardDataTableFormatter::text($this->bank_name),
            "account_number" => DashboardDataTableFormatter::text($this->account_number),
            "iban_number"    => DashboardDataTableFormatter::text($this->iban_number),
            "user_id"        => DashboardDataTableFormatter::relations($this->user,"fullname","dashboard.users.show"),
            "added_by_id"    => DashboardDataTableFormatter::relations($this->addedBy,"fullname","dashboard.users.show"),
            "package_id"     => DashboardDataTableFormatter::relations($this->package,"price","dashboard.wallet-packages.show"),
            "order_id"       => DashboardDataTableFormatter::relations($this->order,"reference_id","dashboard.orders.show"),
            "expired_at"     => $this->expired_at ? Carbon::parse($this->expired_at)->format('Y-m-d') : null,
            "actions"        => $this->actions,
            "created_at"     => $this->created_at?->format("Y-m-d h:i a"),
            "select_switch"  => $this->select_switch,
           
        ];
    }
}
