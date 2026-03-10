<?php

namespace Core\Orders\Observers;

use Core\Orders\Models\OrderItemQtyUpdate;

class OrderItemQtyUpdateObserver
{
    /**
     * Handle the OrderItemQtyUpdate "creating" event.
     *
     * @param  \Core\Orders\Models\OrderItemQtyUpdate  $orderItemQtyUpdate
     * @return void
     */
    public function creating(OrderItemQtyUpdate $orderItemQtyUpdate)
    {
    
    }
    /**
     * Handle the OrderItemQtyUpdate "created" event.
     *
     * @param  \Core\Orders\Models\OrderItemQtyUpdate  $orderItemQtyUpdate
     * @return void
     */
    public function created(OrderItemQtyUpdate $orderItemQtyUpdate)
    {
    
    }

    /**
     * Handle the OrderItemQtyUpdate "updating" event.
     *
     * @param  \Core\Orders\Models\OrderItemQtyUpdate  $orderItemQtyUpdate
     * @return void
     */
    public function updating(OrderItemQtyUpdate $orderItemQtyUpdate)
    {

    }
    /**
     * Handle the OrderItemQtyUpdate "updated" event.
     *
     * @param  \Core\Orders\Models\OrderItemQtyUpdate  $orderItemQtyUpdate
     * @return void
     */
    public function updated(OrderItemQtyUpdate $orderItemQtyUpdate)
    {

    }
    /**
     * Handle the OrderItemQtyUpdate "saving" event.
     *
     * @param  \Core\Orders\Models\OrderItemQtyUpdate  $orderItemQtyUpdate
     * @return void
     */
    public function saving(OrderItemQtyUpdate $orderItemQtyUpdate)
    {

    }
    /**
     * Handle the OrderItemQtyUpdate "saved" event.
     *
     * @param  \Core\Orders\Models\OrderItemQtyUpdate  $orderItemQtyUpdate
     * @return void
     */
    public function saved(OrderItemQtyUpdate $orderItemQtyUpdate)
    {

    }

    /**
     * Handle the OrderItemQtyUpdate "deleted" event.
     *
     * @param  \Core\Orders\Models\OrderItemQtyUpdate  $orderItemQtyUpdate
     * @return void
     */
    public function deleted(OrderItemQtyUpdate $orderItemQtyUpdate)
    {
      
    }

    /**
     * Handle the OrderItemQtyUpdate "restored" event.
     *
     * @param  \Core\Orders\Models\OrderItemQtyUpdate  $orderItemQtyUpdate
     * @return void
     */
    public function restored(OrderItemQtyUpdate $orderItemQtyUpdate)
    {
        //
    }

    /**
     * Handle the OrderItemQtyUpdate "force deleted" event.
     *
     * @param  \Core\Orders\Models\OrderItemQtyUpdate  $orderItemQtyUpdate
     * @return void
     */
    public function forceDeleted(OrderItemQtyUpdate $orderItemQtyUpdate)
    {
        //
    }
}
