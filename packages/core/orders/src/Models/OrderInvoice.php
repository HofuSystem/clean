<?php

namespace Core\Orders\Models;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Core\Users\Models\User;
use Core\Orders\Observers\OrderInvoiceObserver;

use Core\Settings\Models\CoreModel;
use Carbon\Carbon;
use App\Observers\GlobalModelObserver;


#[ObservedBy([OrderInvoiceObserver::class])]
#[ObservedBy([GlobalModelObserver::class])]

class OrderInvoice extends CoreModel {
    
	protected $table             = 'order_invoices';
	protected $fillable          = ['invoice_num', 'data', 'order_id', 'user_id', 'creator_id', 'updater_id'];
    protected $guarded           = [];
    

    //start Scopes
    function scopeSearch($query){
        
        //filter text on  invoice_num
        if((request()->has("filters.invoice_num")) and !empty(request("filters.invoice_num"))){
            $query->where("invoice_num","LIKE","%".request("filters.invoice_num")."%");
        }
        
        //filter select on  order
        if((request()->has("filters.order_id")) and !empty(request("filters.order_id"))){
            $query->whereRelation("order","id",request("filters.order_id"));
        }
        
        //filter select on  user
        if((request()->has("filters.user_id")) and !empty(request("filters.user_id"))){
            $query->whereRelation("user","id",request("filters.user_id"));
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

    public function user(){
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    //end relations

    //start Attributes
    
    public function getActionsAttribute(){
      return $this->getActions('order-invoices');
    }

    public function getItemsActionsAttribute(){
        return $this->getItemsActions('order-invoices');
    }
    public function getShowActionsAttribute(){
        return $this->getShowActions('order-invoices');
    }

    public function getItemDataAttribute(){
        return $this->getItemData('order-invoices');
    }
    //end Attributes

}
