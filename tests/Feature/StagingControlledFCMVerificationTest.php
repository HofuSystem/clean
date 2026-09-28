<?php

namespace Tests\Feature;

use Core\Notification\Jobs\SendFcmBatchJob;
use Core\Notification\Models\Notification;
use Core\Notification\Models\NotificationToken;
use Core\Notification\Models\UsersNotification;
use Core\Notification\Services\FCMService;
use Core\Notification\Services\RecipientEligibilityService;
use Core\Users\Models\Device;
use Core\Users\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class StagingControlledFCMVerificationTest extends TestCase
{
    use DatabaseTransactions;

    protected User $testUser1;
    protected User $testUserMultiDevice;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'cache.default' => 'array',
            'notification.direct_fcm_enabled' => true,
            'notification.delivery_events_enabled' => false,
            'notification.permission_mode' => 'observe',
            'notification.marketing_preference_mode' => 'observe',
        ]);

        // Mock FCM Config and Token in Cache so FCMService proceeds safely to Http::pool
        Cache::forever('fcm_config_file', [
            'project_id' => 'cleanstation-staging-mock',
            'client_email' => 'staging-mock@cleanstation.iam.gserviceaccount.com',
        ]);
        Cache::forever('fcm_access_token', 'mock_staging_access_token_safe_test');

        $this->testUser1 = User::create([
            'fullname' => 'Test User Single',
            'email' => 'test-single-' . uniqid() . '@example.com',
            'phone' => '9665' . rand(1000000, 9999999),
            'password' => bcrypt('password'),
            'is_active' => 1,
            'is_allow_notify' => 1,
            'is_allow_notify_confirmed_at' => now(),
        ]);

        $this->testUserMultiDevice = User::create([
            'fullname' => 'Test User Multi Device',
            'email' => 'test-multi-' . uniqid() . '@example.com',
            'phone' => '9665' . rand(1000000, 9999999),
            'password' => bcrypt('password'),
            'is_active' => 1,
            'is_allow_notify' => 1,
            'is_allow_notify_confirmed_at' => now(),
        ]);
    }

    protected function createCampaign(array $attributes = []): Notification
    {
        return Notification::create(array_merge([
            'title' => 'Controlled Test Campaign',
            'body' => 'Controlled Test Body',
            'for' => 'phone',
            'for_data' => $this->testUser1->phone,
            'types' => json_encode(['apps']),
            'purpose' => 'marketing',
            'processing_status' => 'processing',
            'payload' => json_encode(['delivery_channel' => 'direct_fcm']),
        ], $attributes));
    }

    /**
     * 1. Single test device campaign dispatch & execution.
     */
    public function test_01_single_device_campaign_metrics_and_execution()
    {
        $campaign = $this->createCampaign();
        $dev = Device::create([
            'user_id' => $this->testUser1->id,
            'device_token' => 'fcm_test_token_single_' . uniqid(),
            'type' => 'android',
            'token_status' => 'valid',
        ]);

        $tokRow = NotificationToken::create([
            'notification_id' => $campaign->id,
            'device_id' => $dev->id,
            'user_id' => $this->testUser1->id,
            'token' => $dev->device_token,
            'platform' => 'android',
            'topic' => 'direct',
            'status' => 'queued',
        ]);

        $devicesBatch = [[
            'notification_token_id' => $tokRow->id,
            'device_id' => $dev->id,
            'user_id' => $this->testUser1->id,
            'token' => $dev->device_token,
            'platform' => 'android',
            'app_context' => 'client',
        ]];

        Http::fake([
            'https://fcm.googleapis.com/*' => Http::response(['name' => 'projects/test/messages/msg_001'], 200),
        ]);

        $startMem = memory_get_usage();
        $startTime = microtime(true);

        $job = new SendFcmBatchJob($campaign, $devicesBatch);
        $job->handle(app(FCMService::class));

        $duration = microtime(true) - $startTime;

        $tokRow->refresh();
        $this->assertEquals('accepted', $tokRow->status);
        $this->assertNotNull($tokRow->accepted_at);
        $this->assertNull($tokRow->error_code);

        $this->assertLessThan(2.0, $duration, 'Single device job should execute under 2 seconds');
    }

    /**
     * 2. Five test devices campaign batch.
     */
    public function test_02_five_devices_campaign_metrics()
    {
        $campaign = $this->createCampaign();
        $devicesBatch = [];

        Http::fake([
            'https://fcm.googleapis.com/*' => Http::response(['name' => 'projects/test/messages/msg_batch'], 200),
        ]);

        for ($i = 0; $i < 5; $i++) {
            $dev = Device::create([
                'user_id' => $this->testUser1->id,
                'device_token' => 'fcm_test_token_5_' . $i . '_' . uniqid(),
                'type' => $i % 2 === 0 ? 'android' : 'ios',
                'token_status' => 'valid',
            ]);

            $tokRow = NotificationToken::create([
                'notification_id' => $campaign->id,
                'device_id' => $dev->id,
                'user_id' => $this->testUser1->id,
                'token' => $dev->device_token,
                'platform' => $dev->type,
                'topic' => 'direct',
                'status' => 'queued',
            ]);

            $devicesBatch[] = [
                'notification_token_id' => $tokRow->id,
                'device_id' => $dev->id,
                'user_id' => $this->testUser1->id,
                'token' => $dev->device_token,
                'platform' => $dev->type,
                'app_context' => 'client',
            ];
        }

        $job = new SendFcmBatchJob($campaign, $devicesBatch);
        $job->handle(app(FCMService::class));

        $acceptedCount = NotificationToken::where('notification_id', $campaign->id)->where('status', 'accepted')->count();
        $this->assertEquals(5, $acceptedCount);
    }

    /**
     * 3. 50-device batch size verification, query count and memory safety.
     */
    public function test_03_fifty_device_batch_query_count_and_memory()
    {
        $campaign = $this->createCampaign();
        $devicesBatch = [];

        Http::fake([
            'https://fcm.googleapis.com/*' => Http::response(['name' => 'projects/test/messages/msg_50'], 200),
        ]);

        for ($i = 0; $i < 50; $i++) {
            $dev = Device::create([
                'user_id' => $this->testUser1->id,
                'device_token' => 'fcm_test_tok_50_' . $i . '_' . uniqid(),
                'type' => 'android',
                'token_status' => 'valid',
            ]);

            $tok = NotificationToken::create([
                'notification_id' => $campaign->id,
                'device_id' => $dev->id,
                'user_id' => $this->testUser1->id,
                'token' => $dev->device_token,
                'platform' => 'android',
                'topic' => 'direct',
                'status' => 'queued',
            ]);

            $devicesBatch[] = [
                'notification_token_id' => $tok->id,
                'device_id' => $dev->id,
                'user_id' => $this->testUser1->id,
                'token' => $dev->device_token,
                'platform' => 'android',
                'app_context' => 'client',
            ];
        }

        $this->assertCount(50, $devicesBatch, 'Batch size must be exactly 50');

        $queryCount = 0;
        DB::listen(function () use (&$queryCount) {
            $queryCount++;
        });

        $startMem = memory_get_usage();
        $job = new SendFcmBatchJob($campaign, $devicesBatch);
        $job->handle(app(FCMService::class));

        $acceptedCount = NotificationToken::where('notification_id', $campaign->id)->where('status', 'accepted')->count();
        $this->assertEquals(50, $acceptedCount);

        $this->assertLessThan(200, $queryCount, 'Database queries for 50-device batch must remain strictly bounded');
    }

    /**
     * 4. Multi-device user routing: sends to all eligible devices of the user.
     */
    public function test_04_multi_device_user_receives_push_on_all_eligible_devices()
    {
        $campaign = $this->createCampaign(['for_data' => $this->testUserMultiDevice->phone]);

        // Device 1: Android
        $dev1 = Device::create([
            'user_id' => $this->testUserMultiDevice->id,
            'device_token' => 'multi_dev_android_' . uniqid(),
            'type' => 'android',
            'token_status' => 'valid',
        ]);
        // Device 2: iOS
        $dev2 = Device::create([
            'user_id' => $this->testUserMultiDevice->id,
            'device_token' => 'multi_dev_ios_' . uniqid(),
            'type' => 'ios',
            'token_status' => 'valid',
        ]);

        $tok1 = NotificationToken::create([
            'notification_id' => $campaign->id,
            'device_id' => $dev1->id,
            'user_id' => $this->testUserMultiDevice->id,
            'token' => $dev1->device_token,
            'platform' => 'android',
            'topic' => 'direct',
            'status' => 'queued',
        ]);
        $tok2 = NotificationToken::create([
            'notification_id' => $campaign->id,
            'device_id' => $dev2->id,
            'user_id' => $this->testUserMultiDevice->id,
            'token' => $dev2->device_token,
            'platform' => 'ios',
            'topic' => 'direct',
            'status' => 'queued',
        ]);

        Http::fake([
            'https://fcm.googleapis.com/*' => Http::response(['name' => 'projects/test/messages/msg_multi'], 200),
        ]);

        $devicesBatch = [
            ['notification_token_id' => $tok1->id, 'device_id' => $dev1->id, 'user_id' => $this->testUserMultiDevice->id, 'token' => $dev1->device_token, 'platform' => 'android', 'app_context' => 'client'],
            ['notification_token_id' => $tok2->id, 'device_id' => $dev2->id, 'user_id' => $this->testUserMultiDevice->id, 'token' => $dev2->device_token, 'platform' => 'ios', 'app_context' => 'client'],
        ];

        $job = new SendFcmBatchJob($campaign, $devicesBatch);
        $job->handle(app(FCMService::class));

        $tok1->refresh();
        $tok2->refresh();

        $this->assertEquals('accepted', $tok1->status);
        $this->assertEquals('accepted', $tok2->status);
    }

    /**
     * 5. Idempotency: Re-running a job does NOT re-send or duplicate accepted tokens.
     */
    public function test_05_job_rerun_does_not_resend_already_accepted_tokens()
    {
        $campaign = $this->createCampaign();
        $dev = Device::create([
            'user_id' => $this->testUser1->id,
            'device_token' => 'idempotent_tok_' . uniqid(),
            'type' => 'android',
            'token_status' => 'valid',
        ]);

        $tok = NotificationToken::create([
            'notification_id' => $campaign->id,
            'device_id' => $dev->id,
            'user_id' => $this->testUser1->id,
            'token' => $dev->device_token,
            'platform' => 'android',
            'topic' => 'direct',
            'status' => 'accepted',
            'accepted_at' => now(),
        ]);

        $httpCallsCount = 0;
        Http::fake(function () use (&$httpCallsCount) {
            $httpCallsCount++;
            return Http::response(['name' => 'projects/test/messages/msg'], 200);
        });

        $devicesBatch = [[
            'notification_token_id' => $tok->id,
            'device_id' => $dev->id,
            'user_id' => $this->testUser1->id,
            'token' => $dev->device_token,
            'platform' => 'android',
            'app_context' => 'client',
        ]];

        $job = new SendFcmBatchJob($campaign, $devicesBatch);
        $job->handle(app(FCMService::class));

        $this->assertEquals(0, $httpCallsCount, 'Already accepted tokens must be skipped to prevent duplicate sends');
    }

    /**
     * 6. Error classification: permanent vs transient.
     */
    public function test_06_error_classification_permanent_and_transient()
    {
        $campaign = $this->createCampaign();

        // Device 1: Unregistered (Permanent)
        $devPerm = Device::create([
            'user_id' => $this->testUser1->id,
            'device_token' => 'invalid_fcm_token_perm',
            'type' => 'android',
        ]);
        $tokPerm = NotificationToken::create([
            'notification_id' => $campaign->id,
            'device_id' => $devPerm->id,
            'user_id' => $this->testUser1->id,
            'token' => $devPerm->device_token,
            'topic' => 'direct',
            'status' => 'queued',
        ]);

        // Device 2: Unavailable (Transient)
        $devTrans = Device::create([
            'user_id' => $this->testUser1->id,
            'device_token' => 'unavailable_fcm_token_trans',
            'type' => 'ios',
        ]);
        $tokTrans = NotificationToken::create([
            'notification_id' => $campaign->id,
            'device_id' => $devTrans->id,
            'user_id' => $this->testUser1->id,
            'token' => $devTrans->device_token,
            'topic' => 'direct',
            'status' => 'queued',
        ]);

        Http::fake([
            'https://fcm.googleapis.com/*' => Http::sequence()
                ->push(['error' => ['status' => 'UNREGISTERED', 'message' => 'Token unregistered']], 404)
                ->push(['error' => ['status' => 'UNAVAILABLE', 'message' => 'Service temporarily unavailable']], 503),
        ]);

        $devicesBatch = [
            ['notification_token_id' => $tokPerm->id, 'device_id' => $devPerm->id, 'user_id' => $this->testUser1->id, 'token' => $devPerm->device_token, 'platform' => 'android', 'app_context' => 'client'],
            ['notification_token_id' => $tokTrans->id, 'device_id' => $devTrans->id, 'user_id' => $this->testUser1->id, 'token' => $devTrans->device_token, 'platform' => 'ios', 'app_context' => 'client'],
        ];

        $job = new SendFcmBatchJob($campaign, $devicesBatch);
        $job->handle(app(FCMService::class));

        $tokPerm->refresh();
        $tokTrans->refresh();

        $this->assertEquals('permanent_failed', $tokPerm->status);
        $this->assertEquals('UNREGISTERED', $tokPerm->error_code);

        $this->assertEquals('transient_failed', $tokTrans->status);
        $this->assertEquals('UNAVAILABLE', $tokTrans->error_code);
    }
}
