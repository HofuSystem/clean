<?php

namespace Core\Notification\Console\Commands;

use Core\Notification\Jobs\FeedbackAfterCompletionJob;
use Core\Notification\Jobs\WelcomeNotificationJob;
use Illuminate\Console\Command;

class SendFeedBackNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-feed-back';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        FeedbackAfterCompletionJob::dispatch();
    }
}
