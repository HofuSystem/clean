<?php

namespace Core\Users\Observers;

use Core\Users\Models\User;

class UserObserver
{
    /**
     * Handle the User "creating" event.
     *
     * @param  \Core\Users\Models\User  $user
     * @return void
     */
    public function creating(User $user)
    {
    
    }
    /**
     * Handle the User "created" event.
     *
     * @param  \Core\Users\Models\User  $user
     * @return void
     */
    public function created(User $user)
    {
    
    }

    /**
     * Handle the User "updating" event.
     *
     * @param  \Core\Users\Models\User  $user
     * @return void
     */
    public function updating(User $user)
    {

    }
    /**
     * Handle the User "updated" event.
     *
     * @param  \Core\Users\Models\User  $user
     * @return void
     */
    public function updated(User $user)
    {

    }
    /**
     * Handle the User "saving" event.
     *
     * @param  \Core\Users\Models\User  $user
     * @return void
     */
    public function saving(User $user)
    {

    }
    /**
     * Handle the User "saved" event.
     *
     * @param  \Core\Users\Models\User  $user
     * @return void
     */
    public function saved(User $user)
    {

    }

    /**
     * Handle the User "deleted" event.
     *
     * @param  \Core\Users\Models\User  $user
     * @return void
     */
    public function deleted(User $user)
    {
      
    }

    /**
     * Handle the User "restored" event.
     *
     * @param  \Core\Users\Models\User  $user
     * @return void
     */
    public function restored(User $user)
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     *
     * @param  \Core\Users\Models\User  $user
     * @return void
     */
    public function forceDeleted(User $user)
    {
        //
    }
}
