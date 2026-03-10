<?php

namespace Core\Users\Observers;

use Core\Users\Models\Profile;

class ProfileObserver
{
    /**
     * Handle the Profile "creating" event.
     *
     * @param  \Core\Users\Models\Profile  $profile
     * @return void
     */
    public function creating(Profile $profile)
    {
    
    }
    /**
     * Handle the Profile "created" event.
     *
     * @param  \Core\Users\Models\Profile  $profile
     * @return void
     */
    public function created(Profile $profile)
    {
    
    }

    /**
     * Handle the Profile "updating" event.
     *
     * @param  \Core\Users\Models\Profile  $profile
     * @return void
     */
    public function updating(Profile $profile)
    {

    }
    /**
     * Handle the Profile "updated" event.
     *
     * @param  \Core\Users\Models\Profile  $profile
     * @return void
     */
    public function updated(Profile $profile)
    {

    }
    /**
     * Handle the Profile "saving" event.
     *
     * @param  \Core\Users\Models\Profile  $profile
     * @return void
     */
    public function saving(Profile $profile)
    {

    }
    /**
     * Handle the Profile "saved" event.
     *
     * @param  \Core\Users\Models\Profile  $profile
     * @return void
     */
    public function saved(Profile $profile)
    {

    }

    /**
     * Handle the Profile "deleted" event.
     *
     * @param  \Core\Users\Models\Profile  $profile
     * @return void
     */
    public function deleted(Profile $profile)
    {
      
    }

    /**
     * Handle the Profile "restored" event.
     *
     * @param  \Core\Users\Models\Profile  $profile
     * @return void
     */
    public function restored(Profile $profile)
    {
        //
    }

    /**
     * Handle the Profile "force deleted" event.
     *
     * @param  \Core\Users\Models\Profile  $profile
     * @return void
     */
    public function forceDeleted(Profile $profile)
    {
        //
    }
}
