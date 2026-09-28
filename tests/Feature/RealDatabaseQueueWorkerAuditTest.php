<?php

namespace Tests\Feature;

use Core\Notification\Jobs\SendFcmBatchJob;
use Core\Notification\Models\Notification;
use Core\Notification\Models\NotificationToken;
use Core\Notification\Services\FCMService;
use Core\Users\Models\Device;
use Core\Users\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class RealDatabaseQueueWorkerAuditTest extends TestCase
{
    protected function tearDown(): void
    {
        Notification::setEventDispatcher(app('events'));
        DB::connection('sqlite')->table('jobs')->delete();
        config(['queue.default' => 'sync']);
        parent::tearDown();
    }

    protected User $testUser;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'cache.default' => 'array',
            'queue.default' => 'database',
            'queue.connections.database.connection' => 'sqlite',
            'queue.connections.database.table' => 'jobs',
            'queue.connections.database.queue' => 'default',
            'queue.connections.database.retry_after' => 90,
        ]);

        // Clean queue tables in testing.sqlite
        DB::connection('sqlite')->table('jobs')->delete();
        DB::connection('sqlite')->table('failed_jobs')->delete();

        // Mock FCM cache credentials
        Cache::forever('fcm_config_file', [
            'project_id' => 'cleanstation-worker-audit',
            'client_email' => 'worker-audit@cleanstation.iam.gserviceaccount.com',
        ]);
        Cache::forever('fcm_access_token', 'mock_token_worker_audit');
        Notification::unsetEventDispatcher();

        $this->testUser = User::create([
            'fullname' => 'Queue Worker Audit User',
            'email' => 'qw-audit-' . uniqid() . '@example.com',
            'phone' => '9665' . rand(1000000, 9999999),
            'password' => bcrypt('password'),
            'is_active' => 1,
            'is_allow_notify' => 1,
            'is_allow_notify_confirmed_at' => now(),
        ]);
    }

    protected function createCampaign(array $attributes = []): Notification
    {
        $campaign = Notification::create(array_merge([
            'title' => 'Worker Audit Campaign',
            'body' => 'Worker Audit Body',
            'for' => 'phone',
            'for_data' => $this->testUser->phone,
            'types' => json_encode(['apps']),
            'purpose' => 'marketing',
            'processing_status' => 'processing',
            'payload' => json_encode(['delivery_channel' => 'direct_fcm']),
        ], $attributes));

        // Delete observer-dispatched SendNotificationJob so queue is dedicated to SendFcmBatchJob
        DB::connection('sqlite')->table('jobs')->delete();

        return $campaign;
    }

    /**
     * Section 3: Real Database Queue Worker Full Cycle.
     */
    public function test_01_real_queue_worker_executes_dispatched_job_and_updates_database()
    {
        config(['notification.direct_fcm_enabled' => true]);

        $campaign = $this->createCampaign();
        $dev = Device::create([
            'user_id' => $this->testUser->id,
            'device_token' => 'fcm_worker_test_token_' . uniqid(),
            'type' => 'android',
            'token_status' => 'valid',
        ]);

        $tok = NotificationToken::create([
            'notification_id' => $campaign->id,
            'device_id' => $dev->id,
            'user_id' => $this->testUser->id,
            'token' => $dev->device_token,
            'platform' => 'android',
            'topic' => 'direct',
            'status' => 'queued',
            'queued_at' => now(),
        ]);

        $devicesBatch = [[
            'notification_token_id' => $tok->id,
            'device_id' => $dev->id,
            'user_id' => $this->testUser->id,
            'token' => $dev->device_token,
            'platform' => 'android',
            'app_context' => 'client',
        ]];

        // 1. Dispatch SendFcmBatchJob to real database queue
        SendFcmBatchJob::dispatch($campaign, $devicesBatch);

        // 2. Assert job row exists in jobs table
        $jobRow = DB::connection('sqlite')->table('jobs')->first();
        $this->assertNotNull($jobRow, 'Job must exist in database jobs table after dispatch');

        // 3. Inspect serialized payload
        $payload = json_decode($jobRow->payload, true);
        $this->assertNotEmpty($payload);
        $this->assertEquals(SendFcmBatchJob::class, $payload['displayName']);

        $command = unserialize($payload['data']['command']);
        $this->assertInstanceOf(SendFcmBatchJob::class, $command);
        $this->assertNotEmpty($command->devicesBatch);
        $this->assertEquals($tok->id, $command->devicesBatch[0]['notification_token_id']);
        $this->assertEquals($dev->id, $command->devicesBatch[0]['device_id']);

        // 4. Fake external FCM call
        Http::fake([
            'https://fcm.googleapis.com/*' => Http::response(['name' => 'projects/test/messages/msg_worker_01'], 200),
        ]);

        $failedBefore = DB::connection('sqlite')->table('failed_jobs')->count();
        $this->assertEquals(0, $failedBefore);

        // 5. Execute real worker command via Artisan::call
        $exitCode = Artisan::call('queue:work', [
            'connection' => 'database',
            '--queue' => 'default',
            '--stop-when-empty' => true,
            '--tries' => 1,
            '--timeout' => 60,
            '--memory' => 512,
        ]);
        $this->assertEquals(0, $exitCode);

        // 6. Assert job deleted from jobs table upon success
        $this->assertDatabaseMissing('jobs', ['id' => $jobRow->id], 'sqlite');
        $batchRemaining = DB::connection('sqlite')->table('jobs')->where('payload', 'like', '%SendFcmBatchJob%')->count();
        $this->assertEquals(0, $batchRemaining, 'Completed job must be deleted from jobs table');

        // 7. Assert notification_tokens updated to accepted
        $tok->refresh();
        $this->assertEquals('accepted', $tok->status);
        $this->assertNotNull($tok->accepted_at);

        // 8. Assert failed_jobs remained 0
        $failedAfter = DB::connection('sqlite')->table('failed_jobs')->count();
        $this->assertEquals(0, $failedAfter);
    }

    /**
     * Section 4: Kill Switch via Real Database Queue Worker.
     */
    public function test_02_kill_switch_via_real_queue_worker_execution()
    {
        // 1. Enable Direct FCM at dispatch time
        config(['notification.direct_fcm_enabled' => true]);

        $campaign = $this->createCampaign();
        $batchSize = 5;
        $devicesBatch = [];
        $tokens = [];

        for ($i = 0; $i < $batchSize; $i++) {
            $dev = Device::create([
                'user_id' => $this->testUser->id,
                'device_token' => 'fcm_ks_worker_token_' . $i . '_' . uniqid(),
                'type' => 'android',
                'token_status' => 'valid',
            ]);

            $tok = NotificationToken::create([
                'notification_id' => $campaign->id,
                'device_id' => $dev->id,
                'user_id' => $this->testUser->id,
                'token' => $dev->device_token,
                'platform' => 'android',
                'topic' => 'direct',
                'status' => 'queued',
                'queued_at' => now(),
            ]);

            $tokens[] = $tok;
            $devicesBatch[] = [
                'notification_token_id' => $tok->id,
                'device_id' => $dev->id,
                'user_id' => $this->testUser->id,
                'token' => $dev->device_token,
                'platform' => 'android',
                'app_context' => 'client',
            ];
        }

        // 2. Dispatch to queue
        SendFcmBatchJob::dispatch($campaign, $devicesBatch);

        $jobRow = DB::connection('sqlite')->table('jobs')->where('payload', 'like', '%SendFcmBatchJob%')->first();
        $this->assertNotNull($jobRow);
        $this->assertEquals($batchSize, NotificationToken::where('notification_id', $campaign->id)->where('status', 'queued')->count());

        // 3. Deactivate Direct FCM (Kill Switch) BEFORE worker runs
        config(['notification.direct_fcm_enabled' => false]);

        $httpCallsCount = 0;
        Http::fake(function () use (&$httpCallsCount) {
            $httpCallsCount++;
            return Http::response(['name' => 'forbidden'], 200);
        });

        // 4. Run worker
        $exitCode = Artisan::call('queue:work', [
            'connection' => 'database',
            '--queue' => 'default',
            '--stop-when-empty' => true,
            '--tries' => 1,
            '--timeout' => 60,
            '--memory' => 512,
        ]);
        $this->assertEquals(0, $exitCode);

        // 5. Verify assertions
        $this->assertEquals(0, $httpCallsCount, 'Zero HTTP requests to FCM when kill switch is tripped');
        $this->assertDatabaseMissing('jobs', ['id' => $jobRow->id], 'sqlite');
        $batchRemaining = DB::connection('sqlite')->table('jobs')->where('payload', 'like', '%SendFcmBatchJob%')->count();
        $this->assertEquals(0, $batchRemaining, 'Job must be cleared from jobs table');

        $queuedRemaining = NotificationToken::where('notification_id', $campaign->id)->where('status', 'queued')->count();
        $this->assertEquals(0, $queuedRemaining, 'No tokens remain queued');

        $skippedCount = NotificationToken::where('notification_id', $campaign->id)
            ->where('status', 'skipped_by_feature_flag')
            ->where('error_code', 'DIRECT_FCM_DISABLED')
            ->count();
        $this->assertEquals($batchSize, $skippedCount, 'All tokens in batch recorded as skipped_by_feature_flag');

        $this->assertEquals(0, NotificationToken::where('notification_id', $campaign->id)->where('status', 'accepted')->count());
        $this->assertEquals(0, NotificationToken::where('notification_id', $campaign->id)->whereIn('status', ['permanent_failed', 'transient_failed'])->count());
        $this->assertEquals(0, DB::connection('sqlite')->table('failed_jobs')->count());
    }

    /**
     * Section 5: Fail-Closed Test via Real Queue Worker.
     */
    public function test_03_fail_closed_via_real_queue_worker_prevents_destructive_updates()
    {
        config(['notification.direct_fcm_enabled' => false]);

        $campaign = $this->createCampaign();

        // Legitimate queued token that must NOT be touched
        $dev = Device::create([
            'user_id' => $this->testUser->id,
            'device_token' => 'legit_token_' . uniqid(),
            'type' => 'android',
        ]);
        $legitToken = NotificationToken::create([
            'notification_id' => $campaign->id,
            'device_id' => $dev->id,
            'user_id' => $this->testUser->id,
            'token' => $dev->device_token,
            'platform' => 'android',
            'topic' => 'direct',
            'status' => 'queued',
        ]);

        // Malformed batch with missing identifiers
        $malformedBatch = [
            ['notification_token_id' => null, 'device_id' => null, 'token' => null],
            ['notification_token_id' => null, 'device_id' => null, 'token' => ''],
        ];

        SendFcmBatchJob::dispatch($campaign, $malformedBatch);
        $jobRow = DB::connection('sqlite')->table('jobs')->where('payload', 'like', '%SendFcmBatchJob%')->first();
        $this->assertNotNull($jobRow);

        // Run worker
        $exitCode = Artisan::call('queue:work', [
            'connection' => 'database',
            '--queue' => 'default',
            '--stop-when-empty' => true,
            '--tries' => 1,
            '--timeout' => 60,
            '--memory' => 512,
        ]);
        $this->assertEquals(0, $exitCode);

        // Verify fail-closed behavior:
        // 1. The legitimate token remains 'queued' (zero rows destructively modified)
        $legitToken->refresh();
        $this->assertEquals('queued', $legitToken->status, 'Fail-closed must not update unrelated queued tokens');

        // 2. The job finishes gracefully (handles fail-closed with error log) without throwing fatal crash
        $this->assertDatabaseMissing('jobs', ['id' => $jobRow->id], 'sqlite');
        $batchRemaining = DB::connection('sqlite')->table('jobs')->where('payload', 'like', '%SendFcmBatchJob%')->count();
        $this->assertEquals(0, $batchRemaining);
        $this->assertEquals(0, DB::connection('sqlite')->table('failed_jobs')->count());
    }

    /**
     * Section 7: Verification of app_context (normalize technician -> technical).
     */
    public function test_04_app_context_normalizes_technician_to_technical()
    {
        $payload = [
            'device_token' => 'tok_technician_' . uniqid(),
            'platform' => 'android',
            'installation_id' => 'inst_tech_' . uniqid(),
            'app_context' => 'technician', // Legacy/informal value
            'notification_permission' => 'authorized',
            'app_version' => '2.1.0',
        ];

        $response = $this->actingAs($this->testUser, 'sanctum')->postJson('/api/devices/sync', $payload);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'data' => [
                'app_context' => 'technical', // Returned canonical value
            ]
        ]);

        $deviceId = $response->json('data.device_id');
        $this->assertNotNull($deviceId);

        // Database assertions:
        // 1. Confirms stored value is 'technical'
        $this->assertDatabaseHas('devices', [
            'id' => $deviceId,
            'app_context' => 'technical',
        ]);

        // 2. Confirms 'technician' was NOT stored
        $this->assertDatabaseMissing('devices', [
            'id' => $deviceId,
            'app_context' => 'technician',
        ]);
    }
}
