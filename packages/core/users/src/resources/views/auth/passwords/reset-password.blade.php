@extends('admin::layouts.dashboard-login')

@section("styles")
    <style>
        .container .card{
            position: fixed;
            width: 600px;
            top: 30%;
            left: 50%;
            transform: translate(-50%);
        }
    </style>
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Reset Password Code Confirmation') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('password.updatingPassword') }}">
                        @csrf

                        <input type="hidden" name="username" value="{{ $username ?? '' }}">
                        <input type="hidden" name="type" value="code">

                        <div class="row mb-3">
                            <label for="code" class="col-md-4 col-form-label text-md-end">{{ __('Code') }}</label>

                            <div class="col-md-6">
                                <input id="code" type="text" class="form-control" name="code" required autofocus>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password" class="col-md-4 col-form-label text-md-end">{{ __('New Password') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="text" class="form-control" name="password" required>
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Submit Code') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    window.addEventListener("load", function() {
        $("form")[0].addEventListener("submit", function(e) {
            e.preventDefault();
            xhr = new XMLHttpRequest;
            xhr.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    try{
                        let res = JSON.parse(this.response);
                        if(res["status"]) {
                            window.location = "{{ url('/') }}";
                            return toastr.success(res.message)
                        }
                        return toastr.error(res.message)
                    } catch(err) {
                        toastr.error('invalid password or code')
                    }
                }
            }
            xhr.open("POST", "{{ route('password.updatingPassword') }}", true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhr.send("username="+this.username.value+"&type="+this.type.value+"&code="+this.code.value+"&password="+this.password.value+"&_token="+this._token.value);
        });
        // alert();
    });
</script>
@endsection
