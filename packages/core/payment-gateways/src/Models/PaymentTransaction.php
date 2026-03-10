<?php

namespace Core\PaymentGateways\Models;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Core\PaymentGateways\Observers\PaymentTransactionObserver;
use Core\Settings\Models\CoreModel;
use Illuminate\Database\Eloquent\Model;
use App\Observers\GlobalModelObserver;
use Carbon\Carbon;
use Core\Users\Models\User;

#[ObservedBy([PaymentTransactionObserver::class])]
#[ObservedBy([GlobalModelObserver::class])]

class PaymentTransaction extends CoreModel {
	protected $table             = 'payment_transactions';
	protected $fillable          = ['transaction_id', 'for', 'status', 'amount', 'request_data', 'payment_method', 'payment_data', 'payment_response', 'creator_id', 'updater_id', 'user_id'];
    protected $guarded           = [];
  

    //start Scopes
    function scopeSearch($query){
        
        //filter text on  transaction_id
        if((request()->has("filters.transaction_id")) and !empty(request("filters.transaction_id"))){
            $query->where("transaction_id","LIKE","%".request("filters.transaction_id")."%");
        }
        
        //filter text on  for
        if((request()->has("filters.for")) and !empty(request("filters.for"))){
            $query->where("for","LIKE","%".request("filters.for")."%");
        }
        
        //filter text on  status
        if((request()->has("filters.status")) and !empty(request("filters.status"))){
            $query->where("status","LIKE","%".request("filters.status")."%");
        }
        
        //filter text on  request_data
        if((request()->has("filters.request_data")) and !empty(request("filters.request_data"))){
            $query->where("request_data","LIKE","%".request("filters.request_data")."%");
        }
        
        //filter text on  payment_method
        if((request()->has("filters.payment_method")) and !empty(request("filters.payment_method"))){
            $query->where("payment_method","LIKE","%".request("filters.payment_method")."%");
        }
        
        //filter text on  payment_data
        if((request()->has("filters.payment_data")) and !empty(request("filters.payment_data"))){
            $query->where("payment_data","LIKE","%".request("filters.payment_data")."%");
        }
        
        //filter text on  payment_response
        if((request()->has("filters.payment_response")) and !empty(request("filters.payment_response"))){
            $query->where("payment_response","LIKE","%".request("filters.payment_response")."%");
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
    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }
    //end relations

    //start Attributes
    
    public function getActionsAttribute(){
      return $this->getActions('payment-transactions');
    }

    public function getItemsActionsAttribute(){
        return $this->getItemsActions('payment-transactions');
    }
    public function getShowActionsAttribute(){
        return $this->getShowActions('payment-transactions');
    }

    public function getItemDataAttribute(){
        return $this->getItemData('payment-transactions');
    }
    //end Attributes

}
