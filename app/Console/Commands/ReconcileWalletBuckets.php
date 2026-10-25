<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ReconcileWalletBuckets extends Command
{
    protected $signature = 'wallet:reconcile-buckets {--apply : Apply the replay after reviewing the dry-run}';
    protected $description = 'Replay the wallet ledger chronologically and classify legacy withdrawals promotional-first';

    public function handle(): int
    {
        $apply = (bool) $this->option('apply');
        $mismatches = 0;
        $splits = 0;

        DB::table('users')->whereExists(function ($query): void {
            $query->selectRaw('1')->from('wallet_transactions')->whereColumn('wallet_transactions.user_id', 'users.id');
        })->orderBy('id')->select(['id', 'wallet'])->chunkById(200, function ($users) use ($apply, &$mismatches, &$splits): void {
            foreach ($users as $user) {
                DB::transaction(function () use ($user, $apply, &$mismatches, &$splits): void {
                    DB::table('users')->where('id', $user->id)->lockForUpdate()->first();
                    $rows = DB::table('wallet_transactions')->where('user_id', $user->id)
                        ->where(function ($query): void {
                            $query->where('status', 'accepted')
                                ->orWhere(function ($pending): void {
                                    $pending->where('type', 'withdraw')->where('status', 'pending');
                                });
                        })->orderBy('created_at')->orderBy('id')->get();
                    $balances = ['promotional' => 0.0, 'funded' => 0.0, 'unclassified' => 0.0];

                    foreach ($rows as $row) {
                        $amount = round(abs((float) $row->amount), 2);
                        $bucket = in_array($row->wallet_bucket, array_keys($balances), true) ? $row->wallet_bucket : 'unclassified';
                        if ($row->type === 'deposit') {
                            $balances[$bucket] = round($balances[$bucket] + $amount, 2);
                            continue;
                        }

                        $remaining = $amount;
                        $parts = [];
                        $isBankWithdrawal = $row->type === 'withdraw'
                            && ($row->transaction_type === 'withdraw' || $row->bank_name || $row->iban_number);
                        $candidateBuckets = $isBankWithdrawal
                            ? ['funded', 'unclassified']
                            : ['promotional', 'funded', 'unclassified'];
                        foreach ($candidateBuckets as $candidate) {
                            $take = round(min($remaining, max(0, $balances[$candidate])), 2);
                            if ($take > 0) {
                                $parts[$candidate] = $take;
                                $balances[$candidate] = round($balances[$candidate] - $take, 2);
                                $remaining = round($remaining - $take, 2);
                            }
                        }
                        if ($remaining > 0) {
                            $parts['unclassified'] = round(($parts['unclassified'] ?? 0) + $remaining, 2);
                            $balances['unclassified'] = round($balances['unclassified'] - $remaining, 2);
                        }

                        if ($apply) {
                            $this->applyParts($row, $parts);
                        }
                        if (count($parts) > 1) {
                            $splits++;
                        }
                    }

                    $ledger = round(array_sum($balances), 2);
                    if (abs($ledger - round((float) $user->wallet, 2)) > 0.01) {
                        $mismatches++;
                        $this->warn("User {$user->id}: ledger {$ledger}, cached wallet {$user->wallet}");
                    }
                });
            }
        });

        $this->info(($apply ? 'Applied' : 'Dry-run') . ": {$splits} split withdrawals; {$mismatches} wallet mismatches.");
        if (! $apply) {
            $this->comment('Review all mismatches, then run again with --apply.');
        }
        return self::SUCCESS;
    }

    private function applyParts(object $row, array $parts): void
    {
        $parts = array_filter($parts, static fn ($amount) => $amount > 0);
        if (! $parts) {
            return;
        }
        $first = true;
        $linked = DB::table('order_transactions')->where('wallet_transaction_id', $row->id)->first();
        foreach ($parts as $bucket => $amount) {
            if ($first) {
                DB::table('wallet_transactions')->where('id', $row->id)->update([
                    'amount' => $amount, 'wallet_bucket' => $bucket, 'updated_at' => now(),
                ]);
                if ($linked) {
                    DB::table('order_transactions')->where('id', $linked->id)->update(['amount' => $amount, 'updated_at' => now()]);
                }
                $first = false;
                continue;
            }

            $copy = (array) $row;
            unset($copy['id']);
            $copy['amount'] = $amount;
            $copy['wallet_bucket'] = $bucket;
            $copy['updated_at'] = now();
            $newWalletId = DB::table('wallet_transactions')->insertGetId($copy);
            if ($linked) {
                $orderCopy = (array) $linked;
                unset($orderCopy['id']);
                $orderCopy['amount'] = $amount;
                $orderCopy['wallet_transaction_id'] = $newWalletId;
                $orderCopy['updated_at'] = now();
                DB::table('order_transactions')->insert($orderCopy);
            }
        }
    }
}
