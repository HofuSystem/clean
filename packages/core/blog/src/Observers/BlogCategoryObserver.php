<?php

namespace Core\Blog\Observers;

use Core\Blog\Models\BlogCategory;

class BlogCategoryObserver
{
    /**
     * Handle the BlogCategory "creating" event.
     *
     * @param  \Core\Blog\Models\BlogCategory  $blogCategory
     * @return void
     */
    public function creating(BlogCategory $blogCategory)
    {
    
    }
    /**
     * Handle the BlogCategory "created" event.
     *
     * @param  \Core\Blog\Models\BlogCategory  $blogCategory
     * @return void
     */
    public function created(BlogCategory $blogCategory)
    {
    
    }

    /**
     * Handle the BlogCategory "updating" event.
     *
     * @param  \Core\Blog\Models\BlogCategory  $blogCategory
     * @return void
     */
    public function updating(BlogCategory $blogCategory)
    {

    }
    /**
     * Handle the BlogCategory "updated" event.
     *
     * @param  \Core\Blog\Models\BlogCategory  $blogCategory
     * @return void
     */
    public function updated(BlogCategory $blogCategory)
    {

    }
    /**
     * Handle the BlogCategory "saving" event.
     *
     * @param  \Core\Blog\Models\BlogCategory  $blogCategory
     * @return void
     */
    public function saving(BlogCategory $blogCategory)
    {

    }
    /**
     * Handle the BlogCategory "saved" event.
     *
     * @param  \Core\Blog\Models\BlogCategory  $blogCategory
     * @return void
     */
    public function saved(BlogCategory $blogCategory)
    {

    }

    /**
     * Handle the BlogCategory "deleted" event.
     *
     * @param  \Core\Blog\Models\BlogCategory  $blogCategory
     * @return void
     */
    public function deleted(BlogCategory $blogCategory)
    {
      
    }

    /**
     * Handle the BlogCategory "restored" event.
     *
     * @param  \Core\Blog\Models\BlogCategory  $blogCategory
     * @return void
     */
    public function restored(BlogCategory $blogCategory)
    {
        //
    }

    /**
     * Handle the BlogCategory "force deleted" event.
     *
     * @param  \Core\Blog\Models\BlogCategory  $blogCategory
     * @return void
     */
    public function forceDeleted(BlogCategory $blogCategory)
    {
        //
    }
}
