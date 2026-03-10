<?php

namespace Core\Notification\Console\Commands;

use Core\Notification\Jobs\InactiveAfterOrderJob;
use Illuminate\Console\Command;

class SendInactiveAfterOrderNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-inactive-after-order-notification';

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
        InactiveAfterOrderJob::dispatch();

    }
}
