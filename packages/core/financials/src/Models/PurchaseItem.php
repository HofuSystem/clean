<?php

namespace Core\Financials\Models;

use Core\Settings\Models\CoreModel;

class PurchaseItem extends CoreModel
{
    protected $table = 'purchase_items';
    protected $fillable = ['name', 'creator_id', 'updater_id'];
    protected $guarded = [];

    //start Scopes
    function scopeSearch($query) {
        if(request()->has("filters.name") && !empty(request("filters.name"))){
            $query->where("name","LIKE","%".request("filters.name")."%");
        }
        if(request()->has('trash') && request()->trash == 1){
            $query->onlyTrashed();
        }
    }
    //end Scopes

    //start Attributes
    public function getActionsAttribute(){
        return $this->getActions('purchase-items');
    }

    public function getItemsActionsAttribute(){
        return $this->getItemsActions('purchase-items');
    }
    
    public function getShowActionsAttribute(){
        return $this->getShowActions('purchase-items');
    }

    public function getItemDataAttribute(){
        return $this->getItemData('purchase-items');
    }
    //end Attributes
}
