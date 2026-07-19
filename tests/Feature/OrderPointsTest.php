<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Core\Users\Models\User;
use Core\Orders\Models\Order;
use Core\Orders\Models\OrderRepresentative;
use Core\Users\Models\Point;

class OrderPointsTest extends TestCase
{
    use DatabaseTransactions;

    public function test_driver_finishes_order_and_points_are_awarded_based_on_price_formula()
    {
        // 1. Create client and driver users
        $client = User::create([
            'fullname' => 'Client Test',
            'email' => 'client-' . uniqid() . '@example.com',
            'phone' => '123' . rand(111111, 999999),
            'password' => 'secret123',
            'is_active' => true,
        ]);
        $clientRoleApi = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'client', 'guard_name' => 'api']);
        $client->assignRole($clientRoleApi);

        $driver = User::create([
            'fullname' => 'Driver Test',
            'email' => 'driver-' . uniqid() . '@example.com',
            'phone' => '123' . rand(111111, 999999),
            'password' => 'secret123',
            'is_active' => true,
        ]);
        $driverRoleApi = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'driver', 'guard_name' => 'api']);
        $driver->assignRole($driverRoleApi);

        // Configure reward rate (points_per_spent_riyal) to 1.5
        (new \Core\Settings\Services\SettingsService())->saveSettings([
            'points_per_spent_riyal' => 1.5
        ]);

        // Authenticate driver
        auth()->setUser($driver);
        auth('web')->setUser($driver);
        auth('api')->setUser($driver);

        // 2. Create order: order_price = 200, delivery_price = 15, total_coupon (discount) = 25
        // Expected points: (200 - 15 - 25) * 1.5 = 160 * 1.5 = 240
        $order = Order::create([
            'reference_id' => 'ORD-' . rand(1000, 9999),
            'client_id' => $client->id,
            'receiving_driver_id' => $driver->id,
            'delivery_driver_id' => $driver->id,
            'status' => 'delivered', // Must be delivered for DriverOrderController
            'type' => 'sales',
            'order_price' => 200,
            'delivery_price' => 15,
            'total_coupon' => 25,
            'total_price' => 190, // order_price (200) + delivery (15) - coupon (25) = 190
            'receiving_date' => now()->format('Y-m-d'),
            'receiving_time' => '10:00',
            'receiving_to_time' => '12:00',
            'pay_type' => 'cash',
        ]);

        OrderRepresentative::create([
            'order_id' => $order->id,
            'representative_id' => $driver->id,
            'type' => 'delivery',
            'date' => now()->format('Y-m-d'),
            'time' => '10:00',
        ]);

        $this->actingAs($driver, 'sanctum');
        auth('api')->setUser($driver);

        // Invoke finished endpoint
        $response = $this->postJson("/api/driver/orders/finished/{$order->id}");
        $response->assertStatus(200);

        // Verify points awarded
        $pointRecord = Point::where('user_id', $client->id)
            ->where('title', 'like', "%{$order->reference_id}%")
            ->first();

        $this->assertNotNull($pointRecord);
        $this->assertEquals(240, $pointRecord->amount);
        $this->assertEquals('deposit', $pointRecord->operation);

        // Verify client's points_balance was updated to 240
        $client->refresh();
        $this->assertEquals(240, $client->points_balance);
    }

    public function test_technical_finishes_order_and_points_are_awarded_based_on_price_formula()
    {
        // 1. Create client and technical users
        $client = User::create([
            'fullname' => 'Client Test',
            'email' => 'client-' . uniqid() . '@example.com',
            'phone' => '123' . rand(111111, 999999),
            'password' => 'secret123',
            'is_active' => true,
        ]);
        $clientRoleApi = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'client', 'guard_name' => 'api']);
        $client->assignRole($clientRoleApi);

        $technical = User::create([
            'fullname' => 'Technical Test',
            'email' => 'tech-' . uniqid() . '@example.com',
            'phone' => '123' . rand(111111, 999999),
            'password' => 'secret123',
            'is_active' => true,
        ]);
        $technicalRoleApi = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'technical', 'guard_name' => 'api']);
        $technical->assignRole($technicalRoleApi);

        // Configure reward rate to 2.0
        (new \Core\Settings\Services\SettingsService())->saveSettings([
            'points_per_spent_riyal' => 2.0
        ]);

        // Authenticate technical user
        auth()->setUser($technical);
        auth('web')->setUser($technical);
        auth('api')->setUser($technical);

        // 2. Create order: order_price = 150, delivery_price = 10, total_coupon = 40
        // Expected points: (150 - 10 - 40) * 2.0 = 100 * 2.0 = 200
        $order = Order::create([
            'reference_id' => 'ORD-' . rand(1000, 9999),
            'client_id' => $client->id,
            'status' => 'started', // Must be started for TechnicalOrderController
            'type' => 'sales',
            'order_price' => 150,
            'delivery_price' => 10,
            'total_coupon' => 40,
            'total_price' => 120, // 150 + 10 - 40
            'receiving_date' => now()->format('Y-m-d'),
            'receiving_time' => '10:00',
            'receiving_to_time' => '12:00',
            'pay_type' => 'cash',
        ]);

        OrderRepresentative::create([
            'order_id' => $order->id,
            'representative_id' => $technical->id,
            'type' => 'technical',
            'date' => now()->format('Y-m-d'),
            'time' => '10:00',
        ]);

        $this->actingAs($technical, 'sanctum');
        auth('api')->setUser($technical);

        // Invoke finished endpoint
        $response = $this->postJson("/api/technical/orders/finished/{$order->id}");
        $response->assertStatus(200);

        // Verify points awarded
        $pointRecord = Point::where('user_id', $client->id)
            ->where('title', 'like', "%{$order->reference_id}%")
            ->first();

        $this->assertNotNull($pointRecord);
        $this->assertEquals(200, $pointRecord->amount);

        // Verify client's points_balance
        $client->refresh();
        $this->assertEquals(200, $client->points_balance);
    }

    public function test_points_are_not_awarded_when_setting_is_zero_or_null()
    {
        $client = User::create([
            'fullname' => 'Client Test',
            'email' => 'client-' . uniqid() . '@example.com',
            'phone' => '123' . rand(111111, 999999),
            'password' => 'secret123',
            'is_active' => true,
        ]);
        $clientRoleApi = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'client', 'guard_name' => 'api']);
        $client->assignRole($clientRoleApi);

        $driver = User::create([
            'fullname' => 'Driver Test',
            'email' => 'driver-' . uniqid() . '@example.com',
            'phone' => '123' . rand(111111, 999999),
            'password' => 'secret123',
            'is_active' => true,
        ]);
        $driverRoleApi = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'driver', 'guard_name' => 'api']);
        $driver->assignRole($driverRoleApi);

        // Configure reward rate to 0
        (new \Core\Settings\Services\SettingsService())->saveSettings([
            'points_per_spent_riyal' => 0
        ]);

        auth()->setUser($driver);
        auth('web')->setUser($driver);
        auth('api')->setUser($driver);

        $order = Order::create([
            'reference_id' => 'ORD-' . rand(1000, 9999),
            'client_id' => $client->id,
            'receiving_driver_id' => $driver->id,
            'delivery_driver_id' => $driver->id,
            'status' => 'delivered',
            'type' => 'sales',
            'order_price' => 100,
            'delivery_price' => 10,
            'total_coupon' => 0,
            'total_price' => 110,
            'receiving_date' => now()->format('Y-m-d'),
            'receiving_time' => '10:00',
            'receiving_to_time' => '12:00',
            'pay_type' => 'cash',
        ]);

        OrderRepresentative::create([
            'order_id' => $order->id,
            'representative_id' => $driver->id,
            'type' => 'delivery',
            'date' => now()->format('Y-m-d'),
            'time' => '10:00',
        ]);

        $this->actingAs($driver, 'sanctum');
        auth('api')->setUser($driver);

        $response = $this->postJson("/api/driver/orders/finished/{$order->id}");
        $response->assertStatus(200);

        // Verify NO points awarded
        $pointRecord = Point::where('user_id', $client->id)
            ->where('title', 'like', "%{$order->reference_id}%")
            ->first();

        $this->assertNull($pointRecord);

        $client->refresh();
        $this->assertEquals(0, $client->points_balance);
    }

    public function test_points_cannot_be_negative()
    {
        $client = User::create([
            'fullname' => 'Client Test',
            'email' => 'client-' . uniqid() . '@example.com',
            'phone' => '123' . rand(111111, 999999),
            'password' => 'secret123',
            'is_active' => true,
        ]);
        $clientRoleApi = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'client', 'guard_name' => 'api']);
        $client->assignRole($clientRoleApi);

        $driver = User::create([
            'fullname' => 'Driver Test',
            'email' => 'driver-' . uniqid() . '@example.com',
            'phone' => '123' . rand(111111, 999999),
            'password' => 'secret123',
            'is_active' => true,
        ]);
        $driverRoleApi = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'driver', 'guard_name' => 'api']);
        $driver->assignRole($driverRoleApi);

        // Configure reward rate to 1.0
        (new \Core\Settings\Services\SettingsService())->saveSettings([
            'points_per_spent_riyal' => 1.0
        ]);

        auth()->setUser($driver);
        auth('web')->setUser($driver);
        auth('api')->setUser($driver);

        // Order where delivery and coupon exceed order price
        // order_price = 50, delivery = 30, coupon = 40. Subtraction yields 50 - 30 - 40 = -20
        $order = Order::create([
            'reference_id' => 'ORD-' . rand(1000, 9999),
            'client_id' => $client->id,
            'receiving_driver_id' => $driver->id,
            'delivery_driver_id' => $driver->id,
            'status' => 'delivered',
            'type' => 'sales',
            'order_price' => 50,
            'delivery_price' => 30,
            'total_coupon' => 40,
            'total_price' => 40, // 50 + 30 - 40
            'receiving_date' => now()->format('Y-m-d'),
            'receiving_time' => '10:00',
            'receiving_to_time' => '12:00',
            'pay_type' => 'cash',
        ]);

        OrderRepresentative::create([
            'order_id' => $order->id,
            'representative_id' => $driver->id,
            'type' => 'delivery',
            'date' => now()->format('Y-m-d'),
            'time' => '10:00',
        ]);

        $this->actingAs($driver, 'sanctum');
        auth('api')->setUser($driver);

        $response = $this->postJson("/api/driver/orders/finished/{$order->id}");
        $response->assertStatus(200);

        // Verify no points record was created
        $pointRecord = Point::where('user_id', $client->id)
            ->where('title', 'like', "%{$order->reference_id}%")
            ->first();

        $this->assertNull($pointRecord);

        $client->refresh();
        $this->assertEquals(0, $client->points_balance);
    }
}
