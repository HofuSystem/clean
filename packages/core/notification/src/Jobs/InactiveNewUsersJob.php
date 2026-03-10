<?php

namespace Core\Notification\Jobs;

use Core\Notification\Models\Notification;
use Core\Settings\Services\SettingsService;
use Core\Users\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class InactiveNewUsersJob implements ShouldQueue
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
            $inactiveNewUsersNotificationTitle = SettingsService::getDataBaseSetting('inactive_new_users_notification_title');
            $inactiveNewUsersNotificationBody = SettingsService::getDataBaseSetting('inactive_new_users_notification_body');
            if ($inactiveNewUsersNotificationTitle && $inactiveNewUsersNotificationBody) {
                User::whereBetween('created_at', [now()->subDays(4), now()->subDays(3)])
                    ->whereDoesntHave('orders')
                    ->get()
                    ->each(function ($user) use ($inactiveNewUsersNotificationTitle, $inactiveNewUsersNotificationBody) {
                        Notification::create([
                            'types'    => json_encode(['apps']),
                            'for'      => 'users',
                            'for_data' => json_encode([$user->id]),
                            'title'    => $inactiveNewUsersNotificationTitle,
                            'body'     => $inactiveNewUsersNotificationBody,
                            'media'    => null,
                        ]);
                        $user->update(['motivated_at' => now()]);
                    });
            }
        } catch (\Throwable $th) {
            logger(__CLASS__ . ' was faild ' . now() . ' ' . $th->getMessage());
        }
    }
}
