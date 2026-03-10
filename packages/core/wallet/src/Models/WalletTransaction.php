<?php

namespace Core\Wallet\Models;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Core\Users\Models\User;
use Core\Wallet\Observers\WalletTransactionObserver;

use Core\Settings\Models\CoreModel;
use Carbon\Carbon;
use App\Observers\GlobalModelObserver;
use Core\Orders\Models\Order;

#[ObservedBy([WalletTransactionObserver::class])]
#[ObservedBy([GlobalModelObserver::class])]

class WalletTransaction extends CoreModel {
    
	protected $table             = 'wallet_transactions';
	protected $fillable          = ['type', 'amount', 'wallet_before', 'wallet_after', 'status', 'transaction_id','transaction_type', 'bank_name', 'account_number', 'iban_number', 'user_id','order_id', 'added_by_id','expired_at', 'package_id','notes', 'creator_id', 'updater_id'];
    protected $guarded           = [];
    

    //start Scopes
    function scopeSearch($query){
        
        //filter select on  type
        if((request()->has("filters.type")) and !empty(request("filters.type"))){
            $query->where("type",request("filters.type"));
        }

        //filter select on  transaction_type
        if((request()->has("filters.transaction_type")) and !empty(request("filters.transaction_type"))){
            $query->where("transaction_type",request("filters.transaction_type"));
        }
        
        //filter by number on  amount
        if((request()->has("filters.amount")) and !empty(request("filters.amount"))){
            $query->where("amount",request("filters.amount"));
        }
        
        //filter by number on  wallet_before
        if((request()->has("filters.wallet_before")) and !empty(request("filters.wallet_before"))){
            $query->where("wallet_before",request("filters.wallet_before"));
        }
        
        //filter by number on  wallet_after
        if((request()->has("filters.wallet_after")) and !empty(request("filters.wallet_after"))){
            $query->where("wallet_after",request("filters.wallet_after"));
        }
        
        //filter select on  status
        if((request()->has("filters.status")) and !empty(request("filters.status"))){
            $query->where("status",request("filters.status"));
        }
        
        //filter text on  transaction_id
        if((request()->has("filters.transaction_id")) and !empty(request("filters.transaction_id"))){
            $query->where("transaction_id","LIKE","%".request("filters.transaction_id")."%");
        }
        
        //filter text on  bank_name
        if((request()->has("filters.bank_name")) and !empty(request("filters.bank_name"))){
            $query->where("bank_name","LIKE","%".request("filters.bank_name")."%");
        }
        
        //filter text on  account_number
        if((request()->has("filters.account_number")) and !empty(request("filters.account_number"))){
            $query->where("account_number","LIKE","%".request("filters.account_number")."%");
        }
        
        //filter text on  iban_number
        if((request()->has("filters.iban_number")) and !empty(request("filters.iban_number"))){
            $query->where("iban_number","LIKE","%".request("filters.iban_number")."%");
        }
        
        //filter select on  user
        if((request()->has("filters.user_id")) and !empty(request("filters.user_id"))){
            $query->whereRelation("user","id",request("filters.user_id"));
        }
        
        //filter select on  addedBy
        if((request()->has("filters.added_by_id")) and !empty(request("filters.added_by_id"))){
            $query->whereRelation("addedBy","id",request("filters.added_by_id"));
        }
        
        //filter select on  package
        if((request()->has("filters.package_id")) and !empty(request("filters.package_id"))){
            $query->whereRelation("package","id",request("filters.package_id"));
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

    public function addedBy(){
        return $this->belongsTo(User::class, 'added_by_id', 'id');
    }

    public function package(){
        return $this->belongsTo(WalletPackage::class, 'package_id', 'id');
    }

    //end relations

    //start Attributes
    
    public function getActionsAttribute(){
      return $this->getActions('wallet-transactions');
    }

    public function getItemsActionsAttribute(){
        return $this->getItemsActions('wallet-transactions');
    }
    public function getShowActionsAttribute(){
        return $this->getShowActions('wallet-transactions');
    }

    public function getItemDataAttribute(){
        return $this->getItemData('wallet-transactions');
    }
    //end Attributes

}
