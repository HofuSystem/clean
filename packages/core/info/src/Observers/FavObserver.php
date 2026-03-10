<?php

namespace Core\Info\Observers;

use Core\Info\Models\Fav;

class FavObserver
{
    /**
     * Handle the Fav "creating" event.
     *
     * @param  \Core\Info\Models\Fav  $fav
     * @return void
     */
    public function creating(Fav $fav)
    {
    
    }
    /**
     * Handle the Fav "created" event.
     *
     * @param  \Core\Info\Models\Fav  $fav
     * @return void
     */
    public function created(Fav $fav)
    {
    
    }

    /**
     * Handle the Fav "updating" event.
     *
     * @param  \Core\Info\Models\Fav  $fav
     * @return void
     */
    public function updating(Fav $fav)
    {

    }
    /**
     * Handle the Fav "updated" event.
     *
     * @param  \Core\Info\Models\Fav  $fav
     * @return void
     */
    public function updated(Fav $fav)
    {

    }
    /**
     * Handle the Fav "saving" event.
     *
     * @param  \Core\Info\Models\Fav  $fav
     * @return void
     */
    public function saving(Fav $fav)
    {

    }
    /**
     * Handle the Fav "saved" event.
     *
     * @param  \Core\Info\Models\Fav  $fav
     * @return void
     */
    public function saved(Fav $fav)
    {

    }

    /**
     * Handle the Fav "deleted" event.
     *
     * @param  \Core\Info\Models\Fav  $fav
     * @return void
     */
    public function deleted(Fav $fav)
    {
      
    }

    /**
     * Handle the Fav "restored" event.
     *
     * @param  \Core\Info\Models\Fav  $fav
     * @return void
     */
    public function restored(Fav $fav)
    {
        //
    }

    /**
     * Handle the Fav "force deleted" event.
     *
     * @param  \Core\Info\Models\Fav  $fav
     * @return void
     */
    public function forceDeleted(Fav $fav)
    {
        //
    }
}
