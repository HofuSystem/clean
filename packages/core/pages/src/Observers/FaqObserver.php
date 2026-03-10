<?php

namespace Core\Pages\Observers;

use Core\Pages\Models\Faq;

class FaqObserver
{
    /**
     * Handle the Faq "creating" event.
     *
     * @param  \Core\Pages\Models\Faq  $faq
     * @return void
     */
    public function creating(Faq $faq)
    {
    
    }
    /**
     * Handle the Faq "created" event.
     *
     * @param  \Core\Pages\Models\Faq  $faq
     * @return void
     */
    public function created(Faq $faq)
    {
    
    }

    /**
     * Handle the Faq "updating" event.
     *
     * @param  \Core\Pages\Models\Faq  $faq
     * @return void
     */
    public function updating(Faq $faq)
    {

    }
    /**
     * Handle the Faq "updated" event.
     *
     * @param  \Core\Pages\Models\Faq  $faq
     * @return void
     */
    public function updated(Faq $faq)
    {

    }
    /**
     * Handle the Faq "saving" event.
     *
     * @param  \Core\Pages\Models\Faq  $faq
     * @return void
     */
    public function saving(Faq $faq)
    {

    }
    /**
     * Handle the Faq "saved" event.
     *
     * @param  \Core\Pages\Models\Faq  $faq
     * @return void
     */
    public function saved(Faq $faq)
    {

    }

    /**
     * Handle the Faq "deleted" event.
     *
     * @param  \Core\Pages\Models\Faq  $faq
     * @return void
     */
    public function deleted(Faq $faq)
    {
      
    }

    /**
     * Handle the Faq "restored" event.
     *
     * @param  \Core\Pages\Models\Faq  $faq
     * @return void
     */
    public function restored(Faq $faq)
    {
        //
    }

    /**
     * Handle the Faq "force deleted" event.
     *
     * @param  \Core\Pages\Models\Faq  $faq
     * @return void
     */
    public function forceDeleted(Faq $faq)
    {
        //
    }
}
