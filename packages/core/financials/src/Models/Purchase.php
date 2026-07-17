<?php

namespace Core\Financials\Models;

use Core\Settings\Models\CoreModel;

class Purchase extends CoreModel
{
    protected $table = 'purchases';
    protected $fillable = ['item_id', 'provider_id', 'reference_id', 'value_before_tax', 'tax_value', 'value_after_tax', 'notes', 'attachment', 'collection_date', 'creator_id', 'updater_id', 'bank_transfer_files'];
    protected $guarded = [];
    protected $casts = [
        'collection_date' => 'date',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->reference_id)) {
                $prefix = 'Pur-' . date('Ymd');
                $lastReference = static::where('reference_id', 'like', $prefix . '%')
                    ->orderBy('reference_id', 'desc')
                    ->first();
                if ($lastReference) {
                    $invoiceOrderNumber = (int) substr($lastReference->reference_id, -5);
                    $model->reference_id = $prefix . '-' . str_pad($invoiceOrderNumber + 1, 5, '0', STR_PAD_LEFT);
                } else {
                    $model->reference_id = $prefix . '-' . str_pad(1, 5, '0', STR_PAD_LEFT);
                }
            }
        });
    }

    //start Scopes
    function scopeSearch($query) {
        if(request()->has("filters.reference_id") && !empty(request("filters.reference_id"))){
            $query->where("reference_id",'like', '%' . request("filters.reference_id") . '%');
        }
        if(request()->has("filters.item_id") && !empty(request("filters.item_id"))){
            $query->where("item_id", request("filters.item_id"));
        }
        if(request()->has("filters.provider_id") && !empty(request("filters.provider_id"))){
            $query->where("provider_id", request("filters.provider_id"));
        }
        if(request()->has("filters.collection_date_from") && !empty(request("filters.collection_date_from"))){
            $query->where("collection_date", ">=", request("filters.collection_date_from"));
        }
        if(request()->has("filters.collection_date_to") && !empty(request("filters.collection_date_to"))){
            $query->where("collection_date", "<=", request("filters.collection_date_to"));
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
