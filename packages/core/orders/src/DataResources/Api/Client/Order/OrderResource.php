<?php

namespace Core\Orders\DataResources\Api\Client\Order;

use Carbon\Carbon;
use Core\Coupons\DataResources\GiftApiResource;
use Core\Info\DataResources\Api\CityResource;
use Core\Info\DataResources\Api\DistrictResource;
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
        $technical = $this->orderRepresentatives->where('type','technical')->first();
        if($technical){
            $delivery = $technical;
        }

        $couponMinmum = json_decode($this->coupon_data)?->order_minimum ?? ($this->coupon?->minimum_price ?? 0);
        $orderWentBelowCopounLevel = $couponMinmum > $this->order_price;
        $hasGfit = isset($this?->coupon?->gift);
        return [
            'id'                    => $this->id ,
            'reference_id'          => $this->reference_id ,
            'type'                  => $this->type,
            'status'                => $this->status,
            'order_items'           => OrderItemResource::collection($this->relationLoaded('items') ? $this->items->where('final_delete', false) : $this->items()->withTrashed()->where('final_delete', false)->get()),
            'city'                  => new CityResource($this->whenLoaded('city')),
            'district'              => new DistrictResource($this->whenLoaded('district')),
            'created_at'            => $this->created_at?->format('d-m-Y'),
            'category'              => ($this->relationLoaded('items') ? $this->items->where('final_delete', false)->first() : $this->items()->where('final_delete', false)->first())?->product?->category?->name ?? '',
            'category_type'         => $this->type,

            'day'                   => $delivery ? Carbon::parse($delivery?->date)->format('l') : Carbon::parse($receiver?->date)->format('l'),
            'date'                  => $delivery ? Carbon::parse($delivery?->date)->format('Y-m-d') : Carbon::parse($receiver?->date)->format('Y-m-d'),
            'from_time'             => $delivery ? Carbon::parse($delivery?->time)->format('H:i') : Carbon::parse($receiver?->time)->format('H:i'),
            'to_time'               => $delivery ? Carbon::parse($delivery?->to_time)->format('H:i') : Carbon::parse($receiver?->to_time)->format('H:i'),
           
            'receiving_day'         => $receiver ? Carbon::parse($receiver?->date)->format('l') : Carbon::parse($delivery?->date)->format('l'),
            'receiving_date'        => $receiver ? Carbon::parse($receiver?->date)->format('Y-m-d') : Carbon::parse($delivery?->date)->format('Y-m-d'),
            'receiving_from_time'   => $receiver ? Carbon::parse($receiver?->time)->format('H:i') : Carbon::parse($delivery?->time)->format('H:i'),
            'receiving_to_time'     => $receiver ? Carbon::parse($receiver?->to_time)->format('H:i') : Carbon::parse($delivery?->to_time)->format('H:i'),
            'total_price'           => (double)$this->total_price,
            'online_payment_method' => $this->online_payment_method,
            'address_description'   => $this->addressDescription,
            'address_text'              => $delivery?->location,
            'hide_payment_option'   => $this->hide_payment_option,
            'coupon_minimum'        => $couponMinmum,
            
            'order_went_below_coupon_level' => $orderWentBelowCopounLevel,
            'coupon_discount_percentage'=>  ($this->order_price > 0) ?  ($this->total_coupon / $this->order_price)  * 100 : 0,
            'coupon_discount_total'     =>  $this->total_coupon,
            'coupon_discount_code'      =>  $this->coupon ? $this->coupon?->code : json_decode($this->coupon_data,true)['code'] ?? null,
            'coupon_discount_type'      =>  $this->coupon ? $this->coupon?->type : json_decode($this->coupon_data,true)['type'] ?? null,
            'note'                      =>  $this->note,
            'order_for'                 =>  $this->order_for,
            'recipient_name'            =>  $this->recipient_name,
            'recipient_phone'           =>  $this->recipient_phone,
            'request_address'           =>  $this->request_address,
            'hide_identity'             =>  $this->hide_identity,
            'perfume'                   =>  $this->perfume,
            'starch_level'              =>  $this->starch_level,

            'paid'                  => (double)$this->paid,
            'card_amount_used'      => $this->card_amount_used,
            'cash_amount_used'      => $this->cash_amount_used,
            'wallet_used'           => $this->wallet_used,
            'wallet_amount_used'    => $this->wallet_amount_used,
            'points_used'           => $this->points_used,
            'points_amount_used'    => $this->points_amount_used,
            'has_been_refunded'     => $this->has_been_refunded,
            'order_transactions'        => OrderTransactionsResource::collection($this->whenLoaded('transactions')),
            'has_gift'              => $hasGfit,
            'gift'                  => $hasGfit ? GiftApiResource::make($this->coupon?->gift) : null,
        ];
    }
}
