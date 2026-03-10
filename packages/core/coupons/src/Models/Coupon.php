<?php

namespace Core\Coupons\Models;

use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Core\Products\Models\Product;
use Core\Categories\Models\Category;
use Core\Users\Models\User;
use Core\Users\Models\Role;
use Core\Coupons\Observers\CouponObserver;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Core\Settings\Models\CoreModel;
use Carbon\Carbon;
use App\Observers\GlobalModelObserver;
use Core\Orders\Models\Order;
use Illuminate\Support\Facades\DB;

#[ObservedBy([CouponObserver::class])]
#[ObservedBy([GlobalModelObserver::class])]

class Coupon extends CoreModel implements TranslatableContract
{
    use Translatable;
    protected $table             = 'coupons';
    protected $fillable          = ['status', 'applying', 'code', 'max_use', 'max_use_per_user', 'payment_method', 'start_at', 'end_at', 'order_type', 'all_products', 'all_users', 'order_minimum', 'order_maximum', 'type', 'value', 'max_value', 'creator_id', 'updater_id'];
    protected $guarded           = [];
    public $translatedAttributes = ["title"];

    //start Scopes
    function scopeSearch($query)
    {

        //filter text on  title
        if ((request()->has("filters.title")) and !empty(request("filters.title"))) {
            $query->whereTranslationLike("title", "%" . request("filters.title") . "%");
        }

        //filter select on  status
        if ((request()->has("filters.status")) and !empty(request("filters.status"))) {
            $query->where("status", request("filters.status"));
        }

        //filter select on  applying
        if ((request()->has("filters.applying")) and !empty(request("filters.applying"))) {
            $query->where("applying", request("filters.applying"));
        }

        //filter text on  code
        if ((request()->has("filters.code")) and !empty(request("filters.code"))) {
            $query->where("code", "LIKE", "%" . request("filters.code") . "%");
        }

        //filter by number on  max_use
        if ((request()->has("filters.max_use")) and !empty(request("filters.max_use"))) {
            $query->where("max_use", request("filters.max_use"));
        }

        //filter by number on  max_use_per_user
        if ((request()->has("filters.max_use_per_user")) and !empty(request("filters.max_use_per_user"))) {
            $query->where("max_use_per_user", request("filters.max_use_per_user"));
        }

        //filter select on  payment_method
        if ((request()->has("filters.payment_method")) and !empty(request("filters.payment_method"))) {
            $query->where("payment_method", request("filters.payment_method"));
        }

        //filter date on  start_at
        if ((request()->has("filters.from_start_at")) and !empty(request("filters.from_start_at"))) {
            $query->where("start_at", ">=", Carbon::parse(request("filters.from_start_at")));
        }

        if ((request()->has("filters.to_start_at")) and !empty(request("filters.to_start_at"))) {
            $query->where("start_at", "<=", Carbon::parse(request("filters.to_start_at")));
        }

        //filter date on  end_at
        if ((request()->has("filters.from_end_at")) and !empty(request("filters.from_end_at"))) {
            $query->where("end_at", ">=", Carbon::parse(request("filters.from_end_at")));
        }

        if ((request()->has("filters.to_end_at")) and !empty(request("filters.to_end_at"))) {
            $query->where("end_at", "<=", Carbon::parse(request("filters.to_end_at")));
        }

        //filter select on  products
        if ((request()->has("filters.products")) and !empty(request("filters.products"))) {
            $query->whereRelation("products", "id", request("filters.products"));
        }

        //filter select on  categories
        if ((request()->has("filters.categories")) and !empty(request("filters.categories"))) {
            $query->whereRelation("categories", "id", request("filters.categories"));
        }

        //filter select on  users
        if ((request()->has("filters.users")) and !empty(request("filters.users"))) {
            $query->whereRelation("users", "id", request("filters.users"));
        }

        //filter select on  roles
        if ((request()->has("filters.roles")) and !empty(request("filters.roles"))) {
            $query->whereRelation("roles", "id", request("filters.roles"));
        }

        //filter by number on  order_minimum
        if ((request()->has("filters.order_minimum")) and !empty(request("filters.order_minimum"))) {
            $query->where("order_minimum", request("filters.order_minimum"));
        }

        //filter by number on  order_maximum
        if ((request()->has("filters.order_maximum")) and !empty(request("filters.order_maximum"))) {
            $query->where("order_maximum", request("filters.order_maximum"));
        }

        //filter select on  type
        if ((request()->has("filters.type")) and !empty(request("filters.type"))) {
            $query->where("type", request("filters.type"));
        }

        //filter by number on  value
        if ((request()->has("filters.value")) and !empty(request("filters.value"))) {
            $query->where("value", request("filters.value"));
        }

        //filter by number on  max_value
        if ((request()->has("filters.max_value")) and !empty(request("filters.max_value"))) {
            $query->where("max_value", request("filters.max_value"));
        }

        //filter date on  created_at
        if ((request()->has("filters.from_created_at")) and !empty(request("filters.from_created_at"))) {
            $query->whereDate("created_at", ">=", Carbon::parse(request("filters.from_created_at")));
        }

        if ((request()->has("filters.to_created_at")) and !empty(request("filters.to_created_at"))) {
            $query->whereDate("created_at", "<=", Carbon::parse(request("filters.to_created_at")));
        }

        //filter date on  updated_at
        if ((request()->has("filters.from_updated_at")) and !empty(request("filters.from_updated_at"))) {
            $query->whereDate("updated_at", ">=", Carbon::parse(request("filters.from_updated_at")));
        }

        if ((request()->has("filters.to_updated_at")) and !empty(request("filters.to_updated_at"))) {
            $query->whereDate("updated_at", "<=", Carbon::parse(request("filters.to_updated_at")));
        }
        if(request()->has('trash') and request()->trash == 1){
            $query->onlyTrashed();
        }
    }
    public function scopeFindMatching(
        $query,
        string | null  $applying     = 'auto',
        string | null  $code         = null,
        int    | null  $userId       = null,
        string | null  $orderType    = null,
        array  | null  $productsIds  = null,
        float  | null  $orderValue   = null,
    ) {
        $query->where('applying', $applying)
        ->when(isset($code), function ($query)use($code) {
                $query->where('code', $code);
            })
            ->when($userId, function ($query)use($userId) {
                $query->where(function ($query)use($userId){
                    $query->where('max_use_per_user', '>', function ($limitQuery)use($userId) {
                      $limitQuery->selectRaw('Count(orders.id)')  // Select relevant columns
                          ->from('orders')
                          ->whereColumn('orders.coupon_id', 'coupons.id')
                          ->where('orders.client_id',$userId);
                  })
                  ->orWhere('max_use_per_user', null);

                });
                  // Ensure discount is used less than max_use
            })
            ->where(function ($query) {
                // Ensure discount is used less than max_use
                $query->where('max_use', '>', function ($limitQuery) {
                    $limitQuery->selectRaw('Count(orders.id)')  // Select relevant columns
                        ->from('orders')
                        ->whereColumn('orders.coupon_id', 'coupons.id');
                       
                })
                ->orWhere('max_use', null);
            })
            ->when($orderType, function ($query)use($orderType) {
                $query->where(function ($query) use ($orderType) {
                    $query->where('order_type', $orderType)
                        ->orWhere('order_type', null);
                });
            })
            ->when(isset($productsIds) and !empty($productsIds), function ($query)use($productsIds) {
                $query->where(function ($query) use ($productsIds) {
                    $query->whereHas('products', function ($query) use ($productsIds) {
                        $query->whereIn('product_id', $productsIds);
                    })
                        ->orWhereHas('categories', function ($query) use ($productsIds) {
                            $query->whereHas('products', function ($query) use ($productsIds) {
                                $query->whereIn('id', $productsIds);
                            })
                            ->orWhereHas('productsSub', function ($query) use ($productsIds) {
                                $query->whereIn('id', $productsIds);
                            });
                        });
                })->orWhere(function ($query) {
                    $query->whereDoesntHave('products')->whereDoesntHave('categories');
                });
            })
            ->when($userId, function ($query)use($userId) {
                $query->where(function ($query) use ($userId){
                    $query->where(function ($query) use ($userId) {
                        $query->whereHas('users', function ($query) use ($userId) {
                            $query->where('id', $userId);
                        })
                        ->orWhereHas('roles', function ($query) use ($userId) {
                            $query->whereHas('users', function ($query) use ($userId) {
                                $query->where('id', $userId);
                            });
                        });
                    })->orWhere(function ($query) {
                        $query->whereDoesntHave('users')->whereDoesntHave('roles');
                    });
                });
            })
            // //filter order minimum or minmum is empty and maximum or maximum is empty 
            ->when($orderValue, function ($query)use($orderValue) {
                $query->where(function ($query) use ($orderValue) {
                    $query
                        ->where(function ($query) use ($orderValue) {
                            $query
                                ->where('order_minimum', '<=', $orderValue)
                                ->orWhere('order_minimum', null);
                        })
                        ->where(function ($query) use ($orderValue) {
                            $query
                                ->where('order_maximum', '>=', $orderValue)
                                ->orWhere('order_maximum', null);
                        });
                });
            })
            ->where('status', 'active')
            ->Where(function($query){
                $query->where('start_at', '<=', now())
                ->orWhere('start_at',null);
            })
            ->Where(function($query){
                $query->where('end_at', '>=', now())
                ->orWhere('end_at',null);
            })
            ;
    }

    //end Scopes

    //start relations

    public function products()
    {
        //                                                           you many need to swap these last att
        return $this->belongsToMany(Product::class, 'coupons_products', 'coupon_id', 'product_id');
    }

    public function categories()
    {
        //                                                           you many need to swap these last att
        return $this->belongsToMany(Category::class, 'coupons_categories', 'coupon_id', 'category_id');
    }

    public function users()
    {
        //                                                           you many need to swap these last att
        return $this->belongsToMany(User::class, 'coupons_users', 'coupon_id', 'user_id');
    }

    public function roles()
    {
        //                                                           you many need to swap these last att
        return $this->belongsToMany(Role::class, 'coupons_roles', 'coupon_id', 'role_id');
    }
    public function orders()
    {
        return $this->hasMany(Order::class,'coupon_id','id');
    }

    //end relations

    //start Attributes

    public function getActionsAttribute()
    {
        return $this->getActions('coupons');
    }

    public function getItemsActionsAttribute()
    {
        return $this->getItemsActions('coupons');
    }
    public function getShowActionsAttribute()
    {
        return $this->getShowActions('coupons');
    }

    public function getItemDataAttribute()
    {
        return $this->getItemData('coupons');
    }
    //end Attributes

}
