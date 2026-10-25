<?php

namespace Tests\Feature;

use Core\Orders\Models\AnalyticsOutbox;
use Core\Orders\Models\Order;
use Core\Orders\Models\OrderTransaction;
use Core\Orders\Services\PointsRedemptionService;
use Core\Orders\Services\OrdersService;
use Core\Settings\Services\SettingsService;
use Core\Users\Models\Point;
use Core\Users\Models\User;
use Core\Wallet\Models\WalletTransaction;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class TrackingV2FinancialRulesTest extends TestCase
{
    use DatabaseTransactions;

    public function test_purchase_is_created_only_after_finished_and_full_settlement(): void
    {
        $client = $this->client();
        $order = $this->order($client, 'delivered', 100, 0);

        OrderTransaction::create(['order_id' => $order->id, 'type' => 'card', 'amount' => 100]);
        $this->assertSame(0, AnalyticsOutbox::where('event_name', 'purchase')->where('order_id', $order->id)->count());

        $order->update(['status' => 'finished']);
        $this->assertSame(2, AnalyticsOutbox::where('event_name', 'purchase')->where('order_id', $order->id)->count());

        $order->update(['status' => 'finished']);
        $this->assertSame(2, AnalyticsOutbox::where('event_name', 'purchase')->where('order_id', $order->id)->count());
    }

    public function test_server_calculates_redemption_value_and_ignores_client_sar_value(): void
    {
        $client = $this->client();
        Point::create(['title' => 'seed', 'amount' => 1000, 'operation' => 'deposit', 'user_id' => $client->id]);
        (new SettingsService())->saveSettings(['minium_points_to_use' => 100, 'riyal_per_point_redeem' => 0.02]);
        $order = $this->order($client, 'pending', 100, 0);

        $result = app(PointsRedemptionService::class)->calculate($client->fresh(), $order, 250);
        $this->assertSame(250, $result['points']);
        $this->assertSame(5.0, $result['value']);
    }

    public function test_redemption_above_due_is_rejected(): void
    {
        $client = $this->client();
        Point::create(['title' => 'seed', 'amount' => 1000, 'operation' => 'deposit', 'user_id' => $client->id]);
        (new SettingsService())->saveSettings(['minium_points_to_use' => 1, 'riyal_per_point_redeem' => 1]);
        $order = $this->order($client, 'pending', 10, 0);

        $this->expectException(ValidationException::class);
        app(PointsRedemptionService::class)->calculate($client->fresh(), $order, 11);
    }

    public function test_overpayment_does_not_create_purchase_until_exact_refund_is_recorded(): void
    {
        $client = $this->client();
        $order = $this->order($client, 'delivered', 100, 0);
        OrderTransaction::create(['order_id' => $order->id, 'type' => 'card', 'amount' => 110]);

        $order->update(['status' => 'finished']);
        $this->assertSame(0, AnalyticsOutbox::where('event_name', 'purchase')->where('order_id', $order->id)->count());

        OrderTransaction::create(['order_id' => $order->id, 'type' => 'wallet', 'amount' => -10]);
        $this->assertSame(2, AnalyticsOutbox::where('event_name', 'purchase')->where('order_id', $order->id)->count());
    }

    public function test_completion_financial_effects_are_idempotent(): void
    {
        $client = $this->client();
        $order = $this->order($client, 'delivered', 100, 0);
        $order->updateQuietly(['cashback' => 10]);

        $service = app(OrdersService::class);
        $service->completeOrderFinancials($order->id);
        $service->completeOrderFinancials($order->id);

        $this->assertSame(1, OrderTransaction::where('order_id', $order->id)->where('type', 'cash')->count());
        $this->assertSame(1, WalletTransaction::where('order_id', $order->id)->where('transaction_type', 'cashback')->count());
        $this->assertEquals(100, $order->fresh()->paid);
    }

    private function client(): User
    {
        return User::create([
            'fullname' => 'Tracking Client', 'email' => uniqid('tracking-', true) . '@example.com',
            'phone' => '9' . random_int(10000000, 99999999), 'password' => 'secret123', 'is_active' => true,
        ]);
    }

    private function order(User $client, string $status, float $total, float $paid): Order
    {
        return Order::create([
            'reference_id' => uniqid('T2-'), 'client_id' => $client->id, 'status' => $status,
            'type' => 'clothes', 'order_price' => $total, 'delivery_price' => 0,
            'total_coupon' => 0, 'total_price' => $total, 'paid' => $paid,
            'receiving_date' => now()->toDateString(), 'receiving_time' => '10:00',
            'receiving_to_time' => '12:00', 'pay_type' => 'card',
        ]);
    }
}
