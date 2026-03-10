<?php

namespace Core\Notification\DataResources\Api;

use Carbon\Carbon;
use Core\MediaCenter\Helpers\MediaCenterHelper;
use Core\Orders\Models\Order;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    { 
        $payload    = json_decode($this->payload,true);
        $key        = $payload['key'] ?? null;
        if($key == "order"){
            $order_id   = $this->order_id ?? $payload['key_id'] ?? null;
        }else{
            $order_id   = $this->order_id;
        }
        $order = Order::find($order_id);
        return [

            "id"            => $this->id,
            "types"         => $this->types,
            "for"           => $this->for,
            "title"         => $this->title,
            "body"          => $this->body,
            "media"         => MediaCenterHelper::getUrls($this->media),
            "sender_id"     => $this?->sender?->name,
            "created_at"    => Carbon::parse($this->created_at)->diffForHumans(),
            "payload"       => json_decode($this->payload),
            "notify_type"   => $key ?? null,
            "order_id"      => $order?->id,
            "reference_id"  => $order?->reference_id,
        
        ];
    }



}
