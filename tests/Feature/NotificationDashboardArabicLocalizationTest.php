<?php

namespace Tests\Feature;

use Core\Notification\Models\Notification;
use Core\Users\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class NotificationDashboardArabicLocalizationTest extends TestCase
{
    use DatabaseTransactions;

    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();
        Queue::fake();
        Http::fake();

        $this->withoutMiddleware([
            \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter::class,
            \Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect::class,
        ]);

        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'dashboard.notifications.export', 'guard_name' => 'web']);

        $this->adminUser = User::create([
            'fullname' => 'Admin Localization Tester',
            'phone' => '0591' . mt_rand(100000, 999999),
            'email' => 'admin_loc_' . uniqid() . '@example.com',
            'password' => bcrypt('password123'),
            'is_active' => 1,
        ]);
        $this->adminUser->assignRole('admin');
        $this->adminUser->givePermissionTo('dashboard.notifications.export');
    }

    public function test_queue_monitor_arabic_localization_and_terms()
    {
        app()->setLocale('ar');

        $notification = Notification::create([
            'title' => 'حملة طابور الاختبار',
            'body' => 'تفاصيل الحملة',
            'for' => 'all',
            'purpose' => 'marketing',
            'types' => json_encode(['apps']),
            'processing_status' => 'completed',
            'payload' => json_encode(['delivery_channel' => 'legacy_topic']),
        ]);

        $response = $this->actingAs($this->adminUser)->get(route('dashboard.notifications.queueMonitor'));
        $response->assertStatus(200);

        $html = $response->getContent();

        // 1. Translated terms must be present
        $this->assertStringContainsString('مراقبة طابور الإشعارات', $html);
        $this->assertStringContainsString('مفتاح إيقاف FCM المباشر', $html);
        $this->assertStringContainsString('أقدم دفعة معلّقة', $html);
        $this->assertStringContainsString('آخر خطأ آمن', $html);
        $this->assertStringContainsString('المسار القديم', $html);

        // 2. Tech names preserved
        $this->assertStringContainsString('FCM', $html);

        // 3. Technical field names or raw English strings removed from Arabic view
        $this->assertStringNotContainsString('is_allow_notify', $html);
        $this->assertStringNotContainsString('(Oldest Pending)', $html);
        $this->assertStringNotContainsString('(Safe Error)', $html);
        $this->assertStringNotContainsString('(Direct FCM Disabled)', $html);
    }

    public function test_customer_and_device_health_arabic_localization_and_terms()
    {
        app()->setLocale('ar');

        $response = $this->actingAs($this->adminUser)->get(route('dashboard.notifications.health'));
        $response->assertStatus(200);

        $html = $response->getContent();

        // 1. Translated terms must be present
        $this->assertStringContainsString('صحة العملاء والأجهزة', $html);
        $this->assertStringContainsString('صلاحية إشعارات النظام', $html);
        $this->assertStringContainsString('توزيع المنصات', $html);
        $this->assertStringContainsString('مسموح بها', $html);
        $this->assertStringContainsString('مرفوضة', $html);
        $this->assertStringContainsString('غير محددة', $html);
        $this->assertStringContainsString('قديمة / غير معروفة', $html);
        $this->assertStringContainsString('حداثة آخر نشاط للأجهزة', $html);
        $this->assertStringContainsString('التفضيل التسويقي', $html);
        $this->assertStringContainsString('تفضيلات الإشعارات التسويقية', $html);
        $this->assertStringContainsString('الإشعارات الفورية', $html);

        // 2. Tech & vendor names preserved
        $this->assertStringContainsString('Apple iOS', $html);
        $this->assertStringContainsString('Android', $html);
        $this->assertStringContainsString('Huawei', $html);

        // 3. Technical field name is_allow_notify strictly forbidden in Arabic view
        $this->assertStringNotContainsString('is_allow_notify', $html);

        // 4. Raw English parenthesis labels removed from Arabic view
        $this->assertStringNotContainsString('(Platforms)', $html);
        $this->assertStringNotContainsString('(OS Permission)', $html);
        $this->assertStringNotContainsString('(Authorized)', $html);
        $this->assertStringNotContainsString('(Denied)', $html);
        $this->assertStringNotContainsString('(Not Determined)', $html);
        $this->assertStringNotContainsString('(Legacy / Unknown)', $html);
        $this->assertStringNotContainsString('(Marketing Preference)', $html);
        $this->assertStringNotContainsString('(Last Seen Recency)', $html);

        // 5. Breadcrumb verification in Arabic
        $this->assertStringContainsString('الرئيسية', $html);
        $this->assertStringContainsString('الإشعارات', $html);
        $this->assertStringContainsString('←', $html);
        $this->assertStringNotContainsString('→', $html);
        $this->assertStringContainsString('العودة للحملات', $html);
    }

    public function test_campaign_details_arabic_localization_and_breadcrumbs()
    {
        app()->setLocale('ar');

        $campaign = Notification::create([
            'title' => 'حملة تفاصيل تجريبية',
            'body' => 'نص الإشعار التجريبي للتوطين',
            'for' => 'all',
            'purpose' => 'marketing',
            'types' => json_encode(['apps']),
            'processing_status' => 'completed',
            'targeted_users_count' => 100,
            'eligible_users_count' => 80,
            'eligible_devices_count' => 90,
            'accepted_by_fcm_count' => 75,
            'received_count' => 50,
            'opened_count' => 20,
            'payload' => json_encode(['delivery_channel' => 'direct_fcm']),
        ]);

        $response = $this->actingAs($this->adminUser)->get(route('dashboard.notifications.show', $campaign->id));
        $response->assertStatus(200);

        $html = $response->getContent();

        // 1. Translated terms must be present
        $this->assertStringContainsString('تفاصيل الحملة', $html);
        $this->assertStringContainsString('الجمهور والأهلية', $html);
        $this->assertStringContainsString('نتائج الإرسال', $html);
        $this->assertStringContainsString('التفاعل والوصول', $html);
        $this->assertStringContainsString('الجمهور المستهدف', $html);
        $this->assertStringContainsString('المستخدمون المؤهلون', $html);
        $this->assertStringContainsString('الأجهزة المؤهلة', $html);
        $this->assertStringContainsString('قُبل من FCM', $html);
        $this->assertStringContainsString('تم الاستلام', $html);
        $this->assertStringContainsString('تم الفتح', $html);
        $this->assertStringContainsString('تصدير CSV', $html);

        // 2. Breadcrumb in Arabic
        $this->assertStringContainsString('الرئيسية', $html);
        $this->assertStringContainsString('الإشعارات', $html);
        $this->assertStringContainsString('←', $html);
        $this->assertStringNotContainsString('→', $html);
        $this->assertStringContainsString('العودة للحملات', $html);

        // Exactly 2 arrows between the 3 breadcrumb elements: الرئيسية ← الإشعارات ← تفاصيل الحملة
        if (preg_match('/<ul class="breadcrumb[^>]*>(.*?)<\/ul>/s', $html, $matches)) {
            $breadcrumbHtml = $matches[1];
            $this->assertEquals(2, substr_count($breadcrumbHtml, '←'));
            $this->assertEquals(0, substr_count($breadcrumbHtml, '→'));
        }

        // 3. Technical & vendor names preserved
        $this->assertStringContainsString('FCM', $html);
        $this->assertStringContainsString('CSV', $html);

        // 4. Raw English parenthesis labels removed from Arabic view
        $this->assertStringNotContainsString('(Marketing)', $html);
        $this->assertStringNotContainsString('(Transactional)', $html);
        $this->assertStringNotContainsString('(Authentication)', $html);
        $this->assertStringNotContainsString('(System)', $html);
        $this->assertStringNotContainsString('(Transient)', $html);
        $this->assertStringNotContainsString('(Permanent)', $html);
        $this->assertStringNotContainsString('(Received)', $html);
        $this->assertStringNotContainsString('(Opened)', $html);
        $this->assertStringNotContainsString('(Export CSV)', $html);
        $this->assertStringNotContainsString('(Eligible)', $html);
        $this->assertStringNotContainsString('(No Device)', $html);
        $this->assertStringNotContainsString('(Accepted)', $html);
        $this->assertStringNotContainsString('(Queued)', $html);
    }

    public function test_legacy_campaign_details_arabic_localization()
    {
        app()->setLocale('ar');

        $campaign = Notification::create([
            'title' => 'حملة قديمة',
            'body' => 'نص الإشعار القديم',
            'for' => 'all',
            'purpose' => 'marketing',
            'types' => json_encode(['apps']),
            'processing_status' => 'completed',
            'sent_count' => 10,
            'payload' => json_encode(['delivery_channel' => 'legacy_topic']),
        ]);

        $response = $this->actingAs($this->adminUser)->get(route('dashboard.notifications.show', $campaign->id));
        $response->assertStatus(200);

        $html = $response->getContent();

        $this->assertStringContainsString('المسار القديم', $html);
        $this->assertStringContainsString('قبول الإرسال القديم', $html);
    }

    public function test_english_localization_remains_intact()
    {
        app()->setLocale('en');

        $this->assertEquals('Notification Queue Monitor', trans('Notification Queue Monitor'));
        $this->assertEquals('Customer & Device Health', trans('Customer & Device Health'));
        $this->assertEquals('Direct FCM Kill Switch', trans('Direct FCM Kill Switch'));
        $this->assertEquals('Direct FCM Disabled', trans('Direct FCM Disabled'));
        $this->assertEquals('Safe Error', trans('Safe Error'));
        $this->assertEquals('Oldest Pending', trans('Oldest Pending'));
        $this->assertEquals('Legacy', trans('Legacy'));
        $this->assertEquals('Platforms', trans('Platforms'));
        $this->assertEquals('OS Permission', trans('OS Permission'));
        $this->assertEquals('Authorized', trans('Authorized'));
        $this->assertEquals('Denied', trans('Denied'));
        $this->assertEquals('Not Determined', trans('Not Determined'));
        $this->assertEquals('Legacy / Unknown', trans('Legacy / Unknown'));
        $this->assertEquals('Last Seen Recency', trans('Last Seen Recency'));
        $this->assertEquals('Marketing Preference', trans('Marketing Preference'));
        $this->assertEquals('Push', trans('Push'));
        $this->assertEquals('Campaign Details', trans('Campaign Details'));
        $this->assertEquals('Legacy Topic', trans('Legacy Topic'));
        $this->assertEquals('Export CSV', trans('Export CSV'));
        $this->assertEquals('Received', trans('Received'));
        $this->assertEquals('Opened', trans('Opened'));
        $this->assertEquals('FCM Accepted', trans('FCM Accepted'));
        $this->assertEquals('Delivery Results', trans('Delivery Results'));
        $this->assertEquals('Audience & Eligibility', trans('Audience & Eligibility'));
        $this->assertEquals('Interaction & Reach', trans('Interaction & Reach'));
        $this->assertEquals('Targeted Audience', trans('Targeted Audience'));
        $this->assertEquals('Eligible Users', trans('Eligible Users'));
        $this->assertEquals('Eligible Devices', trans('Eligible Devices'));

        // Check English breadcrumbs
        $campaign = Notification::create([
            'title' => 'English Breadcrumb Test',
            'body' => 'Body test',
            'for' => 'all',
            'purpose' => 'system',
            'types' => json_encode(['apps']),
            'processing_status' => 'completed',
            'payload' => json_encode(['delivery_channel' => 'direct_fcm']),
        ]);

        $response = $this->actingAs($this->adminUser)->get(route('dashboard.notifications.show', $campaign->id));
        $response->assertStatus(200);

        $html = $response->getContent();
        if (preg_match('/<ul class="breadcrumb[^>]*>(.*?)<\/ul>/s', $html, $matches)) {
            $breadcrumbHtml = $matches[1];
            $this->assertEquals(2, substr_count($breadcrumbHtml, '→'));
            $this->assertEquals(0, substr_count($breadcrumbHtml, '←'));
            $this->assertStringContainsString('Home', $breadcrumbHtml);
            $this->assertStringContainsString('Notifications', $breadcrumbHtml);
            $this->assertStringContainsString('Campaign Details', $breadcrumbHtml);
        }
    }
}
