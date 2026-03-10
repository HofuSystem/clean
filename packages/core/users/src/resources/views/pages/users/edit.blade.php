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
                            <a href="{{ route('dashboard.index') }}"
                                class="text-muted text-hover-primary">@lang('Home')</a>
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
                <div class="card p-3">
                    <div class="card-body row">
                        @isset($item)
                            <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="pills-home-tab" data-bs-toggle="pill"
                                        data-bs-target="#pills-home" type="button" role="tab" aria-controls="pills-home"
                                        aria-selected="true">{{ trans('Main Data') }}</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="pills-profile-data-tab" data-bs-toggle="pill"
                                        data-bs-target="#pills-profile-data" type="button" role="tab"
                                        aria-controls="pills-profile-data" aria-selected="false">{{ trans('profile') }}</button>
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
                                        aria-controls="pills-addresses" aria-selected="false">{{ trans('addresses') }}</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="pills-notifications-tab" data-bs-toggle="pill"
                                        data-bs-target="#pills-notifications" type="button" role="tab"
                                        aria-controls="pills-notifications"
                                        aria-selected="false">{{ trans('notifications') }}</button>
                                </li>

                            </ul>
                        @endisset
                        <div class="tab-content" id="pills-tabContent">
                            <div class="tab-pane fade show active" id="pills-home" role="tabpanel"
                                aria-labelledby="pills-home-tab" tabindex="0">
                                <div class="row">
                                    <form class="form" method="POST" id="operation-form"
                                        data-id="{{ $item->id ?? null }}"
                                        @isset($item)
                                            action="{{ route('dashboard.users.edit', $item->id) }}"
                                            data-mode="edit"
                                        @else
                                            action="{{ route('dashboard.users.create') }}"
                                            data-mode="new"
                                        @endisset>

                                        @csrf
                                        <div class="row">

                                            <div class="form-group mb-3 col-md-6">
                                                <label class="" for="image">{{ trans('avatar') }}</label>
                                                <div class="media-center-group form-control" data-max="1"
                                                    data-type="avatar">
                                                    <input type="text" hidden="hidden" class="form-control"
                                                        name="image" value="{{ old('image', $item->image ?? null) }}">
                                                    <button type="button" class="btn btn-secondary media-center-load"
                                                        style="margin-top: 10px;"><i
                                                            class="fa fa-file-upload"></i></button>
                                                    <div class="input-gallery"></div>
                                                </div>
                                            </div>

                                            <div class="form-group mb-3 col-md-6">
                                                <label class="required" for="fullname">{{ trans('full name') }}</label>
                                                <input type="text" name="fullname" class="form-control "
                                                    placeholder="{{ trans('Enter full name') }} "
                                                    value="@isset($item){{ $item->fullname }}@endisset">

                                            </div>

                                            <div class="form-group mb-3 col-md-6">
                                                <label class="" for="email">{{ trans('email') }}</label>
                                                <input type="email" name="email" class="form-control "
                                                    placeholder="{{ trans('Enter email') }} "
                                                    value="{{ old('email', $item->email ?? null) }}">

                                            </div>


                                            <div class="form-group mb-3 col-md-6">
                                                <label class="required" for="phone">{{ trans('phone') }}</label>
                                                <input type="text" name="phone" class="form-control "
                                                    placeholder="{{ trans('Enter phone') }} "
                                                    value="@isset($item){{ $item->phone }}@endisset">

                                            </div>



                                            <div class="form-group mb-3 col-md-12">
                                                <label class="" for="roles">{{ trans('roles') }}</label>
                                                <select class="custom-select  form-select advance-select" name="roles"
                                                    id="roles" multiple>
                                                    @foreach ($roles ?? [] as $sItem)
                                                        <option data-name="{{ $sItem->name }}"
                                                            data-id="{{ $sItem->id }}" @selected(isset($item) and $item->roles->where('id', $sItem->id)->first())
                                                            value="{{ $sItem->id }}">{{ $sItem->title }}</option>
                                                    @endforeach

                                                </select>

                                            </div>
                                            <div id="technicalsSelect" class="form-group mb-3 col-md-12">
                                                <label class="" for="technicals">{{ trans('technicals') }}</label>
                                                <select class="custom-select  form-select advance-select"
                                                    name="technicals" id="technicals" multiple>
                                                    @foreach ($technicals ?? [] as $sItem)
                                                        <option data-id="{{ $sItem->id }}" @selected(isset($item) and $item->technicals->where('id', $sItem->id)->first())
                                                            value="{{ $sItem->id }}">{{ $sItem->fullname }}</option>
                                                    @endforeach

                                                </select>

                                            </div>

                                            <div class="form-group mb-3 col-md-6">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="is_active"
                                                        name="is_active" @checked(isset($item) and $item->is_active)>
                                                    <label class="form-check-label"
                                                        for="is_active">{{ trans('is active') }}</label>
                                                </div>

                                            </div>

                                            <div class="form-group mb-3 col-md-6">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="is_allow_notify"
                                                        name="is_allow_notify" @checked(isset($item) and $item->is_allow_notify)>
                                                    <label class="form-check-label"
                                                        for="is_allow_notify">{{ trans('is allow notify') }}</label>
                                                </div>

                                            </div>

                                            <div class="form-group mb-3 col-md-12">
                                                <label class=""
                                                    for="date_of_birth">{{ trans('date of birth') }}</label>
                                                <input type="date" name="date_of_birth" class="form-control "
                                                    placeholder="{{ trans('Enter date of birth') }} "
                                                    value="@isset($item){{ $item->date_of_birth }}@endisset">

                                            </div>

                                            <div class="form-group mb-3 col-md-12">
                                                <label class=""
                                                    for="identity_number">{{ trans('identity number') }}</label>
                                                <input type="text" name="identity_number" class="form-control "
                                                    placeholder="{{ trans('Enter identity number') }} "
                                                    value="@isset($item){{ $item->identity_number }}@endisset">

                                            </div>



                                            <div class="form-group mb-3 col-md-12">
                                                <label class="required" for="gender">{{ trans('gender') }}</label>
                                                <select class="custom-select  form-select advance-select" name="gender"
                                                    id="gender">

                                                    <option value="">{{ trans('select gender') }}</option>
                                                    <option value="male" @selected(isset($item) and $item->gender == 'male')>
                                                        {{ trans('male') }}</option>
                                                    <option value="female" @selected(isset($item) and $item->gender == 'female')>
                                                        {{ trans('female') }}</option>

                                                </select>

                                            </div>

                                        </div>
                                        <div class="card-footer">
                                            <div class="row">
                                                <div class="col-lg-9 ml-lg-auto">
                                                    <button type="submit"
                                                        class="btn btn-primary font-weight-bold mr-2">{{ trans('save') }}</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                @isset($item)
                                    <!--begin::Card-->
                                    <div class="card p-3 mt-3">
                                        <div class="card-header">{{ trans('Update Password') }}</div>
                                        <div class="card-body row">
                                            <form id="updatePassword"
                                                action="{{ route('dashboard.users.update-password', $item->id) }}">
                                                <div class="mb-3">
                                                    <label for=""
                                                        class="form-label">{{ trans('new Password') }}</label>
                                                    <input type="password" name="password" id="password"
                                                        class="form-control" placeholder="password" />
                                                </div>
                                                <button class="btn btn-primary" type="submit">{{ trans('update') }}</button>
                                            </form>



                                        </div>
                                    </div>
                                    <!--end::Card-->
                                @endisset
                            </div>
                            @isset($item)
                                <div class="tab-pane fade" id="pills-profile-data" role="tabpanel"
                                    aria-labelledby="pills-home-tab" tabindex="0">
                                    <form class="form operation-form" method="POST"
                                        data-id="{{ $item?->profile?->id ?? null }}"
                                        action="{{ route('dashboard.users.profile.edit', $item?->id) }}" data-mode="edit">

                                        @csrf
                                        <div class="card-body row">
                                            <input type="hidden" name="user_id" value="{{ $item->id }}">

                                            <div class="form-group mb-3 col-md-6">
                                                <label class="" for="country_id">{{ trans('country') }}</label>
                                                <select class="custom-select  form-select advance-select" name="country_id"
                                                    id="country_id">

                                                    <option value="">{{ trans('select country') }}</option>
                                                    @foreach ($countries ?? [] as $sItem)
                                                        <option data-id="{{ $sItem->id }}" @selected(isset($item) and $item?->profile?->country_id == $sItem->id)
                                                            value="{{ $sItem->id }}">{{ $sItem->name }}</option>
                                                    @endforeach

                                                </select>

                                            </div>

                                            <div class="form-group mb-3 col-md-6">
                                                <label class="" for="city_id">{{ trans('city') }}</label>
                                                <select class="custom-select  form-select advance-select" name="city_id"
                                                    id="city_id">

                                                    <option value="">{{ trans('select city') }}</option>
                                                    @foreach ($cities ?? [] as $sItem)
                                                        <option data-id="{{ $sItem->id }}" @selected(isset($item) and $item?->profile?->city_id == $sItem->id)
                                                            value="{{ $sItem->id }}">{{ $sItem->name }}</option>
                                                    @endforeach

                                                </select>

                                            </div>

                                            <div class="form-group mb-3 col-md-6">
                                                <label class="" for="district_id">{{ trans('district') }}</label>
                                                <select class="custom-select  form-select advance-select" name="district_id"
                                                    id="district_id">

                                                    <option value="">{{ trans('select district') }}</option>
                                                    @foreach ($districts ?? [] as $sItem)
                                                        <option data-id="{{ $sItem->id }}" @selected(isset($item) and $item?->profile?->district_id == $sItem->id)
                                                            value="{{ $sItem->id }}">{{ $sItem->name }}</option>
                                                    @endforeach

                                                </select>

                                            </div>

                                            <div class="form-group mb-3 col-md-6">
                                                <label class=""
                                                    for="other_city_name">{{ trans('other city name') }}</label>
                                                <input type="text" name="other_city_name" class="form-control "
                                                    placeholder="{{ trans('Enter other city name') }} "
                                                    value="@isset($item){{ $item?->profile?->other_city_name }}@endisset">

                                            </div>



                                            <div class="form-group mb-3 col-md-12">
                                                <label class="" for="bio">{{ trans('bio') }}</label>
                                                    <textarea type="number" name="bio" class="form-control " placeholder="{{ trans('Enter bio') }} ">
                                                    @isset($item)
                                                    {{ $item?->profile?->bio }}
                                                    @endisset
                                                </textarea>

                                            </div>

                                            <div class="form-group mb-3 col-md-6">
                                                <label class="" for="lat">{{ trans('lat') }}</label>
                                                <input type="number" name="lat" class="form-control "
                                                    placeholder="{{ trans('Enter lat') }} "
                                                    value="{{ old('lat', $item?->profile?->lat ?? null) }}">

                                            </div>

                                            <div class="form-group mb-3 col-md-6">
                                                <label class="" for="lng">{{ trans('lng') }}</label>
                                                <input type="text" name="lng" class="form-control "
                                                    placeholder="{{ trans('Enter lng') }} "
                                                    value="@isset($item){{ $item?->profile?->lng }}@endisset">

                                            </div>

                                        </div>
                                        <div class="card-footer">
                                            <div class="row">
                                                <div class="col-lg-9 ml-lg-auto">
                                                    <button type="submit"
                                                        class="btn btn-primary font-weight-bold mr-2">{{ trans('save') }}</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="tab-pane fade" id="pills-profile" role="tabpanel"
                                    aria-labelledby="pills-profile-tab" tabindex="0">
                                    <div class="mt-3">
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
                                                            @lang('created at')</th>
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
                                                        <th class="orderable text-center py-2" data-name="is_admin_accepted">
                                                            @lang('is admin accepted')</th>

                                                        <th class="orderable text-center py-2" data-name="wallet_used">
                                                            @lang('wallet used')
                                                        </th>
                                                        <th class="orderable text-center py-2" data-name="wallet_amount_used">
                                                            @lang('wallet amount used')</th>
                                                        <th class="orderable text-center py-2" data-name="actions">
                                                            @lang('actions')</th>

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
                                    <div class="mt-3  ">
                                        <div class="items-container mb-3" data-items-on = "user_id"
                                            data-items-name = "walletTransactions" data-items-from ="wallet-transactions">
                                            <button class="btn-operation create-new-items"><i
                                                    class="fas fa-plus"></i></button>
                                        </div>
                                        <h3 class="text-dark">{{ trans('wallet transactions') }}</h3>
                                        <div class="table-responsive">
                                            <!--begin::Table-->
                                            <table
                                                class="view-dataTable table align-middle text-center table-row-dashed fs-6 gy-5"
                                                data-load="{{ route('dashboard.wallet-transactions.index', ['filters' => ['user_id' => $item->id]]) }}">
                                                <!--begin::Table head-->
                                                <thead class="table-primary">
                                                    <!--begin::Table row-->
                                                    <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                                        <th class="orderable text-center py-2" data-name="created_at">
                                                            @lang('added at')</th>
                                                        <th class="orderable text-center py-2" data-name="type">
                                                            @lang('type')</th>
                                                        <th class="orderable text-center py-2" data-name="amount">
                                                            @lang('amount')</th>
                                                        <th class="orderable text-center py-2" data-name="wallet_before">
                                                            @lang('wallet before')</th>
                                                        <th class="orderable text-center py-2" data-name="wallet_after">
                                                            @lang('wallet after')</th>
                                                        <th class="orderable text-center py-2" data-name="status">
                                                            @lang('status')</th>
                                                        <th class="orderable text-center py-2" data-name="expired_at">
                                                            @lang('expire at')</th>
                                                        <th class="orderable text-center py-2" data-name="order_id">
                                                            @lang('order')</th>
                                                        <th class="orderable text-center py-2" data-name="actions">
                                                            @lang('actions')</th>
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
                                        <div class="items-container mb-3" data-items-on = "user_id"
                                            data-items-name = "points" data-items-from ="points">
                                            <button class="btn-operation create-new-items"><i
                                                    class="fas fa-plus"></i></button>
                                        </div>
                                        <h3 class="text-dark">{{ trans('points') }}</h3>
                                        <div class="table-responsive">
                                            <!--begin::Table-->
                                            <table
                                                class="view-dataTable table align-middle text-center table-row-dashed fs-6 gy-5"
                                                data-load="{{ route('dashboard.points.index', ['filters' => ['user_id' => $item->id]]) }}">
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
                                                        <th class="orderable text-center py-2" data-name="expire_at">
                                                            @lang('expire at')</th>
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
                                <div class="tab-pane fade" id="pills-addresses" role="tabpanel"
                                    aria-labelledby="pills-addresses-tab" tabindex="0">
                                    <div class="mt-3">
                                        <h3 class="text-dark">{{ trans('addresses') }}</h3>
                                        <div class="table-responsive">
                                            <!--begin::Table-->
                                            <table
                                                class="view-dataTable table align-middle text-center table-row-dashed fs-6 gy-5"
                                                data-load="{{ route('dashboard.addresses.index', ['filters' => ['user_id' => $item->id]]) }}">
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
                                <div class="tab-pane fade" id="pills-notifications" role="tabpanel"
                                    aria-labelledby="pills-notifications-tab" tabindex="0">
                                    <div class="mt-3">
                                        <h3 class="text-dark">{{ trans('notifications') }}</h3>
                                        @can('dashboard.notifications.create')
                                            <a href="{{ route('dashboard.notifications.create') }}" id ="notify-user"
                                                data-id="{{ $item->id }}" class="btn-operation align-middle ">
                                                <!--begin::Svg Icon | path: icons/duotune/arrows/arr075.svg-->
                                                <i class="fas fa-bell"></i>
                                                <span>@lang('notify')</span>
                                                <!--end::Svg Icon-->
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
                            @endisset
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="wallet-transactionsModal" aria-hidden="true"
                    aria-labelledby="wallet-transactionsModalLabel"
                    data-store="{{ route('dashboard.wallet-transactions.create') }}">
                    <div class="modal-dialog modal-xl">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="wallet-transactionsModalLabel">
                                    {{ trans('WalletTransaction') }}</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form class="modal-form items-modal-form">
                                    <div class="row">

                                        <div class="form-group mb-3 col-md-6">
                                            <label class="required" for="type">{{ trans('type') }}</label>
                                            <select class="custom-select  form-select advance-select" name="type"
                                                id="user_id-type">

                                                <option value="">{{ trans('select type') }}</option>
                                                <option value="deposit" @selected(isset($item) and $item->type == 'deposit')>
                                                    {{ trans('deposit') }}</option>
                                                <option value="withdraw" @selected(isset($item) and $item->type == 'withdraw')>
                                                    {{ trans('withdraw') }}</option>

                                            </select>

                                        </div>

                                        <div class="form-group mb-3 col-md-6">
                                            <label class="required" for="amount">{{ trans('amount') }}</label>
                                            <input type="number" name="amount" class="form-control "
                                                placeholder="{{ trans('Enter amount') }} " value="">

                                        </div>




                                        <div class="form-group mb-3 col-md-6">
                                            <label for="expired_at">{{ trans('expire at') }}</label>
                                            <input type="date" name="expired_at" class="form-control "
                                                placeholder="{{ trans('Enter expire at') }} " value="">

                                        </div>




                                        <div class="col-lg-9 ml-lg-auto">
                                            <button type="submit"
                                                class="btn btn-primary font-weight-bold mr-2">{{ trans('Submit') }}</button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="modal fade" id="pointsModal" aria-hidden="true" aria-labelledby="pointsModalLabel"
                    data-store="{{ route('dashboard.points.create') }}">
                    <div class="modal-dialog modal-xl">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="pointsModalLabel">
                                    {{ trans('points') }}</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form class="modal-form items-modal-form">
                                    <div class="row">

                                        <div class="form-group mb-3 col-md-6">
                                            <label class="required" for="title">{{ trans('title') }}</label>
                                            <input type="text" name="title" class="form-control "
                                                placeholder="{{ trans('Enter title') }} " value="">

                                        </div>
                                        <div class="form-group mb-3 col-md-6">
                                            <label class="required"
                                                for="operation">{{ trans(key: 'operation') }}</label>
                                            <select class="custom-select  form-select advance-select" name="operation"
                                                id="user_id-operation">

                                                <option value="">{{ trans('select operation') }}</option>
                                                <option value="deposit">
                                                    {{ trans('deposit') }}</option>
                                                <option value="withdraw">
                                                    {{ trans('withdraw') }}</option>

                                            </select>

                                        </div>

                                        <div class="form-group mb-3 col-md-6">
                                            <label class="required" for="amount">{{ trans('points.amount') }}</label>
                                            <input type="number" name="amount" class="form-control "
                                                placeholder="{{ trans('Enter point.amount') }} " value="">

                                        </div>

                                        <div class="form-group mb-3 col-md-6">
                                            <label  for="expire_at">{{ trans('expire at') }}</label>
                                            <input type="date" name="expire_at" class="form-control "
                                                placeholder="{{ trans('Enter expire at') }} " value="">

                                        </div>
                                        <div class="col-lg-9 ml-lg-auto">
                                            <button type="submit"
                                                class="btn btn-primary font-weight-bold mr-2">{{ trans('Submit') }}</button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                        </div>
                    </div>
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
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <link href="{{ asset('control') }}/js/custom/crud/form.css" rel="stylesheet" type="text/css" />

    <link href="{{ asset('control') }}/js/custom/crud/show.css" rel="stylesheet" type="text/css" />
@endpush
@push('js')
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/TableDnD/1.0.5/jquery.tablednd.js"></script>
    <script src="{{ asset('control') }}/js/custom/crud/form.js"></script>
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
        $('#updatePassword').submit(function(e) {
            e.preventDefault();
            let password = $(this).find('#password').val();
            let action = $(this).attr('action');
            $.ajax({
                type: "Patch",
                url: action,
                data: {
                    password: password
                },
                dataType: "json",
                beforeSend: function() {
                    // Code to run before the request is sent
                    $('.loader-rm-wrapper').removeClass('hide-loader')
                },
                success: function(response) {
                    Swal.fire({
                        text: response.message,
                        icon: "success",
                        buttonsStyling: false,
                        confirmButtonText: "Ok",
                        customClass: {
                            confirmButton: "btn fw-bold btn-success",
                        }
                    });
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    // Code to run if the request fails
                    response = JSON.parse(jqXHR.responseText);
                    Swal.fire({
                        text: response.message,
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: "Ok",
                        customClass: {
                            confirmButton: "btn fw-bold btn-success",
                        }
                    })
                    $.each(response.errors, function(key, array) {
                        $.each(array, function(index, error) {
                            toastr.error(error, key);
                        });
                    });
                },
                complete: function() {
                    $('.loader-rm-wrapper').addClass('hide-loader')
                }
            });

        });
        $('#technicalsSelect').hide();
        $('#roles').change(function(e) {
            e.preventDefault();
            let selectedOptions = $(this).find(':selected');
            let dataNames = selectedOptions.map(function() {
                return $(this).data('name'); // Get each option's data-name
            }).get(); // Convert to array
            if (dataNames.includes('operator')) {
                $('#technicalsSelect').show();
            } else {
                $('#technicalsSelect').hide();
                $('#technicals').val(null).change();
            }

        });
    </script>
@endpush
