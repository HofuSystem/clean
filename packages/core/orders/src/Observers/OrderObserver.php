<?php

namespace Core\Orders\Observers;

use Core\Notification\Services\TelegramNotificationService;
use Core\Orders\Models\Order;
use Core\Financials\Services\InvoiceService;
use Core\Orders\Services\OrderHistoryService;

class OrderObserver
{
    /**
     * Handle the Order "creating" event.
     *
     * @param  \Core\Orders\Models\Order  $order
     * @return void
     */
    public function creating(Order $order)
    {
    
    }
    /**
     * Handle the Order "created" event.
     *
     * @param  \Core\Orders\Models\Order  $order
     * @return void
     */
    public function created(Order $order)
    {
        app(\Core\Orders\Services\CartFollowUpsService::class)->checkAndMarkAsSale($order);
    }

    /**
     * Handle the Order "updating" event.
     *
     * @param  \Core\Orders\Models\Order  $order
     * @return void
     */
    public function updating(Order $order)
    {

    }
    /**
     * Handle the Order "updated" event.
     *
     * @param  \Core\Orders\Models\Order  $order
     * @return void
     */
    public function updated(Order $order)
    {
        if (! $order->wasChanged('status')) {
            return;
        }

        $eventName = match ($order->status) {
            'order_has_been_delivered_to_admin' => 'order_collected',
            'service_started', 'started' => 'order_processing',
            'delivered' => 'order_delivered',
            'canceled' => 'order_cancelled',
            default => null,
        };

        if ($eventName) {
            $parameters = [];
            if ($eventName === 'order_cancelled') {
                $rawReason = trim((string) $order->admin_cancel_reason);
                $knownReasons = array_unique(array_merge(
                    array_keys(\Core\Orders\Services\OrdersService::issues()),
                    array_values(\Core\Orders\Services\OrdersService::issues())
                ));
                // Cancellation text is free-form in the admin API and may contain
                // personal details. Send only a known reason category.
                $parameters['cancel_reason'] = in_array($rawReason, $knownReasons, true) ? $rawReason : 'other';
            }
            app(\Core\Orders\Services\AnalyticsOutboxService::class)->record(
                $eventName,
                $order,
                $parameters,
                'status:' . $order->status
            );
        }
    }
    /**
     * Handle the Order "saving" event.
     *
     * @param  \Core\Orders\Models\Order  $order
     * @return void
     */
    public function saving(Order $order)
    {
        if($order->isDirty('washer_cost') or $order->isDirty('lab_cost')){
            $order->total_cost = (float)($order->washer_cost ?? 0) + (float)($order->lab_cost ?? 0);
        }
    }
    /**
     * Handle the Order "saved" event.
     *
     * @param  \Core\Orders\Models\Order  $order
     * @return void
     */
    public function saved(Order $order)
    {
        if ($order->isDirty('operator_id')) {
            $operatorId = $order?->operator_id;
            $order?->orderRepresentatives()
            ->where('type','technical')
            ->whereHas('representative',function($representativeQuery) use($operatorId){
                $representativeQuery->where('operator_id','!=',$operatorId);
            })->update(['representative_id' => null]);
        }
        if($order->isDirty('status')){
            $orderHistoryService = app(OrderHistoryService::class);
            $orderHistoryService->logStatusChange($order->id, $order->getOriginal('status'), $order->status);
            if($order->status == 'finished'){
                $invoiceService = app(InvoiceService::class);
                $invoiceService->generateInvoice($order->id);
            }
        }
        if($order->isDirty('pay_type')){
            $orderHistoryService = app(OrderHistoryService::class);
            $orderHistoryService->logPayTypeChange($order->id, $order->getOriginal('pay_type'), $order->pay_type);
        }
        if($order->isDirty('washer_cost') or $order->isDirty('lab_cost')){
            $orderHistoryService = app(OrderHistoryService::class);
            $orderHistoryService->logProviderTotalChange($order->id, $order->getOriginal('total_cost'), $order->total_cost);
        } 
        if($order->isDirty('delivery_price')){
            $orderHistoryService = app(OrderHistoryService::class);
            $orderHistoryService->logDeliveryPriceChange($order->id, $order->getOriginal('delivery_price'), $order->delivery_price);
        }
    }

    /**
     * Handle the Order "deleted" event.
     *
     * @param  \Core\Orders\Models\Order  $order
     * @return void
     */
    public function deleted(Order $order)
    {
      
    }

    /**
     * Handle the Order "restored" event.
     *
     * @param  \Core\Orders\Models\Order  $order
     * @return void
     */
    public function restored(Order $order)
    {
        //
    }

    /**
     * Handle the Order "force deleted" event.
     *
     * @param  \Core\Orders\Models\Order  $order
     * @return void
     */
    public function forceDeleted(Order $order)
    {
        //
    }
}
