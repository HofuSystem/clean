<?php

namespace Core\Users\Observers;

use Core\Users\Models\Role;

class RoleObserver
{
    /**
     * Handle the Role "creating" event.
     *
     * @param  \Core\Users\Models\Role  $role
     * @return void
     */
    public function creating(Role $role)
    {
    
    }
    /**
     * Handle the Role "created" event.
     *
     * @param  \Core\Users\Models\Role  $role
     * @return void
     */
    public function created(Role $role)
    {
    
    }

    /**
     * Handle the Role "updating" event.
     *
     * @param  \Core\Users\Models\Role  $role
     * @return void
     */
    public function updating(Role $role)
    {

    }
    /**
     * Handle the Role "updated" event.
     *
     * @param  \Core\Users\Models\Role  $role
     * @return void
     */
    public function updated(Role $role)
    {

    }
    /**
     * Handle the Role "saving" event.
     *
     * @param  \Core\Users\Models\Role  $role
     * @return void
     */
    public function saving(Role $role)
    {

    }
    /**
     * Handle the Role "saved" event.
     *
     * @param  \Core\Users\Models\Role  $role
     * @return void
     */
    public function saved(Role $role)
    {

    }

    /**
     * Handle the Role "deleted" event.
     *
     * @param  \Core\Users\Models\Role  $role
     * @return void
     */
    public function deleted(Role $role)
    {
      
    }

    /**
     * Handle the Role "restored" event.
     *
     * @param  \Core\Users\Models\Role  $role
     * @return void
     */
    public function restored(Role $role)
    {
        //
    }

    /**
     * Handle the Role "force deleted" event.
     *
     * @param  \Core\Users\Models\Role  $role
     * @return void
     */
    public function forceDeleted(Role $role)
    {
        //
    }
}
