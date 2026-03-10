<?php

namespace Core\CMS\Observers;

use Core\CMS\Models\CmsPageDetail;

class CmsPageDetailObserver
{
    /**
     * Handle the CmsPageDetail "creating" event.
     *
     * @param  \Core\CMS\Models\CmsPageDetail  $cmsPageDetail
     * @return void
     */
    public function creating(CmsPageDetail $cmsPageDetail)
    {
    
    }
    /**
     * Handle the CmsPageDetail "created" event.
     *
     * @param  \Core\CMS\Models\CmsPageDetail  $cmsPageDetail
     * @return void
     */
    public function created(CmsPageDetail $cmsPageDetail)
    {
    
    }

    /**
     * Handle the CmsPageDetail "updating" event.
     *
     * @param  \Core\CMS\Models\CmsPageDetail  $cmsPageDetail
     * @return void
     */
    public function updating(CmsPageDetail $cmsPageDetail)
    {

    }
    /**
     * Handle the CmsPageDetail "updated" event.
     *
     * @param  \Core\CMS\Models\CmsPageDetail  $cmsPageDetail
     * @return void
     */
    public function updated(CmsPageDetail $cmsPageDetail)
    {

    }
    /**
     * Handle the CmsPageDetail "saving" event.
     *
     * @param  \Core\CMS\Models\CmsPageDetail  $cmsPageDetail
     * @return void
     */
    public function saving(CmsPageDetail $cmsPageDetail)
    {

    }
    /**
     * Handle the CmsPageDetail "saved" event.
     *
     * @param  \Core\CMS\Models\CmsPageDetail  $cmsPageDetail
     * @return void
     */
    public function saved(CmsPageDetail $cmsPageDetail)
    {

    }

    /**
     * Handle the CmsPageDetail "deleted" event.
     *
     * @param  \Core\CMS\Models\CmsPageDetail  $cmsPageDetail
     * @return void
     */
    public function deleted(CmsPageDetail $cmsPageDetail)
    {
      
    }

    /**
     * Handle the CmsPageDetail "restored" event.
     *
     * @param  \Core\CMS\Models\CmsPageDetail  $cmsPageDetail
     * @return void
     */
    public function restored(CmsPageDetail $cmsPageDetail)
    {
        //
    }

    /**
     * Handle the CmsPageDetail "force deleted" event.
     *
     * @param  \Core\CMS\Models\CmsPageDetail  $cmsPageDetail
     * @return void
     */
    public function forceDeleted(CmsPageDetail $cmsPageDetail)
    {
        //
    }
}
