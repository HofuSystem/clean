<?php

namespace Core\Orders\Support;

/**
 * Money math for order payments.
 *
 * Order amounts are stored as doubles, so subtracting them produces floating
 * point noise (385.70 - 385 = 0.6999999999999886). Every value returned here is
 * rounded to halalas so that noise never reaches the ledger or the mobile apps.
 */
final class OrderPaymentMath
{
    public const PRECISION = 2;

    /** Smallest amount worth recording as a payment (one halala). */
    public const MINIMUM_AMOUNT = 0.01;

    public static function round(mixed $amount): float
    {
        return round((float) $amount, self::PRECISION);
    }

    /**
     * What the customer still owes on the order. Zero when fully paid or overpaid.
     */
    public static function remainingToCollect(mixed $totalPrice, mixed $paid): float
    {
        return max(self::round((float) $totalPrice - (float) $paid), 0.0);
    }

    /**
     * What the order owes back to the customer (paid more than the total).
     */
    public static function remainingForCustomer(mixed $totalPrice, mixed $paid): float
    {
        return max(self::round((float) $paid - (float) $totalPrice), 0.0);
    }

    /**
     * Whether an amount is large enough to be recorded as a real payment.
     */
    public static function isCollectable(float $amount): bool
    {
        return $amount >= self::MINIMUM_AMOUNT;
    }
}
