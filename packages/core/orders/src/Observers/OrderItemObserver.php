<?php

namespace Core\Orders\Observers;

use Core\Orders\Models\OrderItem;
use Core\Orders\Services\OrderHistoryService;

class OrderItemObserver
{
    /**
     * Handle the OrderItem "creating" event.
     *
     * @param  \Core\Orders\Models\OrderItem  $orderItem
     * @return void
     */
    public function creating(OrderItem $orderItem) {}
    /**
     * Handle the OrderItem "created" event.
     *
     * @param  \Core\Orders\Models\OrderItem  $orderItem
     * @return void
     */
    public function created(OrderItem $orderItem)
    {
        $orderHistoryService = app(OrderHistoryService::class);
        $productData = json_decode($orderItem->product_data, true);
        $productName = $productData['name'] ?? $orderItem->product?->name ?? 'Product #' . $orderItem->product_id;
        $orderHistoryService->logItemAdded($orderItem->order_id, [
            'product_name' => $productName,
            'price' => $orderItem->product_price
        ]);
        
    }

    /**
     * Handle the OrderItem "updating" event.
     *
     * @param  \Core\Orders\Models\OrderItem  $orderItem
     * @return void
     */
    public function updating(OrderItem $orderItem) {}
    /**
     * Handle the OrderItem "updated" event.
     *
     * @param  \Core\Orders\Models\OrderItem  $orderItem
     * @return void
     */
    public function updated(OrderItem $orderItem)
    {
        $orderHistoryService = app(OrderHistoryService::class);
        if ($orderItem->isDirty('quantity')) {
            $productData = json_decode($orderItem->product_data, true);
            $productName = $productData['name'] ?? $record->product?->name ?? 'Product #' . $orderItem->product_id;
            $categoryName = ' | Category #' . $orderItem->product?->category?->name . " | Type #" . $orderItem->product?->subCategory?->name;
            $productName .= $categoryName;
            $orderHistoryService->logItemQuantityChanged(
                $orderItem->order_id,
                $orderItem->id,
                $productName,
                $orderItem->quantity,
                $orderItem->quantity
            );
        }
        if ($orderItem->isDirty('final_delete')) {
            $productData = json_decode($orderItem->product_data, true);
            $productName = $productData['name'] ?? $orderItem->product?->name ?? 'Product #' . $orderItem->product_id;
            $categoryName = ' | Category #' . $orderItem->product?->category?->name . " | Type #" . $orderItem->product?->subCategory?->name;
            $productName .= $categoryName;
            $orderHistoryService->logItemPermanentlyDeleted($orderItem->order_id, $orderItem->id, $productName);
        }
    }
    /**
     * Handle the OrderItem "saving" event.
     *
     * @param  \Core\Orders\Models\OrderItem  $orderItem
     * @return void
     */
    public function saving(OrderItem $orderItem) {}
    /**
     * Handle the OrderItem "saved" event.
     *
     * @param  \Core\Orders\Models\OrderItem  $orderItem
     * @return void
     */
    public function saved(OrderItem $orderItem) {}

    /**
     * Handle the OrderItem "deleted" event.
     *
     * @param  \Core\Orders\Models\OrderItem  $orderItem
     * @return void
     */
    public function deleted(OrderItem $orderItem)
    {
        $orderHistoryService = app(OrderHistoryService::class);
        if ($orderItem->order_id) {
            $productData = json_decode($orderItem->product_data, true);
            $productName = $productData['name'] ?? $orderItem->product?->name ?? 'Product #' . $orderItem->product_id;
            $categoryName = ' | Category #' . $orderItem->product?->category?->name ." | Type #" . $orderItem->product?->subCategory?->name;
            $productName .= $categoryName;
            $orderHistoryService->logItemDeleted($orderItem->order_id, $orderItem->id, $productName);
        }
    }

    /**
     * Handle the OrderItem "restored" event.
     *
     * @param  \Core\Orders\Models\OrderItem  $orderItem
     * @return void
     */
    public function restored(OrderItem $orderItem)
    {
        //
    }

    /**
     * Handle the OrderItem "force deleted" event.
     *
     * @param  \Core\Orders\Models\OrderItem  $orderItem
     * @return void
     */
    public function forceDeleted(OrderItem $orderItem)
    {
        //
    }
}
