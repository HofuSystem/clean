<?php

namespace Core\Info\Observers;

use Core\Info\Models\Nationality;

class NationalityObserver
{
    /**
     * Handle the Nationality "creating" event.
     *
     * @param  \Core\Info\Models\Nationality  $nationality
     * @return void
     */
    public function creating(Nationality $nationality)
    {
    
    }
    /**
     * Handle the Nationality "created" event.
     *
     * @param  \Core\Info\Models\Nationality  $nationality
     * @return void
     */
    public function created(Nationality $nationality)
    {
    
    }

    /**
     * Handle the Nationality "updating" event.
     *
     * @param  \Core\Info\Models\Nationality  $nationality
     * @return void
     */
    public function updating(Nationality $nationality)
    {

    }
    /**
     * Handle the Nationality "updated" event.
     *
     * @param  \Core\Info\Models\Nationality  $nationality
     * @return void
     */
    public function updated(Nationality $nationality)
    {

    }
    /**
     * Handle the Nationality "saving" event.
     *
     * @param  \Core\Info\Models\Nationality  $nationality
     * @return void
     */
    public function saving(Nationality $nationality)
    {

    }
    /**
     * Handle the Nationality "saved" event.
     *
     * @param  \Core\Info\Models\Nationality  $nationality
     * @return void
     */
    public function saved(Nationality $nationality)
    {

    }

    /**
     * Handle the Nationality "deleted" event.
     *
     * @param  \Core\Info\Models\Nationality  $nationality
     * @return void
     */
    public function deleted(Nationality $nationality)
    {
      
    }

    /**
     * Handle the Nationality "restored" event.
     *
     * @param  \Core\Info\Models\Nationality  $nationality
     * @return void
     */
    public function restored(Nationality $nationality)
    {
        //
    }

    /**
     * Handle the Nationality "force deleted" event.
     *
     * @param  \Core\Info\Models\Nationality  $nationality
     * @return void
     */
    public function forceDeleted(Nationality $nationality)
    {
        //
    }
}
