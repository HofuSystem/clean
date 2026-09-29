<?php

namespace Tests\Feature;

use Core\Notification\DataResources\NotificationsResource;
use Core\Notification\Models\Notification;
use Core\Users\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class LegacyTopicIndicatorDisplayTest extends TestCase
{
    use DatabaseTransactions;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        Queue::fake();
        Http::fake();

        $this->user = User::create([
            'fullname' => 'Legacy Indicator Test User',
            'phone' => '0588' . mt_rand(100000, 999999),
            'email' => 'legacy_user_' . uniqid() . '@example.com',
            'password' => bcrypt('password123'),
            'is_active' => 1,
        ]);
    }

    /**
     * Requirement 1: If indicator value = 1 or any number:
     * Display: "قبول الإرسال القديم: {N}"
     * Tooltip: "هذا يعني أن FCM القديم قبل طلب الإرسال فقط، ولا يثبت وصول الإشعار أو فتحه."
     */
    public function test_legacy_topic_indicator_when_value_is_positive_number()
    {
        // Case A: Value = 1
        $notification1 = Notification::create([
            'title' => 'Legacy Campaign 1',
            'body' => 'Body text',
            'for' => 'all',
            'purpose' => 'marketing',
            'types' => json_encode(['apps']),
            'processing_status' => 'completed',
            'payload' => json_encode(['delivery_channel' => 'legacy_topic']),
        ]);
        $notification1->sent_count = 1;

        $resource1 = (new NotificationsResource($notification1))->toArray(Request::create('/admin/notifications'));
        $html1 = $resource1['sent_count'];

        $this->assertStringContainsString('قبول الإرسال القديم: 1', $html1);
        $this->assertStringContainsString('title="هذا يعني أن FCM القديم قبل طلب الإرسال فقط، ولا يثبت وصول الإشعار أو فتحه."', $html1);

        // Case B: Value = 450
        $notification2 = Notification::create([
            'title' => 'Legacy Campaign Many',
            'body' => 'Body text',
            'for' => 'all',
            'purpose' => 'marketing',
            'types' => json_encode(['apps']),
            'processing_status' => 'completed',
            'payload' => json_encode(['delivery_channel' => 'legacy_topic']),
        ]);
        $notification2->sent_count = 450;

        $resource2 = (new NotificationsResource($notification2))->toArray(Request::create('/admin/notifications'));
        $html2 = $resource2['sent_count'];

        $this->assertStringContainsString('قبول الإرسال القديم: 450', $html2);
        $this->assertStringContainsString('title="هذا يعني أن FCM القديم قبل طلب الإرسال فقط، ولا يثبت وصول الإشعار أو فتحه."', $html2);
    }

    /**
     * Requirement 2: If value = 0:
     * Display: "قبول الإرسال القديم: 0"
     * Tooltip: "لم يتم قبول أي إرسال عبر المسار القديم."
     */
    public function test_legacy_topic_indicator_when_value_is_zero()
    {
        $notification = Notification::create([
            'title' => 'Legacy Campaign Zero',
            'body' => 'Body text',
            'for' => 'all',
            'purpose' => 'system',
            'types' => json_encode(['apps']),
            'processing_status' => 'completed',
            'payload' => json_encode(['delivery_channel' => 'legacy_topic']),
        ]);
        $notification->sent_count = 0;

        $resource = (new NotificationsResource($notification))->toArray(Request::create('/admin/notifications'));
        $html = $resource['sent_count'];

        $this->assertStringContainsString('قبول الإرسال القديم: 0', $html);
        $this->assertStringContainsString('title="لم يتم قبول أي إرسال عبر المسار القديم."', $html);
    }

    /**
     * Requirement 3: If value is NULL or unavailable:
     * Do NOT display the dash "—".
     * Display: "غير منطبق"
     * Tooltip: "هذا الإشعار لا يستخدم مسار Legacy Topic أو لا يتوفر له هذا القياس."
     */
    public function test_legacy_topic_indicator_when_value_is_null_or_unavailable()
    {
        $notification = Notification::create([
            'title' => 'Legacy Campaign Null',
            'body' => 'Body text',
            'for' => 'all',
            'purpose' => 'system',
            'types' => json_encode(['apps']),
            'processing_status' => 'queued',
            'payload' => json_encode(['delivery_channel' => 'legacy_topic']),
        ]);
        $notification->sent_count = null;

        $resource = (new NotificationsResource($notification))->toArray(Request::create('/admin/notifications'));
        $html = $resource['sent_count'];

        // Must NOT contain dash —
        $this->assertStringNotContainsString('—', $html);
        // Must display "غير منطبق"
        $this->assertStringContainsString('غير منطبق', $html);
        // Tooltip check
        $this->assertStringContainsString('title="هذا الإشعار لا يستخدم مسار Legacy Topic أو لا يتوفر له هذا القياس."', $html);
    }

    /**
     * Requirement 4: Do not show Legacy Topic indicator for WhatsApp, SMS, or Email;
     * Display instead: "لا ينطبق — قناة WhatsApp/SMS/Email"
     * Tooltip: "هذا الإشعار لا يستخدم مسار Legacy Topic أو لا يتوفر له هذا القياس."
     */
    public function test_legacy_topic_indicator_for_non_fcm_channels_whatsapp_sms_email()
    {
        // WhatsApp channel
        $whatsappNotif = Notification::create([
            'title' => 'WhatsApp Order Alert',
            'body' => 'Order details',
            'for' => 'all',
            'channel' => 'whatsapp',
            'types' => json_encode(['whats_app']),
            'purpose' => 'transactional',
            'processing_status' => 'completed',
        ]);
        $whatsappNotif->sent_count = 10;

        $resWhatsapp = (new NotificationsResource($whatsappNotif))->toArray(Request::create('/admin/notifications'));
        $htmlWhatsapp = $resWhatsapp['sent_count'];

        $this->assertStringContainsString('لا ينطبق — قناة WhatsApp/SMS/Email', $htmlWhatsapp);
        $this->assertStringNotContainsString('قبول الإرسال القديم', $htmlWhatsapp);
        $this->assertStringContainsString('title="هذا الإشعار لا يستخدم مسار Legacy Topic أو لا يتوفر له هذا القياس."', $htmlWhatsapp);

        // SMS channel
        $smsNotif = Notification::create([
            'title' => 'SMS Verification Alert',
            'body' => 'Verification code 1234',
            'for' => 'all',
            'channel' => 'sms',
            'types' => json_encode(['sms']),
            'purpose' => 'authentication',
            'processing_status' => 'completed',
        ]);
        $smsNotif->sent_count = 5;

        $resSms = (new NotificationsResource($smsNotif))->toArray(Request::create('/admin/notifications'));
        $htmlSms = $resSms['sent_count'];

        $this->assertStringContainsString('لا ينطبق — قناة WhatsApp/SMS/Email', $htmlSms);
        $this->assertStringNotContainsString('قبول الإرسال القديم', $htmlSms);
        $this->assertStringContainsString('title="هذا الإشعار لا يستخدم مسار Legacy Topic أو لا يتوفر له هذا القياس."', $htmlSms);

        // Email channel
        $emailNotif = Notification::create([
            'title' => 'Email Newsletter Alert',
            'body' => 'Newsletter details',
            'for' => 'all',
            'channel' => 'email',
            'types' => json_encode(['email']),
            'purpose' => 'marketing',
            'processing_status' => 'completed',
        ]);
        $emailNotif->sent_count = 100;

        $resEmail = (new NotificationsResource($emailNotif))->toArray(Request::create('/admin/notifications'));
        $htmlEmail = $resEmail['sent_count'];

        $this->assertStringContainsString('لا ينطبق — قناة WhatsApp/SMS/Email', $htmlEmail);
        $this->assertStringNotContainsString('قبول الإرسال القديم', $htmlEmail);
        $this->assertStringContainsString('title="هذا الإشعار لا يستخدم مسار Legacy Topic أو لا يتوفر له هذا القياس."', $htmlEmail);
    }
}
