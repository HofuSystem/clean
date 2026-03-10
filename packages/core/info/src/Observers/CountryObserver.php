<?php

namespace Core\Info\Observers;

use Core\Info\Models\Country;

class CountryObserver
{
    /**
     * Handle the Country "creating" event.
     *
     * @param  \Core\Info\Models\Country  $country
     * @return void
     */
    public function creating(Country $country)
    {
    
    }
    /**
     * Handle the Country "created" event.
     *
     * @param  \Core\Info\Models\Country  $country
     * @return void
     */
    public function created(Country $country)
    {
    
    }

    /**
     * Handle the Country "updating" event.
     *
     * @param  \Core\Info\Models\Country  $country
     * @return void
     */
    public function updating(Country $country)
    {

    }
    /**
     * Handle the Country "updated" event.
     *
     * @param  \Core\Info\Models\Country  $country
     * @return void
     */
    public function updated(Country $country)
    {

    }
    /**
     * Handle the Country "saving" event.
     *
     * @param  \Core\Info\Models\Country  $country
     * @return void
     */
    public function saving(Country $country)
    {

    }
    /**
     * Handle the Country "saved" event.
     *
     * @param  \Core\Info\Models\Country  $country
     * @return void
     */
    public function saved(Country $country)
    {

    }

    /**
     * Handle the Country "deleted" event.
     *
     * @param  \Core\Info\Models\Country  $country
     * @return void
     */
    public function deleted(Country $country)
    {
      
    }

    /**
     * Handle the Country "restored" event.
     *
     * @param  \Core\Info\Models\Country  $country
     * @return void
     */
    public function restored(Country $country)
    {
        //
    }

    /**
     * Handle the Country "force deleted" event.
     *
     * @param  \Core\Info\Models\Country  $country
     * @return void
     */
    public function forceDeleted(Country $country)
    {
        //
    }
}
