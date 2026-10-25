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
            if ($orderTransaction->wasRecentlyCreated
                && in_array($orderTransaction->type, ['cash', 'wallet', 'points'], true)
                && (float) $orderTransaction->amount > 0) {
                $order = $orderTransaction->order->fresh();
                if ($order) {
                    $outbox = app(\Core\Orders\Services\AnalyticsOutboxService::class);
                    $paymentType = match ($orderTransaction->type) {
                        'cash' => 'cash',
                        'wallet' => 'wallet',
                        'points' => 'loyalty_points',
                    };
                    $outbox->record('payment_succeeded', $order, [
                        'payment_method' => $paymentType,
                        'amount' => round((float) $orderTransaction->amount, 2),
                        'payment_id' => (string) $orderTransaction->id,
                        'wallet_bucket' => $orderTransaction->walletTransaction?->wallet_bucket,
                    ], 'order-transaction:' . $orderTransaction->id);

                    if ($orderTransaction->type === 'points' && $orderTransaction->point) {
                        $outbox->record('loyalty_redeemed', $order, [
                            'points_redeemed' => (float) $orderTransaction->point->amount,
                            'loyalty_value_used' => round((float) $orderTransaction->amount, 2),
                            'loyalty_transaction_id' => (string) $orderTransaction->point_id,
                        ], 'loyalty-redemption:' . $orderTransaction->id);
                    }

                }
            }

            // Settlement refunds are negative ledger rows, so purchase readiness
            // must also be checked outside the positive-payment branch.
            if ($orderTransaction->wasRecentlyCreated) {
                $settledOrder = $orderTransaction->order->fresh();
                if ($settledOrder?->status === 'finished'
                    && \Core\Orders\Support\OrderPaymentMath::remainingToCollect($settledOrder->total_price, $settledOrder->paid) === 0.0
                    && \Core\Orders\Support\OrderPaymentMath::remainingForCustomer($settledOrder->total_price, $settledOrder->paid) === 0.0) {
                    $purchase = app(\Core\Orders\Services\PurchasePayloadBuilder::class)->build($settledOrder);
                    app(\Core\Orders\Services\AnalyticsOutboxService::class)->record(
                        'purchase', $settledOrder,
                        $purchase['ready'] ? $purchase['payload'] : ['_analytics_blocked_reason' => $purchase['reason']],
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
