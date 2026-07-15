<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Core\Users\Models\User;
use Core\Financials\Models\PurchaseItem;
use Core\Financials\Models\PurchaseProvider;
use Core\Financials\Models\Purchase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class PurchasesCrudTest extends TestCase
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

    protected function createDependentModels()
    {
        $provider = PurchaseProvider::create([
            'name' => 'Test Provider',
            'commercial_registration' => '1234567890',
            'tax_number' => '987654321',
        ]);

        $item = PurchaseItem::create([
            'name' => 'Test Item',
        ]);

        return [$item, $provider];
    }

    public function test_admin_can_access_purchases_index_view()
    {
        $admin = $this->getAdminUser();

        $response = $this->actingAs($admin, 'web')
            ->withoutMiddleware()
            ->get(route('dashboard.purchases.index'));

        $response->assertStatus(200);
        $response->assertSee(trans('Purchases index'));
    }

    public function test_admin_can_access_purchases_create_view()
    {
        $admin = $this->getAdminUser();

        $response = $this->actingAs($admin, 'web')
            ->withoutMiddleware()
            ->get(route('dashboard.purchases.create'));

        $response->assertStatus(200);
        $response->assertSee(trans('Purchase create'));
    }

    public function test_admin_can_access_purchases_edit_view()
    {
        $admin = $this->getAdminUser();
        [$item, $provider] = $this->createDependentModels();

        $purchase = Purchase::create([
            'item_id' => $item->id,
            'provider_id' => $provider->id,
            'value_before_tax' => 100.00,
            'tax_value' => 15.00,
            'value_after_tax' => 115.00,
            'notes' => 'Some notes',
        ]);

        $response = $this->actingAs($admin, 'web')
            ->withoutMiddleware()
            ->get(route('dashboard.purchases.edit', $purchase->id));

        $response->assertStatus(200);
        $response->assertSee(trans('Purchase edit'));
    }

    public function test_admin_can_access_purchases_show_view()
    {
        $admin = $this->getAdminUser();
        [$item, $provider] = $this->createDependentModels();

        $purchase = Purchase::create([
            'item_id' => $item->id,
            'provider_id' => $provider->id,
            'value_before_tax' => 100.00,
            'tax_value' => 15.00,
            'value_after_tax' => 115.00,
            'notes' => 'Purchase Show Test Notes',
        ]);

        $response = $this->actingAs($admin, 'web')
            ->withoutMiddleware()
            ->get(route('dashboard.purchases.show', $purchase->id));

        $response->assertStatus(200);
        $response->assertSee('Purchase Show Test Notes');
    }

    public function test_admin_can_create_purchase_with_attachment_and_date()
    {
        Storage::fake('public');
        $admin = $this->getAdminUser();
        [$item, $provider] = $this->createDependentModels();

        $file = UploadedFile::fake()->create('document.pdf', 500);

        $response = $this->actingAs($admin, 'web')
            ->withoutMiddleware()
            ->postJson(route('dashboard.purchases.create'), [
                'item_id' => $item->id,
                'provider_id' => $provider->id,
                'value_before_tax' => 200.00,
                'tax_value' => 30.00,
                'value_after_tax' => 230.00,
                'notes' => 'New purchase with attachments',
                'attachment' => $file,
                'collection_date' => '2026-07-15',
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => true,
        ]);

        $purchase = Purchase::latest('id')->first();
        $this->assertNotNull($purchase->attachment);
        $this->assertEquals('2026-07-15', $purchase->collection_date->format('Y-m-d'));
        $this->assertStringStartsWith('Pur-', $purchase->reference_id);

        Storage::disk('public')->assertExists($purchase->attachment);

        $this->assertDatabaseHas('purchases', [
            'id' => $purchase->id,
            'item_id' => $item->id,
            'provider_id' => $provider->id,
            'value_before_tax' => 200.00,
            'tax_value' => 30.00,
            'value_after_tax' => 230.00,
            'notes' => 'New purchase with attachments',
            'collection_date' => '2026-07-15',
            'reference_id' => $purchase->reference_id,
        ]);
    }

    public function test_admin_can_create_purchase_with_custom_reference_id()
    {
        $admin = $this->getAdminUser();
        [$item, $provider] = $this->createDependentModels();

        $customRef = 'Custom-Ref-123';

        $response = $this->actingAs($admin, 'web')
            ->withoutMiddleware()
            ->postJson(route('dashboard.purchases.create'), [
                'item_id' => $item->id,
                'provider_id' => $provider->id,
                'value_before_tax' => 200.00,
                'tax_value' => 30.00,
                'value_after_tax' => 230.00,
                'notes' => 'New purchase with custom ref',
                'reference_id' => $customRef,
                'collection_date' => '2026-07-15',
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => true,
        ]);

        $purchase = Purchase::latest('id')->first();
        $this->assertEquals($customRef, $purchase->reference_id);
    }

    public function test_admin_can_create_purchase_with_media_center_attachment_path()
    {
        $admin = $this->getAdminUser();
        [$item, $provider] = $this->createDependentModels();

        $response = $this->actingAs($admin, 'web')
            ->withoutMiddleware()
            ->postJson(route('dashboard.purchases.create'), [
                'item_id' => $item->id,
                'provider_id' => $provider->id,
                'value_before_tax' => 200.00,
                'tax_value' => 30.00,
                'value_after_tax' => 230.00,
                'notes' => 'New purchase with media center attachment',
                'attachment' => 'files/test-document.pdf',
                'collection_date' => '2026-07-15',
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => true,
        ]);

        $purchase = Purchase::latest('id')->first();
        $this->assertEquals('files/test-document.pdf', $purchase->attachment);
    }

    public function test_admin_can_update_purchase_and_change_attachment()
    {
        Storage::fake('public');
        $admin = $this->getAdminUser();
        [$item, $provider] = $this->createDependentModels();

        $purchase = Purchase::create([
            'item_id' => $item->id,
            'provider_id' => $provider->id,
            'value_before_tax' => 100.00,
            'tax_value' => 15.00,
            'value_after_tax' => 115.00,
            'collection_date' => '2026-07-11',
        ]);

        $newFile = UploadedFile::fake()->create('updated_doc.pdf', 300);

        $response = $this->actingAs($admin, 'web')
            ->withoutMiddleware()
            ->putJson(route('dashboard.purchases.edit', $purchase->id), [
                'item_id' => $item->id,
                'provider_id' => $provider->id,
                'value_before_tax' => 150.00,
                'tax_value' => 22.50,
                'value_after_tax' => 172.50,
                'notes' => 'Updated purchase details',
                'attachment' => $newFile,
                'collection_date' => '2026-07-20',
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => true,
        ]);

        $purchase->refresh();
        $this->assertNotNull($purchase->attachment);
        $this->assertEquals('2026-07-20', $purchase->collection_date->format('Y-m-d'));

        Storage::disk('public')->assertExists($purchase->attachment);

        $this->assertDatabaseHas('purchases', [
            'id' => $purchase->id,
            'value_before_tax' => 150.00,
            'tax_value' => 22.50,
            'value_after_tax' => 172.50,
            'notes' => 'Updated purchase details',
            'collection_date' => '2026-07-20',
        ]);
    }

    public function test_admin_can_delete_purchase_record()
    {
        $admin = $this->getAdminUser();
        [$item, $provider] = $this->createDependentModels();

        $purchase = Purchase::create([
            'item_id' => $item->id,
            'provider_id' => $provider->id,
            'value_before_tax' => 100.00,
            'tax_value' => 15.00,
            'value_after_tax' => 115.00,
        ]);

        $response = $this->actingAs($admin, 'web')
            ->withoutMiddleware()
            ->deleteJson(route('dashboard.purchases.delete', $purchase->id));

        $response->assertStatus(200);
        $response->assertJson([
            'status' => true,
        ]);

        $this->assertSoftDeleted('purchases', [
            'id' => $purchase->id,
        ]);
    }

    public function test_admin_can_restore_purchase_record()
    {
        $admin = $this->getAdminUser();
        [$item, $provider] = $this->createDependentModels();

        $purchase = Purchase::create([
            'item_id' => $item->id,
            'provider_id' => $provider->id,
            'value_before_tax' => 100.00,
            'tax_value' => 15.00,
            'value_after_tax' => 115.00,
        ]);
        $purchase->delete();

        $response = $this->actingAs($admin, 'web')
            ->withoutMiddleware()
            ->putJson(route('dashboard.purchases.restore', $purchase->id));

        $response->assertStatus(200);
        $response->assertJson([
            'status' => true,
        ]);

        $this->assertDatabaseHas('purchases', [
            'id' => $purchase->id,
            'deleted_at' => null,
        ]);
    }

    public function test_admin_can_filter_purchases_by_item_provider_and_date()
    {
        $admin = $this->getAdminUser();
        [$item1, $provider1] = $this->createDependentModels();
        [$item2, $provider2] = $this->createDependentModels();

        $purchase1 = Purchase::create([
            'item_id' => $item1->id,
            'provider_id' => $provider1->id,
            'value_before_tax' => 100.00,
            'tax_value' => 15.00,
            'value_after_tax' => 115.00,
            'collection_date' => '2026-07-15',
        ]);

        $purchase2 = Purchase::create([
            'item_id' => $item2->id,
            'provider_id' => $provider2->id,
            'value_before_tax' => 200.00,
            'tax_value' => 30.00,
            'value_after_tax' => 230.00,
            'collection_date' => '2026-07-20',
        ]);

        // Filter by item1
        $response = $this->actingAs($admin, 'web')
            ->withoutMiddleware()
            ->postJson(route('dashboard.purchases.index'), [
                'draw' => 1,
                'filters' => [
                    'item_id' => $item1->id,
                ],
            ]);
        $response->assertStatus(200);
        $response->assertJsonPath('recordsFiltered', 1);

        // Filter by provider2
        $response = $this->actingAs($admin, 'web')
            ->withoutMiddleware()
            ->postJson(route('dashboard.purchases.index'), [
                'draw' => 1,
                'filters' => [
                    'provider_id' => $provider2->id,
                ],
            ]);
        $response->assertStatus(200);
        $response->assertJsonPath('recordsFiltered', 1);

        // Filter by collection_date_from of purchase2
        $response = $this->actingAs($admin, 'web')
            ->withoutMiddleware()
            ->postJson(route('dashboard.purchases.index'), [
                'draw' => 1,
                'filters' => [
                    'collection_date_from' => '2026-07-18',
                ],
            ]);
        $response->assertStatus(200);
        $response->assertJsonPath('recordsFiltered', 1);

        // Filter by collection_date range that only includes purchase1
        $response = $this->actingAs($admin, 'web')
            ->withoutMiddleware()
            ->postJson(route('dashboard.purchases.index'), [
                'draw' => 1,
                'filters' => [
                    'collection_date_from' => '2026-07-15',
                    'collection_date_to' => '2026-07-16',
                ],
            ]);
        $response->assertStatus(200);
        $response->assertJsonPath('recordsFiltered', 1);
    }

    public function test_admin_can_access_electronic_invoices_declaration_with_purchases()
    {
        $admin = $this->getAdminUser();
        list($item, $provider) = $this->createDependentModels();

        // Create a purchase with known amounts
        $purchase = Purchase::create([
            'item_id' => $item->id,
            'provider_id' => $provider->id,
            'value_before_tax' => 1000.00,
            'tax_value' => 150.00,
            'value_after_tax' => 1150.00,
            'collection_date' => date('Y-m-d'),
        ]);

        $response = $this->actingAs($admin, 'web')
            ->withoutMiddleware()
            ->get(route('dashboard.electronic-invoices.declaration', ['year' => date('Y')]));

        $response->assertStatus(200);
        $viewData = $response->original->getData();
        $summary = $viewData['summary'] ?? [];

        $this->assertGreaterThanOrEqual(1000.00, $summary['purchases_amount'] ?? 0);
        $this->assertGreaterThanOrEqual(150.00, $summary['purchases_vat'] ?? 0);

        $quarters = $viewData['quarters'] ?? [];
        $currentQ = ceil(date('n') / 3);
        $this->assertNotNull($quarters[$currentQ]);
        $this->assertGreaterThanOrEqual(1000.00, $quarters[$currentQ]['purchases_amount'] ?? 0);
        $this->assertGreaterThanOrEqual(150.00, $quarters[$currentQ]['purchases_vat'] ?? 0);
    }
}
