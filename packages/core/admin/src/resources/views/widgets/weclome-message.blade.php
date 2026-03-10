<div class="col-xl-4 mb-4 col-lg-5 col-12">
    <div class="card  h-100">
        <div class="d-flex align-items-end row">
            <div class="card-body ">
                <h5 class="card-title mb-0">{{ trans('welcome') }} {{ auth()->user()->name }}</h5>
                <p class="mb-2"> {!! config('backend-settings.admin_message.' . config('app.locale')) !!}</p>

            </div>

        </div>
    </div>
</div>
