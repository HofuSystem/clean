<?php

namespace Core\Users\Models;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Core\Users\Observers\PointObserver;

use Core\Settings\Models\CoreModel;
use Carbon\Carbon;
use App\Observers\GlobalModelObserver;


#[ObservedBy([PointObserver::class])]
#[ObservedBy([GlobalModelObserver::class])]

class Point extends CoreModel {
    
	protected $table             = 'points';
	protected $fillable          = ['title', 'amount', 'operation', 'expire_at', 'user_id', 'order_id', 'creator_id', 'updater_id'];
    protected $guarded           = [];
    protected $casts             = ['amount' => 'float', 'expire_at' => 'datetime'];
    

    //start Scopes
    function scopeSearch($query){
        
        //filter text on  title
        if((request()->has("filters.title")) and !empty(request("filters.title"))){
            $query->where("title","LIKE","%".request("filters.title")."%");
        }
        
        //filter text on  amount
        if((request()->has("filters.amount")) and !empty(request("filters.amount"))){
            $query->where("amount","LIKE","%".request("filters.amount")."%");
        }
        if((request()->has("filters.user_id")) and !empty(request("filters.user_id"))){
            $query->where("user_id",request("filters.user_id"));
        }

        if (request()->filled('filters.order_id')) {
            $query->where('order_id', request('filters.order_id'));
        }

        if (request()->filled('filters.creator_id')) {
            $query->where('creator_id', request('filters.creator_id'));
        }

        if (request()->filled('filters.search')) {
            $search = request('filters.search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('fullname', 'LIKE', "%{$search}%")
                            ->orWhere('phone', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('order', function ($orderQuery) use ($search) {
                        $orderQuery->where('reference_id', 'LIKE', "%{$search}%");
                    });
            });
        }
        
        //filter select on  operation
        if((request()->has("filters.operation")) and !empty(request("filters.operation"))){
            $query->where("operation",request("filters.operation"));
        }
        
        //filter date on  created_at
        if((request()->has("filters.from_created_at")) and !empty(request("filters.from_created_at"))){
            $query->whereDate("created_at",">=",Carbon::parse(request("filters.from_created_at")));
        }

        if((request()->has("filters.to_created_at")) and !empty(request("filters.to_created_at"))){
            $query->whereDate("created_at","<=",Carbon::parse(request("filters.to_created_at")));
        }
        
        //filter date on  updated_at
        if((request()->has("filters.from_updated_at")) and !empty(request("filters.from_updated_at"))){
            $query->whereDate("updated_at",">=",Carbon::parse(request("filters.from_updated_at")));
        }

        if((request()->has("filters.to_updated_at")) and !empty(request("filters.to_updated_at"))){
            $query->whereDate("updated_at","<=",Carbon::parse(request("filters.to_updated_at")));
        }
        
        if(request()->has('trash') and request()->trash == 1){
            $query->onlyTrashed();
        }
    }
  
    //end Scopes

    //start relations
    
    public function user(){
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function order(){
        return $this->belongsTo(\Core\Orders\Models\Order::class, 'order_id', 'id');
    }

    public function creator(){
        return $this->belongsTo(User::class, 'creator_id', 'id');
    }

    public function getDisplayTitleAttribute(): string
    {
        $title = trim((string) $this->title);
        if (!str_starts_with(app()->getLocale(), 'ar')) {
            return trans($title);
        }

        if (preg_match('/^for order\s*:\s*(.+)$/i', $title, $matches)) {
            $reference = $this->order?->reference_id ?: trim($matches[1]);
            return 'نقاط مكتسبة من الطلب رقم ' . $reference;
        }

        if (preg_match('/^used in order\s*:\s*(.+)$/i', $title, $matches)) {
            $reference = $this->order?->reference_id ?: trim($matches[1]);
            return 'استخدام نقاط للطلب رقم ' . $reference;
        }

        if (preg_match('/^Points returned due to order cancellation\s*(\d+)$/i', $title, $matches)) {
            $reference = $this->order?->reference_id ?: $matches[1];
            return 'إرجاع نقاط بعد إلغاء الطلب رقم ' . $reference;
        }

        if ($title === 'Expired Points') {
            return trans('points_ui.expired_points');
        }

        if (strcasecmp($title, 'register gift points') === 0) {
            return 'مكافأة نقاط التسجيل';
        }

        if (preg_match('/^referral gift points for user\s*:\s*(.+)$/i', $title, $matches)) {
            return 'مكافأة إحالة عن العميل ' . trim($matches[1]);
        }

        return trans($title);
    }

    //end relations

    //start Attributes
    
    public function getActionsAttribute(){
      return $this->getActions('points');
    }

    public function getItemsActionsAttribute(){
        return $this->getItemsActions('points');
    }
    public function getShowActionsAttribute(){
        return $this->getShowActions('points');
    }

    public function getItemDataAttribute(){
        return $this->getItemData('points');
    }
    //end Attributes

}
