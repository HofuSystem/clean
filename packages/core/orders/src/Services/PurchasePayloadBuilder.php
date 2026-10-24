<?php

namespace Core\Orders\Services;

use Core\Orders\Models\Order;

class PurchasePayloadBuilder
{
    private const VAT_RATE = 0.15;

    /**
     * Calculate the purchase snapshot from persisted order/item values only.
     * A blocked result is preferable to sending a guessed financial payload.
     */
    public function build(Order $order): array
    {
        $items = $order->items()->with('product')->get();
        if ($items->isEmpty()) {
            return ['ready' => false, 'reason' => 'purchase_missing_items'];
        }

        $grossItemsTotal = (float) $order->order_price;
        $itemsGrossSum = (float) $items->sum(fn ($item) => (float) $item->total_price);
        if ($grossItemsTotal <= 0 || abs($grossItemsTotal - $itemsGrossSum) > 0.01) {
            return ['ready' => false, 'reason' => 'purchase_item_total_mismatch'];
        }

        $couponDiscount = (float) ($order->total_coupon ?? 0);
        if ($couponDiscount < 0 || $couponDiscount > $grossItemsTotal) {
            return ['ready' => false, 'reason' => 'purchase_invalid_discount'];
        }

        $netItemsValue = round(($grossItemsTotal - $couponDiscount) / (1 + self::VAT_RATE), 2);
        $tax = round(($grossItemsTotal - $couponDiscount) - $netItemsValue, 2);
        $remainingDiscount = $couponDiscount;
        $purchaseItems = [];
        $lastIndex = $items->count() - 1;

        foreach ($items->values() as $index => $item) {
            $lineGross = (float) $item->total_price;
            $lineDiscount = $index === $lastIndex
                ? $remainingDiscount
                : ($grossItemsTotal > 0 ? $couponDiscount * $lineGross / $grossItemsTotal : 0);
            $remainingDiscount -= $lineDiscount;
            $lineNet = max(0, $lineGross - $lineDiscount) / (1 + self::VAT_RATE);

            $quantity = (float) $item->quantity;
            if ((float) $item->width > 0 && (float) $item->height > 0) {
                $quantity *= (float) $item->width * (float) $item->height;
            }
            if ($quantity <= 0) {
                return ['ready' => false, 'reason' => 'purchase_invalid_item_quantity'];
            }

            $productData = is_array($item->product_data)
                ? $item->product_data
                : (json_decode((string) $item->product_data, true) ?: []);
            $name = $item->product_name
                ?: ($productData['name'] ?? $item->product?->name ?? null)
                ?: 'Product #' . ($item->product_id ?? $item->id);

            $purchaseItems[] = [
                'item_id' => (string) ($item->product_id ?? $item->id),
                'item_name' => mb_substr((string) $name, 0, 100),
                'price' => round($lineNet / $quantity, 6),
                'quantity' => $quantity,
                'discount' => round(($lineDiscount / (1 + self::VAT_RATE)) / $quantity, 6),
            ];
        }

        $walletTransactions = \Core\Wallet\Models\WalletTransaction::query()
            ->where('order_id', $order->id)
            ->where('type', 'withdraw')
            ->where('status', 'accepted')
            ->get(['amount', 'wallet_bucket']);
        $walletUsedByBucket = ['funded' => 0.0, 'promotional' => 0.0, 'unclassified' => 0.0];
        foreach ($walletTransactions as $walletTransaction) {
            $bucket = in_array($walletTransaction->wallet_bucket, ['funded', 'promotional', 'unclassified'], true)
                ? $walletTransaction->wallet_bucket
                : 'unclassified';
            $walletUsedByBucket[$bucket] += abs((float) $walletTransaction->amount);
        }
        $fundedWalletUsed = round($walletUsedByBucket['funded'], 2);
        $promoWalletUsed = round($walletUsedByBucket['promotional'], 2);
        $unclassifiedWalletUsed = round($walletUsedByBucket['unclassified'], 2);
        $loyaltyValueUsed = round((float) ($order->points_amount_used ?? 0), 2);
        $payload = [
            'value' => $netItemsValue,
            'tax' => $tax,
            'shipping' => round((float) ($order->delivery_price ?? 0), 2),
            'items' => $purchaseItems,
            'funded_wallet_used' => $fundedWalletUsed,
            'promo_wallet_used' => $promoWalletUsed,
            'unclassified_wallet_used' => $unclassifiedWalletUsed,
            'loyalty_value_used' => $loyaltyValueUsed,
            // Keep legacy names while schema-v2 consumers migrate.
            'wallet_paid_amount' => round((float) ($order->wallet_amount_used ?? 0), 2),
            'wallet_promo_amount' => $promoWalletUsed,
            'loyalty_points_used' => (bool) $order->points_used ? (float) ($order->points_amount ?? 0) : 0,
        ];

        if (abs(($payload['value'] + $payload['tax'] + $payload['shipping']) - (float) $order->total_price) > 0.01) {
            return ['ready' => false, 'reason' => 'purchase_total_reconciliation_failed'];
        }

        return ['ready' => true, 'payload' => $payload];
    }
}
