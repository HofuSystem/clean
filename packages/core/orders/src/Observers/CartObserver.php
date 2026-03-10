<?php

namespace Core\Orders\Observers;

use Core\Orders\Models\Cart;

class CartObserver
{
    /**
     * Handle the Cart "creating" event.
     *
     * @param  \Core\Orders\Models\Cart  $cart
     * @return void
     */
    public function creating(Cart $cart)
    {
    
    }
    /**
     * Handle the Cart "created" event.
     *
     * @param  \Core\Orders\Models\Cart  $cart
     * @return void
     */
    public function created(Cart $cart)
    {
    
    }

    /**
     * Handle the Cart "updating" event.
     *
     * @param  \Core\Orders\Models\Cart  $cart
     * @return void
     */
    public function updating(Cart $cart)
    {

    }
    /**
     * Handle the Cart "updated" event.
     *
     * @param  \Core\Orders\Models\Cart  $cart
     * @return void
     */
    public function updated(Cart $cart)
    {

    }
    /**
     * Handle the Cart "saving" event.
     *
     * @param  \Core\Orders\Models\Cart  $cart
     * @return void
     */
    public function saving(Cart $cart)
    {

    }
    /**
     * Handle the Cart "saved" event.
     *
     * @param  \Core\Orders\Models\Cart  $cart
     * @return void
     */
    public function saved(Cart $cart)
    {

    }

    /**
     * Handle the Cart "deleted" event.
     *
     * @param  \Core\Orders\Models\Cart  $cart
     * @return void
     */
    public function deleted(Cart $cart)
    {
      
    }

    /**
     * Handle the Cart "restored" event.
     *
     * @param  \Core\Orders\Models\Cart  $cart
     * @return void
     */
    public function restored(Cart $cart)
    {
        //
    }

    /**
     * Handle the Cart "force deleted" event.
     *
     * @param  \Core\Orders\Models\Cart  $cart
     * @return void
     */
    public function forceDeleted(Cart $cart)
    {
        //
    }
}
