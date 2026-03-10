<?php

namespace Core\Orders\Commands;

use Carbon\Carbon;
use Core\Notification\Services\TelegramNotificationService;
use Core\Orders\Models\Order;
use Illuminate\Console\Command;

class OrderStartsDeliveryInOneHour extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:order-starts-delivery-in-one-hour';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Order starts delivery in one hour';

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
                ->whereBetween('time', [Carbon::now()->subMinutes(60), Carbon::now()->subMinutes(59)]);
        })
        ->get();
        $telegramNotificationService = new TelegramNotificationService();
        foreach ($orders as $order) {
            $telegramNotificationService->sendMessage("@cleanstationnoperation", $telegramNotificationService->formatOrderStartsDeliveryInOneHourMessage($order));
        }
        
    }
}
