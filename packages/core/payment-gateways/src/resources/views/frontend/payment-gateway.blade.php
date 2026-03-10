@if (isset($transaction) and $transaction->status == 'pending')

<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('Secure Payment') }}</title>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- MyFatoorah Payment SDK -->
    @if (isset($sessionId) && $sessionId)
        @php
            $baseUrl = config('services.myfatoorah.base_url');
            $isTest = strpos($baseUrl, 'demo') !== false || strpos($baseUrl, 'test') !== false;
            $countryCodeLower = strtolower($countryCode ?? 'SAU');

            // SDK URL based on environment and country
            // Test Environment
            if ($isTest) {
                $sdkJsUrl = 'https://demo.myfatoorah.com/payment/v1/session.js';
            } else {
                // Live Environment - based on country
                switch ($countryCodeLower) {
                    case 'kwt': // Kuwait
                    case 'bhr': // Bahrain
                    case 'jor': // Jordan
                    case 'omn': // Oman
                        $sdkJsUrl = 'https://portal.myfatoorah.com/payment/v1/session.js';
                        break;
                    case 'are': // UAE
                        $sdkJsUrl = 'https://ae.myfatoorah.com/payment/v1/session.js';
                        break;
                    case 'sau': // Saudi Arabia
                        $sdkJsUrl = 'https://sa.myfatoorah.com/payment/v1/session.js';
                        break;
                    case 'qat': // Qatar
                        $sdkJsUrl = 'https://qa.myfatoorah.com/payment/v1/session.js';
                        break;
                    case 'egy': // Egypt
                        $sdkJsUrl = 'https://eg.myfatoorah.com/payment/v1/session.js';
                        break;
                    default:
                        // Default to Saudi Arabia
                        $sdkJsUrl = 'https://sa.myfatoorah.com/payment/v1/session.js';
                        break;
                }
            }
        @endphp
        <script src="{{ $sdkJsUrl }}"></script>
    @endif

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Helvetica Neue', sans-serif;
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 50%, #7e8ba3 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .payment-container {
            background: white;
            border-radius: 24px;
            box-shadow: 0 30px 80px rgba(0, 0, 0, 0.25);
            padding: 0;
            max-width: 480px;
            width: 100%;
            overflow: hidden;
        }

        .payment-body {
            padding: 30px 15px;
        }

        .payment-info {
            background: #f8f9fb;
            padding: 20px;
            border-radius: 16px;
            margin-bottom: 25px;
        }

        .payment-info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }

        .payment-info-row:last-child {
            margin-bottom: 0;
        }

        .payment-info-label {
            color: #6b7280;
            font-size: 14px;
        }

        .payment-info-value {
            color: #1f2937;
            font-weight: 600;
            font-size: 14px;
        }

        .amount-highlight {
            font-size: 18px;
            color: #1e3c72;
        }

        .status-message {
            margin-bottom: 25px;
            padding: 16px;
            border-radius: 12px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .status-message.success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #6ee7b7;
        }

        .status-message.cancel {
            background: #fef3c7;
            color: #92400e;
            border: 1px solid #fcd34d;
        }

        .status-message.failed {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }

        .status-icon {
            font-size: 24px;
        }

        .payment-form-title {
            font-size: 16px;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 16px;
        }

        #embedded-payment {
            min-height: 300px;
        }

        .secure-badge {
            text-align: center;
            padding: 20px 0 0;
            color: #9ca3af;
            font-size: 13px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .secure-icon {
            font-size: 16px;
        }

        .loading {
            display: none;
            text-align: center;
            padding: 20px;
        }

        .loading.active {
            display: block;
        }

        .spinner {
            border: 3px solid #f3f4f6;
            border-top: 3px solid #1e3c72;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto 15px;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .no-methods {
            text-align: center;
            padding: 30px;
            color: #6b7280;
        }

        .no-methods-icon {
            font-size: 48px;
            margin-bottom: 15px;
        }

        /* 3DS/OTP Iframe Modal */
        .iframe-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.8);
            z-index: 99999;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .iframe-modal.active {
            display: flex;
        }

        .iframe-container {
            background: white;
            border-radius: 16px;
            width: 100%;
            max-width: 500px;
            max-height: 90vh;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        .iframe-header {
            background: #1e3c72;
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .iframe-header h3 {
            font-size: 16px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .iframe-close-btn {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            padding: 8px 16px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            transition: background 0.3s;
        }

        .iframe-close-btn:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        .iframe-content {
            height: 500px;
        }

        .iframe-content iframe {
            width: 100%;
            height: 100%;
            border: none;
        }

        [dir="rtl"] .status-message {
            flex-direction: row-reverse;
        }

        @media (max-width: 480px) {
            .payment-container {
                border-radius: 16px;
            }

            .payment-body {
                padding: 20px;
            }

            .iframe-container {
                max-height: 80vh;
            }

            .iframe-content {
                height: 400px;
            }
        }
    </style>
</head>

<body>
    <div class="payment-container">
        <div class="payment-body">
            @if (isset($transaction) && $transaction)
                <div class="payment-info">
                    @if (isset($transaction->request_data['order_id']))
                        <div class="payment-info-row">
                            <span class="payment-info-label">{{ __('Order ID') }}</span>
                            <span class="payment-info-value">#{{ $transaction->request_data['order_id'] }}</span>
                        </div>
                    @endif
                    <div class="payment-info-row">
                        <span class="payment-info-label">{{ __('Amount') }}</span>
                        <span class="payment-info-value amount-highlight">{{ number_format($transaction->amount, 2) }}
                            {{ __('SAR') }}</span>
                    </div>
                </div>
            @endif

            @if (isset($status) && $status)
                <div class="status-message {{ $status }}">
                    @if ($status == 'success')
                        <span class="status-icon">✓</span>
                        <div>
                            <strong>{{ __('Payment Successful!') }}</strong>
                            <p>{{ __('Your payment has been processed successfully.') }}</p>
                        </div>
                    @elseif($status == 'cancel')
                        <span class="status-icon">⚠</span>
                        <div>
                            <strong>{{ __('Payment Cancelled') }}</strong>
                            <p>{{ __('The payment was cancelled.') }}</p>
                        </div>
                    @elseif($status == 'failed')
                        <span class="status-icon">✗</span>
                        <div>
                            <strong>{{ __('Payment Failed') }}</strong>
                            <p>{{ $message ?? __('An error occurred while processing your payment.') }}</p>
                        </div>
                    @endif
                </div>
            @else
                @if (isset($configError) && $configError)
                    <div class="no-methods">
                        <div class="no-methods-icon">⚙️</div>
                        <p><strong>{{ __('Configuration Error') }}</strong></p>
                        <p style="font-size: 14px; margin-top: 10px;">
                            {{ __('Payment gateway is not properly configured.') }}</p>
                        <p style="font-size: 12px; margin-top: 10px; color: #dc2626;">
                            {{ __('Please ensure MYFATOORAH_API_KEY is set in your environment file.') }}
                        </p>
                    </div>
                @elseif(isset($sessionId) && $sessionId)
                    <div class="payment-form-title">{{ __('Complete Your Payment') }}</div>

                    <!-- Embedded Payment Container (Step 3 from docs) -->
                    <div id="embedded-payment"></div>

                    <div class="loading" id="payment-loading">
                        <div class="spinner"></div>
                        <p>{{ __('Processing payment...') }}</p>
                    </div>
                @else
                    <div class="no-methods">
                        <div class="no-methods-icon">⚠️</div>
                        <p><strong>{{ __('Unable to Initialize Payment') }}</strong></p>
                        @if (isset($apiError) && $apiError)
                            <p style="font-size: 13px; margin-top: 10px; color: #dc2626;">{{ $apiError }}</p>
                        @endif
                        <p style="font-size: 12px; margin-top: 15px;">
                            {{ __('Please contact support if this problem persists.') }}</p>
                    </div>
                @endif
            @endif

            <div class="secure-badge">
                <span class="secure-icon">🔒</span>
                <span>{{ __('Powered by clean station - Secure Payment Gateway') }}</span>
            </div>
        </div>
    </div>

    <!-- 3DS/OTP Iframe Modal -->
    <div class="iframe-modal" id="iframe-modal">
        <div class="iframe-container">
            <div class="iframe-header">
                <h3>🔐 {{ __('Secure Verification') }}</h3>
                <button class="iframe-close-btn" onclick="closeIframeModal()">{{ __('Cancel') }}</button>
            </div>
            <div class="iframe-content">
                <iframe id="payment-iframe" src="about:blank"></iframe>
            </div>
        </div>
    </div>

    @if (isset($sessionId) && $sessionId)
        <script>
            // =====================================================
            // Step 1: Configuration variables from InitiateSession
            // =====================================================
            var sessionId = '{{ $sessionId }}';
            var countryCode = '{{ $countryCode ?? 'SAU' }}';
            var currencyCode = 'SAR';
            var amount = '{{ $transaction->amount }}';
            var transactionId = '{{ $transaction->transaction_id }}';
            var callbackUrl =
            '{{ route('payment-gateway.web.callback', ['transaction_id' => $transaction->transaction_id]) }}';
            var executePaymentUrl =
                '{{ route('payment-gateway.web.execute-embedded', ['transaction_id' => $transaction->transaction_id]) }}';
            var csrfToken = '{{ csrf_token() }}';
            var currentLanguage = '{{ app()->getLocale() }}';
            var paymentLanguage = (currentLanguage === 'ar') ? 'ar' : 'en';


            // =====================================================
            // Step 4: Configure the Embedded Payment
            // =====================================================
            var config = {
                sessionId: sessionId, // From InitiateSession
                countryCode: countryCode, // From InitiateSession
                currencyCode: currencyCode, // Payment currency
                amount: amount, // Displayed on ApplePay/GooglePay/STCPay
                callback: payment, // Callback function (Step 5)
                containerId: "embedded-payment", // Div ID from Step 3
                paymentOptions: ["ApplePay", "Card"], // Payment methods to display
                language: paymentLanguage, // Use app locale (en or ar)
                settings: {
                    card: {
                        style: {
                            button: {
                                useCustomButton: false,
                                textContent: "{{ __('Pay Now') }}",
                                fontSize: "18px",
                                fontFamily: "inherit",
                                color: "white",
                                backgroundColor: "#203760",
                                height: "52px",
                                borderRadius: "12px",
                                width: "100%",
                                margin: "0 auto",
                                cursor: "pointer"
                            }
                        }
                    },
                    applePay: {
                        language: paymentLanguage
                    }
                }
            };

            // =====================================================
            // UI Helper Functions
            // =====================================================
            var loadingDiv = document.getElementById('payment-loading');
            var iframeModal = document.getElementById('iframe-modal');
            var paymentIframe = document.getElementById('payment-iframe');

            function showLoading() {
                if (loadingDiv) loadingDiv.classList.add('active');
            }

            function hideLoading() {
                if (loadingDiv) loadingDiv.classList.remove('active');
            }

            function showIframeModal(url) {
                console.log('Opening 3DS/OTP iframe:', url);
                paymentIframe.src = url;
                iframeModal.classList.add('active');
            }

            function closeIframeModal() {
                console.log('Closing iframe modal');
                iframeModal.classList.remove('active');
                paymentIframe.src = 'about:blank';
                hideLoading();
            }

            // =====================================================
            // Step 5: Handle the callback function
            // =====================================================
            function payment(response) {

                if (response.isSuccess) {
                    showLoading();
                    var paymentType = response.paymentType || 'Card';
                    console.log(paymentType + ' : ' + JSON.stringify(response));
                    // Step 6: Send SessionId to backend for ExecutePayment
                    executePayment(response, paymentType);
                } else {
                    console.error('Payment failed:', response);
                    hideLoading();
                    var errorMessage = '{{ __('Payment failed. Please try again.') }}';
                    if (response.error) {
                        errorMessage = response.error;
                    }
                    alert('❌ ' + errorMessage);
                }
            }

            // =====================================================
            // Step 6: Call ExecutePayment Endpoint via Backend
            // =====================================================
            function executePayment(response, paymentType) {
                // Prepare payload - send SessionId to backend
                // Backend will call ExecutePayment endpoint
                var payload = {
                    session_id: response.sessionId,
                    card_brand: response.cardBrand || (response.card ? response.card.brand : null),
                    card_identifier: response.cardIdentifier,
                    payment_type: paymentType
                };

                $.ajax({
                    url: executePaymentUrl,
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    data: JSON.stringify(payload),
                    dataType: 'text', // Get raw text to check for dd output
                    success: function(responseText, status, xhr) {

                        // Check for dd/dump output
                        if (isDebugOutput(responseText)) {
                            console.log('Debug output detected!');
                            showDebugOutput(responseText);
                            return;
                        }

                        // Parse JSON response
                        var data;
                        try {
                            data = JSON.parse(responseText);
                        } catch (e) {
                            console.error('Failed to parse JSON');
                            showDebugOutput(responseText);
                            return;
                        }


                        if (data.success) {
                            // Always redirect user when payment URL is returned
                            if (data.payment_url) {
                                window.location.href = data.payment_url;
                                return;
                            }

                            // No payment URL: payment completed (e.g. paid) — redirect to callback to confirm
                            if (data.status === 'paid' || data.status === 'Paid') {
                                window.location.href = callbackUrl + '?paymentId=' + data.payment_id;
                                return;
                            }

                            hideLoading();
                        } else {
                            hideLoading();
                            var errorMsg = data.message || data.error ||
                                '{{ __('Payment failed. Please try again.') }}';
                            console.error('Payment failed:', errorMsg);
                            alert('❌ ' + errorMsg);
                        }
                    },
                    error: function(xhr, status, error) {
                        hideLoading();
                        console.error('AJAX error:', {
                            status: status,
                            error: error
                        });

                        if (xhr.responseText && isDebugOutput(xhr.responseText)) {
                            showDebugOutput(xhr.responseText);
                            return;
                        }

                        var errorMsg = '{{ __('An error occurred. Please try again.') }}';
                        if (xhr.responseJSON) {
                            errorMsg = xhr.responseJSON.message || xhr.responseJSON.error || errorMsg;
                        }
                        alert('❌ ' + errorMsg);
                    }
                });
            }

            // =====================================================
            // Event Listener for 3DS Iframe Redirection
            // After customer completes OTP/3DS, get redirection URL
            // =====================================================
            window.addEventListener("message", function(event) {
                if (!event.data) return;

                try {
                    var message = JSON.parse(event.data);

                    // Only proceed if sender is "MF-3DSecure"
                    if (message.sender === "MF-3DSecure") {
                        console.log('3DS message received:', message);
                        var redirectUrl = message.url;

                        if (redirectUrl) {
                            console.log('3DS completed, redirect URL:', redirectUrl);

                            // Close iframe and redirect to callback
                            closeIframeModal();
                            showLoading();

                            // Redirect to the callback URL
                            window.location.href = redirectUrl;
                        }
                    }
                } catch (error) {
                    // Not a JSON message, ignore
                    return;
                }
            }, false);

            // =====================================================
            // Additional Callback Functions
            // =====================================================
            function onSessionStarted() {
                console.log('Payment session started');
            }

            function onSessionCanceled() {
                console.log('Payment session canceled');
                hideLoading();
            }

            function onStcSessionStarted(data) {
                console.log('STC Session Started:', data);
            }

            function onCardBinChanged(response) {
                console.log('Card BIN changed:', response);
            }

            // Update amount (for dynamic pricing)
            function updateAmount(newAmount) {
                myfatoorah.updateAmount(newAmount);
            }

            // Custom submit (if using custom button)
            function customSubmit() {
                myfatoorah.submitCardPayment();
            }

            // =====================================================
            // Debug Output Functions
            // =====================================================
            function showDebugOutput(content) {
                var existingModal = document.getElementById('debug-modal');
                if (existingModal) existingModal.remove();

                var modal = document.createElement('div');
                modal.id = 'debug-modal';
                modal.style.cssText =
                    'position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.8);z-index:999999;overflow:auto;padding:20px;';

                var container = document.createElement('div');
                container.style.cssText = 'background:#1e1e1e;border-radius:8px;max-width:95%;margin:0 auto;';

                var header = document.createElement('div');
                header.style.cssText =
                    'background:#333;padding:10px 15px;border-radius:8px 8px 0 0;display:flex;justify-content:space-between;align-items:center;';
                header.innerHTML = '<span style="color:#ff6b6b;font-weight:bold;font-family:monospace;">🐛 Debug Output</span>';

                var closeBtn = document.createElement('button');
                closeBtn.innerHTML = '✕ Close';
                closeBtn.style.cssText =
                    'background:#ff6b6b;color:white;border:none;padding:5px 15px;border-radius:4px;cursor:pointer;';
                closeBtn.onclick = function() {
                    modal.remove();
                    hideLoading();
                };
                header.appendChild(closeBtn);

                var contentDiv = document.createElement('div');

                if (content.indexOf('<') !== -1 && content.indexOf('>') !== -1) {
                    var iframe = document.createElement('iframe');
                    iframe.style.cssText = 'width:100%;height:80vh;border:none;background:white;';
                    contentDiv.appendChild(iframe);
                    container.appendChild(header);
                    container.appendChild(contentDiv);
                    modal.appendChild(container);
                    document.body.appendChild(modal);
                    iframe.contentDocument.open();
                    iframe.contentDocument.write(content);
                    iframe.contentDocument.close();
                } else {
                    var pre = document.createElement('pre');
                    pre.style.cssText =
                        'color:#00ff00;padding:15px;margin:0;font-family:monospace;font-size:13px;white-space:pre-wrap;';
                    pre.textContent = content;
                    contentDiv.appendChild(pre);
                    container.appendChild(header);
                    container.appendChild(contentDiv);
                    modal.appendChild(container);
                    document.body.appendChild(modal);
                }
            }

            function isDebugOutput(text) {
                if (!text || typeof text !== 'string') return false;
                return text.indexOf('sf-dump') !== -1 ||
                    text.indexOf('Sfdump') !== -1 ||
                    text.indexOf('<!DOCTYPE html>') !== -1 ||
                    text.indexOf('<pre>') !== -1 ||
                    text.indexOf('xdebug') !== -1;
            }

            // =====================================================
            // Initialize MyFatoorah SDK
            // =====================================================
            function initPayment() {
                if (typeof myfatoorah !== 'undefined') {
                    try {
                        console.log('Initializing MyFatoorah SDK...');
                        myfatoorah.init(config);
                        console.log('✓ MyFatoorah SDK initialized successfully');
                    } catch (e) {
                        console.error('✗ MyFatoorah init error:', e);
                        alert('{{ __('Payment form could not be initialized. Please refresh the page.') }}');
                    }
                } else {
                    console.error('✗ MyFatoorah SDK not loaded');
                    alert('{{ __('Payment system could not be loaded. Please refresh the page.') }}');
                }
            }

            // Start when page loads
            if (document.readyState === 'complete') {
                initPayment();
            } else {
                window.addEventListener('load', initPayment);
            }
        </script>
    @endif
</body>

</html>
@endif
