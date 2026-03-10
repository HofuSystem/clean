<?php

namespace Core\Pages\Observers;

use Core\Pages\Models\Page;

class PageObserver
{
    /**
     * Handle the Page "creating" event.
     *
     * @param  \Core\Pages\Models\Page  $page
     * @return void
     */
    public function creating(Page $page)
    {
    
    }
    /**
     * Handle the Page "created" event.
     *
     * @param  \Core\Pages\Models\Page  $page
     * @return void
     */
    public function created(Page $page)
    {
    
    }

    /**
     * Handle the Page "updating" event.
     *
     * @param  \Core\Pages\Models\Page  $page
     * @return void
     */
    public function updating(Page $page)
    {

    }
    /**
     * Handle the Page "updated" event.
     *
     * @param  \Core\Pages\Models\Page  $page
     * @return void
     */
    public function updated(Page $page)
    {

    }
    /**
     * Handle the Page "saving" event.
     *
     * @param  \Core\Pages\Models\Page  $page
     * @return void
     */
    public function saving(Page $page)
    {

    }
    /**
     * Handle the Page "saved" event.
     *
     * @param  \Core\Pages\Models\Page  $page
     * @return void
     */
    public function saved(Page $page)
    {

    }

    /**
     * Handle the Page "deleted" event.
     *
     * @param  \Core\Pages\Models\Page  $page
     * @return void
     */
    public function deleted(Page $page)
    {
      
    }

    /**
     * Handle the Page "restored" event.
     *
     * @param  \Core\Pages\Models\Page  $page
     * @return void
     */
    public function restored(Page $page)
    {
        //
    }

    /**
     * Handle the Page "force deleted" event.
     *
     * @param  \Core\Pages\Models\Page  $page
     * @return void
     */
    public function forceDeleted(Page $page)
    {
        //
    }
}
