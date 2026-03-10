<?php

namespace Core\Orders\Observers;

use Core\Orders\Models\OrderTypesOfDatum;

class OrderTypesOfDatumObserver
{
    /**
     * Handle the OrderTypesOfDatum "creating" event.
     *
     * @param  \Core\Orders\Models\OrderTypesOfDatum  $orderTypesOfDatum
     * @return void
     */
    public function creating(OrderTypesOfDatum $orderTypesOfDatum)
    {
    
    }
    /**
     * Handle the OrderTypesOfDatum "created" event.
     *
     * @param  \Core\Orders\Models\OrderTypesOfDatum  $orderTypesOfDatum
     * @return void
     */
    public function created(OrderTypesOfDatum $orderTypesOfDatum)
    {
    
    }

    /**
     * Handle the OrderTypesOfDatum "updating" event.
     *
     * @param  \Core\Orders\Models\OrderTypesOfDatum  $orderTypesOfDatum
     * @return void
     */
    public function updating(OrderTypesOfDatum $orderTypesOfDatum)
    {

    }
    /**
     * Handle the OrderTypesOfDatum "updated" event.
     *
     * @param  \Core\Orders\Models\OrderTypesOfDatum  $orderTypesOfDatum
     * @return void
     */
    public function updated(OrderTypesOfDatum $orderTypesOfDatum)
    {

    }
    /**
     * Handle the OrderTypesOfDatum "saving" event.
     *
     * @param  \Core\Orders\Models\OrderTypesOfDatum  $orderTypesOfDatum
     * @return void
     */
    public function saving(OrderTypesOfDatum $orderTypesOfDatum)
    {

    }
    /**
     * Handle the OrderTypesOfDatum "saved" event.
     *
     * @param  \Core\Orders\Models\OrderTypesOfDatum  $orderTypesOfDatum
     * @return void
     */
    public function saved(OrderTypesOfDatum $orderTypesOfDatum)
    {

    }

    /**
     * Handle the OrderTypesOfDatum "deleted" event.
     *
     * @param  \Core\Orders\Models\OrderTypesOfDatum  $orderTypesOfDatum
     * @return void
     */
    public function deleted(OrderTypesOfDatum $orderTypesOfDatum)
    {
      
    }

    /**
     * Handle the OrderTypesOfDatum "restored" event.
     *
     * @param  \Core\Orders\Models\OrderTypesOfDatum  $orderTypesOfDatum
     * @return void
     */
    public function restored(OrderTypesOfDatum $orderTypesOfDatum)
    {
        //
    }

    /**
     * Handle the OrderTypesOfDatum "force deleted" event.
     *
     * @param  \Core\Orders\Models\OrderTypesOfDatum  $orderTypesOfDatum
     * @return void
     */
    public function forceDeleted(OrderTypesOfDatum $orderTypesOfDatum)
    {
        //
    }
}
