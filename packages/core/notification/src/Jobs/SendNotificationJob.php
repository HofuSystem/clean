<?php

namespace Core\Notification\Jobs;

use Core\Notification\Helpers\NotificationsManger;
use Core\Notification\Models\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct(protected Notification $notification)
    {
        $this->afterCommit();
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        NotificationsManger::getInstance()->sendNotification($this->notification);
    }
}
