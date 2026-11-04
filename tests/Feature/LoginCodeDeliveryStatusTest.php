<?php

namespace Tests\Feature;

use Core\Notification\Models\Notification;
use Core\Settings\Services\SettingsService;
use Core\Users\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use ReflectionClass;
use RuntimeException;
use Tests\TestCase;

class LoginCodeDeliveryStatusTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        app(SettingsService::class)->saveSettings([
            'login_using' => 'code',
            'notify_login_using' => ['email'],
        ]);
    }

    protected function tearDown(): void
    {
        Cache::forget('app_database_settings');
        (new ReflectionClass(SettingsService::class))->getProperty('allSettings')->setValue(null);

        parent::tearDown();
    }

    private function client(): User
    {
        return User::create([
            'fullname' => 'Login Delivery Test',
            'phone' => '0599' . random_int(100000, 999999),
            'email' => 'login-delivery-' . uniqid() . '@example.com',
            'password' => bcrypt('secret123'),
            'is_active' => 1,
        ]);
    }

    public function test_failed_otp_channel_keeps_its_real_status_without_changing_login_response(): void
    {
        $user = $this->client();
        Mail::shouldReceive('to')->once()->andThrow(new RuntimeException('Test mail transport unavailable'));

        $this->postJson('/api/client/login', ['phone' => $user->phone])->assertOk();

        $notification = Notification::where('title', 'verify message')
            ->where('for_data', json_encode([$user->id]))
            ->latest('id')->firstOrFail();

        $this->assertSame('failed', $notification->processing_status);
        $this->assertSame(0, (int) (json_decode($notification->payload, true)['sent_count'] ?? -1));
        $this->assertSame('failed', DB::table('users_notifications')
            ->where('notifications_id', $notification->id)->where('user_id', $user->id)->value('status'));
        $this->assertNotNull($user->fresh()->verified_code);
    }

    public function test_login_still_succeeds_when_code_delivery_succeeds(): void
    {
        $user = $this->client();
        Mail::fake();

        $this->postJson('/api/client/login', ['phone' => $user->phone])->assertOk();

        $notification = Notification::where('title', 'verify message')
            ->where('for_data', json_encode([$user->id]))
            ->latest('id')->firstOrFail();

        $this->assertSame('completed', $notification->processing_status);
        $this->assertSame('sent', DB::table('users_notifications')
            ->where('notifications_id', $notification->id)->where('user_id', $user->id)->value('status'));
    }
}
