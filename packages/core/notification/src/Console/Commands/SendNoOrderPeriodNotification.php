<?php

namespace Core\Notification\Console\Commands;

use Core\Notification\Jobs\NoOrderPeriodJob;
use Illuminate\Console\Command;

class SendNoOrderPeriodNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-no-order-period-notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send notifications to users after periods without orders';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Dispatching no order period notification job...');
        NoOrderPeriodJob::dispatch();
        $this->info('No order period notification job dispatched successfully.');
    }
}
