<?php

namespace Core\Orders\Models;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Core\Orders\Observers\OrderItemQtyUpdateObserver;

use Core\Settings\Models\CoreModel;
use Carbon\Carbon;
use App\Observers\GlobalModelObserver;


#[ObservedBy([OrderItemQtyUpdateObserver::class])]
#[ObservedBy([GlobalModelObserver::class])]

class OrderItemQtyUpdate extends CoreModel {
    
	protected $table             = 'order_item_qty_updates';
	protected $fillable          = ['item_id', 'from', 'to', 'updater_email', 'creator_id', 'updater_id'];
    protected $guarded           = [];
    

    //start Scopes
    function scopeSearch($query){
        
        //filter select on  item
        if((request()->has("filters.item_id")) and !empty(request("filters.item_id"))){
            $query->whereRelation("item","id",request("filters.item_id"));
        }
        
        //filter by number on  from
        if((request()->has("filters.from")) and !empty(request("filters.from"))){
            $query->where("from",request("filters.from"));
        }
        
        //filter by number on  to
        if((request()->has("filters.to")) and !empty(request("filters.to"))){
            $query->where("to",request("filters.to"));
        }
        
        //filter by email on  updater_email
        if((request()->has("filters.updater_email")) and !empty(request("filters.updater_email"))){
            $query->where("updater_email","LIKE","%".request("filters.updater_email")."%");
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
    
    public function item(){
        return $this->belongsTo(OrderItem::class, 'item_id', 'id');
    }

    //end relations

    //start Attributes
    
    public function getActionsAttribute(){
      return $this->getActions('order-item-qty-updates');
    }

    public function getItemsActionsAttribute(){
        return $this->getItemsActions('order-item-qty-updates');
    }
    public function getShowActionsAttribute(){
        return $this->getShowActions('order-item-qty-updates');
    }

    public function getItemDataAttribute(){
        return $this->getItemData('order-item-qty-updates');
    }
    //end Attributes

}
