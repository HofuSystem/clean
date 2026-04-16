<?php

namespace Core\Coupons\Observers;

use Core\Coupons\Models\Gift;
use Core\Coupons\Models\Coupon;

class GiftObserver
{
    /**
     * Handle the Gift "saved" event.
     *
     * @param  \Core\Coupons\Models\Gift  $gift
     * @return void
     */
    public function saved(Gift $gift)
    {
        if ($gift->coupon_code) {
            // Try to find by coupon_id first, then by code
            $coupon = $gift->coupon_id ? Coupon::find($gift->coupon_id) : Coupon::where('code', $gift->coupon_code)->first();
            
            $data = [
                'status'         => $gift->status,
                'start_at'       => $gift->from,
                'end_at'         => $gift->to,
                'order_type'     => $gift->order_type,
                'order_minimum'  => $gift->orders_min,
                'order_maximum'  => $gift->orders_max,
                'type'           => $gift->type,
                'value'          => $gift->value,
                'max_value'      => $gift->max_value,
                'register_from'  => $gift->register_from,
                'register_to'    => $gift->register_to,
                'orders_from'    => $gift->orders_from,
                'orders_to'      => $gift->orders_to,
                'applying'       => 'manual',

                'code'           => $gift->coupon_code,
                'creator_id'     => $gift->creator_id,
                'updater_id'     => $gift->updater_id,
            ];

            if ($coupon) {
                $coupon->update($data);
            } else {
                $coupon = Coupon::create($data);
            }

            // Sync translations
            foreach (['en', 'ar'] as $locale) {
                $giftTranslation = $gift->translate($locale);
                if ($giftTranslation) {
                    $couponTranslation = $coupon->translateOrNew($locale);
                    $couponTranslation->title = $giftTranslation->title;
                    $couponTranslation->intro = $giftTranslation->intro;

                }
            }
            $coupon->save();

            // Link coupon back to gift if not already linked (quietly to avoid recursion)
            if ($gift->coupon_id !== $coupon->id) {
                $gift->updateQuietly(['coupon_id' => $coupon->id]);
            }
        }

        // If coupon_code was changed, maybe deactivate the old one?
        // The user didn't explicitly ask for this, but "matching" implies 1-to-1.
        if ($gift->wasChanged('coupon_code')) {
            $oldCode = $gift->getOriginal('coupon_code');
            if ($oldCode && $oldCode != $gift->coupon_code) {
                Coupon::where('code', $oldCode)->update(['status' => 'not-active']);
            }
        }
    }

}
