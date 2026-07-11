<?php

namespace Core\Financials\Models;

use Core\Settings\Models\CoreModel;
use Core\Info\Models\City;
use Core\Info\Models\District;

class PurchaseProvider extends CoreModel
{
    protected $table = 'purchase_providers';
    protected $fillable = ['name', 'commercial_registration', 'tax_number', 'street_name', 'building_no', 'city_id', 'district_id', 'postal_code', 'creator_id', 'updater_id'];
    protected $guarded = [];

    //start Scopes
    function scopeSearch($query) {
        if(request()->has("filters.name") && !empty(request("filters.name"))){
            $query->where("name","LIKE","%".request("filters.name")."%");
        }
        if(request()->has("filters.commercial_registration") && !empty(request("filters.commercial_registration"))){
            $query->where("commercial_registration","LIKE","%".request("filters.commercial_registration")."%");
        }
        if(request()->has("filters.tax_number") && !empty(request("filters.tax_number"))){
            $query->where("tax_number","LIKE","%".request("filters.tax_number")."%");
        }
        if(request()->has('trash') && request()->trash == 1){
            $query->onlyTrashed();
        }
    }
    //end Scopes

    public function city(){
        return $this->belongsTo(City::class, 'city_id', 'id');
    }

    public function district(){
        return $this->belongsTo(District::class, 'district_id', 'id');
    }

    //start Attributes
    public function getActionsAttribute(){
        return $this->getActions('purchase-providers');
    }

    public function getItemsActionsAttribute(){
        return $this->getItemsActions('purchase-providers');
    }
    
    public function getShowActionsAttribute(){
        return $this->getShowActions('purchase-providers');
    }

    public function getItemDataAttribute(){
        return $this->getItemData('purchase-providers');
    }
    //end Attributes
}
