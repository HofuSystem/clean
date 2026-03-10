@extends('layouts.auth')

@section('content')
<div class="login-container">
    <div class="hero-section" style="background-image: url('{{ asset('website/client/image/hero-login.jpg') }}');">
        <div class="hero-content login">
            <img src="{{ asset('website/client/image/bg.svg') }}" class="bg-hero" alt="">

            <div>
                <div class="hero-text">
                    <span class="hero-title">{{ __('auth.quality_life') }}</span>
                    <span>{{ __('auth.best_services') }}</span>

                </div>
            </div>
        </div>
    </div>
    <div class="login-form-section">
        <div class="logo">
            <a href="/">
                <img src="{{ asset('website/client/image/logo.svg') }}" alt="Clean Station Logo" class="logo-image">
            </a>
        </div>

        <div class="login-form">
            <h2 class="form-title">{{ __('auth.login_title') }}</h2>

            <form id="loginForm" method="POST" action="{{ route('client.login') }}">
                @csrf
                <div class="form-group">
                    <div class="phone-input-group">
                        <div class="country-code">
                            <span class="flag">
                                <img src="{{ asset('website/client/image/ksa.jpg') }}" width="22" alt="SA">
                            </span>
                            <span>+966</span>
                        </div>
                        <input type="number" name="phone" class="phone-input @error('phone') is-invalid @enderror" placeholder="{{ __('auth.phone_placeholder') }}" value="{{ old('phone') }}" required>
                    </div>
                    @error('phone')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-group">
                    <div class="password-group">
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" id="password" placeholder="{{ __('auth.password_placeholder') }}" required>
                        <button type="button" class="password-toggle">
                            <i class="far fa-eye" id="eyeIcon"></i>
                        </button>
                    </div>
                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                    {{-- <div class="forgot-password">
                        <a href="#">{{ __('auth.forgot_password') }}</a>
                    </div> --}}
                </div>

                <div class="d-flex justify-content-center">
                    <button type="submit" class="login-btn">{{ __('auth.login_button') }}</button>
                </div>
            </form>

            {{-- <div class="register-link">
                {{ __('auth.no_account') }} <a href="{{ route('client.register') }}">{{ __('auth.create_account') }}</a>
            </div> --}}
        </div>
    </div>


</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const passwordToggle = document.querySelector('.password-toggle');
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');

        passwordToggle.addEventListener('click', function () {
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        });
    });
</script>
@endsection
