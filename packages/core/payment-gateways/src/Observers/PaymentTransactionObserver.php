<?php

namespace Core\PaymentGateways\Observers;

use Core\PaymentGateways\Models\PaymentTransaction;

class PaymentTransactionObserver
{
    /**
     * Handle the PaymentTransaction "creating" event.
     *
     * @param  \Core\PaymentGateways\Models\PaymentTransaction  $paymentTransaction
     * @return void
     */
    public function creating(PaymentTransaction $paymentTransaction)
    {
    
    }
    /**
     * Handle the PaymentTransaction "created" event.
     *
     * @param  \Core\PaymentGateways\Models\PaymentTransaction  $paymentTransaction
     * @return void
     */
    public function created(PaymentTransaction $paymentTransaction)
    {
    
    }

    /**
     * Handle the PaymentTransaction "updating" event.
     *
     * @param  \Core\PaymentGateways\Models\PaymentTransaction  $paymentTransaction
     * @return void
     */
    public function updating(PaymentTransaction $paymentTransaction)
    {

    }
    /**
     * Handle the PaymentTransaction "updated" event.
     *
     * @param  \Core\PaymentGateways\Models\PaymentTransaction  $paymentTransaction
     * @return void
     */
    public function updated(PaymentTransaction $paymentTransaction)
    {

    }
    /**
     * Handle the PaymentTransaction "saving" event.
     *
     * @param  \Core\PaymentGateways\Models\PaymentTransaction  $paymentTransaction
     * @return void
     */
    public function saving(PaymentTransaction $paymentTransaction)
    {

    }
    /**
     * Handle the PaymentTransaction "saved" event.
     *
     * @param  \Core\PaymentGateways\Models\PaymentTransaction  $paymentTransaction
     * @return void
     */
    public function saved(PaymentTransaction $paymentTransaction)
    {

    }

    /**
     * Handle the PaymentTransaction "deleted" event.
     *
     * @param  \Core\PaymentGateways\Models\PaymentTransaction  $paymentTransaction
     * @return void
     */
    public function deleted(PaymentTransaction $paymentTransaction)
    {
      
    }

    /**
     * Handle the PaymentTransaction "restored" event.
     *
     * @param  \Core\PaymentGateways\Models\PaymentTransaction  $paymentTransaction
     * @return void
     */
    public function restored(PaymentTransaction $paymentTransaction)
    {
        //
    }

    /**
     * Handle the PaymentTransaction "force deleted" event.
     *
     * @param  \Core\PaymentGateways\Models\PaymentTransaction  $paymentTransaction
     * @return void
     */
    public function forceDeleted(PaymentTransaction $paymentTransaction)
    {
        //
    }
}
