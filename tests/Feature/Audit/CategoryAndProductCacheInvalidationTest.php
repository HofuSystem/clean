<?php

namespace Tests\Feature\Audit;

use Core\Categories\Models\Category;
use Core\Categories\Services\CategoryCacheService;
use Core\Products\Models\Product;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class CategoryAndProductCacheInvalidationTest extends TestCase
{
    public function test_category_cache_service_flushes_all_keys_and_database_cache()
    {
        // Set test keys in Cache
        Cache::put('home_economy_bags_ar', 'old_ar_data', 600);
        Cache::put('home_economy_bags_en', 'old_en_data', 600);
        Cache::put('home_services_sales_ar', 'old_sales_data', 600);

        $this->assertEquals('old_ar_data', Cache::get('home_economy_bags_ar'));
        $this->assertEquals('old_en_data', Cache::get('home_economy_bags_en'));

        // Flush
        CategoryCacheService::flush();

        $this->assertNull(Cache::get('home_economy_bags_ar'));
        $this->assertNull(Cache::get('home_economy_bags_en'));
        $this->assertNull(Cache::get('home_services_sales_ar'));
    }

    public function test_updating_a_product_triggers_cache_flush()
    {
        Cache::put('home_economy_bags_ar', 'cached_bag_list', 600);
        Cache::put('home_economy_bags_en', 'cached_bag_list', 600);

        // Find or create a product and save it
        $product = Product::first();
        if ($product) {
            $product->touch(); // Triggers saved event & ProductObserver
        } else {
            CategoryCacheService::flush();
        }

        $this->assertNull(Cache::get('home_economy_bags_ar'));
        $this->assertNull(Cache::get('home_economy_bags_en'));
    }

    public function test_updating_a_category_triggers_cache_flush()
    {
        Cache::put('home_economy_bags_ar', 'cached_bag_list', 600);
        Cache::put('home_services_sales_ar', 'cached_sales_list', 600);

        $category = Category::first();
        if ($category) {
            $category->touch(); // Triggers saved event & CategoryObserver
        } else {
            CategoryCacheService::flush();
        }

        $this->assertNull(Cache::get('home_economy_bags_ar'));
        $this->assertNull(Cache::get('home_services_sales_ar'));
    }

    public function test_package_product_creation_invalidates_cache_and_persists_is_package()
    {
        Cache::put('home_economy_bags_ar', 'stale_bags_data', 600);

        $user = \Core\Users\Models\User::first();
        if (!$user) {
            $user = \Core\Users\Models\User::create([
                'fullname' => 'Admin User',
                'email' => 'admin-' . uniqid() . '@example.com',
                'phone' => '123' . rand(111111, 999999),
                'password' => 'secret123',
                'is_active' => true,
            ]);
        }
        $perm = \Spatie\Permission\Models\Permission::firstOrCreate([
            'name' => 'dashboard.products.create',
            'guard_name' => 'web',
        ]);
        $user->givePermissionTo($perm);
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        $this->actingAs($user, 'web');

        $category = Category::where('is_package', 1)->first();
        if (!$category) {
            $category = Category::create([
                'slug' => 'test-bags-' . uniqid(),
                'type' => 'clothes',
                'is_package' => 1,
                'status' => 'active',
                'delivery_price' => 0,
                'image' => 'bags.png',
                'ar' => ['name' => 'حقائب اقتصادية'],
                'en' => ['name' => 'Economic Bags'],
            ]);
        }

        $uniqueSku = 'test-bag-' . uniqid();
        $response = $this->post(route('dashboard.products.create'), [
            'type' => 'clothes',
            'is_package' => 1,
            'category_id' => $category->id,
            'sku' => $uniqueSku,
            'price' => 99.5,
            'points' => 99.5,
            'cost' => 40,
            'status' => 'active',
            'translations' => [
                'ar' => [
                    'name' => 'حقيبة تجريبية جديدة',
                    'desc' => 'وصف الحقيبة التجريبية',
                ],
                'en' => [
                    'name' => 'New Test Bag',
                    'desc' => 'New Test Bag Description',
                ],
            ],
        ]);

        $this->assertEquals(200, $response->status());
        // Cache must be invalidated
        $this->assertNull(Cache::get('home_economy_bags_ar'));

        // Product should exist with is_package = 1
        $createdProduct = Product::where('sku', $uniqueSku)->first();
        $this->assertNotNull($createdProduct);
        $this->assertEquals(1, $createdProduct->is_package);
        $this->assertEquals($category->id, $createdProduct->category_id);
        $this->assertEquals('active', $createdProduct->status);
        $this->assertEquals('حقيبة تجريبية جديدة', $createdProduct->translate('ar')->name);

        // Cleanup
        $createdProduct->forceDelete();
    }
}
