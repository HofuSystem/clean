<?php

namespace Core\Info\Observers;

use Core\Info\Models\City;

class CityObserver
{
    /**
     * Handle the City "creating" event.
     *
     * @param  \Core\Info\Models\City  $city
     * @return void
     */
    public function creating(City $city)
    {
    
    }
    /**
     * Handle the City "created" event.
     *
     * @param  \Core\Info\Models\City  $city
     * @return void
     */
    public function created(City $city)
    {
    
    }

    /**
     * Handle the City "updating" event.
     *
     * @param  \Core\Info\Models\City  $city
     * @return void
     */
    public function updating(City $city)
    {

    }
    /**
     * Handle the City "updated" event.
     *
     * @param  \Core\Info\Models\City  $city
     * @return void
     */
    public function updated(City $city)
    {

    }
    /**
     * Handle the City "saving" event.
     *
     * @param  \Core\Info\Models\City  $city
     * @return void
     */
    public function saving(City $city)
    {

    }
    /**
     * Handle the City "saved" event.
     *
     * @param  \Core\Info\Models\City  $city
     * @return void
     */
    public function saved(City $city)
    {

    }

    /**
     * Handle the City "deleted" event.
     *
     * @param  \Core\Info\Models\City  $city
     * @return void
     */
    public function deleted(City $city)
    {
      
    }

    /**
     * Handle the City "restored" event.
     *
     * @param  \Core\Info\Models\City  $city
     * @return void
     */
    public function restored(City $city)
    {
        //
    }

    /**
     * Handle the City "force deleted" event.
     *
     * @param  \Core\Info\Models\City  $city
     * @return void
     */
    public function forceDeleted(City $city)
    {
        //
    }
}
