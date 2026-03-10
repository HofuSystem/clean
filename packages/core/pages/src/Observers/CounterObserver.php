<?php

namespace Core\Pages\Observers;

use Core\Pages\Models\Counter;

class CounterObserver
{
    /**
     * Handle the Counter "creating" event.
     *
     * @param  \Core\Pages\Models\Counter  $counter
     * @return void
     */
    public function creating(Counter $counter)
    {
    
    }
    /**
     * Handle the Counter "created" event.
     *
     * @param  \Core\Pages\Models\Counter  $counter
     * @return void
     */
    public function created(Counter $counter)
    {
    
    }

    /**
     * Handle the Counter "updating" event.
     *
     * @param  \Core\Pages\Models\Counter  $counter
     * @return void
     */
    public function updating(Counter $counter)
    {

    }
    /**
     * Handle the Counter "updated" event.
     *
     * @param  \Core\Pages\Models\Counter  $counter
     * @return void
     */
    public function updated(Counter $counter)
    {

    }
    /**
     * Handle the Counter "saving" event.
     *
     * @param  \Core\Pages\Models\Counter  $counter
     * @return void
     */
    public function saving(Counter $counter)
    {

    }
    /**
     * Handle the Counter "saved" event.
     *
     * @param  \Core\Pages\Models\Counter  $counter
     * @return void
     */
    public function saved(Counter $counter)
    {

    }

    /**
     * Handle the Counter "deleted" event.
     *
     * @param  \Core\Pages\Models\Counter  $counter
     * @return void
     */
    public function deleted(Counter $counter)
    {
      
    }

    /**
     * Handle the Counter "restored" event.
     *
     * @param  \Core\Pages\Models\Counter  $counter
     * @return void
     */
    public function restored(Counter $counter)
    {
        //
    }

    /**
     * Handle the Counter "force deleted" event.
     *
     * @param  \Core\Pages\Models\Counter  $counter
     * @return void
     */
    public function forceDeleted(Counter $counter)
    {
        //
    }
}
