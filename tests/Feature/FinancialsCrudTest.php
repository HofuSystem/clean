<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Core\Users\Models\User;
use Core\B2B\Models\Company;
use Core\Financials\Models\Financial;

class FinancialsCrudTest extends TestCase
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

    protected function createCompany()
    {
        return Company::create([
            'fullname' => 'Test Company',
            'email' => 'company-' . uniqid() . '@example.com',
            'phone' => '123' . rand(111111, 999999),
            'is_active' => true,
        ]);
    }

    public function test_admin_can_access_financials_index_view()
    {
        $admin = $this->getAdminUser();

        $response = $this->actingAs($admin, 'web')
            ->withoutMiddleware()
            ->get(route('dashboard.financials.index'));

        $response->assertStatus(200);
        $response->assertSee(trans('Financials'));
    }

    public function test_admin_can_access_financials_create_view()
    {
        $admin = $this->getAdminUser();

        $response = $this->actingAs($admin, 'web')
            ->withoutMiddleware()
            ->get(route('dashboard.financials.create'));

        $response->assertStatus(200);
        $response->assertSee(trans('Financial Create'));
    }

    public function test_admin_can_access_financials_edit_view()
    {
        $admin = $this->getAdminUser();
        $company = $this->createCompany();
        $financial = Financial::create([
            'company_id' => $company->id,
            'amount' => 1500.50,
            'type' => 'owed',
            'note' => 'Initial owed amount',
        ]);

        $response = $this->actingAs($admin, 'web')
            ->withoutMiddleware()
            ->get(route('dashboard.financials.edit', $financial->id));

        $response->assertStatus(200);
        $response->assertSee(trans('Financial Edit'));
    }

    public function test_admin_can_access_financials_show_view()
    {
        $admin = $this->getAdminUser();
        $company = $this->createCompany();
        $financial = Financial::create([
            'company_id' => $company->id,
            'amount' => 1500.50,
            'type' => 'owed',
            'note' => 'Show note test',
        ]);

        $response = $this->actingAs($admin, 'web')
            ->withoutMiddleware()
            ->get(route('dashboard.financials.show', $financial->id));

        $response->assertStatus(200);
        $response->assertSee('Show note test');
    }

    public function test_admin_can_create_financial_record()
    {
        $admin = $this->getAdminUser();
        $company = $this->createCompany();

        $response = $this->actingAs($admin, 'web')
            ->withoutMiddleware()
            ->postJson(route('dashboard.financials.create'), [
                'company_id' => $company->id,
                'amount' => 500.00,
                'type' => 'paid',
                'note' => 'Paid invoice standard amount',
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => true,
        ]);

        $this->assertDatabaseHas('financials', [
            'company_id' => $company->id,
            'amount' => 500.00,
            'type' => 'paid',
            'note' => 'Paid invoice standard amount',
        ]);
    }

    public function test_admin_can_update_financial_record()
    {
        $admin = $this->getAdminUser();
        $company = $this->createCompany();
        $financial = Financial::create([
            'company_id' => $company->id,
            'amount' => 1000.00,
            'type' => 'owed',
            'note' => 'Initial',
        ]);

        $response = $this->actingAs($admin, 'web')
            ->withoutMiddleware()
            ->putJson(route('dashboard.financials.edit', $financial->id), [
                'company_id' => $company->id,
                'amount' => 1200.00,
                'type' => 'paid',
                'note' => 'Updated note details',
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => true,
        ]);

        $this->assertDatabaseHas('financials', [
            'id' => $financial->id,
            'amount' => 1200.00,
            'type' => 'paid',
            'note' => 'Updated note details',
        ]);
    }

    public function test_admin_can_delete_financial_record()
    {
        $admin = $this->getAdminUser();
        $company = $this->createCompany();
        $financial = Financial::create([
            'company_id' => $company->id,
            'amount' => 1000.00,
            'type' => 'owed',
        ]);

        $response = $this->actingAs($admin, 'web')
            ->withoutMiddleware()
            ->deleteJson(route('dashboard.financials.delete', $financial->id));

        $response->assertStatus(200);
        $response->assertJson([
            'status' => true,
        ]);

        $this->assertSoftDeleted('financials', [
            'id' => $financial->id,
        ]);
    }

    public function test_admin_can_restore_financial_record()
    {
        $admin = $this->getAdminUser();
        $company = $this->createCompany();
        $financial = Financial::create([
            'company_id' => $company->id,
            'amount' => 1000.00,
            'type' => 'owed',
        ]);
        $financial->delete();

        $response = $this->actingAs($admin, 'web')
            ->withoutMiddleware()
            ->putJson(route('dashboard.financials.restore', $financial->id));

        $response->assertStatus(200);
        $response->assertJson([
            'status' => true,
        ]);

        $this->assertDatabaseHas('financials', [
            'id' => $financial->id,
            'deleted_at' => null,
        ]);
    }

    public function test_admin_can_create_financial_record_with_user()
    {
        $admin = $this->getAdminUser();
        $user = User::create([
            'fullname' => 'Test Client User',
            'email' => 'client-' . uniqid() . '@example.com',
            'phone' => '123' . rand(111111, 999999),
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin, 'web')
            ->withoutMiddleware()
            ->postJson(route('dashboard.financials.create'), [
                'user_id' => $user->id,
                'amount' => 600.00,
                'type' => 'owed',
                'note' => 'User owed amount',
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => true,
        ]);

        $this->assertDatabaseHas('financials', [
            'user_id' => $user->id,
            'amount' => 600.00,
            'type' => 'owed',
            'note' => 'User owed amount',
        ]);
    }

    public function test_admin_cannot_create_financial_record_without_owner()
    {
        $admin = $this->getAdminUser();

        $response = $this->actingAs($admin, 'web')
            ->withoutMiddleware()
            ->postJson(route('dashboard.financials.create'), [
                'amount' => 600.00,
                'type' => 'owed',
            ]);

        $response->assertStatus(422);
    }

    public function test_admin_can_update_financial_record_from_company_to_user()
    {
        $admin = $this->getAdminUser();
        $company = $this->createCompany();
        $financial = Financial::create([
            'company_id' => $company->id,
            'amount' => 1000.00,
            'type' => 'owed',
        ]);

        $user = User::create([
            'fullname' => 'Test Client User 2',
            'email' => 'client2-' . uniqid() . '@example.com',
            'phone' => '123' . rand(111111, 999999),
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin, 'web')
            ->withoutMiddleware()
            ->putJson(route('dashboard.financials.edit', $financial->id), [
                'user_id' => $user->id,
                'company_id' => null,
                'amount' => 1200.00,
                'type' => 'paid',
                'note' => 'Switched to user ownership',
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => true,
        ]);

        $this->assertDatabaseHas('financials', [
            'id' => $financial->id,
            'company_id' => null,
            'user_id' => $user->id,
            'amount' => 1200.00,
            'type' => 'paid',
        ]);
    }
}
