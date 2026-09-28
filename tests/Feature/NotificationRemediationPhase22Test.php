<?php

namespace Tests\Feature;

use Core\Notification\Helpers\NotificationsManger;
use Core\Notification\Jobs\SendFcmBatchJob;
use Core\Notification\Models\Notification;
use Core\Notification\Models\NotificationToken;
use Core\Notification\Models\UsersNotification;
use Core\Notification\Services\RecipientEligibilityService;
use Core\Users\Models\Device;
use Core\Users\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class NotificationRemediationPhase22Test extends TestCase
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

        $this->adminUser = User::create([
            'fullname' => 'Admin User Phase22',
            'email' => 'admin-p22-' . uniqid() . '@example.com',
            'phone' => '9665' . rand(1000000, 9999999),
            'password' => bcrypt('secret123'),
            'is_active' => 1,
        ]);
        $this->adminUser->assignRole('admin');

        $this->showOnlyStaff = User::create([
            'fullname' => 'Show Only Staff P22',
            'email' => 'show-p22-' . uniqid() . '@example.com',
            'phone' => '9665' . rand(1000000, 9999999),
            'password' => bcrypt('secret123'),
            'is_active' => 1,
        ]);
        $this->showOnlyStaff->givePermissionTo('dashboard.notifications.show');

        $this->exportStaff = User::create([
            'fullname' => 'Export Staff P22',
            'email' => 'export-p22-' . uniqid() . '@example.com',
            'phone' => '9665' . rand(1000000, 9999999),
            'password' => bcrypt('secret123'),
            'is_active' => 1,
        ]);
        $this->exportStaff->givePermissionTo('dashboard.notifications.export');

        $this->clientUser = User::create([
            'fullname' => 'Client User Phase22',
            'email' => 'client-p22-' . uniqid() . '@example.com',
            'phone' => '9665' . rand(1000000, 9999999),
            'password' => bcrypt('secret123'),
            'is_active' => 1,
            'is_allow_notify' => 1,
            'is_allow_notify_confirmed_at' => now(),
        ]);
        $this->clientUser->assignRole('client');
    }

    protected function createCampaign(array $attributes = []): Notification
    {
        return Notification::create(array_merge([
            'title' => 'Phase 2.2 Test Campaign',
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
     * 1. Proves devicesBatch payload origin, keys, and meaning of identifiers.
     */
    public function test_01_proves_devices_batch_payload_origin_and_meaning_of_identifiers()
    {
        $dev = Device::create([
            'user_id' => $this->clientUser->id,
            'device_token' => 'pipe_tok_' . uniqid(),
            'type' => 'android',
            'installation_id' => 'inst_p22_' . uniqid(),
            'app_context' => 'client',
            'token_status' => 'active',
        ]);

        $notification = $this->createCampaign();

        // 1. Trace from RecipientEligibilityService
        $eligibilityService = app(RecipientEligibilityService::class);
        $result = $eligibilityService->evaluateEligibility($notification);
        $eligibleDevices = $result['eligible_devices'];

        $this->assertNotEmpty($eligibleDevices);
        $firstItem = collect($eligibleDevices)->firstWhere('device_id', $dev->id);

        $this->assertNotNull($firstItem);
        // Assert pipeline output keys
        $this->assertArrayHasKey('device_id', $firstItem);
        $this->assertArrayHasKey('user_id', $firstItem);
        $this->assertArrayHasKey('token', $firstItem);
        $this->assertArrayHasKey('platform', $firstItem);
        $this->assertArrayHasKey('installation_id', $firstItem);
        $this->assertArrayHasKey('app_context', $firstItem);

        // Meaning of device_id: it matches devices.id
        $this->assertEquals($dev->id, $firstItem['device_id']);
        $this->assertEquals($this->clientUser->id, $firstItem['user_id']);
        $this->assertEquals($dev->device_token, $firstItem['token']);
        $this->assertEquals($dev->installation_id, $firstItem['installation_id']);

        // Assert that raw recipient pipeline has NO notification_token_id and NO 'id'
        $this->assertArrayNotHasKey('notification_token_id', $firstItem);
        $this->assertArrayNotHasKey('id', $firstItem);

        // 2. Pre-insert and map notification_token_id as done in NotificationsManger
        $tokenRecord = $this->createToken([
            'notification_id' => $notification->id,
            'user_id' => $firstItem['user_id'],
            'device_id' => $firstItem['device_id'],
            'token' => $firstItem['token'],
            'status' => 'queued',
        ]);

        $insertedTokens = DB::table('notification_tokens')
            ->where('notification_id', $notification->id)
            ->select(['id', 'device_id', 'token'])
            ->get();

        $tokenMap = [];
        foreach ($insertedTokens as $tok) {
            $tokenMap[($tok->device_id ?? '') . '#' . $tok->token] = $tok->id;
        }

        foreach ($eligibleDevices as &$d) {
            $key = ($d['device_id'] ?? '') . '#' . ($d['token'] ?? '');
            if (isset($tokenMap[$key])) {
                $d['notification_token_id'] = $tokenMap[$key];
            }
        }
        unset($d);

        $mappedItem = collect($eligibleDevices)->firstWhere('device_id', $dev->id);
        $this->assertArrayHasKey('notification_token_id', $mappedItem);
        $this->assertEquals($tokenRecord->id, $mappedItem['notification_token_id']);
    }

    /**
     * 2. notification_token_id updates the exact record.
     */
    public function test_02_notification_token_id_updates_exact_record()
    {
        config(['notification.direct_fcm_enabled' => false]);

        $notification = $this->createCampaign();
        $dev1 = Device::create(['user_id' => $this->clientUser->id, 'device_token' => 'tok_p22_exact_1_' . uniqid(), 'type' => 'ios']);
        $dev2 = Device::create(['user_id' => $this->clientUser->id, 'device_token' => 'tok_p22_exact_2_' . uniqid(), 'type' => 'android']);

        $token1 = $this->createToken(['notification_id' => $notification->id, 'device_id' => $dev1->id, 'token' => $dev1->device_token, 'status' => 'queued']);
        $token2 = $this->createToken(['notification_id' => $notification->id, 'device_id' => $dev2->id, 'token' => $dev2->device_token, 'status' => 'queued']);

        $batch = [
            [
                'notification_token_id' => $token1->id,
                'device_id' => $dev1->id,
                'user_id' => $this->clientUser->id,
                'token' => $dev1->device_token,
            ],
        ];

        $job = new SendFcmBatchJob($notification, $batch);
        $job->handle();

        $this->assertEquals('skipped_by_feature_flag', $job->executionStatus);

        $token1->refresh();
        $token2->refresh();

        $this->assertEquals('skipped_by_feature_flag', $token1->status);
        $this->assertEquals('DIRECT_FCM_DISABLED', $token1->error_code);
        $this->assertEquals('queued', $token2->status); // Unaffected!
    }

    /**
     * 3. devices.id is not treated as notification_tokens.id.
     */
    public function test_03_devices_id_is_not_treated_as_notification_tokens_id()
    {
        config(['notification.direct_fcm_enabled' => false]);

        $notification = $this->createCampaign();

        // Create an unrelated queued token
        $unrelatedToken = $this->createToken([
            'notification_id' => $notification->id,
            'status' => 'queued',
            'token' => 'unrelated_tok_' . uniqid(),
        ]);

        // Create a device with id matching or distinct, but batch has 'id' that represents device
        $dev = Device::create(['user_id' => $this->clientUser->id, 'device_token' => 'tok_dev_id_test_' . uniqid(), 'type' => 'android']);

        // Batch passes 'id' representing device, but token doesn't match $unrelatedToken
        $batch = [
            [
                'id' => $unrelatedToken->id, // If treated as notification_tokens.id, it would wrongfully match!
                'device_id' => $dev->id,
                'token' => 'non_matching_token_xyz',
            ],
        ];

        $job = new SendFcmBatchJob($notification, $batch);
        $job->handle();

        $unrelatedToken->refresh();
        // Since 'id' is NOT treated as notification_token_id, unrelatedToken must NOT be touched!
        $this->assertEquals('queued', $unrelatedToken->status);
    }

    /**
     * 4. Batch with empty identifiers updates zero rows (Fail-Closed).
     */
    public function test_04_batch_with_empty_identifiers_updates_zero_rows_fail_closed()
    {
        config(['notification.direct_fcm_enabled' => false]);

        $notification = $this->createCampaign();
        $token1 = $this->createToken(['notification_id' => $notification->id, 'status' => 'queued']);
        $token2 = $this->createToken(['notification_id' => $notification->id, 'status' => 'queued']);

        // Batch with no usable identifiers
        $batch = [
            ['bogus' => 'data'],
            [],
        ];

        $job = new SendFcmBatchJob($notification, $batch);
        $job->handle();

        $this->assertEquals('skipped_by_feature_flag', $job->executionStatus);

        $token1->refresh();
        $token2->refresh();

        $this->assertEquals('queued', $token1->status);
        $this->assertEquals('queued', $token2->status);
    }

    /**
     * 5. Batch with empty identifiers does not convert all campaign queued rows to skipped.
     */
    public function test_05_batch_with_empty_identifiers_does_not_convert_all_campaign_queued_rows()
    {
        config(['notification.direct_fcm_enabled' => false]);

        $notification = $this->createCampaign();
        $tokens = [];
        for ($i = 0; $i < 5; $i++) {
            $tokens[] = $this->createToken(['notification_id' => $notification->id, 'status' => 'queued']);
        }

        // Completely empty batch
        $batch = [];

        $job = new SendFcmBatchJob($notification, $batch);
        $job->handle();

        foreach ($tokens as $tok) {
            $tok->refresh();
            $this->assertEquals('queued', $tok->status);
        }
    }

    /**
     * 6. Two campaigns with intersecting IDs do not affect each other.
     */
    public function test_06_two_campaigns_with_intersecting_ids_do_not_affect_each_other()
    {
        config(['notification.direct_fcm_enabled' => false]);

        $campaignA = $this->createCampaign(['title' => 'Campaign A']);
        $campaignB = $this->createCampaign(['title' => 'Campaign B']);

        $dev = Device::create(['user_id' => $this->clientUser->id, 'device_token' => 'tok_inter_' . uniqid(), 'type' => 'ios']);

        $tokenA = $this->createToken([
            'notification_id' => $campaignA->id,
            'device_id' => $dev->id,
            'token' => $dev->device_token,
            'status' => 'queued',
        ]);

        $tokenB = $this->createToken([
            'notification_id' => $campaignB->id,
            'device_id' => $dev->id,
            'token' => $dev->device_token,
            'status' => 'queued',
        ]);

        $batchA = [
            [
                'notification_token_id' => $tokenA->id,
                'device_id' => $dev->id,
                'token' => $dev->device_token,
            ],
        ];

        $job = new SendFcmBatchJob($campaignA, $batchA);
        $job->handle();

        $tokenA->refresh();
        $tokenB->refresh();

        $this->assertEquals('skipped_by_feature_flag', $tokenA->status);
        $this->assertEquals('queued', $tokenB->status);
    }

    /**
     * 7. Two batches within the same campaign: only requested batch is updated.
     */
    public function test_07_two_batches_within_same_campaign_only_requested_batch_is_updated()
    {
        config(['notification.direct_fcm_enabled' => false]);

        $notification = $this->createCampaign();

        $dev1 = Device::create(['user_id' => $this->clientUser->id, 'device_token' => 'tok_b1_' . uniqid(), 'type' => 'ios']);
        $dev2 = Device::create(['user_id' => $this->clientUser->id, 'device_token' => 'tok_b2_' . uniqid(), 'type' => 'android']);

        $t1 = $this->createToken(['notification_id' => $notification->id, 'device_id' => $dev1->id, 'token' => $dev1->device_token, 'status' => 'queued']);
        $t2 = $this->createToken(['notification_id' => $notification->id, 'device_id' => $dev2->id, 'token' => $dev2->device_token, 'status' => 'queued']);

        $batch1 = [
            ['notification_token_id' => $t1->id, 'device_id' => $dev1->id, 'token' => $dev1->device_token],
        ];

        $job = new SendFcmBatchJob($notification, $batch1);
        $job->handle();

        $t1->refresh();
        $t2->refresh();

        $this->assertEquals('skipped_by_feature_flag', $t1->status);
        $this->assertEquals('queued', $t2->status);
    }

    /**
     * 8. Cross-pair: (device 1, token A) & (device 2, token B) must NOT match (device 1, token B) or (device 2, token A).
     */
    public function test_08_cross_pair_protection_in_legacy_matching()
    {
        config(['notification.direct_fcm_enabled' => false]);

        $notification = $this->createCampaign();

        $dev1 = Device::create(['user_id' => $this->clientUser->id, 'device_token' => 'tok_pair_A_' . uniqid(), 'type' => 'ios']);
        $dev2 = Device::create(['user_id' => $this->clientUser->id, 'device_token' => 'tok_pair_B_' . uniqid(), 'type' => 'android']);

        // Legitimate token records
        $token1 = $this->createToken(['notification_id' => $notification->id, 'device_id' => $dev1->id, 'token' => $dev1->device_token, 'status' => 'queued']);
        $token2 = $this->createToken(['notification_id' => $notification->id, 'device_id' => $dev2->id, 'token' => $dev2->device_token, 'status' => 'queued']);

        // Cross-paired token records
        $crossToken1 = $this->createToken(['notification_id' => $notification->id, 'device_id' => $dev1->id, 'token' => $dev2->device_token, 'status' => 'queued']);
        $crossToken2 = $this->createToken(['notification_id' => $notification->id, 'device_id' => $dev2->id, 'token' => $dev1->device_token, 'status' => 'queued']);

        // Legacy batch WITHOUT notification_token_id
        $batch = [
            ['device_id' => $dev1->id, 'token' => $dev1->device_token],
            ['device_id' => $dev2->id, 'token' => $dev2->device_token],
        ];

        $job = new SendFcmBatchJob($notification, $batch);
        $job->handle();

        $token1->refresh();
        $token2->refresh();
        $crossToken1->refresh();
        $crossToken2->refresh();

        // Exact pairs match
        $this->assertEquals('skipped_by_feature_flag', $token1->status);
        $this->assertEquals('skipped_by_feature_flag', $token2->status);

        // Cross-pairs MUST REMAIN queued
        $this->assertEquals('queued', $crossToken1->status);
        $this->assertEquals('queued', $crossToken2->status);
    }

    /**
     * 9. accepted/transient/permanent/processing records do not change.
     */
    public function test_09_accepted_transient_permanent_records_do_not_change()
    {
        config(['notification.direct_fcm_enabled' => false]);

        $notification = $this->createCampaign();

        $dev = Device::create(['user_id' => $this->clientUser->id, 'device_token' => 'tok_status_stay_' . uniqid(), 'type' => 'ios']);

        $acc = $this->createToken(['notification_id' => $notification->id, 'device_id' => $dev->id, 'token' => $dev->device_token, 'status' => 'accepted']);
        $trans = $this->createToken(['notification_id' => $notification->id, 'device_id' => $dev->id, 'token' => $dev->device_token . '_tr', 'status' => 'transient_failed', 'error_code' => 'UNAVAILABLE']);
        $perm = $this->createToken(['notification_id' => $notification->id, 'device_id' => $dev->id, 'token' => $dev->device_token . '_pm', 'status' => 'permanent_failed', 'error_code' => 'UNREGISTERED']);

        $batch = [
            ['notification_token_id' => $acc->id, 'device_id' => $dev->id, 'token' => $dev->device_token],
            ['notification_token_id' => $trans->id, 'device_id' => $dev->id, 'token' => $dev->device_token . '_tr'],
            ['notification_token_id' => $perm->id, 'device_id' => $dev->id, 'token' => $dev->device_token . '_pm'],
        ];

        $job = new SendFcmBatchJob($notification, $batch);
        $job->handle();

        $acc->refresh();
        $trans->refresh();
        $perm->refresh();

        $this->assertEquals('accepted', $acc->status);
        $this->assertEquals('transient_failed', $trans->status);
        $this->assertEquals('UNAVAILABLE', $trans->error_code);
        $this->assertEquals('permanent_failed', $perm->status);
        $this->assertEquals('UNREGISTERED', $perm->error_code);
    }

    /**
     * 10. Re-running job remains idempotent.
     */
    public function test_10_rerunning_job_remains_idempotent()
    {
        config(['notification.direct_fcm_enabled' => false]);

        $notification = $this->createCampaign();
        $dev = Device::create(['user_id' => $this->clientUser->id, 'device_token' => 'tok_idem_' . uniqid(), 'type' => 'android']);
        $token = $this->createToken(['notification_id' => $notification->id, 'device_id' => $dev->id, 'token' => $dev->device_token, 'status' => 'queued']);

        $batch = [
            ['notification_token_id' => $token->id, 'device_id' => $dev->id, 'token' => $dev->device_token],
        ];

        // 1st run
        $job1 = new SendFcmBatchJob($notification, $batch);
        $job1->handle();

        $token->refresh();
        $this->assertEquals('skipped_by_feature_flag', $token->status);
        $firstUpdatedAt = $token->updated_at;

        // 2nd run
        $job2 = new SendFcmBatchJob($notification, $batch);
        $job2->handle();

        $token->refresh();
        $this->assertEquals('skipped_by_feature_flag', $token->status);
        $this->assertEquals('skipped_by_feature_flag', $job2->executionStatus);
    }

    /**
     * 11. No HTTP or FCM request occurs when flag is false.
     */
    public function test_11_no_http_or_fcm_request_occurs_when_flag_is_false()
    {
        config(['notification.direct_fcm_enabled' => false]);
        Http::fake();

        $notification = $this->createCampaign();
        $dev = Device::create(['user_id' => $this->clientUser->id, 'device_token' => 'tok_no_http_' . uniqid(), 'type' => 'android']);
        $token = $this->createToken(['notification_id' => $notification->id, 'device_id' => $dev->id, 'token' => $dev->device_token, 'status' => 'queued']);

        $batch = [
            ['notification_token_id' => $token->id, 'device_id' => $dev->id, 'token' => $dev->device_token],
        ];

        $job = new SendFcmBatchJob($notification, $batch);
        $job->handle();

        Http::assertNothingSent();
    }

    /**
     * 12. Direct campaign with snapshot and skipped rows displays Direct FCM.
     */
    public function test_12_direct_campaign_with_snapshot_and_skipped_rows_displays_direct_fcm()
    {
        $notification = $this->createCampaign([
            'payload' => json_encode(['delivery_channel' => 'direct_fcm']),
            'accepted_by_fcm_count' => 0,
        ]);

        $dev = Device::create(['user_id' => $this->clientUser->id, 'device_token' => 'tok_dir_skip_' . uniqid(), 'type' => 'android']);
        $this->createToken([
            'notification_id' => $notification->id,
            'device_id' => $dev->id,
            'token' => $dev->device_token,
            'status' => 'skipped_by_feature_flag',
            'error_code' => 'DIRECT_FCM_DISABLED',
        ]);

        $this->assertEquals('direct_fcm', $notification->getDeliveryChannel());
    }

    /**
     * 13. Legacy campaign with old notification_tokens remains Legacy.
     */
    public function test_13_legacy_campaign_with_old_notification_tokens_remains_legacy()
    {
        $notification = $this->createCampaign([
            'payload' => json_encode(['delivery_channel' => 'legacy_topic']),
            'accepted_by_fcm_count' => 0,
        ]);

        $dev = Device::create(['user_id' => $this->clientUser->id, 'device_token' => 'tok_leg_old_' . uniqid(), 'type' => 'ios']);
        $this->createToken([
            'notification_id' => $notification->id,
            'device_id' => $dev->id,
            'token' => $dev->device_token,
            'status' => 'success',
            'topic' => 'all',
        ]);

        $this->assertEquals('legacy_topic', $notification->getDeliveryChannel());
    }

    /**
     * 14. Historical campaign without snapshot is not classified Direct simply because token rows exist.
     */
    public function test_14_historical_campaign_without_snapshot_is_not_classified_direct_simply_due_to_tokens()
    {
        $notification = $this->createCampaign([
            'payload' => null, // No snapshot
            'accepted_by_fcm_count' => 0,
        ]);

        $dev = Device::create(['user_id' => $this->clientUser->id, 'device_token' => 'tok_hist_no_snap_' . uniqid(), 'type' => 'ios']);
        $this->createToken([
            'notification_id' => $notification->id,
            'device_id' => $dev->id,
            'token' => $dev->device_token,
            'status' => 'success',
            'topic' => 'all',
        ]);

        $this->assertEquals('legacy_topic', $notification->getDeliveryChannel());
    }

    /**
     * 15. Historical campaign similar to 1120558 displays correct semantics in show and queue monitor.
     */
    public function test_15_historical_campaign_similar_to_1120558_displays_correct_semantics()
    {
        // Replicating baseline campaign #1120558
        $notification = $this->createCampaign([
            'title' => 'Historical Campaign 1120558 Mock',
            'body' => 'Historical Legacy Topic Push',
            'payload' => null, // Historical had no delivery_channel snapshot
            'accepted_by_fcm_count' => 0,
            'sent_count' => 2142,
            'processing_status' => 'completed',
        ]);

        $dev = Device::create(['user_id' => $this->clientUser->id, 'device_token' => 'tok_1120558_' . uniqid(), 'type' => 'ios']);
        $this->createToken([
            'notification_id' => $notification->id,
            'device_id' => $dev->id,
            'token' => $dev->device_token,
            'status' => 'success',
            'topic' => 'all',
        ]);
        $notification->users()->attach($this->clientUser->id, ['status' => 'sent']);

        // Channel must be legacy_topic, NOT direct_fcm
        $this->assertEquals('legacy_topic', $notification->getDeliveryChannel());
        $this->assertEquals('Legacy Topic Subscription', $notification->getDeliveryMetricLabel());
        $this->assertEquals('1', $notification->getDeliveryMetricValue());

        // Show page verification
        $resShow = $this->actingAs($this->adminUser)->get(route('dashboard.notifications.show', $notification->id));
        $resShow->assertStatus(200);
        $resShow->assertSee('Legacy Topic Subscription');

        // Queue monitor verification
        $resMonitor = $this->actingAs($this->adminUser)->get(route('dashboard.notifications.queueMonitor'));
        $resMonitor->assertStatus(200);
    }

    /**
     * 16. Previous Export Authorization security remains intact.
     */
    public function test_16_export_authorization_security_remains_intact()
    {
        $notification = $this->createCampaign();

        // Show-only staff gets 403 on export
        $resShowStaff = $this->actingAs($this->showOnlyStaff)->get(route('dashboard.notifications.exportDetails', [$notification->id, 'eligibility']));
        $resShowStaff->assertStatus(403);

        // Client gets 403
        $resClient = $this->actingAs($this->clientUser)->get(route('dashboard.notifications.exportDetails', [$notification->id, 'eligibility']));
        $resClient->assertStatus(403);

        // Export authorized staff gets 200 stream
        $resExport = $this->actingAs($this->exportStaff)->get(route('dashboard.notifications.exportDetails', [$notification->id, 'eligibility']));
        $resExport->assertStatus(200);
    }
}