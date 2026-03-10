<?php

namespace Core\Orders\Commands;

use Carbon\Carbon;
use Core\Notification\Services\TelegramNotificationService;
use Core\Orders\Models\Order;
use Illuminate\Console\Command;

class OrderIsLateForPickup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:order-is-late-for-pickup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Order is late for pickup';

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
                ->whereBetween('to_time', [Carbon::now(), Carbon::now()->subMinutes(1)]);
        })
        ->get();
        $telegramNotificationService = new TelegramNotificationService();
        foreach ($orders as $order) {
            $telegramNotificationService->sendMessage("@cleanstationsupport", $telegramNotificationService->formatOrderIsLateForPickupMessage($order));
        }
        
    }
}
