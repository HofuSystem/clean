<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Core\Users\Models\User;
use Core\Financials\Models\PurchaseItem;
use Core\Financials\Models\PurchaseProvider;
use Core\Financials\Models\Purchase;

class PurchasingImportExportTest extends TestCase
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

    // Purchase Providers Tests
    public function test_admin_can_access_purchase_providers_import_view()
    {
        $admin = $this->getAdminUser();

        $response = $this->actingAs($admin, 'web')
            ->withoutMiddleware()
            ->get(route('dashboard.purchase-providers.import'));

        $response->assertStatus(200);
        $response->assertSee(trans('Purchase provider import'));
    }

    public function test_admin_can_import_purchase_providers()
    {
        $admin = $this->getAdminUser();

        $response = $this->actingAs($admin, 'web')
            ->withoutMiddleware()
            ->postJson(route('dashboard.purchase-providers.import'), [
                'data' => [
                    [
                        'name' => 'Provider Import Test',
                        'commercial_registration' => '1234567890',
                        'tax_number' => '987654321',
                        'street_name' => 'Main Street',
                        'building_no' => '12',
                        'postal_code' => '12345'
                    ]
                ]
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => true,
        ]);

        $this->assertDatabaseHas('purchase_providers', [
            'name' => 'Provider Import Test',
            'commercial_registration' => '1234567890',
            'tax_number' => '987654321',
            'street_name' => 'Main Street',
            'building_no' => '12',
            'postal_code' => '12345'
        ]);
    }

    public function test_admin_can_export_purchase_providers()
    {
        $admin = $this->getAdminUser();

        $response = $this->actingAs($admin, 'web')
            ->withoutMiddleware()
            ->get(route('dashboard.purchase-providers.export', ['headersOnly' => 1]));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    // Purchase Items Tests
    public function test_admin_can_access_purchase_items_import_view()
    {
        $admin = $this->getAdminUser();

        $response = $this->actingAs($admin, 'web')
            ->withoutMiddleware()
            ->get(route('dashboard.purchase-items.import'));

        $response->assertStatus(200);
        $response->assertSee(trans('Purchase item import'));
    }

    public function test_admin_can_import_purchase_items()
    {
        $admin = $this->getAdminUser();

        $response = $this->actingAs($admin, 'web')
            ->withoutMiddleware()
            ->postJson(route('dashboard.purchase-items.import'), [
                'data' => [
                    [
                        'name' => 'Item Import Test'
                    ]
                ]
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => true,
        ]);

        $this->assertDatabaseHas('purchase_items', [
            'name' => 'Item Import Test'
        ]);
    }

    public function test_admin_can_export_purchase_items()
    {
        $admin = $this->getAdminUser();

        $response = $this->actingAs($admin, 'web')
            ->withoutMiddleware()
            ->get(route('dashboard.purchase-items.export', ['headersOnly' => 1]));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    // Purchases Tests
    public function test_admin_can_access_purchases_import_view()
    {
        $admin = $this->getAdminUser();

        $response = $this->actingAs($admin, 'web')
            ->withoutMiddleware()
            ->get(route('dashboard.purchases.import'));

        $response->assertStatus(200);
        $response->assertSee(trans('Purchase import'));
    }

    public function test_admin_can_import_purchases()
    {
        $admin = $this->getAdminUser();

        // Create dependent models
        $provider = PurchaseProvider::create([
            'name' => 'Dep Provider',
            'commercial_registration' => '111',
            'tax_number' => '222',
        ]);

        $item = PurchaseItem::create([
            'name' => 'Dep Item',
        ]);

        $response = $this->actingAs($admin, 'web')
            ->withoutMiddleware()
            ->postJson(route('dashboard.purchases.import'), [
                'data' => [
                    [
                        'item_id' => $item->id,
                        'provider_id' => $provider->id,
                        'value_before_tax' => 100.00,
                        'tax_value' => 15.00,
                        'value_after_tax' => 115.00,
                        'notes' => 'Import purchase test notes'
                    ]
                ]
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => true,
        ]);

        $this->assertDatabaseHas('purchases', [
            'item_id' => $item->id,
            'provider_id' => $provider->id,
            'value_before_tax' => 100.00,
            'tax_value' => 15.00,
            'value_after_tax' => 115.00,
            'notes' => 'Import purchase test notes'
        ]);
    }

    public function test_admin_can_export_purchases()
    {
        $admin = $this->getAdminUser();

        $response = $this->actingAs($admin, 'web')
            ->withoutMiddleware()
            ->get(route('dashboard.purchases.export', ['headersOnly' => 1]));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }
}
