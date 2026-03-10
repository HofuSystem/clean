<?php

namespace Core\Orders\Models;

use Core\Orders\Helpers\OrderHelper;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Core\Users\Models\User;
use Core\Coupons\Models\Coupon;
use Core\Orders\Observers\OrderObserver;

use Core\Settings\Models\CoreModel;
use Carbon\Carbon;
use App\Observers\GlobalModelObserver;
use Core\Info\Models\City;
use Core\Info\Models\District;
use Core\Orders\Scopes\UnderOperationOrdersScope;
use Core\Settings\Services\SettingsService;
use Core\Wallet\Models\WalletTransaction;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Auth;

#[ObservedBy([OrderObserver::class])]
#[ObservedBy([GlobalModelObserver::class])]

class Order extends CoreModel
{

    protected $table             = 'orders';
    protected $fillable          = ['reference_id', 'type', 'status', 'client_id', 'operator_id', 'city_id', 'district_id', 'pay_type', 'transaction_id', 'order_status_times', 'days_per_week', 'days_per_week_names', 'days_per_month_dates', 'note', 'online_payment_method', 'coupon_id', 'coupon_data', 'total_coupon', 'order_price', 'delivery_price', 'total_price', 'total_cost', 'total_provider_invoice', 'paid', 'is_admin_accepted', 'admin_cancel_reason', 'wallet_used', 'cashback', 'wallet_amount_used', 'points_used','points_amount', 'points_amount_used', 'feedback_requested_at', 'creator_id', 'updater_id', 'hide_payment_option','original_products_total','cash_amount_used','card_amount_used','has_been_refunded'];
    protected $guarded           = [];


    //start Scopes
    function scopeAnalysis($query, $cityId, $from, $to,$status = null)
    {
        $query->when($cityId, function ($query) use ($cityId) {
            $query->where('orders.city_id', $cityId);
        })->when($from, function ($query) use ($from) {
            $query->whereDate('orders.created_at', '>=', Carbon::parse($from));
        })->when($to, function ($query) use ($to) {
            $query->whereDate('orders.created_at', '<=', Carbon::parse($to));
        })->when($status, function ($query) use ($status) {
            if (is_array($status)) {
                $query->whereIn('orders.status', $status);
            } else {
                $query->where('orders.status', $status);
            }
        });
    }
    function scopeAnalysisRepresentatives($query, $type, $from, $to)
    {
      $query->when($type, function ($query) use ($type) {
            if (is_array($type)) {
                $query->whereIn('order_representatives.type', $type);
            } else {
                $query->where('order_representatives.type', $type);
            }
        })->when($from, function ($query) use ($from) {
            $query->whereDate('order_representatives.date', '>=', Carbon::parse($from));
        })->when($to, function ($query) use ($to) {
            $query->whereDate('order_representatives.date', '<=', Carbon::parse($to));
        });
    }
    function scopeUnderMyControl($query)
    {
        return $query->when(Request::is('*company*'), function ($query) {
            $query->whereHas('client.roles',function($operatorsQuery){
                $operatorsQuery->where('name','company');
            });
        });
    }
    function scopeSearch($query)
    {
        // Join with order_representatives to get receiver_date and delivery_date
        $query->leftJoin('order_representatives as or_receiver', function($join) {
            $join->on('orders.id', '=', 'or_receiver.order_id')
                 ->where('or_receiver.type', '=', 'receiver');
        })
        ->leftJoin('order_representatives as or_delivery', function($join) {
            $join->on('orders.id', '=', 'or_delivery.order_id')
                 ->where('or_delivery.type', '=', 'delivery');
        })
        ->select('orders.*', 'or_receiver.date as receiver_date', 'or_delivery.date as delivery_date');

        //filter text on  reference_id
        if ((request()->has("filters.reference_id")) and !empty(request("filters.reference_id"))) {
            $query->where("orders.reference_id", "LIKE", "%" . request("filters.reference_id") . "%");
        }

        //filter select on  type
        if ((request()->has("filters.type")) and !empty(request("filters.type"))) {
            $query->where("orders.type", request("filters.type"));
        }

        //filter select on  status
        if ((request()->has("filters.status")) and !empty(request("filters.status"))) {
            if (is_array(request("filters.status"))) {
                if(in_array('received',request("filters.status"))){
                    $query->whereNotIn('orders.status', ['pending', 'canceled', 'finished','failed_payment','pending_payment','cancel_payment']);
                }else{
                    $query->whereIn("orders.status", request("filters.status"));
                }
            } else {
                if (request("filters.status") == 'received') {
                    $query->whereNotIn('orders.status', ['pending', 'canceled', 'finished','failed_payment','pending_payment','cancel_payment']);
                } else {
                    $query->where("orders.status", request("filters.status"));
                }
            }
        }

        if ((request()->has("filters.operator_id")) and !empty(request("filters.operator_id"))) {
            $query->whereHas('operator', function ($operatorsQuery) {
                $operatorsQuery->where('id', request("filters.operator_id"));
            });
        }
        if ((request()->has("filters.representative_id")) and !empty(request("filters.representative_id"))) {
            $query->whereHas('orderRepresentatives.representative', function ($representativeQuery) {
                $representativeQuery->where('id', request("filters.representative_id"));
            });
        }
        
    
        //filter select on  client
        if ((request()->has("filters.client_id")) and !empty(request("filters.client_id"))) {
            $query->whereRelation("client", "id", request("filters.client_id"));
        }
        if ((request()->has("filters.city_id")) and !empty(request("filters.city_id"))) {
            $query->whereRelation("city", "id", request("filters.city_id"));
        }

        //filter select on  pay_type
        if ((request()->has("filters.pay_type")) and !empty(request("filters.pay_type"))) {
            $query->where("orders.pay_type", request("filters.pay_type"));
        }

        //filter text on  transaction_id
        if ((request()->has("filters.transaction_id")) and !empty(request("filters.transaction_id"))) {
            $query->where("orders.transaction_id", "LIKE", "%" . request("filters.transaction_id") . "%");
        }

        //filter text on  note
        if ((request()->has("filters.note")) and !empty(request("filters.note"))) {
            $query->where("orders.note", "LIKE", "%" . request("filters.note") . "%");
        }

        //filter select on  coupon
        if ((request()->has("filters.coupon_id")) and !empty(request("filters.coupon_id"))) {
            $query->whereRelation("coupon", "id", request("filters.coupon_id"));
        }

        //filter by number on  order_price
        if ((request()->has("filters.order_price")) and !empty(request("filters.order_price"))) {
            $query->where("orders.order_price", request("filters.order_price"));
        }

        //filter by number on  delivery_price
        if ((request()->has("filters.delivery_price")) and !empty(request("filters.delivery_price"))) {
            $query->where("orders.delivery_price", request("filters.delivery_price"));
        }

        //filter by number on  total_price
        if ((request()->has("filters.total_price")) and !empty(request("filters.total_price"))) {
            $query->where("orders.total_price", request("filters.total_price"));
        }

        //filter by number on  paid
        if ((request()->has("filters.paid")) and !empty(request("filters.paid"))) {
            $query->where("orders.paid", request("filters.paid"));
        }

        //filter text on  admin_cancel_reason
        if ((request()->has("filters.admin_cancel_reason")) and !empty(request("filters.admin_cancel_reason"))) {
            $query->where("orders.admin_cancel_reason", "LIKE", "%" . request("filters.admin_cancel_reason") . "%");
        }

        //filter by number on  wallet_amount_used
        if ((request()->has("filters.wallet_amount_used")) and !empty(request("filters.wallet_amount_used"))) {
            $query->where("orders.wallet_amount_used", request("filters.wallet_amount_used"));
        }

        
        $repType = request("filters.rep_type");

        //filter date on  from_date
        if ((request()->has("filters.from_date")) and !empty(request("filters.from_date"))) {
            $query->whereHas('orderRepresentatives', function ($orderRepresentativesQuery) use ($repType) {
                $orderRepresentativesQuery->whereDate('date', '>=', request("filters.from_date"))
                ->when($repType, function ($query) use ($repType) {
                    $query->where('type', $repType);
                });
            });
        }
        if ((request()->has("filters.to_date")) and !empty(request("filters.to_date"))) {
            $query->whereHas('orderRepresentatives', function ($orderRepresentativesQuery) use ($repType) {
                $orderRepresentativesQuery->whereDate('date', '<=', request("filters.to_date"))
                ->when($repType, function ($query) use ($repType) {
                    $query->where('type', $repType);
                });
            });
        }
        if ((request()->has("filters.from_time")) and !empty(request("filters.from_time"))) {
            $query->whereHas('orderRepresentatives', function ($orderRepresentativesQuery) use ($repType) {
                $orderRepresentativesQuery->where('time', '>=', request("filters.from_time"))
                ->when($repType, function ($query) use ($repType) {
                    $query->where('type', $repType);
                });
            });
        }
        if ((request()->has("filters.to_time")) and !empty(request("filters.to_time"))) {
            $query->whereHas('orderRepresentatives', function ($orderRepresentativesQuery) use ($repType) {
                $orderRepresentativesQuery->where('time', '<=', request("filters.to_time"))
                ->when($repType, function ($query) use ($repType) {
                    $query->where('type', $repType);
                });
            });
        }

        //filter date on  created_at
        if ((request()->has("filters.from_created_at")) and !empty(request("filters.from_created_at"))) {
            $query->whereDate("orders.created_at", ">=", Carbon::parse(request("filters.from_created_at")));
        }

        if ((request()->has("filters.to_created_at")) and !empty(request("filters.to_created_at"))) {
            $query->whereDate("orders.created_at", "<=", Carbon::parse(request("filters.to_created_at")));
        }

        //filter date on  updated_at
        if ((request()->has("filters.from_updated_at")) and !empty(request("filters.from_updated_at"))) {
            $query->whereDate("orders.updated_at", ">=", Carbon::parse(request("filters.from_updated_at")));
        }

        if ((request()->has("filters.to_updated_at")) and !empty(request("filters.to_updated_at"))) {
            $query->whereDate("orders.updated_at", "<=", Carbon::parse(request("filters.to_updated_at")));
        }
        if ((request()->has("filters.phone")) and !empty(request("filters.phone"))) {
            $query->whereHas("client", function ($clientQuery) {
                $clientQuery->where('phone', 'LIKE', '%' . request("filters.phone") . '%');
            });
        }
        if(request()->has("forCompany") and request("forCompany") == true){
            $query->whereHas("client.roles",function($clientQuery){
                $clientQuery->where("name","company");
            });
        }
        if (request()->has("filters.client_id") && !empty(request("filters.client_id"))) {
            return;
        }
        $cancelTesting = request()->has("filters.cancel_testing") ? request("filters.cancel_testing") : null;
        if(!$cancelTesting){
            $query->testAccounts((request()->has("filters.testAccount")) and !empty(request("filters.testAccount")));
        }
        if(request()->has('trash') and request()->trash == 1){
            $query->onlyTrashed();
        }
    }
    function scopeHasRepresentatives($query, $type = null, $id = null)
    {
        return $query
            ->whereHas('orderRepresentatives', function ($orderRepresentativesQuery) use ($id, $type) {
                $orderRepresentativesQuery->when($type, function ($q) use ($type) {
                    if (is_array($type)) {
                        $q->whereIn('type', $type);
                    } else {
                        $q->where('type', $type);
                    }
                })
                    ->when($id, function ($q) use ($id) {
                        if (is_array($id)) {
                            $q->whereIn('representative_id', $id);
                        } else {
                            $q->where('representative_id', $id);
                        }
                    });
            });
    }
    function scopeTestAccounts($query, $hasTesting = false)
    {
        $testAccounts = SettingsService::getDataBaseSetting('testing_accounts') ?? [];
        if ($hasTesting) {
            $query->whereHas("client", function ($clientQuery)use($testAccounts) {
                $clientQuery->whereIn('id',$testAccounts);
            });
        }else{
            $query->whereHas("client", function ($clientQuery)use($testAccounts) {
                $clientQuery->whereNotIn('id',$testAccounts);
            });
        }
    }
    function scopeB2b($query){
       
        $order = request()->input('order.0.column');
        $dir   = request()->input('order.0.dir');

        // Searching
        if (!empty(request()->input('search.value'))) {
            $search = request()->input('search.value');
            $query->where(function ($q) use ($search) {
                $q->where('reference_id', 'LIKE', "%{$search}%")
                    ->orWhere('status', 'LIKE', "%{$search}%")
                    ->orWhere('total_price', 'LIKE', "%{$search}%");
            });
        }
        // Ordering
        $columns = [0 => 'id', 1 => 'or_receiver.date', 2 => 'id', 3 => 'total_price', 4 => 'status'];
        if (isset($columns[$order])) {
            $query->orderBy($columns[$order], $dir);
        } else {
            $query->orderByRaw('or_receiver.date IS NOT NULL DESC')
                ->orderByDesc('or_receiver.date')
                ->orderByDesc('orders.id');
        }
        

    }
    //end Scopes

    //start relations
    public function city()
    {
        return $this->belongsTo(City::class, 'city_id', 'id');
    }

    public function district()
    {
        return $this->belongsTo(District::class, 'district_id', 'id');
    }

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id', 'id');
    }
    public function operator()
    {
        return $this->belongsTo(User::class, 'operator_id', 'id');
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class, 'coupon_id', 'id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'id');
    }

    public function orderRepresentatives()
    {
        return $this->hasMany(OrderRepresentative::class, 'order_id', 'id');
    }

    public function reports()
    {
        return $this->hasMany(OrderReport::class, 'order_id', 'id');
    }
    public function report()
    {
        return $this->hasOne(OrderReport::class, 'order_id', 'id');
    }

    public function moreDatas()
    {
        return $this->hasMany(OrderTypesOfDatum::class, 'order_id', 'id');
    }
    public function walletTransactions()
    {
        return $this->hasMany(WalletTransaction::class, 'order_id', 'id');
    }
    public function transactions()
    {
        return $this->hasMany(OrderTransaction::class, 'order_id', 'id');
    }

    public function histories()
    {
        return $this->hasMany(OrderHistory::class, 'order_id', 'id')->orderByDesc('created_at');
    }


    //end relations

    //start Attributes
    public function getCouponStringAttribute()
    {
        $item = $this->coupon;
        if(!$item){
            $item = json_decode($this->coupon_data);
        }
        if(!$item){
            return null;
        }
        $value = isset($item?->value) ? $item?->value : "0";
        $type = isset($item?->type) ? $item?->type : "no-discount";
        $title =  $item->code.", ".trans('type').": ".trans($type).", ".trans('value').": ".$value;
        $orderTotal = $this->total_price;
        $totalCoupon = $item?->value;
        if($type == 'percentage' || $type == 'cashback'){
            $title .= "%";
            $totalCoupon = $orderTotal * $totalCoupon / 100;
        }else{
            $title .= " ".trans('SAR');
        }
        $title .= " ||| ".trans('total').": ".$totalCoupon." ".trans('SAR');
        return $title;
    }
    public function getCouponStringForInvoiceAttribute()
    {
        $item = $this->coupon;
        if(!$item){
            $item = json_decode($this->coupon_data);
        }
        if(!$item){
            return null;
        }
        $title =  $item->code;
        $value = isset($item?->value) ? $item?->value : "0";
        $type = isset($item?->type) ? $item?->type : "no-discount";
        switch($type){
            case 'percentage':
                $title .= " ".$value."%";
                break;
            case 'value':
                $title .= " ".$value." ". trans('SAR');
                break;
            case 'cashback':
                $title .= " ".$value." ". trans( 'cashback');
                break;
        }
      
        
        return $title;
    }
    public function getActions($slug)
    {

        $forCompany = request()->has("forCompany") and request("forCompany") == true;
        $actions = '<div class ="d-flex justify-content-center">';
        if(request()->has('trash') and request()->trash == 1){
            if (auth('web')->check() and auth('web')->user()->can('dashboard.'.$slug.'.restore')) {
                $actions .= '<a class="btn-operation d-flex justify-content-center align-items-center mx-1 btn-restore" href="' . route('dashboard.'.$slug.'.restore', ['id' => $this->id]) . '">
                    <i class="fa fa-trash-restore"></i> <span>' . trans('restore') . '</span>
                    </a>';
            }
            $actions .= '</div>';
            return $actions;
        }
        if (auth('web')->check() and auth('web')->user()->can('dashboard.' . $slug . '.show')) {
            $actions .= '<a class="btn-operation d-flex justify-content-center align-items-center mx-1 " href="' . route('dashboard.' . $slug . '.show', ['id' => $this->reference_id,'forCompany' => $forCompany ?? false ]) . '">
                <i class="fa fa-eye"></i> <span>' . trans('show') . '</span>
                </a>';
        }
        if (auth('web')->check() and auth('web')->user()->can('dashboard.' . $slug . '.edit')) {
            $actions .= '<a class="btn-operation d-flex justify-content-center align-items-center mx-1 " href="' . route('dashboard.' . $slug . '.edit', ['id' => $this->reference_id,'forCompany' => $forCompany ?? false ]) . '">
            <i class="fa fa-edit"></i> <span>' . trans('edit') . '</span>
            </a>';
        }
        if (auth('web')->check() and auth('web')->user()->can('dashboard.' . $slug . '.invoice')) {
            $actions .= '<a class="btn-operation d-flex justify-content-center align-items-center mx-1 " href="' . route('dashboard.' . $slug . '.invoice', ['id' => $this->reference_id,'forCompany' => $forCompany ?? false ]) . '">
                <i class="fas fa-print"></i> <span>' . trans('invoice') . '</span>
                </a>';
        }
        if (auth('web')->check() and auth('web')->user()->can('dashboard.' . $slug . '.delete')) {
            $actions .= '<a class="btn-operation d-flex justify-content-center align-items-center mx-1 delete-btn" href="' . route('dashboard.' . $slug . '.delete', ['id' => $this->id,'forCompany' => $forCompany ?? false ]) . '">
                <i class="fa fa-trash"></i><span> ' . trans('delete') . ' </span>
                </a>';
        }

        $actions .= '</div>';
        return $actions;
    }
    public function getActionsAttribute()
    {
        return $this->getActions('orders');
    }
    public function getItemsActionsAttribute()
    {
        return $this->getItemsActions('orders');
    }
    public function getShowActionsAttribute()
    {
        return $this->getShowActions('orders');
    }
    public function getItemDataAttribute()
    {
        return $this->getItemData('orders');
    }
    public function getAddressDescriptionAttribute()
    {
        $rep = $this->orderRepresentatives()->whereHas('address', function ($addressQuery) {
            $addressQuery->whereNotNull('description');
        })->first();
        return $rep?->address?->description;
    }
    public function getStatusChangesAttribute()
    {
        $statusChanges  = json_decode($this->order_status_times, true);
        $list           = [];
        foreach ($statusChanges as $statusChange) {
            foreach ($statusChange as $status => $date) {
                $list[] = (object)[
                    'status'    => $status,
                    'date'      => (is_array($date)) ? $date[0] ?? null : $date,
                    'day'       => (is_array($date)) ?  Carbon::parse($date[0])->translatedFormat('l') : Carbon::parse($date)->translatedFormat('l'),
                    'email'     => (is_array($date)) ? $date[1] ?? null : "",
                    'notes'     => (is_array($date)) ? $date[2] ?? null : "",
                ];
            }
        }
        return $list;
    }
    public function getCouponTypeAttribute()
    {
        return json_decode($this->coupon_data, true)['type'] ?? $this->coupon?->type ?? 'no-discount';
    }
    public function setOrderStatusTimesAttribute($value)
    {
        $status     = $this->order_status_times ? json_decode($this->attributes['order_status_times'], true) : [];
        $status[]   = $value;
        $status     = json_encode($status);
        $this->attributes['order_status_times'] = $status;
        return $status;
    }
    public function getOrderTrackingAttribute()
    {
       return OrderHelper::orderStatusTimes($this);
    }
    //end Attributes
    protected static function booted()
    {
        static::addGlobalScope(new UnderOperationOrdersScope());
    }
}
