<?php

namespace Core\Pages\Observers;

use Core\Pages\Models\Feature;

class FeatureObserver
{
    /**
     * Handle the Feature "creating" event.
     *
     * @param  \Core\Pages\Models\Feature  $feature
     * @return void
     */
    public function creating(Feature $feature)
    {
    
    }
    /**
     * Handle the Feature "created" event.
     *
     * @param  \Core\Pages\Models\Feature  $feature
     * @return void
     */
    public function created(Feature $feature)
    {
    
    }

    /**
     * Handle the Feature "updating" event.
     *
     * @param  \Core\Pages\Models\Feature  $feature
     * @return void
     */
    public function updating(Feature $feature)
    {

    }
    /**
     * Handle the Feature "updated" event.
     *
     * @param  \Core\Pages\Models\Feature  $feature
     * @return void
     */
    public function updated(Feature $feature)
    {

    }
    /**
     * Handle the Feature "saving" event.
     *
     * @param  \Core\Pages\Models\Feature  $feature
     * @return void
     */
    public function saving(Feature $feature)
    {

    }
    /**
     * Handle the Feature "saved" event.
     *
     * @param  \Core\Pages\Models\Feature  $feature
     * @return void
     */
    public function saved(Feature $feature)
    {

    }

    /**
     * Handle the Feature "deleted" event.
     *
     * @param  \Core\Pages\Models\Feature  $feature
     * @return void
     */
    public function deleted(Feature $feature)
    {
      
    }

    /**
     * Handle the Feature "restored" event.
     *
     * @param  \Core\Pages\Models\Feature  $feature
     * @return void
     */
    public function restored(Feature $feature)
    {
        //
    }

    /**
     * Handle the Feature "force deleted" event.
     *
     * @param  \Core\Pages\Models\Feature  $feature
     * @return void
     */
    public function forceDeleted(Feature $feature)
    {
        //
    }
}
