<?php

namespace Core\Users\Observers;

use Core\Users\Models\Permission;

class PermissionObserver
{
    /**
     * Handle the Permission "creating" event.
     *
     * @param  \Core\Users\Models\Permission  $permission
     * @return void
     */
    public function creating(Permission $permission)
    {
    
    }
    /**
     * Handle the Permission "created" event.
     *
     * @param  \Core\Users\Models\Permission  $permission
     * @return void
     */
    public function created(Permission $permission)
    {
    
    }

    /**
     * Handle the Permission "updating" event.
     *
     * @param  \Core\Users\Models\Permission  $permission
     * @return void
     */
    public function updating(Permission $permission)
    {

    }
    /**
     * Handle the Permission "updated" event.
     *
     * @param  \Core\Users\Models\Permission  $permission
     * @return void
     */
    public function updated(Permission $permission)
    {

    }
    /**
     * Handle the Permission "saving" event.
     *
     * @param  \Core\Users\Models\Permission  $permission
     * @return void
     */
    public function saving(Permission $permission)
    {

    }
    /**
     * Handle the Permission "saved" event.
     *
     * @param  \Core\Users\Models\Permission  $permission
     * @return void
     */
    public function saved(Permission $permission)
    {

    }

    /**
     * Handle the Permission "deleted" event.
     *
     * @param  \Core\Users\Models\Permission  $permission
     * @return void
     */
    public function deleted(Permission $permission)
    {
      
    }

    /**
     * Handle the Permission "restored" event.
     *
     * @param  \Core\Users\Models\Permission  $permission
     * @return void
     */
    public function restored(Permission $permission)
    {
        //
    }

    /**
     * Handle the Permission "force deleted" event.
     *
     * @param  \Core\Users\Models\Permission  $permission
     * @return void
     */
    public function forceDeleted(Permission $permission)
    {
        //
    }
}
