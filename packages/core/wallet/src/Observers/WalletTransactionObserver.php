<?php

namespace Core\Wallet\Observers;

use Core\Users\Models\User;
use Core\Wallet\Models\WalletTransaction;
use Core\Wallet\Services\WalletTransactionsService;
use Core\PaymentGateways\Models\PaymentTransaction;
use Carbon\Carbon;

class WalletTransactionObserver
{
    /**
     * Handle the WalletTransaction "creating" event.
     *
     * @param  \Core\Wallet\Models\WalletTransaction  $walletTransaction
     * @return void
     */
    public function creating(WalletTransaction $walletTransaction) {
        if (! $walletTransaction->wallet_bucket) {
            $walletTransaction->wallet_bucket = match ([$walletTransaction->type, $walletTransaction->transaction_type]) {
                ['deposit', 'charge'], ['deposit', 'deposit'], ['deposit', 'remaining_amount'] => 'funded',
                ['deposit', 'promotional_add'], ['deposit', 'cashback'], ['deposit', 'reward'],
                ['withdraw', 'expiry_deduction'] => 'promotional',
                default => 'unclassified',
            };
        }
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
        if ($walletTransaction->status !== 'accepted') {
            return;
        }

        $paymentTransaction = $walletTransaction->transaction_id
            ? PaymentTransaction::query()
                ->where('transaction_id', $walletTransaction->transaction_id)
                ->where('for', 'wallet_charge')
                ->where('user_id', $walletTransaction->user_id)
                ->first()
            : null;
        $paymentRequest = $paymentTransaction?->request_data
            ? (json_decode($paymentTransaction->request_data, true) ?: [])
            : [];
        $context = request()->input('analytics_context', $paymentRequest['analytics_context'] ?? []);
        $context = is_array($context) ? $context : [];

        $eventName = null;
        $parameters = ['amount' => round(abs((float) $walletTransaction->amount), 2)];
        $parameters['wallet_bucket'] = $walletTransaction->wallet_bucket ?: 'unclassified';
        if ($walletTransaction->type === 'deposit' && $walletTransaction->transaction_type === 'charge') {
            $eventName = 'wallet_recharged';
            $parameters['amount_paid'] = round((float) ($paymentTransaction?->amount ?? $walletTransaction->amount), 2);
            $parameters['amount_credited'] = round((float) $walletTransaction->amount, 2);
            $parameters['wallet_bucket'] = 'funded';
            $parameters['wallet_package_id'] = $walletTransaction->package_id !== null ? (string) $walletTransaction->package_id : null;
            if ($paymentTransaction?->payment_method) {
                $method = strtolower((string) $paymentTransaction->payment_method);
                $parameters['payment_type'] = str_contains($method, 'apple')
                    ? 'apple_pay'
                    : (str_contains($method, 'mada') ? 'mada' : 'card');
            }
        } elseif ($walletTransaction->type === 'deposit'
            && in_array($walletTransaction->transaction_type, ['compensation_add', 'promotional_add', 'cashback', 'reward'], true)) {
            $eventName = 'wallet_credit_issued';
            $parameters['credit_type'] = $walletTransaction->transaction_type;
            if (in_array($walletTransaction->transaction_type, ['promotional_add', 'cashback', 'reward'], true)) {
                $parameters['wallet_bucket'] = 'promotional';
            }
            if ($walletTransaction->expired_at) {
                $parameters['expires_at'] = Carbon::parse($walletTransaction->expired_at)->toIso8601String();
            }
        } elseif ($walletTransaction->type === 'withdraw' && $walletTransaction->transaction_type === 'expiry_deduction') {
            $eventName = 'wallet_credit_expired';
        } elseif ($walletTransaction->type === 'deposit' && $walletTransaction->transaction_type === 'remaining_amount') {
            $eventName = 'refund';
            $parameters['refund_type'] = 'wallet';
            $parameters['wallet_bucket'] = 'funded';
        }

        if (! $eventName || ! $walletTransaction->user_id) {
            return;
        }

        $order = $walletTransaction->order;
        if ($order && (int) $order->client_id === (int) $walletTransaction->user_id) {
            if ($order->analyticsContext) {
                $context = [
                    'app_instance_id' => $order->analyticsContext->app_instance_id,
                    'platform' => $order->analyticsContext->platform,
                ];
            }
            app(\Core\Orders\Services\AnalyticsOutboxService::class)->record(
                $eventName,
                $order,
                $parameters + ['wallet_transaction_id' => (string) $walletTransaction->id],
                'wallet-transaction:' . $walletTransaction->id
            );
            return;
        }

        app(\Core\Orders\Services\AnalyticsOutboxService::class)->recordUserEvent(
            $eventName,
            (int) $walletTransaction->user_id,
            $context,
            $parameters,
            $paymentTransaction
                ? 'payment:' . $paymentTransaction->id
                : 'wallet-transaction:' . $walletTransaction->id
        );
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
