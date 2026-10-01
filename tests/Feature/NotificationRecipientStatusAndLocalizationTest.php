<?php

namespace Tests\Feature;

use Core\Notification\Helpers\NotificationsManger;
use Core\Notification\Models\Notification;
use Core\Users\Models\Device;
use Core\Users\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class NotificationRecipientStatusAndLocalizationTest extends TestCase
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
            'fullname' => 'Admin Status Tester',
            'phone' => '0599' . mt_rand(100000, 999999),
            'email' => 'admin_status_' . uniqid() . '@example.com',
            'password' => bcrypt('password123'),
            'is_active' => 1,
        ]);
        $this->adminUser->assignRole('admin');
    }

    public function test_edit_screen_renders_arabic_status_filter_options_without_new_order(): void
    {
        app()->setLocale('ar');

        $notification = Notification::create([
            'title' => 'حملة فحص الفلتر',
            'body' => 'محتوى الفحص',
            'for' => 'all',
            'types' => json_encode(['apps']),
            'purpose' => 'general',
            'processing_status' => 'completed',
        ]);

        $response = $this->actingAs($this->adminUser)
            ->get(route('dashboard.notifications.edit', $notification->id));

        $response->assertStatus(200);
        $content = $response->getContent();

        $this->assertStringContainsString('جميع الحالات', $content);
        $this->assertStringContainsString('تم الإرسال', $content);
        $this->assertStringContainsString('قيد الانتظار', $content);
        $this->assertStringContainsString('فشل الإرسال', $content);
        $this->assertStringNotContainsString('طلب جديد', $content);
    }

    public function test_self_healing_routine_syncs_pending_recipients_for_completed_notification(): void
    {
        $userWithDevice = User::create([
            'fullname' => 'Mohammed Device User',
            'phone' => '0598' . mt_rand(100000, 999999),
            'email' => 'dev_user_' . uniqid() . '@example.com',
            'password' => bcrypt('secret'),
            'is_active' => 1,
        ]);

        Device::create([
            'user_id' => $userWithDevice->id,
            'device_id' => 'dev_' . uniqid(),
            'device_token' => 'fcm_tok_' . uniqid(),
            'type' => 'android',
            'token_status' => 'active',
            'notification_permission' => 'granted',
        ]);

        $notification = Notification::create([
            'title' => 'حملة الاختبار للتعافي التلقائي',
            'body' => 'نص تجريبي',
            'for' => 'all',
            'types' => json_encode(['apps']),
            'processing_status' => 'completed',
            'sent_count' => 0,
        ]);

        DB::table('users_notifications')->insert([
            'notifications_type' => Notification::class,
            'notifications_id' => $notification->id,
            'user_id' => $userWithDevice->id,
            'status' => 'pending',
            'response' => 'pending device dispatch',
        ]);

        // Call getSentToUsers AJAX endpoint
        $response = $this->actingAs($this->adminUser)
            ->post(route('dashboard.notifications.getSentToUsers', $notification->id), [
                'draw' => 1,
                'start' => 0,
                'length' => 10,
            ]);

        $response->assertStatus(200);

        // Verify that the record was healed to 'sent'
        $pivot = DB::table('users_notifications')
            ->where('notifications_type', Notification::class)
            ->where('notifications_id', $notification->id)
            ->where('user_id', $userWithDevice->id)
            ->first();

        $this->assertNotNull($pivot);
        $this->assertEquals('sent', $pivot->status);
        $this->assertEquals('Sent successfully via FCM', $pivot->response);

        // Verify notification sent_count was updated
        $notification->refresh();
        $this->assertEquals(1, $notification->sent_count);
    }

    public function test_resend_to_user_updates_status_and_marks_tokenless_as_failed(): void
    {
        $userWithDevice = User::create([
            'fullname' => 'Resend Eligible User',
            'phone' => '0597' . mt_rand(100000, 999999),
            'email' => 'resend_dev_' . uniqid() . '@example.com',
            'password' => bcrypt('secret'),
            'is_active' => 1,
        ]);

        Device::create([
            'user_id' => $userWithDevice->id,
            'device_id' => 'dev_' . uniqid(),
            'device_token' => 'fcm_tok_' . uniqid(),
            'type' => 'ios',
            'token_status' => 'active',
            'notification_permission' => 'granted',
        ]);

        $userWithoutDevice = User::create([
            'fullname' => 'Resend No Device User',
            'phone' => '0596' . mt_rand(100000, 999999),
            'email' => 'resend_nodev_' . uniqid() . '@example.com',
            'password' => bcrypt('secret'),
            'is_active' => 1,
        ]);

        $notification = Notification::create([
            'title' => 'حملة إعادة الإرسال',
            'body' => 'إعادة إرسال',
            'for' => 'all',
            'types' => json_encode(['apps']),
            'processing_status' => 'completed',
        ]);

        DB::table('users_notifications')->insert([
            [
                'notifications_type' => Notification::class,
                'notifications_id' => $notification->id,
                'user_id' => $userWithDevice->id,
                'status' => 'pending',
                'response' => null,
            ],
            [
                'notifications_type' => Notification::class,
                'notifications_id' => $notification->id,
                'user_id' => $userWithoutDevice->id,
                'status' => 'pending',
                'response' => null,
            ],
        ]);

        NotificationsManger::getInstance()->resendToUsers($notification, [$userWithDevice, $userWithoutDevice]);

        $pivot1 = DB::table('users_notifications')
            ->where('notifications_type', Notification::class)
            ->where('notifications_id', $notification->id)
            ->where('user_id', $userWithDevice->id)
            ->first();

        $pivot2 = DB::table('users_notifications')
            ->where('notifications_type', Notification::class)
            ->where('notifications_id', $notification->id)
            ->where('user_id', $userWithoutDevice->id)
            ->first();

        $this->assertEquals('sent', $pivot1->status);
        $this->assertEquals('failed', $pivot2->status);
        $this->assertEquals('No device token', $pivot2->response);
    }

    public function test_legacy_fcm_sender_updates_users_notifications_to_sent(): void
    {
        $user = User::create([
            'fullname' => 'FCM Sent Tester',
            'phone' => '0595' . mt_rand(100000, 999999),
            'email' => 'fcm_sent_' . uniqid() . '@example.com',
            'password' => bcrypt('secret'),
            'is_active' => 1,
        ]);

        $deviceToken = 'token_' . uniqid();
        Device::create([
            'user_id' => $user->id,
            'device_id' => 'dev_' . uniqid(),
            'device_token' => $deviceToken,
            'type' => 'android',
            'token_status' => 'active',
            'notification_permission' => 'granted',
        ]);

        $notification = Notification::create([
            'title' => 'حملة الإرسال المباشر للموضوع القديم',
            'body' => 'رسالة تجريبية',
            'for' => 'all',
            'types' => json_encode(['apps']),
            'processing_status' => 'processing',
        ]);

        DB::table('users_notifications')->insert([
            'notifications_type' => Notification::class,
            'notifications_id' => $notification->id,
            'user_id' => $user->id,
            'status' => 'pending',
            'response' => null,
        ]);

        \Core\Notification\Helpers\NotificationsSender::fcm(
            [
                [
                    'id' => $user->id,
                    'token' => $deviceToken,
                    'notificationId' => $notification->id,
                ]
            ],
            $notification->title,
            $notification->body
        );

        $pivot = DB::table('users_notifications')
            ->where('notifications_type', Notification::class)
            ->where('notifications_id', $notification->id)
            ->where('user_id', $user->id)
            ->first();

        $this->assertEquals('sent', $pivot->status);
        $this->assertEquals('Sent successfully via FCM', $pivot->response);
    }
}
