<?php

namespace Core\B2B\Observers;

use Core\B2B\Models\ContractsPrice;

class ContractsPriceObserver
{
    /**
     * Handle the ContractsPrice "creating" event.
     *
     * @param  \Core\B2B\Models\ContractsPrice  $contractsPrice
     * @return void
     */
    public function creating(ContractsPrice $contractsPrice)
    {
    
    }
    /**
     * Handle the ContractsPrice "created" event.
     *
     * @param  \Core\B2B\Models\ContractsPrice  $contractsPrice
     * @return void
     */
    public function created(ContractsPrice $contractsPrice)
    {
    
    }

    /**
     * Handle the ContractsPrice "updating" event.
     *
     * @param  \Core\B2B\Models\ContractsPrice  $contractsPrice
     * @return void
     */
    public function updating(ContractsPrice $contractsPrice)
    {

    }
    /**
     * Handle the ContractsPrice "updated" event.
     *
     * @param  \Core\B2B\Models\ContractsPrice  $contractsPrice
     * @return void
     */
    public function updated(ContractsPrice $contractsPrice)
    {

    }
    /**
     * Handle the ContractsPrice "saving" event.
     *
     * @param  \Core\B2B\Models\ContractsPrice  $contractsPrice
     * @return void
     */
    public function saving(ContractsPrice $contractsPrice)
    {

    }
    /**
     * Handle the ContractsPrice "saved" event.
     *
     * @param  \Core\B2B\Models\ContractsPrice  $contractsPrice
     * @return void
     */
    public function saved(ContractsPrice $contractsPrice)
    {

    }

    /**
     * Handle the ContractsPrice "deleted" event.
     *
     * @param  \Core\B2B\Models\ContractsPrice  $contractsPrice
     * @return void
     */
    public function deleted(ContractsPrice $contractsPrice)
    {
      
    }

    /**
     * Handle the ContractsPrice "restored" event.
     *
     * @param  \Core\B2B\Models\ContractsPrice  $contractsPrice
     * @return void
     */
    public function restored(ContractsPrice $contractsPrice)
    {
        //
    }

    /**
     * Handle the ContractsPrice "force deleted" event.
     *
     * @param  \Core\B2B\Models\ContractsPrice  $contractsPrice
     * @return void
     */
    public function forceDeleted(ContractsPrice $contractsPrice)
    {
        //
    }
}
