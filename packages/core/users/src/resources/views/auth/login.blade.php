@extends('admin::layouts.dashboard-auth')

@section('content')
    <!-- Content -->

    <div class="container-fluid">
        <div class="authentication-wrapper authentication-basic container-p-y"
            style="background-image: background-size:cover;">
            <div class="authentication-inner py-4">
                <!-- Login -->
                <div class="card">
                    <div class="card-body">
                        <!-- Logo -->
                        <div class="app-brand justify-content-center">
                            <a href="{{ route('dashboard.index') }}" class="app-brand-link gap-2">
                                <span class="app-brand-logo demo">
                                    <img class="img-fluid" src="{{ config('app.logo') }}">
                                </span>
                            </a>
                        </div>
                        <!-- /Logo -->
                        <h4 class="mb-1 pt-2 text-center">{{ $title }}
                        </h4>
                        <p class="card-text mb-2 mt-1 text-center">
                            {{ config('backend-settings.admin_style.admin_welcome_message') }}</p>
                        <form id="formAuthentication" class="mb-3 fv-plugins-bootstrap5 fv-plugins-framework"
                            action="{{ route('login') }}" method="POST" novalidate="novalidate">
                            @csrf
                            <div class="mb-3 fv-plugins-icon-container">
                                <label for="email" class="form-label">{{ trans('Email') }}</label>
                                <input type="text" class="form-control" id="email" name="email"
                                    placeholder="Enter your email or username" autofocus="">
                                <div
                                    class="fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback">
                                    @error('email')
                                        <strong>{{ $message }}</strong>
                                    @enderror
                                </div>

                            </div>
                            <div class="mb-3 form-password-toggle fv-plugins-icon-container">

                                <div class="input-group input-group-merge has-validation">
                                    <input type="password" id="password" class="form-control" name="password"
                                        placeholder="············" aria-describedby="password">
                                    <span class="input-group-text cursor-pointer"><i
                                            class="fa-regular fa-eye-slash"></i></span>
                                </div>
                                <div
                                    class="fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback">
                                    @error('password')
                                        <strong>{{ $message }}</strong>
                                    @enderror
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="remember" name="remember" checked
                                    >
                                    <label class="form-check-label" for="remember-me"> {{ trans('Remember Me') }} </label>
                                </div>
                            </div>
                            <div class="mb-3">
                                <button class="btn btn-primary d-grid w-100 waves-effect waves-light" type="submit">{{ trans('Sign in') }}</button>
                            </div>
                            <input type="hidden">
                        </form>

                     


                    </div>
                </div>
                <!-- /Register -->
            </div>
        </div>
    </div>
@endsection
@push('js')
    <script>

    </script>
@endpush
