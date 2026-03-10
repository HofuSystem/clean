<?php

namespace Core\Notification\Console\Commands;

use Core\Notification\Jobs\CelebrateBirthdayJob;
use Illuminate\Console\Command;

class SendCelebrateBirthdayNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-celebrate-birthday-notification';

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
        CelebrateBirthdayJob::dispatch();
    }
}
