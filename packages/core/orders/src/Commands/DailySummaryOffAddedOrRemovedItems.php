<?php

namespace Core\Orders\Commands;

use Core\Notification\Services\TelegramNotificationService;
use Core\Orders\Models\Order;
use Illuminate\Console\Command;

class DailySummaryOffAddedOrRemovedItems extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:daily-summary-off-added-or-removed-items';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Daily summary off added or removed items';

   
    public function handle()
    {
        $orders = Order::testAccounts(false)
        ->whereBetween('created_at', [now()->startOfDay(), now()->endOfDay()])
        ->whereHas('items', function($query){
            $query->withTrashed()
            ->whereNotNull('deleted_at')
            ->orWhereNotNull('update_by_admin')
            ->orWhereNotNull('add_by_admin');
        })
        ->get();
        if($orders->count() > 0){
            $telegramNotificationService = new TelegramNotificationService();
            $telegramNotificationService->sendMessage("@cleanstationreports", $telegramNotificationService->formatDailySummaryMessage($orders));
        }
     
    }
}
