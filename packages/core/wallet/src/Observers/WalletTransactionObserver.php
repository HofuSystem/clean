<?php

namespace Core\Wallet\Observers;

use Core\Users\Models\User;
use Core\Wallet\Models\WalletTransaction;
use Core\Wallet\Services\WalletTransactionsService;

class WalletTransactionObserver
{
    /**
     * Handle the WalletTransaction "creating" event.
     *
     * @param  \Core\Wallet\Models\WalletTransaction  $walletTransaction
     * @return void
     */
    public function creating(WalletTransaction $walletTransaction) {
        $user = User::find($walletTransaction->user_id);
        if ($user) {
            $walletTransaction->wallet_before = $user->wallet;
        }
        $walletTransaction->added_by_id = auth()?->user()?->id;
    }
    /**
     * Handle the WalletTransaction "created" event.
     *
     * @param  \Core\Wallet\Models\WalletTransaction  $walletTransaction
     * @return void
     */
    public function created(WalletTransaction $walletTransaction)
    {
        
    }

    /**
     * Handle the WalletTransaction "updating" event.
     *
     * @param  \Core\Wallet\Models\WalletTransaction  $walletTransaction
     * @return void
     */
    public function updating(WalletTransaction $walletTransaction) {

    }
    /**
     * Handle the WalletTransaction "updated" event.
     *
     * @param  \Core\Wallet\Models\WalletTransaction  $walletTransaction
     * @return void
     */
    public function updated(WalletTransaction $walletTransaction)
    {
    
    }
    /**
     * Handle the WalletTransaction "saving" event.
     *
     * @param  \Core\Wallet\Models\WalletTransaction  $walletTransaction
     * @return void
     */
    public function saving(WalletTransaction $walletTransaction) {
    }
    /**
     * Handle the WalletTransaction "saved" event.
     *
     * @param  \Core\Wallet\Models\WalletTransaction  $walletTransaction
     * @return void
     */
    public function saved(WalletTransaction $walletTransaction) {
        if ($walletTransaction->isDirty('user_id')) {
            $oldUserId = $walletTransaction->getOriginal('user_id');
            if ($oldUserId && $oldUserId != $walletTransaction->user_id) {
                WalletTransactionsService::updateUserWallet($oldUserId);
            }
        }
        $walletAfter = WalletTransactionsService::updateUserWallet($walletTransaction->user_id);
        $walletTransaction->updateQuietly(['wallet_after' => $walletAfter]);
    }

    /**
     * Handle the WalletTransaction "deleted" event.
     *
     * @param  \Core\Wallet\Models\WalletTransaction  $walletTransaction
     * @return void
     */
    public function deleted(WalletTransaction $walletTransaction)
    {
        WalletTransactionsService::updateUserWallet($walletTransaction->user_id);
    }

    /**
     * Handle the WalletTransaction "restored" event.
     *
     * @param  \Core\Wallet\Models\WalletTransaction  $walletTransaction
     * @return void
     */
    public function restored(WalletTransaction $walletTransaction)
    {
        WalletTransactionsService::updateUserWallet($walletTransaction->user_id);
    }

    /**
     * Handle the WalletTransaction "force deleted" event.
     *
     * @param  \Core\Wallet\Models\WalletTransaction  $walletTransaction
     * @return void
     */
    public function forceDeleted(WalletTransaction $walletTransaction)
    {
        WalletTransactionsService::updateUserWallet($walletTransaction->user_id);
    }
}
