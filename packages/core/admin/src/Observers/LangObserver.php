<?php

namespace Core\Admin\Observers;

use Core\Admin\Models\Language;

class LangObserver
{
    /**
     * Handle the Language "created" event.
     *
     * @param  \App\Models\Language  $language
     * @return void
     */
    public function created(Language $language)
    {
    
    }

    /**
     * Handle the Language "updated" event.
     *
     * @param  \App\Models\Language  $language
     * @return void
     */
    public function updated(Language $language)
    {

    }

    /**
     * Handle the Language "deleted" event.
     *
     * @param  \App\Models\Language  $language
     * @return void
     */
    public function deleted(Language $language)
    {
      
    }

    /**
     * Handle the Language "restored" event.
     *
     * @param  \App\Models\Language  $language
     * @return void
     */
    public function restored(Language $language)
    {
        //
    }

    /**
     * Handle the Language "force deleted" event.
     *
     * @param  \App\Models\Language  $language
     * @return void
     */
    public function forceDeleted(Language $language)
    {
        //
    }
}
