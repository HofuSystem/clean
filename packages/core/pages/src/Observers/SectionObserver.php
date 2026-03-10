<?php

namespace Core\Pages\Observers;

use Core\Pages\Models\Section;

class SectionObserver
{
    /**
     * Handle the Section "creating" event.
     *
     * @param  \Core\Pages\Models\Section  $section
     * @return void
     */
    public function creating(Section $section)
    {
    
    }
    /**
     * Handle the Section "created" event.
     *
     * @param  \Core\Pages\Models\Section  $section
     * @return void
     */
    public function created(Section $section)
    {
    
    }

    /**
     * Handle the Section "updating" event.
     *
     * @param  \Core\Pages\Models\Section  $section
     * @return void
     */
    public function updating(Section $section)
    {

    }
    /**
     * Handle the Section "updated" event.
     *
     * @param  \Core\Pages\Models\Section  $section
     * @return void
     */
    public function updated(Section $section)
    {

    }
    /**
     * Handle the Section "saving" event.
     *
     * @param  \Core\Pages\Models\Section  $section
     * @return void
     */
    public function saving(Section $section)
    {

    }
    /**
     * Handle the Section "saved" event.
     *
     * @param  \Core\Pages\Models\Section  $section
     * @return void
     */
    public function saved(Section $section)
    {

    }

    /**
     * Handle the Section "deleted" event.
     *
     * @param  \Core\Pages\Models\Section  $section
     * @return void
     */
    public function deleted(Section $section)
    {
      
    }

    /**
     * Handle the Section "restored" event.
     *
     * @param  \Core\Pages\Models\Section  $section
     * @return void
     */
    public function restored(Section $section)
    {
        //
    }

    /**
     * Handle the Section "force deleted" event.
     *
     * @param  \Core\Pages\Models\Section  $section
     * @return void
     */
    public function forceDeleted(Section $section)
    {
        //
    }
}
