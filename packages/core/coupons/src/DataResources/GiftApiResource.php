<?php

namespace Core\Coupons\DataResources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GiftApiResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $user = $request->user('sanctum') ?? auth()->user();
        $isInMyGifts = $user ? $this->users()->where('users.id', $user->id)->exists() : false;

        return [
            "id"             => $this->id,
            "title"          => $this->title,
            "intro"          => $this->intro,
            "btn_text"       => $this->btn_text,
            "status"         => $this->status,
            "coupon_code"    => $this->coupon_code,
            "order_type"     => $this->order_type ? explode(',',$this->order_type) : [],
            "coupon_id"      => $this->coupon_id,
            "value"          => $this->value,
            "type"           => $this->type,
            "max_value"      => $this->max_value,
            "is_in_my_gifts" => $isInMyGifts,
        ];
    }
}
