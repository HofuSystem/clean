<?php

namespace Core\Categories\Observers;

use Core\Categories\Models\Category;

class CategoryObserver
{
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

    }

    /**
     * Handle the Category "deleted" event.
     *
     * @param  \Core\Categories\Models\Category  $category
     * @return void
     */
    public function deleted(Category $category)
    {
      
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
