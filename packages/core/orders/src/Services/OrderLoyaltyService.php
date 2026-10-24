<?php

namespace Core\Orders\Services;

use Core\Orders\Models\Order;
use Core\Orders\Models\OrderLoyaltyAward;
use Core\Users\Models\Point;
use Core\Settings\Services\SettingsService;
use Core\Wallet\Models\WalletTransaction;
use Core\Orders\Models\OrderTransaction;
use Illuminate\Support\Facades\DB;

class OrderLoyaltyService
{
    /**
     * Award points once after an order is finished. Points have no expiry.
     */
    public function awardForFinishedOrder(int $orderId): ?int
    {
        return DB::transaction(function () use ($orderId) {
            $order = Order::query()->lockForUpdate()->find($orderId);
            if (! $order || $order->status !== 'finished' || ! $order->client_id) {
                return null;
            }

            if (OrderLoyaltyAward::query()->where('order_id', $order->id)->exists()) {
                return null;
            }

            $rate = max(0, (float) (SettingsService::getDataBaseSetting('points_per_spent_riyal') ?? 0));
            // Use customer-facing total (VAT and delivery included), less
            // promotional wallet value, redeemed points, and recorded refunds.
            $promoWalletUsed = (float) WalletTransaction::query()
                ->where('order_id', $order->id)
                ->where('type', 'withdraw')
                ->where('status', 'accepted')
                ->where('wallet_bucket', 'promotional')
                ->sum('amount');
            $refundedAmount = (float) OrderTransaction::query()
                ->where('order_id', $order->id)
                ->where('amount', '<', 0)
                ->sum('amount');
            $eligibleAmount = max(0, round(
                (float) ($order->total_price ?? 0)
                - $promoWalletUsed
                - (float) ($order->points_amount_used ?? 0)
                + $refundedAmount,
                2
            ));
            $eligibleCents = (int) round($eligibleAmount * 100);
            $rateTenThousandths = (int) round($rate * 10000);
            $points = $rateTenThousandths > 0
                ? intdiv($eligibleCents * $rateTenThousandths, 1000000)
                : 0;

            $award = OrderLoyaltyAward::query()->create([
                'order_id' => $order->id,
                'user_id' => $order->client_id,
                'eligible_amount' => $eligibleAmount,
                'points_earned' => $points,
                'points_per_riyal' => $rate,
            ]);

            if ($points > 0) {
                Point::query()->create([
                    'title' => 'for order : ' . $order->reference_id,
                    'amount' => $points,
                    'operation' => 'deposit',
                    'user_id' => $order->client_id,
                    'order_id' => $order->id,
                ]);
            }

            if ($points > 0) {
                app(AnalyticsOutboxService::class)->record('loyalty_earned', $order, [
                    'eligible_amount' => $eligibleAmount,
                    'points_earned' => $points,
                    'points_per_riyal' => $rate,
                    'loyalty_award_id' => (string) $award->id,
                ], 'loyalty-award:' . $award->id);
            }

            return $points;
        });
    }
}
