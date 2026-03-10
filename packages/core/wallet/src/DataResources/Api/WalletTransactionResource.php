<?php

namespace Core\Wallet\DataResources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class WalletTransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'                => $this->id,
            'amount'            => (int) $this->amount ,
            'before_charge'     => (int) $this->wallet_before ,
            'after_charge'      => (int) $this->wallet_after ,
            'type'              =>  $this->transaction_type ?? $this->type,
            'add_date'          =>  $this->created_at->format('d-F-Y'),
            'expired_date'      =>  $this->expired_at ? date('d-m-Y H:i', strtotime($this->expired_at)) : null,
        ];
    }
}
