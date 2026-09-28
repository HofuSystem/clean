<?php

namespace Tests\Feature;

use Core\Notification\Helpers\NotificationDataNormalizer;
use Core\Notification\Helpers\NotificationsManger;
use Core\Notification\Jobs\SendFcmBatchJob;
use Core\Notification\DataResources\NotificationsResource;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Core\Notification\Models\BannerNotification;
use Core\Notification\Models\Notification;
use Core\Notification\Models\NotificationToken;
use Core\Notification\Services\RecipientEligibilityService;
use Core\Users\Models\Device;
use Core\Users\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class NotificationRemediationPhase1Test extends TestCase
{
    use DatabaseTransactions;

    protected User $clientUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Ensure roles exist
        Role::firstOrCreate(['name' => 'client', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'driver', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'technical', 'guard_name' => 'web']);

        $this->clientUser = User::create([
            'fullname' => 'Test Client User',
            'email' => 'client-' . uniqid() . '@example.com',
            'phone' => '9665' . rand(1000000, 9999999),
            'password' => bcrypt('secret123'),
            'is_active' => 1,
            'is_allow_notify' => 1,
        ]);
        $this->clientUser->assignRole('client');
    }

    public function test_device_sync_accepts_type_as_alias_for_platform()
    {
        $response = $this->actingAs($this->clientUser, 'sanctum')->postJson('/api/devices/sync', [
            'device_token' => 'token_type_alias_' . uniqid(),
            'type' => 'android', // sending 'type' instead of 'platform'
            'installation_id' => 'inst_' . uniqid(),
            'app_context' => 'client',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('data.platform', 'android');

        $this->assertDatabaseHas('devices', [
            'user_id' => $this->clientUser->id,
            'type' => 'android',
        ]);
    }

    public function test_device_sync_accepts_platform_directly()
    {
        $response = $this->actingAs($this->clientUser, 'sanctum')->postJson('/api/devices/sync', [
            'device_token' => 'token_platform_direct_' . uniqid(),
            'platform' => 'ios',
            'installation_id' => 'inst_' . uniqid(),
            'app_context' => 'client',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('data.platform', 'ios');

        $this->assertDatabaseHas('devices', [
            'user_id' => $this->clientUser->id,
            'type' => 'ios',
        ]);
    }

    public function test_device_sync_normalizes_granted_to_authorized()
    {
        $response = $this->actingAs($this->clientUser, 'sanctum')->postJson('/api/devices/sync', [
            'device_token' => 'token_granted_' . uniqid(),
            'platform' => 'ios',
            'notification_permission' => 'granted', // alias for 'authorized'
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('data.notification_permission', 'authorized');

        $this->assertDatabaseHas('devices', [
            'user_id' => $this->clientUser->id,
            'notification_permission' => 'authorized',
        ]);
    }

    public function test_device_sync_normalizes_technician_to_technical()
    {
        $response = $this->actingAs($this->clientUser, 'sanctum')->postJson('/api/devices/sync', [
            'device_token' => 'token_tech_' . uniqid(),
            'platform' => 'android',
            'app_context' => 'technician', // alias for 'technical'
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('data.app_context', 'technical');

        $this->assertDatabaseHas('devices', [
            'user_id' => $this->clientUser->id,
            'app_context' => 'technical',
        ]);
    }

    public function test_device_sync_restores_and_rebinds_soft_deleted_device()
    {
        $token = 'token_restore_' . uniqid();
        $inst = 'inst_restore_' . uniqid();

        $device = Device::create([
            'user_id' => $this->clientUser->id,
            'device_token' => $token,
            'installation_id' => $inst,
            'type' => 'ios',
            'token_status' => 'active',
        ]);

        // Soft delete device
        $device->delete();
        $this->assertSoftDeleted('devices', ['id' => $device->id]);

        // Sync with same token
        $response = $this->actingAs($this->clientUser, 'sanctum')->postJson('/api/devices/sync', [
            'device_token' => $token,
            'platform' => 'ios',
            'installation_id' => $inst,
            'app_version' => '5.0.0',
        ]);

        $response->assertStatus(200);
        $refreshed = Device::find($device->id);
        $this->assertNotNull($refreshed, 'Device should be restored from soft deletion');
        $this->assertEquals('5.0.0', $refreshed->app_version);
        $this->assertFalse($refreshed->trashed());
    }

    public function test_device_sync_handles_legacy_device_without_installation_id()
    {
        $token = 'token_legacy_' . uniqid();

        $response = $this->actingAs($this->clientUser, 'sanctum')->postJson('/api/devices/sync', [
            'device_token' => $token,
            'platform' => 'android',
            // installation_id omitted
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('devices', [
            'user_id' => $this->clientUser->id,
            'device_token' => $token,
            'installation_id' => null,
        ]);
    }

    public function test_notification_events_rejects_when_feature_flag_is_disabled()
    {
        config(['notification.delivery_events_enabled' => false]);

        $response = $this->actingAs($this->clientUser, 'sanctum')->postJson('/api/client/notifications/events', [
            'notification_id' => 1,
            'event_type' => 'received',
            'installation_id' => 'inst_any',
        ]);

        $response->assertStatus(403);
    }

    public function test_notification_events_allows_owner_and_records_idempotently()
    {
        config(['notification.delivery_events_enabled' => true]);

        $inst = 'inst_events_' . uniqid();
        $token = 'token_events_' . uniqid();

        $device = Device::create([
            'user_id' => $this->clientUser->id,
            'device_token' => $token,
            'installation_id' => $inst,
            'type' => 'ios',
            'token_status' => 'active',
        ]);

        $notification = Notification::create([
            'title' => 'Test Notification',
            'body' => 'Test Body',
            'for' => 'all',
            'purpose' => 'marketing',
            'types' => json_encode(['apps']),
            'received_count' => 0,
            'opened_count' => 0,
        ]);

        $tokenRecord = NotificationToken::create([
            'topic' => 'direct',
            'token' => $token,
            'status' => 'accepted',
            'title' => 'Test Notification',
            'notification_id' => $notification->id,
            'user_id' => $this->clientUser->id,
            'device_id' => $device->id,
            'installation_id' => $inst,
            'platform' => 'ios',
            'attempts' => 1,
            'accepted_at' => now(),
        ]);

        // 1. Record received event
        $res1 = $this->actingAs($this->clientUser, 'sanctum')->postJson('/api/client/notifications/events', [
            'notification_id' => $notification->id,
            'event_type' => 'received',
            'installation_id' => $inst,
        ]);
        $res1->assertStatus(200);

        $notification->refresh();
        $tokenRecord->refresh();
        $this->assertEquals(1, $notification->received_count);
        $this->assertNotNull($tokenRecord->received_at);

        // 2. Duplicate received call must NOT increment counter twice
        $resDuplicate = $this->actingAs($this->clientUser, 'sanctum')->postJson('/api/client/notifications/events', [
            'notification_id' => $notification->id,
            'event_type' => 'received',
            'installation_id' => $inst,
        ]);
        $resDuplicate->assertStatus(200);
        $notification->refresh();
        $this->assertEquals(1, $notification->received_count, 'Duplicate received event must not increment counter twice');

        // 3. Record opened event
        $resOpened = $this->actingAs($this->clientUser, 'sanctum')->postJson('/api/client/notifications/events', [
            'notification_id' => $notification->id,
            'event_type' => 'opened',
            'installation_id' => $inst,
        ]);
        $resOpened->assertStatus(200);

        $notification->refresh();
        $tokenRecord->refresh();
        $this->assertEquals(1, $notification->opened_count);
        $this->assertNotNull($tokenRecord->opened_at);

        // 4. Duplicate opened call must NOT increment opened_count twice
        $resOpenedDup = $this->actingAs($this->clientUser, 'sanctum')->postJson('/api/client/notifications/events', [
            'notification_id' => $notification->id,
            'event_type' => 'opened',
            'installation_id' => $inst,
        ]);
        $resOpenedDup->assertStatus(200);
        $notification->refresh();
        $this->assertEquals(1, $notification->opened_count, 'Duplicate opened event must not increment counter twice');
    }

    public function test_notification_events_rejects_foreign_device_owner()
    {
        config(['notification.delivery_events_enabled' => true]);

        // Create a foreign user and their device
        $foreignUser = User::create([
            'fullname' => 'Foreign User',
            'email' => 'foreign-' . uniqid() . '@example.com',
            'phone' => '9665' . rand(1000000, 9999999),
            'password' => bcrypt('secret123'),
            'is_active' => 1,
        ]);

        $foreignInst = 'inst_foreign_' . uniqid();
        $foreignDevice = Device::create([
            'user_id' => $foreignUser->id,
            'device_token' => 'token_foreign_' . uniqid(),
            'installation_id' => $foreignInst,
            'type' => 'ios',
        ]);

        $notification = Notification::create([
            'title' => 'Test Notification',
            'body' => 'Test Body',
            'for' => 'all',
            'purpose' => 'marketing',
            'types' => json_encode(['apps']),
        ]);

        // Authenticated client tries to report an event on the foreign device
        $response = $this->actingAs($this->clientUser, 'sanctum')->postJson('/api/client/notifications/events', [
            'notification_id' => $notification->id,
            'event_type' => 'opened',
            'installation_id' => $foreignInst,
        ]);

        $response->assertStatus(403);
    }

    public function test_observe_vs_enforce_for_marketing_opt_out()
    {
        // Client opted out explicitly
        $this->clientUser->update([
            'is_allow_notify' => 0,
            'is_allow_notify_confirmed_at' => now(),
        ]);

        $token = 'token_optout_' . uniqid();
        Device::create([
            'user_id' => $this->clientUser->id,
            'device_token' => $token,
            'type' => 'ios',
            'token_status' => 'active',
        ]);

        $notification = new Notification([
            'for' => 'users',
            'for_data' => $this->clientUser->id,
            'purpose' => 'marketing',
            'types' => json_encode(['apps']),
        ]);

        $service = new RecipientEligibilityService();

        // 1. Observe mode: records observation but does not block delivery
        config(['notification.marketing_preference_mode' => 'observe']);
        $resultObserve = $service->evaluateEligibility($notification);
        $this->assertEquals(1, $resultObserve['eligible_devices_count'], 'In observe mode, devices should still be included for delivery');
        $this->assertEquals('marketing_disabled', $resultObserve['user_eligibility'][$this->clientUser->id]['status']);

        // 2. Enforce mode: blocks marketing delivery
        config(['notification.marketing_preference_mode' => 'enforce']);
        $resultEnforce = $service->evaluateEligibility($notification);
        $this->assertEquals(0, $resultEnforce['eligible_devices_count'], 'In enforce mode, opted out users must be blocked from delivery');
        $this->assertEquals('marketing_disabled', $resultEnforce['user_eligibility'][$this->clientUser->id]['status']);
    }

    public function test_observe_vs_enforce_for_permission_denied()
    {
        $token = 'token_perm_denied_' . uniqid();
        Device::create([
            'user_id' => $this->clientUser->id,
            'device_token' => $token,
            'type' => 'ios',
            'token_status' => 'active',
            'notification_permission' => 'denied',
        ]);

        $notification = new Notification([
            'for' => 'users',
            'for_data' => $this->clientUser->id,
            'purpose' => 'marketing',
            'types' => json_encode(['apps']),
        ]);

        $service = new RecipientEligibilityService();

        // 1. Observe mode: logged but device is still included
        config(['notification.permission_mode' => 'observe']);
        $resultObserve = $service->evaluateEligibility($notification);
        $this->assertEquals(1, $resultObserve['eligible_devices_count'], 'In observe mode, denied devices are not dropped');

        // 2. Enforce mode: denied devices are dropped
        config(['notification.permission_mode' => 'enforce']);
        $resultEnforce = $service->evaluateEligibility($notification);
        $this->assertEquals(0, $resultEnforce['eligible_devices_count'], 'In enforce mode, denied devices must be dropped');
    }

    public function test_transactional_notification_unaffected_by_marketing_opt_out_and_roles()
    {
        // Create driver user who has opted out of marketing
        $driver = User::create([
            'fullname' => 'Driver Operational User',
            'email' => 'driver-' . uniqid() . '@example.com',
            'phone' => '9665' . rand(1000000, 9999999),
            'password' => bcrypt('secret123'),
            'is_active' => 1,
            'is_allow_notify' => 0,
            'is_allow_notify_confirmed_at' => now(),
        ]);
        $driver->assignRole('driver');

        Device::create([
            'user_id' => $driver->id,
            'device_token' => 'token_driver_' . uniqid(),
            'type' => 'android',
            'app_context' => 'driver',
            'token_status' => 'active',
            'notification_permission' => 'authorized',
        ]);

        $notification = new Notification([
            'for' => 'users',
            'for_data' => $driver->id,
            'purpose' => 'transactional', // operational push
            'types' => json_encode(['apps']),
        ]);

        config(['notification.marketing_preference_mode' => 'enforce']);

        $service = new RecipientEligibilityService();
        $result = $service->evaluateEligibility($notification);

        $this->assertEquals(1, $result['eligible_users_count'], 'Transactional push must reach drivers even in enforce mode');
        $this->assertEquals(1, $result['eligible_devices_count']);
        $this->assertEquals('eligible', $result['user_eligibility'][$driver->id]['status']);
    }

    public function test_direct_fcm_flag_triggers_batch_vs_legacy_fallback()
    {
        $token = 'token_flag_test_' . uniqid();
        Device::create([
            'user_id' => $this->clientUser->id,
            'device_token' => $token,
            'type' => 'ios',
            'token_status' => 'active',
        ]);

        $notification = Notification::create([
            'title' => 'Flag Test',
            'body' => 'Flag Body',
            'for' => 'users',
            'for_data' => $this->clientUser->id,
            'purpose' => 'marketing',
            'types' => json_encode(['apps']),
        ]);

        // When direct_fcm_enabled is false -> runs legacy path, completes without creating batch jobs or fake token records
        config(['notification.direct_fcm_enabled' => false]);
        NotificationsManger::getInstance()->sendNotification($notification);

        $notification->refresh();
        $this->assertEquals('completed', $notification->processing_status);

        // Assert no notification_tokens direct batch rows were created
        $directTokens = NotificationToken::where('notification_id', $notification->id)->count();
        $this->assertEquals(0, $directTokens, 'Legacy fallback should not create direct FCM batch token rows');
    }

    public function test_notification_data_normalizer_handles_json_array_csv_int_null()
    {
        $this->assertEquals([1, 2, 3], NotificationDataNormalizer::toUserIds([1, 2, 3]));
        $this->assertEquals([10, 20, 30], NotificationDataNormalizer::toUserIds('10, 20, 30'));
        $this->assertEquals([100, 200], NotificationDataNormalizer::toUserIds('[100, 200]'));
        $this->assertEquals([55], NotificationDataNormalizer::toUserIds(55));
        $this->assertEquals([], NotificationDataNormalizer::toUserIds(null));
        $this->assertEquals([], NotificationDataNormalizer::toUserIds(''));
    }

    public function test_banner_notification_polymorphic_isolation()
    {
        $notification = Notification::create([
            'title' => 'Standard Notification',
            'body' => 'Standard Body',
            'for' => 'all',
            'purpose' => 'marketing',
            'types' => json_encode(['apps']),
        ]);

        // Insert standard notification user pivot
        $stdId = DB::table('users_notifications')->insertGetId([
            'user_id' => $this->clientUser->id,
            'notifications_id' => $notification->id,
            'notifications_type' => Notification::class,
            'status' => 'pending',
        ]);

        // Insert polymorphic BannerNotification record with same ID but different polymorphic type
        $bannerId = DB::table('users_notifications')->insertGetId([
            'user_id' => $this->clientUser->id,
            'notifications_id' => $notification->id,
            'notifications_type' => BannerNotification::class,
            'status' => 'pending',
            'response' => 'banner_untouched',
        ]);

        // Update only Notification::class polymorphic records
        DB::table('users_notifications')
            ->where('notifications_type', Notification::class)
            ->where('notifications_id', $notification->id)
            ->where('user_id', $this->clientUser->id)
            ->update([
                'status' => 'sent',
                'response' => 'updated_successfully',
            ]);

        // Verify standard notification was updated
        $standardRecord = DB::table('users_notifications')->find($stdId);
        $this->assertEquals('sent', $standardRecord->status);
        $this->assertEquals('updated_successfully', $standardRecord->response);

        // Verify BannerNotification with same notification ID was NEVER modified
        $bannerRecord = DB::table('users_notifications')->find($bannerId);
        $this->assertEquals('pending', $bannerRecord->status);
        $this->assertEquals('banner_untouched', $bannerRecord->response);
    }

    public function test_job_level_kill_switch_stops_queued_job_when_flag_becomes_false()
    {
        // 1. Setup notification and device batch while direct_fcm_enabled is initially true
        config(['notification.direct_fcm_enabled' => true]);

        $notification = Notification::create([
            'title' => 'Kill Switch Test',
            'body' => 'Kill Switch Body',
            'for' => 'users',
            'for_data' => $this->clientUser->id,
            'purpose' => 'marketing',
            'types' => json_encode(['apps']),
            'processing_status' => 'processing',
        ]);

        $batch = [
            [
                'token' => 'token_kill_switch_1',
                'user_id' => $this->clientUser->id,
                'device_id' => 1,
                'installation_id' => 'inst_ks_1',
                'platform' => 'android',
            ],
            [
                'token' => 'token_kill_switch_2',
                'user_id' => $this->clientUser->id,
                'device_id' => 2,
                'installation_id' => 'inst_ks_2',
                'platform' => 'ios',
            ],
        ];

        $job = new SendFcmBatchJob($notification, $batch);

        // 2. Kill switch triggered: Change flag to false before job executes
        config(['notification.direct_fcm_enabled' => false]);
        Http::fake();

        // 3. Execute the job
        $job->handle();

        // 4. Assertions:
        // - Execution status is explicitly skipped_by_feature_flag
        $this->assertEquals('skipped_by_feature_flag', $job->executionStatus);

        // - ZERO HTTP requests made to Google FCM
        Http::assertNothingSent();

        // - ZERO fake token rows or fake accepted/failed counters created
        $tokensCount = NotificationToken::where('notification_id', $notification->id)->count();
        $this->assertEquals(0, $tokensCount, 'Kill switch must not generate any token rows');

        $notification->refresh();
        $this->assertEquals(0, (int)$notification->accepted_by_fcm_count, 'No accepted counter increment');
        $this->assertEquals(0, (int)$notification->permanent_failed_count, 'No failed counter increment');
    }

    public function test_no_duplicate_sending_during_flag_transition()
    {
        $token = 'token_transition_' . uniqid();
        $device = Device::create([
            'user_id' => $this->clientUser->id,
            'device_token' => $token,
            'type' => 'ios',
            'token_status' => 'active',
        ]);

        $notification = Notification::create([
            'title' => 'Transition Test',
            'body' => 'Transition Body',
            'for' => 'users',
            'for_data' => $this->clientUser->id,
            'purpose' => 'transactional',
            'types' => json_encode(['apps']),
        ]);

        // 1. Send with direct_fcm_enabled = false (legacy topic path)
        config(['notification.direct_fcm_enabled' => false]);
        NotificationsManger::getInstance()->sendNotification($notification);

        $notification->refresh();
        $this->assertEquals('legacy_topic', $notification->getDeliveryChannel());
        $this->assertEquals('completed', $notification->processing_status);

        // 2. Switch flag to true
        config(['notification.direct_fcm_enabled' => true]);

        // 3. Verify that the legacy notification retains its legacy snapshot and channel
        $this->assertEquals('legacy_topic', $notification->fresh()->getDeliveryChannel());
        $directTokens = NotificationToken::where('notification_id', $notification->id)->count();
        $this->assertEquals(0, $directTokens, 'Legacy notification must never be converted or duplicate sent');
    }

    public function test_dashboard_semantics_for_legacy_and_direct_campaigns()
    {
        config(['notification.new_dashboard_metrics_enabled' => true]);

        // 1. Direct FCM campaign
        $directNotif = Notification::create([
            'title' => 'Direct Campaign',
            'body' => 'Direct Body',
            'for' => 'all',
            'purpose' => 'marketing',
            'types' => json_encode(['apps']),
            'processing_status' => 'completed',
            'accepted_by_fcm_count' => 450,
            'payload' => json_encode(['delivery_channel' => 'direct_fcm']),
        ]);

        $directResource = (new NotificationsResource($directNotif))->toArray(Request::create('/admin/notifications'));

        $this->assertEquals('direct_fcm', $directResource['delivery_channel']);
        $this->assertEquals('Direct FCM', $directResource['delivery_channel_label']);
        $this->assertEquals(450, $directResource['accepted_by_fcm']);
        $this->assertStringContainsString('قُبل من FCM', $directResource['sent_count']);
        $this->assertStringContainsString('450', $directResource['sent_count']);

        // 2. Legacy campaign
        $legacyNotif = Notification::create([
            'title' => 'Legacy Campaign',
            'body' => 'Legacy Body',
            'for' => 'all',
            'purpose' => 'marketing',
            'types' => json_encode(['apps']),
            'processing_status' => 'completed',
            'accepted_by_fcm_count' => 0,
            'payload' => json_encode(['delivery_channel' => 'legacy_topic']),
        ]);
        $legacyNotif->sent_count = 1200; // Simulated legacy users count

        $legacyResource = (new NotificationsResource($legacyNotif))->toArray(Request::create('/admin/notifications'));

        $this->assertEquals('legacy_topic', $legacyResource['delivery_channel']);
        $this->assertEquals('Legacy Topic Subscription', $legacyResource['delivery_channel_label']);
        // Crucial requirement: accepted_by_fcm MUST NOT display legacy sent_count
        $this->assertEquals('—', $legacyResource['accepted_by_fcm']);
        $this->assertEquals(1200, $legacyResource['legacy_topic_count']);
        $this->assertStringContainsString('Legacy Topic Subscription', $legacyResource['sent_count']);
        $this->assertStringNotContainsString('قُبل من FCM', $legacyResource['sent_count']);
    }

    public function test_test_database_isolation_and_events_ownership_preserved()
    {
        // 1. Verify test database is isolated sqlite
        $this->assertEquals('sqlite', config('database.default'));
        $this->assertStringContainsString('testing.sqlite', config('database.connections.sqlite.database'));

        // 2. Foreign device owner rejected with 403
        config(['notification.delivery_events_enabled' => true]);

        $otherUser = User::create([
            'fullname' => 'Other User',
            'email' => 'other_' . uniqid() . '@example.com',
            'phone' => '123' . rand(111111, 999999),
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);

        $device = Device::create([
            'user_id' => $otherUser->id,
            'device_token' => 'token_isolation_' . uniqid(),
            'type' => 'android',
            'token_status' => 'active',
            'installation_id' => 'inst_iso_' . uniqid(),
        ]);

        $notification = Notification::create([
            'title' => 'Isolation Notification',
            'body' => 'Body',
            'for' => 'all',
            'purpose' => 'marketing',
            'types' => json_encode(['apps']),
        ]);

        $response = $this->actingAs($this->clientUser, 'sanctum')->postJson('/api/client/notifications/events', [
            'notification_id' => $notification->id,
            'event_type' => 'received',
            'installation_id' => $device->installation_id,
        ]);

        $response->assertStatus(403);
    }
}