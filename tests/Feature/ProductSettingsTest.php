<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Core\Products\Models\Product;
use Core\Products\Models\ProductSetting;
use Core\Categories\Models\Category;
use Core\Users\Models\User;

class ProductSettingsTest extends TestCase
{
    use DatabaseTransactions;

    public function test_flowers_and_gifts_product_details_returns_product_settings()
    {
        // 0. Create and authenticate a user
        $user = User::create([
            'fullname' => 'Test User',
            'email' => 'test-' . uniqid() . '@example.com',
            'phone' => '123' . rand(111111, 999999),
            'password' => 'secret123',
            'is_active' => true,
        ]);

        $this->actingAs($user, 'sanctum');
        auth('api')->setUser($user);

        // 1. Create a category
        $category = Category::create([
            'slug' => 'test-category-' . uniqid(),
            'status' => 'active',
            'delivery_price' => 10,
            'image' => 'category_test.png',
            'type' => 'services',
            'en' => [
                'name' => 'Test Category EN',
            ],
            'ar' => [
                'name' => 'Test Category AR',
            ],
        ]);

        // 2. Create a product linked to this category
        $product = Product::create([
            'category_id' => $category->id,
            'slug' => 'test-product-' . uniqid(),
            'status' => 'active',
            'price' => 100,
            'cost' => 50,
            'quantity' => 10,
            'wash_type' => 'washer',
            'image' => 'product_test.png',
            'type' => 'normal',
            'en' => [
                'name' => 'Test Product EN',
                'desc' => 'Test Description EN',
            ],
            'ar' => [
                'name' => 'Test Product AR',
                'desc' => 'Test Description AR',
            ],
        ]);

        // 3. Create a parent product setting (without product_id column)
        $parentSetting = ProductSetting::create([
            'slug' => 'test-setting-' . uniqid(),
            'status' => 'active',
            'en' => [
                'name' => 'Test Setting Parent EN',
            ],
            'ar' => [
                'name' => 'Test Setting Parent AR',
            ],
        ]);

        // 4. Create a child/sub setting
        $childSetting = ProductSetting::create([
            'slug' => 'test-subsetting-' . uniqid(),
            'parent_id' => $parentSetting->id,
            'addon_price' => 20,
            'status' => 'active',
            'en' => [
                'name' => 'Test Setting Child Option EN',
            ],
            'ar' => [
                'name' => 'Test Setting Child Option AR',
            ],
        ]);

        // 4.5 Associate to product using the many-to-many relationship
        $product->productSettings()->attach([$parentSetting->id, $childSetting->id]);

        // 5. Query the API endpoint
        $response = $this->getJson("/api/flowers-and-gifts/products/{$product->id}");

        // 6. Assertions
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'product',
                'category_settings',
                'addons',
            ],
            'status'
        ]);

        $responseData = $response->json('data.category_settings');
        $this->assertCount(1, $responseData);
        $this->assertEquals('Test Setting Parent EN', $responseData[0]['name']);
        
        $subSettings = $responseData[0]['sub_settings'];
        $this->assertCount(1, $subSettings);
        $this->assertEquals('Test Setting Child Option EN', $subSettings[0]['name']);
        $this->assertEquals(20, $subSettings[0]['price']);
    }

    public function test_products_service_get_accepts_integer_and_string_id()
    {
        // 1. Create a category
        $category = Category::create([
            'slug' => 'test-category-' . uniqid(),
            'status' => 'active',
            'delivery_price' => 10,
            'image' => 'category_test.png',
            'type' => 'services',
            'en' => [
                'name' => 'Test Category EN',
            ],
            'ar' => [
                'name' => 'Test Category AR',
            ],
        ]);

        // 2. Create a product linked to this category
        $product = Product::create([
            'category_id' => $category->id,
            'slug' => 'test-product-' . uniqid(),
            'status' => 'active',
            'price' => 100,
            'cost' => 50,
            'quantity' => 10,
            'wash_type' => 'washer',
            'image' => 'product_test.png',
            'type' => 'normal',
            'en' => [
                'name' => 'Test Product EN',
                'desc' => 'Test Description EN',
            ],
            'ar' => [
                'name' => 'Test Product AR',
                'desc' => 'Test Description AR',
            ],
        ]);

        $service = app(\Core\Products\Services\ProductsService::class);

        // Test with integer
        $retrievedByInt = $service->get($product->id);
        $this->assertEquals($product->id, $retrievedByInt->id);

        // Test with string
        $retrievedByStr = $service->get((string)$product->id);
        $this->assertEquals($product->id, $retrievedByStr->id);
    }

    public function test_dashboard_product_settings_creation_and_subsetting_validation()
    {
        // 1. Create and authenticate a user
        $user = User::create([
            'fullname' => 'Admin User',
            'email' => 'admin-' . uniqid() . '@example.com',
            'phone' => '123' . rand(111111, 999999),
            'password' => 'secret123',
            'is_active' => true,
        ]);

        // Create the permission and assign it to the user using Spatie Permission model
        $permission = \Spatie\Permission\Models\Permission::firstOrCreate([
            'name' => 'dashboard.product-settings.create',
            'guard_name' => 'web',
        ]);
        
        $user->givePermissionTo($permission);
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        $this->actingAs($user, 'web');

        // 2. Test storing a top-level setting
        $settingData = [
            'status' => 'active',
            'addon_price' => 15,
            'cost' => 10,
            'translations' => [
                'en' => [
                    'name' => 'Parent Setting EN',
                    'description' => 'Parent Desc EN'
                ],
                'ar' => [
                    'name' => 'Parent Setting AR',
                    'description' => 'Parent Desc AR'
                ]
            ]
        ];

        $response = $this->postJson(route('dashboard.product-settings.create'), $settingData);
        $response->assertStatus(200);
        $parentId = $response->json('entity.id');

        $this->assertDatabaseHas('product_settings', [
            'id' => $parentId,
            'addon_price' => 15
        ]);

        // 3. Test storing a sub-setting/option (with parent_id).
        $subsettingData = [
            'parent_id' => $parentId,
            'status' => 'active',
            'addon_price' => 5,
            'cost' => 3,
            'translations' => [
                'en' => [
                    'name' => 'Child Setting EN',
                ],
                'ar' => [
                    'name' => 'Child Setting AR',
                ]
            ]
        ];

        $responseSub = $this->postJson(route('dashboard.product-settings.create'), $subsettingData);
        $responseSub->assertStatus(200);
        $childId = $responseSub->json('entity.id');

        // Assert that the child setting has parent_id
        $this->assertDatabaseHas('product_settings', [
            'id' => $childId,
            'parent_id' => $parentId,
            'addon_price' => 5
        ]);
    }

    public function test_dashboard_product_settings_association_and_deletion()
    {
        // 1. Create and authenticate a user
        $user = User::create([
            'fullname' => 'Admin User',
            'email' => 'admin-' . uniqid() . '@example.com',
            'phone' => '123' . rand(111111, 999999),
            'password' => 'secret123',
            'is_active' => true,
        ]);

        // Create and assign the required route permissions to the user using Spatie Permission model
        foreach (['dashboard.products.edit', 'dashboard.products.associate-settings', 'dashboard.products.delete-setting'] as $permName) {
            $perm = \Spatie\Permission\Models\Permission::firstOrCreate([
                'name' => $permName,
                'guard_name' => 'web',
            ]);
            $user->givePermissionTo($perm);
        }
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        $this->actingAs($user, 'web');

        // 2. Create a category
        $category = Category::create([
            'slug' => 'test-cat-' . uniqid(),
            'status' => 'active',
            'delivery_price' => 10,
            'image' => 'category_test.png',
            'type' => 'services',
            'en' => [
                'name' => 'Test Category EN',
            ],
            'ar' => [
                'name' => 'Test Category AR',
            ],
        ]);

        // 3. Create a product
        $product = Product::create([
            'category_id' => $category->id,
            'slug' => 'test-prod-' . uniqid(),
            'status' => 'active',
            'price' => 100,
            'cost' => 50,
            'quantity' => 10,
            'wash_type' => 'washer',
            'image' => 'product_test.png',
            'type' => 'normal',
            'en' => [
                'name' => 'Test Product EN',
                'desc' => 'Test Description EN',
            ],
            'ar' => [
                'name' => 'Test Product AR',
                'desc' => 'Test Description AR',
            ],
        ]);

        // 4. Create a global setting
        $globalSetting = ProductSetting::create([
            'slug' => 'global-setting',
            'status' => 'active',
            'en' => [
                'name' => 'Global Setting Name',
            ]
        ]);

        // 5. Create a global option (child of global setting)
        $globalOption = ProductSetting::create([
            'parent_id' => $globalSetting->id,
            'slug' => 'global-option',
            'status' => 'active',
            'addon_price' => 12,
            'en' => [
                'name' => 'Global Option Name',
            ]
        ]);

        // 6. Test associate-settings route
        $response = $this->postJson(route('dashboard.products.associate-settings', $product->id), [
            'setting_id' => $globalSetting->id,
            'option_ids' => [$globalOption->id],
        ]);

        $response->assertStatus(200);

        // Verify product setting associations are created in the pivot table
        $this->assertDatabaseHas('product_product_setting', [
            'product_id' => $product->id,
            'product_setting_id' => $globalSetting->id,
        ]);

        $this->assertDatabaseHas('product_product_setting', [
            'product_id' => $product->id,
            'product_setting_id' => $globalOption->id,
        ]);

        // 7. Test delete-setting route
        $deleteResponse = $this->deleteJson(route('dashboard.products.delete-setting', [
            'id' => $product->id,
            'setting_id' => $globalSetting->id,
        ]));

        $deleteResponse->assertStatus(200);

        // Assert pivot table records are removed
        $this->assertDatabaseMissing('product_product_setting', [
            'product_id' => $product->id,
            'product_setting_id' => $globalSetting->id,
        ]);
        $this->assertDatabaseMissing('product_product_setting', [
            'product_id' => $product->id,
            'product_setting_id' => $globalOption->id,
        ]);
    }
}
