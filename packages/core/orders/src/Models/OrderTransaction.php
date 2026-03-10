<?php

namespace Core\Orders\Models;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Core\Coupons\Models\Coupon;
use Core\Wallet\Models\WalletTransaction;
use Core\Orders\Observers\OrderTransactionObserver;

    use Core\Settings\Models\CoreModel;
use App\Observers\GlobalModelObserver;
use Carbon\Carbon;
use Core\Users\Models\Point;

#[ObservedBy([OrderTransactionObserver::class])]
#[ObservedBy([GlobalModelObserver::class])]

class OrderTransaction extends CoreModel {
	protected $table             = 'order_transactions';
	protected $fillable          = ['order_id', 'type', 'online_payment_method', 'amount', 'transaction_id', 'point_id', 'wallet_transaction_id','notes', 'creator_id', 'updater_id'];
    protected $guarded           = [];
    

    //start Scopes
    function scopeSearch($query){
        
        //filter select on  order
        if((request()->has("filters.order_id")) and !empty(request("filters.order_id"))){
            $query->whereRelation("order","id",request("filters.order_id"));
        }
        
        //filter text on  type
        if((request()->has("filters.type")) and !empty(request("filters.type"))){
            $query->where("type","LIKE","%".request("filters.type")."%");
        }
        
        //filter text on  online_payment_method
        if((request()->has("filters.online_payment_method")) and !empty(request("filters.online_payment_method"))){
            $query->where("online_payment_method","LIKE","%".request("filters.online_payment_method")."%");
        }
        
        //filter by number on  amount
        if((request()->has("filters.amount")) and !empty(request("filters.amount"))){
            $query->where("amount",request("filters.amount"));
        }
        
        //filter text on  transaction_id
        if((request()->has("filters.transaction_id")) and !empty(request("filters.transaction_id"))){
            $query->where("transaction_id","LIKE","%".request("filters.transaction_id")."%");
        }
        
        //filter select on  point
        if((request()->has("filters.point_id")) and !empty(request("filters.point_id"))){
            $query->whereRelation("point","id",request("filters.point_id"));
        }
        
        //filter select on  walletTransaction
        if((request()->has("filters.wallet_transaction_id")) and !empty(request("filters.wallet_transaction_id"))){
            $query->whereRelation("walletTransaction","id",request("filters.wallet_transaction_id"));
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

    public function point(){
        return $this->belongsTo(Point::class, 'point_id', 'id');
    }

    public function walletTransaction(){
        return $this->belongsTo(WalletTransaction::class, 'wallet_transaction_id', 'id');
    }

    //end relations

    //start Attributes
    
    public function getActionsAttribute(){
      return $this->getActions('order-transactions');
    }

    public function getItemsActionsAttribute(){
        return $this->getItemsActions('order-transactions');
    }
    public function getShowActionsAttribute(){
        return $this->getShowActions('order-transactions');
    }

    public function getItemDataAttribute(){
        return $this->getItemData('order-transactions');
    }
    //end Attributes

}
