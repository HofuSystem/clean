<?php

namespace Core\Blog\Observers;

use Core\Blog\Models\Blog;

class BlogObserver
{
    /**
     * Handle the Blog "creating" event.
     *
     * @param  \Core\Blog\Models\Blog  $blog
     * @return void
     */
    public function creating(Blog $blog)
    {
    
    }
    /**
     * Handle the Blog "created" event.
     *
     * @param  \Core\Blog\Models\Blog  $blog
     * @return void
     */
    public function created(Blog $blog)
    {
    
    }

    /**
     * Handle the Blog "updating" event.
     *
     * @param  \Core\Blog\Models\Blog  $blog
     * @return void
     */
    public function updating(Blog $blog)
    {

    }
    /**
     * Handle the Blog "updated" event.
     *
     * @param  \Core\Blog\Models\Blog  $blog
     * @return void
     */
    public function updated(Blog $blog)
    {

    }
    /**
     * Handle the Blog "saving" event.
     *
     * @param  \Core\Blog\Models\Blog  $blog
     * @return void
     */
    public function saving(Blog $blog)
    {

    }
    /**
     * Handle the Blog "saved" event.
     *
     * @param  \Core\Blog\Models\Blog  $blog
     * @return void
     */
    public function saved(Blog $blog)
    {

    }

    /**
     * Handle the Blog "deleted" event.
     *
     * @param  \Core\Blog\Models\Blog  $blog
     * @return void
     */
    public function deleted(Blog $blog)
    {
      
    }

    /**
     * Handle the Blog "restored" event.
     *
     * @param  \Core\Blog\Models\Blog  $blog
     * @return void
     */
    public function restored(Blog $blog)
    {
        //
    }

    /**
     * Handle the Blog "force deleted" event.
     *
     * @param  \Core\Blog\Models\Blog  $blog
     * @return void
     */
    public function forceDeleted(Blog $blog)
    {
        //
    }
}
