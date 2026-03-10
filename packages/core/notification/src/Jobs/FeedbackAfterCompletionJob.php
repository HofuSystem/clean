<?php

namespace Core\Notification\Jobs;

use Carbon\Carbon;
use Core\Notification\Models\Notification;
use Core\Orders\Models\Order;
use Core\Settings\Services\SettingsService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class FeedbackAfterCompletionJob implements ShouldQueue
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
            $feedbackAfterCompletionNotificationTitle = SettingsService::getDataBaseSetting('feedback_after_completion_notification_title');
            $feedbackAfterCompletionNotificationBody = SettingsService::getDataBaseSetting('feedback_after_completion_notification_body');
            if($feedbackAfterCompletionNotificationTitle && $feedbackAfterCompletionNotificationBody){
            Order::whereIn('status', ['finished', 'delivered'])
                ->whereBetween('created_at', [now()->subDays(7), now()])
                ->whereNull('feedback_requested_at')
                ->whereBetween('updated_at', [now()->subHours(2), now()->subHours(1)])
                ->with('user')
                ->get()
                ->each(function ($order) use ($feedbackAfterCompletionNotificationTitle, $feedbackAfterCompletionNotificationBody) {
                    Notification::create([
                        'types'    => json_encode(['apps']),
                        'for'      => 'users',
                        'for_data' => json_encode([$order->user_id]),
                        'title'    => $feedbackAfterCompletionNotificationTitle,
                        'body'     => $feedbackAfterCompletionNotificationBody,
                        'media'    => null,
                        'order_id' => $order->id,
                    ]);
                    $order->update(['feedback_requested_at' => now()]);
                });
            }
        } catch (\Throwable $th) {
            logger(__CLASS__ . ' was faild ' . now() . ' ' . $th->getMessage());
        }
    }
}
