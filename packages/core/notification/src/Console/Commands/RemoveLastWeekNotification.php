<?php

namespace Core\Notification\Console\Commands;

use Core\Notification\Jobs\RemoveLastWeekJobs;
use Core\Notification\Jobs\WelcomeNotificationJob;
use Illuminate\Console\Command;

class RemoveLastWeekNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:delete_old_week_notification';

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
        RemoveLastWeekJobs::dispatch();
    }
}
