<?php

namespace Tests\Feature;

use Tests\TestCase;
use Core\Users\Models\User;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DeleteAccountTest extends TestCase
{
    use DatabaseTransactions;

    public function test_user_can_delete_account_successfully()
    {
        $user = User::create([
            'fullname' => 'Test Apple Delete User',
            'phone' => '0599999991',
            'email' => 'testdelete1@example.com',
            'is_active' => true,
            'password' => 'password123',
        ]);

        $token = $user->createToken('test-token')->plainTextToken;
        $this->assertCount(1, $user->tokens);

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/delete_account');

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'message' => 'Account deleted successfully',
        ]);

        // Verify tokens are deleted
        $this->assertCount(0, $user->tokens()->get());

        // Verify user is marked inactive and soft deleted
        $freshUser = User::withTrashed()->find($user->id);
        $this->assertNotNull($freshUser->deleted_at);
        $this->assertFalse((bool) $freshUser->is_active);
    }
}
