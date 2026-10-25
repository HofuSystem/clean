<?php

namespace Core\Wallet\Console\Commands;

use Core\Wallet\Models\WalletTransaction;
use Illuminate\Console\Command;

class HandleExpiredWalletTransaction extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:handle-expired-wallet-transaction';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Handle expired wallet transaction';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userIds = WalletTransaction::query()->where('type', 'deposit')
            ->where('wallet_bucket', 'promotional')->whereNotNull('expired_at')
            ->where('expired_at', '<=', now())->distinct()->pluck('user_id');

        foreach ($userIds as $userId) {
            \Illuminate\Support\Facades\DB::transaction(function () use ($userId): void {
                \Core\Users\Models\User::query()->whereKey($userId)->lockForUpdate()->firstOrFail();
                $rows = WalletTransaction::query()->where('user_id', $userId)
                    ->where('status', 'accepted')->where('wallet_bucket', 'promotional')
                    ->orderBy('created_at')->orderBy('id')->get();
                $credits = [];
                foreach ($rows as $row) {
                    if ($row->type === 'deposit') {
                        $credits[] = ['transaction' => $row, 'remaining' => round(abs((float) $row->amount), 2)];
                        continue;
                    }
                    $debit = round(abs((float) $row->amount), 2);
                    usort($credits, static function (array $left, array $right): int {
                        $leftExpiry = $left['transaction']->expired_at
                            ? \Carbon\Carbon::parse($left['transaction']->expired_at)->getTimestamp()
                            : PHP_INT_MAX;
                        $rightExpiry = $right['transaction']->expired_at
                            ? \Carbon\Carbon::parse($right['transaction']->expired_at)->getTimestamp()
                            : PHP_INT_MAX;
                        return $leftExpiry <=> $rightExpiry;
                    });
                    foreach ($credits as &$credit) {
                        if ($debit <= 0) break;
                        $used = min($debit, $credit['remaining']);
                        $credit['remaining'] = round($credit['remaining'] - $used, 2);
                        $debit = round($debit - $used, 2);
                    }
                    unset($credit);
                }

                foreach ($credits as $credit) {
                    $source = $credit['transaction'];
                    if ($credit['remaining'] < 0.01 || ! $source->expired_at
                        || \Carbon\Carbon::parse($source->expired_at)->isFuture()) {
                        continue;
                    }
                    WalletTransaction::create([
                        'title' => 'Expired promotional wallet credit #' . $source->id,
                        'type' => 'withdraw', 'amount' => $credit['remaining'], 'user_id' => $userId,
                        'status' => 'accepted', 'transaction_type' => 'expiry_deduction',
                        'wallet_bucket' => 'promotional',
                    ]);
                }
            });
        }
    }
}
