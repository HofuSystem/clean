<?php

namespace Core\Orders\Services;

use Core\Orders\Models\OrderHistory;
use Core\Orders\Models\Order;
use Illuminate\Support\Facades\Auth;

class OrderHistoryService
{
    /**
     * Log a change to an order
     *
     * @param int $orderId
     * @param string $actionType
     * @param string $notes
     * @param mixed $oldValue
     * @param mixed $newValue
     * @return OrderHistory
     */
    public function log(int $orderId, string $actionType, string $notes, $oldValue = null, $newValue = null): OrderHistory
    {
        $changedBy = $this->getChangedByData();
        
        return OrderHistory::create([
            'order_id' => $orderId,
            'action_type' => $actionType,
            'notes' => $notes,
            'changed_by' => $changedBy,
            'old_value' => $oldValue,
            'new_value' => $newValue,
        ]);
    }
    
    /**
     * Log status change
     */
    public function logStatusChange(int $orderId, $oldStatus, $newStatus)
    {
        if(!$oldStatus || !$newStatus){
            return;
        }
        $notes = trans('Order status changed from :old to :new', [
            'old' => trans($oldStatus,[],'ar'),
            'new' => trans($newStatus,[],'ar')
        ],'ar');
        
        return $this->log(
            $orderId,
            'status_changed',
            $notes,
            ['status' => $oldStatus],
            ['status' => $newStatus]
        );
    }
    
    /**
     * Log pay type change
     */
    public function logPayTypeChange(int $orderId, $oldPayType, $newPayType)
    {
        if(!$oldPayType || !$newPayType){
            return;
        }
        $notes = trans('Payment type changed from :old to :new', [
            'old' => trans($oldPayType,[],'ar'),
            'new' => trans($newPayType,[],'ar')
        ],'ar');
        
        return $this->log(
            $orderId,
            'pay_type_changed',
            $notes,
            ['pay_type' => $oldPayType],
            ['pay_type' => $newPayType]
        );
    }
    
    /**
     * Log provider total change
     */
    public function logProviderTotalChange(int $orderId, $oldTotal, $newTotal)
    {
        if(!$oldTotal || !$newTotal){
            return;
        }
        $notes = trans('Provider total changed from :old to :new', [
            'old' => $oldTotal,
            'new' => $newTotal
        ],'ar');
        
        return $this->log(
            $orderId,
            'provider_total_changed',
            $notes,
            ['provider_total' => $oldTotal],
            ['provider_total' => $newTotal]
        );
    }
    
    /**
     * Log delivery price change
     */
    public function logDeliveryPriceChange(int $orderId, $oldPrice, $newPrice)
    {
        if(!$oldPrice || !$newPrice){
            return;
        }
        $notes = trans('Delivery price changed from :old SAR to :new SAR', [
            'old' => $oldPrice,
            'new' => $newPrice
        ],'ar');
        
        return $this->log(
            $orderId,
            'delivery_price_changed',
            $notes,
            ['delivery_price' => $oldPrice],
            ['delivery_price' => $newPrice]
        );
    }
    
    /**
     * Log coupon change
     */
    public function logCouponChange(int $orderId, $oldCoupon, $newCoupon)
    {
        if(!$oldCoupon || !$newCoupon){
            return;
        }
        $oldCouponCode = is_array($oldCoupon) ? ($oldCoupon['code'] ?? 'None') : ($oldCoupon?->code ?? 'None');
        $newCouponCode = is_array($newCoupon) ? ($newCoupon['code'] ?? 'None') : ($newCoupon?->code ?? 'None');
        
        // Extract only code and title/name from coupons
        $oldCouponData = null;
        if ($oldCoupon) {
            $oldCouponData = [
                'code' => is_array($oldCoupon) ? ($oldCoupon['code'] ?? null) : $oldCoupon->code,
                'title' => is_array($oldCoupon) ? ($oldCoupon['title'] ?? $oldCoupon['name'] ?? null) : ($oldCoupon->title ?? $oldCoupon->name ?? null),
            ];
        }
        
        $newCouponData = null;
        if ($newCoupon) {
            $newCouponData = [
                'code' => is_array($newCoupon) ? ($newCoupon['code'] ?? null) : $newCoupon->code,
                'title' => is_array($newCoupon) ? ($newCoupon['title'] ?? $newCoupon['name'] ?? null) : ($newCoupon->title ?? $newCoupon->name ?? null),
            ];
        }
        
        $notes = trans('Applied coupon changed from :old to :new', [
            'old' => $oldCouponCode,
            'new' => $newCouponCode
        ],'ar');
        
        return $this->log(
            $orderId,
            'coupon_changed',
            $notes,
            $oldCouponData ? ['coupon' => $oldCouponData] : null,
            $newCouponData ? ['coupon' => $newCouponData] : null
        );
    }
    
    /**
     * Log representative change
     */
    public function logRepresentativeChange(int $orderId, string $type, $oldRepId, $newRepId, $oldRepName = null, $newRepName = null): OrderHistory
    {
        $notes = trans(':type representative changed from :old to :new', [
            'type' => trans($type,[],'ar'),
            'old' => $oldRepName ?? $oldRepId ?? 'لا يوجد',
            'new' => $newRepName ?? $newRepId ?? 'لا يوجد'
        ],'ar');
        
        return $this->log(
            $orderId,
            'representative_changed',
            $notes,
            ['type' => $type, 'representative_id' => $oldRepId, 'name' => $oldRepName],
            ['type' => $type, 'representative_id' => $newRepId, 'name' => $newRepName]
        );
    }
    
    /**
     * Log representative date/time change
     */
    public function logRepresentativeDateTimeChange(int $orderId, string $type, $oldDate, $newDate, $oldTime = null, $newTime = null): OrderHistory
    {
        $notes = trans(':type representative schedule changed', ['type' => trans($type,[],'ar')],'ar');
        
        return $this->log(
            $orderId,
            'representative_datetime_changed',
            $notes,
            ['type' => $type, 'date' => $oldDate, 'time' => $oldTime],
            ['type' => $type, 'date' => $newDate, 'time' => $newTime]
        );
    }
    
    /**
     * Log representative location change
     */
    public function logRepresentativeLocationChange(int $orderId, string $type, $oldLocation, $newLocation): OrderHistory
    {
        $notes = trans(':type representative location changed', ['type' => trans($type,[],'ar')],'ar');
        
        return $this->log(
            $orderId,
            'representative_location_changed',
            $notes,
            ['type' => $type, 'location' => $oldLocation],
            ['type' => $type, 'location' => $newLocation]
        );
    }
    
    /**
     * Log item added
     */
    public function logItemAdded(int $orderId, array $itemData): OrderHistory
    {
        $productName = $itemData['product_name'] ?? $itemData['product_id'] ?? 'Unknown';
        $quantity = $itemData['quantity'] ?? 1;
        
        $notes = trans('Item added: :product (Qty: :qty)', [
            'product' => $productName,
            'qty' => $quantity
        ],'ar');
        
        return $this->log(
            $orderId,
            'item_added',
            $notes,
            null,
            $itemData
        );
    }
    
    /**
     * Log item quantity changed
     */
    public function logItemQuantityChanged(int $orderId, $itemId, $productName, $oldQty, $newQty): OrderHistory
    {
        $notes = trans('Item quantity changed for :product from :old to :new', [
            'product' => $productName,
            'old' => $oldQty,
            'new' => $newQty
        ],'ar');
        
        return $this->log(
            $orderId,
            'item_quantity_changed',
            $notes,
            ['product_name' => $productName, 'item_id' => $itemId, 'quantity' => $oldQty],
            ['product_name' => $productName, 'item_id' => $itemId, 'quantity' => $newQty]
        );
    }
    
    /**
     * Log item deleted (soft delete)
     */
    public function logItemDeleted(int $orderId, $itemId, $productName): OrderHistory
    {
        $notes = trans('Item deleted: :product', ['product' => $productName],'ar');
        
        return $this->log(
            $orderId,
            'item_deleted',
            $notes,
            ['item_id' => $itemId, 'product_name' => $productName],
            null
        );
    }
    
    /**
     * Log item permanently deleted
     */
    public function logItemPermanentlyDeleted(int $orderId, $itemId, $productName): OrderHistory
    {
        $notes = trans('Item permanently deleted: :product', ['product' => $productName],'ar');
        
        return $this->log(
            $orderId,
            'item_permanently_deleted',
            $notes,
            ['item_id' => $itemId, 'product_name' => $productName],
            null
        );
    }
    
    /**
     * Get the current user data for changed_by field
     */
    private function getChangedByData(): ?array
    {
        $user = Auth::guard('web')->user();
        
        if (!$user) {
            $user = Auth::guard('api')->user() ;
        }
        if (!$user){
            return null;
        }
        return [
            'user_id' => $user->id,
            'name' => $user->fullname ?? $user->email,
            'email' => $user->email,
            'phone' => $user->phone ?? null,
        ];
    }
    
    /**
     * Get history for an order
     */
    public function getOrderHistory(int $orderId)
    {
        return OrderHistory::where('order_id', $orderId)
            ->orderByDesc('created_at')
            ->get();
    }
}

