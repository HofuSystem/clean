<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Core\Users\Models\User;
use Core\Financials\Models\DailyFinancialReport;

class FinancialAnalysisTest extends TestCase
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

    public function test_admin_can_access_monthly_financial_analysis()
    {
        $admin = $this->getAdminUser();

        $response = $this->actingAs($admin, 'web')
            ->withoutMiddleware()
            ->get(route('dashboard.financial-analysis', ['year' => date('Y')]));

        $response->assertStatus(200);
        $response->assertSee(trans('Monthly Financial Summary'));
    }

    public function test_admin_can_access_daily_financial_analysis()
    {
        $admin = $this->getAdminUser();

        $response = $this->actingAs($admin, 'web')
            ->withoutMiddleware()
            ->get(route('dashboard.financial-analysis.daily', ['year' => date('Y'), 'month' => (int)date('m')]));

        $response->assertStatus(200);
        $response->assertSee(trans('Daily Summary'));
    }

    public function test_admin_can_store_daily_financial_inputs()
    {
        $admin = $this->getAdminUser();
        $date = date('Y-m-d');

        $response = $this->actingAs($admin, 'web')
            ->withoutMiddleware()
            ->postJson(route('dashboard.financial-analysis.store-daily'), [
                'date' => $date,
                'ad_cost' => 120.50,
                'operating_expenses' => 340.00,
                'bank_balance' => 15000.75,
                'note' => 'Test daily entry note',
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        $this->assertDatabaseHas('daily_financial_reports', [
            'date' => $date,
            'ad_cost' => 120.50,
            'operating_expenses' => 340.00,
            'bank_balance' => 15000.75,
            'note' => 'Test daily entry note',
        ]);
    }

    public function test_admin_can_export_monthly_financial_analysis()
    {
        $admin = $this->getAdminUser();

        $response = $this->actingAs($admin, 'web')
            ->withoutMiddleware()
            ->get(route('dashboard.financial-analysis.export-monthly', ['year' => date('Y')]));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->assertHeader('content-disposition', 'attachment; filename=monthly-financial-analysis-' . date('Y') . '.xlsx');
    }

    public function test_admin_can_export_daily_financial_analysis()
    {
        $admin = $this->getAdminUser();

        $response = $this->actingAs($admin, 'web')
            ->withoutMiddleware()
            ->get(route('dashboard.financial-analysis.export-daily', ['year' => date('Y'), 'month' => (int)date('m')]));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        
        $monthName = strtolower(date('F', mktime(0, 0, 0, (int)date('m'), 1)));
        $response->assertHeader('content-disposition', 'attachment; filename=daily-financial-analysis-' . date('Y') . '-' . $monthName . '.xlsx');
    }
}
