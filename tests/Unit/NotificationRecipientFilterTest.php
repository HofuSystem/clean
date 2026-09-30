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

    public function test_for_users_with_empty_for_data_targets_all_matching_users_not_zero(): void
    {
        $manger = NotificationsManger::getInstance();
        $eligibilityService = app(RecipientEligibilityService::class);

        // When for = 'users' and for_data is empty
        $query = $manger->getNotificationUserQuery('users', null, null, null, null, null, null, null);
        $this->assertGreaterThan(0, $query->count());

        $notification = new Notification([
            'for' => 'users',
            'for_data' => null,
            'purpose' => 'transactional'
        ]);

        $targetedQuery = $eligibilityService->getTargetedUsersQuery($notification);
        $this->assertGreaterThan(0, $targetedQuery->count());
    }

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
    }
}
