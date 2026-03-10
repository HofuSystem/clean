@extends('layouts.auth')

@section('content')
<div class="login-container">
    <div class="login-form-section">

        <div class="login-form">
            <h2 class="form-title">{{ __('auth.register_title') }}</h2>

            <form id="signupForm" method="POST" action="{{ route('client.register') }}">
                @csrf
                <div class="form-group">
                    <input type="text" name="company_name" class="form-control @error('company_name') is-invalid @enderror" placeholder="{{ __('auth.company_name_placeholder') }}" value="{{ old('company_name') }}" required>
                    @error('company_name')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="d-flex gap-2">
                    <div class="form-group w-50">
                        <div class="phone-input-group">
                            <div class="country-code">
                                <span class="flag">
                                    <img src="{{ asset('website/client/image/ksa.jpg') }}" width="22" alt="SA">
                                </span>
                                <span>+966</span>
                            </div>
                            <input type="tel" name="phone" class="phone-input @error('phone') is-invalid @enderror" placeholder="{{ __('auth.phone_placeholder') }}" value="{{ old('phone') }}" required>
                        </div>
                        @error('phone')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="form-group w-50">
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="{{ __('auth.email_placeholder') }}" value="{{ old('email') }}" required>
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <input type="text" name="address" class="form-control @error('address') is-invalid @enderror" placeholder="{{ __('auth.address_placeholder') }}" value="{{ old('address') }}" required>
                    @error('address')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>



                <div class="form-group">
                    <div class="dropdown-group">
                        <select name="business_field" id="business_field" class="form-control dropdown-select @error('business_field') is-invalid @enderror" required>
                            <option value="" disabled selected>{{ __('auth.business_field_placeholder') }}</option>
                            <option value="cleaning" {{ old('business_field') == 'cleaning' ? 'selected' : '' }}>{{ __('auth.cleaning') }}</option>
                            <option value="maintenance" {{ old('business_field') == 'maintenance' ? 'selected' : '' }}>{{ __('auth.maintenance') }}</option>
                            <option value="hospitality" {{ old('business_field') == 'hospitality' ? 'selected' : '' }}>{{ __('auth.hospitality') }}</option>
                            <option value="healthcare" {{ old('business_field') == 'healthcare' ? 'selected' : '' }}>{{ __('auth.healthcare') }}</option>
                            <option value="education" {{ old('business_field') == 'education' ? 'selected' : '' }}>{{ __('auth.education') }}</option>
                            <option value="retail" {{ old('business_field') == 'retail' ? 'selected' : '' }}>{{ __('auth.retail') }}</option>
                            <option value="other" {{ old('business_field') == 'other' ? 'selected' : '' }}>{{ __('auth.other') }}</option>
                        </select>
                        <i class="fas fa-chevron-down dropdown-arrow"></i>
                    </div>
                    @error('business_field')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-group" id="other_business_field_container" style="display: none;">
                    <input type="text" name="other_business_field" class="form-control @error('other_business_field') is-invalid @enderror" placeholder="{{ __('auth.other_business_field_placeholder') }}" value="{{ old('other_business_field') }}">
                    @error('other_business_field')
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
                </div>

                <div class="form-group">
                    <div class="password-group">
                        <input type="password" name="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror" id="password_confirmation" placeholder="{{ __('auth.password_confirmation_placeholder') }}" required>
                        <button type="button" class="password-toggle">
                            <i class="far fa-eye" id="eyeIconConfirmation"></i>
                        </button>
                    </div>
                    @error('password_confirmation')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="d-flex justify-content-center">
                    <button type="submit" class="login-btn">{{ __('auth.register_button') }}</button>
                </div>
            </form>

            <div class="register-link">
                {{ __('auth.have_account') }} <a href="{{ route('client.login') }}">{{ __('auth.login_now') }}</a>
            </div>
        </div>
    </div>
    <div class="hero-section signUp" style="background-image: url('{{ asset('website/client/image/hero-signup.jpg') }}');">
        <div class="hero-content ">
            <img src="{{ asset('website/client/image/bg.svg') }}" class="bg-hero" alt="">
            <div>
                <img src="{{ asset('website/client/image/logo-white.svg') }}" class="hero-logo" alt="">
                <div class="hero-text">
                    <span class="hero-title">{{ __('auth.quality_life') }}</span>
                    <span>{{ __('auth.best_services') }}</span>

                </div>
            </div>
        </div>
    </div>

</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Password toggle functionality
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

        // Password confirmation toggle functionality
        const passwordConfirmationToggle = document.querySelectorAll('.password-toggle')[1];
        const passwordConfirmationInput = document.getElementById('password_confirmation');
        const eyeIconConfirmation = document.getElementById('eyeIconConfirmation');

        passwordConfirmationToggle.addEventListener('click', function () {
            if (passwordConfirmationInput.type === 'password') {
                passwordConfirmationInput.type = 'text';
                eyeIconConfirmation.classList.remove('fa-eye');
                eyeIconConfirmation.classList.add('fa-eye-slash');
            } else {
                passwordConfirmationInput.type = 'password';
                eyeIconConfirmation.classList.remove('fa-eye-slash');
                eyeIconConfirmation.classList.add('fa-eye');
            }
        });

        // Business field other option functionality
        const businessFieldSelect = document.getElementById('business_field');
        const otherBusinessFieldContainer = document.getElementById('other_business_field_container');
        const otherBusinessFieldInput = document.querySelector('input[name="other_business_field"]');

        function toggleOtherBusinessField() {
            if (businessFieldSelect.value === 'other') {
                otherBusinessFieldContainer.style.display = 'block';
                otherBusinessFieldInput.setAttribute('required', 'required');
            } else {
                otherBusinessFieldContainer.style.display = 'none';
                otherBusinessFieldInput.removeAttribute('required');
                otherBusinessFieldInput.value = '';
            }
        }

        // Check on page load if "other" was previously selected
        toggleOtherBusinessField();

        // Listen for changes
        businessFieldSelect.addEventListener('change', toggleOtherBusinessField);
    });
</script>
@endsection
