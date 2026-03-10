<?php

namespace Core\Users\Observers;

use Core\Users\Models\Device;

class DeviceObserver
{
    /**
     * Handle the Device "creating" event.
     *
     * @param  \Core\Users\Models\Device  $device
     * @return void
     */
    public function creating(Device $device)
    {
    
    }
    /**
     * Handle the Device "created" event.
     *
     * @param  \Core\Users\Models\Device  $device
     * @return void
     */
    public function created(Device $device)
    {
    
    }

    /**
     * Handle the Device "updating" event.
     *
     * @param  \Core\Users\Models\Device  $device
     * @return void
     */
    public function updating(Device $device)
    {

    }
    /**
     * Handle the Device "updated" event.
     *
     * @param  \Core\Users\Models\Device  $device
     * @return void
     */
    public function updated(Device $device)
    {

    }
    /**
     * Handle the Device "saving" event.
     *
     * @param  \Core\Users\Models\Device  $device
     * @return void
     */
    public function saving(Device $device)
    {

    }
    /**
     * Handle the Device "saved" event.
     *
     * @param  \Core\Users\Models\Device  $device
     * @return void
     */
    public function saved(Device $device)
    {

    }

    /**
     * Handle the Device "deleted" event.
     *
     * @param  \Core\Users\Models\Device  $device
     * @return void
     */
    public function deleted(Device $device)
    {
      
    }

    /**
     * Handle the Device "restored" event.
     *
     * @param  \Core\Users\Models\Device  $device
     * @return void
     */
    public function restored(Device $device)
    {
        //
    }

    /**
     * Handle the Device "force deleted" event.
     *
     * @param  \Core\Users\Models\Device  $device
     * @return void
     */
    public function forceDeleted(Device $device)
    {
        //
    }
}
