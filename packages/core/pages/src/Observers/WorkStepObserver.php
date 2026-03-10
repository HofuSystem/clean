<?php

namespace Core\Pages\Observers;

use Core\Pages\Models\WorkStep;

class WorkStepObserver
{
    /**
     * Handle the WorkStep "creating" event.
     *
     * @param  \Core\Pages\Models\WorkStep  $workStep
     * @return void
     */
    public function creating(WorkStep $workStep)
    {
    
    }
    /**
     * Handle the WorkStep "created" event.
     *
     * @param  \Core\Pages\Models\WorkStep  $workStep
     * @return void
     */
    public function created(WorkStep $workStep)
    {
    
    }

    /**
     * Handle the WorkStep "updating" event.
     *
     * @param  \Core\Pages\Models\WorkStep  $workStep
     * @return void
     */
    public function updating(WorkStep $workStep)
    {

    }
    /**
     * Handle the WorkStep "updated" event.
     *
     * @param  \Core\Pages\Models\WorkStep  $workStep
     * @return void
     */
    public function updated(WorkStep $workStep)
    {

    }
    /**
     * Handle the WorkStep "saving" event.
     *
     * @param  \Core\Pages\Models\WorkStep  $workStep
     * @return void
     */
    public function saving(WorkStep $workStep)
    {

    }
    /**
     * Handle the WorkStep "saved" event.
     *
     * @param  \Core\Pages\Models\WorkStep  $workStep
     * @return void
     */
    public function saved(WorkStep $workStep)
    {

    }

    /**
     * Handle the WorkStep "deleted" event.
     *
     * @param  \Core\Pages\Models\WorkStep  $workStep
     * @return void
     */
    public function deleted(WorkStep $workStep)
    {
      
    }

    /**
     * Handle the WorkStep "restored" event.
     *
     * @param  \Core\Pages\Models\WorkStep  $workStep
     * @return void
     */
    public function restored(WorkStep $workStep)
    {
        //
    }

    /**
     * Handle the WorkStep "force deleted" event.
     *
     * @param  \Core\Pages\Models\WorkStep  $workStep
     * @return void
     */
    public function forceDeleted(WorkStep $workStep)
    {
        //
    }
}

