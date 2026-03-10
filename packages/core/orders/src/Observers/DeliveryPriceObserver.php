<?php

namespace Core\Orders\Observers;

use Core\Orders\Models\DeliveryPrice;

class DeliveryPriceObserver
{
    /**
     * Handle the DeliveryPrice "creating" event.
     *
     * @param  \Core\Orders\Models\DeliveryPrice  $DeliveryPrice
     * @return void
     */
    public function creating(DeliveryPrice $DeliveryPrice)
    {
    
    }
    /**
     * Handle the DeliveryPrice "created" event.
     *
     * @param  \Core\Orders\Models\DeliveryPrice  $DeliveryPrice
     * @return void
     */
    public function created(DeliveryPrice $DeliveryPrice)
    {
    
    }

    /**
     * Handle the DeliveryPrice "updating" event.
     *
     * @param  \Core\Orders\Models\DeliveryPrice  $DeliveryPrice
     * @return void
     */
    public function updating(DeliveryPrice $DeliveryPrice)
    {

    }
    /**
     * Handle the DeliveryPrice "updated" event.
     *
     * @param  \Core\Orders\Models\DeliveryPrice  $DeliveryPrice
     * @return void
     */
    public function updated(DeliveryPrice $DeliveryPrice)
    {

    }
    /**
     * Handle the DeliveryPrice "saving" event.
     *
     * @param  \Core\Orders\Models\DeliveryPrice  $DeliveryPrice
     * @return void
     */
    public function saving(DeliveryPrice $DeliveryPrice)
    {

    }
    /**
     * Handle the DeliveryPrice "saved" event.
     *
     * @param  \Core\Orders\Models\DeliveryPrice  $DeliveryPrice
     * @return void
     */
    public function saved(DeliveryPrice $DeliveryPrice)
    {

    }

    /**
     * Handle the DeliveryPrice "deleted" event.
     *
     * @param  \Core\Orders\Models\DeliveryPrice  $DeliveryPrice
     * @return void
     */
    public function deleted(DeliveryPrice $DeliveryPrice)
    {
      
    }

    /**
     * Handle the DeliveryPrice "restored" event.
     *
     * @param  \Core\Orders\Models\DeliveryPrice  $DeliveryPrice
     * @return void
     */
    public function restored(DeliveryPrice $DeliveryPrice)
    {
        //
    }

    /**
     * Handle the DeliveryPrice "force deleted" event.
     *
     * @param  \Core\Orders\Models\DeliveryPrice  $DeliveryPrice
     * @return void
     */
    public function forceDeleted(DeliveryPrice $DeliveryPrice)
    {
        //
    }
}
