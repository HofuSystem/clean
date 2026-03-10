<?php

namespace Core\Orders\DataResources\Api\Client\Order;

use Carbon\Carbon;
use Core\Info\DataResources\Api\CityResource;
use Core\Info\DataResources\Api\DistrictResource;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderWithOutItemsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $technical = $this->orderRepresentatives->where('type','technical')->first();
        $couponMinmum = json_decode($this->coupon_data)?->order_minimum ?? ($this->coupon?->minimum_price ?? 0);
        $orderWentBelowCopounLevel = $couponMinmum > $this->order_price;
        return [
            'id'            => $this->id ,
            'reference_id'  => $this->reference_id ,
            'type'          => $this->type,
            'status'        => $this->status,
            'city'          =>  new CityResource($this->whenLoaded('city')),
            'district'      =>  new DistrictResource($this->whenLoaded('distinct')),
            'created_at'    => $this->created_at?->format('d-m-Y'),
            'category_type' => $this->type,
            'day'           => $technical?->date ? Carbon::parse($technical?->date)->format('l')          : null,
            'date'          => $technical?->date ? Carbon::parse($technical?->date)->format('Y-m-d')      : null,
            'from_time'     => $technical?->time ? Carbon::parse($technical?->time)->format('H:i')        : null,
            'to_time'       => $technical?->to_time ? Carbon::parse($technical?->to_time)->format('H:i')  : null,
            'total_price'   => (double)$this->total_price ,
            'address_description'   => $this->addressDescription,
            'hide_payment_option'   => $this->hide_payment_option,
            'coupon_minimum'        => $couponMinmum,
            'order_went_below_coupon_level' => $orderWentBelowCopounLevel,
            'note'                          =>  $this->note,

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
