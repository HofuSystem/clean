<?php

namespace Core\Categories\Models;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Core\Info\Models\City;
use Core\Categories\Observers\PriceObserver;

use Core\Settings\Models\CoreModel;
use Carbon\Carbon;
use App\Observers\GlobalModelObserver;


#[ObservedBy([PriceObserver::class])]
#[ObservedBy([GlobalModelObserver::class])]

class Price extends CoreModel {
    
	protected $table             = 'prices';
	protected $fillable          = ['priceable_type', 'priceable_id', 'city_id', 'price','cost', 'creator_id', 'updater_id'];
    protected $guarded           = [];
    

    //start Scopes
    function scopeSearch($query){
        
        //filter text on  priceable_type
        if((request()->has("filters.priceable_type")) and !empty(request("filters.priceable_type"))){
            $query->where("priceable_type","LIKE","%".request("filters.priceable_type")."%");
        }
        
        //filter by number on  priceable_id
        if((request()->has("filters.priceable_id")) and !empty(request("filters.priceable_id"))){
            $query->where("priceable_id",request("filters.priceable_id"));
        }
        
        //filter select on  city
        if((request()->has("filters.city_id")) and !empty(request("filters.city_id"))){
            $query->whereRelation("city","id",request("filters.city_id"));
        }
        
        //filter by number on  price
        if((request()->has("filters.price")) and !empty(request("filters.price"))){
            $query->where("price",request("filters.price"));
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
    
    public function city(){
        return $this->belongsTo(City::class, 'city_id', 'id');
    }

    //end relations

    //start Attributes
    
    public function getActionsAttribute(){
      return $this->getActions('prices');
    }

    public function getItemsActionsAttribute(){
        return $this->getItemsActions('prices');
    }
    public function getShowActionsAttribute(){
        return $this->getShowActions('prices');
    }

    public function getItemDataAttribute(){
        return $this->getItemData('prices');
    }
    //end Attributes

}
