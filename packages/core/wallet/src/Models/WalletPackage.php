<?php

namespace Core\Wallet\Models;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Core\Wallet\Observers\WalletPackageObserver;

use Core\Settings\Models\CoreModel;
use Carbon\Carbon;
use App\Observers\GlobalModelObserver;


#[ObservedBy([WalletPackageObserver::class])]
#[ObservedBy([GlobalModelObserver::class])]

class WalletPackage extends CoreModel {
    
	protected $table             = 'wallet_packages';
	protected $fillable          = ['image', 'price', 'value', 'status', 'creator_id', 'updater_id'];
    protected $guarded           = [];
    

    //start Scopes
    function scopeSearch($query){
        
        //filter by number on  price
        if((request()->has("filters.price")) and !empty(request("filters.price"))){
            $query->where("price",request("filters.price"));
        }
        
        //filter by number on  value
        if((request()->has("filters.value")) and !empty(request("filters.value"))){
            $query->where("value",request("filters.value"));
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
    
    //end relations

    //start Attributes
    
    public function getActionsAttribute(){
      return $this->getActions('wallet-packages');
    }

    public function getItemsActionsAttribute(){
        return $this->getItemsActions('wallet-packages');
    }
    public function getShowActionsAttribute(){
        return $this->getShowActions('wallet-packages');
    }

    public function getItemDataAttribute(){
        return $this->getItemData('wallet-packages');
    }
    //end Attributes

}
