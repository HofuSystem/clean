<?php

namespace Tests\Feature;

use Core\Orders\Models\Order;
use Core\Orders\Models\OrderRepresentative;
use Core\Orders\Models\OrderTransaction;
use Core\Orders\Support\OrderPaymentMath;
use Core\PaymentGateways\Models\PaymentTransaction;
use Core\Users\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Regression tests for order L534413536: a fast order with total 385.75 was
 * charged 385.70 by card, a phantom 0.05 cash transaction was booked on
 * delivery, and the driver app displayed "remaining for customer
 * 0.6999999999999886" because total_price is sent as an integer.
 */
class OrderPaymentRoundingTest extends TestCase
{
    use DatabaseTransactions;

    private function makeUser(string $role): User
    {
        $user = User::create([
            'fullname'  => ucfirst($role) . ' Test',
            'email'     => $role . '-' . uniqid() . '@example.com',
            'phone'     => '123' . rand(111111, 999999),
            'password'  => 'secret123',
            'is_active' => true,
        ]);
        $user->assignRole(Role::firstOrCreate(['name' => $role, 'guard_name' => 'api']));

        return $user;
    }

    private function makeOrder(User $client, float $totalPrice, float $paid, string $status = 'delivered'): Order
    {
        return Order::create([
            'reference_id'      => 'L' . rand(100000000, 999999999),
            'client_id'         => $client->id,
            'status'            => $status,
            'type'              => 'fastorder',
            'order_price'       => $totalPrice,
            'delivery_price'    => 0,
            'total_coupon'      => 0,
            'total_price'       => $totalPrice,
            'paid'              => $paid,
            'receiving_date'    => now()->format('Y-m-d'),
            'receiving_time'    => '10:00',
            'receiving_to_time' => '12:00',
            'pay_type'          => 'card',
            'is_admin_accepted' => true,
        ]);
    }

    private function assignDriver(Order $order, User $driver): void
    {
        // OrderRepresentativeObserver records auth()->user()->email on creation.
        auth()->setUser($driver);
        auth('web')->setUser($driver);
        auth('api')->setUser($driver);
        OrderRepresentative::create([
            'order_id'          => $order->id,
            'representative_id' => $driver->id,
            'type'              => 'delivery',
            'date'              => now()->format('Y-m-d'),
            'time'              => '10:00',
        ]);
        $this->actingAs($driver, 'sanctum');
        auth('api')->setUser($driver);
    }

    private function cashTransactions(Order $order)
    {
        return OrderTransaction::where('order_id', $order->id)->where('type', 'cash')->get();
    }

    // ---------------------------------------------------------------- math

    public function test_math_rounds_floating_point_noise_to_halalas(): void
    {
        $this->assertSame(0.0, OrderPaymentMath::remainingForCustomer(385.75, 385.70));
        $this->assertSame(0.05, OrderPaymentMath::remainingToCollect(385.75, 385.70));
        // The exact value the driver app was displaying: paid - (int) total.
        $this->assertSame(0.7, OrderPaymentMath::remainingForCustomer(385, 385.70));
        $this->assertSame(0.0, OrderPaymentMath::remainingToCollect(385.75, 385.7499999999));
        $this->assertSame(0.0, OrderPaymentMath::remainingToCollect(100, 120));
        $this->assertSame(20.0, OrderPaymentMath::remainingForCustomer(100, 120));
        $this->assertFalse(OrderPaymentMath::isCollectable(0.004));
        $this->assertTrue(OrderPaymentMath::isCollectable(0.01));
    }

    // -------------------------------------------------- driver: finish order

    public function test_driver_finish_books_a_real_remaining_difference_as_cash(): void
    {
        $client = $this->makeUser('client');
        $driver = $this->makeUser('driver');
        $order  = $this->makeOrder($client, 385.75, 385.70);
        $this->assignDriver($order, $driver);

        $this->postJson("/api/driver/orders/finished/{$order->id}")->assertStatus(200);

        $cash = $this->cashTransactions($order);
        $this->assertCount(1, $cash);
        $this->assertEquals(0.05, $cash->first()->amount);
    }

    public function test_driver_finish_ignores_floating_point_residue(): void
    {
        $client = $this->makeUser('client');
        $driver = $this->makeUser('driver');
        // 3 items x 128.5833 with a 15% coupon style residue: paid differs by < 1 halala.
        $order = $this->makeOrder($client, 385.75, 385.7499999999);
        $this->assignDriver($order, $driver);

        $this->postJson("/api/driver/orders/finished/{$order->id}")->assertStatus(200);

        $this->assertCount(0, $this->cashTransactions($order));
    }

    public function test_driver_finish_does_not_book_cash_when_order_is_fully_paid(): void
    {
        $client = $this->makeUser('client');
        $driver = $this->makeUser('driver');
        $order  = $this->makeOrder($client, 385.75, 385.75);
        $this->assignDriver($order, $driver);

        $this->postJson("/api/driver/orders/finished/{$order->id}")->assertStatus(200);

        $this->assertCount(0, $this->cashTransactions($order));
    }

    // ------------------------------------------------ driver: order details

    public function test_driver_order_details_expose_rounded_balances(): void
    {
        $client = $this->makeUser('client');
        $driver = $this->makeUser('driver');
        $order  = $this->makeOrder($client, 385.75, 385.70);
        $this->assignDriver($order, $driver);

        $response = $this->getJson("/api/driver/orders/{$order->id}")->assertStatus(200);

        $response->assertJsonPath('data.remaining_to_collect', 0.05);
        $response->assertJsonPath('data.remaining_for_customer', 0);

        $overpaid = $this->makeOrder($client, 385.75, 400);
        $this->assignDriver($overpaid, $driver);

        $response = $this->getJson("/api/driver/orders/{$overpaid->id}")->assertStatus(200);
        $response->assertJsonPath('data.remaining_to_collect', 0);
        $response->assertJsonPath('data.remaining_for_customer', 14.25);
    }

    // ------------------------------------------- client: pay fast order v2

    public function test_fast_payment_amount_is_derived_from_the_order_not_the_client(): void
    {
        $client = $this->makeUser('client');
        $order  = $this->makeOrder($client, 385.75, 0, 'ready_to_delivered');
        $this->actingAs($client, 'sanctum');
        auth('api')->setUser($client);

        // The app rounds to one decimal and sends 385.70; the server must charge 385.75.
        $response = $this->postJson("/api/client/pay_fastorder/{$order->id}/v2", [
            'pay_type' => 'card',
            'paid'     => 385.70,
        ])->assertStatus(200);

        $this->assertNotEmpty($response->json('data.payment_url'));

        $transaction = PaymentTransaction::where('for', 'fast_payment')->latest('id')->first();
        $this->assertNotNull($transaction);
        $this->assertEquals(385.75, $transaction->amount);
        $this->assertEquals(385.75, json_decode($transaction->request_data, true)['paid']);
    }

    public function test_second_payment_after_items_were_added_charges_only_the_new_amount(): void
    {
        $client = $this->makeUser('client');
        // Fully paid at 300.00 by card, then items were added and the total became 385.75.
        $order = $this->makeOrder($client, 385.75, 300, 'ready_to_delivered');
        OrderTransaction::create([
            'order_id' => $order->id,
            'type'     => 'card',
            'amount'   => 300,
            'notes'    => 'first payment',
        ]);
        (new \Core\Settings\Services\SettingsService())->saveSettings(['multiple_payment_fees' => 2]);
        $this->actingAs($client, 'sanctum');
        auth('api')->setUser($client);

        // The app rounds the new amount to 85.70; the server charges 85.75 + 2.00 repeat-payment fee.
        $this->postJson("/api/client/pay_fastorder/{$order->id}/v2", [
            'pay_type' => 'card',
            'paid'     => 85.70,
        ])->assertStatus(200);

        $this->assertEquals(87.75, PaymentTransaction::where('for', 'fast_payment')->latest('id')->value('amount'));
    }

    public function test_fast_payment_deducts_wallet_the_client_actually_has(): void
    {
        $client = $this->makeUser('client');
        $client->forceFill(['wallet' => 100])->save();
        $order = $this->makeOrder($client, 385.75, 0, 'ready_to_delivered');
        $this->actingAs($client, 'sanctum');
        auth('api')->setUser($client);

        $this->postJson("/api/client/pay_fastorder/{$order->id}/v2", [
            'pay_type'           => 'card',
            'paid'               => 285.7,
            'wallet_used'        => true,
            'wallet_amount_used' => 100,
        ])->assertStatus(200);

        $this->assertEquals(285.75, PaymentTransaction::where('for', 'fast_payment')->latest('id')->value('amount'));
    }

    public function test_fast_payment_ignores_wallet_the_client_cannot_cover(): void
    {
        $client = $this->makeUser('client');
        $client->forceFill(['wallet' => 10])->save();
        $order = $this->makeOrder($client, 385.75, 0, 'ready_to_delivered');
        $this->actingAs($client, 'sanctum');
        auth('api')->setUser($client);

        $this->postJson("/api/client/pay_fastorder/{$order->id}/v2", [
            'pay_type'           => 'card',
            'paid'               => 285.7,
            'wallet_used'        => true,
            'wallet_amount_used' => 100,
        ])->assertStatus(200);

        // payFastOrder() would not apply this wallet amount, so the card covers everything.
        $this->assertEquals(385.75, PaymentTransaction::where('for', 'fast_payment')->latest('id')->value('amount'));
    }

    public function test_fast_payment_is_rejected_when_nothing_remains(): void
    {
        $client = $this->makeUser('client');
        $order  = $this->makeOrder($client, 385.75, 385.75, 'ready_to_delivered');
        $this->actingAs($client, 'sanctum');
        auth('api')->setUser($client);

        $before = PaymentTransaction::count();

        $this->postJson("/api/client/pay_fastorder/{$order->id}/v2", [
            'pay_type' => 'card',
            'paid'     => 0.05,
        ])->assertStatus(422);

        $this->assertSame($before, PaymentTransaction::count());
    }
}
