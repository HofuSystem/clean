<?php

namespace Core\Categories\Observers;

use Core\Categories\Models\CategoryDateTime;

class CategoryDateTimeObserver
{
    /**
     * Handle the CategoryDateTime "creating" event.
     *
     * @param  \Core\Categories\Models\CategoryDateTime  $categoryDateTime
     * @return void
     */
    public function creating(CategoryDateTime $categoryDateTime)
    {
    
    }
    /**
     * Handle the CategoryDateTime "created" event.
     *
     * @param  \Core\Categories\Models\CategoryDateTime  $categoryDateTime
     * @return void
     */
    public function created(CategoryDateTime $categoryDateTime)
    {
    
    }

    /**
     * Handle the CategoryDateTime "updating" event.
     *
     * @param  \Core\Categories\Models\CategoryDateTime  $categoryDateTime
     * @return void
     */
    public function updating(CategoryDateTime $categoryDateTime)
    {

    }
    /**
     * Handle the CategoryDateTime "updated" event.
     *
     * @param  \Core\Categories\Models\CategoryDateTime  $categoryDateTime
     * @return void
     */
    public function updated(CategoryDateTime $categoryDateTime)
    {

    }
    /**
     * Handle the CategoryDateTime "saving" event.
     *
     * @param  \Core\Categories\Models\CategoryDateTime  $categoryDateTime
     * @return void
     */
    public function saving(CategoryDateTime $categoryDateTime)
    {

    }
    /**
     * Handle the CategoryDateTime "saved" event.
     *
     * @param  \Core\Categories\Models\CategoryDateTime  $categoryDateTime
     * @return void
     */
    public function saved(CategoryDateTime $categoryDateTime)
    {

    }

    /**
     * Handle the CategoryDateTime "deleted" event.
     *
     * @param  \Core\Categories\Models\CategoryDateTime  $categoryDateTime
     * @return void
     */
    public function deleted(CategoryDateTime $categoryDateTime)
    {
      
    }

    /**
     * Handle the CategoryDateTime "restored" event.
     *
     * @param  \Core\Categories\Models\CategoryDateTime  $categoryDateTime
     * @return void
     */
    public function restored(CategoryDateTime $categoryDateTime)
    {
        //
    }

    /**
     * Handle the CategoryDateTime "force deleted" event.
     *
     * @param  \Core\Categories\Models\CategoryDateTime  $categoryDateTime
     * @return void
     */
    public function forceDeleted(CategoryDateTime $categoryDateTime)
    {
        //
    }
}
