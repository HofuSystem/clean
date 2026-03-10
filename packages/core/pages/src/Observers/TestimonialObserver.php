<?php

namespace Core\Pages\Observers;

use Core\Pages\Models\Testimonial;

class TestimonialObserver
{
    /**
     * Handle the Testimonial "creating" event.
     *
     * @param  \Core\Pages\Models\Testimonial  $testimonial
     * @return void
     */
    public function creating(Testimonial $testimonial)
    {
    
    }
    /**
     * Handle the Testimonial "created" event.
     *
     * @param  \Core\Pages\Models\Testimonial  $testimonial
     * @return void
     */
    public function created(Testimonial $testimonial)
    {
    
    }

    /**
     * Handle the Testimonial "updating" event.
     *
     * @param  \Core\Pages\Models\Testimonial  $testimonial
     * @return void
     */
    public function updating(Testimonial $testimonial)
    {

    }
    /**
     * Handle the Testimonial "updated" event.
     *
     * @param  \Core\Pages\Models\Testimonial  $testimonial
     * @return void
     */
    public function updated(Testimonial $testimonial)
    {

    }
    /**
     * Handle the Testimonial "saving" event.
     *
     * @param  \Core\Pages\Models\Testimonial  $testimonial
     * @return void
     */
    public function saving(Testimonial $testimonial)
    {

    }
    /**
     * Handle the Testimonial "saved" event.
     *
     * @param  \Core\Pages\Models\Testimonial  $testimonial
     * @return void
     */
    public function saved(Testimonial $testimonial)
    {

    }

    /**
     * Handle the Testimonial "deleted" event.
     *
     * @param  \Core\Pages\Models\Testimonial  $testimonial
     * @return void
     */
    public function deleted(Testimonial $testimonial)
    {
      
    }

    /**
     * Handle the Testimonial "restored" event.
     *
     * @param  \Core\Pages\Models\Testimonial  $testimonial
     * @return void
     */
    public function restored(Testimonial $testimonial)
    {
        //
    }

    /**
     * Handle the Testimonial "force deleted" event.
     *
     * @param  \Core\Pages\Models\Testimonial  $testimonial
     * @return void
     */
    public function forceDeleted(Testimonial $testimonial)
    {
        //
    }
}
