<?php

namespace Core\Orders\Models;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Core\Products\Models\Product;
use Core\Orders\Observers\OrderItemObserver;

use Core\Settings\Models\CoreModel;
use Carbon\Carbon;
use App\Observers\GlobalModelObserver;


#[ObservedBy([OrderItemObserver::class])]
#[ObservedBy([GlobalModelObserver::class])]

class OrderItem extends CoreModel {
    
	protected $table             = 'order_items';
	protected $fillable          = ['order_id', 'product_id', 'product_data', 'product_price','product_cost', 'quantity', 'width','height', 'add_by_admin', 'update_by_admin', 'is_picked', 'is_delivered', 'creator_id', 'updater_id','deleted_at', 'final_delete'];
    protected $guarded           = [];
    

    //start Scopes
    function scopeSearch($query){
        
        //filter select on  order
        if((request()->has("filters.order_id")) and !empty(request("filters.order_id"))){
            $query->whereRelation("order","id",request("filters.order_id"));
        }
        
        //filter select on  product
        if((request()->has("filters.product_id")) and !empty(request("filters.product_id"))){
            $query->whereRelation("product","id",request("filters.product_id"));
        }
        
        //filter by number on  product_price
        if((request()->has("filters.product_price")) and !empty(request("filters.product_price"))){
            $query->where("product_price",request("filters.product_price"));
        }
        
        //filter by number on  quantity
        if((request()->has("filters.quantity")) and !empty(request("filters.quantity"))){
            $query->where("quantity",request("filters.quantity"));
        }
        
        //filter by number on  width
        if((request()->has("filters.width")) and !empty(request("filters.width"))){
            $query->where("width",request("filters.width"));
        }
        //filter by number on  height
        if((request()->has("filters.height")) and !empty(request("filters.height"))){
            $query->where("height",request("filters.height"));
        }
        
        //filter by email on  add_by_admin
        if((request()->has("filters.add_by_admin")) and !empty(request("filters.add_by_admin"))){
            $query->where("add_by_admin","LIKE","%".request("filters.add_by_admin")."%");
        }
        
        //filter by email on  update_by_admin
        if((request()->has("filters.update_by_admin")) and !empty(request("filters.update_by_admin"))){
            $query->where("update_by_admin","LIKE","%".request("filters.update_by_admin")."%");
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

    public function product(){
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function qtyUpdates(){
        return $this->hasMany(OrderItemQtyUpdate::class, 'item_id', 'id');
    }

    //end relations

    //start Attributes
    
    public function getActionsAttribute(){
      return $this->getActions('order-items');
    }

    public function getItemsActionsAttribute(){
        return $this->getItemsActions('order-items');
    }
    public function getShowActionsAttribute(){
        return $this->getShowActions('order-items');
    }

    public function getItemDataAttribute(){
        return $this->getItemData('order-items');
    }
    public function getTotalPriceAttribute(){
        return (isset($this->width) and $this->width and isset($this->height) and $this->height) 
        ? ($this->width * $this->height * $this->quantity * $this->product_price) 
        : ($this->quantity * $this->product_price);
    }
    public function getTotalCostAttribute(){
        return (isset($this->width) and $this->width and isset($this->height) and $this->height) 
        ? ($this->width * $this->height * $this->quantity * $this->product_cost) 
        : ($this->quantity * $this->product_cost);
    }
    //end Attributes

}
