<?php

namespace Core\Coupons\Observers;

use Core\Coupons\Models\Coupon;

class CouponObserver
{
    /**
     * Handle the Coupon "creating" event.
     *
     * @param  \Core\Coupons\Models\Coupon  $coupon
     * @return void
     */
    public function creating(Coupon $coupon)
    {
    
    }
    /**
     * Handle the Coupon "created" event.
     *
     * @param  \Core\Coupons\Models\Coupon  $coupon
     * @return void
     */
    public function created(Coupon $coupon)
    {
    
    }

    /**
     * Handle the Coupon "updating" event.
     *
     * @param  \Core\Coupons\Models\Coupon  $coupon
     * @return void
     */
    public function updating(Coupon $coupon)
    {

    }
    /**
     * Handle the Coupon "updated" event.
     *
     * @param  \Core\Coupons\Models\Coupon  $coupon
     * @return void
     */
    public function updated(Coupon $coupon)
    {

    }
    /**
     * Handle the Coupon "saving" event.
     *
     * @param  \Core\Coupons\Models\Coupon  $coupon
     * @return void
     */
    public function saving(Coupon $coupon)
    {

    }
    /**
     * Handle the Coupon "saved" event.
     *
     * @param  \Core\Coupons\Models\Coupon  $coupon
     * @return void
     */
    public function saved(Coupon $coupon)
    {

    }

    /**
     * Handle the Coupon "deleted" event.
     *
     * @param  \Core\Coupons\Models\Coupon  $coupon
     * @return void
     */
    public function deleted(Coupon $coupon)
    {
      
    }

    /**
     * Handle the Coupon "restored" event.
     *
     * @param  \Core\Coupons\Models\Coupon  $coupon
     * @return void
     */
    public function restored(Coupon $coupon)
    {
        //
    }

    /**
     * Handle the Coupon "force deleted" event.
     *
     * @param  \Core\Coupons\Models\Coupon  $coupon
     * @return void
     */
    public function forceDeleted(Coupon $coupon)
    {
        //
    }
}
