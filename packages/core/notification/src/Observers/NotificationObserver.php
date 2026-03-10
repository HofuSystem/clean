<?php

namespace Core\Notification\Observers;

use Core\Notification\Helpers\NotificationsManger;
use Core\Notification\Models\Notification;

class NotificationObserver
{
    /**
     * Handle the Notification "creating" event.
     *
     * @param  \Core\Notification\Models\Notification  $notification
     * @return void
     */
    public function creating(Notification $notification)
    {
        if(!isset($notification->title)){
            $notification->title = config('app.name');
        }
        $notification->sender_id = auth()->id() ?? null;
    }
    /**
     * Handle the Notification "created" event.
     *
     * @param  \Core\Notification\Models\Notification  $notification
     * @return void
     */
    public function created(Notification $notification)
    {
        NotificationsManger::getInstance()->sendNotification($notification);
    }

    /**
     * Handle the Notification "updating" event.
     *
     * @param  \Core\Notification\Models\Notification  $notification
     * @return void
     */
    public function updating(Notification $notification)
    {

    }
    /**
     * Handle the Notification "updated" event.
     *
     * @param  \Core\Notification\Models\Notification  $notification
     * @return void
     */
    public function updated(Notification $notification)
    {

    }
    /**
     * Handle the Notification "saving" event.
     *
     * @param  \Core\Notification\Models\Notification  $notification
     * @return void
     */
    public function saving(Notification $notification)
    {

    }
    /**
     * Handle the Notification "saved" event.
     *
     * @param  \Core\Notification\Models\Notification  $notification
     * @return void
     */
    public function saved(Notification $notification)
    {

    }

    /**
     * Handle the Notification "deleted" event.
     *
     * @param  \Core\Notification\Models\Notification  $notification
     * @return void
     */
    public function deleted(Notification $notification)
    {
      
    }

    /**
     * Handle the Notification "restored" event.
     *
     * @param  \Core\Notification\Models\Notification  $notification
     * @return void
     */
    public function restored(Notification $notification)
    {
        //
    }

    /**
     * Handle the Notification "force deleted" event.
     *
     * @param  \Core\Notification\Models\Notification  $notification
     * @return void
     */
    public function forceDeleted(Notification $notification)
    {
        //
    }
}
