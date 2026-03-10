<?php

namespace Core\Categories\Observers;

use Core\Categories\Models\Price;

class PriceObserver
{
    /**
     * Handle the Price "creating" event.
     *
     * @param  \Core\Categories\Models\Price  $price
     * @return void
     */
    public function creating(Price $price)
    {
    
    }
    /**
     * Handle the Price "created" event.
     *
     * @param  \Core\Categories\Models\Price  $price
     * @return void
     */
    public function created(Price $price)
    {
    
    }

    /**
     * Handle the Price "updating" event.
     *
     * @param  \Core\Categories\Models\Price  $price
     * @return void
     */
    public function updating(Price $price)
    {

    }
    /**
     * Handle the Price "updated" event.
     *
     * @param  \Core\Categories\Models\Price  $price
     * @return void
     */
    public function updated(Price $price)
    {

    }
    /**
     * Handle the Price "saving" event.
     *
     * @param  \Core\Categories\Models\Price  $price
     * @return void
     */
    public function saving(Price $price)
    {

    }
    /**
     * Handle the Price "saved" event.
     *
     * @param  \Core\Categories\Models\Price  $price
     * @return void
     */
    public function saved(Price $price)
    {

    }

    /**
     * Handle the Price "deleted" event.
     *
     * @param  \Core\Categories\Models\Price  $price
     * @return void
     */
    public function deleted(Price $price)
    {
      
    }

    /**
     * Handle the Price "restored" event.
     *
     * @param  \Core\Categories\Models\Price  $price
     * @return void
     */
    public function restored(Price $price)
    {
        //
    }

    /**
     * Handle the Price "force deleted" event.
     *
     * @param  \Core\Categories\Models\Price  $price
     * @return void
     */
    public function forceDeleted(Price $price)
    {
        //
    }
}
