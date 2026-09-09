<?php

namespace Tests\Feature\Audit;

use App\Observers\GlobalModelObserver;
use Core\Comments\Services\CommentingService;
use Core\PaymentGateways\Services\MyFatoorahService;
use Core\Users\Models\User;
use Core\Users\Services\UsersService;
use Core\Wallet\Controllers\Api\WalletController;
use Core\Wallet\Models\WalletTransaction;
use Core\Wallet\Requests\Api\WithdrawRequest;
use Core\Wallet\Services\WalletPackagesService;
use Core\Wallet\Services\WalletTransactionsService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Mockery;
use Tests\Support\IsolatedAuditTestCase;

class WalletWithdrawalTest extends IsolatedAuditTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->app->instance(GlobalModelObserver::class, Mockery::mock(GlobalModelObserver::class)->shouldIgnoreMissing());
        Schema::create('users', function (Blueprint $t) {
            $t->id(); $t->decimal('wallet', 10, 2)->default(0); $t->timestamps(); $t->softDeletes();
        });
        Schema::create('wallet_transactions', function (Blueprint $t) {
            $t->id(); $t->unsignedBigInteger('user_id'); $t->unsignedBigInteger('added_by_id')->nullable();
            $t->decimal('amount', 10, 2); $t->decimal('wallet_before', 10, 2); $t->decimal('wallet_after', 10, 2);
            $t->string('type'); $t->string('transaction_type'); $t->string('status');
            $t->string('bank_name')->nullable(); $t->string('account_number')->nullable(); $t->string('iban_number')->nullable();
            $t->unsignedBigInteger('order_id')->nullable(); $t->text('notes')->nullable();
            $t->timestamps(); $t->softDeletes();
        });
        foreach ([1 => 100, 379 => 1000] as $id => $amount) {
            DB::table('users')->insert(['id' => $id, 'wallet' => $amount]);
            DB::table('wallet_transactions')->insert(['user_id' => $id, 'amount' => $amount,
                'wallet_before' => 0, 'wallet_after' => $amount, 'type' => 'deposit', 'transaction_type' => 'charge', 'status' => 'accepted']);
        }
    }

    private function data(mixed $amount = 40): array
    {
        return ['amount' => $amount, 'bank_name' => 'Test bank', 'account_number' => '000-test', 'iban_number' => 'TEST-IBAN'];
    }

    private function service(): WalletTransactionsService
    {
        return new WalletTransactionsService(Mockery::mock(CommentingService::class));
    }

    private function api(array $data)
    {
        $request = Mockery::mock(WithdrawRequest::class)->makePartial();
        $request->initialize([], $data, [], [], [], ['REQUEST_URI' => '/api/wallet/withdraw', 'REQUEST_METHOD' => 'POST']);
        $request->shouldReceive('validated')->andReturn($data);
        $this->app->instance('request', $request);
        $request->setUserResolver(fn () => User::find(1));
        $controller = new WalletController($this->service(), Mockery::mock(UsersService::class), Mockery::mock(WalletPackagesService::class), Mockery::mock(MyFatoorahService::class));
        return $controller->withdraw($request);
    }

    public function test_api_uses_request_user_even_when_the_old_fixed_user_has_no_balance(): void
    {
        DB::table('users')->where('id', 379)->update(['wallet' => 0]);
        $response = $this->api($this->data());
        $this->assertSame(200, $response->getStatusCode());
        $body = $response->getData(true);
        $this->assertSame('success', $body['status']);
        $this->assertEquals(40, $body['data']['amount']);
        $this->assertEquals(100, $body['data']['before_charge']);
        $this->assertEquals(60, $body['data']['after_charge']);
        $this->assertSame('withdraw', $body['data']['type']);
        $this->assertEquals(60, User::find(1)->wallet);
        $this->assertEquals(0, User::find(379)->wallet);
        $this->assertSame('pending', WalletTransaction::where('type', 'withdraw')->first()->status);
    }

    public function test_api_rejects_more_than_client_balance_even_if_other_user_is_rich(): void
    {
        $response = $this->api($this->data(101));
        $this->assertSame(422, $response->getStatusCode());
        $this->assertSame('fail', $response->getData(true)['status']);
        $this->assertEquals(100, User::find(1)->wallet);
        $this->assertEquals(1000, User::find(379)->wallet);
        $this->assertSame(0, WalletTransaction::where('type', 'withdraw')->count());
    }

    public function test_stale_user_cannot_spend_the_same_balance_twice(): void
    {
        $first = User::find(1); $stale = User::find(1);
        $this->service()->withdraw($this->data(70), $first);
        try {
            $this->service()->withdraw($this->data(70), $stale);
            $this->fail('Stale balance was used');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('amount', $e->errors());
        }
        $this->assertEquals(30, User::find(1)->wallet);
        $this->assertSame(1, WalletTransaction::where('type', 'withdraw')->count());
    }

    public function test_full_balance_is_allowed_and_system_fields_cannot_be_overridden(): void
    {
        $data = $this->data(100) + ['user_id' => 379, 'type' => 'deposit', 'status' => 'accepted',
            'wallet_after' => 9999, 'wallet_before' => 9999, 'transaction_type' => 'charge'];
        $this->service()->withdraw($data, User::find(1));
        $entry = WalletTransaction::where('type', 'withdraw')->firstOrFail();
        $this->assertSame('pending', $entry->status);
        $this->assertSame('withdraw', $entry->transaction_type);
        $this->assertEquals(1, $entry->user_id);
        $this->assertEquals(100, $entry->wallet_before);
        $this->assertEquals(0, $entry->wallet_after);
        $this->assertEquals(0, User::find(1)->wallet);
        $this->assertSame('TEST-IBAN', $entry->iban_number);
    }

    public function test_invalid_amounts_and_oversized_bank_fields_do_not_create_entries(): void
    {
        foreach ([-1, 0, 1.5, 'invalid'] as $amount) {
            try { $this->service()->withdraw($this->data($amount), User::find(1)); $this->fail('Invalid amount accepted'); }
            catch (ValidationException $e) { $this->assertArrayHasKey('amount', $e->errors()); }
        }
        $data = $this->data(); $data['iban_number'] = str_repeat('X', 256);
        try { $this->service()->withdraw($data, User::find(1)); $this->fail('Oversized field accepted'); }
        catch (ValidationException $e) { $this->assertArrayHasKey('iban_number', $e->errors()); }
        $this->assertSame(0, WalletTransaction::where('type', 'withdraw')->count());
        $this->assertEquals(100, User::find(1)->wallet);
    }

    public function test_failure_after_observer_updates_balance_rolls_back_both_changes(): void
    {
        new WalletTransaction; // Register the real observer before the injected failure.
        WalletTransaction::saved(function ($entry) {
            if ($entry->type === 'withdraw') {
                $this->assertEquals(60, User::find(1)->wallet);
                throw new \RuntimeException('Simulated failure after balance update');
            }
        });
        try { $this->service()->withdraw($this->data(), User::find(1)); $this->fail('Failure was swallowed'); }
        catch (\RuntimeException $e) { $this->assertSame('Simulated failure after balance update', $e->getMessage()); }
        $this->assertEquals(100, User::find(1)->wallet);
        $this->assertSame(0, WalletTransaction::where('type', 'withdraw')->count());
        $this->assertSame(0, DB::transactionLevel());
    }
}
