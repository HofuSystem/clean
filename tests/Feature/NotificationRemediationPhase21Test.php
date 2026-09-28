<?php

namespace Tests\Feature;

use Core\Notification\Jobs\SendFcmBatchJob;
use Core\Notification\Models\Notification;
use Core\Notification\Models\NotificationToken;
use Core\Notification\Models\UsersNotification;
use Core\Notification\Services\FCMService;
use Core\Users\Models\Device;
use Core\Users\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class NotificationRemediationPhase21Test extends TestCase
{
    use DatabaseTransactions;

    protected User $adminUser;
    protected User $showOnlyStaff;
    protected User $exportStaff;
    protected User $clientUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware([
            \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter::class,
            \Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect::class,
        ]);

        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'client', 'guard_name' => 'web']);

        Permission::firstOrCreate(['name' => 'dashboard.notifications.show', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'dashboard.notifications.export', 'guard_name' => 'web']);

        // 1. Full admin
        $this->adminUser = User::create([
            'fullname' => 'Admin User Phase21',
            'email' => 'admin-p21-' . uniqid() . '@example.com',
            'phone' => '9665' . rand(1000000, 9999999),
            'password' => bcrypt('secret123'),
            'is_active' => 1,
        ]);
        $this->adminUser->assignRole('admin');

        // 2. Staff with ONLY show permission (no admin role)
        $this->showOnlyStaff = User::create([
            'fullname' => 'Show Only Staff',
            'email' => 'show-only-' . uniqid() . '@example.com',
            'phone' => '9665' . rand(1000000, 9999999),
            'password' => bcrypt('secret123'),
            'is_active' => 1,
        ]);
        $this->showOnlyStaff->givePermissionTo('dashboard.notifications.show');

        // 3. Staff with export permission
        $this->exportStaff = User::create([
            'fullname' => 'Export Staff',
            'email' => 'export-staff-' . uniqid() . '@example.com',
            'phone' => '9665' . rand(1000000, 9999999),
            'password' => bcrypt('secret123'),
            'is_active' => 1,
        ]);
        $this->exportStaff->givePermissionTo('dashboard.notifications.export');
        $this->exportStaff->givePermissionTo('dashboard.notifications.show');

        // 4. Client / Non-admin
        $this->clientUser = User::create([
            'fullname' => 'Client User Phase21',
            'email' => 'client-p21-' . uniqid() . '@example.com',
            'phone' => '9665' . rand(1000000, 9999999),
            'password' => bcrypt('secret123'),
            'is_active' => 1,
        ]);
        $this->clientUser->assignRole('client');
    }

    protected function createCampaign(array $attributes = []): Notification
    {
        return Notification::create(array_merge([
            'title' => 'Phase 2.1 Test Campaign',
            'body' => 'Test Body',
            'for' => 'all',
            'types' => json_encode(['apps']),
            'purpose' => 'marketing',
            'processing_status' => 'completed',
            'payload' => json_encode(['delivery_channel' => 'direct_fcm']),
        ], $attributes));
    }

    protected function createToken(array $attributes = []): NotificationToken
    {
        return NotificationToken::create(array_merge([
            'topic' => 'direct',
            'token' => 'fcm_tok_' . uniqid(),
            'status' => 'queued',
            'attempts' => 0,
            'queued_at' => now(),
        ], $attributes));
    }

    /**
     * 1. Queued rows become skipped when flag is off.
     */
    public function test_01_queued_rows_become_skipped_when_flag_is_off()
    {
        config(['notification.direct_fcm_enabled' => false]);

        $notification = $this->createCampaign();
        $device1 = Device::create(['user_id' => $this->clientUser->id, 'device_token' => 'tok_1_' . uniqid(), 'type' => 'android']);
        $device2 = Device::create(['user_id' => $this->clientUser->id, 'device_token' => 'tok_2_' . uniqid(), 'type' => 'ios']);

        $token1 = $this->createToken(['notification_id' => $notification->id, 'device_id' => $device1->id, 'token' => $device1->device_token, 'status' => 'queued']);
        $token2 = $this->createToken(['notification_id' => $notification->id, 'device_id' => $device2->id, 'token' => $device2->device_token, 'status' => 'queued']);

        $batch = [
            ['device_id' => $device1->id, 'user_id' => $this->clientUser->id, 'token' => $device1->device_token],
            ['device_id' => $device2->id, 'user_id' => $this->clientUser->id, 'token' => $device2->device_token],
        ];

        $job = new SendFcmBatchJob($notification, $batch);
        $job->handle();

        $this->assertEquals('skipped_by_feature_flag', $job->executionStatus);

        $token1->refresh();
        $token2->refresh();

        $this->assertEquals('skipped_by_feature_flag', $token1->status);
        $this->assertEquals('DIRECT_FCM_DISABLED', $token1->error_code);
        $this->assertEquals('skipped_by_feature_flag', $token2->status);
        $this->assertEquals('DIRECT_FCM_DISABLED', $token2->error_code);

        $notification->refresh();
        $this->assertEquals(0, (int) $notification->accepted_by_fcm_count);
        $this->assertEquals(0, (int) $notification->transient_failed_count);
        $this->assertEquals(0, (int) $notification->permanent_failed_count);
    }

    /**
     * 2. Accepted rows remain unchanged.
     */
    public function test_02_accepted_rows_remain_unchanged()
    {
        config(['notification.direct_fcm_enabled' => false]);

        $notification = $this->createCampaign();
        $device = Device::create(['user_id' => $this->clientUser->id, 'device_token' => 'tok_acc_' . uniqid(), 'type' => 'android']);

        $acceptedToken = $this->createToken([
            'notification_id' => $notification->id,
            'device_id' => $device->id,
            'token' => $device->device_token,
            'status' => 'accepted',
            'accepted_at' => now(),
        ]);

        $batch = [
            ['device_id' => $device->id, 'user_id' => $this->clientUser->id, 'token' => $device->device_token],
        ];

        $job = new SendFcmBatchJob($notification, $batch);
        $job->handle();

        $acceptedToken->refresh();
        $this->assertEquals('accepted', $acceptedToken->status);
        $this->assertNull($acceptedToken->error_code);
    }

    /**
     * 3. Transient/Permanent failed rows remain unchanged.
     */
    public function test_03_transient_and_permanent_failed_rows_remain_unchanged()
    {
        config(['notification.direct_fcm_enabled' => false]);

        $notification = $this->createCampaign();
        $dev1 = Device::create(['user_id' => $this->clientUser->id, 'device_token' => 'tok_tf_' . uniqid(), 'type' => 'android']);
        $dev2 = Device::create(['user_id' => $this->clientUser->id, 'device_token' => 'tok_pf_' . uniqid(), 'type' => 'ios']);

        $transientToken = $this->createToken([
            'notification_id' => $notification->id,
            'device_id' => $dev1->id,
            'token' => $dev1->device_token,
            'status' => 'transient_failed',
            'error_code' => 'UNAVAILABLE',
        ]);
        $permanentToken = $this->createToken([
            'notification_id' => $notification->id,
            'device_id' => $dev2->id,
            'token' => $dev2->device_token,
            'status' => 'permanent_failed',
            'error_code' => 'UNREGISTERED',
        ]);

        $batch = [
            ['device_id' => $dev1->id, 'user_id' => $this->clientUser->id, 'token' => $dev1->device_token],
            ['device_id' => $dev2->id, 'user_id' => $this->clientUser->id, 'token' => $dev2->device_token],
        ];

        $job = new SendFcmBatchJob($notification, $batch);
        $job->handle();

        $transientToken->refresh();
        $permanentToken->refresh();

        $this->assertEquals('transient_failed', $transientToken->status);
        $this->assertEquals('UNAVAILABLE', $transientToken->error_code);
        $this->assertEquals('permanent_failed', $permanentToken->status);
        $this->assertEquals('UNREGISTERED', $permanentToken->error_code);
    }

    /**
     * 4. Only the current batch is updated.
     */
    public function test_04_only_current_batch_is_updated()
    {
        config(['notification.direct_fcm_enabled' => false]);

        $notification = $this->createCampaign();
        $devBatch1 = Device::create(['user_id' => $this->clientUser->id, 'device_token' => 'tok_b1_' . uniqid(), 'type' => 'android']);
        $devBatch2 = Device::create(['user_id' => $this->clientUser->id, 'device_token' => 'tok_b2_' . uniqid(), 'type' => 'ios']);

        $tokenBatch1 = $this->createToken([
            'notification_id' => $notification->id,
            'device_id' => $devBatch1->id,
            'token' => $devBatch1->device_token,
            'status' => 'queued',
        ]);
        $tokenBatch2 = $this->createToken([
            'notification_id' => $notification->id,
            'device_id' => $devBatch2->id,
            'token' => $devBatch2->device_token,
            'status' => 'queued',
        ]);

        // Job executed ONLY for devBatch1
        $batch = [
            ['device_id' => $devBatch1->id, 'user_id' => $this->clientUser->id, 'token' => $devBatch1->device_token],
        ];

        $job = new SendFcmBatchJob($notification, $batch);
        $job->handle();

        $tokenBatch1->refresh();
        $tokenBatch2->refresh();

        $this->assertEquals('skipped_by_feature_flag', $tokenBatch1->status);
        $this->assertEquals('queued', $tokenBatch2->status); // Remains queued!
    }

    /**
     * 5. Unrelated notification rows remain unchanged.
     */
    public function test_05_unrelated_notification_rows_remain_unchanged()
    {
        config(['notification.direct_fcm_enabled' => false]);

        $campaignA = $this->createCampaign(['title' => 'Campaign A']);
        $campaignB = $this->createCampaign(['title' => 'Campaign B']);

        $dev = Device::create(['user_id' => $this->clientUser->id, 'device_token' => 'tok_shared_' . uniqid(), 'type' => 'android']);

        $tokenA = $this->createToken(['notification_id' => $campaignA->id, 'device_id' => $dev->id, 'token' => $dev->device_token, 'status' => 'queued']);
        $tokenB = $this->createToken(['notification_id' => $campaignB->id, 'device_id' => $dev->id, 'token' => $dev->device_token, 'status' => 'queued']);

        $batch = [
            ['device_id' => $dev->id, 'user_id' => $this->clientUser->id, 'token' => $dev->device_token],
        ];

        // Run job for Campaign A
        $job = new SendFcmBatchJob($campaignA, $batch);
        $job->handle();

        $tokenA->refresh();
        $tokenB->refresh();

        $this->assertEquals('skipped_by_feature_flag', $tokenA->status);
        $this->assertEquals('queued', $tokenB->status); // Unrelated campaign remains queued!
    }

    /**
     * 6. Executing the same skipped job twice is idempotent.
     */
    public function test_06_executing_same_skipped_job_twice_is_idempotent()
    {
        config(['notification.direct_fcm_enabled' => false]);

        $notification = $this->createCampaign();
        $dev = Device::create(['user_id' => $this->clientUser->id, 'device_token' => 'tok_idem_' . uniqid(), 'type' => 'android']);

        $token = $this->createToken([
            'notification_id' => $notification->id,
            'device_id' => $dev->id,
            'token' => $dev->device_token,
            'status' => 'queued',
        ]);

        $batch = [
            ['device_id' => $dev->id, 'user_id' => $this->clientUser->id, 'token' => $dev->device_token],
        ];

        $job = new SendFcmBatchJob($notification, $batch);
        $job->handle();

        $token->refresh();
        $this->assertEquals('skipped_by_feature_flag', $token->status);
        $updatedAtFirst = $token->updated_at;

        // Run second time
        $job2 = new SendFcmBatchJob($notification, $batch);
        $job2->handle();

        $token->refresh();
        $this->assertEquals('skipped_by_feature_flag', $token->status);

        $notification->refresh();
        $this->assertEquals(0, (int) $notification->accepted_by_fcm_count);
    }

    /**
     * 7. No HTTP/FCM request occurs while disabled.
     */
    public function test_07_no_http_or_fcm_request_occurs_while_disabled()
    {
        config(['notification.direct_fcm_enabled' => false]);
        Http::fake();

        $notification = $this->createCampaign();
        $dev = Device::create(['user_id' => $this->clientUser->id, 'device_token' => 'tok_no_http_' . uniqid(), 'type' => 'android']);
        $this->createToken(['notification_id' => $notification->id, 'device_id' => $dev->id, 'token' => $dev->device_token, 'status' => 'queued']);

        $batch = [
            ['device_id' => $dev->id, 'user_id' => $this->clientUser->id, 'token' => $dev->device_token],
        ];

        $job = new SendFcmBatchJob($notification, $batch);
        $job->handle();

        Http::assertNothingSent();
    }

    /**
     * 8. Mixed accepted + skipped campaign.
     */
    public function test_08_mixed_accepted_and_skipped_campaign()
    {
        $notification = $this->createCampaign(['accepted_by_fcm_count' => 2]);
        $devAcc = Device::create(['user_id' => $this->clientUser->id, 'device_token' => 'tok_acc_mix_' . uniqid(), 'type' => 'android']);
        $devSkip = Device::create(['user_id' => $this->clientUser->id, 'device_token' => 'tok_skip_mix_' . uniqid(), 'type' => 'ios']);

        // First batch was accepted
        $tokenAcc = $this->createToken([
            'notification_id' => $notification->id,
            'device_id' => $devAcc->id,
            'token' => $devAcc->device_token,
            'status' => 'accepted',
            'accepted_at' => now(),
        ]);

        // Second batch was queued, then flag was disabled
        $tokenSkip = $this->createToken([
            'notification_id' => $notification->id,
            'device_id' => $devSkip->id,
            'token' => $devSkip->device_token,
            'status' => 'queued',
        ]);

        config(['notification.direct_fcm_enabled' => false]);

        $batch2 = [
            ['device_id' => $devSkip->id, 'user_id' => $this->clientUser->id, 'token' => $devSkip->device_token],
        ];

        $job = new SendFcmBatchJob($notification, $batch2);
        $job->handle();

        $tokenAcc->refresh();
        $tokenSkip->refresh();

        $this->assertEquals('accepted', $tokenAcc->status);
        $this->assertEquals('skipped_by_feature_flag', $tokenSkip->status);

        $notification->refresh();
        $this->assertEquals(2, (int) $notification->accepted_by_fcm_count);
    }

    /**
     * 9. Queue Monitor shows skipped count correctly.
     */
    public function test_09_queue_monitor_shows_skipped_count_correctly()
    {
        $notification = $this->createCampaign(['title' => 'Monitor Skipped Campaign']);
        $dev = Device::create(['user_id' => $this->clientUser->id, 'device_token' => 'tok_mon_' . uniqid(), 'type' => 'android']);

        for ($i = 0; $i < 3; $i++) {
            $this->createToken([
                'notification_id' => $notification->id,
                'device_id' => $dev->id,
                'token' => 'tok_mon_' . $i . '_' . uniqid(),
                'status' => 'skipped_by_feature_flag',
                'error_code' => 'DIRECT_FCM_DISABLED',
            ]);
        }

        $response = $this->actingAs($this->adminUser)->get(route('dashboard.notifications.queueMonitor'));
        $response->assertStatus(200);
        $response->assertSee('مكتمل — تخطي بـ Flag');
    }

    /**
     * 10. Campaign does not visually imply successful delivery.
     */
    public function test_10_campaign_does_not_visually_imply_successful_delivery()
    {
        $notification = $this->createCampaign([
            'title' => 'Purely Skipped Campaign',
            'accepted_by_fcm_count' => 0,
            'processing_status' => 'completed',
        ]);

        $dev = Device::create(['user_id' => $this->clientUser->id, 'device_token' => 'tok_pure_skip_' . uniqid(), 'type' => 'android']);
        $this->createToken([
            'notification_id' => $notification->id,
            'device_id' => $dev->id,
            'token' => $dev->device_token,
            'status' => 'skipped_by_feature_flag',
            'error_code' => 'DIRECT_FCM_DISABLED',
        ]);

        $response = $this->actingAs($this->adminUser)->get(route('dashboard.notifications.show', $notification->id));
        $response->assertStatus(200);
        $response->assertSee('مكتمل — تخطي بـ Flag');
        $response->assertSee('تخطي Flag');
    }

    /**
     * 11. Show permission does not grant export.
     */
    public function test_11_show_permission_does_not_grant_export()
    {
        $notification = $this->createCampaign();

        // Show permission user CAN view show page
        $resShow = $this->actingAs($this->showOnlyStaff)->get(route('dashboard.notifications.show', $notification->id));
        $resShow->assertStatus(200);

        // Show permission user CANNOT view export (403 Forbidden)
        $resExport = $this->actingAs($this->showOnlyStaff)->get(route('dashboard.notifications.exportDetails', [$notification->id, 'eligibility']));
        $resExport->assertStatus(403);
    }

    /**
     * 12. Export permission grants intended export endpoints with masked tokens.
     */
    public function test_12_export_permission_grants_intended_export_endpoints()
    {
        $notification = $this->createCampaign();
        $rawToken = 'sensitive_raw_fcm_token_xyz_987654321';
        $dev = Device::create(['user_id' => $this->clientUser->id, 'device_token' => $rawToken, 'type' => 'android']);
        $this->createToken(['notification_id' => $notification->id, 'device_id' => $dev->id, 'token' => $rawToken, 'status' => 'accepted']);

        $response = $this->actingAs($this->exportStaff)->get(route('dashboard.notifications.exportDetails', [$notification->id, 'devices']));
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');

        $content = $response->streamedContent();
        $this->assertStringNotContainsString($rawToken, $content);
        $this->assertStringContainsString('***654321', $content);
    }

    /**
     * 13. All three export types enforce authorization.
     */
    public function test_13_all_three_export_types_enforce_authorization()
    {
        $notification = $this->createCampaign();
        $types = ['eligibility', 'devices', 'errors'];

        foreach ($types as $type) {
            // Show only staff gets 403 on all three
            $resBlocked = $this->actingAs($this->showOnlyStaff)->get(route('dashboard.notifications.exportDetails', [$notification->id, $type]));
            $resBlocked->assertStatus(403);

            // Export staff gets 200 on all three
            $resAllowed = $this->actingAs($this->exportStaff)->get(route('dashboard.notifications.exportDetails', [$notification->id, $type]));
            $resAllowed->assertStatus(200);
        }
    }

    /**
     * 14. Non-admin cannot export.
     */
    public function test_14_non_admin_cannot_export()
    {
        $notification = $this->createCampaign();

        $response = $this->actingAs($this->clientUser)->get(route('dashboard.notifications.exportDetails', [$notification->id, 'eligibility']));
        $response->assertStatus(403);
    }

    /**
     * 15. Rejected export does not stream data.
     */
    public function test_15_rejected_export_does_not_stream_data()
    {
        $notification = $this->createCampaign();
        $dev = Device::create(['user_id' => $this->clientUser->id, 'device_token' => 'secret_stream_tok', 'type' => 'android']);
        $this->createToken(['notification_id' => $notification->id, 'device_id' => $dev->id, 'token' => 'secret_stream_tok', 'status' => 'queued']);

        $response = $this->actingAs($this->showOnlyStaff)->get(route('dashboard.notifications.exportDetails', [$notification->id, 'devices']));

        $response->assertStatus(403);
        $this->assertFalse($response->headers->has('Content-Disposition'));
        $this->assertNotEquals('text/csv; charset=UTF-8', $response->headers->get('Content-Type'));
        $this->assertStringNotContainsString('Device ID', $response->getContent());
        $this->assertStringNotContainsString('secret_stream_tok', $response->getContent());
    }
}
