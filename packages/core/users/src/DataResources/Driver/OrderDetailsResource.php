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
        $report = $this->relationLoaded('reports')
            ? $this->reports->where('user_id', auth('api')->id())->first()
            : OrderReport::where('order_id', $this->id)->where('user_id', auth('api')->id())->first();
        if($report){
            $is_report = true ;
        }
        $receiver = $this->orderRepresentatives->where('type','receiver')->first();
        $delivery = $this->orderRepresentatives->where('type','delivery')->first();
        $technical = $this->orderRepresentatives->where('type','technical')->first();
        if($technical){
            $delivery = $technical;
        }

        $driverStatus = in_array($this->status, ['pending', 'receiving_driver_accepted', 'order_has_been_delivered_to_admin']) ? 'receiver' : 'delivery';
        if($this->type =='sales'){
            $driverStatus = 'delivery';
        }
        $lat = null;
        $lng = null;
        $location = null;
        if($driverStatus == 'delivery'){
            $lat = $delivery?->lat;
            $lng = $delivery?->lng;
            $location = $delivery?->address?->city?->name . ' - ' . $delivery?->address?->district?->name . ' - ' . $delivery?->address?->location;
        }
        if($driverStatus == 'receiver'){
            $lat = $receiver?->lat;
            $lng = $receiver?->lng;
            $location = $receiver?->address?->city?->name . ' - ' . $receiver?->address?->district?->name . ' - ' . $receiver?->address?->location;
        }

        return [

            'id'                    => $this->id ,
            'reference_id'          => $this->reference_id,
            'type'                  => $this->type ,
            'client'                => new SimpleUserResource($this->client),
            'order_items'           => OrderItemResource::collection($this->relationLoaded('items') ? $this->items : $this->items()->withTrashed()->get()),

            'day'                   => $delivery ? Carbon::parse($delivery?->date)->format('l') : Carbon::parse($receiver?->date)->format('l'),
            'date'                  => $delivery ? Carbon::parse($delivery?->date)->format('Y-m-d') : Carbon::parse($receiver?->date)->format('Y-m-d'),
            'from_time'             => $delivery ? Carbon::parse($delivery?->time)->format('H:i') : Carbon::parse($receiver?->time)->format('H:i'),
            'to_time'               => $delivery ? Carbon::parse($delivery?->to_time)->format('H:i') : Carbon::parse($receiver?->to_time)->format('H:i'),

           

            'receiving_day'         => $receiver ? Carbon::parse($receiver?->date)->format('l') : Carbon::parse($delivery?->date)->format('l'),
            'receiving_date'        => $receiver ? Carbon::parse($receiver?->date)->format('Y-m-d') : Carbon::parse($delivery?->date)->format('Y-m-d'),
            'receiving_from_time'   => $receiver ? Carbon::parse($receiver?->time)->format('H:i') : Carbon::parse($delivery?->time)->format('H:i'),
            'receiving_to_time'     => $receiver ? Carbon::parse($receiver?->to_time)->format('H:i') : Carbon::parse($delivery?->to_time)->format('H:i'),
            
            'lat'                   => $lat,
            'lng'                   => $lng,
            'location'              => $location,
            'building_image'        => \Core\MediaCenter\Helpers\MediaCenterHelper::getImagesUrl($delivery?->address?->image ?? $receiver?->address?->image),

            'created_at'            => $this->created_at->format('d-m-Y'),
            'category'              => ($this->relationLoaded('items') ? $this->items->first() : $this->items()->first())?->product?->category?->name ?? '',
            'category_type'         => $this->type ,
            'pay_type'              => $this->pay_type ,
            'total_price'           => (int)$this->total_price ,
            'returned_to_customer'  => (double)abs(($this->relationLoaded('transactions') ? $this->transactions->where('amount', '<', 0)->sum('amount') : $this->transactions()->where('amount', '<', 0)->sum('amount')) ?? 0),
            'status'                => $this->status,
            'is_report'             => $is_report ,
            'pay_type_method'       => $this->pay_type,
            'online_payment_method' => $this->online_payment_method,
            'address_text'   => $this->addressDescription,
            'location'              => $delivery?->location,
            'note'                  => $this->note,
            'perfume'               => $this->perfume,
            'starch_level'          => $this->starch_level,
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
