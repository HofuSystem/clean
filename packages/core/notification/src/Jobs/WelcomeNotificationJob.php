<?php

namespace Core\Notification\Jobs;

use Core\Notification\Models\Notification;
use Core\Settings\Services\SettingsService;
use Core\Users\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class WelcomeNotificationJob implements ShouldQueue
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
        logger(__CLASS__.' was running '.now());
        try {
            $welcomeNotificationTitle = SettingsService::getDataBaseSetting('welcome_notification_title');
            $welcomeNotificationBody = SettingsService::getDataBaseSetting('welcome_notification_body');
            if($welcomeNotificationTitle && $welcomeNotificationBody){
            //code...
            User::whereBetween('created_at', [now()->subMinutes(6), now()->subMinutes(5)])
            ->get()
            ->each(function ($user) use ($welcomeNotificationTitle, $welcomeNotificationBody) {
                Notification::create([
                    'types'    => json_encode(['apps']),
                    'for'      => 'users',
                    'for_data' => json_encode([$user->id]),
                    'title'    => $welcomeNotificationTitle,
                    'body'     => $welcomeNotificationBody,
                    'media'    => null,
                ]);
            });
            }
        } catch (\Throwable $th) {
            logger(__CLASS__.' was faild '.now().' '.$th->getMessage());

        }
   
    }
}
