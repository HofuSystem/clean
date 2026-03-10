@extends('admin::layouts.dashboard')
@section('content')
    <!--end::Header-->
    <!--begin::Content-->
    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        <!--begin::Toolbar-->
        <div class="toolbar my-3" id="kt_toolbar">
            <!--begin::Container-->
            <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
                <!--begin::Page title-->
                <div data-kt-swapper="true" data-kt-swapper-mode="prepend"
                    data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}"
                    class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
                    <!--begin::Title-->
                    <h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1">{{ $title }}</h1>
                    <!--end::Title-->
                    <!--begin::Separator-->
                    <span class="h-20px border-gray-200 border-start mx-4"></span>
                    <!--end::Separator-->
                    <!--begin::Breadcrumb-->
                    <ul class="breadcrumb breadcrumb-separatorless fw-bold fs-7 my-1">
                        <!--begin::Item-->
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('dashboard.index') }}" class="text-muted text-hover-primary">@lang('Home')</a>
                        </li>
                        <!--end::Item-->

                        <!--begin::Item-->
                        <li class="breadcrumb-item text-muted">@lang('users')</li>
                        <!--end::Item-->

                        <!--begin::Item-->
                        <li class="breadcrumb-item text-dark">{{ $title }}</li>
                        <!--end::Item-->
                    </ul>
                    <!--end::Breadcrumb-->
                </div>
                <!--end::Page title-->

            </div>
            <!--end::Container-->
        </div>
        <!--end::Toolbar-->
        <!--begin::Post-->
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <!--begin::Container-->
            <div id="kt_content_container" class="container-fluid">
                <!--begin::Card-->
                <div class="card show-page">

                    <div class="card">
                        <div class="card-body row">
                            <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="pills-home-tab" data-bs-toggle="pill"
                                        data-bs-target="#pills-home" type="button" role="tab"
                                        aria-controls="pills-home" aria-selected="true">{{ trans('Main Data') }}</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="pills-profile-data-tab" data-bs-toggle="pill"
                                        data-bs-target="#pills-profile-data" type="button" role="tab"
                                        aria-controls="pills-profile-data"
                                        aria-selected="false">{{ trans('profile') }}</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="pills-profile-tab" data-bs-toggle="pill"
                                        data-bs-target="#pills-profile" type="button" role="tab"
                                        aria-controls="pills-profile" aria-selected="false">{{ trans('Orders') }}</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="pills-contact-tab" data-bs-toggle="pill"
                                        data-bs-target="#pills-contact" type="button" role="tab"
                                        aria-controls="pills-contact"
                                        aria-selected="false">{{ trans('wallet Transactions') }}</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="pills-points-tab" data-bs-toggle="pill"
                                        data-bs-target="#pills-points" type="button" role="tab"
                                        aria-controls="pills-points" aria-selected="false">{{ trans('points') }}</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="pills-addresses-tab" data-bs-toggle="pill"
                                        data-bs-target="#pills-addresses" type="button" role="tab"
                                        aria-controls="pills-addresses"
                                        aria-selected="false">{{ trans('addresses') }}</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="pills-notifications-tab" data-bs-toggle="pill"
                                        data-bs-target="#pills-notifications" type="button" role="tab"
                                        aria-controls="pills-notifications" aria-selected="false">{{ trans('notifications') }}</button>
                                </li>
                            </ul>
                            <div class="tab-content" id="pills-tabContent">
                                <div class="tab-pane fade show active" id="pills-home" role="tabpanel"
                                    aria-labelledby="pills-home-tab" tabindex="0">
                                    <div class="row">
                                        <div class="form-group mb-3 col-md-6">
                                            <label>{{ trans('avatar') }}</label>
                                            <div class="gallary-images">{!! Core\MediaCenter\Helpers\MediaCenterHelper::getImagesHtml($item->image) !!}</div>
                                        </div>

                                        <div class="form-group mb-3 col-md-6">
                                            <label>{{ trans('full name') }}</label>
                                            <p>{{ $item->fullname ?? 'N/A' }}</p>
                                        </div>

                                        <div class="form-group mb-3 col-md-6">
                                            <label>{{ trans('email') }}</label>
                                            <p>{{ $item->email ?? 'N/A' }}</p>
                                        </div>

                                        <div class="form-group mb-3 col-md-6">
                                            <label>{{ trans('email verified at') }}</label>
                                            <p>{{ $item->email_verified_at ?? 'N/A' }}</p>
                                        </div>

                                        <div class="form-group mb-3 col-md-6">
                                            <label>{{ trans('phone') }}</label>
                                            <p>{{ $item->phone ?? 'N/A' }}</p>
                                        </div>

                                        <div class="form-group mb-3 col-md-6">
                                            <label>{{ trans('phone verified at') }}</label>
                                            <p>{{ $item->phone_verified_at ?? 'N/A' }}</p>
                                        </div>

                                        <div class="form-group mb-3 col-md-12">
                                            <label>{{ trans('roles') }}</label>
                                            <div class="row">
                                                @foreach ($item->roles as $single)
                                                    <div class="col-md-2 col-sm-4 alert alert-success m-1" role="alert">
                                                        <a href="{{ route('dashboard.roles.show', $single->id) }}">
                                                            {{ $single->name }} </a>
                                                    </div>
                                                @endforeach
                                            </div>

                                        </div>

                                        <div class="form-group mb-3 col-md-6">
                                            <label>{{ trans('is active') }}</label>
                                            <p>{{ $item->is_active ?? 'N/A' }}</p>
                                        </div>

                                        <div class="form-group mb-3 col-md-6">
                                            <label>{{ trans('is allow notify') }}</label>
                                            <p>{{ $item->is_allow_notify ?? 'N/A' }}</p>
                                        </div>

                                        <div class="form-group mb-3 col-md-12">
                                            <label>{{ trans('date of birth') }}</label>
                                            <p>{{ $item->date_of_birth ?? 'N/A' }}</p>
                                        </div>

                                        <div class="form-group mb-3 col-md-12">
                                            <label>{{ trans('identity number') }}</label>
                                            <p>{{ $item->identity_number ?? 'N/A' }}</p>
                                        </div>

                                        <div class="form-group mb-3 col-md-6">
                                            <label>{{ trans('wallet') }}</label>
                                            <p>{{ $item->wallet ?? 'N/A' }}</p>
                                        </div>

                                        <div class="form-group mb-3 col-md-6">
                                            <label>{{ trans('points balance') }}</label>
                                            <p>{{ $item->points_balance ?? 'N/A' }}</p>
                                        </div>

                                        <div class="form-group mb-3 col-md-12">
                                            <label>{{ trans('gender') }}</label>
                                            <div class="alert alert-warning m-1" role="alert">
                                                {{ $item->gender ?? 'N/A' }}</div>
                                        </div>



                                        <div class="form-group mb-3 col-md-6">
                                            <label>{{ trans('referral code') }}</label>
                                            <p>{{ $item->referral_code ?? 'N/A' }}</p>
                                        </div>


                                        <div class="form-group mb-3 col-md-6">
                                            <label>{{ trans('last login at') }}</label>
                                            <p>{{ $item->last_login_at ?? 'N/A' }}</p>
                                        </div>

                                        <div class="form-group mb-3 col-md-6">
                                            <label>{{ trans('register at') }}</label>
                                            <p>{{ $item->created_at ?? 'N/A' }}</p>
                                        </div>

                                    </div>
                                </div>
                                <div class="tab-pane fade" id="pills-profile-data" role="tabpanel"
                                    aria-labelledby="pills-home-tab" tabindex="0">
                                    <div class="row">

                                        <div class="form-group mb-3 col-md-6">
                                            <label>{{ trans('country') }}</label>
                                            @isset($item?->profile?->country)
                                                <div class="alert alert-primary m-1" role="alert"><a
                                                        href="{{ route('dashboard.countries.show', $item?->profile?->country->id) }}">{{ $item?->country?->name ?? 'N/A' }}</a>
                                                </div>
                                            @endisset

                                        </div>

                                        <div class="form-group mb-3 col-md-6">
                                            <label>{{ trans('city') }}</label>
                                            @isset($item?->profile?->city)
                                                <div class="alert alert-primary m-1" role="alert"><a
                                                        href="{{ route('dashboard.cities.show', $item?->profile?->city->id) }}">{{ $item?->city?->name ?? 'N/A' }}</a>
                                                </div>
                                            @endisset

                                        </div>

                                        <div class="form-group mb-3 col-md-6">
                                            <label>{{ trans('district') }}</label>
                                            @isset($item?->profile?->district)
                                                <div class="alert alert-primary m-1" role="alert"><a
                                                        href="{{ route('dashboard.districts.show', $item?->profile?->district->id) }}">{{ $item?->district?->name ?? 'N/A' }}</a>
                                                </div>
                                            @endisset

                                        </div>

                                        <div class="form-group mb-3 col-md-6">
                                            <label>{{ trans('other city name') }}</label>
                                            <p>{{ $item?->profile?->other_city_name ?? 'N/A' }}</p>
                                        </div>

                                        <div class="form-group mb-3 col-md-12">
                                            <label>{{ trans('user') }}</label>
                                            @isset($item?->profile?->user)
                                                <div class="alert alert-primary m-1" role="alert"><a
                                                        href="{{ route('dashboard.users.show', $item?->profile?->user->id) }}">{{ $item?->user?->fullname ?? 'N/A' }}</a>
                                                </div>
                                            @endisset

                                        </div>

                                        <div class="form-group mb-3 col-md-12">
                                            <label>{{ trans('bio') }}</label>
                                            <p>{{ $item?->profile?->bio ?? 'N/A' }}</p>
                                        </div>

                                        <div class="form-group mb-3 col-md-6">
                                            <label>{{ trans('lat') }}</label>
                                            <p>{{ $item?->profile?->lat ?? 'N/A' }}</p>
                                        </div>

                                        <div class="form-group mb-3 col-md-6">
                                            <label>{{ trans('lng') }}</label>
                                            <p>{{ $item?->profile?->lng ?? 'N/A' }}</p>
                                        </div>


                                    </div>
                                </div>
                                <div class="tab-pane fade" id="pills-profile" role="tabpanel"
                                    aria-labelledby="pills-profile-tab" tabindex="0">
                                    <div class="mt-3">
                                        <div class="row">
                                            <div class="form-group mb-3 col-md-4">
                                                <label class="mb-2">{{ trans('tire') }}</label>
                                                <span class="p-2  mt-2  rounded"  style="background-color:{{ $customerTire['color'] }}; color:#fff ">{{ trans($customerTire['type']) }}</span>
                                            </div>
                                            <div class="form-group mb-3 col-md-4">
                                                <label>{{ trans('total orders') }}</label>
                                                <p>{{ $totalOrders ?? 'N/A' }}</p>
                                            </div>
                                            <div class="form-group mb-3 col-md-4">
                                                <label>{{ trans('total sales') }}</label>
                                                <p>{{ $totalSpent ?? 'N/A' }} {{ trans('SAR') }}</p>
                                            </div>
                                            <div class="form-group mb-3 col-md-6">
                                                <label>{{ trans('first order date') }}</label>
                                                <p>{{ $firstOrderDate ?? 'N/A' }}</p>
                                            </div>
                                            <div class="form-group mb-3 col-md-6">
                                                <label>{{ trans('last order date') }}</label>
                                                <p>{{ $lastOrderDate ?? 'N/A' }}</p>
                                            </div>
                                        </div>
                                        <h3 class="text-dark">{{ trans('orders') }}</h3>
                                        <div class="table-responsive">
                                            <!--begin::Table-->
                                            <table
                                                class="view-dataTable table align-middle text-center table-row-dashed fs-6 gy-5"
                                                data-load="{{ route('dashboard.orders.index', ['filters' => ['client_id' => $item->id]]) }}">
                                                <!--begin::Table head-->
                                                <thead class="table-primary">
                                                    <!--begin::Table row-->
                                                    <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                                        <th class="orderable text-center py-2" data-name="created_at">
                                                            @lang('created at')
                                                        </th>

                                                        <th class="orderable text-center py-2" data-name="reference_id">
                                                            @lang('reference_id')</th>
                                                        <th class="orderable text-center py-2" data-name="type">
                                                            @lang('type')</th>
                                                        <th class="orderable text-center py-2" data-name="status">
                                                            @lang('status')</th>
                                                        <th class="orderable text-center py-2" data-name="pay_type">
                                                            @lang('pay type')</th>
                                                        <th class="orderable text-center py-2" data-name="coupon_id">
                                                            @lang('coupon')</th>
                                                        <th class="orderable text-center py-2" data-name="total_price">
                                                            @lang('total price')
                                                        </th>

                                                        <th class="orderable text-center py-2" data-name="wallet_used">
                                                            @lang('wallet used')
                                                        </th>
                                                        <th class="orderable text-center py-2"
                                                            data-name="wallet_amount_used">
                                                            @lang('wallet amount used')</th>

                                                    </tr>
                                                    <!--end::Table row-->
                                                </thead>
                                                <!--end::Table head-->
                                                <!--begin::Table body-->
                                                <tbody class="text-gray-600 fw-bold">

                                                </tbody>
                                                <!--end::Table body-->
                                            </table>
                                            <!--end::Table-->
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="pills-contact" role="tabpanel"
                                    aria-labelledby="pills-contact-tab" tabindex="0">
                                    <div class="mt-3">
                                        <h3 class="text-dark">{{ trans('wallet transactions') }}</h3>
                                        <div class="table-responsive">
                                            <!--begin::Table-->
                                            <table
                                                class="view-dataTable table align-middle text-center table-row-dashed fs-6 gy-5"
                                                data-load="{{ route('dashboard.wallet-transactions.index', ['filters'=>['user_id' => $item->id]]) }}">
                                                <!--begin::Table head-->
                                                <thead class="table-primary">
                                                    <!--begin::Table row-->
                                                    <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                                    <th class="orderable text-center py-2" data-name="created_at">
                                                        @lang('created at')
                                                    </th>
    
                                                    <th class="orderable text-center py-2" data-name="type">
                                                            @lang('type')</th>
                                                        <th class="orderable text-center py-2" data-name="amount">
                                                            @lang('amount')</th>
                                                        <th class="orderable text-center py-2" data-name="wallet_before">
                                                            @lang('wallet before')</th>
                                                        <th class="orderable text-center py-2" data-name="wallet_after">
                                                            @lang('wallet after')</th>
                                                        <th class="orderable text-center py-2" data-name="added_by_id">
                                                            @lang('added by')</th>
                                                        <th class="orderable text-center py-2" data-name="order_id">
                                                            @lang('order')</th>
                                                        <th class="orderable text-center py-2" data-name="status">
                                                            @lang('status')</th>
                                                        <th class="orderable text-center py-2" data-name="expired_at">
                                                            @lang('expire at')</th>
                                                    </tr>
                                                    <!--end::Table row-->
                                                </thead>
                                                <!--end::Table head-->
                                                <!--begin::Table body-->
                                                <tbody class="text-gray-600 fw-bold">

                                                </tbody>
                                                <!--end::Table body-->
                                            </table>
                                            <!--end::Table-->
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="pills-points" role="tabpanel"
                                    aria-labelledby="pills-points-tab" tabindex="0">
                                    <div class="mt-3">
                                        <h3 class="text-dark">{{ trans('points') }}</h3>
                                        <div class="table-responsive">
                                            <!--begin::Table-->
                                            <table
                                                class="view-dataTable table align-middle text-center table-row-dashed fs-6 gy-5"
                                                data-load="{{ route('dashboard.points.index', ['filters'=>['user_id' => $item->id]]) }}">
                                                <!--begin::Table head-->
                                                <thead class="table-primary">
                                                    <!--begin::Table row-->
                                                    <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                                        <th class="orderable text-center py-2" data-name="title">
                                                            @lang('title')</th>
                                                        <th class="orderable text-center py-2" data-name="amount">
                                                            @lang('points.amount')</th>
                                                        <th class="orderable text-center py-2" data-name="operation">
                                                            @lang('operation')</th>
                                                    </tr>
                                                    <!--end::Table row-->
                                                </thead>
                                                <!--end::Table head-->
                                                <!--begin::Table body-->
                                                <tbody class="text-gray-600 fw-bold">

                                                </tbody>
                                                <!--end::Table body-->
                                            </table>
                                            <!--end::Table-->
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="pills-addresses" role="tabpanel"
                                    aria-labelledby="pills-addresses-tab" tabindex="0">
                                    <div class="mt-3">
                                        <h3 class="text-dark">{{ trans('addresses') }}</h3>
                                        <div class="table-responsive">
                                            <!--begin::Table-->
                                            <table
                                                class="view-dataTable table align-middle text-center table-row-dashed fs-6 gy-5"
                                                data-load="{{ route('dashboard.addresses.index', ['filters'=>['user_id' => $item->id]]) }}">
                                                <!--begin::Table head-->
                                                <thead class="table-primary">
                                                    <!--begin::Table row-->
                                                    <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                                        <th class="orderable text-center py-2" data-name="location">
                                                            @lang('location')</th>
                                                        <th class="orderable text-center py-2" data-name="lat">
                                                            @lang('lat')</th>
                                                        <th class="orderable text-center py-2" data-name="lng">
                                                            @lang('lng')</th>
                                                        <th class="orderable text-center py-2" data-name="city_id">
                                                            @lang('city')</th>
                                                        <th class="orderable text-center py-2" data-name="district_id">
                                                            @lang('district')</th>
                                                    </tr>
                                                    <!--end::Table row-->
                                                </thead>
                                                <!--end::Table head-->
                                                <!--begin::Table body-->
                                                <tbody class="text-gray-600 fw-bold">

                                                </tbody>
                                                <!--end::Table body-->
                                            </table>
                                            <!--end::Table-->
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="pills-notifications" role="tabpanel"
                                aria-labelledby="pills-notifications-tab" tabindex="0">
                                <div class="mt-3">
                                    <h3 class="text-dark">{{ trans('notifications') }}</h3>
                                    @can('dashboard.notifications.create')
                                        <a href="{{ route('dashboard.notifications.create') }}" id ="notify-user" data-id="{{ $item->id }}"
                                            class="btn btn-warning align-middle ">
                                            <!--begin::Svg Icon | path: icons/duotune/arrows/arr075.svg-->
                                            <i class="fas fa-bell"></i>
                                            <!--end::Svg Icon-->@lang('notify')
                                        </a>
                                    @endcan
                                    <div class="table-responsive">
                                        <!--begin::Table-->
                                        <table
                                            class="view-dataTable table align-middle text-center table-row-dashed fs-6 gy-5"
                                            data-load="{{ route('dashboard.notifications.index', ['for_user' => $item->id]) }}">
                                            <!--begin::Table head-->
                                            <thead class="table-primary">
                                                <!--begin::Table row-->
                                                <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                                    <th class="text-center py-2" data-name="types">
                                                        @lang('types')</th>
                                                    <th class="orderable text-center py-2" data-name="title">
                                                        @lang('title')</th>
                                                    <th class="orderable text-center py-2" data-name="body">
                                                        @lang('body')</th>
                                                    <th class="orderable text-center py-2" data-name="media">
                                                        @lang('media')</th>
                                                    <th class="orderable text-center py-2" data-name="sender_id">
                                                        @lang('sender_id')</th>

                                                    <th class="orderable text-center py-2" data-name="actions">
                                                        @lang('action')</th>
                                                </tr>
                                                <!--end::Table row-->
                                            </thead>
                                            <!--end::Table head-->
                                            <!--begin::Table body-->
                                            <tbody class="text-gray-600 fw-bold">

                                            </tbody>
                                            <!--end::Table body-->
                                        </table>
                                        <!--end::Table-->
                                    </div>
                                </div>
                            </div>
                            </div>




                        </div>
                        <div class="card-footer">
                            <a href="{{ route('dashboard.users.index') }}" class="btn btn-secondary"><i
                                    class="fas fa-arrow-left"></i> {{ trans('Back') }}</a>
                        </div>
                    </div>


                    @include('comment::inc.comment-section', [
                        'commentUrl' => route('dashboard.users.comment', $item->id),
                    ])
                </div>
                <!--end::Card-->
            </div>
            <!--end::Container-->
        </div>
        <!--end::Post-->
    </div>
    <!--end::Content-->
    @include('media::mediaCenter.modal')
    @include('notification::inc.notifyModal')
@endsection
@push('css')

    <link href="{{ asset('control') }}/assets/plugins/custom/datatables/datatables.bundle.css"
        rel="stylesheet"type="text/css" />

    <link href="{{ asset('control') }}/js/custom/crud/show.css" rel="stylesheet" type="text/css" />
@endpush
@push('js')
    <script>
        var deleteUrl = "{{ route('dashboard.orders.delete', '%s') }}"
    </script>




    <!--begin::Page Custom Javascript(used by this page)-->

    <script>
        $('#notify-user').click(function(e) {
             e.preventDefault()
             $('#notifyModal').modal('show')
             $('#notifyModal [name=for]').val('users')
             let id = $(this).data('id');
             id = `[${id}]`;
             $('#notifyModal [name=for_data]').val(id)
         })
 </script>
@endpush
