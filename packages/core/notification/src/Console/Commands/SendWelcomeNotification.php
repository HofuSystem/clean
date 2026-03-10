<?php

namespace Core\Notification\Console\Commands;

use Core\Notification\Jobs\WelcomeNotificationJob;
use Illuminate\Console\Command;

class SendWelcomeNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-welcome-notification';

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
        WelcomeNotificationJob::dispatch();
    }
}
