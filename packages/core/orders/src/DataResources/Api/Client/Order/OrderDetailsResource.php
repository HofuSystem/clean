<?php

namespace Core\Orders\DataResources\Api\Client\Order;

use Carbon\Carbon;
use Core\Info\DataResources\Api\CityResource;
use Core\Info\DataResources\Api\DistrictResource;
use Core\Orders\Helpers\OrderHelper;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderDetailsResource extends JsonResource
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
        $couponMinmum = json_decode($this->coupon_data)?->order_minimum ?? ($this->coupon?->minimum_price ?? 0);
        $orderWentBelowCopounLevel = $couponMinmum > $this->order_price;
        return [
            'id'                            =>  $this->id,
            'reference_id'                  =>  $this->reference_id,
            'type'                          =>  $this->type,
            'status'                        =>  $this->status,
            'order_items'                   =>  OrderItemResource::collection($this->items()->withTrashed()->where('final_delete', false)->get()),
            'city'                          =>  new CityResource($this->whenLoaded('city')),
            'district'                      =>  new DistrictResource($this->whenLoaded('distinct')),

            'day'                           => $delivery?->date         ? Carbon::parse($delivery?->date)->format('l')                  : Carbon::parse($receiver?->date)->format('l'),
            'date'                          => $delivery?->date         ? Carbon::parse($delivery?->date)->format('Y-m-d')              : Carbon::parse($receiver?->date)->format('Y-m-d'),
            'from_time'                     => $delivery?->time         ? Carbon::parse($delivery?->time)->format('H:i')                : Carbon::parse($receiver?->time)->format('H:i'),
            'to_time'                       => $delivery?->to_time      ? Carbon::parse($delivery->to_time)->addHour()->format('H:i')   : Carbon::parse($receiver?->to_time)->addHour()->format('H:i'),

            'receiving_day'                 => $receiver?->date         ? Carbon::parse($receiver?->date)->format('l')      : Carbon::parse($delivery?->date)->format('l'),
            'receiving_date'                => $receiver?->date         ? Carbon::parse($receiver?->date)->format('Y-m-d')  : Carbon::parse($delivery?->date)->format('Y-m-d'),
            'receiving_from_time'           => $receiver?->time         ? Carbon::parse($receiver?->time)->format('H:i')  : Carbon::parse($delivery?->time)->format('H:i'),
            'receiving_to_time'             => $receiver?->to_time      ? Carbon::parse($receiver?->to_time)->format('H:i')  : Carbon::parse($delivery?->to_time)->format('H:i'),

            'created_at'                    => $this->created_at?->format('d-m-Y'),
            'category'                      => $this->items()->where('final_delete', false)->first()?->product()->first()?->category?->name ?? '',
            'category_type'                 => $this->type,
            'pay_type'                      => $this->pay_type,
            'order_status_times'            => $this->status == 'rejected' || $this->status == 'canceled' ?  [] : OrderHelper::orderStatusTimes($this),
            'has_coupon'                    => $this->coupon_id > 0,
            'order_price'                   => (float)$this->order_price,
            'total_price'                   => (float)$this->total_price,
            'returned_to_customer'          => (double)abs($this->transactions()->where('amount','<',0)->sum('amount')) ,
            'delivery_price'                => (float)$this->delivery_price,
            'coupon_discount_percentage'    => ($this->order_price > 0) ?  ($this->total_coupon / $this->order_price)  * 100 : 0,
            'coupon_discount_total'         => $this->total_coupon,
            'coupon_discount_code'          => $this->coupon ? $this->coupon?->code : json_decode($this->coupon_data,true)['code'] ?? null,
            'coupon_discount_type'          => $this->coupon ? $this->coupon?->type : json_decode($this->coupon_data,true)['type'] ?? null,
            'online_payment_method'         => $this->online_payment_method,
            'nots'                          => $this->desc,
            'address_description'           => $this->addressDescription,
            'hide_payment_option'           => $this->hide_payment_option,
            'coupon_minimum'                => $couponMinmum,
            'order_went_below_coupon_level' => $orderWentBelowCopounLevel,
            'note'                          => $this->note,

            'paid'                  => (float)$this->paid,
            'card_amount_used'      => $this->card_amount_used,
            'cash_amount_used'      => $this->cash_amount_used,
            'wallet_used'           => $this->wallet_used,
            'wallet_amount_used'    => $this->wallet_amount_used,
            'points_used'           => $this->points_used,
            'points_amount_used'    => $this->points_amount_used,
            'has_been_refunded'     => $this->has_been_refunded,
            'order_transactions'            => OrderTransactionsResource::collection($this->whenLoaded('transactions')),
        ];
    }
}
