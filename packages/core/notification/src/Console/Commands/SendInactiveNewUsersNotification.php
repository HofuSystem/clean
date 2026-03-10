<?php

namespace Core\Notification\Console\Commands;

use Core\Notification\Jobs\InactiveNewUsersJob;
use Illuminate\Console\Command;

class SendInactiveNewUsersNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-inactive-new-users-notification';

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
        InactiveNewUsersJob::dispatch();
    }
}
