<?php

namespace Core\Orders\DataResources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Core\Admin\Helpers\DashboardDataTableFormatter;
use Core\Orders\Helpers\OrderHelper;

class OrdersResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {

        $data =  [

            "id"                  => $this->id,
            "reference_id"        => DashboardDataTableFormatter::text($this->reference_id),
            "type"                => DashboardDataTableFormatter::text($this->type),
            "status"              => DashboardDataTableFormatter::text($this->status),
            "client_id"           => DashboardDataTableFormatter::text($this->client?->fullname),
            "operator_id"         => DashboardDataTableFormatter::relations($this->operator,"fullname","dashboard.users.show"),
            "city_id"             => DashboardDataTableFormatter::text($this->city?->name ?? $this->client?->profile?->city?->name),
            "district_id"         => DashboardDataTableFormatter::text($this->district?->name ?? $this->client?->profile?->district?->name),
            "pay_type"            => DashboardDataTableFormatter::text($this->pay_type),
            "note"                => DashboardDataTableFormatter::text($this->note),
            "coupon_id"           => DashboardDataTableFormatter::relations($this->coupon,"code","dashboard.coupons.show"),
            "total_price"         => DashboardDataTableFormatter::text($this->total_price),
            "total_provider_invoice" => DashboardDataTableFormatter::text($this->total_provider_invoice),
            "paid"                => DashboardDataTableFormatter::text($this->paid),
            "is_admin_accepted"   => DashboardDataTableFormatter::checkbox($this->is_admin_accepted),
            "admin_cancel_reason" => DashboardDataTableFormatter::text($this->admin_cancel_reason),
            "wallet_used"         => DashboardDataTableFormatter::checkbox($this->wallet_used),
            "wallet_amount_used"  => DashboardDataTableFormatter::text($this->wallet_amount_used),
            "phone"               => $this->client?->phone,
            "created_at"          => \Carbon\Carbon::parse($this->created_at)->format('Y-m-d'),
            "actions"             => $this->actions,
            "select_switch"       => $this->select_switch,

        ];
        

        $data['status'] = OrderHelper::getStatusColor($this->status);

        $payType    = trans($this->pay_type);
        $payTitle   = $this->online_payment_method ?? $payType;
        if ($this->pay_type=='cash')
            $payType = '<span class="btn-operation" > <i class="fas fa-money-bill-wave"></i> <span  class="p-2">'.$payTitle.'</span></span>';
        elseif ($this->pay_type=='card')
            $payType = '<span class="btn-operation" > <i class="fas fa-credit-card"></i><span  class="p-2">'.$payTitle.'</span></span>';
        elseif ($this->pay_type=='wallet')
           $payType =  '<span class="btn-operation" > <i class="fas fa-wallet"></i><span  class="p-2">'.$payTitle.'</span></span>';

        $data['pay_type'] = $payType;

        $technical  = $this->resource->orderRepresentatives()->where('type','technical')->latest()->first();
        $data['technical_date']     = $technical?->date;
        $data['technical_time']     = $technical?->time ? Carbon::parse($technical->time)->format('h:i A') . ' : ' . Carbon::parse($technical->to_time)->format('h:i A') : null;

        $delivery   = $this->resource->orderRepresentatives()->where('type','delivery')->latest()->first();
        $data['delivery_date']      = $delivery?->date;
        $data['delivery_time']      = $delivery?->time ? Carbon::parse($delivery->time)->format('h:i A') . ' : ' . Carbon::parse($delivery->to_time)->format('h:i A') : null;

        $receiver   = $this->resource->orderRepresentatives()->where('type','receiver')->latest()->first();
        $data['receiver_date']     = $receiver?->date;
        $data['receiver_time']     = $receiver?->time ? Carbon::parse($receiver->time)->format('h:i A') . ' : ' . Carbon::parse($receiver->to_time)->format('h:i A') : null;


        return $data;
    }
}
