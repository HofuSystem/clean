<?php

namespace Core\Products\Models;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Core\Categories\Models\Category;
use Core\Categories\Models\Price;
use Core\Products\Observers\ProductObserver;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Core\Settings\Models\CoreModel;
use Carbon\Carbon;
use App\Observers\GlobalModelObserver;
use Core\Orders\Models\OrderItem;
use Core\Users\Models\User;
use Core\Users\Models\ContractsPrice;
use Core\Users\Models\ContractsCustomerPrice;

#[ObservedBy([ProductObserver::class])]
#[ObservedBy([GlobalModelObserver::class])]

class Product extends CoreModel implements TranslatableContract{
    use Translatable;
	protected $table             = 'products';
	protected $fillable          = [ 'image', 'type', 'sku', 'is_package', 'category_id', 'sub_category_id', 'price','cost', 'quantity', 'status', 'creator_id', 'updater_id'];
    protected $guarded           = [];
    public $translatedAttributes = ["name","desc"];

    //start Scopes
    function scopeSearch($query){
        if(request()->page == 'best_sales'){
            $to = request('filters.to');
            $from = request('filters.from');
            if (!request('filters.to')) {
                $to = request()->to;
            }
            $filterType = request()->has('filters.type-filter') ? request('filters.type-filter') : 'sales-products';
                    $query
                    ->when($filterType == 'sales-products',function($salesProductQuery){
                        $salesProductQuery->whereIn('products.category_id',['10','11','12']);
                    })
                    ->when($filterType == 'sales-package-products',function($salesProductQuery){
                        $salesProductQuery->whereIn('products.category_id',['13'])->where('is_package',true);
                    })
                    ->where('products.status', 'active')
                    ->whereHas('orderItems.order', function ($q) use ($from, $to) {
                        $q->testAccounts(false)
                        ->whereIn('orders.status', ['finished','delivered'])
                        ->when($from, function ($q) use ( $from, $to) {
                            return $q->whereBetween('orders.created_at', [$from, $to]);
                        });
                    })
                    ->withSum(['orderItems as total_quantity' => function ($query) use ($from, $to) {
                        $query
                        ->whereHas('order', function ($q) use ($from,$to) {
                            $q->testAccounts(false)
                            ->whereIn('orders.status', ['finished','delivered'])
                            ->when($from, function ($q) use ( $from, $to) {
                                return $q->whereBetween('orders.created_at', [$from, $to]);
                            });
                        })
                        ->select(\DB::raw('SUM(quantity)'));
                    }],'total_quantity');
        }
        //filter text on  name
        if((request()->has("filters.name")) and !empty(request("filters.name"))){
            $query->whereTranslationLike("name","%".request("filters.name")."%");
        }

        //filter text on  desc
        if((request()->has("filters.desc")) and !empty(request("filters.desc"))){
            $query->where("desc"."_".config("app.locale"),"LIKE","%".request("filters.desc")."%");
        }

        //filter select on  type
        if((request()->has("filters.type")) and !empty(request("filters.type"))){
            if(request("filters.type") == 'packages'){
                $query->where("is_package",true);
            }else{
                $query->where("type",request("filters.type"));
            }
        }

        //filter text on  sku
        if((request()->has("filters.sku")) and !empty(request("filters.sku"))){
            $query->where("sku","LIKE","%".request("filters.sku")."%");
        }

        //filter select on  category
        if((request()->has("filters.category_id")) and !empty(request("filters.category_id"))){
            $query->whereRelation("category","id",request("filters.category_id"));
        }

        //filter select on  subCategory
        if((request()->has("filters.sub_category_id")) and !empty(request("filters.sub_category_id"))){
            $query->whereRelation("subCategory","id",request("filters.sub_category_id"));
        }

        //filter by number on  price
        if((request()->has("filters.price")) and !empty(request("filters.price"))){
            $query->where("price",request("filters.price"));
        }

        //filter by number on  quantity
        if((request()->has("filters.quantity")) and !empty(request("filters.quantity"))){
            $query->where("quantity",request("filters.quantity"));
        }

        //filter select on  status
        if((request()->has("filters.status")) and !empty(request("filters.status"))){
            $query->where("status",request("filters.status"));
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
    public function favers(){
        return $this->morphToMany(User::class, 'favs', 'favs', 'favs_id', 'user_id')
        ->where('favs_type', self::class);
    }
    public function category(){
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function subCategory(){
        return $this->belongsTo(Category::class, 'sub_category_id', 'id');
    }

    public function prices(){
        return $this->morphMany(Price::class, 'priceable');
    }
    public function orderItems(){
        return $this->hasMany(OrderItem::class, 'product_id');
    }
    public function contractsPrices(){
        return $this->hasMany(ContractsPrice::class, 'product_id');
    }
    public function contractCustomerPrices(){
        return $this->hasMany(ContractsCustomerPrice::class, 'product_id');
    }

    //end relations

    //start Attributes
    public function getImageUrlAttribute(){
        return url('storage/'.$this->attributes['image']);
    }
    public function getActionsAttribute(){
      return $this->getActions('products');
    }

    public function getItemsActionsAttribute(){
        return $this->getItemsActions('products');
    }
    public function getShowActionsAttribute(){
        return $this->getShowActions('products');
    }

    public function getItemDataAttribute(){
        return $this->getItemData('products');
    }
    //end Attributes

}
