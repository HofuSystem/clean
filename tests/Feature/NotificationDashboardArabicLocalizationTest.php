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

        $this->adminUser = User::create([
            'fullname' => 'Admin Localization Tester',
            'phone' => '0591' . mt_rand(100000, 999999),
            'email' => 'admin_loc_' . uniqid() . '@example.com',
            'password' => bcrypt('password123'),
            'is_active' => 1,
        ]);
        $this->adminUser->assignRole('admin');
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
    }
}
