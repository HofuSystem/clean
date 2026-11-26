<?php

namespace Core\Wallet\Console\Commands;

use Carbon\Carbon;
use Core\Users\Models\User;
use Core\Wallet\Models\WalletTransaction;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class HandleExpiredWalletTransaction extends Command
{
    protected $signature = 'app:handle-expired-wallet-transaction
                            {--dry-run : Report deductions without writing any wallet rows}
                            {--limit=0 : Optional max number of users to process}';

    protected $description = 'Deduct remaining expired promotional wallet credits without touching funded or still-valid balances';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $limit = max(0, (int) $this->option('limit'));

        $userIds = WalletTransaction::query()
            ->where('type', 'deposit')
            ->where('wallet_bucket', 'promotional')
            ->whereNotNull('expired_at')
            ->where('expired_at', '<=', now())
            ->distinct()
            ->orderBy('user_id')
            ->pluck('user_id');

        if ($limit > 0) {
            $userIds = $userIds->take($limit);
        }

        $report = [];
        $deductedUsers = 0;
        $deductedAmount = 0.0;
        $skipped = 0;
        $failed = 0;

        foreach ($userIds as $userId) {
            try {
                $result = $dryRun
                    ? $this->previewUser((int) $userId)
                    : $this->processUser((int) $userId);

                if ($result === null) {
                    $skipped++;
                    continue;
                }

                if ($result['deduct_total'] < 0.01) {
                    $skipped++;
                    continue;
                }

                $report[] = $result;
                $deductedUsers++;
                $deductedAmount = round($deductedAmount + $result['deduct_total'], 2);
            } catch (Throwable $e) {
                $failed++;
                Log::error('Expired wallet credit handling failed for user', [
                    'user_id' => $userId,
                    'dry_run' => $dryRun,
                    'exception' => $e,
                ]);
                $this->error("user={$userId} failed: ".$e->getMessage());
            }
        }

        if ($report !== []) {
            $this->table(
                ['user_id', 'phone', 'wallet', 'expired_remaining', 'deduct', 'credits'],
                array_map(static fn (array $row): array => [
                    $row['user_id'],
                    $row['phone'],
                    number_format($row['wallet_before'], 2, '.', ''),
                    number_format($row['expired_remaining'], 2, '.', ''),
                    number_format($row['deduct_total'], 2, '.', ''),
                    $row['credits_summary'],
                ], $report)
            );
        }

        $this->info(($dryRun ? 'DRY-RUN ' : '').'users_with_deduction='.$deductedUsers
            .' amount='.number_format($deductedAmount, 2, '.', '')
            .' skipped='.$skipped
            .' failed='.$failed
            .' candidates='.$userIds->count());

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }

    /**
     * @return array{user_id:int,phone:?string,wallet_before:float,expired_remaining:float,deduct_total:float,credits_summary:string}|null
     */
    private function previewUser(int $userId): ?array
    {
        $user = User::withTrashed()->find($userId);
        if (! $user) {
            return null;
        }

        $plan = $this->buildDeductionPlan($userId, max(0, round((float) $user->wallet, 2)));
        if ($plan['deduct_total'] < 0.01) {
            return null;
        }

        return $this->formatResult($user, $plan);
    }

    /**
     * @return array{user_id:int,phone:?string,wallet_before:float,expired_remaining:float,deduct_total:float,credits_summary:string}|null
     */
    private function processUser(int $userId): ?array
    {
        return DB::transaction(function () use ($userId): ?array {
            $user = User::withTrashed()->whereKey($userId)->lockForUpdate()->first();
            if (! $user) {
                return null;
            }

            $walletBefore = max(0, round((float) $user->wallet, 2));
            $plan = $this->buildDeductionPlan($userId, $walletBefore);
            if ($plan['deduct_total'] < 0.01) {
                return null;
            }

            foreach ($plan['items'] as $item) {
                WalletTransaction::create([
                    'type' => 'withdraw',
                    'amount' => $item['amount'],
                    'user_id' => $userId,
                    'status' => 'accepted',
                    'transaction_type' => 'expiry_deduction',
                    'wallet_bucket' => 'promotional',
                    'notes' => 'Expired promotional wallet credit #'.$item['source_id'],
                ]);
            }

            $user->refresh();

            return $this->formatResult($user, $plan, $walletBefore);
        });
    }

    /**
     * Rebuild promotional FIFO remainders and return only expired leftovers,
     * capped so the cached wallet never goes negative.
     *
     * @return array{expired_remaining:float,deduct_total:float,items:list<array{source_id:int,expired_at:string,amount:float}>}
     */
    private function buildDeductionPlan(int $userId, float $availableWallet): array
    {
        $rows = WalletTransaction::query()
            ->where('user_id', $userId)
            ->where('status', 'accepted')
            ->where('wallet_bucket', 'promotional')
            ->orderBy('created_at')
            ->orderBy('id')
            ->get();

        $credits = [];
        foreach ($rows as $row) {
            if ($row->type === 'deposit') {
                $credits[] = [
                    'transaction' => $row,
                    'remaining' => round(abs((float) $row->amount), 2),
                ];
                continue;
            }

            $debit = round(abs((float) $row->amount), 2);
            usort($credits, static function (array $left, array $right): int {
                $leftExpiry = $left['transaction']->expired_at
                    ? Carbon::parse($left['transaction']->expired_at)->getTimestamp()
                    : PHP_INT_MAX;
                $rightExpiry = $right['transaction']->expired_at
                    ? Carbon::parse($right['transaction']->expired_at)->getTimestamp()
                    : PHP_INT_MAX;

                if ($leftExpiry === $rightExpiry) {
                    return $left['transaction']->id <=> $right['transaction']->id;
                }

                return $leftExpiry <=> $rightExpiry;
            });

            foreach ($credits as &$credit) {
                if ($debit <= 0) {
                    break;
                }
                $used = min($debit, $credit['remaining']);
                $credit['remaining'] = round($credit['remaining'] - $used, 2);
                $debit = round($debit - $used, 2);
            }
            unset($credit);
        }

        $items = [];
        $expiredRemaining = 0.0;
        $walletLeft = $availableWallet;

        foreach ($credits as $credit) {
            $source = $credit['transaction'];
            if ($credit['remaining'] < 0.01 || ! $source->expired_at
                || Carbon::parse($source->expired_at)->isFuture()) {
                continue;
            }

            $expiredRemaining = round($expiredRemaining + $credit['remaining'], 2);
            $amount = min($credit['remaining'], $walletLeft);
            if ($amount < 0.01) {
                continue;
            }

            $items[] = [
                'source_id' => (int) $source->id,
                'expired_at' => Carbon::parse($source->expired_at)->toDateString(),
                'amount' => $amount,
            ];
            $walletLeft = round($walletLeft - $amount, 2);
        }

        return [
            'expired_remaining' => $expiredRemaining,
            'deduct_total' => round(array_sum(array_column($items, 'amount')), 2),
            'items' => $items,
        ];
    }

    /**
     * @param  array{expired_remaining:float,deduct_total:float,items:list<array{source_id:int,expired_at:string,amount:float}>}  $plan
     * @return array{user_id:int,phone:?string,wallet_before:float,expired_remaining:float,deduct_total:float,credits_summary:string}
     */
    private function formatResult(User $user, array $plan, ?float $walletBefore = null): array
    {
        $summary = collect($plan['items'])
            ->map(static fn (array $item): string => '#'.$item['source_id'].'@'.$item['expired_at'].'='.$item['amount'])
            ->implode(', ');

        return [
            'user_id' => (int) $user->id,
            'phone' => $user->phone,
            'wallet_before' => $walletBefore ?? max(0, round((float) $user->wallet, 2)),
            'expired_remaining' => $plan['expired_remaining'],
            'deduct_total' => $plan['deduct_total'],
            'credits_summary' => $summary,
        ];
    }
}
