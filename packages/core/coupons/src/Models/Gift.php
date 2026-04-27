<?php

namespace Core\Coupons\Models;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Core\Settings\Models\CoreModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Observers\GlobalModelObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Core\Coupons\Observers\GiftObserver;
use Carbon\Carbon;

#[ObservedBy([GlobalModelObserver::class, GiftObserver::class])]
class Gift extends CoreModel implements TranslatableContract

{
    use Translatable, SoftDeletes;

    protected $table = 'gifts';
    protected $fillable = [
        'status',
        'from',
        'to',
        'coupon_code',
        'order_type',
        'register_from',
        'register_to',
        'orders_from',
        'orders_to',
        'orders_min',
        'orders_max',
        'type',
        'value',
        'max_value',
        'coupon_id',
        'creator_id',
        'updater_id'
    ];

    public $translatedAttributes = ['title', 'intro', 'btn_text', 'label', 'description'];

    public function coupon()
    {
        return $this->belongsTo(Coupon::class, 'coupon_id');
    }

    public function scopeSearch($query)
    {
        if (request()->has("filters.title") && !empty(request("filters.title"))) {
            $query->whereTranslationLike("title", "%" . request("filters.title") . "%");
        }

        if (request()->has("filters.status") && !empty(request("filters.status"))) {
            $query->where("status", request("filters.status"));
        }

        if (request()->has("filters.order_type") && !empty(request("filters.order_type"))) {
            $query->where("order_type", request("filters.order_type"));
        }

        if (request()->has("filters.coupon_code") && !empty(request("filters.coupon_code"))) {
            $query->where("coupon_code", "LIKE", "%" . request("filters.coupon_code") . "%");
        }

        if (request()->has('trash') && request()->trash == 1) {
            $query->onlyTrashed();
        }
    }

    public function getActionsAttribute()
    {
        return $this->getActions('gifts');
    }

    public function getItemsActionsAttribute()
    {
        return $this->getItemsActions('gifts');
    }

    public function getShowActionsAttribute()
    {
        return $this->getShowActions('gifts');
    }

    public function getItemDataAttribute()
    {
        return $this->getItemData('gifts');
    }
}
