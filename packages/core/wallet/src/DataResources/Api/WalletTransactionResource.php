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
        return [
            'id'                     => $this->id,
            'amount'                 => (int) $this->amount,
            'before_charge'          => (int) $this->wallet_before,
            'after_charge'           => (int) $this->wallet_after,
            'type'                   => $this->type,
            'transaction_type'       => $this->transaction_type ?? $this->type,
            'transaction_type_text'  => trans($this->transaction_type ?? $this->type),
            'notes'                  => $this->notes,
            'add_date'               => $this->created_at->format('d-F-Y'),
            'expired_date'           => $this->expired_at ? date('d-m-Y H:i', strtotime($this->expired_at)) : null,
        ];
    }
}
