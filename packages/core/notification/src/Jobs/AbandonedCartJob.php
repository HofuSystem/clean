<?php

namespace Core\Notification\Jobs;

use Core\Notification\Models\Notification;
use Core\Orders\Models\Cart;
use Core\Settings\Helpers\ToolHelper;
use Core\Settings\Services\SettingsService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class AbandonedCartJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        logger(__CLASS__ . ' was running ' . now());

        try {
            $abandonedCartNotificationTitle = SettingsService::getDataBaseSetting('abandoned_cart_notification_title');
            $abandonedCartNotificationBody = SettingsService::getDataBaseSetting('abandoned_cart_notification_body');
            if ($abandonedCartNotificationTitle && $abandonedCartNotificationBody) {
                Cart::whereBetween('updated_at', [now()->subMinutes(31), now()->subMinutes(30)])
                    ->with('user')
                    ->get()
                    ->each(function ($cart) use ($abandonedCartNotificationTitle, $abandonedCartNotificationBody) {

                        $cartItems      =  json_decode($cart->data);
                        $cartItems      =  ToolHelper::isJson($cartItems) ? json_decode($cartItems) : $cartItems;
                        if (count($cartItems) > 0) {
                            Notification::create([
                                'types'    => json_encode(['apps']),
                                'for'      => 'users',
                                'for_data' => json_encode([$cart->user_id]),
                                'title'    => $abandonedCartNotificationTitle,
                                'body'     => $abandonedCartNotificationBody,
                                'media'    => null,
                            ]);
                        }
                    });
            }
        } catch (\Throwable $th) {
            logger(__CLASS__ . ' was faild ' . now() . ' ' . $th->getMessage());
        }
    }
}
