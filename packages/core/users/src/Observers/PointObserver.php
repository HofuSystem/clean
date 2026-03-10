<?php

namespace Core\Users\Observers;

use Core\Users\Models\Point;
use Core\Users\Models\User;
use Core\Users\Services\PointsService;

class PointObserver
{
    /**
     * Handle the Point "creating" event.
     *
     * @param  \Core\Users\Models\Point  $point
     * @return void
     */
    public function creating(Point $point)
    {
      
    }
    /**
     * Handle the Point "created" event.
     *
     * @param  \Core\Users\Models\Point  $point
     * @return void
     */
    public function created(Point $point)
    {
       
    }

    /**
     * Handle the Point "updating" event.
     *
     * @param  \Core\Users\Models\Point  $point
     * @return void
     */
    public function updating(Point $point)
    {

    }
    /**
     * Handle the Point "updated" event.
     *
     * @param  \Core\Users\Models\Point  $point
     * @return void
     */
    public function updated(Point $point)
    {
        
    }
    /**
     * Handle the Point "saving" event.
     *
     * @param  \Core\Users\Models\Point  $point
     * @return void
     */
    public function saving(Point $point)
    {

    }
    /**
     * Handle the Point "saved" event.
     *
     * @param  \Core\Users\Models\Point  $point
     * @return void
     */
    public function saved(Point $point)
    {
        PointsService::updateUserPoints($point->user_id);
    }

    /**
     * Handle the Point "deleted" event.
     *
     * @param  \Core\Users\Models\Point  $point
     * @return void
     */
    public function deleted(Point $point)
    {
        PointsService::updateUserPoints($point->user_id);
    }

    /**
     * Handle the Point "restored" event.
     *
     * @param  \Core\Users\Models\Point  $point
     * @return void
     */
    public function restored(Point $point)
    {
        PointsService::updateUserPoints($point->user_id);
    }

    /**
     * Handle the Point "force deleted" event.
     *
     * @param  \Core\Users\Models\Point  $point
     * @return void
     */
    public function forceDeleted(Point $point)
    {
        PointsService::updateUserPoints($point->user_id);
    }
}
