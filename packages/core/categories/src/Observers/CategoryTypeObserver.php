<?php

namespace Core\Categories\Observers;

use Core\Categories\Models\CategoryType;

class CategoryTypeObserver
{
    /**
     * Handle the CategoryType "creating" event.
     *
     * @param  \Core\Categories\Models\CategoryType  $categoryType
     * @return void
     */
    public function creating(CategoryType $categoryType)
    {
    
    }
    /**
     * Handle the CategoryType "created" event.
     *
     * @param  \Core\Categories\Models\CategoryType  $categoryType
     * @return void
     */
    public function created(CategoryType $categoryType)
    {
    
    }

    /**
     * Handle the CategoryType "updating" event.
     *
     * @param  \Core\Categories\Models\CategoryType  $categoryType
     * @return void
     */
    public function updating(CategoryType $categoryType)
    {

    }
    /**
     * Handle the CategoryType "updated" event.
     *
     * @param  \Core\Categories\Models\CategoryType  $categoryType
     * @return void
     */
    public function updated(CategoryType $categoryType)
    {

    }
    /**
     * Handle the CategoryType "saving" event.
     *
     * @param  \Core\Categories\Models\CategoryType  $categoryType
     * @return void
     */
    public function saving(CategoryType $categoryType)
    {

    }
    /**
     * Handle the CategoryType "saved" event.
     *
     * @param  \Core\Categories\Models\CategoryType  $categoryType
     * @return void
     */
    public function saved(CategoryType $categoryType)
    {

    }

    /**
     * Handle the CategoryType "deleted" event.
     *
     * @param  \Core\Categories\Models\CategoryType  $categoryType
     * @return void
     */
    public function deleted(CategoryType $categoryType)
    {
      
    }

    /**
     * Handle the CategoryType "restored" event.
     *
     * @param  \Core\Categories\Models\CategoryType  $categoryType
     * @return void
     */
    public function restored(CategoryType $categoryType)
    {
        //
    }

    /**
     * Handle the CategoryType "force deleted" event.
     *
     * @param  \Core\Categories\Models\CategoryType  $categoryType
     * @return void
     */
    public function forceDeleted(CategoryType $categoryType)
    {
        //
    }
}
