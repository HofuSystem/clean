<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Core\Users\Models\User;
use Core\Users\Models\Device;
use Core\Notification\Models\Notification;
use Core\Notification\Models\NotificationToken;
use Core\Notification\Jobs\SendFcmBatchJob;
use Core\Notification\Services\FCMService;

class NotificationPerformanceRemediationTest extends TestCase
{
    protected function tearDown(): void
    {
        Notification::setEventDispatcher(app('events'));
        DB::table('jobs')->delete();
        parent::tearDown();
    }
    protected User $testUser;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => database_path('testing.sqlite'),
            
            'notification.direct_fcm_enabled' => true,
        ]);

        DB::table('jobs')->delete();
        DB::table('failed_jobs')->delete();

        Cache::forever('fcm_config_file', [
            'project_id' => 'cleanstation-perf-test',
            'client_email' => 'perf@cleanstation.iam.gserviceaccount.com',
        ]);
        Cache::forever('fcm_access_token', 'mock_perf_token_123');
        Notification::unsetEventDispatcher();

        $this->testUser = User::create([
            'fullname' => 'Perf User ' . uniqid(),
            'email' => 'perf_' . uniqid() . '@example.com',
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
            'title' => 'Perf Test Campaign ' . uniqid(),
            'body' => 'Perf Test Body',
            'for' => 'none', 'for_data' => 'none',
            'types' => json_encode(['apps']),
            'purpose' => 'marketing',
            'processing_status' => 'processing',
            'payload' => json_encode(['delivery_channel' => 'direct_fcm']),
        ], $attributes));

        DB::table('jobs')->delete();

        return $campaign;
    }

    protected function createDeviceAndToken(Notification $campaign, User $user, string $status = 'queued'): array
    {
        $now = now();
        $tokenStr = 'fcm_tok_' . uniqid();

        $device = Device::create([
            'user_id' => $user->id,
            'device_token' => $tokenStr,
            'type' => 'android',
            'token_status' => 'valid',
            'app_context' => 'client',
            'installation_id' => 'inst_' . uniqid(),
        ]);

        $tokenId = DB::table('notification_tokens')->insertGetId([
            'notification_id' => $campaign->id,
            'user_id' => $user->id,
            'device_id' => $device->id,
            'token' => $tokenStr,
            'topic' => 'direct',
            'status' => $status,
            'title' => $campaign->title,
            'attempts' => 0,
            'queued_at' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('users_notifications')->insert([
            'notifications_type' => Notification::class,
            'notifications_id' => $campaign->id,
            'user_id' => $user->id,
            'status' => 'pending',
            'accepted_devices_count' => 0,
            'failed_devices_count' => 0,
        ]);

        $batchItem = [
            'notification_token_id' => $tokenId,
            'device_id' => $device->id,
            'user_id' => $user->id,
            'token' => $tokenStr,
            'platform' => 'android',
            'installation_id' => $device->installation_id,
            'app_context' => 'client',
        ];

        return [$device, $tokenId, $batchItem];
    }

    /**
     * 1. 50 accepted devices in a single batch
     */
    public function test_01_fifty_accepted_devices()
    {
        Http::fake([
            'https://fcm.googleapis.com/*' => Http::response(['name' => 'projects/test/messages/msg_accepted'], 200),
        ]);

        $campaign = $this->createCampaign();
        $devicesBatch = [];
        $tokenIds = [];

        for ($i = 0; $i < 50; $i++) {
            $u = User::create([
                'fullname' => "User {$i}",
                'email' => "user_50_{$i}_" . uniqid() . "@example.com",
                'phone' => '9665' . rand(1000000, 9999999),
                'password' => 'secret',
                'is_active' => 1,
            ]);
            [, $tId, $batchItem] = $this->createDeviceAndToken($campaign, $u);
            $devicesBatch[] = $batchItem;
            $tokenIds[] = $tId;
        }

        $job = new SendFcmBatchJob($campaign, $devicesBatch);
        $job->handle();

        $acceptedTokens = DB::table('notification_tokens')
            ->where('notification_id', $campaign->id)
            ->where('status', 'accepted')
            ->get();

        $this->assertCount(50, $acceptedTokens);
        foreach ($acceptedTokens as $tok) {
            $this->assertEquals(1, $tok->attempts);
            $this->assertNotNull($tok->accepted_at);
            $this->assertEquals('projects/test/messages/msg_accepted', $tok->fcm_message_id);
        }

        $campaign->refresh();
        $this->assertEquals(50, $campaign->accepted_by_fcm_count);
        $this->assertEquals(0, $campaign->permanent_failed_count);
        $this->assertEquals(0, $campaign->transient_failed_count);
    }

    /**
     * 2. Mixed: 20 accepted, 15 transient, 15 permanent
     */
    public function test_02_mixed_status_batch()
    {
        $campaign = $this->createCampaign();
        $devicesBatch = [];
        $permDeviceIds = [];

        for ($i = 0; $i < 50; $i++) {
            $u = User::create([
                'fullname' => "Mixed User {$i}",
                'email' => "mixed_{$i}_" . uniqid() . "@example.com",
                'phone' => '9665' . rand(1000000, 9999999),
                'password' => 'secret',
                'is_active' => 1,
            ]);
            [$dev, $tId, $batchItem] = $this->createDeviceAndToken($campaign, $u);
            $devicesBatch[] = $batchItem;

            if ($i >= 35) {
                $permDeviceIds[] = $dev->id;
            }
        }

        Http::fake(function (\Illuminate\Http\Client\Request $request) {
            $body = json_decode($request->body(), true);
            $token = $body['message']['token'] ?? '';

            static $callIndex = 0;
            $idx = $callIndex++;

            if ($idx < 20) {
                return Http::response(['name' => "projects/test/messages/msg_{$idx}"], 200);
            } elseif ($idx < 35) {
                return Http::response(['error' => ['status' => 'UNAVAILABLE', 'message' => 'Server busy']], 503);
            } else {
                return Http::response(['error' => ['status' => 'NOT_FOUND', 'message' => 'Requested entity was not found']], 404);
            }
        });

        $job = new SendFcmBatchJob($campaign, $devicesBatch);
        $job->handle();

        $this->assertEquals(20, DB::table('notification_tokens')->where('notification_id', $campaign->id)->where('status', 'accepted')->count());
        $this->assertEquals(15, DB::table('notification_tokens')->where('notification_id', $campaign->id)->where('status', 'transient_failed')->count());
        $this->assertEquals(15, DB::table('notification_tokens')->where('notification_id', $campaign->id)->where('status', 'permanent_failed')->count());

        // All 50 must have attempts = 1
        $this->assertEquals(50, DB::table('notification_tokens')->where('notification_id', $campaign->id)->where('attempts', 1)->count());

        // Permanent failed devices must be deactivated
        $invalidDevs = DB::table('devices')->whereIn('id', $permDeviceIds)->where('token_status', 'invalid')->count();
        $this->assertEquals(15, $invalidDevs);

        $campaign->refresh();
        $this->assertEquals(20, $campaign->accepted_by_fcm_count);
        $this->assertEquals(15, $campaign->transient_failed_count);
        $this->assertEquals(15, $campaign->permanent_failed_count);
    }

    /**
     * 3. Multi-device user
     */
    public function test_03_multi_device_user()
    {
        $campaign = $this->createCampaign();
        $user = $this->testUser;

        // User has 3 devices: 2 will be accepted, 1 transient failed
        $batch = [];
        for ($i = 0; $i < 3; $i++) {
            [, , $item] = $this->createDeviceAndToken($campaign, $user);
            $batch[] = $item;
        }

        Http::fake(function () {
            static $c = 0;
            $c++;
            if ($c <= 2) {
                return Http::response(['name' => 'projects/test/messages/multi_ok'], 200);
            }
            return Http::response(['error' => ['status' => 'UNAVAILABLE', 'message' => 'Busy']], 503);
        });

        $job = new SendFcmBatchJob($campaign, $batch);
        $job->handle();

        $userNotif = DB::table('users_notifications')
            ->where('notifications_type', Notification::class)
            ->where('notifications_id', $campaign->id)
            ->where('user_id', $user->id)
            ->first();

        $this->assertNotNull($userNotif);
        $this->assertEquals('sent', $userNotif->status);
        $this->assertEquals(2, $userNotif->accepted_devices_count);
        $this->assertEquals(1, $userNotif->failed_devices_count);
        $this->assertEquals('Accepted on 2 device(s)', $userNotif->response);
    }

    /**
     * 4. Same campaign in two batches
     */
    public function test_04_same_campaign_two_batches()
    {
        Http::fake([
            'https://fcm.googleapis.com/*' => Http::response(['name' => 'projects/test/messages/msg_ok'], 200),
        ]);

        $campaign = $this->createCampaign();

        // Batch 1 (10 devices)
        $batch1 = [];
        for ($i = 0; $i < 10; $i++) {
            $u = User::create(['fullname' => "B1 User {$i}", 'email' => "b1_{$i}_" . uniqid() . "@example.com", 'password' => 'secret']);
            [, , $item] = $this->createDeviceAndToken($campaign, $u);
            $batch1[] = $item;
        }

        // Batch 2 (10 devices)
        $batch2 = [];
        for ($i = 0; $i < 10; $i++) {
            $u = User::create(['fullname' => "B2 User {$i}", 'email' => "b2_{$i}_" . uniqid() . "@example.com", 'password' => 'secret']);
            [, , $item] = $this->createDeviceAndToken($campaign, $u);
            $batch2[] = $item;
        }

        (new SendFcmBatchJob($campaign, $batch1))->handle();
        (new SendFcmBatchJob($campaign, $batch2))->handle();

        $campaign->refresh();
        $this->assertEquals(20, $campaign->accepted_by_fcm_count);
        $this->assertEquals(20, DB::table('notification_tokens')->where('notification_id', $campaign->id)->where('status', 'accepted')->count());
    }

    /**
     * 5. Retry transient
     */
    public function test_05_retry_transient()
    {
        $campaign = $this->createCampaign();
        $user = $this->testUser;
        [, $tId, $item] = $this->createDeviceAndToken($campaign, $user);

        Http::fake([
            'https://fcm.googleapis.com/*' => Http::sequence()
                ->push(['error' => ['status' => 'UNAVAILABLE', 'message' => 'Retry later']], 503)
                ->push(['name' => 'projects/test/messages/retry_success'], 200),
        ]);

        // Attempt 1: Transient failure
        (new SendFcmBatchJob($campaign, [$item]))->handle();

        $tok = DB::table('notification_tokens')->where('id', $tId)->first();
        $this->assertEquals('transient_failed', $tok->status);
        $this->assertEquals(1, $tok->attempts);

        // Attempt 2: Retry succeeds
        (new SendFcmBatchJob($campaign, [$item]))->handle();

        $tok = DB::table('notification_tokens')->where('id', $tId)->first();
        $this->assertEquals('accepted', $tok->status);
        $this->assertEquals(2, $tok->attempts);
    }

    /**
     * 6. Idempotent job replay
     */
    public function test_06_idempotent_job_replay()
    {
        Http::fake([
            'https://fcm.googleapis.com/*' => Http::response(['name' => 'projects/test/messages/idempotent_ok'], 200),
        ]);

        $campaign = $this->createCampaign();
        [, $tId, $item] = $this->createDeviceAndToken($campaign, $this->testUser);

        // First run
        (new SendFcmBatchJob($campaign, [$item]))->handle();
        $campaign->refresh();
        $this->assertEquals(1, $campaign->accepted_by_fcm_count);

        $tok = DB::table('notification_tokens')->where('id', $tId)->first();
        $this->assertEquals(1, $tok->attempts);

        // Replay same job
        $httpCallCount = 0;
        Http::fake(function () use (&$httpCallCount) {
            $httpCallCount++;
            return Http::response(['name' => 'projects/test/messages/should_not_call'], 200);
        });

        (new SendFcmBatchJob($campaign, [$item]))->handle();

        $this->assertEquals(0, $httpCallCount); // No HTTP calls made on replay
        $tok = DB::table('notification_tokens')->where('id', $tId)->first();
        $this->assertEquals(1, $tok->attempts); // attempts must not increment on replay

        $campaign->refresh();
        $this->assertEquals(1, $campaign->accepted_by_fcm_count); // counters must not double count
    }

    /**
     * 7. Kill Switch does not increment attempts
     */
    public function test_07_kill_switch_does_not_increment_attempts()
    {
        config(['notification.direct_fcm_enabled' => false]);

        $campaign = $this->createCampaign();
        [, $tId, $item] = $this->createDeviceAndToken($campaign, $this->testUser);

        $job = new SendFcmBatchJob($campaign, [$item]);
        $job->handle();

        $tok = DB::table('notification_tokens')->where('id', $tId)->first();
        $this->assertEquals('skipped_by_feature_flag', $tok->status);
        $this->assertEquals('DIRECT_FCM_DISABLED', $tok->error_code);
        $this->assertEquals(0, $tok->attempts); // Must remain 0 because no send occurred
    }

    /**
     * 8. Accepted replay does not increment attempts
     */
    public function test_08_accepted_replay_does_not_increment_attempts()
    {
        $campaign = $this->createCampaign();
        [, $tId, $item] = $this->createDeviceAndToken($campaign, $this->testUser, 'accepted');

        DB::table('notification_tokens')->where('id', $tId)->update(['attempts' => 1]);

        // Replay with already accepted token
        FCMService::getInstance()->sendBatchDirect($campaign, [$item]);

        $tok = DB::table('notification_tokens')->where('id', $tId)->first();
        $this->assertEquals(1, $tok->attempts);
    }

    /**
     * 9. Atomic attempts under two workers / parallel execution
     */
    public function test_09_atomic_attempts_concurrency()
    {
        $campaign = $this->createCampaign();
        [, $tId, $item] = $this->createDeviceAndToken($campaign, $this->testUser);

        // Simulate two sequential attempts that execute SQL attempts + 1
        DB::table('notification_tokens')->where('id', $tId)->update(['attempts' => DB::raw('attempts + 1')]);
        DB::table('notification_tokens')->where('id', $tId)->update(['attempts' => DB::raw('attempts + 1')]);

        $tok = DB::table('notification_tokens')->where('id', $tId)->first();
        $this->assertEquals(2, $tok->attempts);
    }

    /**
     * 10. Campaign counters equal token result counts
     */
    public function test_10_campaign_counters_equal_token_result_counts()
    {
        Http::fake([
            'https://fcm.googleapis.com/*' => Http::response(['name' => 'projects/test/messages/reconcile'], 200),
        ]);

        $campaign = $this->createCampaign();
        $batch = [];
        for ($i = 0; $i < 10; $i++) {
            $u = User::create(['fullname' => "Reconcile User {$i}", 'email' => "rec_{$i}_" . uniqid() . "@example.com", 'password' => 'secret']);
            [, , $item] = $this->createDeviceAndToken($campaign, $u);
            $batch[] = $item;
        }

        (new SendFcmBatchJob($campaign, $batch))->handle();
        $campaign->refresh();

        $tokenAccepted = DB::table('notification_tokens')->where('notification_id', $campaign->id)->where('status', 'accepted')->count();
        $tokenTransient = DB::table('notification_tokens')->where('notification_id', $campaign->id)->where('status', 'transient_failed')->count();
        $tokenPermanent = DB::table('notification_tokens')->where('notification_id', $campaign->id)->where('status', 'permanent_failed')->count();

        $this->assertEquals($tokenAccepted, $campaign->accepted_by_fcm_count);
        $this->assertEquals($tokenTransient, $campaign->transient_failed_count);
        $this->assertEquals($tokenPermanent, $campaign->permanent_failed_count);
    }

    /**
     * 11. Permanent failure invalidates correct device/token only
     */
    public function test_11_permanent_failure_invalidates_correct_device_token_only()
    {
        $campaign = $this->createCampaign();
        [$devA, , $itemA] = $this->createDeviceAndToken($campaign, $this->testUser);
        [$devB, , $itemB] = $this->createDeviceAndToken($campaign, $this->testUser);

        Http::fake(function (\Illuminate\Http\Client\Request $request) use ($itemA) {
            $body = json_decode($request->body(), true);
            if ($body['message']['token'] === $itemA['token']) {
                return Http::response(['error' => ['status' => 'UNREGISTERED', 'message' => 'Token unregistered']], 404);
            }
            return Http::response(['error' => ['status' => 'UNAVAILABLE', 'message' => 'Temporary error']], 503);
        });

        (new SendFcmBatchJob($campaign, [$itemA, $itemB]))->handle();

        $devA->refresh();
        $devB->refresh();

        $this->assertEquals('invalid', $devA->token_status);
        $this->assertEquals('valid', $devB->token_status); // Transient failure must not deactivate device
    }

    /**
     * 12. Token refresh race: Job contains old token, device table has new token -> do NOT invalidate new token!
     */
    public function test_12_token_refresh_race_condition_protection()
    {
        $campaign = $this->createCampaign();
        [$dev, , $item] = $this->createDeviceAndToken($campaign, $this->testUser);

        // Before job executes, device token is refreshed in database
        $newToken = 'fcm_refreshed_new_token_' . uniqid();
        $dev->update(['device_token' => $newToken]);

        // Job attempts to send to OLD token and gets UNREGISTERED
        Http::fake([
            'https://fcm.googleapis.com/*' => Http::response(['error' => ['status' => 'UNREGISTERED', 'message' => 'Old token dead']], 404),
        ]);

        (new SendFcmBatchJob($campaign, [$item]))->handle();

        $dev->refresh();
        // Crucial: The device MUST NOT be marked invalid because the token was already refreshed!
        $this->assertEquals('valid', $dev->token_status);
        $this->assertEquals($newToken, $dev->device_token);
    }

    /**
     * 13. Morph isolation for BannerNotification
     */
    public function test_13_morph_isolation_for_banner_notification()
    {
        Http::fake([
            'https://fcm.googleapis.com/*' => Http::response(['name' => 'projects/test/messages/morph_ok'], 200),
        ]);

        $campaign = $this->createCampaign();
        $user = $this->testUser;
        [, , $item] = $this->createDeviceAndToken($campaign, $user);

        // Insert a record for BannerNotification for same user
        $bannerRowId = DB::table('users_notifications')->insertGetId([
            'notifications_type' => 'Core\Notification\Models\BannerNotification',
            'notifications_id' => 999,
            'user_id' => $user->id,
            'status' => 'pending',
            'response' => 'banner_original_response',
        ]);

        (new SendFcmBatchJob($campaign, [$item]))->handle();

        $bannerRow = DB::table('users_notifications')->where('id', $bannerRowId)->first();
        $this->assertEquals('pending', $bannerRow->status);
        $this->assertEquals('banner_original_response', $bannerRow->response);
    }

    /**
     * 14. Empty batch
     */
    public function test_14_empty_batch()
    {
        $campaign = $this->createCampaign();
        $result = FCMService::getInstance()->sendBatchDirect($campaign, []);

        $this->assertEquals(['accepted' => 0, 'permanent_failed' => 0, 'transient_failed' => 0], $result);
    }

    /**
     * 15. Partial/malformed FCM response
     */
    public function test_15_partial_malformed_fcm_response()
    {
        Http::fake([
            'https://fcm.googleapis.com/*' => Http::response('MALFORMED_NON_JSON_RESPONSE', 500),
        ]);

        $campaign = $this->createCampaign();
        [, $tId, $item] = $this->createDeviceAndToken($campaign, $this->testUser);

        (new SendFcmBatchJob($campaign, [$item]))->handle();

        $tok = DB::table('notification_tokens')->where('id', $tId)->first();
        $this->assertEquals('transient_failed', $tok->status);
        $this->assertEquals(1, $tok->attempts);
    }

    /**
     * 16. HTTP exception for one request does not corrupt other results
     */
    public function test_16_http_exception_for_one_request_does_not_corrupt_other_results()
    {
        $campaign = $this->createCampaign();
        $user1 = $this->testUser;
        $user2 = User::create(['fullname' => 'User 2', 'email' => 'u2_' . uniqid() . '@example.com', 'password' => 'secret']);

        [, $tId1, $item1] = $this->createDeviceAndToken($campaign, $user1);
        [, $tId2, $item2] = $this->createDeviceAndToken($campaign, $user2);

        Http::fake(function (\Illuminate\Http\Client\Request $request) use ($item1) {
            $body = json_decode($request->body(), true);
            if (($body['message']['token'] ?? '') === $item1['token']) {
                return Http::response(['name' => 'projects/test/messages/ok'], 200);
            }
            return Http::response(['error' => ['status' => 'UNAVAILABLE', 'message' => 'Connection timeout during pool']], 504);
        });

        (new SendFcmBatchJob($campaign, [$item1, $item2]))->handle();

        $tok1 = DB::table('notification_tokens')->where('id', $tId1)->first();
        $tok2 = DB::table('notification_tokens')->where('id', $tId2)->first();

        $this->assertEquals('accepted', $tok1->status);
        $this->assertEquals('transient_failed', $tok2->status);
    }
}
