<?php

namespace Core\Categories\Observers;

use Core\Categories\Models\Slider;
use Core\Categories\Models\SliderView;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SliderObserver
{
    private function flushCategoriesCache()
    {
        try {
            if (Cache::supportsTags()) {
                Cache::tags(['categories_api'])->flush();
            } else {
                Cache::forget('categories_api');
            }
        } catch (\Throwable $e) {
            Log::warning('Failed to flush category cache: ' . $e->getMessage());
        }
    }
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
        } else if ($slider->isDirty('link')) {
            SliderView::updateOrCreate([
                'slider_id' => $slider->id,
                'url' => $link,
            ], [
                'uuid' => (string) Str::uuid(),
                'views_count' => 0
            ]);
        }

        $this->flushCategoriesCache();
    }

    /**
     * Handle the Slider "deleted" event.
     *
     * @param  \Core\Categories\Models\Slider  $slider
     * @return void
     */
    public function deleted(Slider $slider)
    {
        $this->flushCategoriesCache();
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
