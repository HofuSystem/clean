<?php

namespace Core\Financials\Models;

use Core\Settings\Models\CoreModel;

class Purchase extends CoreModel
{
    protected $table = 'purchases';
    protected $fillable = ['item_id', 'provider_id', 'value_before_tax', 'tax_value', 'value_after_tax', 'notes', 'attachment', 'collection_date', 'creator_id', 'updater_id'];
    protected $guarded = [];
    protected $casts = [
        'collection_date' => 'date',
    ];

    //start Scopes
    function scopeSearch($query) {
        if(request()->has("filters.item_id") && !empty(request("filters.item_id"))){
            $query->where("item_id", request("filters.item_id"));
        }
        if(request()->has("filters.provider_id") && !empty(request("filters.provider_id"))){
            $query->where("provider_id", request("filters.provider_id"));
        }
        if(request()->has('trash') && request()->trash == 1){
            $query->onlyTrashed();
        }
    }
    //end Scopes

    public function item(){
        return $this->belongsTo(PurchaseItem::class, 'item_id', 'id');
    }

    public function provider(){
        return $this->belongsTo(PurchaseProvider::class, 'provider_id', 'id');
    }

    //start Attributes
    public function getActionsAttribute(){
        return $this->getActions('purchases');
    }

    public function getItemsActionsAttribute(){
        return $this->getItemsActions('purchases');
    }
    
    public function getShowActionsAttribute(){
        return $this->getShowActions('purchases');
    }

    public function getItemDataAttribute(){
        return $this->getItemData('purchases');
    }
    //end Attributes
}
