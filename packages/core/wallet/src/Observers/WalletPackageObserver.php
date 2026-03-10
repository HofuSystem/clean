<?php

namespace Core\Wallet\Observers;

use Core\Wallet\Models\WalletPackage;

class WalletPackageObserver
{
    /**
     * Handle the WalletPackage "creating" event.
     *
     * @param  \Core\Wallet\Models\WalletPackage  $walletPackage
     * @return void
     */
    public function creating(WalletPackage $walletPackage)
    {
    
    }
    /**
     * Handle the WalletPackage "created" event.
     *
     * @param  \Core\Wallet\Models\WalletPackage  $walletPackage
     * @return void
     */
    public function created(WalletPackage $walletPackage)
    {
    
    }

    /**
     * Handle the WalletPackage "updating" event.
     *
     * @param  \Core\Wallet\Models\WalletPackage  $walletPackage
     * @return void
     */
    public function updating(WalletPackage $walletPackage)
    {

    }
    /**
     * Handle the WalletPackage "updated" event.
     *
     * @param  \Core\Wallet\Models\WalletPackage  $walletPackage
     * @return void
     */
    public function updated(WalletPackage $walletPackage)
    {

    }
    /**
     * Handle the WalletPackage "saving" event.
     *
     * @param  \Core\Wallet\Models\WalletPackage  $walletPackage
     * @return void
     */
    public function saving(WalletPackage $walletPackage)
    {

    }
    /**
     * Handle the WalletPackage "saved" event.
     *
     * @param  \Core\Wallet\Models\WalletPackage  $walletPackage
     * @return void
     */
    public function saved(WalletPackage $walletPackage)
    {

    }

    /**
     * Handle the WalletPackage "deleted" event.
     *
     * @param  \Core\Wallet\Models\WalletPackage  $walletPackage
     * @return void
     */
    public function deleted(WalletPackage $walletPackage)
    {
      
    }

    /**
     * Handle the WalletPackage "restored" event.
     *
     * @param  \Core\Wallet\Models\WalletPackage  $walletPackage
     * @return void
     */
    public function restored(WalletPackage $walletPackage)
    {
        //
    }

    /**
     * Handle the WalletPackage "force deleted" event.
     *
     * @param  \Core\Wallet\Models\WalletPackage  $walletPackage
     * @return void
     */
    public function forceDeleted(WalletPackage $walletPackage)
    {
        //
    }
}
