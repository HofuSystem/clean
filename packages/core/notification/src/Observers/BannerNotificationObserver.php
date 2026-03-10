<?php

namespace Core\Notification\Observers;

use Core\Notification\Models\BannerNotification;

class BannerNotificationObserver
{
    /**
     * Handle the BannerNotification "creating" event.
     *
     * @param  \Core\Notification\Models\BannerNotification  $bannerNotification
     * @return void
     */
    public function creating(BannerNotification $bannerNotification)
    {
    
    }
    /**
     * Handle the BannerNotification "created" event.
     *
     * @param  \Core\Notification\Models\BannerNotification  $bannerNotification
     * @return void
     */
    public function created(BannerNotification $bannerNotification)
    {
    
    }

    /**
     * Handle the BannerNotification "updating" event.
     *
     * @param  \Core\Notification\Models\BannerNotification  $bannerNotification
     * @return void
     */
    public function updating(BannerNotification $bannerNotification)
    {

    }
    /**
     * Handle the BannerNotification "updated" event.
     *
     * @param  \Core\Notification\Models\BannerNotification  $bannerNotification
     * @return void
     */
    public function updated(BannerNotification $bannerNotification)
    {

    }
    /**
     * Handle the BannerNotification "saving" event.
     *
     * @param  \Core\Notification\Models\BannerNotification  $bannerNotification
     * @return void
     */
    public function saving(BannerNotification $bannerNotification)
    {

    }
    /**
     * Handle the BannerNotification "saved" event.
     *
     * @param  \Core\Notification\Models\BannerNotification  $bannerNotification
     * @return void
     */
    public function saved(BannerNotification $bannerNotification)
    {

    }

    /**
     * Handle the BannerNotification "deleted" event.
     *
     * @param  \Core\Notification\Models\BannerNotification  $bannerNotification
     * @return void
     */
    public function deleted(BannerNotification $bannerNotification)
    {
      
    }

    /**
     * Handle the BannerNotification "restored" event.
     *
     * @param  \Core\Notification\Models\BannerNotification  $bannerNotification
     * @return void
     */
    public function restored(BannerNotification $bannerNotification)
    {
        //
    }

    /**
     * Handle the BannerNotification "force deleted" event.
     *
     * @param  \Core\Notification\Models\BannerNotification  $bannerNotification
     * @return void
     */
    public function forceDeleted(BannerNotification $bannerNotification)
    {
        //
    }
}
