<?php

namespace Core\Orders\Commands;

use Carbon\Carbon;
use Core\Notification\Services\TelegramNotificationService;
use Core\Orders\Models\Order;
use Illuminate\Console\Command;

class OrderIsLateForDelivery extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:order-is-late-for-delivery';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Order is late for delivery';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $orders = Order::testAccounts(false)
        ->whereNotIn('status', ['delivered','finished','canceled','pending_payment','failed_payment','canceled_payment'])
        ->whereHas('orderRepresentatives', function($query){
            $query->where('type', 'delivery')
                ->whereDate('date', Carbon::now()->format('Y-m-d'))
                ->whereBetween('to_time', [Carbon::now(), Carbon::now()->subMinutes(1)]);
        })
        ->get();
        $telegramNotificationService = new TelegramNotificationService();
        foreach ($orders as $order) {
            $telegramNotificationService->sendMessage("@cleanstationsupport", $telegramNotificationService->formatOrderIsLateForDeliveryMessage($order));
        }
        
    }
}
