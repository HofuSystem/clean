<?php

namespace Core\Financials\Observers;

use Core\Financials\Models\OrderInvoice;

class OrderInvoiceObserver
{
    /**
     * Handle the OrderInvoice "creating" event.
     *
     * @param  \Core\Financials\Models\OrderInvoice  $orderInvoice
     * @return void
     */
    public function creating(OrderInvoice $orderInvoice)
    {
    
    }
    /**
     * Handle the OrderInvoice "created" event.
     *
     * @param  \Core\Financials\Models\OrderInvoice  $orderInvoice
     * @return void
     */
    public function created(OrderInvoice $orderInvoice)
    {
    
    }

    /**
     * Handle the OrderInvoice "updating" event.
     *
     * @param  \Core\Financials\Models\OrderInvoice  $orderInvoice
     * @return void
     */
    public function updating(OrderInvoice $orderInvoice)
    {

    }
    /**
     * Handle the OrderInvoice "updated" event.
     *
     * @param  \Core\Financials\Models\OrderInvoice  $orderInvoice
     * @return void
     */
    public function updated(OrderInvoice $orderInvoice)
    {

    }
    /**
     * Handle the OrderInvoice "saving" event.
     *
     * @param  \Core\Financials\Models\OrderInvoice  $orderInvoice
     * @return void
     */
    public function saving(OrderInvoice $orderInvoice)
    {

    }
    /**
     * Handle the OrderInvoice "saved" event.
     *
     * @param  \Core\Financials\Models\OrderInvoice  $orderInvoice
     * @return void
     */
    public function saved(OrderInvoice $orderInvoice)
    {

    }

    /**
     * Handle the OrderInvoice "deleted" event.
     *
     * @param  \Core\Financials\Models\OrderInvoice  $orderInvoice
     * @return void
     */
    public function deleted(OrderInvoice $orderInvoice)
    {
      
    }

    /**
     * Handle the OrderInvoice "restored" event.
     *
     * @param  \Core\Financials\Models\OrderInvoice  $orderInvoice
     * @return void
     */
    public function restored(OrderInvoice $orderInvoice)
    {
        //
    }

    /**
     * Handle the OrderInvoice "force deleted" event.
     *
     * @param  \Core\Financials\Models\OrderInvoice  $orderInvoice
     * @return void
     */
    public function forceDeleted(OrderInvoice $orderInvoice)
    {
        //
    }
}
