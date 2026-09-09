<?php

namespace Core\PaymentGateways\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use Core\Orders\Models\Order;
use Core\PaymentGateways\Models\PaymentTransaction;
use Core\PaymentGateways\Services\MyFatoorahService;
use Core\Orders\Services\OrdersService;
use Core\Wallet\Services\WalletTransactionsService;
use Core\Wallet\Services\WalletChargePricing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Core\Users\Models\User;
use Core\PaymentGateways\Services\PaymentResultVerifier;

class PaymentGatewayController extends Controller
{

    public function __construct(
        protected OrdersService $ordersService,
        protected WalletTransactionsService $walletTransactionsService,
        protected MyFatoorahService $myfatoorahService
    ) {
    }

    public function paymentGateway(Request $request, $transaction_id)
    {
        try {
            $status = $request->query('status');
            $message = $request->query('message');
            $transaction = PaymentTransaction::where('transaction_id', $transaction_id)->first();

            // Check if transaction exists
            if (!$transaction) {
                return view('payment-gateways::frontend.payment-gateway', [
                    'title' => __('Payment Gateway'),
                    'status' => 'failed',
                    'message' => __('Transaction not found.'),
                    'transaction' => null,
                    'sessionId' => null,
                    'countryCode' => null,
                    'configError' => true,
                ]);
            }

            if (isset($status) && isset($message)) {
                return view('payment-gateways::frontend.payment-gateway', [
                    'title' => __('Payment Gateway'),
                    'status' => $status,
                    'message' => $message,
                    'transaction' => $transaction,
                    'sessionId' => null,
                    'countryCode' => null,
                    'configError' => true,
                ]);
            }

            if ($transaction->status != 'pending') {
                return view('payment-gateways::frontend.payment-gateway', [
                    'title' => __('Payment Gateway'),
                    'status' => $transaction->status,
                    'message' => $transaction->message,
                    'transaction' => $transaction,
                ]);
            }

            // Check if MyFatoorah is configured
            if (!$this->myfatoorahService->isConfigured()) {
                return view('payment-gateways::frontend.payment-gateway', [
                    'title' => __('Payment Gateway'),
                    'status' => 'failed',
                    'message' => __('Payment gateway is not properly configured.'),
                    'transaction' => $transaction,
                    'sessionId' => null,
                    'countryCode' => null,
                    'configError' => true,
                ]);
            }
            // Initiate embedded payment session
            $result = $this->myfatoorahService->initiateSession([
                'amount' => $transaction->amount,
                'transaction_id' => $transaction->transaction_id,
                'customer_name' => $transaction->user->fullname ?? null,
                'customer_email' => $transaction->user->email ?? null,
                'customer_mobile' => $transaction->user->phone ?? null,
                'language' => app()->getLocale(),
            ]);
            $sessionId = $result['session_id'] ?? null;
            $countryCode = $result['country_code'] ?? 'SAU';
            $error = $result['error'] ?? null;

            // Get payment method IDs from config (hardcoded)
            $paymentMethodIds = $this->myfatoorahService->getAllPaymentMethodIds($transaction->amount);

            return view('payment-gateways::frontend.payment-gateway', [
                'title' => __('Payment Gateway'),
                'status' => $status,
                'message' => $message,
                'transaction' => $transaction,
                'sessionId' => $sessionId,
                'countryCode' => $countryCode,
                'applePayMethodId' => $paymentMethodIds['apple_pay_id'] ?? null,
                'madaMethodId' => $paymentMethodIds['mada_id'] ?? null,
                'apiError' => $error,
            ]);
        } catch (\Throwable $th) {
            report($th);
            return view('payment-gateways::frontend.payment-gateway', [
                'title' => __('Payment Gateway'),
                'status' => 'failed',
                'message' => __('Transaction not found.'),
                'transaction' => null,
                'sessionId' => null,
                'countryCode' => null,
                'configError' => true,
            ]);
        }
    }

    /**
     * Execute embedded payment with tokenized card
     */
    public function executeEmbeddedPayment(Request $request, $transaction_id)
    {
        try {
            $transaction = PaymentTransaction::where('transaction_id', $transaction_id)->firstOrFail();
            $paymentType = $request->input('payment_type', 'Card');
            $paymentMethodId = $request->input('payment_method_id');

            $paymentData = [
                'session_id' => $request->input('session_id'),
                'card_identifier' => $request->input('card_identifier'),
                'card_token' => $request->input('card_token'),
                'card_brand' => $request->input('card_brand'),
                'amount' => $transaction->amount,
                'callback_url' => route('payment-gateway.web.callback', ['transaction_id' => $transaction_id]),
                'transaction_id' => $transaction->transaction_id,
                'customer_name' => $transaction->user->fullname ?? null,
                'customer_email' => $transaction->user->email ?? null,
                'customer_mobile' => $transaction->user->phone ?? null,
                'language' => app()->getLocale(),
                'payment_type' => $paymentType,
                'payment_method_id' => $paymentMethodId, // Pass payment method ID for Apple Pay
            ];

            // Execute payment with card token
            $result = $this->myfatoorahService->executeEmbeddedPayment($paymentData);

            if ($result['success']) {
                // Update payment method based on what was actually used
                $paymentMethodName = $this->getPaymentMethodName($paymentType);
                $transaction->payment_method = $paymentMethodName;
                $transaction->payment_response = json_encode($result['data']);
                $transaction->gateway_transaction_id = $result['invoice_id'] ?? null;
                $transaction->save();

                $invoiceStatus = $result['invoice_status'] ?? 'Pending';
                $paymentId = $result['invoice_id'] ?? $result['payment_id'] ?? null;
                $paymentUrl = $result['payment_url'] ?? null;

                // Always return payment URL when provided — frontend redirects the user
                if (!empty($paymentUrl)) {
                    return response()->json([
                        'success' => true,
                        'redirect_required' => true,
                        'payment_url' => $paymentUrl,
                        'payment_id' => $paymentId,
                        'status' => $invoiceStatus,
                        'message' => __('Redirecting to complete payment...')
                    ]);
                }

                // No payment URL: return status (e.g. paid — frontend redirects to callback)
                if ($invoiceStatus === 'Paid') {
                    return response()->json([
                        'success' => true,
                        'payment_id' => $paymentId,
                        'message' => __('Payment executed successfully'),
                        'status' => 'paid'
                    ]);
                }
                return response()->json([
                    'success' => true,
                    'payment_id' => $paymentId,
                    'message' => __('Payment executed successfully'),
                    'status' => $invoiceStatus,
                ]);
            }

            return response()->json([
                'success' => false,
                'error' => $result['error'] ?? __('Payment execution failed'),
                'message' => $result['error'] ?? __('Payment execution failed')
            ], 400);

        } catch (\Throwable $th) {
            report($th);
            return response()->json([
                'success' => false,
                'error' => $th->getMessage(),
                'message' => __('An error occurred while processing your payment')
            ], 500);
        }
    }

    /**
     * Handle MyFatoorah callback
     */
    public function paymentCallback(Request $request, $transaction_id)
    {
        try {
            $transaction = PaymentTransaction::where('transaction_id', $transaction_id)->firstOrFail();
            if ($transaction->status === 'success') {
                return $this->successfulPaymentRedirect($transaction);
            }
            $paymentId = $request->input('paymentId') ?? $request->input('Id')
                ?? $request->input('InvoiceId') ?? $transaction->gateway_transaction_id;
            if (! is_scalar($paymentId) || (string) $paymentId === '') {
                throw new \UnexpectedValueException('Payment ID not found');
            }
            // The existing embedded page also puts the stored InvoiceId in paymentId.
            $keyType = $request->filled('paymentId') && (string) $paymentId !== (string) $transaction->gateway_transaction_id
                ? 'PaymentId' : 'InvoiceId';
            $result = $this->myfatoorahService->getPaymentStatus((string) $paymentId, $keyType);
            if (! ($result['success'] ?? false) || ! is_array($result['data'] ?? null)) {
                throw new \UnexpectedValueException('Unable to verify payment status');
            }

            // Do network I/O before locking, then re-read to serialize repeated callbacks.
            return DB::transaction(function () use ($transaction, $result) {
                $transaction = PaymentTransaction::whereKey($transaction->id)->lockForUpdate()->firstOrFail();
                if ($transaction->status === 'success') {
                    return $this->successfulPaymentRedirect($transaction);
                }
                (new PaymentResultVerifier)->verify($transaction, $result['data']);
                $transaction->payment_response = json_encode($result['data'], JSON_THROW_ON_ERROR);
                $transaction->gateway_transaction_id = (string) $result['data']['InvoiceId'];
                $status = $result['data']['InvoiceStatus'] ?? null;
                if ($status === 'Paid') {
                    return $this->handleSuccessPayment($transaction);
                }
                if ($status === 'Failed') {
                    return $this->handleFailedPayment($transaction, $this->myfatoorahService->getMessage($result));
                }
                if (in_array($status, ['Canceled', 'Cancelled'], true)) {
                    return $this->handleCancelPayment($transaction, $this->myfatoorahService->getMessage($result));
                }
                // A pending invoice is not evidence of cancellation.
                $transaction->save();
                return redirect()->route('payment-gateway.web', [
                    'transaction_id' => $transaction->transaction_id,
                    'status' => 'pending',
                    'message' => __('Payment is still pending'),
                ]);
            });
        } catch (\Throwable $th) {
            report($th);
            return redirect()->route('payment-gateway.web', [
                'transaction_id' => $transaction_id,
                'status' => 'failed',
                'message' => __('Unable to verify payment status'),
            ]);
        }
    }

    /**
     * Handle successful payment
     */
    private function handleSuccessPayment($transaction)
    {
        $requestData = json_decode($transaction->request_data, true, 512, JSON_THROW_ON_ERROR);
        $user = User::whereKey($transaction->user_id)->lockForUpdate()->firstOrFail();
        if (in_array($transaction->for, ['order_payment', 'fast_payment'], true)) {
            $order = Order::whereKey($requestData['order_id'])->where('client_id', $user->id)->lockForUpdate()->firstOrFail();
            if ($transaction->for === 'order_payment' && in_array($order->status, ['failed_payment', 'cancel_payment'], true)) {
                // A later verified payment can recover an earlier failed/canceled callback.
                $order->update(['status' => 'pending_payment']);
            }
        }
        $requestData['transaction_id'] = $transaction->transaction_id;
        if ($transaction->for === 'order_payment') {
            $this->ordersService->updateStatus($requestData['order_id'], [
                'status' => 'pending',
                'transaction_id' => $transaction->transaction_id,
                'online_payment_method' => $transaction->payment_method,
            ]);
        } elseif ($transaction->for === 'fast_payment') {
            $requestData['online_payment_method'] = $transaction->payment_method;
            $requestData['paid'] = $transaction->amount;
            $this->ordersService->payFastOrder($requestData['order_id'], $requestData, $user);
        } elseif ($transaction->for === 'wallet_charge') {
            $chargeData = (new WalletChargePricing)->ledgerData($transaction);
            $this->walletTransactionsService->charge($chargeData, $user);
        } else {
            throw new \UnexpectedValueException('Unsupported payment purpose.');
        }

        // Success and its financial effects commit or roll back together.
        $transaction->status = 'success';
        $transaction->save();

        return $this->successfulPaymentRedirect($transaction);
    }

    private function successfulPaymentRedirect(PaymentTransaction $transaction)
    {
        return redirect()->route('payment-gateway.web', [
            'transaction_id' => $transaction->transaction_id,
            'status' => 'success',
            'message' => trans('Payment Successful'),
        ]);
    }

    /**
     * Handle failed payment
     */
    private function handleFailedPayment($transaction, $message = 'Payment Failed')
    {
        $transaction->status = 'failed';
        $transaction->save();

        if ($transaction->for == 'order_payment') {
            $requestData = json_decode($transaction->request_data, true);
            $order = Order::where('id', $requestData['order_id'])->lockForUpdate()->first();
            if ($order && $order->status == 'pending_payment') {
                $order->update(['status' => 'failed_payment']);
            }
        }
        return redirect()->route('payment-gateway.web', [
            'transaction_id' => $transaction->transaction_id,
            'status' => 'failed',
            'message' => $message,
        ]);
    }

    /**
     * Handle canceled payment
     */
    private function handleCancelPayment($transaction, $message = 'Payment Canceled')
    {
        $transaction->status = 'canceled';
        $transaction->save();

        if ($transaction->for == 'order_payment') {
            $requestData = json_decode($transaction->request_data, true);
            $order = Order::where('id', $requestData['order_id'])->lockForUpdate()->first();
            if ($order && $order->status == 'pending_payment') {
                $order->update(['status' => 'cancel_payment']);
            }
        }
        return redirect()->route('payment-gateway.web', [
            'transaction_id' => $transaction->transaction_id,
            'status' => 'cancel',
            'message' => $message,
        ]);
    }

    /**
     * Get formatted payment method name
     */
    private function getPaymentMethodName($paymentType): string
    {
        return match ($paymentType) {
            'ApplePay' => 'applepay',
            default => 'card',
        };
    }

}
