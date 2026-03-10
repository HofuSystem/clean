@extends('admin::layouts.dashboard-login')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card" style="position: fixed;width: 400px;top: 30%;transform: translate(-50%);left: 50%;">
                <h2 class="card-header" style="width: max-content;margin: 0 auto;">{{ __('Reset Password') }}</h2>

                <div class="card-body">
                    @if (isset($message))
                        <div class="alert alert-success" role="alert">
                            {{ $message }}
                        </div>
                    @endif
                    @error('email')
                        <div class="alert alert-success" role="alert">
                            {{ $message }}
                        </div>
                    @enderror
                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf

                        <input id="username" placeholder="{{ __('Email Or Username Or Phone') }}" type="text" class="form-control @error('username') is-invalid @enderror" name="username" value="{{ $username ?? old('username') }}" required autocomplete="username" autofocus>
                        <input type="hidden" name="type" value="forgot">
                        @error('username')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror

                        <button style="margin:5px 0;width:100%" type="submit" class="btn btn-primary">
                            {{ __('Send Password Reset Code') }}
                        </button>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
