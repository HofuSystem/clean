<?php

namespace Tests\Feature;

use Core\Orders\Jobs\NotifyNewOrderTelegramJob;
use Core\Orders\Models\Order;
use Core\Orders\Services\OrdersService;
use Core\Settings\Services\SettingsService;
use Core\Users\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Queue;
use Mockery;
use Tests\TestCase;

class NotifyNewOrderTelegramJobTest extends TestCase
{
    use DatabaseTransactions;

    private function makeClient(): User
    {
        return User::create([
            'fullname' => 'Telegram Test',
            'email' => 'tg-' . uniqid() . '@example.com',
            'phone' => '123' . rand(111111, 999999),
            'password' => 'secret123',
            'is_active' => true,
        ]);
    }

    private function makeOrder(User $client): Order
    {
        return Order::create([
            'reference_id' => 'L' . rand(100000000, 999999999),
            'client_id' => $client->id,
            'status' => 'pending',
            'type' => 'clothes',
            'order_price' => 0,
            'delivery_price' => 9,
            'total_coupon' => 0,
            'total_price' => 9,
            'paid' => 0,
            'pay_type' => 'cash',
            'is_admin_accepted' => true,
        ]);
    }

    public function test_create_order_path_queues_telegram_instead_of_sending_inline(): void
    {
        Queue::fake();

        (new SettingsService())->saveSettings(['testing_accounts' => []]);

        $order = $this->makeOrder($this->makeClient());

        app(OrdersService::class)->queueNewOrderTelegramNotification($order);

        Queue::assertPushed(NotifyNewOrderTelegramJob::class, function (NotifyNewOrderTelegramJob $job) use ($order) {
            return $job->orderId === (int) $order->id;
        });
    }

    public function test_testing_accounts_do_not_queue_telegram(): void
    {
        Queue::fake();

        $client = $this->makeClient();
        $order = $this->makeOrder($client);

        (new SettingsService())->saveSettings(['testing_accounts' => [$client->id]]);

        app(OrdersService::class)->queueNewOrderTelegramNotification($order);

        Queue::assertNotPushed(NotifyNewOrderTelegramJob::class);
    }

    public function test_job_skips_quietly_when_order_is_missing(): void
    {
        $telegram = Mockery::mock(\Core\Notification\Services\TelegramNotificationService::class);
        $telegram->shouldNotReceive('sendMessage');
        $telegram->shouldNotReceive('formatNewOrderMessage');

        (new NotifyNewOrderTelegramJob(999999999))->handle($telegram);
        $this->assertTrue(true);
    }
}
