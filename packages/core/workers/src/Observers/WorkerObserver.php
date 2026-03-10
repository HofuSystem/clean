<?php

namespace Core\Workers\Observers;

use Core\Workers\Models\Worker;

class WorkerObserver
{
    /**
     * Handle the Worker "creating" event.
     *
     * @param  \Core\Workers\Models\Worker  $worker
     * @return void
     */
    public function creating(Worker $worker)
    {
    
    }
    /**
     * Handle the Worker "created" event.
     *
     * @param  \Core\Workers\Models\Worker  $worker
     * @return void
     */
    public function created(Worker $worker)
    {
    
    }

    /**
     * Handle the Worker "updating" event.
     *
     * @param  \Core\Workers\Models\Worker  $worker
     * @return void
     */
    public function updating(Worker $worker)
    {

    }
    /**
     * Handle the Worker "updated" event.
     *
     * @param  \Core\Workers\Models\Worker  $worker
     * @return void
     */
    public function updated(Worker $worker)
    {

    }
    /**
     * Handle the Worker "saving" event.
     *
     * @param  \Core\Workers\Models\Worker  $worker
     * @return void
     */
    public function saving(Worker $worker)
    {

    }
    /**
     * Handle the Worker "saved" event.
     *
     * @param  \Core\Workers\Models\Worker  $worker
     * @return void
     */
    public function saved(Worker $worker)
    {

    }

    /**
     * Handle the Worker "deleted" event.
     *
     * @param  \Core\Workers\Models\Worker  $worker
     * @return void
     */
    public function deleted(Worker $worker)
    {
      
    }

    /**
     * Handle the Worker "restored" event.
     *
     * @param  \Core\Workers\Models\Worker  $worker
     * @return void
     */
    public function restored(Worker $worker)
    {
        //
    }

    /**
     * Handle the Worker "force deleted" event.
     *
     * @param  \Core\Workers\Models\Worker  $worker
     * @return void
     */
    public function forceDeleted(Worker $worker)
    {
        //
    }
}
