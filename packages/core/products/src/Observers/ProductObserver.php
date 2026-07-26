<?php

namespace Core\Products\Observers;

use Core\Products\Models\Product;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ProductObserver
{
    private function flushCategoriesCache()
    {
        try {
            if (Cache::supportsTags()) {
                Cache::tags(['categories_api'])->flush();
            } else {
                Cache::forget('categories_api');
            }
        } catch (\Throwable $e) {
            Log::warning('Failed to flush category cache: ' . $e->getMessage());
        }
    }
    /**
     * Handle the Product "creating" event.
     *
     * @param  \Core\Products\Models\Product  $product
     * @return void
     */
    public function creating(Product $product)
    {
    
    }
    /**
     * Handle the Product "created" event.
     *
     * @param  \Core\Products\Models\Product  $product
     * @return void
     */
    public function created(Product $product)
    {
    
    }

    /**
     * Handle the Product "updating" event.
     *
     * @param  \Core\Products\Models\Product  $product
     * @return void
     */
    public function updating(Product $product)
    {

    }
    /**
     * Handle the Product "updated" event.
     *
     * @param  \Core\Products\Models\Product  $product
     * @return void
     */
    public function updated(Product $product)
    {

    }
    /**
     * Handle the Product "saving" event.
     *
     * @param  \Core\Products\Models\Product  $product
     * @return void
     */
    public function saving(Product $product)
    {

    }
    /**
     * Handle the Product "saved" event.
     *
     * @param  \Core\Products\Models\Product  $product
     * @return void
     */
    public function saved(Product $product)
    {
        $this->flushCategoriesCache();
    }

    /**
     * Handle the Product "deleted" event.
     *
     * @param  \Core\Products\Models\Product  $product
     * @return void
     */
    public function deleted(Product $product)
    {
        $this->flushCategoriesCache();
    }

    /**
     * Handle the Product "restored" event.
     *
     * @param  \Core\Products\Models\Product  $product
     * @return void
     */
    public function restored(Product $product)
    {
        //
    }

    /**
     * Handle the Product "force deleted" event.
     *
     * @param  \Core\Products\Models\Product  $product
     * @return void
     */
    public function forceDeleted(Product $product)
    {
        //
    }
}
