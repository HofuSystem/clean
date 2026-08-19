<?php

namespace Core\Wallet\DataResources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class WalletTransactionResource extends JsonResource
{
    /**
     * @return array
     */
    public function toArray($request)
    {
        $orderNumber = $this->order?->reference_id ?? $this->order_id;
        $type = $this->transaction_type ?? $this->type;
        $expiryText = $this->expired_at ? trans('wallet_msg_expires_at', ['date' => date('d-m-Y', strtotime($this->expired_at))]) : null;

        return [
            'id'                     => $this->id,
            'amount'                 => (int) $this->amount,
            'before_charge'          => (int) $this->wallet_before,
            'after_charge'           => (int) $this->wallet_after,
            'type'                   => $this->type,
            'transaction_type'       => $type,
            'transaction_type_text'  => $this->formatTransactionTypeText($type, $orderNumber),
            'notes'                  => $this->notes,
            'order_id'               => $orderNumber,
            'expiry_text'            => $expiryText,
            'add_date'               => $this->created_at->format('d-F-Y'),
            'expired_date'           => $this->expired_at ? date('d-m-Y H:i', strtotime($this->expired_at)) : null,
        ];
    }

    /**
     * Format a descriptive user-friendly message based on transaction type and order.
     */
    protected function formatTransactionTypeText($type, $orderNumber = null): string
    {
        if ($orderNumber) {
            return match ($type) {
                'order_payment'    => trans('wallet_msg_order_payment', ['order' => $orderNumber]),
                'remaining_amount' => trans('wallet_msg_remaining_amount', ['order' => $orderNumber]),
                'compensation_add' => trans('wallet_msg_compensation', ['order' => $orderNumber]),
                'reward'           => trans('wallet_msg_reward_order', ['order' => $orderNumber]),
                'cashback'         => trans('wallet_msg_cashback_order', ['order' => $orderNumber]),
                default            => trans($type) . " ({$orderNumber})",
            };
        }

        return trans($type);
    }
}
