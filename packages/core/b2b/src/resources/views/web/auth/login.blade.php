<!DOCTYPE html>
<html lang="{{app()->getLocale()}}" dir="{{app()->getLocale() == 'ar' ? 'rtl' : 'ltr'}}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }}</title>
    <meta name="title" content="{{ $title }}">
    <meta name="description" content="{{ $description }}">
    <link rel="icon" href="{{ config('app.icon') }}">
    <link rel="stylesheet" href="{{ asset('client/css/app.css') }}">
    <style>
        /* Login-specific styles */
        .login-shake {
            animation: shake 0.5s cubic-bezier(0.36, 0.07, 0.19, 0.97) both;
        }

        @keyframes shake {

            10%,
            90% {
                transform: translateX(-2px);
            }

            20%,
            80% {
                transform: translateX(4px);
            }

            30%,
            50%,
            70% {
                transform: translateX(-6px);
            }

            40%,
            60% {
                transform: translateX(6px);
            }
        }

        .field-error {
            border-color: #ef4444 !important;
            background-color: #fef2f2 !important;
        }

        .field-error:focus {
            ring-color: rgba(239, 68, 68, 0.15) !important;
        }

        .field-success {
            border-color: #22c55e !important;
        }

        .error-msg {
            color: #ef4444;
            font-size: 0.75rem;
            font-weight: 700;
            margin-top: 0.375rem;
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .error-msg svg {
            width: 14px;
            height: 14px;
            flex-shrink: 0;
        }

        .login-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 0.7s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .password-toggle {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            padding: 4px;
            color: #9ca3af;
            transition: color 0.2s;
        }

        [dir="rtl"] .password-toggle {
            left: unset;
            right: 14px;
        }

        .password-toggle:hover {
            color: #4b5563;
        }

        /* Login card entrance animation (no translate-x to avoid conflicting with flexbox centering) */
        @keyframes loginCardIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-login-card {
            animation: loginCardIn 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
        }

        /* Mobile branding banner */
        .mobile-brand-banner {
            display: none;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            padding: 1.5rem;
            text-align: center;
            color: white;
            border-radius: 2rem 2rem 0 0;
        }

        /* Alert banner */
        .error-banner {
            background: linear-gradient(135deg, #fef2f2, #fee2e2);
            border: 1px solid #fecaca;
            border-radius: 1rem;
            padding: 0.875rem 1rem;
            margin-bottom: 1.5rem;
            animation: fadeIn 0.3s ease;
        }

        /* Safe area for notched devices */
        @supports (padding: env(safe - area - inset - bottom)) {
            .login-wrapper-safe {
                padding-bottom: env(safe-area-inset-bottom);
            }
        }

        /* Mobile responsive overrides */
        @media (max-width: 1023px) {
            .mobile-brand-banner {
                display: block;
            }
        }

        @media (max-width: 640px) {
            .login-card {
                border-radius: 1.5rem !important;
                margin: 0.5rem;
                box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.12) !important;
            }

            .login-card .login-form-area {
                padding: 1.5rem !important;
            }

            .login-card .login-form-area h1 {
                font-size: 1.5rem !important;
            }

            .login-card .login-form-area p {
                font-size: 0.8rem !important;
            }

            .login-card .login-form-area input[type="text"],
            .login-card .login-form-area input[type="password"] {
                padding: 0.875rem 1rem !important;
                font-size: 0.875rem !important;
            }

            .login-card .login-form-area button[type="submit"] {
                padding: 0.875rem 1.5rem !important;
                font-size: 1rem !important;
            }

            .login-card .login-form-area label.text-xs {
                font-size: 0.65rem !important;
            }

            .login-card .mobile-brand-banner {
                padding: 1.25rem 1rem;
                border-radius: 1.5rem 1.5rem 0 0;
            }

            .login-card .mobile-brand-banner h3 {
                font-size: 1rem !important;
            }

            .login-card .mobile-brand-banner p {
                font-size: 0.7rem !important;
            }
        }
    </style>
</head>

<body class="text-gray-900 flex flex-col min-h-screen relative">
    <!-- Toast Container -->
    <div id="toast-container"
        class="fixed top-8 left-1/2 -translate-x-1/2 z-[9999] flex flex-col gap-2 pointer-events-none"></div>

    <!-- LOGIN SCREEN WRAPPER -->
    <div id="login-wrapper"
        class="min-h-screen flex items-center justify-center bg-gray-50 relative overflow-hidden active px-3 sm:px-4 py-4 login-wrapper-safe">
        <!-- Background Decorations -->
        <div
            class="absolute top-0 right-0 w-[500px] sm:w-[800px] h-[500px] sm:h-[800px] bg-gradient-to-br from-[#1c75bc]/10 to-purple-500/10 rounded-full blur-3xl -translate-y-1/2 translate-x-1/3">
        </div>
        <div
            class="absolute bottom-0 left-0 w-[400px] sm:w-[600px] h-[400px] sm:h-[600px] bg-gradient-to-tr from-blue-500/10 to-[#1c75bc]/5 rounded-full blur-3xl translate-y-1/3 -translate-x-1/3">
        </div>

        <div
            class="login-card w-full max-w-5xl mx-auto bg-white/90 backdrop-blur-2xl rounded-[2rem] lg:rounded-[2.5rem] shadow-[0_30px_60px_-15px_rgba(0,0,0,0.15)] border border-white flex flex-col lg:flex-row-reverse overflow-hidden relative z-10 animate-login-card">

            <!-- Mobile Branding Banner (visible < lg) -->
            <div class="mobile-brand-banner">
                <div class="flex items-center justify-center gap-3 mb-2">
                    <div
                        class="w-10 h-10 bg-white/10 backdrop-blur-md border border-white/20 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-black">{{ __('client.b2b_title') }}</h3>
                </div>
                <p class="text-gray-300 text-xs font-medium">{{ __('client.b2b_tagline') }}</p>
            </div>

            <!-- Login Form Area -->
            <div
                class="login-form-area w-full lg:w-1/2 p-6 sm:p-8 md:p-14 flex flex-col justify-center items-center relative dir-dependent-text">
                <a href="{{ LaravelLocalization::getLocalizedURL(app()->getLocale() == 'ar' ? 'en' : 'ar', null, [], true) }}"
                    class="absolute top-4 right-4 sm:top-6 sm:right-6 md:top-8 md:right-8 border border-gray-200 bg-white rounded-xl px-3 sm:px-4 py-1.5 sm:py-2 text-[10px] sm:text-xs font-black tracking-widest uppercase hover:bg-gray-50 transition-all shadow-sm z-20">
                    {{ app()->getLocale() == 'ar' ? 'EN' : 'AR' }}
                </a>
                <div class="w-full max-w-sm">
                    <div
                        class="mb-6 sm:mb-10 flex flex-col items-center lg:items-start text-center lg:text-start dir-dependent-items">
                        <img src="https://i.postimg.cc/gxGfY6Z7/lwqw-msttyl-2-(1).png" alt="Clean Station Logo"
                            class="h-10 sm:h-14 w-auto object-contain mb-4 sm:mb-6 drop-shadow-sm">
                        <h1 class="text-2xl sm:text-3xl md:text-4xl font-black text-gray-900 tracking-tight mb-1 sm:mb-2"
                            data-i18n="login_title">{{ __('client.login_title') }}</h1>
                        <p class="text-gray-500 font-medium text-xs sm:text-sm md:text-base" data-i18n="login_subtitle">
                            {{ __('client.login_subtitle') }}
                        </p>
                    </div>

                    <!-- Global Error Banner -->
                    @if($errors->any())
                        <div class="error-banner" role="alert">
                            <div class="flex items-center gap-2">
                                <div class="flex-shrink-0 w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-red-800">{{ __('client.login_failed') }}</p>
                                    <p class="text-xs text-red-600 mt-0.5">{{ $errors->first() }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <form id="loginForm" action="{{ route('client.login') }}" method="POST"
                        class="space-y-4 sm:space-y-6" novalidate>
                        @csrf
                        <!-- Email / Phone Field -->
                        <div class="space-y-1.5 sm:space-y-2">
                            <label for="email"
                                class="text-xs font-bold text-gray-400 uppercase tracking-widest px-1 block mb-1"
                                data-i18n="phone_email_label">{{ __('client.phone_email_label') }}</label>
                            <input type="text" id="email" name="phone" value="{{ old('email') }}" required dir="ltr"
                                autocomplete="username"
                                class="w-full p-3 sm:p-4 bg-gray-50/80 border border-gray-200 rounded-2xl outline-none focus:border-[#1c75bc] focus:bg-white focus:ring-4 focus:ring-blue-50 transition-all font-bold text-gray-800 text-left @error('email') field-error @enderror"
                                placeholder="05XXXXXXXX / admin@hotel.com" />
                            @error('email')
                                <p class="error-msg">
                                    <svg fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                            <p id="email-client-error" class="error-msg" style="display:none;">
                                <svg fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                        clip-rule="evenodd" />
                                </svg>
                                <span></span>
                            </p>
                        </div>

                        <!-- Password Field -->
                        <div class="space-y-1.5 sm:space-y-2">
                            <label for="password"
                                class="text-xs font-bold text-gray-400 uppercase tracking-widest px-1 flex justify-between w-full mb-1">
                                <span data-i18n="password_label">{{ __('client.password_label') }}</span>
                            </label>
                            <div class="relative">
                                <input type="password" id="password" name="password" required dir="ltr"
                                    autocomplete="current-password" minlength="6"
                                    class="w-full p-3 sm:p-4 bg-gray-50/80 border border-gray-200 rounded-2xl outline-none focus:border-[#1c75bc] focus:bg-white focus:ring-4 focus:ring-blue-50 transition-all font-mono tracking-widest text-left @error('password') field-error @enderror"
                                    placeholder="••••••••" />
                                <button type="button" class="password-toggle" id="togglePassword"
                                    aria-label="{{ __('client.show_hide_password') }}">
                                    <svg id="eyeIcon" class="w-5 h-5" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <svg id="eyeOffIcon" class="w-5 h-5" style="display:none" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                    </svg>
                                </button>
                            </div>
                            @error('password')
                                <p class="error-msg">
                                    <svg fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                            <p id="password-client-error" class="error-msg" style="display:none;">
                                <svg fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                        clip-rule="evenodd" />
                                </svg>
                                <span></span>
                            </p>
                        </div>

                        <!-- Remember Me & Forgot Password -->
                        <div class="flex items-center justify-between pt-1 sm:pt-2 dir-dependent-flex">
                            <label class="flex items-center gap-2 cursor-pointer group" for="remember">
                                <input type="checkbox" id="remember" name="remember"
                                    class="w-4 h-4 text-[#1c75bc] rounded border-gray-300 focus:ring-[#1c75bc] transition-colors" />
                                <span
                                    class="text-xs sm:text-sm font-bold text-gray-600 group-hover:text-gray-900 transition-colors"
                                    data-i18n="remember_me">{{ __('client.remember_me') }}</span>
                            </label>
                            <a href="#" class="text-xs sm:text-sm font-bold text-[#1c75bc] hover:underline"
                                data-i18n="forgot_password">{{ __('client.forgot_password') }}</a>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" id="loginSubmitBtn"
                            class="w-full bg-gray-900 hover:bg-black text-white px-6 sm:px-8 py-3 sm:py-4 rounded-2xl text-base sm:text-lg font-black shadow-xl shadow-black/10 transition-all hover:-translate-y-1 active:translate-y-0 mt-2 sm:mt-4 flex justify-center items-center gap-2 disabled:opacity-70 disabled:cursor-not-allowed disabled:hover:translate-y-0">
                            <span id="btnText" data-i18n="sign_in_btn">{{ __('client.sign_in_btn') }}</span>
                            <svg id="btnArrow" class="w-5 h-5 rtl-rotate" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                            </svg>
                            <span id="btnSpinner" class="login-spinner" style="display:none;"></span>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Desktop Branding Image (visible >= lg) -->
            <div
                class="hidden lg:flex w-1/2 bg-gray-900 relative overflow-hidden p-12 flex-col justify-between items-end text-white">
                <img src="https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&q=80&w=1000&h=1000"
                    class="absolute inset-0 w-full h-full object-cover opacity-40 mix-blend-overlay" alt="{{ __('client.hotel_banner') }}">
                <div class="absolute inset-0 bg-gradient-to-t from-gray-900 via-gray-900/60 to-transparent"></div>
                <div
                    class="relative z-10 w-16 h-16 bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl flex items-center justify-center">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                        </path>
                    </svg>
                </div>
                <div class="relative z-10 dir-dependent-text">
                    <h2 class="text-3xl font-black mb-4 leading-tight">
                        {{ __('client.b2b_tagline') }}
                    </h2>
                    <p class="text-gray-300 font-medium text-sm max-w-sm leading-relaxed">
                        {{ __('client.b2b_description') }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Client-side validation & UX Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('loginForm');
            const emailInput = document.getElementById('email');
            const passwordInput = document.getElementById('password');
            const submitBtn = document.getElementById('loginSubmitBtn');
            const btnText = document.getElementById('btnText');
            const btnArrow = document.getElementById('btnArrow');
            const btnSpinner = document.getElementById('btnSpinner');
            const togglePassword = document.getElementById('togglePassword');
            const eyeIcon = document.getElementById('eyeIcon');
            const eyeOffIcon = document.getElementById('eyeOffIcon');
            const emailClientError = document.getElementById('email-client-error');
            const passwordClientError = document.getElementById('password-client-error');

            // Password toggle
            if (togglePassword) {
                togglePassword.addEventListener('click', function () {
                    const isPassword = passwordInput.type === 'password';
                    passwordInput.type = isPassword ? 'text' : 'password';
                    eyeIcon.style.display = isPassword ? 'none' : 'block';
                    eyeOffIcon.style.display = isPassword ? 'block' : 'none';
                });
            }

            // Helper: show/hide client error
            function showFieldError(input, errorEl, message) {
                input.classList.add('field-error');
                input.classList.remove('field-success');
                errorEl.querySelector('span').textContent = message;
                errorEl.style.display = 'flex';
            }

            function clearFieldError(input, errorEl) {
                input.classList.remove('field-error');
                errorEl.style.display = 'none';
            }

            function markFieldSuccess(input, errorEl) {
                input.classList.remove('field-error');
                input.classList.add('field-success');
                errorEl.style.display = 'none';
            }

            // Real-time validation on blur
            emailInput.addEventListener('blur', function () {
                if (!this.value.trim()) {
                    showFieldError(this, emailClientError, '{{ __('client.validation_email_required') }}');
                } else {
                    markFieldSuccess(this, emailClientError);
                }
            });

            emailInput.addEventListener('input', function () {
                if (this.value.trim()) {
                    clearFieldError(this, emailClientError);
                }
            });

            passwordInput.addEventListener('blur', function () {
                if (!this.value) {
                    showFieldError(this, passwordClientError, '{{ __('client.validation_password_required') }}');
                } else if (this.value.length < 6) {
                    showFieldError(this, passwordClientError, '{{ __('client.validation_password_min') }}');
                } else {
                    markFieldSuccess(this, passwordClientError);
                }
            });

            passwordInput.addEventListener('input', function () {
                if (this.value && this.value.length >= 6) {
                    clearFieldError(this, passwordClientError);
                }
            });

            // Form submission: client-side validation + loading state
            form.addEventListener('submit', function (e) {
                let hasError = false;

                // Validate email
                if (!emailInput.value.trim()) {
                    showFieldError(emailInput, emailClientError, '{{ __('client.validation_email_required') }}');
                    hasError = true;
                }

                // Validate password
                if (!passwordInput.value) {
                    showFieldError(passwordInput, passwordClientError, '{{ __('client.validation_password_required') }}');
                    hasError = true;
                } else if (passwordInput.value.length < 6) {
                    showFieldError(passwordInput, passwordClientError, '{{ __('client.validation_password_min') }}');
                    hasError = true;
                }

                if (hasError) {
                    e.preventDefault();
                    // Shake the form card
                    const card = document.querySelector('.login-card');
                    card.classList.add('login-shake');
                    setTimeout(() => card.classList.remove('login-shake'), 600);
                    return;
                }

                // Show loading state
                submitBtn.disabled = true;
                btnText.textContent = '{{ __('client.logging_in') }}';
                btnArrow.style.display = 'none';
                btnSpinner.style.display = 'inline-block';
            });

            // If there are server errors, shake the form on load
            @if($errors->any())
                const card = document.querySelector('.login-card');
                card.classList.add('login-shake');
                setTimeout(() => card.classList.remove('login-shake'), 600);
            @endif
    });
    </script>
</body>

</html>