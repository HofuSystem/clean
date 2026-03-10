<?php

namespace Core\Users\Models;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Core\Products\Models\Product;
use Core\Users\Observers\ContractsPriceObserver;

use Illuminate\Database\Eloquent\Model;
use App\Observers\GlobalModelObserver;
use Core\Settings\Models\CoreModel;
use Carbon\Carbon;


#[ObservedBy([ContractsPriceObserver::class])]
#[ObservedBy([GlobalModelObserver::class])]

class ContractsPrice extends CoreModel {
	protected $table             = 'contracts_prices';
	protected $fillable          = ['contract_id', 'product_id', 'price', 'creator_id', 'updater_id'];
    protected $guarded           = [];
    

    //start Scopes
    function scopeSearch($query){
        
        //filter select on  contract
        if((request()->has("filters.contract_id")) and !empty(request("filters.contract_id"))){
            $query->whereRelation("contract","id",request("filters.contract_id"));
        }
        
        //filter select on  product
        if((request()->has("filters.product_id")) and !empty(request("filters.product_id"))){
            $query->whereRelation("product","id",request("filters.product_id"));
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
    
    public function contract(){
        return $this->belongsTo(Contract::class, 'contract_id', 'id');
    }

    public function product(){
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    //end relations

    //start Attributes
    
    public function getActionsAttribute(){
      return $this->getActions('contracts-prices');
    }

    public function getItemsActionsAttribute(){
        return $this->getItemsActions('contracts-prices');
    }
    public function getShowActionsAttribute(){
        return $this->getShowActions('contracts-prices');
    }

    public function getItemDataAttribute(){
        return $this->getItemData('contracts-prices');
    }
    //end Attributes

}
