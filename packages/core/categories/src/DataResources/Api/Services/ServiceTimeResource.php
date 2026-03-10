<?php

namespace Core\Categories\DataResources\Api\Services;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceTimeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $is_available = true ;
        // $order_count = \App\Models\Order::where('time_id' , $this->id)->count();
        // if($order_count >= $this->order_count ){
        //     $is_available = false ;
        // }

        $time_ago = false ;
       
        if(Carbon::parse($this->to)->format('H:i') < now()->format('H:i')){
            $time_ago = true ;
        }
        if(Carbon::parse($this->from)->format('H:i') < now()->format('H:i')){
            $time_ago = true ;
        }

        return [
            'id'            => $this->id,
            'from'          => Carbon::parse($this->from)->format('H:i'),
            'to'            => Carbon::parse($this->to)->format('H:i'),
            'is_available'  => $is_available,
            'time_ago'      => $time_ago ,
        ];
    }
}
