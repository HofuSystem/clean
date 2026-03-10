<?php

namespace Core\Users\DataResources\Driver;

use Carbon\Carbon;
use Core\Orders\DataResources\Api\Client\Order\OrderTransactionsResource;
use Core\Users\DataResources\Api\SimpleUserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    { 
        $receiver = $this->orderRepresentatives->where('type','receiver')->first();
        $delivery = $this->orderRepresentatives->where('type','delivery')->first();
        $driverStatus = in_array($this->status, ['pending', 'receiving_driver_accepted', 'order_has_been_delivered_to_admin']) ? 'receiver' : 'delivery';
       
        $location = null;
        if($driverStatus == 'delivery'){
            $location = $delivery?->address?->city?->name . ' - ' . $delivery?->address?->district?->name . ' - ' . $delivery?->address?->location;
        }
        if($driverStatus == 'receiver'){
            $location = $receiver?->address?->city?->name . ' - ' . $receiver?->address?->district?->name . ' - ' . $receiver?->address?->location;
        }
        $couponMinmum = json_decode($this->coupon_data)?->order_minimum ?? ($this->coupon?->minimum_price ?? 0);
        $orderWentBelowCopounLevel = $couponMinmum > $this->order_price;
        return [
            'id'                    => $this->id ,
            'reference_id'          => $this->reference_id ,
            'type'                  => $this->type ,
            'client'                => new SimpleUserResource($this->client),
            
            'day'                   => $delivery?->date         ? Carbon::parse($delivery?->date)->format('l') : Carbon::parse($receiver?->date)->format('l'),
            'date'                  => $delivery?->date         ? Carbon::parse($delivery?->date)->format('Y-m-d') : Carbon::parse($receiver?->date)->format('Y-m-d'),
            'from_time'             => $delivery?->time         ? Carbon::parse($delivery?->time)->format('H:i') : Carbon::parse($receiver?->time)->format('H:i'),
            'to_time'               => $delivery?->to_time      ? Carbon::parse($delivery->to_time)->addHour()->format('H:i') : Carbon::parse($receiver?->to_time)->addHour()->format('H:i'),
            
            'receiving_day'         => $receiver?->date         ? Carbon::parse($receiver?->date)->format('l') : Carbon::parse($delivery?->date)->format('l'),
            'receiving_date'        => $receiver?->date         ? Carbon::parse($receiver?->date)->format('Y-m-d') : Carbon::parse($delivery?->date)->format('Y-m-d'),
            'receiving_from_time'   => $receiver?->time         ? Carbon::parse($receiver?->time)->format('H:i') : Carbon::parse($delivery?->time)->format('H:i'),
            'receiving_to_time'     => $receiver?->to_time         ? Carbon::parse($receiver?->to_time)->format('H:i') : Carbon::parse($delivery?->to_time)->format('H:i'),

            'created_at'            => $this->created_at->format('d-m-Y'),
            'category'              => $this->items()->first()?->product()->first()?->category?->name ?? '',
            'category_type'         => $this->type ,
            'status'                => $this->status,
            'address_description'   => $this->addressDescription,
            'hide_payment_option'   => $this->hide_payment_option,
            'coupon_minimum'        => $couponMinmum,
            'order_went_below_coupon_level' => $orderWentBelowCopounLevel,
            'note'                          =>  $this->note,
            'location'              => $location,
            'total_price'           => $this->total_price,
            
            'paid'                  => (double)$this->paid,
            'card_amount_used'      => $this->card_amount_used,
            'cash_amount_used'      => $this->cash_amount_used,
            'wallet_used'           => $this->wallet_used,
            'wallet_amount_used'    => $this->wallet_amount_used,
            'points_used'           => $this->points_used,
            'points_amount_used'    => $this->points_amount_used,
            'has_been_refunded'     => $this->has_been_refunded,
            'order_transactions'    => OrderTransactionsResource::collection($this->whenLoaded('transactions')),
        ];
    }
}
