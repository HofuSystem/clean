<?php

namespace Core\Orders\Observers;

use Core\Orders\Models\ReportReason;

class ReportReasonObserver
{
    /**
     * Handle the ReportReason "creating" event.
     *
     * @param  \Core\Orders\Models\ReportReason  $reportReason
     * @return void
     */
    public function creating(ReportReason $reportReason)
    {
    
    }
    /**
     * Handle the ReportReason "created" event.
     *
     * @param  \Core\Orders\Models\ReportReason  $reportReason
     * @return void
     */
    public function created(ReportReason $reportReason)
    {
    
    }

    /**
     * Handle the ReportReason "updating" event.
     *
     * @param  \Core\Orders\Models\ReportReason  $reportReason
     * @return void
     */
    public function updating(ReportReason $reportReason)
    {

    }
    /**
     * Handle the ReportReason "updated" event.
     *
     * @param  \Core\Orders\Models\ReportReason  $reportReason
     * @return void
     */
    public function updated(ReportReason $reportReason)
    {

    }
    /**
     * Handle the ReportReason "saving" event.
     *
     * @param  \Core\Orders\Models\ReportReason  $reportReason
     * @return void
     */
    public function saving(ReportReason $reportReason)
    {

    }
    /**
     * Handle the ReportReason "saved" event.
     *
     * @param  \Core\Orders\Models\ReportReason  $reportReason
     * @return void
     */
    public function saved(ReportReason $reportReason)
    {

    }

    /**
     * Handle the ReportReason "deleted" event.
     *
     * @param  \Core\Orders\Models\ReportReason  $reportReason
     * @return void
     */
    public function deleted(ReportReason $reportReason)
    {
      
    }

    /**
     * Handle the ReportReason "restored" event.
     *
     * @param  \Core\Orders\Models\ReportReason  $reportReason
     * @return void
     */
    public function restored(ReportReason $reportReason)
    {
        //
    }

    /**
     * Handle the ReportReason "force deleted" event.
     *
     * @param  \Core\Orders\Models\ReportReason  $reportReason
     * @return void
     */
    public function forceDeleted(ReportReason $reportReason)
    {
        //
    }
}
