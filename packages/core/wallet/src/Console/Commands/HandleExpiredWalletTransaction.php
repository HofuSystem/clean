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
        $walletTransactions = WalletTransaction::whereDate('expired_at', '<=', now())
            ->where('type', 'deposit')
            ->has('user')
            ->get();
        foreach ($walletTransactions as $transaction) {
            $usedAmountSinceCreation = WalletTransaction::where('user_id', $transaction->user_id)
                ->where('id', '!=', $transaction->id)
                ->where('created_at', '>=', $transaction->created_at)
                ->where('type', 'withdraw')
                ->sum('amount');
            if($usedAmountSinceCreation < $transaction->amount){
                $notUsedAmount = $transaction->amount - $usedAmountSinceCreation;
                WalletTransaction::create([
                    'type'              => 'withdraw',
                    'amount'            => $notUsedAmount,
                    'user_id'           => $transaction->user_id,
                    'status'            => 'accepted',
                    'transaction_type'  => 'expiry_deduction',
                ]);
            }
        }
    }
}
