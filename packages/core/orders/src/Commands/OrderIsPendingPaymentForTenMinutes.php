<?php

namespace Core\Orders\Commands;

use Core\Notification\Services\TelegramNotificationService;
use Core\Orders\Models\Order;
use Illuminate\Console\Command;

class OrderIsPendingPaymentForTenMinutes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:order-is-pending-payment-for-ten-minutes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Order is pending payment for ten minutes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $orders = Order::testAccounts(false)
        ->where('status', 'pending_payment')
        ->whereBetween('created_at', [now()->subMinutes(11), now()->subMinutes(10)])
        ->get();
        $telegramNotificationService = new TelegramNotificationService();
        foreach ($orders as $order) {
            $telegramNotificationService->sendMessage("@cleanstationneworders", $telegramNotificationService->formatOrderIsPendingPaymentForTenMinutesMessage($order));
        }
 
    }
}
