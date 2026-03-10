<?php

namespace Core\Users\Observers;

use Core\Users\Models\ContractsCustomerPrice;

class ContractsCustomerPriceObserver
{
    /**
     * Handle the ContractsCustomerPrice "created" event.
     *
     * @param  \Core\Users\Models\ContractsCustomerPrice  $contractsCustomerPrice
     * @return void
     */
    public function created(ContractsCustomerPrice $contractsCustomerPrice)
    {
        //
    }

    /**
     * Handle the ContractsCustomerPrice "updated" event.
     *
     * @param  \Core\Users\Models\ContractsCustomerPrice  $contractsCustomerPrice
     * @return void
     */
    public function updated(ContractsCustomerPrice $contractsCustomerPrice)
    {
        //
    }

    /**
     * Handle the ContractsCustomerPrice "deleted" event.
     *
     * @param  \Core\Users\Models\ContractsCustomerPrice  $contractsCustomerPrice
     * @return void
     */
    public function deleted(ContractsCustomerPrice $contractsCustomerPrice)
    {
        //
    }

    /**
     * Handle the ContractsCustomerPrice "restored" event.
     *
     * @param  \Core\Users\Models\ContractsCustomerPrice  $contractsCustomerPrice
     * @return void
     */
    public function restored(ContractsCustomerPrice $contractsCustomerPrice)
    {
        //
    }

    /**
     * Handle the ContractsCustomerPrice "force deleted" event.
     *
     * @param  \Core\Users\Models\ContractsCustomerPrice  $contractsCustomerPrice
     * @return void
     */
    public function forceDeleted(ContractsCustomerPrice $contractsCustomerPrice)
    {
        //
    }
}

