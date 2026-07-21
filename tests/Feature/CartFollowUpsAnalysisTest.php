<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Core\Users\Models\User;
use Core\Orders\Models\CartFollowUp;
use Core\Info\Models\City;
use Illuminate\Support\Facades\DB;

class CartFollowUpsAnalysisTest extends TestCase
{
    use DatabaseTransactions;

    protected function getAdminUser()
    {
        return User::create([
            'fullname' => 'Admin Test',
            'email' => 'admin-' . uniqid() . '@example.com',
            'phone' => '123' . rand(111111, 999999),
            'password' => bcrypt('secret123'),
            'is_active' => true,
        ]);
    }

    public function test_admin_can_access_cart_follow_ups_analysis()
    {
        $admin = $this->getAdminUser();

        $response = $this->actingAs($admin, 'web')
            ->withoutMiddleware()
            ->get(route('dashboard.cart-follow-ups.analysis'));

        $response->assertStatus(200);
        $response->assertSee(trans('Follow Ups Analysis'));
    }

    public function test_analysis_data_calculation()
    {
        // 1. Create client user
        $client = User::create([
            'fullname' => 'Client Test',
            'email' => 'client-' . uniqid() . '@example.com',
            'phone' => '123' . rand(111111, 999999),
            'password' => bcrypt('secret123'),
            'is_active' => true,
        ]);

        // 2. Create city and translation
        $city = City::create([
            'slug' => 'test-city-' . uniqid(),
            'status' => 'active',
        ]);
        
        DB::table('city_translations')->insert([
            'city_id' => $city->id,
            'locale' => config('app.locale', 'ar'),
            'name' => 'Test City Name',
        ]);

        // Create profile for client
        DB::table('profiles')->insert([
            'user_id' => $client->id,
            'city_id' => $city->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 3. Create admin user
        $admin = $this->getAdminUser();

        // 4. Create cart
        $cartId = DB::table('carts')->insertGetId([
            'user_id' => $client->id,
            'created_at' => now()->subDays(2),
            'updated_at' => now()->subDays(2),
        ]);

        // Clear existing data or count initially to make it resilient
        $initialTotal = \DB::table('cart_follow_ups')->count();
        $initialSales = \DB::table('cart_follow_ups')->where('status', 'sale')->count();

        // 5. Create follow up records
        CartFollowUp::create([
            'cart_id' => $cartId,
            'admin_id' => $admin->id,
            'phone' => $client->phone,
            'status' => 'sale',
            'followed_up_at' => now()->subHours(1)->format('Y-m-d H:i:s'),
            'order_at' => now()->format('Y-m-d H:i:s'),
        ]);

        CartFollowUp::create([
            'cart_id' => $cartId,
            'admin_id' => $admin->id,
            'phone' => $client->phone,
            'status' => 'no_answer',
            'followed_up_at' => now()->subHours(3)->format('Y-m-d H:i:s'),
        ]);

        // Call the service method
        $service = new \Core\Orders\Services\CartFollowUpsService();
        $data = $service->getAnalysisData();

        $this->assertEquals($initialTotal + 2, $data['total']);
        $this->assertEquals($initialSales + 1, $data['sales_count']);
        
        $this->assertArrayHasKey('sale', $data['statuses']);
        $this->assertArrayHasKey('no_answer', $data['statuses']);

        // Test web access with content
        $response = $this->actingAs($admin, 'web')
            ->withoutMiddleware()
            ->get(route('dashboard.cart-follow-ups.analysis'));

        $response->assertStatus(200);
        $response->assertSee('Test City Name');
        $response->assertSee('Admin Test');
    }

    public function test_analysis_data_with_date_filters()
    {
        $admin = $this->getAdminUser();
        $client = User::create([
            'fullname' => 'Filter Client',
            'email' => 'client-' . uniqid() . '@example.com',
            'phone' => '123' . rand(111111, 999999),
            'password' => bcrypt('secret123'),
            'is_active' => true,
        ]);

        $cartId = DB::table('carts')->insertGetId([
            'user_id' => $client->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Follow up 1: 5 days ago
        CartFollowUp::create([
            'cart_id' => $cartId,
            'admin_id' => $admin->id,
            'phone' => $client->phone,
            'status' => 'sale',
            'followed_up_at' => now()->subDays(5)->format('Y-m-d H:i:s'),
        ]);

        // Follow up 2: Today
        CartFollowUp::create([
            'cart_id' => $cartId,
            'admin_id' => $admin->id,
            'phone' => $client->phone,
            'status' => 'sale',
            'followed_up_at' => now()->format('Y-m-d H:i:s'),
        ]);

        $service = new \Core\Orders\Services\CartFollowUpsService();

        // Count existing records within our filter range to handle database pollution/seeding
        $initialFiltered = CartFollowUp::where('followed_up_at', '>=', now()->subDays(3)->startOfDay())
            ->where('followed_up_at', '<=', now()->endOfDay())
            ->count();

        // 1. Without filters, both should be counted (at least 2)
        $dataNoFilter = $service->getAnalysisData();
        $this->assertTrue($dataNoFilter['total'] >= 2);

        // 2. Filter from 3 days ago to today (should match the count of all records in this range, which includes the one for today)
        $dataFiltered = $service->getAnalysisData(now()->subDays(3)->format('Y-m-d'), now()->format('Y-m-d'));
        $this->assertEquals($initialFiltered, $dataFiltered['total']);

        // 3. Test HTTP request integration
        $response = $this->actingAs($admin, 'web')
            ->withoutMiddleware()
            ->get(route('dashboard.cart-follow-ups.analysis', [
                'from_date' => now()->subDays(3)->format('Y-m-d'),
                'to_date' => now()->format('Y-m-d'),
            ]));

        $response->assertStatus(200);
    }
}
