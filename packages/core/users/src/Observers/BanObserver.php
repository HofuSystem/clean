<?php

namespace Core\Users\Observers;

use Core\Users\Models\Ban;

class BanObserver
{
    /**
     * Handle the Ban "creating" event.
     *
     * @param  \Core\Users\Models\Ban  $ban
     * @return void
     */
    public function creating(Ban $ban)
    {
    
    }
    /**
     * Handle the Ban "created" event.
     *
     * @param  \Core\Users\Models\Ban  $ban
     * @return void
     */
    public function created(Ban $ban)
    {
    
    }

    /**
     * Handle the Ban "updating" event.
     *
     * @param  \Core\Users\Models\Ban  $ban
     * @return void
     */
    public function updating(Ban $ban)
    {

    }
    /**
     * Handle the Ban "updated" event.
     *
     * @param  \Core\Users\Models\Ban  $ban
     * @return void
     */
    public function updated(Ban $ban)
    {

    }
    /**
     * Handle the Ban "saving" event.
     *
     * @param  \Core\Users\Models\Ban  $ban
     * @return void
     */
    public function saving(Ban $ban)
    {

    }
    /**
     * Handle the Ban "saved" event.
     *
     * @param  \Core\Users\Models\Ban  $ban
     * @return void
     */
    public function saved(Ban $ban)
    {

    }

    /**
     * Handle the Ban "deleted" event.
     *
     * @param  \Core\Users\Models\Ban  $ban
     * @return void
     */
    public function deleted(Ban $ban)
    {
      
    }

    /**
     * Handle the Ban "restored" event.
     *
     * @param  \Core\Users\Models\Ban  $ban
     * @return void
     */
    public function restored(Ban $ban)
    {
        //
    }

    /**
     * Handle the Ban "force deleted" event.
     *
     * @param  \Core\Users\Models\Ban  $ban
     * @return void
     */
    public function forceDeleted(Ban $ban)
    {
        //
    }
}
