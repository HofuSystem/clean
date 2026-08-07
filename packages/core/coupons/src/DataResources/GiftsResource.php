<?php

namespace Core\Coupons\DataResources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Core\Admin\Helpers\DashboardDataTableFormatter;

class GiftsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id"            => $this->id,
            "title"         => DashboardDataTableFormatter::text($this->title),
            "coupon_code"   => DashboardDataTableFormatter::text($this->coupon_code),
            "order_type"    => DashboardDataTableFormatter::text(trans($this->order_type ?? '')),
            "from"          => DashboardDataTableFormatter::text($this->from),
            "to"            => DashboardDataTableFormatter::text($this->to),
            "value"         => DashboardDataTableFormatter::text($this->value),
            "type"          => DashboardDataTableFormatter::text(trans($this->type ?? '')),
            "status"        => DashboardDataTableFormatter::text(trans($this->status)),

            "actions"       => $this->actions,
            "select_switch" => $this->select_switch,
        ];
    }
}
