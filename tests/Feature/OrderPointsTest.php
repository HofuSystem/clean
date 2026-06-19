<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Core\Products\Models\Product;
use Core\Categories\Models\Category;
use Core\Users\Models\User;
use Core\Orders\Models\Order;
use Core\Orders\Models\OrderItem;
use Core\Orders\Models\OrderRepresentative;
use Core\Users\Models\Point;

class OrderPointsTest extends TestCase
{
    use DatabaseTransactions;

    public function test_driver_finishes_order_and_points_are_awarded_based_on_product_points()
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

        // Authenticate driver on default / web / api guards to satisfy observers
        auth()->setUser($driver);
        auth('web')->setUser($driver);
        auth('api')->setUser($driver);

        // 2. Create category
        $category = Category::create([
            'slug' => 'laundry-cat-' . uniqid(),
            'status' => 'active',
            'delivery_price' => 5,
            'image' => 'cat.png',
            'type' => 'sales',
            'en' => ['name' => 'Laundry EN'],
            'ar' => ['name' => 'Laundry AR'],
        ]);

        // 3. Create products with distinct points
        $product1 = Product::create([
            'category_id' => $category->id,
            'slug' => 'prod-1-' . uniqid(),
            'status' => 'active',
            'price' => 50,
            'points' => 20, // 20 points per item
            'cost' => 30,
            'quantity' => 100,
            'wash_type' => 'washer',
            'image' => 'p1.png',
            'type' => 'sales',
            'en' => ['name' => 'Prod 1 EN', 'desc' => 'D1'],
            'ar' => ['name' => 'Prod 1 AR', 'desc' => 'D1'],
        ]);

        $product2 = Product::create([
            'category_id' => $category->id,
            'slug' => 'prod-2-' . uniqid(),
            'status' => 'active',
            'price' => 100,
            'points' => 45.5, // 45.5 points per item
            'cost' => 60,
            'quantity' => 100,
            'wash_type' => 'washer',
            'image' => 'p2.png',
            'type' => 'sales',
            'en' => ['name' => 'Prod 2 EN', 'desc' => 'D2'],
            'ar' => ['name' => 'Prod 2 AR', 'desc' => 'D2'],
        ]);

        // 4. Create an order with items
        $order = Order::create([
            'reference_id' => 'ORD-' . rand(1000, 9999),
            'client_id' => $client->id,
            'receiving_driver_id' => $driver->id,
            'delivery_driver_id' => $driver->id,
            'status' => 'delivered', // Must be delivered for DriverOrderController
            'type' => 'sales',
            'order_price' => 200,
            'total_price' => 205,
            'receiving_date' => now()->format('Y-m-d'),
            'receiving_time' => '10:00',
            'receiving_to_time' => '12:00',
            'pay_type' => 'cash',
        ]);

        // Create the driver relationship records in the order_representatives table
        OrderRepresentative::create([
            'order_id' => $order->id,
            'representative_id' => $driver->id,
            'type' => 'delivery',
            'date' => now()->format('Y-m-d'),
            'time' => '10:00',
        ]);

        // Create OrderItems
        // Item 1: quantity 2 of Product 1 (2 * 20 = 40 points)
        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product1->id,
            'product_price' => $product1->price,
            'product_cost' => $product1->cost,
            'quantity' => 2,
            'product_data' => $product1->toJson(),
            'wash_type' => $product1->wash_type,
        ]);

        // Item 2: quantity 1 of Product 2 (1 * 45.5 = 45.5 points)
        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product2->id,
            'product_price' => $product2->price,
            'product_cost' => $product2->cost,
            'quantity' => 1,
            'product_data' => $product2->toJson(),
            'wash_type' => $product2->wash_type,
        ]);

        // Expected total points: round(2 * 20 + 1 * 45.5) = round(85.5) = 86
        $expectedPoints = round(2 * 20 + 1 * 45.5);

        // 5. Authenticate driver for the HTTP request
        $this->actingAs($driver, 'sanctum');
        auth('api')->setUser($driver);

        // 6. Invoke finished endpoint
        $response = $this->postJson("/api/driver/orders/finished/{$order->id}");
        $response->assertStatus(200);

        // 7. Verify point record
        $pointRecord = Point::where('user_id', $client->id)
            ->where('title', 'like', "%{$order->reference_id}%")
            ->first();

        $this->assertNotNull($pointRecord);
        $this->assertEquals($expectedPoints, $pointRecord->amount);
    }
}
