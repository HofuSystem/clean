<?php

namespace Core\Info\Observers;

use Core\Info\Models\District;

class DistrictObserver
{
    /**
     * Handle the District "creating" event.
     *
     * @param  \Core\Info\Models\District  $district
     * @return void
     */
    public function creating(District $district)
    {
    
    }
    /**
     * Handle the District "created" event.
     *
     * @param  \Core\Info\Models\District  $district
     * @return void
     */
    public function created(District $district)
    {
    
    }

    /**
     * Handle the District "updating" event.
     *
     * @param  \Core\Info\Models\District  $district
     * @return void
     */
    public function updating(District $district)
    {

    }
    /**
     * Handle the District "updated" event.
     *
     * @param  \Core\Info\Models\District  $district
     * @return void
     */
    public function updated(District $district)
    {
        if ($district->isDirty('status') && $district->status === 'active') {
            \Core\Info\Services\CoverageNotificationService::notifySubscribersOnActivation(null, $district->id);
        }
    }
    /**
     * Handle the District "saving" event.
     *
     * @param  \Core\Info\Models\District  $district
     * @return void
     */
    public function saving(District $district)
    {

    }
    /**
     * Handle the District "saved" event.
     *
     * @param  \Core\Info\Models\District  $district
     * @return void
     */
    public function saved(District $district)
    {
        $this->clearCache($district);
    }

    /**
     * Handle the District "deleted" event.
     *
     * @param  \Core\Info\Models\District  $district
     * @return void
     */
    public function deleted(District $district)
    {
        $this->clearCache($district);
    }

    /**
     * Handle the District "restored" event.
     *
     * @param  \Core\Info\Models\District  $district
     * @return void
     */
    public function restored(District $district)
    {
        $this->clearCache($district);
    }

    /**
     * Handle the District "force deleted" event.
     *
     * @param  \Core\Info\Models\District  $district
     * @return void
     */
    public function forceDeleted(District $district)
    {
        $this->clearCache($district);
    }

    protected function clearCache(District $district)
    {
        foreach (['ar', 'en'] as $lang) {
            \Illuminate\Support\Facades\Cache::forget("api_districts_city_{$district->city_id}_{$lang}");
        }
    }
}
