<?php

namespace Core\Orders\Services;

use Core\Orders\Models\Order;
use Core\Settings\Services\SettingsService;
use Core\Users\Models\User;
use Illuminate\Validation\ValidationException;

final class PointsRedemptionService
{
    /**
     * Calculate the SAR value on the server. The legacy points_amount_used input
     * is deliberately ignored so old app versions remain compatible and safe.
     */
    public function calculate(User $user, Order $order, mixed $requestedPoints): array
    {
        $points = max(0, (int) $requestedPoints);
        $minimum = max(0, (int) (SettingsService::getDataBaseSetting('minium_points_to_use') ?? 0));
        $rate = max(0, (float) (SettingsService::getDataBaseSetting('riyal_per_point_redeem') ?? 0));

        if ($points <= 0 || $points < $minimum || $points > (int) $user->points_balance) {
            throw ValidationException::withMessages(['points_amount' => trans('There is not enough points balance')]);
        }

        $value = round($points * $rate, 2);
        $due = \Core\Orders\Support\OrderPaymentMath::remainingToCollect($order->total_price, $order->paid);
        if ($value <= 0 || $value > $due) {
            throw ValidationException::withMessages(['points_amount' => trans('The redeemed points value exceeds the remaining order amount')]);
        }

        return ['points' => $points, 'value' => $value];
    }
}
