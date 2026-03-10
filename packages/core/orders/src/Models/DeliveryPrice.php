<?php

namespace Core\Orders\Models;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Core\Categories\Models\Category;
use Core\Info\Models\City;
use Core\Info\Models\District;
use Core\Orders\Observers\DeliveryPriceObserver;

use Core\Settings\Models\CoreModel;
use Carbon\Carbon;
use App\Observers\GlobalModelObserver;


#[ObservedBy([DeliveryPriceObserver::class])]
#[ObservedBy([GlobalModelObserver::class])]

class DeliveryPrice extends CoreModel {
    
	protected $table             = 'delivery_prices';
	protected $fillable          = ['category_id', 'city_id', 'district_id', 'price', 'free_delivery', 'creator_id', 'updater_id'];
    protected $guarded           = [];
    

    //start Scopes
    function scopeSearch($query){
        
        //filter select on  category
        if((request()->has("filters.category_id")) and !empty(request("filters.category_id"))){
            $query->whereRelation("category","id",request("filters.category_id"));
        }
        
        //filter select on  city
        if((request()->has("filters.city_id")) and !empty(request("filters.city_id"))){
            $query->whereRelation("city_id","",request("filters.city_id"));
        }
        
        //filter select on  district
        if((request()->has("filters.district_id")) and !empty(request("filters.district_id"))){
            $query->whereRelation("district","id",request("filters.district_id"));
        }
        
        //filter by number on  price
        if((request()->has("filters.price")) and !empty(request("filters.price"))){
            $query->where("price",request("filters.price"));
        }

        //filter by number on  free_delivery
        if((request()->has("filters.free_delivery")) and !empty(request("filters.free_delivery"))){
            $query->where("free_delivery",request("filters.free_delivery"));
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
    function scopeMatchingDeliveryPrice($query,$category_id,$address){
       $query           ->where(function ($categoryQuery) use ($category_id) {
        $categoryQuery->whereNull('category_id')
            ->when(is_int($category_id), function ($categoryQuery) use ($category_id) {
                $categoryQuery->OrWhere('category_id', $category_id);
            })->when(is_array($category_id), function ($categoryQuery) use ($category_id) {
                $categoryQuery->orWhereIn('category_id', $category_id);
            });
    })->where(function ($cityQuery) use ($address) {
        $cityQuery->whereNull('city_id')
            ->when(is_int($address->city_id), function ($cityQuery) use ($address) {
                $cityQuery->OrWhere('city_id', $address->city_id);
            })->when(is_array($address->city_id), function ($cityQuery) use ($address) {
                $cityQuery->orWhereIn('city_id', $address->city_id);
            });
    })->where(function ($districtQuery) use ($address) {
        $districtQuery->whereNull('district_id')
            ->when(is_int($address->district_id), function ($districtQuery) use ($address) {
                $districtQuery->OrWhere('district_id', $address->district_id);
            })->when(is_array($address->district_id), function ($districtQuery) use ($address) {
                $districtQuery->orWhereIn('district_id', $address->district_id);
            });
    })
    ->orderBy('price', 'desc');
    }
    //end Scopes

    //start relations
    
    public function category(){
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function city(){
        return $this->belongsTo(City::class, 'city_id', 'id');
    }

    public function district(){
        return $this->belongsTo(District::class, 'district_id', 'id');
    }

    //end relations

    //start Attributes
    
    public function getActionsAttribute(){
      return $this->getActions('delivery-prices');
    }

    public function getItemsActionsAttribute(){
        return $this->getItemsActions('delivery-prices');
    }
    public function getShowActionsAttribute(){
        return $this->getShowActions('delivery-prices');
    }

    public function getItemDataAttribute(){
        return $this->getItemData('delivery-prices');
    }
    //end Attributes

}
