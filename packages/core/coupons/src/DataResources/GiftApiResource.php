<?php

namespace Core\Coupons\DataResources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GiftApiResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id"            => $this->id,
            "title"         => $this->title,
            "intro"         => $this->intro,
            "coupon_code"   => $this->coupon_code,
            "coupon_id"     => $this->coupon_id,
            "value"         => $this->value,
            "type"          => $this->type,
            "max_value"     => $this->max_value,
        ];
    }
}
