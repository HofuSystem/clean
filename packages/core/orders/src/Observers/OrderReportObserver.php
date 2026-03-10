<?php

namespace Core\Orders\Observers;

use Core\Orders\Models\OrderReport;

class OrderReportObserver
{
    /**
     * Handle the OrderReport "creating" event.
     *
     * @param  \Core\Orders\Models\OrderReport  $orderReport
     * @return void
     */
    public function creating(OrderReport $orderReport)
    {
    
    }
    /**
     * Handle the OrderReport "created" event.
     *
     * @param  \Core\Orders\Models\OrderReport  $orderReport
     * @return void
     */
    public function created(OrderReport $orderReport)
    {
    
    }

    /**
     * Handle the OrderReport "updating" event.
     *
     * @param  \Core\Orders\Models\OrderReport  $orderReport
     * @return void
     */
    public function updating(OrderReport $orderReport)
    {

    }
    /**
     * Handle the OrderReport "updated" event.
     *
     * @param  \Core\Orders\Models\OrderReport  $orderReport
     * @return void
     */
    public function updated(OrderReport $orderReport)
    {

    }
    /**
     * Handle the OrderReport "saving" event.
     *
     * @param  \Core\Orders\Models\OrderReport  $orderReport
     * @return void
     */
    public function saving(OrderReport $orderReport)
    {

    }
    /**
     * Handle the OrderReport "saved" event.
     *
     * @param  \Core\Orders\Models\OrderReport  $orderReport
     * @return void
     */
    public function saved(OrderReport $orderReport)
    {

    }

    /**
     * Handle the OrderReport "deleted" event.
     *
     * @param  \Core\Orders\Models\OrderReport  $orderReport
     * @return void
     */
    public function deleted(OrderReport $orderReport)
    {
      
    }

    /**
     * Handle the OrderReport "restored" event.
     *
     * @param  \Core\Orders\Models\OrderReport  $orderReport
     * @return void
     */
    public function restored(OrderReport $orderReport)
    {
        //
    }

    /**
     * Handle the OrderReport "force deleted" event.
     *
     * @param  \Core\Orders\Models\OrderReport  $orderReport
     * @return void
     */
    public function forceDeleted(OrderReport $orderReport)
    {
        //
    }
}
