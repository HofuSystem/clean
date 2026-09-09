<?php

namespace Tests\Feature\Audit;

use Core\Orders\Services\OrdersService;
use Core\PaymentGateways\Controllers\FrontEnd\PaymentGatewayController;
use Core\PaymentGateways\Models\PaymentTransaction;
use Core\PaymentGateways\Services\MyFatoorahService;
use Core\Wallet\Services\WalletTransactionsService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Mockery;
use Tests\Support\IsolatedAuditTestCase;

class PaymentCallbackTest extends IsolatedAuditTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->decimal('wallet', 10, 2)->default(0);
            $table->softDeletes();
        });
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id');
            $table->string('status');
            $table->softDeletes();
            $table->timestamps();
        });
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_id');
            $table->string('gateway_transaction_id')->nullable();
            $table->string('for');
            $table->string('status');
            $table->decimal('amount', 10, 2);
            $table->text('request_data');
            $table->text('payment_response')->nullable();
            $table->text('payment_data')->nullable();
            $table->string('payment_method')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->softDeletes();
            $table->timestamps();
        });
        Schema::create('test_ledger', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_id');
            $table->decimal('amount', 10, 2);
        });
        DB::table('users')->insert(['id' => 1, 'wallet' => 0]);
        DB::table('orders')->insert(['id' => 10, 'client_id' => 1, 'status' => 'pending_payment']);
        DB::table('payment_transactions')->insert([
            'id' => 1, 'transaction_id' => 'local-123', 'gateway_transaction_id' => '900',
            'for' => 'wallet_charge', 'status' => 'pending', 'amount' => 100,
            'user_id' => 1, 'request_data' => json_encode(['amount' => 100, 'type' => 'deposit', 'order_id' => 10]),
        ]);
    }

    private function gatewayResult(array $overrides = []): array
    {
        $data = array_replace([
            'InvoiceId' => 900, 'CustomerReference' => 'local-123', 'InvoiceValue' => 100,
            'InvoiceDisplayValue' => '100.00 SAR', 'InvoiceStatus' => 'Paid',
            'InvoiceTransactions' => [['PaymentId' => 'payment-900']],
        ], $overrides);

        return ['success' => true, 'status' => $data['InvoiceStatus'], 'data' => $data];
    }

    private function invoke(PaymentGatewayController $controller, array $query = ['paymentId' => 'payment-900'])
    {
        // Domain services are substituted below; model activity observers are outside this test's scope.
        return Model::withoutEvents(fn () => $controller->paymentCallback(
            Request::create('/en/payment-gateway/local-123/callback', 'GET', $query), 'local-123'
        ));
    }

    private function controller(array $result, ?callable $charge = null, ?callable $gatewayHook = null, string $expectedId = 'payment-900', string $expectedType = 'PaymentId'): PaymentGatewayController
    {
        $gateway = Mockery::mock(MyFatoorahService::class);
        $gateway->shouldReceive('getPaymentStatus')->once()->with($expectedId, $expectedType)
            ->andReturnUsing(function () use ($result, $gatewayHook) {
                if ($gatewayHook) { $gatewayHook(); }
                return $result;
            });
        $gateway->shouldReceive('getMessage')->andReturn('Payment not completed');
        $wallet = Mockery::mock(WalletTransactionsService::class);
        if ($charge) {
            $wallet->shouldReceive('charge')->once()->andReturnUsing($charge);
        } else {
            $wallet->shouldNotReceive('charge');
        }
        $orders = Mockery::mock(OrdersService::class);

        return new PaymentGatewayController($orders, $wallet, $gateway);
    }

    private function credit(array $data, $user): void
    {
        $this->assertSame('local-123', $data['transaction_id']);
        $this->assertEquals(1, $user->id);
        DB::table('test_ledger')->insert(['transaction_id' => $data['transaction_id'], 'amount' => $data['amount']]);
        DB::table('users')->where('id', $user->id)->increment('wallet', $data['amount']);
    }

    private function assertRedirectStatus($response, string $status): void
    {
        parse_str(parse_url($response->getTargetUrl(), PHP_URL_QUERY), $query);
        $this->assertSame($status, $query['status']);
        $this->assertStringContainsString('/local-123', $response->getTargetUrl());
    }

    public function test_repeated_success_credits_wallet_once_and_does_not_requery_gateway(): void
    {
        DB::table('payment_transactions')->update(['request_data' => json_encode(['amount' => 999, 'type' => 'deposit'])]);
        $controller = $this->controller($this->gatewayResult(), fn ($data, $user) => $this->credit($data, $user));
        $this->assertRedirectStatus($this->invoke($controller), 'success');
        $this->assertRedirectStatus($this->invoke($controller), 'success');
        $this->assertSame(1, DB::table('test_ledger')->count());
        $this->assertEquals(100, DB::table('users')->value('wallet'));
        $this->assertSame('success', PaymentTransaction::find(1)->status);
        $this->assertSame('900', PaymentTransaction::find(1)->gateway_transaction_id);
    }

    public function test_mismatched_or_incomplete_gateway_data_never_changes_the_transaction(): void
    {
        foreach ([
            ['CustomerReference' => 'different-order'], ['CustomerReference' => null],
            ['InvoiceId' => 901], ['InvoiceValue' => 99], ['InvoiceValue' => '100.001'],
            ['InvoiceDisplayValue' => '100.00 USD'], ['InvoiceDisplayValue' => '99.00 SAR'],
            ['InvoiceDisplayValue' => null],
        ] as $invalid) {
            $this->assertRedirectStatus($this->invoke($this->controller($this->gatewayResult($invalid))), 'failed');
            $this->assertSame('pending', PaymentTransaction::find(1)->status);
            $this->assertNull(PaymentTransaction::find(1)->payment_response);
        }
        $this->assertSame(0, DB::table('test_ledger')->count());
    }

    public function test_financial_error_rolls_back_and_the_next_callback_can_retry(): void
    {
        $failing = $this->controller($this->gatewayResult(), function ($data, $user) {
            $this->credit($data, $user);
            throw new \RuntimeException('Simulated financial persistence failure');
        });
        $this->assertRedirectStatus($this->invoke($failing), 'failed');
        $this->assertSame(0, DB::table('test_ledger')->count());
        $this->assertEquals(0, DB::table('users')->value('wallet'));
        $this->assertSame('pending', PaymentTransaction::find(1)->status);
        $this->assertNull(PaymentTransaction::find(1)->payment_response);
        $retry = $this->controller($this->gatewayResult(), fn ($data, $user) => $this->credit($data, $user));
        $this->assertRedirectStatus($this->invoke($retry), 'success');
        $this->assertSame(1, DB::table('test_ledger')->count());
    }

    public function test_status_is_reread_after_gateway_response_before_applying_effects(): void
    {
        $controller = $this->controller($this->gatewayResult(['InvoiceStatus' => 'Canceled']), null, function () {
            DB::table('payment_transactions')->where('id', 1)->update(['status' => 'success']);
        });
        $this->assertRedirectStatus($this->invoke($controller), 'success');
        $this->assertSame('success', PaymentTransaction::find(1)->status);
    }

    public function test_pending_callback_does_not_cancel_the_order(): void
    {
        DB::table('payment_transactions')->update(['for' => 'order_payment']);
        $this->assertRedirectStatus($this->invoke($this->controller($this->gatewayResult(['InvoiceStatus' => 'Pending']))), 'pending');
        $this->assertSame('pending', PaymentTransaction::find(1)->status);
        $this->assertSame('pending_payment', DB::table('orders')->value('status'));
    }

    public function test_canceled_invoice_does_not_change_an_already_active_order(): void
    {
        DB::table('payment_transactions')->update(['for' => 'order_payment']);
        DB::table('orders')->update(['status' => 'pending']);
        $this->assertRedirectStatus($this->invoke($this->controller($this->gatewayResult(['InvoiceStatus' => 'Canceled']))), 'cancel');
        $this->assertSame('canceled', PaymentTransaction::find(1)->status);
        $this->assertSame('pending', DB::table('orders')->value('status'));
    }

    public function test_old_payment_id_storage_is_accepted_when_the_invoice_contains_it(): void
    {
        DB::table('payment_transactions')->update(['gateway_transaction_id' => 'payment-900']);
        $controller = $this->controller($this->gatewayResult(['InvoiceDisplayValue' => '100.000 SR']), fn ($data, $user) => $this->credit($data, $user), null, 'payment-900', 'InvoiceId');
        $this->assertRedirectStatus($this->invoke($controller), 'success');
        $this->assertSame('900', PaymentTransaction::find(1)->gateway_transaction_id);
    }

    public function test_gateway_failure_leaves_financial_state_unchanged(): void
    {
        $controller = $this->controller(['success' => false]);
        $this->assertRedirectStatus($this->invoke($controller), 'failed');
        $this->assertSame('pending', PaymentTransaction::find(1)->status);
        $this->assertEquals(0, DB::table('users')->value('wallet'));
    }

    public function test_existing_invoice_parameter_variants_still_work(): void
    {
        foreach ([['paymentId' => '900'], ['InvoiceId' => '900'], ['Id' => '900'], []] as $query) {
            $controller = $this->controller($this->gatewayResult(['InvoiceStatus' => 'Pending']), null, null, '900', 'InvoiceId');
            $this->assertRedirectStatus($this->invoke($controller, $query), 'pending');
        }
    }

    public function test_fast_order_uses_verified_amount_and_is_applied_once(): void
    {
        DB::table('payment_transactions')->update(['for' => 'fast_payment', 'request_data' => json_encode(['order_id' => 10, 'paid' => 999])]);
        $gateway = Mockery::mock(MyFatoorahService::class);
        $gateway->shouldReceive('getPaymentStatus')->once()->andReturn($this->gatewayResult());
        $orders = Mockery::mock(OrdersService::class);
        $orders->shouldReceive('payFastOrder')->once()->andReturnUsing(function ($id, $data, $user) {
            $this->assertEquals(10, $id);
            $this->assertEquals(100, $data['paid']);
            $this->assertSame('local-123', $data['transaction_id']);
            $this->assertEquals(1, $user->id);
            DB::table('test_ledger')->insert(['transaction_id' => $data['transaction_id'], 'amount' => $data['paid']]);
        });
        $controller = new PaymentGatewayController($orders, Mockery::mock(WalletTransactionsService::class), $gateway);
        $this->assertRedirectStatus($this->invoke($controller), 'success');
        $this->assertRedirectStatus($this->invoke($controller), 'success');
        $this->assertSame(1, DB::table('test_ledger')->count());
    }

    public function test_later_verified_success_recovers_failed_order_without_repeating_it(): void
    {
        DB::table('payment_transactions')->update(['for' => 'order_payment', 'status' => 'failed']);
        DB::table('orders')->update(['status' => 'failed_payment']);
        $gateway = Mockery::mock(MyFatoorahService::class);
        $gateway->shouldReceive('getPaymentStatus')->once()->andReturn($this->gatewayResult());
        $orders = Mockery::mock(OrdersService::class);
        $orders->shouldReceive('updateStatus')->once()->andReturnUsing(function ($id, $data) {
            $this->assertSame('pending_payment', DB::table('orders')->value('status'));
            $this->assertSame('pending', $data['status']);
            $this->assertSame('local-123', $data['transaction_id']);
            DB::table('orders')->where('id', $id)->update(['status' => $data['status']]);
        });
        $controller = new PaymentGatewayController($orders, Mockery::mock(WalletTransactionsService::class), $gateway);
        $this->assertRedirectStatus($this->invoke($controller), 'success');
        $this->assertRedirectStatus($this->invoke($controller), 'success');
        $this->assertSame('pending', DB::table('orders')->value('status'));
    }

    public function test_order_payment_cannot_be_applied_to_a_different_customer(): void
    {
        DB::table('payment_transactions')->update(['for' => 'order_payment']);
        DB::table('orders')->update(['client_id' => 2]);
        $this->assertRedirectStatus($this->invoke($this->controller($this->gatewayResult())), 'failed');
        $this->assertSame('pending', PaymentTransaction::find(1)->status);
        $this->assertSame('pending_payment', DB::table('orders')->value('status'));
    }

    public function test_pending_page_displays_an_explanation_instead_of_a_blank_status(): void
    {
        app()->setLocale('en');
        $html = view('payment-gateways::frontend.payment-gateway', [
            'transaction' => PaymentTransaction::find(1), 'status' => 'pending',
            'sessionId' => null, 'configError' => true,
        ])->render();
        $this->assertStringContainsString('Payment is still pending', $html);
        $this->assertStringContainsString('Please check your order status before trying another payment.', $html);
        $this->assertStringNotContainsString('Configuration Error', $html);
    }
}
