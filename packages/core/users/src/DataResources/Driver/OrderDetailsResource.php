<?php

namespace Core\Users\DataResources\Driver;

use Carbon\Carbon;
use Core\Info\DataResources\Api\CityResource;
use Core\Info\DataResources\Api\DistrictResource;
use Core\Orders\Models\OrderReport;
use Core\Orders\DataResources\Api\Client\Order\OrderTransactionsResource;
use Core\Users\DataResources\Api\SimpleUserResource;
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
        $is_report = false ;
        $report = OrderReport::where('order_id',$this->id)->where('user_id',auth('api')->id())->first();
        if($report){
            $is_report = true ;
        }
        $receiver = $this->orderRepresentatives->where('type','receiver')->first();
        $delivery = $this->orderRepresentatives->where('type','delivery')->first();
        $driverStatus = in_array($this->status, ['pending', 'receiving_driver_accepted', 'order_has_been_delivered_to_admin']) ? 'receiver' : 'delivery';
        $lat = null;
        $lng = null;
        $location = null;
        if($driverStatus == 'delivery'){
            $lat = $delivery->lat;
            $lng = $delivery->lng;
            $location = $delivery?->address?->city?->name . ' - ' . $delivery?->address?->district?->name . ' - ' . $delivery?->address?->location;
        }
        if($driverStatus == 'receiver'){
            $lat = $receiver->lat;
            $lng = $receiver->lng;
            $location = $receiver?->address?->city?->name . ' - ' . $receiver?->address?->district?->name . ' - ' . $receiver?->address?->location;
        }

        return [

            'id'                    => $this->id ,
            'reference_id'          => $this->reference_id,
            'type'                  => $this->type ,
            'client'                => new SimpleUserResource($this->client),
            'order_items'           => OrderItemResource::collection($this->items()->withTrashed()->get()),

            'day'                   => $delivery?->date         ? Carbon::parse($delivery?->date)->format('l') : Carbon::parse($receiver?->date)->format('l'),
            'date'                  => $delivery?->date         ? Carbon::parse($delivery?->date)->format('Y-m-d') : Carbon::parse($receiver?->date)->format('Y-m-d'),
            'from_time'             => $delivery?->time         ? Carbon::parse($delivery?->time)->format('H:i') : Carbon::parse($receiver?->time)->format('H:i'),
            'to_time'               => $delivery?->to_time      ? Carbon::parse($delivery->to_time)->addHour()->format('H:i') : Carbon::parse($receiver?->to_time)->addHour()->format('H:i'),

           

            'receiving_day'         => $receiver?->date         ? Carbon::parse($receiver?->date)->format('l') : Carbon::parse($delivery?->date)->format('l'),
            'receiving_date'        => $receiver?->date         ? Carbon::parse($receiver?->date)->format('Y-m-d') : Carbon::parse($delivery?->date)->format('Y-m-d'),
            'receiving_from_time'   => $receiver?->time         ? Carbon::parse($receiver?->time)->format('H:i') : Carbon::parse($delivery?->time)->format('H:i'),
            'receiving_to_time'     => $receiver?->to_time      ? Carbon::parse($receiver?->to_time)->format('H:i') : Carbon::parse($delivery?->to_time)->format('H:i'),
            
            'lat'                   => $lat,
            'lng'                   => $lng,
            'location'              => $location,

            'created_at'            => $this->created_at->format('d-m-Y'),
            'category'              => $this->items()->first()?->product()->first()?->category?->name ?? '',
            'category_type'         => $this->type ,
            'pay_type'              => $this->pay_type ,
            'total_price'           => (int)$this->total_price ,
            'returned_to_customer'  => (double)abs($this->transactions()->where('amount','<',0)->sum('amount')) ?? 0 ,
            'status'                => $this->status,
            'is_report'             => $is_report ,
            'pay_type_method'       => $this->pay_type,
            'online_payment_method' => $this->online_payment_method,
            'address_description'   => $this->addressDescription,
            'note'                  => $this->note,
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
