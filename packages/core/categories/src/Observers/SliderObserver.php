<?php

namespace Core\Categories\Observers;

use Core\Categories\Models\Slider;
use Core\Categories\Models\SliderView;
use Illuminate\Support\Str;

class SliderObserver
{
    /**
     * Handle the Slider "creating" event.
     *
     * @param  \Core\Categories\Models\Slider  $slider
     * @return void
     */
    public function creating(Slider $slider)
    {

    }
    /**
     * Handle the Slider "created" event.
     *
     * @param  \Core\Categories\Models\Slider  $slider
     * @return void
     */
    public function created(Slider $slider)
    {

    }

    /**
     * Handle the Slider "updating" event.
     *
     * @param  \Core\Categories\Models\Slider  $slider
     * @return void
     */
    public function updating(Slider $slider)
    {

    }
    /**
     * Handle the Slider "updated" event.
     *
     * @param  \Core\Categories\Models\Slider  $slider
     * @return void
     */
    public function updated(Slider $slider)
    {

    }
    /**
     * Handle the Slider "saving" event.
     *
     * @param  \Core\Categories\Models\Slider  $slider
     * @return void
     */
    public function saving(Slider $slider)
    {

    }

    /**
     * Handle the Slider "saved" event.
     * Fires after both create and update.
     * Creates or refreshes the SliderView UUID entry when the link changes.
     *
     * @param  \Core\Categories\Models\Slider  $slider
     * @return void
     */
    public function saved(Slider $slider)
    {
        $link = $slider->link;

        if (!$link) {
            // No link — nothing to track
            return;
        }
        if($slider->isDirty('link')){
            SliderView::updateOrCreate([
                'slider_id' => $slider->id,
                'url' => $link,
            ],[
                'uuid' => (string) Str::uuid(),
                'views_count' => 0
            ]);
        }
    }

    /**
     * Handle the Slider "deleted" event.
     *
     * @param  \Core\Categories\Models\Slider  $slider
     * @return void
     */
    public function deleted(Slider $slider)
    {

    }

    /**
     * Handle the Slider "restored" event.
     *
     * @param  \Core\Categories\Models\Slider  $slider
     * @return void
     */
    public function restored(Slider $slider)
    {
        //
    }

    /**
     * Handle the Slider "force deleted" event.
     *
     * @param  \Core\Categories\Models\Slider  $slider
     * @return void
     */
    public function forceDeleted(Slider $slider)
    {
        //
    }
}
