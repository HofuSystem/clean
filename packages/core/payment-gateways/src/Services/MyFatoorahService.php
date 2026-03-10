<?php

namespace Core\PaymentGateways\Services;

use Core\PaymentGateways\Models\PaymentTransaction;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MyFatoorahService
{
    protected $apiKey;
    protected $baseUrl;
    protected $countryCode;

    public function __construct()
    {
        $this->apiKey      = config('services.myfatoorah.api_key');
        $this->baseUrl     = config('services.myfatoorah.base_url');
        $this->countryCode = config('services.myfatoorah.country_code', 'SAU');
    }

    /**
     * Check if MyFatoorah is configured
     */
    public function isConfigured(): bool
    {
        return !empty($this->apiKey) && !empty($this->baseUrl);
    }

    /**
     * Create a payment transaction and return the payment gateway URL
     */
    public function createTransaction($amount, $orderId, $requestData, $userId, $for, $prefix = null)
    {
        $requestData['order_id'] = $orderId;
        $paymentTransaction      = PaymentTransaction::create([
            'transaction_id' => $prefix ? $prefix . Str::random(2) : Str::random(10),
            'for'            => $for,
            'status'         => 'pending',
            'request_data'   => json_encode($requestData),
            'amount'         => $amount,
            'user_id'        => $userId,
        ]);

        $locale = app()->getLocale();

        return url($locale . '/payment-gateway/' . $paymentTransaction->transaction_id);
    }

    /**
     * Get all payment method IDs from config
     */
    public function getAllPaymentMethodIds(float $amount): array
    {
        $configIds = config('services.myfatoorah.payment_method_ids', []);
        
        return [
            'apple_pay_id'  => $configIds['apple_pay_id'] ?? null,
            'mada_id'       => $configIds['mada_id'] ?? null,
            'google_pay_id' => $configIds['google_pay_id'] ?? null,
        ];
    }

    /**
     * Initiate embedded payment session with MyFatoorah via direct API call
     */
    public function initiateSession(array $params): array
    {
        try {
            // Validate required parameters
            $requiredParams = ['amount', 'transaction_id'];
            foreach ($requiredParams as $param) {
                if (!isset($params[$param])) {
                    throw new \InvalidArgumentException("Missing required parameter: {$param}");
                }
            }

            $customerData = $this->prepareCustomerData($params);
            
            $postFields = [
                'CustomerName'       => $customerData['name'],
                'InvoiceValue'       => $params['amount'],
                'DisplayCurrencyIso' => 'SAR',
                'MobileCountryCode'  => '+966',
                'CustomerMobile'     => $customerData['mobile'],
                'CustomerEmail'      => $customerData['email'],
                'Language'           => $params['language'] ?? 'en',
                'CustomerReference'  => $params['transaction_id'],
                'UserDefinedField'   => $params['transaction_id'],
            ];
            
            Log::info('InitiateSession request', [
                'url' => $this->baseUrl . '/v2/InitiateSession',
                'fields' => $postFields
            ]);
            
            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type'  => 'application/json',
                ])
                ->post($this->baseUrl . '/v2/InitiateSession', $postFields);

            Log::info('InitiateSession response', [
                'status' => $response->status(),
                'body' => $response->json()
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['IsSuccess']) && $data['IsSuccess'] === true) {
                    Log::info('Session created successfully', [
                        'session_id' => $data['Data']['SessionId'] ?? null,
                        'country_code' => $data['Data']['CountryCode'] ?? null
                    ]);
                    
                    return [
                        'success'      => true,
                        'session_id'   => $data['Data']['SessionId'] ?? null,
                        'country_code' => $data['Data']['CountryCode'] ?? $this->countryCode,
                        'data'         => $data['Data'] ?? null,
                        'error'        => null
                    ];
                }
                
                $errorMessage = $data['Message'] ?? 'Failed to initiate session';
                if (!empty($data['ValidationErrors'])) {
                    $errorMessage .= ': ' . $this->formatValidationErrors($data['ValidationErrors']);
                }
                
                throw new \Exception($errorMessage);
            }
            
            $errorData = $response->json();
            $errorMessage = $errorData['Message'] ?? 'API request failed';
            if (!empty($errorData['ValidationErrors'])) {
                $errorMessage .= ': ' . $this->formatValidationErrors($errorData['ValidationErrors']);
            }
            
            throw new \Exception($errorMessage);
            
        } catch (\Exception $e) {
            Log::error('Session initiation error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success'      => false,
                'session_id'   => null,
                'country_code' => null,
                'data'         => null,
                'error'        => 'An error occurred: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Execute embedded payment with card token via direct API call
     */
    public function executeEmbeddedPayment(array $params): array
    {
        try {
            // Validate required parameters
            $requiredParams = ['session_id', 'amount', 'transaction_id'];
            foreach ($requiredParams as $param) {
                if (!isset($params[$param])) {
                    throw new \InvalidArgumentException("Missing required parameter: {$param}");
                }
            }

            $customerData = $this->prepareCustomerData($params);
            
            $executeFields = [
                'SessionId'          => $params['session_id'],
                'InvoiceValue'       => $params['amount'],
                'CallBackUrl'        => $params['callback_url'] ?? '',
                'ErrorUrl'           => $params['callback_url'] ?? '',
                'CustomerName'       => $customerData['name'],
                'DisplayCurrencyIso' => 'SAR',
                'MobileCountryCode'  => '+966',
                'CustomerMobile'     => $customerData['mobile'],
                'CustomerEmail'      => $customerData['email'],
                'Language'           => $params['language'] ?? 'en',
                'CustomerReference'  => $params['transaction_id'],
            ];
            
            if (!empty($params['card_identifier'])) {
                $executeFields['CardIdentifier'] = $params['card_identifier'];
            }
            if (!empty($params['payment_method_id'])) {
                $executeFields['PaymentMethodId'] = $params['payment_method_id'];
            }
            
            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type'  => 'application/json',
                ])
                ->post($this->baseUrl . '/v2/ExecutePayment', $executeFields);

            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['IsSuccess']) && $data['IsSuccess'] === true) {
                    $invoiceId     = $data['Data']['InvoiceId'] ?? null;
                    $invoiceStatus = $data['Data']['InvoiceStatus'] ?? 'Pending';
                    $paymentURL    = $data['Data']['PaymentURL'] ?? null;
                    
                    // Always return payment URL when API provides it — frontend redirects the user
                    return [
                        'success'        => true,
                        'invoice_id'     => $invoiceId,
                        'payment_id'     => $invoiceId,
                        'invoice_status' => $invoiceStatus,
                        'payment_url'    => !empty($paymentURL) ? $paymentURL : null,
                        'data'           => $data['Data'] ?? null,
                        'error'          => null
                    ];
                }
                
                $errorMessage = $data['Message'] ?? 'Failed to execute payment';
                if (!empty($data['ValidationErrors'])) {
                    $errorMessage .= ': ' . $this->formatValidationErrors($data['ValidationErrors']);
                }
                
                throw new \Exception($errorMessage);
            }
            
            $errorData = $response->json();
            $errorMessage = $errorData['Message'] ?? 'API request failed';
            if (!empty($errorData['ValidationErrors'])) {
                $errorMessage .= ': ' . $this->formatValidationErrors($errorData['ValidationErrors']);
            }
            
            throw new \Exception($errorMessage);
            
        } catch (\Exception $e) {
            Log::error('Embedded payment execution error', [
                'transaction_id' => $params['transaction_id'] ?? 'unknown',
                'error'          => $e->getMessage(),
                'trace'          => $e->getTraceAsString()
            ]);
            
            return [
                'success'        => false,
                'invoice_id'     => null,
                'payment_id'     => null,
                'invoice_status' => null,
                'payment_url'    => null,
                'data'           => null,
                'error'          => 'An error occurred: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get payment status from MyFatoorah
     */
    public function getPaymentStatus(string $paymentId, string $keyType = 'InvoiceId'): array
    {
        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type'  => 'application/json',
                ])
                ->post($this->baseUrl . '/v2/GetPaymentStatus', [
                    'Key'     => $paymentId,
                    'KeyType' => $keyType,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['IsSuccess']) && $data['IsSuccess'] === true) {
                    $invoiceStatus = $data['Data']['InvoiceStatus'] ?? '';
                    
                    return [
                        'success' => true,
                        'status'  => $invoiceStatus,
                        'data'    => $data['Data'] ?? null,
                        'error'   => null
                    ];
                }
            }
            
            $errorData = $response->json();
            $errorMessage = $errorData['Message'] ?? 'Unable to verify payment status';
            
            // Retry with PaymentId if InvoiceId didn't match
            if (stripos($errorMessage, 'No data match') !== false && $keyType === 'InvoiceId') {
                return $this->getPaymentStatus($paymentId, 'PaymentId');
            }
            
            return [
                'success' => false,
                'status'  => null,
                'data'    => null,
                'error'   => $errorMessage
            ];
            
        } catch (\Exception $e) {
            Log::error('Get payment status error', [
                'payment_id' => $paymentId,
                'error'      => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'status'  => null,
                'data'    => null,
                'error'   => 'An error occurred: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Prepare customer data with validation and defaults
     */
    protected function prepareCustomerData(array $params): array
    {
        $customerName   = $params['customer_name'] ?? 'Guest Customer';
        $customerEmail  = $params['customer_email'] ?? null;
        $customerMobile = $params['customer_mobile'] ?? null;
        
        // Validate and set default email
        if (empty($customerEmail) || !filter_var($customerEmail, FILTER_VALIDATE_EMAIL)) {
            $customerEmail = 'customer-' . $params['transaction_id'] . '@placeholder.local';
        }
        
        // Validate and format mobile number
        if (empty($customerMobile)) {
            $customerMobile = '500000000';
        } else {
            $customerMobile = preg_replace('/[^0-9]/', '', $customerMobile);
            $customerMobile = ltrim($customerMobile, '966');
            $customerMobile = ltrim($customerMobile, '0');
            if (strlen($customerMobile) < 9) {
                $customerMobile = '500000000';
            }
        }
        
        return [
            'name'   => $customerName,
            'email'  => $customerEmail,
            'mobile' => $customerMobile
        ];
    }

    /**
     * Format validation errors from MyFatoorah API response
     */
    protected function formatValidationErrors(array $errors): string
    {
        $messages = [];
        foreach ($errors as $error) {
            if (is_array($error)) {
                $messages[] = $error['Error'] ?? $error['Name'] ?? json_encode($error);
            } else {
                $messages[] = $error;
            }
        }
        return implode(', ', $messages);
    }
    public function getMessage($result){
        try{
        $InvoiceTransactions = $result['data']['InvoiceTransactions'] ?? [];
        $InvoiceTransaction = end($InvoiceTransactions);
        $statusCode = $InvoiceTransaction['ErrorCode']; 
        if($statusCode == 'MF001'){
            return trans('3DS authentication failed, possible reasons (user inserted a wrong password, cardholder/card issuer are not enrolled with 3DS, or the issuer bank has technical issue).');
        }
        if($statusCode == 'MF002'){
            return trans('The issuer bank has declined the transaction');
        }
        if($statusCode == 'MF003'){
            return trans('The transaction has been blocked from the gateway');
        }
        if($statusCode == 'MF004'){
            return trans('Insufficient funds');
        }
        if($statusCode == 'MF005'){
            return trans('Session timeout');
        }
        if($statusCode == 'MF006'){
            return trans('Transaction canceled');
        }
        if($statusCode == 'MF007'){
            return trans('The card is expired');
        }
        if($statusCode == 'MF008'){
            return trans('The card issuer doesn\'t respond');
        }
        if($statusCode == 'MF009'){
            return trans('Denied by Risk');
        }
        if($statusCode == 'MF010'){
            return trans('Wrong Security Code');
        }
        if($statusCode == 'MF020'){
            return trans('Unspecified Failure');
        }
        }catch(\Exception $e){
            return trans('payment failed');
        }
    }
}
