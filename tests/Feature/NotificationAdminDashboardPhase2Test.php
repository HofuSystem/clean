<?php

namespace Tests\Feature;

use Core\Notification\DataResources\NotificationsResource;
use Core\Notification\Helpers\NotificationDataNormalizer;
use Core\Notification\Jobs\SendFcmBatchJob;
use Core\Notification\Models\BannerNotification;
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
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class NotificationAdminDashboardPhase2Test extends TestCase
{
    use DatabaseTransactions;

    protected User $adminUser;
    protected User $clientUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Disable locale redirects so test routes hit controllers directly
        $this->withoutMiddleware([
            \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter::class,
            \Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect::class,
        ]);

        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'client', 'guard_name' => 'web']);

        $this->adminUser = User::create([
            'fullname' => 'Admin Test User',
            'email' => 'admin-' . uniqid() . '@example.com',
            'phone' => '9665' . str_pad((string)mt_rand(1, 99999999), 8, '0', STR_PAD_LEFT),
            'password' => bcrypt('secret123'),
            'is_active' => 1,
        ]);
        $this->adminUser->assignRole('admin');

        $this->clientUser = User::create([
            'fullname' => 'Client Test User',
            'email' => 'client-' . uniqid() . '@example.com',
            'phone' => '9665' . str_pad((string)mt_rand(1, 99999999), 8, '0', STR_PAD_LEFT),
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
            'title' => 'Test Campaign',
            'body' => 'Test Notification Body',
            'for' => 'all',
            'types' => json_encode(['apps']),
            'purpose' => 'marketing',
            'processing_status' => 'completed',
        ], $attributes));
    }

    protected function createToken(array $attributes = []): NotificationToken
    {
        return NotificationToken::create(array_merge([
            'topic' => 'all',
            'token' => 'tok_' . uniqid(),
            'status' => 'accepted',
        ], $attributes));
    }

    public function test_campaign_details_loads_for_direct_fcm_with_correct_metrics()
    {
        $notification = $this->createCampaign([
            'title' => 'Direct Campaign Promo',
            'body' => 'Exclusive Direct FCM Push',
            'purpose' => 'marketing',
            'targeted_users_count' => 500,
            'eligible_users_count' => 300,
            'eligible_devices_count' => 320,
            'accepted_by_fcm_count' => 280,
            'permanent_failed_count' => 15,
            'transient_failed_count' => 25,
            'received_count' => 150,
            'opened_count' => 45,
            'payload' => json_encode(['delivery_channel' => 'direct_fcm']),
        ]);

        $response = $this->actingAs($this->adminUser)->get(route('dashboard.notifications.show', $notification->id));

        $response->assertStatus(200);
        $response->assertSee('Direct FCM');
        $response->assertSee('قُبل من FCM');
        $response->assertSee('280');
        $response->assertSee('Direct Campaign Promo');
    }

    public function test_campaign_details_loads_for_legacy_topic_with_correct_semantics()
    {
        $notification = $this->createCampaign([
            'title' => 'Legacy Campaign Announcement',
            'body' => 'Sent to broadcast topic',
            'purpose' => 'system',
            'sent_count' => 1500,
            'payload' => json_encode(['delivery_channel' => 'legacy_topic']),
        ]);

        $response = $this->actingAs($this->adminUser)->get(route('dashboard.notifications.show', $notification->id));

        $response->assertStatus(200);
        $response->assertSee('Legacy Topic');
        $response->assertSee('Legacy Topic Subscription');
        // Engagement should be explicitly unmeasurable
        $response->assertSee('غير متاح');
    }

    public function test_polymorphic_banner_notifications_are_strictly_isolated()
    {
        $notification = $this->createCampaign([
            'title' => 'Campaign For Isolation',
            'for' => 'users',
        ]);

        $banner = BannerNotification::create([
            'publish_date' => now(),
            'next_vision_hour' => 1,
            'status' => 'active',
        ]);

        // Create UsersNotification for regular notification
        $campaignUserNotif = UsersNotification::create([
            'notifications_type' => Notification::class,
            'notifications_id' => $notification->id,
            'user_id' => $this->clientUser->id,
            'eligibility_status' => 'eligible',
            'eligibility_reason' => 'User is active and allowed',
            'accepted_devices_count' => 1,
            'failed_devices_count' => 0,
        ]);

        // Create UsersNotification for BannerNotification
        $bannerUserNotif = UsersNotification::create([
            'notifications_type' => BannerNotification::class,
            'notifications_id' => $banner->id,
            'user_id' => $this->clientUser->id,
            'eligibility_status' => 'eligible',
            'eligibility_reason' => 'Banner displayed on home feed',
            'accepted_devices_count' => 0,
            'failed_devices_count' => 0,
        ]);

        // Fetch eligibility users for the notification
        $response = $this->actingAs($this->adminUser)->postJson(
            route('dashboard.notifications.getEligibilityUsers', $notification->id)
        );

        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals($campaignUserNotif->id, $data[0]['id']);
        $this->assertEquals('User is active and allowed', $data[0]['eligibility_reason']);
        // Assert banner user notification is never included
        $this->assertNotEquals($bannerUserNotif->id, $data[0]['id']);
    }

    public function test_users_eligibility_datatable_filters_and_search()
    {
        $notification = $this->createCampaign([
            'title' => 'Filter Test Campaign',
            'for' => 'users',
        ]);

        $userEligible = User::create([
            'fullname' => 'Eligible User UniqueName',
            'email' => 'elig-' . uniqid() . '@example.com',
            'phone' => '96651111111',
            'password' => bcrypt('123'),
            'is_active' => 1,
        ]);
        UsersNotification::create([
            'notifications_type' => Notification::class,
            'notifications_id' => $notification->id,
            'user_id' => $userEligible->id,
            'eligibility_status' => 'eligible',
        ]);

        $userBlocked = User::create([
            'fullname' => 'Blocked Marketing User',
            'email' => 'block-' . uniqid() . '@example.com',
            'phone' => '96652222222',
            'password' => bcrypt('123'),
            'is_active' => 1,
        ]);
        UsersNotification::create([
            'notifications_type' => Notification::class,
            'notifications_id' => $notification->id,
            'user_id' => $userBlocked->id,
            'eligibility_status' => 'marketing_disabled',
            'eligibility_reason' => 'User opted out',
        ]);

        // Filter by marketing_disabled
        $response = $this->actingAs($this->adminUser)->postJson(
            route('dashboard.notifications.getEligibilityUsers', $notification->id),
            ['filter_status' => 'marketing_disabled']
        );
        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals($userBlocked->id, $data[0]['user_id']);

        // Search by user fullname
        $searchResponse = $this->actingAs($this->adminUser)->postJson(
            route('dashboard.notifications.getEligibilityUsers', $notification->id),
            ['search' => 'UniqueName']
        );
        $searchResponse->assertStatus(200);
        $searchData = $searchResponse->json('data');
        $this->assertCount(1, $searchData);
        $this->assertEquals($userEligible->id, $searchData[0]['user_id']);
    }

    public function test_device_results_datatable_filters_and_token_masking()
    {
        $notification = $this->createCampaign([
            'title' => 'Device Token Masking Test',
        ]);

        $rawSecretToken = 'secret_fcm_device_token_xyz987654';

        $device = Device::create([
            'user_id' => $this->clientUser->id,
            'device_token' => $rawSecretToken,
            'type' => 'android',
            'app_context' => 'client',
            'app_version' => '2.4.0',
            'token_status' => 'active',
        ]);

        $this->createToken([
            'notification_id' => $notification->id,
            'user_id' => $this->clientUser->id,
            'device_id' => $device->id,
            'platform' => 'android',
            'token' => $rawSecretToken,
            'status' => 'accepted',
            'accepted_at' => now(),
        ]);

        $response = $this->actingAs($this->adminUser)->postJson(
            route('dashboard.notifications.getDeviceResults', $notification->id)
        );

        $response->assertStatus(200);
        $content = $response->getContent();

        // STRICT ASSERTION: Raw token MUST NOT be exposed in JSON response!
        $this->assertStringNotContainsString($rawSecretToken, $content);

        // Masked token MUST be exposed (*** + last 6 characters)
        $expectedMask = '***' . substr($rawSecretToken, -6);
        $this->assertStringContainsString($expectedMask, $content);
    }

    public function test_audience_preview_is_strictly_read_only_and_matches_service()
    {
        Http::fake();
        Queue::fake([SendFcmBatchJob::class]);

        $initialNotificationsCount = Notification::count();
        $initialUserNotifsCount = UsersNotification::count();
        $initialTokensCount = NotificationToken::count();

        // Create active client with device
        $userWithDevice = User::create([
            'fullname' => 'Client With Device',
            'email' => 'withdev-' . uniqid() . '@example.com',
            'phone' => '96653333333',
            'password' => bcrypt('123'),
            'is_active' => 1,
            'is_allow_notify' => 1,
            'is_allow_notify_confirmed_at' => now(),
        ]);
        $userWithDevice->assignRole('client');

        Device::create([
            'user_id' => $userWithDevice->id,
            'device_token' => 'token_preview_' . uniqid(),
            'type' => 'ios',
            'token_status' => 'active',
            'notification_permission' => 'authorized',
        ]);

        $response = $this->actingAs($this->adminUser)->postJson(
            route('dashboard.notifications.previewAudience'),
            [
                'purpose' => 'marketing',
                'for' => 'all',
            ]
        );

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'message',
            'data' => [
                'total_matching_users',
                'active_users',
                'eligible_users',
                'eligible_devices',
                'breakdown' => [
                    'no_device',
                    'no_valid_token',
                    'marketing_disabled',
                    'permission_denied',
                    'legacy_unknown',
                    'inactive',
                ],
                'platforms',
                'device_ratio',
            ]
        ]);

        // Verify strictly read-only: NO DB rows created
        $this->assertEquals($initialNotificationsCount, Notification::count());
        $this->assertEquals($initialUserNotifsCount, UsersNotification::count());
        $this->assertEquals($initialTokensCount, NotificationToken::count());

        // Verify NO jobs queued and NO external calls made
        Queue::assertNothingPushed();
        Http::assertNothingSent();
    }

    public function test_audience_preview_generates_warning_when_eligible_devices_ratio_is_low()
    {
        // Query users for a phone filter that matches no devices
        $response = $this->actingAs($this->adminUser)->postJson(
            route('dashboard.notifications.previewAudience'),
            [
                'purpose' => 'marketing',
                'for' => 'phone',
                'for_data' => '9999999999999',
            ]
        );

        $response->assertStatus(200);
        $this->assertNotNull($response->json('data.warning'));
    }

    public function test_queue_monitor_displays_only_notification_campaigns()
    {
        $notification = $this->createCampaign([
            'title' => 'Monitored Campaign',
            'processing_status' => 'processing',
        ]);

        $this->createToken([
            'notification_id' => $notification->id,
            'token' => 'mon_token_' . uniqid(),
            'status' => 'queued',
            'queued_at' => now(),
        ]);

        $response = $this->actingAs($this->adminUser)->get(route('dashboard.notifications.queueMonitor'));

        $response->assertStatus(200);
        $response->assertSee('Monitored Campaign');
        $response->assertSee('في الطابور');
        $response->assertSee('مراقبة دفعات حملات الإشعارات');
    }

    public function test_retry_transient_retries_only_transient_failed_devices()
    {
        config(['notification.direct_fcm_enabled' => true]);

        $notification = $this->createCampaign([
            'title' => 'Retry Test Campaign',
            'payload' => json_encode(['delivery_channel' => 'direct_fcm']),
        ]);

        // 1 accepted token
        $acceptedToken = $this->createToken([
            'notification_id' => $notification->id,
            'user_id' => $this->clientUser->id,
            'token' => 'tok_accepted_' . uniqid(),
            'status' => 'accepted',
            'accepted_at' => now(),
        ]);

        // 1 permanent failed token
        $permToken = $this->createToken([
            'notification_id' => $notification->id,
            'user_id' => $this->clientUser->id,
            'token' => 'tok_permanent_' . uniqid(),
            'status' => 'permanent_failed',
            'failed_at' => now(),
        ]);

        // 2 transient failed tokens
        $transToken1 = $this->createToken([
            'notification_id' => $notification->id,
            'user_id' => $this->clientUser->id,
            'token' => 'tok_trans_1_' . uniqid(),
            'status' => 'transient_failed',
            'failed_at' => now(),
        ]);

        $transToken2 = $this->createToken([
            'notification_id' => $notification->id,
            'user_id' => $this->clientUser->id,
            'token' => 'tok_trans_2_' . uniqid(),
            'status' => 'transient_failed',
            'failed_at' => now(),
        ]);

        Queue::fake([SendFcmBatchJob::class]);

        $response = $this->actingAs($this->adminUser)->postJson(
            route('dashboard.notifications.retryTransient', $notification->id)
        );

        $response->assertStatus(200);

        // Accepted and permanent tokens must NOT be touched
        $this->assertEquals('accepted', $acceptedToken->fresh()->status);
        $this->assertEquals('permanent_failed', $permToken->fresh()->status);

        // Transient tokens must be set to queued
        $this->assertEquals('queued', $transToken1->fresh()->status);
        $this->assertEquals('queued', $transToken2->fresh()->status);

        // Batch job dispatched
        Queue::assertPushed(SendFcmBatchJob::class, 1);
    }

    public function test_retry_transient_fails_safely_when_direct_fcm_is_disabled()
    {
        config(['notification.direct_fcm_enabled' => false]);

        $notification = $this->createCampaign([
            'title' => 'Disabled Flag Campaign',
        ]);

        $this->createToken([
            'notification_id' => $notification->id,
            'token' => 'tok_' . uniqid(),
            'status' => 'transient_failed',
        ]);

        Queue::fake([SendFcmBatchJob::class]);

        $response = $this->actingAs($this->adminUser)->postJson(
            route('dashboard.notifications.retryTransient', $notification->id)
        );

        $response->assertStatus(422);
        Queue::assertNothingPushed();
    }

    public function test_retry_transient_is_idempotent_and_prevents_duplicate_sending()
    {
        config(['notification.direct_fcm_enabled' => true]);

        $notification = $this->createCampaign([
            'title' => 'Idempotent Retry Campaign',
        ]);

        // All tokens already accepted
        $this->createToken([
            'notification_id' => $notification->id,
            'token' => 'tok_acc_' . uniqid(),
            'status' => 'accepted',
        ]);

        $response = $this->actingAs($this->adminUser)->postJson(
            route('dashboard.notifications.retryTransient', $notification->id)
        );

        $response->assertStatus(422);
        $this->assertStringContainsString('No transient failed devices found', $response->json('message'));
    }

    public function test_detailed_exports_stream_csv_with_masked_tokens_and_filters()
    {
        $notification = $this->createCampaign([
            'title' => 'Export Test Campaign',
        ]);

        $secretToken = 'unmasked_private_token_999888777';

        $this->createToken([
            'notification_id' => $notification->id,
            'user_id' => $this->clientUser->id,
            'token' => $secretToken,
            'status' => 'accepted',
        ]);

        UsersNotification::create([
            'notifications_type' => Notification::class,
            'notifications_id' => $notification->id,
            'user_id' => $this->clientUser->id,
            'eligibility_status' => 'eligible',
        ]);

        // 1. Eligibility Export
        $eligResponse = $this->actingAs($this->adminUser)->get(
            route('dashboard.notifications.exportDetails', [$notification->id, 'eligibility'])
        );
        $eligResponse->assertStatus(200);
        $this->assertStringContainsString('text/csv', $eligResponse->headers->get('Content-Type'));

        // 2. Devices Export
        $devResponse = $this->actingAs($this->adminUser)->get(
            route('dashboard.notifications.exportDetails', [$notification->id, 'devices'])
        );
        $devResponse->assertStatus(200);
        $csvContent = $devResponse->streamedContent();

        // STRICT ASSERTION: Raw token MUST NOT appear in CSV
        $this->assertStringNotContainsString($secretToken, $csvContent);
        // Masked token MUST appear
        $this->assertStringContainsString('***' . substr($secretToken, -6), $csvContent);
    }

    public function test_authorization_and_idor_protection()
    {
        $notification = $this->createCampaign([
            'title' => 'Protected Campaign',
        ]);

        // Unauthenticated request redirects or returns 401
        $guestResponse = $this->get(route('dashboard.notifications.show', $notification->id));
        $this->assertTrue(in_array($guestResponse->status(), [302, 401]));

        // Unauthorized client user gets 403
        $clientResponse = $this->actingAs($this->clientUser)->get(route('dashboard.notifications.show', $notification->id));
        $this->assertEquals(403, $clientResponse->status());

        // Non-existent ID returns 404
        $notFoundResponse = $this->actingAs($this->adminUser)->get(route('dashboard.notifications.show', 9999999));
        $notFoundResponse->assertStatus(404);
    }

    public function test_query_budget_and_no_n_plus_one()
    {
        $notification = $this->createCampaign([
            'title' => 'Budget Test Campaign',
        ]);

        // Create 20 records
        for ($i = 0; $i < 20; $i++) {
            $u = User::create([
                'fullname' => "User Batch {$i}",
                'email' => "batch-{$i}-" . uniqid() . '@example.com',
                'phone' => '9665' . str_pad((string)mt_rand(1, 99999999), 8, '0', STR_PAD_LEFT),
                'password' => bcrypt('123'),
                'is_active' => 1,
            ]);

            UsersNotification::create([
                'notifications_type' => Notification::class,
                'notifications_id' => $notification->id,
                'user_id' => $u->id,
                'eligibility_status' => 'eligible',
            ]);

            $this->createToken([
                'notification_id' => $notification->id,
                'user_id' => $u->id,
                'token' => "tok_batch_{$i}_" . uniqid(),
                'status' => 'accepted',
            ]);
        }

        // Measure query count for eligibility datatable
        DB::enableQueryLog();
        DB::flushQueryLog();

        $this->actingAs($this->adminUser)->postJson(
            route('dashboard.notifications.getEligibilityUsers', $notification->id),
            ['start' => 0, 'length' => 10]
        );

        $eligQueries = DB::getQueryLog();
        // Budget: count query + data query + eager load users <= 6 queries
        $this->assertLessThanOrEqual(6, count($eligQueries), 'Eligibility datatable exceeded query budget!');

        // Measure query count for devices datatable
        DB::flushQueryLog();

        $this->actingAs($this->adminUser)->postJson(
            route('dashboard.notifications.getDeviceResults', $notification->id),
            ['start' => 0, 'length' => 10]
        );

        $deviceQueries = DB::getQueryLog();
        // Budget: count query + data query + eager load users + eager load devices <= 6 queries
        $this->assertLessThanOrEqual(6, count($deviceQueries), 'Device datatable exceeded query budget!');
    }
}
