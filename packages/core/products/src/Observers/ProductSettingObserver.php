<?php

namespace Core\Products\Observers;

use Core\Products\Models\ProductSetting;

class ProductSettingObserver
{
    public function creating(ProductSetting $productSetting)
    {
    
    }
    public function created(ProductSetting $productSetting)
    {
    
    }
    public function updating(ProductSetting $productSetting)
    {

    }
    public function updated(ProductSetting $productSetting)
    {

    }
    public function saving(ProductSetting $productSetting)
    {
        if ($productSetting->parent_id) {
            $parent = ProductSetting::find($productSetting->parent_id);
            if ($parent && $parent->general) {
                $productSetting->general = true;
            }
        }
    }
    public function saved(ProductSetting $productSetting)
    {
        if ($productSetting->general && is_null($productSetting->parent_id)) {
            ProductSetting::where('parent_id', $productSetting->id)
                ->where(function($q) {
                    $q->where('general', '!=', true)->orWhereNull('general');
                })
                ->update(['general' => true]);
        }
    }
    public function deleted(ProductSetting $productSetting)
    {
      
    }
    public function restored(ProductSetting $productSetting)
    {
        
    }
    public function forceDeleted(ProductSetting $productSetting)
    {
        
    }
}
