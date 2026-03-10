<?php

namespace Core\Orders\Commands;

use Core\Notification\Services\TelegramNotificationService;
use Core\Orders\Models\Order;
use Illuminate\Console\Command;

class CreateScheduledOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-scheduled-orders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create scheduled orders';


    public function handle()
    {
        // Get scheduled orders where type is 'date' and receiver_date is tomorrow
        $tomorrow = now()->addDay()->toDateString();
        $scheduledOrders = \Core\Orders\Models\OrderSchedule::query()
            ->where('type', 'date')
            ->whereDate('receiver_date', $tomorrow)
            ->get();
        $orderService = app(\Core\Orders\Services\OrdersService::class);

        foreach ($scheduledOrders as $schedule) {
            $data = [];
            $data['client_id'] = $schedule->client_id;
            $data['receiving_address_id'] = $schedule->receiver_address_id;
            $data['receiving_date'] = $schedule->receiver_date;
            $data['receiving_time'] = $schedule->receiver_time;
            $data['receiving_to_time'] = $schedule->receiver_to_time;

            $data['delivery_address_id'] = $schedule->delivery_address_id;
            $data['delivery_date'] = $schedule->delivery_date;
            $data['delivery_time'] = $schedule->delivery_time;
            $data['delivery_to_time'] = $schedule->delivery_to_time;

            $data['delivery_to_time'] = $schedule->delivery_to_time;
            $data['order_price'] = 0;
            $data['type'] = 'fastorder';
            $data['pay_type'] = 'contract';
            $data['desc'] = $schedule->note;
            $orderService->createOrder($data, [], $schedule->client);
        }

        // Handle 'day' type schedules where receiver_day is tomorrow's day name
        $orderReceivingDay = strtolower(now()->addDay()->format('l')); // e.g., 'Monday'
        $scheduledDayOrders = \Core\Orders\Models\OrderSchedule::query()
            ->where('type', 'day')
            ->where('receiver_day', $orderReceivingDay)
            ->get();
        foreach ($scheduledDayOrders as $schedule) {
            $orderReceivingDate = now()->addDay()->toDateString();
            // Find the next date after $orderReceivingDate that matches $schedule->delivery_day
            $orderDeliveryDate = \Carbon\Carbon::parse($orderReceivingDate)
                ->next($schedule->delivery_day)
                ->toDateString();

            $data = [];
            $data['client_id'] = $schedule->client_id;
            $data['receiving_address_id'] = $schedule->receiver_address_id;
            $data['receiving_date'] = $orderReceivingDate;
            $data['receiving_time'] = $schedule->receiver_time;
            $data['receiving_to_time'] = $schedule->receiver_to_time;

            $data['delivery_address_id'] = $schedule->delivery_address_id;
            $data['delivery_date'] = $orderDeliveryDate;
            $data['delivery_time'] = $schedule->delivery_time;
            $data['delivery_to_time'] = $schedule->delivery_to_time;

            $data['order_price'] = 0;
            $data['type'] = 'fastorder';
            $data['pay_type'] = 'contract';
            $data['desc'] = $schedule->note;
            $orderService->createOrder($data, [], $schedule->client);
        }
    }
}
