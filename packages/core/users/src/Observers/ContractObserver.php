<?php

namespace Core\Users\Observers;

use Core\Users\Models\Contract;

class ContractObserver
{
    /**
     * Handle the Contract "creating" event.
     *
     * @param  \Core\Users\Models\Contract  $contract
     * @return void
     */
    public function creating(Contract $contract)
    {
    
    }
    /**
     * Handle the Contract "created" event.
     *
     * @param  \Core\Users\Models\Contract  $contract
     * @return void
     */
    public function created(Contract $contract)
    {
    
    }

    /**
     * Handle the Contract "updating" event.
     *
     * @param  \Core\Users\Models\Contract  $contract
     * @return void
     */
    public function updating(Contract $contract)
    {

    }
    /**
     * Handle the Contract "updated" event.
     *
     * @param  \Core\Users\Models\Contract  $contract
     * @return void
     */
    public function updated(Contract $contract)
    {

    }
    /**
     * Handle the Contract "saving" event.
     *
     * @param  \Core\Users\Models\Contract  $contract
     * @return void
     */
    public function saving(Contract $contract)
    {

    }
    /**
     * Handle the Contract "saved" event.
     *
     * @param  \Core\Users\Models\Contract  $contract
     * @return void
     */
    public function saved(Contract $contract)
    {

    }

    /**
     * Handle the Contract "deleted" event.
     *
     * @param  \Core\Users\Models\Contract  $contract
     * @return void
     */
    public function deleted(Contract $contract)
    {
      
    }

    /**
     * Handle the Contract "restored" event.
     *
     * @param  \Core\Users\Models\Contract  $contract
     * @return void
     */
    public function restored(Contract $contract)
    {
        //
    }

    /**
     * Handle the Contract "force deleted" event.
     *
     * @param  \Core\Users\Models\Contract  $contract
     * @return void
     */
    public function forceDeleted(Contract $contract)
    {
        //
    }
}
