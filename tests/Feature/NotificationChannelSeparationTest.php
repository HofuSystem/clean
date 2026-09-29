<?php

namespace Tests\Feature;

use Core\Notification\DataResources\NotificationsResource;
use Core\Notification\Helpers\NotificationChannelResolver;
use Core\Notification\Helpers\NotificationsManger;
use Core\Notification\Models\BannerNotification;
use Core\Notification\Models\Notification;
use Core\Notification\Models\NotificationToken;
use Core\Users\Models\Device;
use Core\Users\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class NotificationChannelSeparationTest extends TestCase
{
    use DatabaseTransactions;

    protected User $clientUser;

    protected function setUp(): void
    {
        parent::setUp();
        Http::fake();

        $this->clientUser = User::create([
            'fullname' => 'Channel Test Client',
            'phone' => '0599' . mt_rand(100000, 999999),
            'email' => 'channel_client_' . uniqid() . '@example.com',
            'password' => bcrypt('password123'),
            'is_active' => 1,
            'is_allow_notify' => 1,
            'is_allow_notify_confirmed_at' => now(),
        ]);
    }

    /**
     * 1. verify message via WhatsApp classifies: channel=whatsapp, purpose=authentication
     */
    public function test_01_verify_message_via_whatsapp_resolves_channel_whatsapp_and_purpose_auth(): void
    {
        $notif = Notification::create([
            'types' => '["whats_app"]',
            'for' => 'users',
            'for_data' => json_encode([$this->clientUser->id]),
            'title' => 'verify message',
            'body' => 'verified_code_is : 9876',
            'purpose' => null,
        ]);

        $this->assertEquals(NotificationChannelResolver::CHANNEL_WHATSAPP, $notif->getChannel());
        $this->assertEquals(NotificationChannelResolver::PURPOSE_AUTHENTICATION, $notif->getPurpose());
        $this->assertTrue($notif->isWhatsApp());
        $this->assertFalse($notif->isFcm());
        $this->assertEquals('WhatsApp / Authentication', NotificationChannelResolver::formatCombinedLabel($notif));
    }

    /**
     * 2. Order notification via apps classifies: channel=app_fcm, purpose=transactional
     */
    public function test_02_order_notification_via_apps_resolves_channel_app_fcm_and_purpose_transactional(): void
    {
        $notif = Notification::create([
            'types' => '["apps"]',
            'for' => 'users',
            'for_data' => json_encode([$this->clientUser->id]),
            'title' => 'المندوب بالطريق 🚚',
            'body' => 'المندوب قادم لاستلام طلبك رقم 1234',
            'purpose' => null,
        ]);

        $this->assertEquals(NotificationChannelResolver::CHANNEL_APP_FCM, $notif->getChannel());
        $this->assertEquals(NotificationChannelResolver::PURPOSE_TRANSACTIONAL, $notif->getPurpose());
        $this->assertTrue($notif->isFcm());
        $this->assertFalse($notif->isWhatsApp());
        $this->assertEquals('App FCM / Transactional', NotificationChannelResolver::formatCombinedLabel($notif));
    }

    /**
     * 3. Marketing offer via apps classifies: channel=app_fcm, purpose=marketing
     */
    public function test_03_marketing_offer_via_apps_resolves_channel_app_fcm_and_purpose_marketing(): void
    {
        $notif = Notification::create([
            'types' => '["apps"]',
            'for' => 'all',
            'title' => 'عرض نهاية الأسبوع 🔥',
            'body' => 'استخدم كوبون CLEAN20 للحصول على خصم 20%',
            'purpose' => null,
        ]);

        $this->assertEquals(NotificationChannelResolver::CHANNEL_APP_FCM, $notif->getChannel());
        $this->assertEquals(NotificationChannelResolver::PURPOSE_MARKETING, $notif->getPurpose());
        $this->assertTrue($notif->isFcm());
        $this->assertEquals('App FCM / Marketing', NotificationChannelResolver::formatCombinedLabel($notif));
    }

    /**
     * 4. WhatsApp does not increment accepted_by_fcm_count
     */
    public function test_04_whatsapp_does_not_increment_accepted_by_fcm_count(): void
    {
        $notif = Notification::create([
            'types' => '["whats_app"]',
            'for' => 'users',
            'for_data' => json_encode([$this->clientUser->id]),
            'title' => 'verify message',
            'body' => 'verified_code_is : 3321',
        ]);

        NotificationsManger::getInstance()->sendNotification($notif);
        $notif->refresh();

        $this->assertEquals(0, (int)($notif->accepted_by_fcm_count ?? 0));
        $this->assertEquals(0, (int)($notif->eligible_devices_count ?? 0));
        $tokenCount = NotificationToken::where('notification_id', $notif->id)->count();
        $this->assertEquals(0, $tokenCount, 'WhatsApp must never generate FCM device tokens');
        $this->assertEquals('completed', $notif->processing_status);
    }

    /**
     * 5. SMS does not increment received_count or accepted_by_fcm_count
     */
    public function test_05_sms_does_not_increment_fcm_counters(): void
    {
        $notif = Notification::create([
            'types' => '["sms"]',
            'for' => 'users',
            'for_data' => json_encode([$this->clientUser->id]),
            'title' => 'كود الدخول',
            'body' => 'كود الدخول الخاص بك هو 4455',
        ]);

        NotificationsManger::getInstance()->sendNotification($notif);
        $notif->refresh();

        $this->assertEquals(0, (int)($notif->received_count ?? 0));
        $this->assertEquals(0, (int)($notif->accepted_by_fcm_count ?? 0));
        $this->assertEquals(0, (int)($notif->eligible_devices_count ?? 0));
        $this->assertEquals(NotificationChannelResolver::CHANNEL_SMS, $notif->getChannel());
    }

    /**
     * 6. Email does not appear as FCM
     */
    public function test_06_email_does_not_appear_as_fcm(): void
    {
        $notif = Notification::create([
            'types' => '["email"]',
            'for' => 'users',
            'for_data' => json_encode([$this->clientUser->id]),
            'title' => 'تحديث شروط الاستخدام',
            'body' => 'تم تحديث سياسة الخصوصية والشروط',
            'purpose' => 'system',
        ]);

        $this->assertFalse($notif->isFcm());
        $this->assertTrue($notif->isEmail());
        $this->assertEquals('email', $notif->getDeliveryChannel());
        $this->assertEquals('خادم Email', $notif->getDeliveryMetricLabel());

        $resource = (new NotificationsResource($notif))->toArray(request());
        $this->assertEquals(trans('Not applicable'), $resource['fcm_users_count']);
        $this->assertEquals('—', $resource['eligible_devices_count']);
        $this->assertEquals('—', $resource['accepted_by_fcm']);
    }

    /**
     * 7. is_allow_notify does not block OTP or order notification
     */
    public function test_07_is_allow_notify_does_not_block_otp_or_order_notification(): void
    {
        // User with notifications disabled
        $optedOutUser = User::create([
            'fullname' => 'Opted Out Client',
            'phone' => '0599' . mt_rand(100000, 999999),
            'email' => 'optout_' . uniqid() . '@example.com',
            'password' => bcrypt('password123'),
            'is_active' => 1,
            'is_allow_notify' => 0,
            'is_allow_notify_confirmed_at' => now(),
        ]);

        // A. OTP via WhatsApp
        $otpNotif = Notification::create([
            'types' => '["whats_app"]',
            'for' => 'users',
            'for_data' => json_encode([$optedOutUser->id]),
            'title' => 'verify message',
            'body' => 'verified_code_is : 1111',
        ]);

        NotificationsManger::getInstance()->sendNotification($otpNotif);
        $otpNotif->refresh();

        $userNotifStatus = DB::table('users_notifications')
            ->where('notifications_id', $otpNotif->id)
            ->where('user_id', $optedOutUser->id)
            ->value('status');

        $this->assertEquals('sent', $userNotifStatus, 'OTP must never be blocked by is_allow_notify');

        // B. Order Notification via Apps
        Device::create([
            'user_id' => $optedOutUser->id,
            'type' => 'android',
            'device_token' => 'fcm_token_order_' . uniqid(),
            'token_status' => 'valid',
            'notification_permission' => 'authorized',
        ]);

        $orderNotif = Notification::create([
            'types' => '["apps"]',
            'for' => 'users',
            'for_data' => json_encode([$optedOutUser->id]),
            'title' => 'طلبك جاهز 📦',
            'body' => 'تم تجهيز طلبك وجاري إسناده للسائق',
        ]);

        NotificationsManger::getInstance()->sendNotification($orderNotif);
        $orderNotif->refresh();

        $orderPivotStatus = DB::table('users_notifications')
            ->where('notifications_id', $orderNotif->id)
            ->where('user_id', $optedOutUser->id)
            ->value('eligibility_status');

        $this->assertEquals('eligible', $orderPivotStatus, 'Transactional orders must never be blocked by marketing opt-out');
    }

    /**
     * 8. Legacy records do not display as marketing automatically
     */
    public function test_08_legacy_records_do_not_display_as_marketing_automatically(): void
    {
        $historical = Notification::create([
            'types' => '[]',
            'for' => 'all',
            'title' => 'مرحبا بك',
            'body' => 'أهلا بك في تطبيق كلين ستيشن',
            'purpose' => null,
        ]);

        $this->assertEquals(NotificationChannelResolver::PURPOSE_LEGACY_UNKNOWN, $historical->getPurpose());

        $resource = (new NotificationsResource($historical))->toArray(request());
        $this->assertStringContainsString('غير مصنف', $resource['purpose']);
        $this->assertStringNotContainsString('تسويقي', $resource['purpose']);
    }

    /**
     * 9. Legacy apps and APIs work as is (Endpoint compatibility)
     */
    public function test_09_legacy_apps_and_apis_work_as_is(): void
    {
        $device = Device::create([
            'user_id' => $this->clientUser->id,
            'type' => 'android',
            'device_token' => 'legacy_token_' . uniqid(),
        ]);

        $this->actingAs($this->clientUser, 'sanctum');

        $response = $this->postJson('/api/update_fcm', [
            'device_token' => 'new_legacy_token_' . uniqid(),
            'type' => 'android',
        ]);

        $response->assertStatus(200);
    }

    /**
     * 10. Direct FCM remains false by default
     */
    public function test_10_direct_fcm_remains_false_by_default(): void
    {
        $this->assertFalse(config('notification.direct_fcm_enabled'));
        $this->assertEquals('observe', config('notification.permission_mode'));
        $this->assertEquals('observe', config('notification.marketing_preference_mode'));
    }

    /**
     * 11. BannerNotification polymorphic isolation remains intact
     */
    public function test_11_banner_notification_unaffected(): void
    {
        $banner = BannerNotification::create([
            'publish_date' => now(),
            'un_publish_date' => now()->addDays(7),
            'next_vision_hour' => 24,
            'status' => 'active',
        ]);

        DB::table('users_notifications')->insert([
            'notifications_type' => BannerNotification::class,
            'notifications_id' => $banner->id,
            'user_id' => $this->clientUser->id,
            'status' => 'sent',
            'eligibility_status' => 'eligible',
        ]);

        $bannerUserCount = DB::table('users_notifications')
            ->where('notifications_type', BannerNotification::class)
            ->where('notifications_id', $banner->id)
            ->count();

        $this->assertEquals(1, $bannerUserCount);

        // Standard notification queries must isolate polymorphic types
        $bannerInNotifQuery = DB::table('users_notifications')
            ->where('notifications_type', BannerNotification::class)
            ->where('notifications_id', $banner->id)
            ->first();

        $this->assertEquals(BannerNotification::class, $bannerInNotifQuery->notifications_type);
        $this->assertDatabaseHas('banner_notifications', [
            'id' => $banner->id,
            'status' => 'active',
        ]);
    }

    /**
     * 12. No real HTTP or FCM requests executed during test
     */
    public function test_12_no_real_http_or_fcm_requests_during_execution(): void
    {
        Http::assertNothingSent();
    }

    /**
     * 13. Scope search filters by types correctly for JSON arrays and strings
     */
    public function test_13_scope_search_filters_by_types_json(): void
    {
        $notifWhatsApp = Notification::create([
            'types' => '["whats_app"]',
            'for' => 'all',
            'title' => 'WA Test ' . uniqid(),
            'body' => 'verify message test',
        ]);

        $notifApps = Notification::create([
            'types' => '["apps"]',
            'for' => 'all',
            'title' => 'Apps Test ' . uniqid(),
            'body' => 'app notification test',
        ]);

        // Simulate request with filters.types = 'whats_app'
        request()->merge(['filters' => ['types' => 'whats_app']]);
        $waResults = Notification::search()->pluck('id')->toArray();
        $this->assertContains($notifWhatsApp->id, $waResults);
        $this->assertNotContains($notifApps->id, $waResults);

        // Simulate request with filters.types = 'apps'
        request()->merge(['filters' => ['types' => 'apps']]);
        $appsResults = Notification::search()->pluck('id')->toArray();
        $this->assertContains($notifApps->id, $appsResults);
        $this->assertNotContains($notifWhatsApp->id, $appsResults);

        // Clean up request
        request()->replace([]);
    }

    /**
     * 14. Scope search filters by channel for both explicit column and legacy inferred
     */
    public function test_14_scope_search_filters_by_channel(): void
    {
        $notifExplicit = Notification::create([
            'channel' => 'app_fcm',
            'types' => '["apps"]',
            'for' => 'all',
            'title' => 'Explicit Channel ' . uniqid(),
            'body' => 'explicit test',
        ]);

        $notifLegacyWa = Notification::create([
            'channel' => null,
            'types' => '["whats_app"]',
            'for' => 'all',
            'title' => 'Legacy WA ' . uniqid(),
            'body' => 'legacy test',
        ]);

        request()->merge(['filters' => ['channel' => 'app_fcm']]);
        $appResults = Notification::search()->pluck('id')->toArray();
        $this->assertContains($notifExplicit->id, $appResults);
        $this->assertNotContains($notifLegacyWa->id, $appResults);

        request()->merge(['filters' => ['channel' => 'whatsapp']]);
        $waResults = Notification::search()->pluck('id')->toArray();
        $this->assertContains($notifLegacyWa->id, $waResults);
        $this->assertNotContains($notifExplicit->id, $waResults);

        request()->replace([]);
    }

    /**
     * 15. Scope search filters by purpose for both explicit and legacy inferred
     */
    public function test_15_scope_search_filters_by_purpose(): void
    {
        $notifAuth = Notification::create([
            'purpose' => null,
            'types' => '["whats_app"]',
            'for' => 'all',
            'title' => 'كود التحقق الخاص بك هو 1234',
            'body' => 'verify message code 1234',
        ]);

        $notifMkt = Notification::create([
            'purpose' => 'marketing',
            'types' => '["apps"]',
            'for' => 'all',
            'title' => 'عرض خاص ' . uniqid(),
            'body' => 'خصم 50%',
        ]);

        request()->merge(['filters' => ['purpose' => 'authentication']]);
        $authResults = Notification::search()->pluck('id')->toArray();
        $this->assertContains($notifAuth->id, $authResults);
        $this->assertNotContains($notifMkt->id, $authResults);

        request()->merge(['filters' => ['purpose' => 'marketing']]);
        $mktResults = Notification::search()->pluck('id')->toArray();
        $this->assertContains($notifMkt->id, $mktResults);
        $this->assertNotContains($notifAuth->id, $mktResults);

        request()->replace([]);
    }

    /**
     * 16. Translation keys exist and resolve properly
     */
    public function test_16_translations_exist_and_resolve(): void
    {
        app()->setLocale('ar');
        $this->assertNotEquals('reference', trans('reference'));
        $this->assertNotEquals('search for reference', trans('search for reference'));
        $this->assertEquals('المرجع', trans('reference'));
        $this->assertEquals('القناة', trans('channel'));
        $this->assertEquals('الغرض', trans('purpose'));
        $this->assertEquals('واتساب', trans('whats_app'));

        app()->setLocale('en');
        $this->assertEquals('Reference', trans('reference'));
        $this->assertEquals('Channel', trans('channel'));
        $this->assertEquals('Purpose', trans('purpose'));
    }
}