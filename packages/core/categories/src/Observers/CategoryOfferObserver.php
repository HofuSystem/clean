<?php

namespace Core\Categories\Observers;

use Core\Categories\Models\CategoryOffer;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CategoryOfferObserver
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
     * Handle the Offer"creating" event.
     *
     * @param  \Core\Categories\Models\CategoryOffer  $categoryOffer
     * @return void
     */
    public function creating(CategoryOffer $categoryOffer)
    {
    
    }
    /**
     * Handle the Offer"created" event.
     *
     * @param  \Core\Categories\Models\CategoryOffer  $categoryOffer
     * @return void
     */
    public function created(CategoryOffer $categoryOffer)
    {
    
    }

    /**
     * Handle the Offer"updating" event.
     *
     * @param  \Core\Categories\Models\CategoryOffer  $categoryOffer
     * @return void
     */
    public function updating(CategoryOffer $categoryOffer)
    {

    }
    /**
     * Handle the Offer"updated" event.
     *
     * @param  \Core\Categories\Models\CategoryOffer  $categoryOffer
     * @return void
     */
    public function updated(CategoryOffer $categoryOffer)
    {

    }
    /**
     * Handle the Offer"saving" event.
     *
     * @param  \Core\Categories\Models\CategoryOffer  $categoryOffer
     * @return void
     */
    public function saving(CategoryOffer $categoryOffer)
    {

    }
    /**
     * Handle the Offer"saved" event.
     *
     * @param  \Core\Categories\Models\CategoryOffer  $categoryOffer
     * @return void
     */
    public function saved(CategoryOffer $categoryOffer)
    {
        $this->flushCategoriesCache();
    }

    /**
     * Handle the Offer"deleted" event.
     *
     * @param  \Core\Categories\Models\CategoryOffer  $categoryOffer
     * @return void
     */
    public function deleted(CategoryOffer $categoryOffer)
    {
        $this->flushCategoriesCache();
    }

    /**
     * Handle the Offer"restored" event.
     *
     * @param  \Core\Categories\Models\CategoryOffer  $categoryOffer
     * @return void
     */
    public function restored(CategoryOffer $categoryOffer)
    {
        //
    }

    /**
     * Handle the Offer"force deleted" event.
     *
     * @param  \Core\Categories\Models\CategoryOffer  $categoryOffer
     * @return void
     */
    public function forceDeleted(CategoryOffer $categoryOffer)
    {
        //
    }
}
