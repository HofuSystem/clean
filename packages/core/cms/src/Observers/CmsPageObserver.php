<?php

namespace Core\CMS\Observers;

use Core\CMS\Models\CmsPage;

class CmsPageObserver
{
    /**
     * Handle the CmsPage "creating" event.
     *
     * @param  \Core\CMS\Models\CmsPage  $cmsPage
     * @return void
     */
    public function creating(CmsPage $cmsPage)
    {
    
    }
    /**
     * Handle the CmsPage "created" event.
     *
     * @param  \Core\CMS\Models\CmsPage  $cmsPage
     * @return void
     */
    public function created(CmsPage $cmsPage)
    {
    
    }

    /**
     * Handle the CmsPage "updating" event.
     *
     * @param  \Core\CMS\Models\CmsPage  $cmsPage
     * @return void
     */
    public function updating(CmsPage $cmsPage)
    {

    }
    /**
     * Handle the CmsPage "updated" event.
     *
     * @param  \Core\CMS\Models\CmsPage  $cmsPage
     * @return void
     */
    public function updated(CmsPage $cmsPage)
    {

    }
    /**
     * Handle the CmsPage "saving" event.
     *
     * @param  \Core\CMS\Models\CmsPage  $cmsPage
     * @return void
     */
    public function saving(CmsPage $cmsPage)
    {

    }
    /**
     * Handle the CmsPage "saved" event.
     *
     * @param  \Core\CMS\Models\CmsPage  $cmsPage
     * @return void
     */
    public function saved(CmsPage $cmsPage)
    {

    }

    /**
     * Handle the CmsPage "deleted" event.
     *
     * @param  \Core\CMS\Models\CmsPage  $cmsPage
     * @return void
     */
    public function deleted(CmsPage $cmsPage)
    {
      
    }

    /**
     * Handle the CmsPage "restored" event.
     *
     * @param  \Core\CMS\Models\CmsPage  $cmsPage
     * @return void
     */
    public function restored(CmsPage $cmsPage)
    {
        //
    }

    /**
     * Handle the CmsPage "force deleted" event.
     *
     * @param  \Core\CMS\Models\CmsPage  $cmsPage
     * @return void
     */
    public function forceDeleted(CmsPage $cmsPage)
    {
        //
    }
}
