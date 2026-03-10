<?php

namespace Core\Orders\Observers;

use Core\Orders\Models\OrderTransaction;
use Core\Orders\Services\OrderTransactionsService;

class OrderTransactionObserver
{
    /**
     * Handle the OrderTransaction "creating" event.
     *
     * @param  \Core\Orders\Models\OrderTransaction  $orderTransaction
     * @return void
     */
    public function creating(OrderTransaction $orderTransaction)
    {
    
    }
    /**
     * Handle the OrderTransaction "created" event.
     *
     * @param  \Core\Orders\Models\OrderTransaction  $orderTransaction
     * @return void
     */
    public function created(OrderTransaction $orderTransaction)
    {
    
    }

    /**
     * Handle the OrderTransaction "updating" event.
     *
     * @param  \Core\Orders\Models\OrderTransaction  $orderTransaction
     * @return void
     */
    public function updating(OrderTransaction $orderTransaction)
    {

    }
    /**
     * Handle the OrderTransaction "updated" event.
     *
     * @param  \Core\Orders\Models\OrderTransaction  $orderTransaction
     * @return void
     */
    public function updated(OrderTransaction $orderTransaction)
    {

    }
    /**
     * Handle the OrderTransaction "saving" event.
     *
     * @param  \Core\Orders\Models\OrderTransaction  $orderTransaction
     * @return void
     */
    public function saving(OrderTransaction $orderTransaction)
    {
        
    }
    /**
     * Handle the OrderTransaction "saved" event.
     *
     * @param  \Core\Orders\Models\OrderTransaction  $orderTransaction
     * @return void
     */
    public function saved(OrderTransaction $orderTransaction)
    {
        if($orderTransaction->order){
            OrderTransactionsService::updateOrderPaid($orderTransaction->order);
        }
    }

    /**
     * Handle the OrderTransaction "deleted" event.
     *
     * @param  \Core\Orders\Models\OrderTransaction  $orderTransaction
     * @return void
     */
    public function deleted(OrderTransaction $orderTransaction)
    {
        if($orderTransaction->order){
            OrderTransactionsService::updateOrderPaid($orderTransaction->order);
        }
    }

    /**
     * Handle the OrderTransaction "restored" event.
     *
     * @param  \Core\Orders\Models\OrderTransaction  $orderTransaction
     * @return void
     */
    public function restored(OrderTransaction $orderTransaction)
    {
        if($orderTransaction->order){
            OrderTransactionsService::updateOrderPaid($orderTransaction->order);
        }
    }

    /**
     * Handle the OrderTransaction "force deleted" event.
     *
     * @param  \Core\Orders\Models\OrderTransaction  $orderTransaction
     * @return void
     */
    public function forceDeleted(OrderTransaction $orderTransaction)
    {
        if($orderTransaction->order){
            OrderTransactionsService::updateOrderPaid($orderTransaction->order);
        }
    }
}
