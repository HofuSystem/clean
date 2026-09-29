<?php

namespace Tests\Feature;

use Core\Notification\DataResources\NotificationsResource;
use Core\Notification\Models\Notification;
use Core\Notification\Models\UsersNotification;
use Core\Users\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class NotificationCampaignDetailsDisplayTest extends TestCase
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
            'fullname' => 'Admin Test User',
            'email' => 'admin-' . uniqid() . '@example.com',
            'phone' => '9665' . str_pad((string)mt_rand(1, 99999999), 8, '0', STR_PAD_LEFT),
            'password' => bcrypt('secret123'),
            'is_active' => 1,
        ]);
        $this->adminUser->assignRole('admin');
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

    /**
     * Case 1: Legacy completed campaign that reached users:
     * Does NOT show 0 and does NOT show Retry button.
     */
    public function test_legacy_completed_campaign_that_reached_users_does_not_show_zero_nor_retry_button()
    {
        $notification = $this->createCampaign([
            'title' => 'Legacy Campaign Reached Phone',
            'body' => 'Broadcasted to topic and received on device',
            'purpose' => 'marketing',
            'processing_status' => 'completed',
            'sent_count' => null,
            'accepted_by_fcm_count' => 0,
            'transient_failed_count' => 0,
            'permanent_failed_count' => 0,
            'payload' => json_encode(['delivery_channel' => 'legacy_topic']),
        ]);

        $response = $this->actingAs($this->adminUser)->get(route('dashboard.notifications.show', $notification->id));

        $response->assertStatus(200);

        // Requirement 1: Must NOT display "قبول الإرسال القديم: 0"
        $response->assertDontSee('قبول الإرسال القديم: 0');

        // Requirement 4: Must NOT show retry button
        $response->assertDontSee('btn-retry-transient');
        $response->assertDontSee('إعادة محاولة الأخطاء المؤقتة');
        $response->assertDontSee('<div class="modal fade" id="modal-retry-transient"', false);

        // Must display processed unmeasurable label instead
        $response->assertSee('مسار الإرسال القديم:');
        $response->assertSee('تمت المعالجة');
        $response->assertSee('الوصول الفعلي غير قابل للقياس من الخادم');
    }

    /**
     * Case 2: Legacy completed campaign without measurement:
     * Displays "غير قابل للقياس".
     */
    public function test_legacy_completed_campaign_without_measurement_displays_unmeasurable_label()
    {
        $notification = $this->createCampaign([
            'title' => 'Legacy Campaign Without Count',
            'body' => 'Unmeasurable reach via topic',
            'purpose' => 'system',
            'processing_status' => 'completed',
            'sent_count' => null,
            'accepted_by_fcm_count' => 0,
            'payload' => json_encode(['delivery_channel' => 'legacy_topic']),
        ]);

        $response = $this->actingAs($this->adminUser)->get(route('dashboard.notifications.show', $notification->id));

        $response->assertStatus(200);

        // Requirement 1 & 7: Displays "غير قابل للقياس" and "مسار الإرسال القديم: تمت المعالجة"
        $response->assertSee('غير قابل للقياس');
        $response->assertSee('مسار الإرسال القديم:');
        $response->assertSee('تمت المعالجة');
        $response->assertSee('الوصول الفعلي غير قابل للقياس من الخادم');
        $response->assertDontSee('قبول الإرسال القديم: 0');
    }

    /**
     * Case 3: Campaign with transient_failed > 0:
     * Displays Retry button.
     */
    public function test_campaign_with_transient_failed_displays_retry_button()
    {
        $notification = $this->createCampaign([
            'title' => 'Campaign With Retriable Failures',
            'body' => 'Some tokens failed temporarily',
            'purpose' => 'marketing',
            'processing_status' => 'completed',
            'transient_failed_count' => 8,
            'permanent_failed_count' => 2,
            'accepted_by_fcm_count' => 120,
            'payload' => json_encode(['delivery_channel' => 'direct_fcm']),
        ]);

        $response = $this->actingAs($this->adminUser)->get(route('dashboard.notifications.show', $notification->id));

        $response->assertStatus(200);

        // Requirement 4 & 7: Displays retry button for transient failures
        $response->assertSee('btn-retry-transient');
        $response->assertSee('إعادة محاولة الأخطاء المؤقتة');
        $response->assertSee('8');
        $response->assertSee('modal-retry-transient');
    }

    /**
     * Case 4: Direct FCM campaign:
     * Displays accepted_by_fcm_count normally.
     */
    public function test_direct_fcm_campaign_displays_accepted_by_fcm_count_normally()
    {
        $notification = $this->createCampaign([
            'title' => 'Direct FCM Campaign',
            'body' => 'Direct push delivery',
            'purpose' => 'marketing',
            'processing_status' => 'completed',
            'accepted_by_fcm_count' => 385,
            'transient_failed_count' => 0,
            'permanent_failed_count' => 5,
            'payload' => json_encode(['delivery_channel' => 'direct_fcm']),
        ]);

        $response = $this->actingAs($this->adminUser)->get(route('dashboard.notifications.show', $notification->id));

        $response->assertStatus(200);

        // Requirement 3 & 7: Displays accepted_by_fcm_count normally
        $response->assertSee('قُبل من FCM');
        $response->assertSee('385');
        $response->assertDontSee('مسار الإرسال القديم: تمت المعالجة');
        $response->assertDontSee('الوصول الفعلي غير قابل للقياس من الخادم');
    }

    /**
     * Case 5: Legacy campaign with saved count > 0:
     * Displays "قبول الإرسال القديم: X" with disclaimer.
     */
    public function test_legacy_campaign_with_saved_count_displays_exact_count_and_disclaimer()
    {
        $notification = $this->createCampaign([
            'title' => 'Legacy Campaign With Explicit Count',
            'body' => 'Legacy broadcast with count',
            'purpose' => 'marketing',
            'processing_status' => 'completed',
            'sent_count' => 950,
            'payload' => json_encode(['delivery_channel' => 'legacy_topic', 'sent_count' => 950]),
        ]);

        $response = $this->actingAs($this->adminUser)->get(route('dashboard.notifications.show', $notification->id));

        $response->assertStatus(200);

        // Requirement 2: Displays count and disclaimer
        $response->assertSee('قبول الإرسال القديم');
        $response->assertSee('950');
        $response->assertSee('هذا يعني قبول طلب الإرسال من المسار القديم، وليس إثبات وصول أو فتح الإشعار.');
    }

    /**
     * Case 6: NotificationsResource datatable suppresses resend button
     * when campaign is completed without retriable failures.
     */
    public function test_notifications_resource_does_not_show_resend_pending_button_when_campaign_is_completed()
    {
        $notification = $this->createCampaign([
            'title' => 'Completed Legacy Campaign',
            'processing_status' => 'completed',
            'transient_failed_count' => 0,
            'sent_count' => null,
            'payload' => json_encode(['delivery_channel' => 'legacy_topic']),
        ]);

        // Even if users_notifications had pending status rows
        UsersNotification::create([
            'notifications_type' => Notification::class,
            'notifications_id' => $notification->id,
            'user_id' => $this->adminUser->id,
            'status' => 'pending',
            'eligibility_status' => 'eligible',
        ]);
        $notification->pending_count = 1;

        $resource = (new NotificationsResource($notification))->toArray(Request::create('/admin/notifications'));

        $this->assertStringNotContainsString('resend-pending-btn', $resource['actions']);
    }
}
