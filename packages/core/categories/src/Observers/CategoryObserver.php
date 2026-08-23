<?php

namespace Core\Categories\Observers;

use Core\Categories\Models\Category;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CategoryObserver
{
    private function flushCategoriesCache()
    {
        try {
            if (Cache::supportsTags()) {
                Cache::tags(['categories_api'])->flush();
            } else {
                Cache::forget('categories_api');
                foreach (['ar', 'en'] as $lang) {
                    Cache::forget("home_economy_bags_{$lang}");
                    Cache::forget("home_services_sales_{$lang}");
                }
            }
        } catch (\Throwable $e) {
            Log::warning('Failed to flush category cache: ' . $e->getMessage());
        }
    }
    /**
     * Handle the Category "creating" event.
     *
     * @param  \Core\Categories\Models\Category  $category
     * @return void
     */
    public function creating(Category $category)
    {
    
    }
    /**
     * Handle the Category "created" event.
     *
     * @param  \Core\Categories\Models\Category  $category
     * @return void
     */
    public function created(Category $category)
    {
    
    }

    /**
     * Handle the Category "updating" event.
     *
     * @param  \Core\Categories\Models\Category  $category
     * @return void
     */
    public function updating(Category $category)
    {

    }
    /**
     * Handle the Category "updated" event.
     *
     * @param  \Core\Categories\Models\Category  $category
     * @return void
     */
    public function updated(Category $category)
    {

    }
    /**
     * Handle the Category "saving" event.
     *
     * @param  \Core\Categories\Models\Category  $category
     * @return void
     */
    public function saving(Category $category)
    {

    }
    /**
     * Handle the Category "saved" event.
     *
     * @param  \Core\Categories\Models\Category  $category
     * @return void
     */
    public function saved(Category $category)
    {
        $this->flushCategoriesCache();
    }

    /**
     * Handle the Category "deleted" event.
     *
     * @param  \Core\Categories\Models\Category  $category
     * @return void
     */
    public function deleted(Category $category)
    {
        $this->flushCategoriesCache();
    }

    /**
     * Handle the Category "restored" event.
     *
     * @param  \Core\Categories\Models\Category  $category
     * @return void
     */
    public function restored(Category $category)
    {
        //
    }

    /**
     * Handle the Category "force deleted" event.
     *
     * @param  \Core\Categories\Models\Category  $category
     * @return void
     */
    public function forceDeleted(Category $category)
    {
        //
    }
}
