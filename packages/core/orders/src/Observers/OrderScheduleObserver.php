<?php

namespace Core\Orders\Observers;

use Core\Orders\Models\OrderSchedule;

class OrderScheduleObserver
{
    /**
     * Handle the OrderSchedule "creating" event.
     *
     * @param  \Core\Orders\Models\OrderSchedule  $orderSchedule
     * @return void
     */
    public function creating(OrderSchedule $orderSchedule)
    {
    
    }
    /**
     * Handle the OrderSchedule "created" event.
     *
     * @param  \Core\Orders\Models\OrderSchedule  $orderSchedule
     * @return void
     */
    public function created(OrderSchedule $orderSchedule)
    {
    
    }

    /**
     * Handle the OrderSchedule "updating" event.
     *
     * @param  \Core\Orders\Models\OrderSchedule  $orderSchedule
     * @return void
     */
    public function updating(OrderSchedule $orderSchedule)
    {

    }
    /**
     * Handle the OrderSchedule "updated" event.
     *
     * @param  \Core\Orders\Models\OrderSchedule  $orderSchedule
     * @return void
     */
    public function updated(OrderSchedule $orderSchedule)
    {

    }
    /**
     * Handle the OrderSchedule "saving" event.
     *
     * @param  \Core\Orders\Models\OrderSchedule  $orderSchedule
     * @return void
     */
    public function saving(OrderSchedule $orderSchedule)
    {

    }
    /**
     * Handle the OrderSchedule "saved" event.
     *
     * @param  \Core\Orders\Models\OrderSchedule  $orderSchedule
     * @return void
     */
    public function saved(OrderSchedule $orderSchedule)
    {

    }

    /**
     * Handle the OrderSchedule "deleted" event.
     *
     * @param  \Core\Orders\Models\OrderSchedule  $orderSchedule
     * @return void
     */
    public function deleted(OrderSchedule $orderSchedule)
    {
      
    }

    /**
     * Handle the OrderSchedule "restored" event.
     *
     * @param  \Core\Orders\Models\OrderSchedule  $orderSchedule
     * @return void
     */
    public function restored(OrderSchedule $orderSchedule)
    {
        //
    }

    /**
     * Handle the OrderSchedule "force deleted" event.
     *
     * @param  \Core\Orders\Models\OrderSchedule  $orderSchedule
     * @return void
     */
    public function forceDeleted(OrderSchedule $orderSchedule)
    {
        //
    }
}
