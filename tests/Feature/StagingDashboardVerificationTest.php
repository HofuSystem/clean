<?php

namespace Tests\Feature;

use Core\Notification\Models\Notification;
use Core\Notification\Models\NotificationToken;
use Core\Notification\Models\UsersNotification;
use Core\Users\Models\Device;
use Core\Users\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class StagingDashboardVerificationTest extends TestCase
{
    use DatabaseTransactions;

    protected User $superAdmin;
    protected User $showOnlyStaff;
    protected User $showAndExportStaff;
    protected User $staffWithoutEdit;
    protected User $clientUser;

    protected function setUp(): void
    {
        parent::setUp();

        config(['cache.default' => 'array']);

        $this->withoutMiddleware([
            \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter::class,
            \Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
        ]);

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'client', 'guard_name' => 'web']);

        Permission::firstOrCreate(['name' => 'dashboard.notifications.index', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'dashboard.notifications.show', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'dashboard.notifications.export', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'dashboard.notifications.create', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'dashboard.notifications.health', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'dashboard.notifications.queueMonitor', 'guard_name' => 'web']);

        // 1. Superadmin (role: admin)
        $this->superAdmin = User::create([
            'fullname' => 'Superadmin Staging',
            'email' => 'superadmin-stg-' . uniqid() . '@example.com',
            'phone' => '9665' . rand(1000000, 9999999),
            'password' => bcrypt('secret123'),
            'is_active' => 1,
        ]);
        $this->superAdmin->assignRole('admin');

        // 2. Staff with show only
        $this->showOnlyStaff = User::create([
            'fullname' => 'Show Only Staff Staging',
            'email' => 'show-only-stg-' . uniqid() . '@example.com',
            'phone' => '9665' . rand(1000000, 9999999),
            'password' => bcrypt('secret123'),
            'is_active' => 1,
        ]);
        $this->showOnlyStaff->givePermissionTo('dashboard.notifications.index', 'dashboard.notifications.show', 'dashboard.notifications.health', 'dashboard.notifications.queueMonitor');

        // 3. Staff with show + export
        $this->showAndExportStaff = User::create([
            'fullname' => 'Show and Export Staff Staging',
            'email' => 'show-exp-stg-' . uniqid() . '@example.com',
            'phone' => '9665' . rand(1000000, 9999999),
            'password' => bcrypt('secret123'),
            'is_active' => 1,
        ]);
        $this->showAndExportStaff->givePermissionTo('dashboard.notifications.index', 'dashboard.notifications.show', 'dashboard.notifications.export');

        // 4. Staff without edit
        $this->staffWithoutEdit = User::create([
            'fullname' => 'Staff Without Edit Staging',
            'email' => 'no-edit-stg-' . uniqid() . '@example.com',
            'phone' => '9665' . rand(1000000, 9999999),
            'password' => bcrypt('secret123'),
            'is_active' => 1,
        ]);
        $this->staffWithoutEdit->givePermissionTo('dashboard.notifications.index', 'dashboard.notifications.show');

        // 5. Non-admin Client
        $this->clientUser = User::create([
            'fullname' => 'Client Non-Admin',
            'email' => 'client-nonadmin-' . uniqid() . '@example.com',
            'phone' => '9665' . rand(1000000, 9999999),
            'password' => bcrypt('secret123'),
            'is_active' => 1,
        ]);
        $this->clientUser->assignRole('client');
    }

    protected function createCampaign(array $attributes = []): Notification
    {
        return Notification::create(array_merge([
            'title' => 'Staging Campaign Test',
            'body' => 'Test Body Staging',
            'for' => 'all',
            'types' => json_encode(['apps']),
            'purpose' => 'marketing',
            'processing_status' => 'completed',
            'payload' => json_encode(['delivery_channel' => 'direct_fcm']),
        ], $attributes));
    }

    /**
     * 1. Notifications list, details, queue monitor, health in Arabic & English.
     */
    public function test_01_screens_render_successfully_in_both_locales()
    {
        $campaign = $this->createCampaign(['title' => 'Bilingual Test Campaign']);

        foreach (['ar', 'en'] as $locale) {
            app()->setLocale($locale);

            // List
            $resList = $this->actingAs($this->superAdmin)->get(route('dashboard.notifications.index'));
            $resList->assertStatus(200);

            // Show
            $resShow = $this->actingAs($this->superAdmin)->get(route('dashboard.notifications.show', $campaign->id));
            $resShow->assertStatus(200);

            // Queue Monitor
            $resMon = $this->actingAs($this->superAdmin)->get(route('dashboard.notifications.queueMonitor'));
            $resMon->assertStatus(200);

            // Health
            $resHealth = $this->actingAs($this->superAdmin)->get(route('dashboard.notifications.health'));
            $resHealth->assertStatus(200);
        }
    }

    /**
     * 2. Device results tab masks raw FCM tokens everywhere.
     */
    public function test_02_device_results_masks_raw_tokens()
    {
        $campaign = $this->createCampaign();
        $rawToken = 'fcm_sensitive_secret_token_abcdef1234567890';
        $dev = Device::create([
            'user_id' => $this->clientUser->id,
            'device_token' => $rawToken,
            'type' => 'android',
        ]);

        NotificationToken::create([
            'notification_id' => $campaign->id,
            'device_id' => $dev->id,
            'user_id' => $this->clientUser->id,
            'token' => $rawToken,
            'topic' => 'direct',
            'status' => 'accepted',
        ]);

        $response = $this->actingAs($this->superAdmin)->postJson(route('dashboard.notifications.getDeviceResults', $campaign->id), [
            'start' => 0,
            'length' => 10,
        ]);

        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertNotEmpty($data);

        $htmlRow = json_encode($data);
        // Raw token must NEVER appear unmasked
        $this->assertStringNotContainsString($rawToken, $htmlRow);
        // Masked token must appear
        $this->assertStringContainsString('***', $htmlRow);
    }

    /**
     * 3. Eligibility tab AJAX loads targeted & eligible breakdown.
     */
    public function test_03_eligibility_tab_ajax_loads_successfully()
    {
        $campaign = $this->createCampaign();

        DB::table('users_notifications')->insert([
            'notifications_type' => Notification::class,
            'notifications_id' => $campaign->id,
            'user_id' => $this->clientUser->id,
            'status' => 'sent',
            'eligibility_status' => 'eligible',
            'eligibility_reason' => null,
            'accepted_devices_count' => 1,
            'failed_devices_count' => 0,
        ]);

        $response = $this->actingAs($this->superAdmin)->postJson(route('dashboard.notifications.getEligibilityUsers', $campaign->id), [
            'start' => 0,
            'length' => 10,
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['data', 'recordsTotal', 'recordsFiltered']);
    }

    /**
     * 4. Audience preview is strictly read-only and requires no persistence.
     */
    public function test_04_audience_preview_is_read_only()
    {
        $notifCountBefore = Notification::count();

        $response = $this->actingAs($this->superAdmin)->postJson(route('dashboard.notifications.previewAudience'), [
            'for' => 'phone',
            'for_data' => $this->clientUser->phone,
            'purpose' => 'marketing',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['data' => ['total_matching_users', 'active_users', 'eligible_users', 'eligible_devices']]);
        $this->assertEquals($notifCountBefore, Notification::count());
    }

    /**
     * 5. Permissions matrix: Show-only, Show+Export, Staff-without-edit, Client.
     */
    public function test_05_permissions_matrix_enforces_access_control()
    {
        $campaign = $this->createCampaign();

        // 1. Client user gets 403 on all admin routes
        $this->actingAs($this->clientUser)->get(route('dashboard.notifications.index'))->assertStatus(403);
        $this->actingAs($this->clientUser)->get(route('dashboard.notifications.show', $campaign->id))->assertStatus(403);

        // 2. Show-only staff can view show, but gets 403 on export and create
        $this->actingAs($this->showOnlyStaff)->get(route('dashboard.notifications.show', $campaign->id))->assertStatus(200);
        $this->actingAs($this->showOnlyStaff)->get(route('dashboard.notifications.exportDetails', [$campaign->id, 'devices']))->assertStatus(403);
        $this->actingAs($this->showOnlyStaff)->get(route('dashboard.notifications.create'))->assertStatus(403);

        // 3. Show + Export staff can view show and stream export, but gets 403 on create
        $this->actingAs($this->showAndExportStaff)->get(route('dashboard.notifications.show', $campaign->id))->assertStatus(200);
        $this->actingAs($this->showAndExportStaff)->get(route('dashboard.notifications.create'))->assertStatus(403);

        // 4. Staff without edit gets 403 on create
        $this->actingAs($this->staffWithoutEdit)->get(route('dashboard.notifications.create'))->assertStatus(403);

        // 5. Superadmin can access create
        $this->actingAs($this->superAdmin)->get(route('dashboard.notifications.create'))->assertStatus(200);
    }

    /**
     * 6. Semantics: 'Accepted by FCM' is NOT labeled 'Delivered', Legacy shows Legacy.
     */
    public function test_06_semantics_are_strictly_accurate()
    {
        // 1. Direct campaign
        $directCampaign = $this->createCampaign([
            'title' => 'Direct Campaign Semantics',
            'accepted_by_fcm_count' => 10,
        ]);
        
        $resDirect = $this->actingAs($this->superAdmin)->get(route('dashboard.notifications.show', $directCampaign->id));
        $resDirect->assertStatus(200);
        // Direct metric must show Accepted by FCM in Arabic
        $resDirect->assertSee('قُبل من FCM');

        // 2. Legacy campaign
        $legacyCampaign = $this->createCampaign([
            'title' => 'Legacy Campaign Semantics',
            'payload' => json_encode(['delivery_channel' => 'legacy_topic']),
            'sent_count' => 500,
        ]);
        $resLegacy = $this->actingAs($this->superAdmin)->get(route('dashboard.notifications.show', $legacyCampaign->id));
        $resLegacy->assertStatus(200);
        $resLegacy->assertSee('Legacy Topic');

        // 3. Historical campaign 1120558 Mock
        $histCampaign = $this->createCampaign([
            'title' => 'Campaign 1120558 Replication',
            'payload' => null,
            'accepted_by_fcm_count' => 0,
        ]);
        $this->assertEquals('legacy_topic', $histCampaign->getDeliveryChannel());
    }

    /**
     * 7. CSV Exports stream with masked tokens.
     */
    public function test_07_csv_exports_mask_tokens_in_stream()
    {
        $campaign = $this->createCampaign();
        $rawTok = 'raw_secret_fcm_tok_stream_1234567890';
        $dev = Device::create(['user_id' => $this->clientUser->id, 'device_token' => $rawTok, 'type' => 'ios']);
        NotificationToken::create([
            'notification_id' => $campaign->id,
            'device_id' => $dev->id,
            'token' => $rawTok,
            'topic' => 'direct',
            'status' => 'transient_failed',
            'error_code' => 'UNAVAILABLE',
        ]);

        $response = $this->actingAs($this->showAndExportStaff)->get(route('dashboard.notifications.exportDetails', [$campaign->id, 'devices']));
        $response->assertStatus(200);

        ob_start();
        $response->sendContent();
        $content = ob_get_clean();

        $this->assertStringNotContainsString($rawTok, $content);
        $this->assertStringContainsString('***', $content);
    }
}
