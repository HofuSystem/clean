<?php
namespace Tests\Feature\Audit;

use App\Observers\GlobalModelObserver;
use Core\Comments\Services\CommentingService;
use Core\Orders\Services\OrdersService;
use Core\PaymentGateways\Controllers\FrontEnd\PaymentGatewayController;
use Illuminate\Http\Request;
use Core\PaymentGateways\Models\PaymentTransaction;
use Core\PaymentGateways\Services\MyFatoorahService;
use Core\Users\Models\User;
use Core\Wallet\Models\WalletTransaction;
use Core\Wallet\Services\WalletChargePricing;
use Core\Wallet\Services\WalletTransactionsService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Mockery;
use Tests\Support\IsolatedAuditTestCase;

class WalletChargePricingTest extends IsolatedAuditTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Only activity logging is mocked. Wallet observer and service remain real.
        $this->app->instance(GlobalModelObserver::class, Mockery::mock(GlobalModelObserver::class)->shouldIgnoreMissing());
        Schema::create('users', function (Blueprint $t) {
            $t->id(); $t->decimal('wallet', 10, 2)->default(0); $t->timestamps(); $t->softDeletes();
        });
        Schema::create('wallet_packages', function (Blueprint $t) {
            $t->id(); $t->decimal('price', 10, 2); $t->decimal('value', 10, 2); $t->string('status'); $t->softDeletes();
        });
        Schema::create('payment_transactions', function (Blueprint $t) {
            $t->id(); $t->string('transaction_id'); $t->decimal('amount', 10, 2);
            $t->string('for'); $t->string('status'); $t->text('request_data'); $t->text('payment_data')->nullable();
            $t->string('gateway_transaction_id')->nullable(); $t->text('payment_response')->nullable(); $t->string('payment_method')->nullable();
            $t->unsignedBigInteger('user_id'); $t->timestamps(); $t->softDeletes();
        });
        Schema::create('wallet_transactions', function (Blueprint $t) {
            $t->id(); $t->decimal('amount', 10, 2); $t->string('transaction_id')->nullable();
            $t->unsignedBigInteger('user_id'); $t->unsignedBigInteger('package_id')->nullable();
            $t->unsignedBigInteger('added_by_id')->nullable(); $t->unsignedBigInteger('order_id')->nullable();
            $t->decimal('wallet_before', 10, 2); $t->decimal('wallet_after', 10, 2);
            $t->string('type'); $t->string('status'); $t->string('transaction_type');
            $t->text('notes')->nullable(); $t->timestamps(); $t->softDeletes();
        });
        DB::table('users')->insert(['id' => 1, 'wallet' => 0]);
        DB::table('wallet_packages')->insert(['id' => 7, 'price' => 100, 'value' => 125, 'status' => 'active']);
    }
    private function service(): WalletTransactionsService
    {
        return new WalletTransactionsService(Mockery::mock(CommentingService::class));
    }
    private function createPayment(array $data = ['check_id' => 7], mixed $amount = 100): PaymentTransaction
    {
        (new MyFatoorahService)->createTransaction($amount, null, $data, 1, 'wallet_charge', 'test-wallet-');
        return PaymentTransaction::latest('id')->firstOrFail();
    }
    public function test_quote_is_server_generated_and_ignores_client_overrides(): void
    {
        $payment = $this->createPayment(['check_id' => 7, 'amount' => 1, 'type' => 'withdraw', 'user_id' => 99, 'wallet_quote' => ['credit_amount' => 9999]]);
        $quote = json_decode($payment->payment_data, true)['wallet_quote'];
        $this->assertSame('100.00', $quote['paid_amount']);
        $this->assertSame('125.00', $quote['credit_amount']);
        $this->assertSame(['amount' => '100.00', 'check_id' => 7, 'order_id' => null], json_decode($payment->request_data, true));
    }
    public function test_wrong_prices_and_unavailable_packages_cannot_create_payments(): void
    {
        foreach ([[1, 7], [101, 7], [100, 999], [100, [7]]] as [$amount, $id]) {
            try { $this->createPayment(['check_id' => $id], $amount); $this->fail('Invalid purchase accepted'); }
            catch (ValidationException $e) { $this->assertNotEmpty($e->errors()); }
        }
        DB::table('wallet_packages')->update(['status' => 'inactive']);
        try { $this->createPayment(); $this->fail('Inactive package accepted'); }
        catch (ValidationException $e) { $this->assertNotEmpty($e->errors()); }
        $this->assertSame(0, PaymentTransaction::count());
    }
    public function test_snapshot_keeps_original_credit_after_package_changes_or_deletion(): void
    {
        $payment = $this->createPayment();
        DB::table('wallet_packages')->update(['price' => 200, 'value' => 999, 'deleted_at' => now()]);
        $gateway = Mockery::mock(MyFatoorahService::class);
        $gateway->shouldReceive('getPaymentStatus')->once()->andReturn([
            'success' => true, 'data' => ['InvoiceId' => 900, 'CustomerReference' => $payment->transaction_id,
                'InvoiceStatus' => 'Paid', 'InvoiceValue' => 100, 'InvoiceDisplayValue' => '100.00 SAR'],
        ]);
        $controller = new PaymentGatewayController(Mockery::mock(OrdersService::class), $this->service(), $gateway);
        for ($i = 0; $i < 2; $i++) {
            $response = $controller->paymentCallback(Request::create('/callback', 'GET', ['paymentId' => 'payment-900']), $payment->transaction_id);
            $this->assertStringContainsString('status=success', $response->getTargetUrl());
        }
        $this->assertSame(1, WalletTransaction::count());
        $entry = WalletTransaction::first();
        $this->assertEquals(125, $entry->amount);
        $this->assertEquals(125, $entry->wallet_after);
        $this->assertEquals(125, User::find(1)->wallet);
        $this->assertEquals(7, $entry->package_id);
        $this->assertSame('deposit', $entry->type);
    }
    public function test_legacy_links_require_matching_price_and_ignore_request_quotes(): void
    {
        $payment = new PaymentTransaction;
        $payment->amount = 100; $payment->transaction_id = 'legacy';
        $payment->request_data = json_encode(['check_id' => 7, 'wallet_quote' => ['credit_amount' => 9999]]);
        $this->assertSame('125.00', (new WalletChargePricing)->ledgerData($payment)['amount']);
        $payment->amount = 1;
        $this->expectException(ValidationException::class);
        (new WalletChargePricing)->ledgerData($payment);
    }
    public function test_regular_topup_preserves_decimal_amount(): void
    {
        $payment = $this->createPayment([], '100.25');
        $data = (new WalletChargePricing)->ledgerData($payment);
        $this->assertSame('100.25', $data['amount']); $this->assertNull($data['package_id']);
        $this->service()->charge($data, User::find(1));
        $this->assertEquals(100.25, User::find(1)->wallet);
    }
    public function test_actual_ledger_ignores_extra_fields_and_updates_balance(): void
    {
        $this->service()->charge(['amount' => 10, 'transaction_id' => 'test-credit', 'user_id' => 99,
            'type' => 'withdraw', 'status' => 'rejected', 'wallet_before' => 9000, 'wallet_after' => 9999,
            'transaction_type' => 'order_payment', 'order_id' => 99], User::find(1));
        $entry = WalletTransaction::first();
        $this->assertSame('deposit', $entry->type); $this->assertSame('accepted', $entry->status);
        $this->assertSame('charge', $entry->transaction_type); $this->assertEquals(1, $entry->user_id);
        $this->assertEquals(0, $entry->wallet_before); $this->assertEquals(10, $entry->wallet_after);
        $this->assertNull($entry->order_id); $this->assertEquals(10, User::find(1)->wallet);
    }
    public function test_corrupt_snapshot_is_rejected(): void
    {
        $payment = $this->createPayment();
        $payment->payment_data = json_encode(['wallet_quote' => ['version' => 1, 'paid_amount' => '1.00', 'credit_amount' => '999.00']]);
        $this->expectException(ValidationException::class);
        (new WalletChargePricing)->ledgerData($payment);
    }
    public function test_removed_package_still_credits_snapshot_without_a_broken_foreign_key(): void
    {
        $payment = $this->createPayment();
        DB::table('wallet_packages')->delete();
        $data = (new WalletChargePricing)->ledgerData($payment);
        $this->assertSame('125.00', $data['amount']);
        $this->assertNull($data['package_id']);
        $this->service()->charge($data, User::find(1));
        $this->assertEquals(125, User::find(1)->wallet);
    }
}
