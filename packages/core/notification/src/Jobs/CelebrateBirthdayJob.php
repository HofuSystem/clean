<?php

namespace Core\Notification\Jobs;

use Core\Notification\Models\Notification;
use Core\Orders\Models\Cart;
use Core\Settings\Services\SettingsService;
use Core\Users\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class CelebrateBirthdayJob implements ShouldQueue
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
            $celebrateBirthdayNotificationTitle = SettingsService::getDataBaseSetting('celebrate_birthday_notification_title');
            $celebrateBirthdayNotificationBody = SettingsService::getDataBaseSetting('celebrate_birthday_notification_body');
            if ($celebrateBirthdayNotificationTitle && $celebrateBirthdayNotificationBody) {
                User::where('date_of_birth', now()->format('m-d'))
                    ->get()
                    ->each(function ($user) use ($celebrateBirthdayNotificationTitle, $celebrateBirthdayNotificationBody) {
                        Notification::create([
                            'types'    => json_encode(['apps']),
                            'for'      => 'users',
                            'for_data' => json_encode([$user->id]),
                            'title'    => $celebrateBirthdayNotificationTitle,
                            'body'     => $celebrateBirthdayNotificationBody,
                            'media'    => null,
                        ]);
                    });
            }
        } catch (\Throwable $th) {
            logger(__CLASS__ . ' was faild ' . now() . ' ' . $th->getMessage());
        }
    }
}
