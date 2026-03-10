<?php

namespace Core\Pages\Observers;

use Core\Pages\Models\ContactRequest;

class ContactRequestObserver
{
    /**
     * Handle the ContactRequest "creating" event.
     *
     * @param  \Core\Pages\Models\ContactRequest  $contactRequest
     * @return void
     */
    public function creating(ContactRequest $contactRequest)
    {
    
    }
    /**
     * Handle the ContactRequest "created" event.
     *
     * @param  \Core\Pages\Models\ContactRequest  $contactRequest
     * @return void
     */
    public function created(ContactRequest $contactRequest)
    {
    
    }

    /**
     * Handle the ContactRequest "updating" event.
     *
     * @param  \Core\Pages\Models\ContactRequest  $contactRequest
     * @return void
     */
    public function updating(ContactRequest $contactRequest)
    {

    }
    /**
     * Handle the ContactRequest "updated" event.
     *
     * @param  \Core\Pages\Models\ContactRequest  $contactRequest
     * @return void
     */
    public function updated(ContactRequest $contactRequest)
    {

    }
    /**
     * Handle the ContactRequest "saving" event.
     *
     * @param  \Core\Pages\Models\ContactRequest  $contactRequest
     * @return void
     */
    public function saving(ContactRequest $contactRequest)
    {

    }
    /**
     * Handle the ContactRequest "saved" event.
     *
     * @param  \Core\Pages\Models\ContactRequest  $contactRequest
     * @return void
     */
    public function saved(ContactRequest $contactRequest)
    {

    }

    /**
     * Handle the ContactRequest "deleted" event.
     *
     * @param  \Core\Pages\Models\ContactRequest  $contactRequest
     * @return void
     */
    public function deleted(ContactRequest $contactRequest)
    {
      
    }

    /**
     * Handle the ContactRequest "restored" event.
     *
     * @param  \Core\Pages\Models\ContactRequest  $contactRequest
     * @return void
     */
    public function restored(ContactRequest $contactRequest)
    {
        //
    }

    /**
     * Handle the ContactRequest "force deleted" event.
     *
     * @param  \Core\Pages\Models\ContactRequest  $contactRequest
     * @return void
     */
    public function forceDeleted(ContactRequest $contactRequest)
    {
        //
    }
}
