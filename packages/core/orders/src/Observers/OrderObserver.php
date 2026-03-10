<?php

namespace Core\Orders\Observers;

use Core\Notification\Services\TelegramNotificationService;
use Core\Orders\Models\Order;
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

    }
    /**
     * Handle the Order "saving" event.
     *
     * @param  \Core\Orders\Models\Order  $order
     * @return void
     */
    public function saving(Order $order)
    {

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
        }
        if($order->isDirty('pay_type')){
            $orderHistoryService = app(OrderHistoryService::class);
            $orderHistoryService->logPayTypeChange($order->id, $order->getOriginal('pay_type'), $order->pay_type);
        }
       
        if($order->isDirty('total_provider_invoice')){
            $orderHistoryService = app(OrderHistoryService::class);
            $orderHistoryService->logProviderTotalChange($order->id, $order->getOriginal('total_provider_invoice'), $order->total_provider_invoice);
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
