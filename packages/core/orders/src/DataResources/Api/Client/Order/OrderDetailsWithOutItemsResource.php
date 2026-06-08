<?php

namespace Core\Orders\DataResources\Api\Client\Order;

use Core\B2B\DataResources\Api\SimpleCompaniesResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\HomeMaidSale;
use Carbon\Carbon;
use Core\Categories\Models\CategoryOffer;
use Core\Coupons\DataResources\GiftApiResource;
use Core\Info\DataResources\Api\CityResource;
use Core\Info\DataResources\Api\DistrictResource;
use Core\Orders\Helpers\OrderHelper;
use Core\Users\DataResources\Api\SimpleUserResource;

class OrderDetailsWithOutItemsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $nationality      = OrderHelper::getLocalizedMoreData($this,'nationality_data');
        $contractDuration = OrderHelper::getLocalizedMoreData($this,'contract_duration_data');
        $workerCount      = OrderHelper::getLocalizedMoreData($this,'worker_count_data');
        $hoursCount       = OrderHelper::getLocalizedMoreData($this,'hours_count_data');
        $serviceData      = json_decode($this->moreDatas->where('key','service_data')->first()?->value);
        if(CategoryOffer::where('id',$serviceData?->id)->exists()){
            $workerCount = $serviceData?->workers_num;
            $hoursCount = $serviceData?->hours_num;
        }
        $technical                  = $this->orderRepresentatives->where('type','technical')->first();
        $couponMinmum               = json_decode($this->coupon_data)?->order_minimum ?? ($this->coupon?->minimum_price ?? 0);
        $orderWentBelowCopounLevel  = $couponMinmum > $this->order_price;
        $hasGfit                    = isset($this?->coupon?->gift);
        return [
            'id'                        => $this->id ,
            'reference_id'              => $this->reference_id,
            'type'                      => $this->type ,
            'status'                    => $this->status ,
            'city'                      => new CityResource($this->whenLoaded('city')),
            'district'                  => new DistrictResource($this->whenLoaded('district')),

            'client'                    => $this->b2b_type == 'company' ? new SimpleCompaniesResource($this->company) : new SimpleUserResource($this->client),

            'execute_day'               =>  $technical?->date ? Carbon::parse($technical?->date)->format('l')          : null,
            'execute_date'              =>  $technical?->date ? Carbon::parse($technical?->date)->format('Y-m-d')      : null,
            'execute_from_time'         =>  $technical?->time ? Carbon::parse($technical?->time)->format('H:i')        : null,
            'execute_to_time'           =>  $technical?->to_time ? Carbon::parse($technical?->to_time)->format('H:i')  : null,
            'created_at'                =>  $this->created_at->format('d-m-Y'),
            'category_type'             =>  $this->type ,
            'pay_type'                  =>  $this->pay_type ,
            'order_status_times'        =>  OrderHelper::orderStatusTimes($this)  ,
            'has_coupon'                =>  $this->coupon_id > 0,
            'order_price'               =>  (double)$this->order_price ,
            'total_price'               =>  (double)$this->total_price ,
            'returned_to_customer'      =>  (double)abs($this->transactions->where('amount','<',0)->sum('amount')) ,
            'delivery_price'            =>  (double)$this->delivery_price ,
            'coupon_discount_percentage'=>  ($this->order_price > 0) ?  ($this->total_coupon / $this->order_price)  * 100 : 0,
            'coupon_discount_total'     =>  $this->total_coupon,
            'coupon_discount_code'      =>  $this->coupon ? $this->coupon?->code : json_decode($this->coupon_data,true)['code'] ?? null,
            'coupon_discount_type'      =>  $this->coupon ? $this->coupon?->type : json_decode($this->coupon_data,true)['type'] ?? null,
            'online_payment_method'     =>  $this->online_payment_method,
            'lat'                       =>  $technical?->lat          ? $technical?->lat       :   '',
            'lng'                       =>  $technical?->lng          ? $technical?->lng       :  '',
            'nots'                      =>  $this->desc,


            'service'                   => OrderHelper::getLocalizedMoreData($this,'service_data'),
            'service_type'              => OrderHelper::getLocalizedMoreData($this,'service_type_data'),
            'uniform'                   => OrderHelper::getLocalizedMoreData($this,'uniform_data'),
            'nationality'               => $nationality,
            'worker_count'              => $workerCount,
            'contract_duration'         => $contractDuration,
            'hours_count'               => $hoursCount,
            'period'                    => OrderHelper::getLocalizedMoreData($this,'period_data'),
            'duration'                  => OrderHelper::getLocalizedMoreData($this,'duration_data'),
            'additional'                => OrderHelper::getLocalizedMoreData($this,'additional_data'),
            'days_per_week'             => $this->days_per_week,
            'days_per_week_names'       => $this->days_per_week_names  ? json_decode($this->days_per_week_names) : [],
            'days_per_month_dates'      => $this->days_per_month_dates  ? json_decode($this->days_per_month_dates) : [],
            'address_description'       => $this->addressDescription,
            'address_text'                  => $technical?->location,
            'hide_payment_option'       => $this->hide_payment_option,
            'coupon_minimum'            => $couponMinmum,
            'order_went_below_coupon_level' => $orderWentBelowCopounLevel,
            'note'                          =>  $this->note,

            'paid'                  => (double)$this->paid ,
            'card_amount_used'      => $this->card_amount_used,
            'cash_amount_used'      => $this->cash_amount_used,
            'wallet_used'           => $this->wallet_used,
            'wallet_amount_used'    => $this->wallet_amount_used,
            'points_used'           => $this->points_used,
            'points_amount_used'    => $this->points_amount_used,
            'has_been_refunded'     => $this->has_been_refunded,
            'order_transactions'    => OrderTransactionsResource::collection($this->whenLoaded('transactions')),
            'has_gift'              => $hasGfit,
            'gift'                  => $hasGfit ? GiftApiResource::make($this->coupon?->gift) : null,

        ];
    }

 
}
