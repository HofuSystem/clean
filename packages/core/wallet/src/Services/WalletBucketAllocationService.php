<?php

namespace Core\Wallet\Services;

use Core\Wallet\Models\WalletTransaction;
use Core\Users\Models\User;
use Illuminate\Support\Facades\DB;

class WalletBucketAllocationService
{
    /**
     * Allocate a wallet debit promotional-first, then funded, then legacy or
     * manually unclassified balance. The returned amounts always total the
     * requested debit so existing app payment behavior stays unchanged.
     */
    public function allocate(int $userId, float $amount): array
    {
        return DB::transaction(function () use ($userId, $amount): array {
            User::query()->whereKey($userId)->lockForUpdate()->firstOrFail();
            return $this->calculateAllocation($userId, $amount);
        });
    }

    /**
     * Allocate and persist an order wallet debit while the user row remains
     * locked. This keeps the allocation and ledger writes atomic even when a
     * future caller does not already own an outer database transaction.
     *
     * @return array<int, WalletTransaction>
     */
    public function createOrderPaymentWithdrawals(int $userId, int $orderId, float $amount): array
    {
        return DB::transaction(function () use ($userId, $orderId, $amount): array {
            $user = User::query()->whereKey($userId)->lockForUpdate()->firstOrFail();
            $allocations = $this->calculateAllocation($userId, $amount);
            $transactions = [];

            foreach ($allocations as $bucket => $bucketAmount) {
                $transactions[] = $user->walletTransactions()->create([
                    'amount' => $bucketAmount,
                    'type' => 'withdraw',
                    'status' => 'accepted',
                    'transaction_type' => 'order_payment',
                    'wallet_bucket' => $bucket,
                    'order_id' => $orderId,
                ]);
            }

            return $transactions;
        });
    }

    private function calculateAllocation(int $userId, float $amount): array
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
