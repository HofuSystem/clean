<?php



namespace Tests\Feature;



use Core\Notification\Jobs\AbandonedCartJob;

use Core\Notification\Jobs\CelebrateBirthdayJob;

use Core\Notification\Jobs\InactiveAfterOrderJob;

use Core\Notification\Jobs\SendMails;

use Core\Notification\Jobs\SendSMS;

use Core\Notification\Jobs\SendWhatsApp;

use Core\Notification\Jobs\WelcomeNotificationJob;

use Core\Notification\Models\BannerNotification;

use Core\Notification\Models\Notification;

use Core\Notification\Models\NotificationToken;

use Core\Notification\Models\UsersNotification;

use Core\Notification\Services\RecipientEligibilityService;

use Core\Orders\Models\Order;

use Core\Orders\Models\OrderRepresentative;

use Core\Orders\Observers\OrderRepresentativeObserver;

use Core\Users\Models\Device;

use Core\Users\Models\User;

use Illuminate\Foundation\Testing\DatabaseTransactions;

use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Queue;

use Spatie\Permission\Models\Role;

use Tests\TestCase;



class LegacyCompatibilityGateTest extends TestCase

{

    use DatabaseTransactions;



    protected User $clientUser;

    protected User $driverUser;

    protected User $technicalUser;



    protected function setUp(): void

    {

        parent::setUp();



        $this->withoutMiddleware([

            \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter::class,

            \Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect::class,

        ]);



        Role::firstOrCreate(['name' => 'client', 'guard_name' => 'web']);

        Role::firstOrCreate(['name' => 'driver', 'guard_name' => 'web']);

        Role::firstOrCreate(['name' => 'technical', 'guard_name' => 'web']);



        $this->clientUser = User::create([

            'fullname' => 'Legacy Client User',

            'email' => 'client-leg-' . uniqid() . '@example.com',

            'phone' => '9665' . rand(1000000, 9999999),

            'password' => bcrypt('secret123'),

            'is_active' => 1,

            'is_allow_notify' => 1,

        ]);

        $this->clientUser->assignRole('client');



        $this->driverUser = User::create([

            'fullname' => 'Driver User',

            'email' => 'driver-' . uniqid() . '@example.com',

            'phone' => '9665' . rand(1000000, 9999999),

            'password' => bcrypt('secret123'),

            'is_active' => 1,

        ]);

        $this->driverUser->assignRole('driver');



        $this->technicalUser = User::create([

            'fullname' => 'Technical User',

            'email' => 'technical-' . uniqid() . '@example.com',

            'phone' => '9665' . rand(1000000, 9999999),

            'password' => bcrypt('secret123'),

            'is_active' => 1,

        ]);

        $this->technicalUser->assignRole('technical');

    }



    /**

     * 1. Customer legacy token save via /api/update_fcm endpoint.

     */

    public function test_01_legacy_endpoint_update_fcm_saves_token_without_new_fields()

    {

        $token = 'leg_tok_' . uniqid();



        // Calling legacy endpoint without installation_id, app_context, or notification_permission

        $response = $this->actingAs($this->clientUser, 'sanctum')->postJson('/api/update_fcm', [

            'type' => 'android',

            'device_token' => $token,

        ]);



        $response->assertStatus(200);

        $response->assertJsonPath('status', 'success');



        $this->assertDatabaseHas('devices', [

            'user_id' => $this->clientUser->id,

            'type' => 'android',

            'device_token' => $token,

        ]);

    }



    /**

     * 2. Updating token the legacy way updates device_token correctly.

     */

    public function test_02_legacy_endpoint_updates_existing_token()

    {

        $firstToken = 'tok_first_' . uniqid();

        $secondToken = 'tok_second_' . uniqid();



        $this->actingAs($this->clientUser, 'sanctum')->postJson('/api/update_fcm', [

            'type' => 'ios',

            'device_token' => $firstToken,

        ]);



        $resUpdate = $this->actingAs($this->clientUser, 'sanctum')->postJson('/api/update_fcm', [

            'type' => 'ios',

            'device_token' => $secondToken,

        ]);



        $resUpdate->assertStatus(200);



        $this->assertDatabaseHas('devices', [

            'user_id' => $this->clientUser->id,

            'type' => 'ios',

            'device_token' => $secondToken,

        ]);

    }



    /**

     * 3. Re-login from same device preserves or rebinds without unique constraint error.

     */

    public function test_03_relogin_from_same_device_does_not_crash_on_unique_token()

    {

        $sharedToken = 'shared_tok_' . uniqid();



        // User A logs in and registers token

        $this->actingAs($this->clientUser, 'sanctum')->postJson('/api/update_fcm', [

            'type' => 'android',

            'device_token' => $sharedToken,

        ])->assertStatus(200);



        // User B logs in on same device and registers same token

        $userB = User::create([

            'fullname' => 'Second User On Device',

            'email' => 'user-b-' . uniqid() . '@example.com',

            'phone' => '9665' . rand(1000000, 9999999),

            'password' => bcrypt('secret123'),

            'is_active' => 1,

        ]);



        $resUserB = $this->actingAs($userB, 'sanctum')->postJson('/api/update_fcm', [

            'type' => 'android',

            'device_token' => $sharedToken,

        ]);



        // Must succeed without SQL UNIQUE violation

        $resUserB->assertStatus(200);

    }



    /**

     * 4. Driver app compatibility.

     */

    public function test_04_driver_app_sync_and_targeting_works()

    {

        $driverToken = 'driver_tok_' . uniqid();



        $response = $this->actingAs($this->driverUser, 'sanctum')->postJson('/api/devices/sync', [

            'platform' => 'android',

            'device_token' => $driverToken,

            'app_context' => 'driver',

        ]);



        $response->assertStatus(200);

        $this->assertDatabaseHas('devices', [

            'user_id' => $this->driverUser->id,

            'app_context' => 'driver',

            'device_token' => $driverToken,

        ]);

    }



    /**

     * 5. Technical app compatibility (and technician alias normalization).

     */

    public function test_05_technical_app_sync_and_technician_alias_works()

    {

        $techToken = 'tech_tok_' . uniqid();



        $response = $this->actingAs($this->technicalUser, 'sanctum')->postJson('/api/devices/sync', [

            'type' => 'ios',

            'device_token' => $techToken,

            'app_context' => 'technician', // alias

        ]);



        $response->assertStatus(200);

        $this->assertDatabaseHas('devices', [

            'user_id' => $this->technicalUser->id,

            'app_context' => 'technical',

            'device_token' => $techToken,

        ]);

    }



    /**

     * 6. Transactional notification is NOT blocked by marketing opt-out.

     */

    public function test_06_transactional_notification_unaffected_by_marketing_opt_out()

    {

        // Client explicitly opted out of marketing

        $optedOutClient = User::create([

            'fullname' => 'Opted Out Client',

            'email' => 'optout-' . uniqid() . '@example.com',

            'phone' => '9665' . rand(1000000, 9999999),

            'password' => bcrypt('secret123'),

            'is_active' => 1,

            'is_allow_notify' => 0,

            'is_allow_notify_confirmed_at' => now(),

        ]);

        $optedOutClient->assignRole('client');



        Device::create([

            'user_id' => $optedOutClient->id,

            'device_token' => 'tok_trans_test_' . uniqid(),

            'type' => 'android',

            'token_status' => 'active',

        ]);



        // Transactional notification for this user

        $notif = Notification::create([

            'title' => 'Your Order Has Arrived',

            'body' => 'Driver is at your door',

            'for' => 'users',

            'for_data' => json_encode([$optedOutClient->id]),

            'purpose' => 'transactional',

            'types' => json_encode(['apps']),

        ]);



        $service = app(RecipientEligibilityService::class);

        $result = $service->evaluateEligibility($notif);



        // Client must be ELIGIBLE because purpose is transactional

        $this->assertEquals(1, $result['eligible_users_count']);

        $this->assertEquals('eligible', $result['user_eligibility'][$optedOutClient->id]['status']);

    }



    /**

     * 7. Welcome notification job instantiation and dispatch safety.

     */

    public function test_07_welcome_notification_job_instantiation()

    {

        $job = new WelcomeNotificationJob($this->clientUser->id);

        $this->assertInstanceOf(WelcomeNotificationJob::class, $job);

    }



    /**

     * 8. Automated jobs instantiation safety.

     */

    public function test_08_automated_jobs_instantiation_safety()

    {

        $jobCart = new AbandonedCartJob(['user_id' => $this->clientUser->id]);

        $this->assertInstanceOf(AbandonedCartJob::class, $jobCart);



        $jobBday = new CelebrateBirthdayJob($this->clientUser->id);

        $this->assertInstanceOf(CelebrateBirthdayJob::class, $jobBday);



        $jobInactive = new InactiveAfterOrderJob($this->clientUser->id);

        $this->assertInstanceOf(InactiveAfterOrderJob::class, $jobInactive);

    }



    /**

     * 9. Channels readiness (SMS, WhatsApp, Email).

     */

    public function test_09_multi_channel_jobs_instantiation()

    {

        $smsJob = new SendSMS(['966500000000'], 'Test Title', 'Test Message');

        $this->assertInstanceOf(SendSMS::class, $smsJob);



        $waJob = new SendWhatsApp(['966500000000'], 'Test Title', 'Test Message');

        $this->assertInstanceOf(SendWhatsApp::class, $waJob);



        $mailJob = new SendMails(['test@example.com'], 'Test Title', 'Test Message');

        $this->assertInstanceOf(SendMails::class, $mailJob);

    }



    /**

     * 10. BannerNotification polymorphic isolation.

     */

    public function test_10_banner_notification_polymorphic_isolation()
    {
        $campaign = Notification::create([
            "title" => "Standard Campaign",
            "body" => "Body",
            "for" => "all",
            "types" => json_encode(["apps"]),
            "purpose" => "marketing",
        ]);

        DB::table("users_notifications")->where("notifications_id", $campaign->id)->delete();

        $bannerId = DB::table("users_notifications")->insertGetId([
            "user_id" => $this->clientUser->id,
            "notifications_id" => $campaign->id,
            "notifications_type" => BannerNotification::class,
            "status" => "pending",
            "response" => "banner_untouched",
        ]);

        $standardId = DB::table("users_notifications")->insertGetId([
            "user_id" => $this->clientUser->id,
            "notifications_id" => $campaign->id,
            "notifications_type" => Notification::class,
            "status" => "pending",
            "response" => "standard_notification",
        ]);

        $campaignUserRows = DB::table("users_notifications")
            ->where("notifications_type", Notification::class)
            ->where("notifications_id", $campaign->id)
            ->count();

        $bannerUserRows = DB::table("users_notifications")
            ->where("notifications_type", BannerNotification::class)
            ->where("notifications_id", $campaign->id)
            ->count();

        $this->assertEquals(1, $campaignUserRows);
        $this->assertEquals(1, $bannerUserRows);

        DB::table("users_notifications")
            ->where("notifications_type", Notification::class)
            ->where("notifications_id", $campaign->id)
            ->update(["status" => "sent", "response" => "standard_updated"]);

        $bannerRecord = DB::table("users_notifications")->find($bannerId);
        $this->assertEquals("pending", $bannerRecord->status);
        $this->assertEquals("banner_untouched", $bannerRecord->response);
    }
    public function test_11_device_sync_omits_all_optional_fields_safely()

    {

        // Minimal legacy sync request

        $response = $this->actingAs($this->clientUser, 'sanctum')->postJson('/api/devices/sync', [

            'device_token' => 'min_tok_' . uniqid(),

            'platform' => 'android',

        ]);



        $response->assertStatus(200);

        $response->assertJsonPath('status', 'success');



        $this->assertDatabaseHas('devices', [

            'user_id' => $this->clientUser->id,

            'app_context' => 'client', // resolved from authenticated user role

            'notification_permission' => 'unknown', // default

        ]);

    }

}
