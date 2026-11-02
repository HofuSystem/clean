<?php

namespace Tests\Feature;

use Core\Notification\Models\Notification;
use Core\Notification\Services\RecipientEligibilityService;
use Core\Users\Models\Device;
use Core\Users\Models\User;
use Core\Users\Services\LegacyDeviceRegistrationService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class LegacyDeviceRegistrationTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        Queue::fake();
    }

    private function user(): User
    {
        return User::create([
            'fullname' => 'Device Registration Test',
            'email' => 'legacy-device-' . uniqid() . '@example.com',
            'phone' => '9665' . rand(1000000, 9999999),
            'password' => bcrypt('secret123'),
            'is_active' => 1,
        ]);
    }

    public function test_provider_login_recovers_token_invalidated_only_by_legacy_topic_failure(): void
    {
        $user = $this->user();
        $device = Device::create([
            'user_id' => $user->id,
            'type' => 'ios',
            'device_token' => 'driver-token',
            'app_context' => 'unknown',
            'token_status' => 'invalid',
            'invalid_reason' => 'Legacy topic subscription failure',
            'invalidated_at' => now()->subDay(),
        ]);

        $registered = app(LegacyDeviceRegistrationService::class)
            ->register($user, 'ios', 'driver-token', 'driver');

        $this->assertSame($device->id, $registered->id);
        $this->assertSame('active', $registered->token_status);
        $this->assertSame('driver', $registered->app_context);
        $this->assertNull($registered->invalid_reason);
        $this->assertNull($registered->invalidated_at);

        $notification = Notification::create([
            'types' => '["apps"]',
            'for' => 'users',
            'for_data' => json_encode([$user->id]),
            'purpose' => 'transactional',
            'title' => 'New assignment',
            'body' => 'You have a new order',
        ]);
        $eligibility = app(RecipientEligibilityService::class)->evaluateEligibility($notification);
        $this->assertSame(1, $eligibility['eligible_devices_count']);
        $this->assertSame($device->id, $eligibility['eligible_devices'][0]['device_id']);
    }

    public function test_same_permanently_rejected_token_stays_invalid_until_token_changes(): void
    {
        $user = $this->user();
        $device = Device::create([
            'user_id' => $user->id,
            'type' => 'android',
            'device_token' => 'old-token',
            'token_status' => 'invalid',
            'invalid_reason' => 'FCM permanent unregistered',
            'invalidated_at' => now()->subDay(),
        ]);

        $service = app(LegacyDeviceRegistrationService::class);
        $unchanged = $service->register($user, 'android', 'old-token', 'technical');
        $this->assertSame('invalid', $unchanged->token_status);

        $refreshed = $service->register($user, 'android', 'new-token', 'technical');
        $this->assertSame($device->id, $refreshed->id);
        $this->assertSame('active', $refreshed->token_status);
        $this->assertSame('new-token', $refreshed->device_token);
        $this->assertSame('technical', $refreshed->app_context);
        $this->assertNull($refreshed->invalid_reason);
    }
}
