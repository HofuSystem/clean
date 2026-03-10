<?php

namespace Core\Orders\Models;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Core\Orders\Observers\OrderTypesOfDatumObserver;

use Core\Settings\Models\CoreModel;
use Carbon\Carbon;
use App\Observers\GlobalModelObserver;


#[ObservedBy([OrderTypesOfDatumObserver::class])]
#[ObservedBy([GlobalModelObserver::class])]

class OrderTypesOfDatum extends CoreModel {
    
	protected $table             = 'order_types_of_datas';
	protected $fillable          = ['order_id', 'key', 'value', 'creator_id', 'updater_id'];
    protected $guarded           = [];
    

    //start Scopes
    function scopeSearch($query){
        
        //filter select on  order
        if((request()->has("filters.order_id")) and !empty(request("filters.order_id"))){
            $query->whereRelation("order","id",request("filters.order_id"));
        }
        
        //filter text on  key
        if((request()->has("filters.key")) and !empty(request("filters.key"))){
            $query->where("key","LIKE","%".request("filters.key")."%");
        }
        
        //filter text on  value
        if((request()->has("filters.value")) and !empty(request("filters.value"))){
            $query->where("value","LIKE","%".request("filters.value")."%");
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
    
    public function order(){
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    //end relations

    //start Attributes
    
    public function getActionsAttribute(){
      return $this->getActions('order-types-of-datas');
    }

    public function getItemsActionsAttribute(){
        return $this->getItemsActions('order-types-of-datas');
    }
    public function getShowActionsAttribute(){
        return $this->getShowActions('order-types-of-datas');
    }

    public function getItemDataAttribute(){
        return $this->getItemData('order-types-of-datas');
    }
    //end Attributes

}
