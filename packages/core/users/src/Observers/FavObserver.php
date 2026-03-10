<?php

namespace Core\Users\Observers;

use Core\Users\Models\Fav;

class FavObserver
{
    /**
     * Handle the Fav "creating" event.
     *
     * @param  \Core\Users\Models\Fav  $fav
     * @return void
     */
    public function creating(Fav $fav)
    {
    
    }
    /**
     * Handle the Fav "created" event.
     *
     * @param  \Core\Users\Models\Fav  $fav
     * @return void
     */
    public function created(Fav $fav)
    {
    
    }

    /**
     * Handle the Fav "updating" event.
     *
     * @param  \Core\Users\Models\Fav  $fav
     * @return void
     */
    public function updating(Fav $fav)
    {

    }
    /**
     * Handle the Fav "updated" event.
     *
     * @param  \Core\Users\Models\Fav  $fav
     * @return void
     */
    public function updated(Fav $fav)
    {

    }
    /**
     * Handle the Fav "saving" event.
     *
     * @param  \Core\Users\Models\Fav  $fav
     * @return void
     */
    public function saving(Fav $fav)
    {

    }
    /**
     * Handle the Fav "saved" event.
     *
     * @param  \Core\Users\Models\Fav  $fav
     * @return void
     */
    public function saved(Fav $fav)
    {

    }

    /**
     * Handle the Fav "deleted" event.
     *
     * @param  \Core\Users\Models\Fav  $fav
     * @return void
     */
    public function deleted(Fav $fav)
    {
      
    }

    /**
     * Handle the Fav "restored" event.
     *
     * @param  \Core\Users\Models\Fav  $fav
     * @return void
     */
    public function restored(Fav $fav)
    {
        //
    }

    /**
     * Handle the Fav "force deleted" event.
     *
     * @param  \Core\Users\Models\Fav  $fav
     * @return void
     */
    public function forceDeleted(Fav $fav)
    {
        //
    }
}
