<?php

namespace Tests\Feature;

use Core\Notification\Jobs\SendFcmBatchJob;
use Core\Notification\Models\Notification;
use Core\Notification\Models\NotificationToken;
use Core\Notification\Services\FCMService;
use Core\Users\Models\Device;
use Core\Users\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class StagingKillSwitchAndFailClosedTest extends TestCase
{
    use DatabaseTransactions;

    protected User $testUser;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'cache.default' => 'array',
        ]);

        $this->testUser = User::create([
            'fullname' => 'Kill Switch Drill User',
            'email' => 'ks-drill-' . uniqid() . '@example.com',
            'phone' => '9665' . rand(1000000, 9999999),
            'password' => bcrypt('secret'),
            'is_active' => 1,
            'is_allow_notify' => 1,
            'is_allow_notify_confirmed_at' => now(),
        ]);
    }

    protected function createCampaign(): Notification
    {
        return Notification::create([
            'title' => 'Kill Switch Drill Campaign',
            'body' => 'Drill Body',
            'for' => 'phone',
            'for_data' => $this->testUser->phone,
            'types' => json_encode(['apps']),
            'purpose' => 'marketing',
            'processing_status' => 'processing',
            'payload' => json_encode(['delivery_channel' => 'direct_fcm']),
        ]);
    }

    /**
     * 1. Controlled Kill Switch Transition:
     * - Campaign created while flag true, tokens queued.
     * - Flag flipped to false before job handle.
     * - Worker executes job.
     * - Zero HTTP calls, all tokens marked skipped_by_feature_flag.
     */
    public function test_01_kill_switch_transitions_queued_tokens_to_skipped_without_http_calls()
    {
        // Step 1: Start with Direct FCM = true
        config(['notification.direct_fcm_enabled' => true]);

        $campaign = $this->createCampaign();
        $tokensCount = 5;
        $devicesBatch = [];
        $tokenRowIds = [];

        for ($i = 0; $i < $tokensCount; $i++) {
            $dev = Device::create([
                'user_id' => $this->testUser->id,
                'device_token' => 'fcm_ks_test_tok_' . $i . '_' . uniqid(),
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

            $tokenRowIds[] = $tok->id;
            $devicesBatch[] = [
                'notification_token_id' => $tok->id,
                'device_id' => $dev->id,
                'user_id' => $this->testUser->id,
                'token' => $dev->device_token,
                'platform' => 'android',
                'app_context' => 'client',
            ];
        }

        // Verify pre-execution state: all are queued
        $queuedBefore = NotificationToken::where('notification_id', $campaign->id)
            ->where('status', 'queued')
            ->count();
        $this->assertEquals($tokensCount, $queuedBefore);

        // Step 2: Trigger Kill Switch BEFORE execution
        config(['notification.direct_fcm_enabled' => false]);

        $httpCallsCount = 0;
        Http::fake(function () use (&$httpCallsCount) {
            $httpCallsCount++;
            return Http::response(['name' => 'fcm_forbidden_response'], 200);
        });

        // Step 3: Execute Job
        $job = new SendFcmBatchJob($campaign, $devicesBatch);
        $job->handle(app(FCMService::class));

        // Step 4: Verify Post-execution expectations
        // 1. Zero HTTP calls to Google FCM
        $this->assertEquals(0, $httpCallsCount, 'No HTTP requests should be made to FCM when kill switch is active');

        // 2. Queued count for batch becomes 0
        $queuedAfter = NotificationToken::where('notification_id', $campaign->id)
            ->where('status', 'queued')
            ->count();
        $this->assertEquals(0, $queuedAfter, 'No tokens must remain in queued status');

        // 3. All tokens marked skipped_by_feature_flag with error_code DIRECT_FCM_DISABLED
        $skippedCount = NotificationToken::where('notification_id', $campaign->id)
            ->where('status', 'skipped_by_feature_flag')
            ->where('error_code', 'DIRECT_FCM_DISABLED')
            ->count();
        $this->assertEquals($tokensCount, $skippedCount, 'All tokens must be recorded as skipped_by_feature_flag');

        // 4. Accepted and Failed counts must be 0
        $acceptedCount = NotificationToken::where('notification_id', $campaign->id)->where('status', 'accepted')->count();
        $failedCount = NotificationToken::where('notification_id', $campaign->id)->whereIn('status', ['permanent_failed', 'transient_failed'])->count();
        $this->assertEquals(0, $acceptedCount);
        $this->assertEquals(0, $failedCount);

        // 5. Job execution status reflects skipped
        $this->assertEquals('skipped_by_feature_flag', $job->executionStatus);
    }

    /**
     * 2. Fail-Closed Drill:
     * - Job with corrupted batch (no notification_token_id, no device_id, no token).
     * - Verifies 0 rows updated in DB (does not corrupt or wipe other records).
     * - Error log logged with explicit warning.
     */
    public function test_02_fail_closed_prevents_broad_update_and_logs_error()
    {
        config(['notification.direct_fcm_enabled' => false]);

        $campaign = $this->createCampaign();

        // Create an unrelated queued token that should NOT be modified
        $otherDev = Device::create([
            'user_id' => $this->testUser->id,
            'device_token' => 'unrelated_token_' . uniqid(),
            'type' => 'android',
        ]);
        $unrelatedToken = NotificationToken::create([
            'notification_id' => $campaign->id,
            'device_id' => $otherDev->id,
            'user_id' => $this->testUser->id,
            'token' => $otherDev->device_token,
            'platform' => 'android',
            'topic' => 'direct',
            'status' => 'queued',
        ]);

        // Malformed devices batch with NO valid identifiers
        $malformedBatch = [
            ['notification_token_id' => null, 'device_id' => null, 'token' => null],
            ['notification_token_id' => null, 'device_id' => null, 'token' => ''],
        ];

        $logLogged = false;
        Log::shouldReceive('error')
            ->once()
            ->withArgs(function ($message) use ($campaign) {
                return str_contains($message, 'Kill switch fail-closed activated for notification ' . $campaign->id)
                    && str_contains($message, 'Zero rows updated');
            });

        $job = new SendFcmBatchJob($campaign, $malformedBatch);
        $job->handle(app(FCMService::class));

        // The unrelated token must remain unaffected (0 rows updated by fail-closed)
        $unrelatedToken->refresh();
        $this->assertEquals('queued', $unrelatedToken->status, 'Fail-closed must not perform destructive broad updates');
    }
}
