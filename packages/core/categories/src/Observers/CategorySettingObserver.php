<?php

namespace Core\Categories\Observers;

use Core\Categories\Models\CategorySetting;

class CategorySettingObserver
{
    /**
     * Handle the CategorySetting "creating" event.
     *
     * @param  \Core\Categories\Models\CategorySetting  $categorySetting
     * @return void
     */
    public function creating(CategorySetting $categorySetting)
    {
    
    }
    /**
     * Handle the CategorySetting "created" event.
     *
     * @param  \Core\Categories\Models\CategorySetting  $categorySetting
     * @return void
     */
    public function created(CategorySetting $categorySetting)
    {
    
    }

    /**
     * Handle the CategorySetting "updating" event.
     *
     * @param  \Core\Categories\Models\CategorySetting  $categorySetting
     * @return void
     */
    public function updating(CategorySetting $categorySetting)
    {

    }
    /**
     * Handle the CategorySetting "updated" event.
     *
     * @param  \Core\Categories\Models\CategorySetting  $categorySetting
     * @return void
     */
    public function updated(CategorySetting $categorySetting)
    {

    }
    /**
     * Handle the CategorySetting "saving" event.
     *
     * @param  \Core\Categories\Models\CategorySetting  $categorySetting
     * @return void
     */
    public function saving(CategorySetting $categorySetting)
    {

    }
    /**
     * Handle the CategorySetting "saved" event.
     *
     * @param  \Core\Categories\Models\CategorySetting  $categorySetting
     * @return void
     */
    public function saved(CategorySetting $categorySetting)
    {

    }

    /**
     * Handle the CategorySetting "deleted" event.
     *
     * @param  \Core\Categories\Models\CategorySetting  $categorySetting
     * @return void
     */
    public function deleted(CategorySetting $categorySetting)
    {
      
    }

    /**
     * Handle the CategorySetting "restored" event.
     *
     * @param  \Core\Categories\Models\CategorySetting  $categorySetting
     * @return void
     */
    public function restored(CategorySetting $categorySetting)
    {
        //
    }

    /**
     * Handle the CategorySetting "force deleted" event.
     *
     * @param  \Core\Categories\Models\CategorySetting  $categorySetting
     * @return void
     */
    public function forceDeleted(CategorySetting $categorySetting)
    {
        //
    }
}
