<?php

namespace Core\Notification\Jobs;

use Core\Notification\Models\Notification;
use Core\Settings\Services\SettingsService;
use Core\Users\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class InactiveAfterOrderJob implements ShouldQueue
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
            $inactiveAfterOrderNotificationTitle = SettingsService::getDataBaseSetting('inactive_after_order_notification_title');
            $inactiveAfterOrderNotificationBody = SettingsService::getDataBaseSetting('inactive_after_order_notification_body');
            if ($inactiveAfterOrderNotificationTitle && $inactiveAfterOrderNotificationBody) {
                User::whereHas('orders', function ($q) {
                    $q->whereBetween('created_at', [now()->subDays(8), now()->subDays(7)]);
                })
                    ->whereDoesntHave('orders', function ($q) {
                        $q->where('created_at', '>', now()->subDays(7));
                    })
                    ->get()
                    ->each(function ($user) use ($inactiveAfterOrderNotificationTitle, $inactiveAfterOrderNotificationBody) {
                        Notification::create([
                            'types'    => json_encode(['apps']),
                            'for'      => 'users',
                            'for_data' => json_encode([$user->id]),
                            'title'    => $inactiveAfterOrderNotificationTitle,
                            'body'     => $inactiveAfterOrderNotificationBody,
                            'media'    => null,
                        ]);
                    });
            }
        } catch (\Throwable $th) {
            logger(__CLASS__ . ' was faild ' . now() . ' ' . $th->getMessage());
        }
    }
}
