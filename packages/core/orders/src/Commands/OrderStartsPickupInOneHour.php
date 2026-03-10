<?php

namespace Core\Orders\Commands;

use Carbon\Carbon;
use Core\Notification\Services\TelegramNotificationService;
use Core\Orders\Models\Order;
use Illuminate\Console\Command;

class OrderStartsPickupInOneHour extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:order-starts-pickup-in-one-hour';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Order starts pickup in one hour';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $orders = Order::testAccounts(false)
        ->whereIn('status', ['pending', 'receiving_driver_accepted'])
        ->whereHas('orderRepresentatives', function($query){
            $query->where('type', 'receiver')
                ->whereDate('date', Carbon::now()->format('Y-m-d'))
                ->whereBetween('time', [Carbon::now()->subMinutes(60), Carbon::now()->subMinutes(59)]);
        })
        ->get();
        $telegramNotificationService = new TelegramNotificationService();
        foreach ($orders as $order) {
            $telegramNotificationService->sendMessage("@cleanstationnoperation", $telegramNotificationService->formatOrderStartsPickupInOneHourMessage($order));
        }
        
    }
}
