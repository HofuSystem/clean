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

            // Cash rows are created by driver/technician completion endpoints,
            // where the operator confirms collection. Keep this idempotent by
            // keying the analytics event to the immutable ledger row.
            if ($orderTransaction->wasRecentlyCreated && $orderTransaction->type === 'cash') {
                $order = $orderTransaction->order->fresh();
                if ($order) {
                    $outbox = app(\Core\Orders\Services\AnalyticsOutboxService::class);
                    if ((float) $order->paid >= (float) $order->total_price) {
                        $purchase = app(\Core\Orders\Services\PurchasePayloadBuilder::class)->build($order);
                        $outbox->record(
                            'purchase',
                            $order,
                            $purchase['ready']
                                ? $purchase['payload']
                                : ['_analytics_blocked_reason' => $purchase['reason']],
                            'purchase'
                        );
                    }
                }
            }

            if ($orderTransaction->wasRecentlyCreated
                && in_array($orderTransaction->type, ['wallet', 'points'], true)
                && (float) $orderTransaction->amount > 0) {
                $order = $orderTransaction->order->fresh();
                if ($order && (float) $order->paid >= (float) $order->total_price) {
                    $purchase = app(\Core\Orders\Services\PurchasePayloadBuilder::class)->build($order);
                    app(\Core\Orders\Services\AnalyticsOutboxService::class)->record(
                        'purchase',
                        $order,
                        $purchase['ready']
                            ? $purchase['payload']
                            : ['_analytics_blocked_reason' => $purchase['reason']],
                        'purchase'
                    );
                }
            }
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
