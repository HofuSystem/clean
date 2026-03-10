<?php

namespace Core\Users\Observers;

use Core\Users\Models\UserEditRequest;

class UserEditRequestObserver
{
    /**
     * Handle the UserEditRequest "creating" event.
     *
     * @param  \Core\Users\Models\UserEditRequest  $userEditRequest
     * @return void
     */
    public function creating(UserEditRequest $userEditRequest)
    {
    
    }
    /**
     * Handle the UserEditRequest "created" event.
     *
     * @param  \Core\Users\Models\UserEditRequest  $userEditRequest
     * @return void
     */
    public function created(UserEditRequest $userEditRequest)
    {
    
    }

    /**
     * Handle the UserEditRequest "updating" event.
     *
     * @param  \Core\Users\Models\UserEditRequest  $userEditRequest
     * @return void
     */
    public function updating(UserEditRequest $userEditRequest)
    {

    }
    /**
     * Handle the UserEditRequest "updated" event.
     *
     * @param  \Core\Users\Models\UserEditRequest  $userEditRequest
     * @return void
     */
    public function updated(UserEditRequest $userEditRequest)
    {

    }
    /**
     * Handle the UserEditRequest "saving" event.
     *
     * @param  \Core\Users\Models\UserEditRequest  $userEditRequest
     * @return void
     */
    public function saving(UserEditRequest $userEditRequest)
    {

    }
    /**
     * Handle the UserEditRequest "saved" event.
     *
     * @param  \Core\Users\Models\UserEditRequest  $userEditRequest
     * @return void
     */
    public function saved(UserEditRequest $userEditRequest)
    {

    }

    /**
     * Handle the UserEditRequest "deleted" event.
     *
     * @param  \Core\Users\Models\UserEditRequest  $userEditRequest
     * @return void
     */
    public function deleted(UserEditRequest $userEditRequest)
    {
      
    }

    /**
     * Handle the UserEditRequest "restored" event.
     *
     * @param  \Core\Users\Models\UserEditRequest  $userEditRequest
     * @return void
     */
    public function restored(UserEditRequest $userEditRequest)
    {
        //
    }

    /**
     * Handle the UserEditRequest "force deleted" event.
     *
     * @param  \Core\Users\Models\UserEditRequest  $userEditRequest
     * @return void
     */
    public function forceDeleted(UserEditRequest $userEditRequest)
    {
        //
    }
}
