<?php

namespace Tests\Unit;

use Core\Notification\Helpers\NotificationsManger;
use Core\Notification\Services\RecipientEligibilityService;
use Core\Notification\Models\Notification;
use Core\Users\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class NotificationRecipientFilterTest extends TestCase
{
    use DatabaseTransactions;

    public function test_for_all_targets_all_matching_users(): void
    {
        $manger = NotificationsManger::getInstance();
        $eligibilityService = app(RecipientEligibilityService::class);

        $query = $manger->getNotificationUserQuery('all', null, null, null, null, null, null, null);
        $this->assertGreaterThan(0, $query->count());

        $notification = new Notification([
            'for' => 'all',
            'for_data' => null,
            'purpose' => 'transactional'
        ]);

        $targetedQuery = $eligibilityService->getTargetedUsersQuery($notification);
        $this->assertGreaterThan(0, $targetedQuery->count());
    }

    public function test_for_users_with_empty_for_data_targets_zero_users(): void
    {
        $manger = NotificationsManger::getInstance();
        $eligibilityService = app(RecipientEligibilityService::class);

        // When for = 'users' and for_data is empty, should target 0 users
        $query = $manger->getNotificationUserQuery('users', null, null, null, null, null, null, null);
        $this->assertEquals(0, $query->count());

        $queryEmptyArray = $manger->getNotificationUserQuery('users', [], null, null, null, null, null, null);
        $this->assertEquals(0, $queryEmptyArray->count());

        $queryEmptyJson = $manger->getNotificationUserQuery('users', '[]', null, null, null, null, null, null);
        $this->assertEquals(0, $queryEmptyJson->count());

        $notification = new Notification([
            'for' => 'users',
            'for_data' => null,
            'purpose' => 'transactional'
        ]);

        $targetedQuery = $eligibilityService->getTargetedUsersQuery($notification);
        $this->assertEquals(0, $targetedQuery->count());
    }

    public function test_for_users_with_specific_ids_targets_only_those_ids(): void
    {
        $manger = NotificationsManger::getInstance();

        $user = User::first();
        if (!$user) {
            $this->markTestSkipped('No user found');
        }

        $query = $manger->getNotificationUserQuery('users', [$user->id], null, null, null, null, null, null);
        $this->assertEquals(1, $query->count());
        $this->assertEquals($user->id, $query->first()->id);

        // Also test JSON array string as sent by form
        $queryJson = $manger->getNotificationUserQuery('users', json_encode([$user->id]), null, null, null, null, null, null);
        $this->assertEquals(1, $queryJson->count());
        $this->assertEquals($user->id, $queryJson->first()->id);
    }

    public function test_for_phone_and_email_with_empty_data_target_zero_users(): void
    {
        $manger = NotificationsManger::getInstance();

        $phoneQuery = $manger->getNotificationUserQuery('phone', null, null, null, null, null, null, null);
        $this->assertEquals(0, $phoneQuery->count());

        $emailQuery = $manger->getNotificationUserQuery('email', null, null, null, null, null, null, null);
        $this->assertEquals(0, $emailQuery->count());
    }
}
