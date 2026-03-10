<?php

namespace Core\Workers\Observers;

use Core\Workers\Models\WorkerDay;

class WorkerDayObserver
{
    /**
     * Handle the WorkerDay "creating" event.
     *
     * @param  \Core\Workers\Models\WorkerDay  $workerDay
     * @return void
     */
    public function creating(WorkerDay $workerDay)
    {
    
    }
    /**
     * Handle the WorkerDay "created" event.
     *
     * @param  \Core\Workers\Models\WorkerDay  $workerDay
     * @return void
     */
    public function created(WorkerDay $workerDay)
    {
    
    }

    /**
     * Handle the WorkerDay "updating" event.
     *
     * @param  \Core\Workers\Models\WorkerDay  $workerDay
     * @return void
     */
    public function updating(WorkerDay $workerDay)
    {

    }
    /**
     * Handle the WorkerDay "updated" event.
     *
     * @param  \Core\Workers\Models\WorkerDay  $workerDay
     * @return void
     */
    public function updated(WorkerDay $workerDay)
    {

    }
    /**
     * Handle the WorkerDay "saving" event.
     *
     * @param  \Core\Workers\Models\WorkerDay  $workerDay
     * @return void
     */
    public function saving(WorkerDay $workerDay)
    {

    }
    /**
     * Handle the WorkerDay "saved" event.
     *
     * @param  \Core\Workers\Models\WorkerDay  $workerDay
     * @return void
     */
    public function saved(WorkerDay $workerDay)
    {

    }

    /**
     * Handle the WorkerDay "deleted" event.
     *
     * @param  \Core\Workers\Models\WorkerDay  $workerDay
     * @return void
     */
    public function deleted(WorkerDay $workerDay)
    {
      
    }

    /**
     * Handle the WorkerDay "restored" event.
     *
     * @param  \Core\Workers\Models\WorkerDay  $workerDay
     * @return void
     */
    public function restored(WorkerDay $workerDay)
    {
        //
    }

    /**
     * Handle the WorkerDay "force deleted" event.
     *
     * @param  \Core\Workers\Models\WorkerDay  $workerDay
     * @return void
     */
    public function forceDeleted(WorkerDay $workerDay)
    {
        //
    }
}
