<?php

namespace Core\Wallet\Services;

use Core\Wallet\Models\WalletTransaction;

class WalletBucketAllocationService
{
    /**
     * Allocate a wallet debit promotional-first, then funded, then legacy or
     * manually unclassified balance. The returned amounts always total the
     * requested debit so existing app payment behavior stays unchanged.
     */
    public function allocate(int $userId, float $amount): array
    {
        $balances = ['promotional' => 0.0, 'funded' => 0.0, 'unclassified' => 0.0];

        WalletTransaction::query()
            ->where('user_id', $userId)
            ->where('status', 'accepted')
            ->get(['type', 'amount', 'wallet_bucket'])
            ->each(function (WalletTransaction $transaction) use (&$balances): void {
                $bucket = in_array($transaction->wallet_bucket, ['promotional', 'funded', 'unclassified'], true)
                    ? $transaction->wallet_bucket
                    : 'unclassified';
                $sign = $transaction->type === 'deposit' ? 1 : -1;
                $balances[$bucket] += $sign * abs((float) $transaction->amount);
            });

        foreach ($balances as $bucket => $balance) {
            $balances[$bucket] = max(0, round($balance, 2));
        }

        $remaining = max(0, round($amount, 2));
        $allocation = ['promotional' => 0.0, 'funded' => 0.0, 'unclassified' => 0.0];
        foreach (['promotional', 'funded', 'unclassified'] as $bucket) {
            $taken = min($remaining, $balances[$bucket]);
            $allocation[$bucket] = round($taken, 2);
            $remaining = round($remaining - $taken, 2);
        }

        // Preserve the current wallet total if its legacy ledger and cached
        // user balance differ; surface this remainder as unclassified.
        if ($remaining > 0) {
            $allocation['unclassified'] = round($allocation['unclassified'] + $remaining, 2);
        }

        return array_filter($allocation, static fn (float $value): bool => $value > 0);
    }
}
