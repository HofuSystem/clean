<?php

namespace Core\Orders\Commands;

use Core\Notification\Services\TelegramNotificationService;
use Core\Orders\Models\Order;
use Illuminate\Console\Command;

class ShortPerformanceReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:short-performance-report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Short performance report';

    /**
     * Execute the console command.
     */
    public function sendReport($dateFrom, $dateTo, $type)
    {
        $orders = Order::testAccounts(false)
            ->whereNotIn('status', ['pending_payment', 'failed_payment', 'canceled_payment'])
            ->whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
            ->get();
        $ordersCount    = $orders->count();
        $ordersTotal    = $orders->sum('total_price');
        $ordersCost     = $orders->sum('total_cost');
        $revenue        = $ordersTotal - $ordersCost;
        $telegramNotificationService = new TelegramNotificationService();

        $message = $telegramNotificationService->formatShortPerformanceReportMessage(
            $dateFrom,
            $dateTo,
            $type,
            $ordersCount,
            $ordersTotal,
            $ordersCost,
            $revenue
        );
        $telegramNotificationService->sendMessage('@cleanstationreports', $message);
    }
    public function handle()
    {
        $now             = \Carbon\Carbon::now();
        $today           = $now->toDateString();
        $isFriday        = $now->isFriday();
        $isLastDayOfMonth= $now->isEndOfMonth();
        $isLastDayOfYear = $now->isEndOfYear();


        $this->sendReport($today, $today, 'day');

        if ($isFriday) {
            $startOfWeek = $now->copy()->startOfWeek(\Carbon\Carbon::SATURDAY)->toDateString();
            $endOfWeek = $now->copy()->endOfWeek(\Carbon\Carbon::FRIDAY)->toDateString();
            $this->sendReport($startOfWeek, $endOfWeek, 'week');
        }

        if ($isLastDayOfMonth) {
            $startOfMonth = $now->copy()->startOfMonth()->toDateString();
            $endOfMonth = $now->copy()->endOfMonth()->toDateString();
            $this->sendReport($startOfMonth, $endOfMonth, 'month');
        }

        if ($isLastDayOfYear) {
            $startOfYear = $now->copy()->startOfYear()->toDateString();
            $endOfYear = $now->copy()->endOfYear()->toDateString();
            $this->sendReport($startOfYear, $endOfYear, 'year');
        }
    }
}
