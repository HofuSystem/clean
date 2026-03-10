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
                        <li class="breadcrumb-item text-muted">@lang('settings')</li>
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
                <div class="card">

                    <form class="form" method="POST" id="setting-form" data-id="{{ $item->id ?? null }}"
                        action="{{ route('dashboard.settings.settings') }}" data-mode="new">

                        @csrf
                        <div class="card-body row">
                            <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="pills-home-tab" data-bs-toggle="pill"
                                        data-bs-target="#pills-home" type="button" role="tab" aria-controls="pills-home"
                                        aria-selected="true">{{ trans('Main') }}</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="pills-profile-tab" data-bs-toggle="pill"
                                        data-bs-target="#pills-profile" type="button" role="tab"
                                        aria-controls="pills-profile" aria-selected="false">{{ trans('Content') }}</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="pills-contact-tab" data-bs-toggle="pill"
                                        data-bs-target="#pills-contact" type="button" role="tab"
                                        aria-controls="pills-contact" aria-selected="false">{{ trans('social') }}</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="pills-points-tab" data-bs-toggle="pill"
                                        data-bs-target="#pills-points" type="button" role="tab" aria-controls="pills-points"
                                        aria-selected="false">{{ trans('points') }}</button>
                                </li>

                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="pills-notification-tab" data-bs-toggle="pill"
                                        data-bs-target="#pills-notification" type="button" role="tab"
                                        aria-controls="pills-notification"
                                        aria-selected="false">{{ trans('notification') }}</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="pills-login-tab" data-bs-toggle="pill"
                                        data-bs-target="#pills-login" type="button" role="tab" aria-controls="pills-login"
                                        aria-selected="false">{{ trans('login') }}</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="pills-referral-tab" data-bs-toggle="pill"
                                        data-bs-target="#pills-referral" type="button" role="tab"
                                        aria-controls="pills-referral"
                                        aria-selected="false">{{ trans('referral') }}</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="pills-messages-tab" data-bs-toggle="pill"
                                        data-bs-target="#pills-messages" type="button" role="tab"
                                        aria-controls="pills-messages"
                                        aria-selected="false">{{ trans('messages') }}</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="pills-time-diff-tab" data-bs-toggle="pill"
                                        data-bs-target="#pills-time-diff" type="button" role="tab"
                                        aria-controls="pills-time-diff"
                                        aria-selected="false">{{ trans('time diff') }}</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="pills-delivery-tab" data-bs-toggle="pill"
                                        data-bs-target="#pills-delivery" type="button" role="tab"
                                        aria-controls="pills-delivery"
                                        aria-selected="false">{{ trans('delivery and orders') }}</button>
                                </li>

                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="pills-notification-messages-tab" data-bs-toggle="pill"
                                        data-bs-target="#pills-notification-messages" type="button" role="tab"
                                        aria-controls="pills-notification-messages"
                                        aria-selected="false">{{ trans('notification messages') }}</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="pills-week-prices-tab" data-bs-toggle="pill"
                                        data-bs-target="#pills-week-prices" type="button" role="tab"
                                        aria-controls="pills-week-prices"
                                        aria-selected="false">{{ trans('week prices') }}</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="pills-testing-tab" data-bs-toggle="pill"
                                        data-bs-target="#pills-testing" type="button" role="tab"
                                        aria-controls="pills-testing"
                                        aria-selected="false">{{ trans('testingAccounts') }}</button>
                                </li>

                            </ul>
                            <div class="tab-content" id="pills-tabContent">
                                <div class="tab-pane fade show active" id="pills-home" role="tabpanel"
                                    aria-labelledby="pills-home-tab" tabindex="0">
                                    <div class="row">
                                        <div class="col-12 mt-1">
                                            <ul class="nav nav-tabs" id="languageTabs" role="tablist">

                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link active " id="text-en-tab" data-bs-toggle="tab"
                                                        data-bs-target="#text-en" type="button" role="tab"
                                                        aria-controls="text-en"
                                                        aria-selected=" true">{{ trans('English') }}</button>
                                                </li>

                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link  " id="text-ar-tab" data-bs-toggle="tab"
                                                        data-bs-target="#text-ar" type="button" role="tab"
                                                        aria-controls="text-ar"
                                                        aria-selected=" false">{{ trans('العربية') }}</button>
                                                </li>

                                            </ul>
                                            <div class="tab-content mt-3" id="languageTabsContent">
                                                <div class="tab-pane fade show active" id="text-en" role="tabpanel"
                                                    aria-labelledby="en-tab">

                                                    <div class="form-group mb-3 col-md-12">
                                                        <label class="required" for="text">{{ trans('name') }}</label>
                                                        <input type="text" name="name_en" class="form-control "
                                                            placeholder="{{ trans('Enter name') }} "
                                                            value="{{ $settings['name_en'] ?? null }}">

                                                    </div>

                                                </div>
                                                <div class="tab-pane fade " id="text-ar" role="tabpanel"
                                                    aria-labelledby="ar-tab">

                                                    <div class="form-group mb-3 col-md-12">
                                                        <label class="required" for="text">{{ trans('name') }}</label>
                                                        <input type="text" name="name_ar" class="form-control "
                                                            placeholder="{{ trans('Enter name') }} "
                                                            value="{{ $settings['name_ar'] ?? null }}">

                                                    </div>

                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group mb-3 col-md-12">
                                            <label class="" for="email">{{ trans('email') }}</label>
                                            <input type="email" name="email" class="form-control "
                                                placeholder="{{ trans('Enter email') }} "
                                                value="{{ $settings['email'] ?? null }}">

                                        </div>

                                        <div class="form-group mb-3 col-md-6">
                                            <label class="" for="phone">{{ trans('phone') }}</label>
                                            <input type="text" name="phone" class="form-control "
                                                placeholder="{{ trans('Enter phone') }} "
                                                value="{{ $settings['phone'] ?? null }}">

                                        </div>

                                        <div class="form-group mb-3 col-md-6">
                                            <label class="" for="whatsapp">{{ trans('whatsapp') }}</label>
                                            <input type="text" name="whatsapp" class="form-control "
                                                placeholder="{{ trans('Enter whatsapp') }} "
                                                value="{{ $settings['whatsapp'] ?? null }}">

                                        </div>

                                        <div class="form-group mb-3 col-md-6">
                                            <label class="" for="address">{{ trans('address') }}</label>
                                            <input type="text" name="address" class="form-control "
                                                placeholder="{{ trans('Enter address') }} "
                                                value="{{ $settings['address'] ?? null }}">

                                        </div>

                                        <div class="form-group mb-3 col-md-6">
                                            <label class="" for="maps">{{ trans('maps') }}</label>
                                            <input type="text" name="maps" class="form-control "
                                                placeholder="{{ trans('Enter maps') }} "
                                                value="{{ $settings['maps'] ?? null }}">

                                        </div>

                                        <div class="form-group mb-3 col-md-6">
                                            <label class=""
                                                for="clean_station_commercial_registration">{{ trans('commercial_registration') }}</label>
                                            <input type="text" name="clean_station_commercial_registration"
                                                class="form-control "
                                                placeholder="{{ trans('Enter commercial registration') }} "
                                                value="{{ $settings['clean_station_commercial_registration'] ?? null }}">
                                        </div>

                                        <div class="form-group mb-3 col-md-6">
                                            <label class="" for="clean_station_tax_number">{{ trans('tax_number') }}</label>
                                            <input type="text" name="clean_station_tax_number" class="form-control "
                                                placeholder="{{ trans('Enter tax number') }} "
                                                value="{{ $settings['clean_station_tax_number'] ?? null }}">
                                        </div>

                                        <div class="form-group mb-3 col-md-12">
                                            <label class="" for="logo">{{ trans('logo') }}</label>
                                            <div class="media-center-group form-control" data-max="1" data-type="image">
                                                <input type="text" hidden="hidden" class="form-control" name="logo"
                                                    value="{{ $settings['logo'] ?? null }}">
                                                <button type="button" class="btn btn-secondary media-center-load"
                                                    style="margin-top: 10px;"><i class="fa fa-file-upload"></i></button>
                                                <div class="input-gallery"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="pills-profile" role="tabpanel"
                                    aria-labelledby="pills-profile-tab" tabindex="0">
                                    <div class="row">
                                        <div class="col-12 mt-1">
                                            <ul class="nav nav-tabs" id="languageTabs" role="tablist">

                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link active " id="policy-en-tab" data-bs-toggle="tab"
                                                        data-bs-target="#policy-en" type="button" role="tab"
                                                        aria-controls="policy-en"
                                                        aria-selected=" true">{{ trans('English') }}</button>
                                                </li>

                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link  " id="policy-ar-tab" data-bs-toggle="tab"
                                                        data-bs-target="#policy-ar" type="button" role="tab"
                                                        aria-controls="policy-ar"
                                                        aria-selected=" false">{{ trans('العربية') }}</button>
                                                </li>

                                            </ul>
                                            <div class="tab-content mt-3" id="languageTabsContent">
                                                <div class="tab-pane fade show active" id="policy-en" role="tabpanel"
                                                    aria-labelledby="en-tab">

                                                    <div class="form-group mb-3 col-md-12">
                                                        <h5>{{ trans('policy') }}</h5>
                                                        <div class="editor-container">
                                                            <div id="policy-en" name="policy_en">
                                                                {!! $settings['policy_en'] ?? null !!}
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>
                                                <div class="tab-pane fade " id="policy-ar" role="tabpanel"
                                                    aria-labelledby="ar-tab">

                                                    <div class="form-group mb-3 col-md-12">
                                                        <h5>{{ trans('policy') }}</h5>

                                                        <div class="editor-container">
                                                            <div id="policy-ar" name="policy_ar">
                                                                {!! $settings['policy_ar'] ?? null !!}
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 mt-1">
                                            <ul class="nav nav-tabs" id="languageTabs" role="tablist">

                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link active " id="terms-en-tab" data-bs-toggle="tab"
                                                        data-bs-target="#terms-en" type="button" role="tab"
                                                        aria-controls="terms-en"
                                                        aria-selected=" true">{{ trans('English') }}</button>
                                                </li>

                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link  " id="terms-ar-tab" data-bs-toggle="tab"
                                                        data-bs-target="#terms-ar" type="button" role="tab"
                                                        aria-controls="terms-ar"
                                                        aria-selected=" false">{{ trans('العربية') }}</button>
                                                </li>

                                            </ul>
                                            <div class="tab-content mt-3" id="languageTabsContent">
                                                <div class="tab-pane fade show active" id="terms-en" role="tabpanel"
                                                    aria-labelledby="en-tab">

                                                    <div class="form-group mb-3 col-md-12">
                                                        <h5>{{ trans('terms') }}</h5>
                                                        <div class="editor-container">
                                                            <div id="terms-en" name="terms_en">
                                                                {!! $settings['terms_en'] ?? null !!}
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>
                                                <div class="tab-pane fade " id="terms-ar" role="tabpanel"
                                                    aria-labelledby="ar-tab">

                                                    <div class="form-group mb-3 col-md-12">
                                                        <h5>{{ trans('terms') }}</h5>
                                                        <div class="editor-container">
                                                            <div id="terms-ar" name="terms_ar">
                                                                {!! $settings['terms_ar'] ?? null !!}
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 mt-1">
                                            <ul class="nav nav-tabs" id="languageTabs" role="tablist">

                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link active " id="description-en-tab"
                                                        data-bs-toggle="tab" data-bs-target="#description-en" type="button"
                                                        role="tab" aria-controls="description-en"
                                                        aria-selected=" true">{{ trans('English') }}</button>
                                                </li>

                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link  " id="description-ar-tab" data-bs-toggle="tab"
                                                        data-bs-target="#description-ar" type="button" role="tab"
                                                        aria-controls="description-ar"
                                                        aria-selected=" false">{{ trans('العربية') }}</button>
                                                </li>

                                            </ul>
                                            <div class="tab-content mt-3" id="languageTabsContent">
                                                <div class="tab-pane fade show active" id="description-en" role="tabpanel"
                                                    aria-labelledby="en-tab">

                                                    <div class="form-group mb-3 col-md-12">
                                                        <h5>{{ trans('desc') }}</h5>
                                                        <div class="editor-container">
                                                            <div id="description-en" name="desc_en">
                                                                {!! $settings['desc_en'] ?? null !!}
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>
                                                <div class="tab-pane fade " id="description-ar" role="tabpanel"
                                                    aria-labelledby="ar-tab">

                                                    <div class="form-group mb-3 col-md-12">
                                                        <h5>{{ trans('desc') }}</h5>
                                                        <div class="editor-container">
                                                            <div id="description-ar" name="desc_ar">
                                                                {!! $settings['desc_ar'] ?? null !!}
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 mt-1">
                                            <ul class="nav nav-tabs" id="languageTabs" role="tablist">

                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link active " id="about_us-en-tab"
                                                        data-bs-toggle="tab" data-bs-target="#about_us-en" type="button"
                                                        role="tab" aria-controls="about_us-en"
                                                        aria-selected=" true">{{ trans('English') }}</button>
                                                </li>

                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link  " id="about_us-ar-tab" data-bs-toggle="tab"
                                                        data-bs-target="#about_us-ar" type="button" role="tab"
                                                        aria-controls="about_us-ar"
                                                        aria-selected=" false">{{ trans('العربية') }}</button>
                                                </li>

                                            </ul>
                                            <div class="tab-content mt-3" id="languageTabsContent">
                                                <div class="tab-pane fade show active" id="about_us-en" role="tabpanel"
                                                    aria-labelledby="en-tab">

                                                    <div class="form-group mb-3 col-md-12">
                                                        <h5>{{ trans('about') }}</h5>
                                                        <div class="editor-container">
                                                            <div id="about_us-en" name="about_en">
                                                                {!! $settings['about_en'] ?? null !!}
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>
                                                <div class="tab-pane fade " id="about_us-ar" role="tabpanel"
                                                    aria-labelledby="ar-tab">

                                                    <div class="form-group mb-3 col-md-12">
                                                        <h5>{{ trans('about') }}</h5>

                                                        <div class="editor-container">
                                                            <div id="about_us-ar" name="about_ar">
                                                                {!! $settings['about_ar'] ?? null !!}
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="pills-contact" role="tabpanel"
                                    aria-labelledby="pills-contact-tab" tabindex="0">
                                    <div class="row">

                                        <div class="form-group mb-3 col-md-6">
                                            <label class="" for="facebook">{{ trans('Facebook') }}</label>
                                            <input type="text" name="facebook" class="form-control "
                                                placeholder="{{ trans('Enter Facebook') }} "
                                                value="{{ $settings['facebook'] ?? null }}">

                                        </div>

                                        <div class="form-group mb-3 col-md-6">
                                            <label class="" for="twitter">{{ trans('Twitter') }}</label>
                                            <input type="text" name="twitter" class="form-control "
                                                placeholder="{{ trans('Enter Twitter') }} "
                                                value="{{ $settings['twitter'] ?? null }}">

                                        </div>

                                        <div class="form-group mb-3 col-md-6">
                                            <label class="" for="youtube">{{ trans('Youtube') }}</label>
                                            <input type="text" name="youtube" class="form-control "
                                                placeholder="{{ trans('Enter Youtube') }} "
                                                value="{{ $settings['youtube'] ?? null }}">

                                        </div>

                                        <div class="form-group mb-3 col-md-6">
                                            <label class="" for="instagram">{{ trans('Instagram') }}</label>
                                            <input type="text" name="instagram" class="form-control "
                                                placeholder="{{ trans('Enter Instagram') }} "
                                                value="{{ $settings['instagram'] ?? null }}">

                                        </div>

                                        <div class="form-group mb-3 col-md-6">
                                            <label class="" for="android_link">{{ trans('android link') }}</label>
                                            <input type="text" name="g_play_app" class="form-control "
                                                placeholder="{{ trans('Enter android link') }} "
                                                value="{{ $settings['g_play_app'] ?? null }}">

                                        </div>

                                        <div class="form-group mb-3 col-md-6">
                                            <label class="" for="ios_link">{{ trans('ios link') }}</label>
                                            <input type="text" name="app_store_app" class="form-control "
                                                placeholder="{{ trans('Enter ios link') }} "
                                                value="{{ $settings['app_store_app'] ?? null }}">

                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="pills-points" role="tabpanel"
                                    aria-labelledby="pills-points-tab" tabindex="0">
                                    <div class="row">
                                        <div class="form-group mb-3 col-md-6">
                                            <label class=""
                                                for="points_per_spent_riyal">{{ trans('points per spent riyal') }}</label>
                                            <input type="points_per_spent_riyal" name="points_per_spent_riyal"
                                                class="form-control "
                                                placeholder="{{ trans('Enter points_per_spent_riyal') }} "
                                                value="{{ $settings['points_per_spent_riyal'] ?? null }}">
                                        </div>
                                        <div class="form-group mb-3 col-md-6">
                                            <label class=""
                                                for="riyal_per_point_redeem ">{{ trans('riyal per point redeem') }}</label>
                                            <input type="riyal_per_point_redeem" name="riyal_per_point_redeem"
                                                class="form-control "
                                                placeholder="{{ trans('Enter riyal_per_point_redeem') }} "
                                                value="{{ $settings['riyal_per_point_redeem'] ?? null }}">
                                        </div>
                                        <div class="form-group mb-3 col-md-12">
                                            <label class=""
                                                for="minium_points_to_use ">{{ trans('minium points to use') }}</label>
                                            <input type="minium_points_to_use" name="minium_points_to_use"
                                                class="form-control "
                                                placeholder="{{ trans('Enter minium_points_to_use') }} "
                                                value="{{ $settings['minium_points_to_use'] ?? null }}">
                                        </div>
                                        <div class="form-group mb-3 col-md-12">
                                            <label class="" for="register_points ">{{ trans('register points') }}</label>
                                            <input type="register_points" name="register_points" class="form-control "
                                                placeholder="{{ trans('Enter register_points') }} "
                                                value="{{ $settings['register_points'] ?? null }}">
                                        </div>


                                    </div>
                                </div>
                                <div class="tab-pane fade" id="pills-notification" role="tabpanel"
                                    aria-labelledby="pills-notification-tab" tabindex="0">
                                    <div class="row">

                                        <div class="form-group mb-3 col-md-6">
                                            <label class="required"
                                                for="notify_client_using">{{ trans('notify client using') }}</label>
                                            <select class="custom-select  form-select advance-select"
                                                name="notify_client_using" id="notify_client_using" multiple>
                                                <option value="">{{ trans('select notify_client_using') }}</option>
                                                <option value="apps" @selected(str_contains($settings['notify_client_using'] ?? '', 'apps'))>{{ trans('apps') }}
                                                </option>
                                                <option value="email"
                                                    @selected(str_contains($settings['notify_client_using'] ?? '', 'email'))>{{ trans('email') }}
                                                </option>
                                                <option value="sms" @selected(str_contains($settings['notify_client_using'] ?? '', 'sms'))>{{ trans('sms') }}
                                                </option>
                                                <option value="whats_app"
                                                    @selected(str_contains($settings['notify_client_using'] ?? '', 'whats_app'))>
                                                    {{ trans('whats app') }}
                                                </option>
                                            </select>
                                        </div>
                                        <div class="form-group mb-3 col-md-6">
                                            <label class="required"
                                                for="notify_client_using">{{ trans('notify representatives using') }}</label>
                                            <select class="custom-select  form-select advance-select"
                                                name="notify_representatives_using" id="notify_representatives_using"
                                                multiple>
                                                <option value="">{{ trans('select notify_representatives_using') }}
                                                </option>
                                                <option value="apps"
                                                    @selected(str_contains($settings['notify_representatives_using'] ?? '', 'apps'))>{{ trans('apps') }}
                                                </option>
                                                <option value="email"
                                                    @selected(str_contains($settings['notify_representatives_using'] ?? '', 'email'))>{{ trans('email') }}
                                                </option>
                                                <option value="sms"
                                                    @selected(str_contains($settings['notify_representatives_using'] ?? '', 'sms'))>{{ trans('sms') }}
                                                </option>
                                                <option value="whats_app"
                                                    @selected(str_contains($settings['notify_representatives_using'] ?? '', 'whats_app'))>
                                                    {{ trans('whats app') }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="pills-login" role="tabpanel"
                                    aria-labelledby="pills-login-tab" tabindex="0">
                                    <div class="row">
                                        <div class="form-group mb-3 col-md-6">
                                            <label class="required" for="login_using">{{ trans('login using') }}</label>
                                            <select class="custom-select  form-select advance-select" name="login_using"
                                                id="login_using">
                                                <option value="">{{ trans('select login_using') }}</option>
                                                <option value="password" @selected(str_contains($settings['login_using'] ?? '', 'password'))>
                                                    {{ trans('password') }}
                                                </option>
                                                <option value="code" @selected(str_contains($settings['login_using'] ?? '', 'code'))>{{ trans('code') }}
                                                </option>
                                            </select>
                                        </div>
                                        <div class="form-group mb-3 col-md-6">
                                            <label class="required"
                                                for="notify_login_using">{{ trans('notify login using') }}</label>
                                            <select class="custom-select  form-select advance-select"
                                                name="notify_login_using" id="notify_login_using" multiple>
                                                <option value="">{{ trans('select notify_login_using') }}</option>
                                                <option value="apps" @selected(str_contains($settings['notify_login_using'] ?? "", 'apps'))>{{ trans('apps') }}</option>
                                                <option value="email" @selected(str_contains($settings['notify_login_using'] ?? "", 'email'))>
                                                    {{ trans('email') }}
                                                </option>
                                                <option value="sms" @selected(str_contains($settings['notify_login_using'] ?? "", 'sms'))>{{ trans('sms') }}</option>
                                                <option value="whats_app"
                                                    @selected(str_contains($settings['notify_login_using'] ?? "", 'whats_app'))>{{ trans('whats app') }}</option>
                                            </select>
                                        </div>

                                    </div>
                                </div>
                                <div class="tab-pane fade" id="pills-referral" role="tabpanel"
                                    aria-labelledby="pills-login-tab" tabindex="0">
                                    <div class="row">
                                        <div class="form-group mb-3 col-md-6">
                                            <label class="" for="referral_image_en">{{ trans('referral image en') }}</label>
                                            <div class="media-center-group form-control" data-max="1" data-type="image">
                                                <input type="text" hidden="hidden" class="form-control"
                                                    name="referral_image_en"
                                                    value="{{ $settings['referral_image_en'] ?? null }}">
                                                <button type="button" class="btn btn-secondary media-center-load"
                                                    style="margin-top: 10px;"><i class="fa fa-file-upload"></i></button>
                                                <div class="input-gallery"></div>
                                            </div>
                                        </div>
                                        <div class="form-group mb-3 col-md-6">
                                            <label class="" for="referral_image_ar">{{ trans('referral image ar') }}</label>
                                            <div class="media-center-group form-control" data-max="1" data-type="image">
                                                <input type="text" hidden="hidden" class="form-control"
                                                    name="referral_image_ar"
                                                    value="{{ $settings['referral_image_ar'] ?? null }}">
                                                <button type="button" class="btn btn-secondary media-center-load"
                                                    style="margin-top: 10px;"><i class="fa fa-file-upload"></i></button>
                                                <div class="input-gallery"></div>
                                            </div>
                                        </div>
                                        <div class="form-group mb-3 col-md-12">
                                            <label class="" for="referral_points ">{{ trans('referral points') }}</label>
                                            <input type="referral_points" name="referral_points" class="form-control "
                                                placeholder="{{ trans('Enter referral_points') }} "
                                                value="{{ $settings['referral_points'] ?? null }}">
                                        </div>
                                        <div class="form-group mb-3 col-md-6">
                                            <label class="" for="referral_riyals ">{{ trans('referral points') }}</label>
                                            <input type="referral_riyals" name="referral_riyals" class="form-control "
                                                placeholder="{{ trans('Enter referral_riyals') }} "
                                                value="{{ $settings['referral_riyals'] ?? null }}">
                                        </div>

                                    </div>
                                </div>
                                <div class="tab-pane fade" id="pills-messages" role="tabpanel"
                                    aria-labelledby="pills-messages-tab" tabindex="0">
                                    <div class="row">
                                        <div class="col-12 mt-1">
                                            <ul class="nav nav-tabs" id="languageTabs" role="tablist">

                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link active " id="message-en-tab"
                                                        data-bs-toggle="tab" data-bs-target="#message-en" type="button"
                                                        role="tab" aria-controls="message-en"
                                                        aria-selected=" true">{{ trans('English') }}</button>
                                                </li>

                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link  " id="message-ar-tab" data-bs-toggle="tab"
                                                        data-bs-target="#message-ar" type="button" role="tab"
                                                        aria-controls="message-ar"
                                                        aria-selected=" false">{{ trans('العربية') }}</button>
                                                </li>

                                            </ul>
                                            <div class="tab-content mt-3" id="languageTabsContent">
                                                <div class="tab-pane fade show active" id="message-en" role="tabpanel"
                                                    aria-labelledby="en-tab">

                                                    <div class="form-group mb-3 col-md-12">
                                                        <label class="required"
                                                            for="text">{{ trans('not_available_message') }}</label>
                                                        <input type="text" name="not_available_message_en"
                                                            class="form-control "
                                                            placeholder="{{ trans('Enter not_available_message') }} "
                                                            value="{{ $settings['not_available_message_en'] ?? null }}">

                                                    </div>

                                                </div>
                                                <div class="tab-pane fade " id="message-ar" role="tabpanel"
                                                    aria-labelledby="ar-tab">

                                                    <div class="form-group mb-3 col-md-12">
                                                        <label class="required"
                                                            for="text">{{ trans('not_available_message') }}</label>
                                                        <input type="text" name="not_available_message_ar"
                                                            class="form-control "
                                                            placeholder="{{ trans('Enter not_available_message') }} "
                                                            value="{{ $settings['not_available_message_ar'] ?? null }}">

                                                    </div>

                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <div class="tab-pane fade" id="pills-time-diff" role="tabpanel"
                                    aria-labelledby="pills-messages-tab" tabindex="0">
                                    <div class="row">
                                        <div class="form-group mb-3 col-md-12">
                                            <label class="" for="clothes_hours ">{{ trans('clothes hours') }}</label>
                                            <input type="clothes_hours" name="clothes_hours" class="form-control "
                                                placeholder="{{ trans('Enter clothes_hours') }} "
                                                value="{{ $settings['clothes_hours'] ?? null }}">
                                        </div>
                                        <div class="form-group mb-3 col-md-12">
                                            <label class="" for="carpets_hours ">{{ trans('carpets hours') }}</label>
                                            <input type="carpets_hours" name="carpets_hours" class="form-control "
                                                placeholder="{{ trans('Enter carpets_hours') }} "
                                                value="{{ $settings['carpets_hours'] ?? null }}">
                                        </div>
                                        <div class="form-group mb-3 col-md-12">
                                            <label class="" for="furniture_hours ">{{ trans('furniture hours') }}</label>
                                            <input type="furniture_hours" name="furniture_hours" class="form-control "
                                                placeholder="{{ trans('Enter furniture_hours') }} "
                                                value="{{ $settings['furniture_hours'] ?? null }}">
                                        </div>

                                    </div>
                                </div>
                                <div class="tab-pane fade" id="pills-delivery" role="tabpanel"
                                    aria-labelledby="pills-delivery-tab" tabindex="0">
                                    <div class="row">
                                        <div class="form-group mb-3 col-md-6">
                                            <label class=""
                                                for="first_order_min_price ">{{ trans('first order min price') }}</label>
                                            <input type="number" name="first_order_min_price" class="form-control "
                                                placeholder="{{ trans('Enter first order min price') }} "
                                                value="{{ $settings['first_order_min_price'] ?? null }}">
                                        </div>
                                        <div class="form-group mb-3 col-md-6">
                                            <label class="" for="order_min_price ">{{ trans('order min price') }}</label>
                                            <input type="number" name="order_min_price" class="form-control "
                                                placeholder="{{ trans('Enter order min price') }} "
                                                value="{{ $settings['order_min_price'] ?? null }}">
                                        </div>
                                        <div class="form-group mb-3 col-md-6">
                                            <label class="" for="free_delivery ">{{ trans('free delivery') }}</label>
                                            <input type="number" name="free_delivery" class="form-control "
                                                placeholder="{{ trans('Enter clothes_hours') }} "
                                                value="{{ $settings['free_delivery'] ?? null }}">
                                        </div>
                                        <div class="form-group mb-3 col-md-6">
                                            <label class="" for="delivery_charge ">{{ trans('delivery charge') }}</label>
                                            <input type="number" name="delivery_charge" class="form-control "
                                                placeholder="{{ trans('Enter delivery_charge') }} "
                                                value="{{ $settings['delivery_charge'] ?? null }}">
                                        </div>

                                        <div class="form-group mb-3 col-md-12">
                                            <label class="required"
                                                for="allowed_payment_methods">{{ trans('allowed payment methods') }}</label>
                                            <select class="custom-select form-select advance-select"
                                                name="allowed_payment_methods[]" id="allowed_payment_methods" multiple>
                                                <option value="">{{ trans('select allowed payment methods') }}</option>
                                                @php
                                                    $allowedMethods = isset($settings['allowed_payment_methods']) ? json_decode($settings['allowed_payment_methods'], true) : [];
                                                    $allowedMethods = is_array($allowedMethods) ? $allowedMethods : [];
                                                @endphp
                                                <option value="cash" @selected(in_array('cash', $allowedMethods))>
                                                    {{ trans('cash') }}
                                                </option>
                                                <option value="card" @selected(in_array('card', $allowedMethods))>
                                                    {{ trans('card') }}
                                                </option>
                                                <option value="points" @selected(in_array('points', $allowedMethods))>
                                                    {{ trans('points') }}
                                                </option>
                                                <option value="wallet" @selected(in_array('wallet', $allowedMethods))>
                                                    {{ trans('wallet') }}
                                                </option>
                                            </select>
                                        </div>
                                        <div class="form-group mb-3 col-md-6">
                                            <label class="required"
                                                for="max_carpet_area">{{ trans('Max Allowed Carpet Area (Width × Height)') }}</label>
                                            <input type="number" name="max_carpet_area" class="form-control" step="0.01"
                                                min="0" placeholder="{{ trans('Enter max carpet area in square meters') }}"
                                                value="{{ $settings['max_carpet_area'] ?? null }}">
                                            <small
                                                class="form-text text-muted">{{ trans('Maximum allowed carpet area (width × height) in square meters') }}</small>
                                        </div>
                                        <div class="form-group mb-3 col-md-6">
                                            <label class=""
                                                for="multiple_payment_fees">{{ trans('Multiple payment fees') }}</label>
                                            <input type="number" step="0.01" name="multiple_payment_fees"
                                                class="form-control "
                                                placeholder="{{ trans('Enter multiple payment fees') }} "
                                                value="{{ $settings['multiple_payment_fees'] ?? null }}">
                                            <small
                                                class="form-text text-muted">{{ trans('This amount will be applied for card payment when order has more than one payment (not applied for first payment)') }}</small>
                                        </div>


                                    </div>
                                </div>
                                <div class="tab-pane fade" id="pills-testing" role="tabpanel"
                                    aria-labelledby="pills-testing-tab" tabindex="0">
                                    <div class="row">
                                        <div class="form-group mb-3 col-md-12">
                                            <label class="" for="testing_accounts ">{{ trans('testing accounts') }}</label>
                                            <select class="form-control select2" name="testing_accounts[]"
                                                id="testing_accounts" multiple>
                                                @foreach ($users as $user)
                                                    <option value="{{ $user->id }}" @selected(in_array($user->id, json_decode($settings['testing_accounts'] ?? '[]') ?? []))>
                                                        {{ $user->phone }} - {{ $user->fullname }}
                                                    </option>
                                                @endforeach
                                            </select>

                                        </div>



                                    </div>
                                </div>
                                <div class="tab-pane fade" id="pills-week-prices" role="tabpanel"
                                    aria-labelledby="pills-week-prices-tab" tabindex="0">
                                    <div class="row">
                                        <div class="form-group mb-3 col-md-12">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h5 class="card-title mb-0">{{ trans('week prices') }}</h5>
                                                </div>
                                                <div class="card-body">
                                                    <div class="table-responsive">
                                                        <table class="table  table-striped table-hover"
                                                            id="week-prices-table">
                                                            <thead class="table-primary">
                                                                <tr>
                                                                    <th width="40%">{{ trans('week day') }}</th>
                                                                    <th width="40%">{{ trans('percentage') }}</th>
                                                                    <th width="20%">{{ trans('actions') }}</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="week-prices-tbody">
                                                                @if(isset($settings['week_prices']) && is_array(json_decode($settings['week_prices'], true)))
                                                                    @foreach(json_decode($settings['week_prices'], true) as $index => $weekPrice)
                                                                        <tr class="week-price-row">
                                                                            <td>
                                                                                <select name="week_prices[{{ $index }}][day]"
                                                                                    class="form-control week-day-select">
                                                                                    <option value="">{{ trans('select day') }}
                                                                                    </option>
                                                                                    <option value="monday"
                                                                                        @selected($weekPrice['day'] == 'monday')>
                                                                                        {{ trans('monday') }}
                                                                                    </option>
                                                                                    <option value="tuesday"
                                                                                        @selected($weekPrice['day'] == 'tuesday')>
                                                                                        {{ trans('tuesday') }}
                                                                                    </option>
                                                                                    <option value="wednesday"
                                                                                        @selected($weekPrice['day'] == 'wednesday')>
                                                                                        {{ trans('wednesday') }}
                                                                                    </option>
                                                                                    <option value="thursday"
                                                                                        @selected($weekPrice['day'] == 'thursday')>
                                                                                        {{ trans('thursday') }}
                                                                                    </option>
                                                                                    <option value="friday"
                                                                                        @selected($weekPrice['day'] == 'friday')>
                                                                                        {{ trans('friday') }}
                                                                                    </option>
                                                                                    <option value="saturday"
                                                                                        @selected($weekPrice['day'] == 'saturday')>
                                                                                        {{ trans('saturday') }}
                                                                                    </option>
                                                                                    <option value="sunday"
                                                                                        @selected($weekPrice['day'] == 'sunday')>
                                                                                        {{ trans('sunday') }}
                                                                                    </option>
                                                                                </select>
                                                                            </td>
                                                                            <td>
                                                                                <input type="number"
                                                                                    name="week_prices[{{ $index }}][percentage]"
                                                                                    class="form-control"
                                                                                    placeholder="{{ trans('Enter percentage') }}"
                                                                                    value="{{ $weekPrice['percentage'] ?? '' }}"
                                                                                    min="0" max="100" step="0.01">
                                                                            </td>
                                                                            <td>
                                                                                <button type="button"
                                                                                    class="btn btn-danger btn-sm remove-week-price">
                                                                                    <i class="fa fa-trash"></i>
                                                                                </button>
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach
                                                                @endif
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <div class="mt-3">
                                                        <button type="button" class="btn btn-primary" id="add-week-price">
                                                            <i class="fa fa-plus"></i> {{ trans('add week price') }}
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="pills-notification-messages" role="tabpanel"
                                    aria-labelledby="pills-notification-messages-tab" tabindex="0">
                                    <div class="row">
                                        <div class="form-group mb-3 col-md-12">
                                            <label class=""
                                                for="welcome_notification_title ">{{ trans('welcome notification title') }}</label>
                                            <input type="text" name="welcome_notification_title" class="form-control "
                                                placeholder="{{ trans('Enter welcome_notification_title') }} "
                                                value="{{ $settings['welcome_notification_title'] ?? null }}">
                                        </div>
                                        <div class="form-group mb-3 col-md-12">
                                            <label class=""
                                                for="welcome_notification_body ">{{ trans('welcome notification body') }}</label>
                                            <input type="text" name="welcome_notification_body" class="form-control "
                                                placeholder="{{ trans('Enter welcome_notification_body') }} "
                                                value="{{ $settings['welcome_notification_body'] ?? null }}">
                                        </div>
                                        <div class="form-group mb-3 col-md-12">
                                            <label class=""
                                                for="abandoned_cart_notification_title ">{{ trans('abandoned cart notification title') }}</label>
                                            <input type="text" name="abandoned_cart_notification_title"
                                                class="form-control "
                                                placeholder="{{ trans('Enter abandoned_cart_notification_title') }} "
                                                value="{{ $settings['abandoned_cart_notification_title'] ?? null }}">
                                        </div>
                                        <div class="form-group mb-3 col-md-12">
                                            <label class=""
                                                for="abandoned_cart_notification_body ">{{ trans('abandoned cart notification body') }}</label>
                                            <input type="text" name="abandoned_cart_notification_body" class="form-control "
                                                placeholder="{{ trans('Enter abandoned_cart_notification_body') }} "
                                                value="{{ $settings['abandoned_cart_notification_body'] ?? null }}">
                                        </div>
                                        <div class="form-group mb-3 col-md-12">
                                            <label class=""
                                                for="inactive_new_users_notification_title ">{{ trans('inactive new users notification title') }}</label>
                                            <input type="text" name="inactive_new_users_notification_title"
                                                class="form-control "
                                                placeholder="{{ trans('Enter inactive_new_users_notification_title') }} "
                                                value="{{ $settings['inactive_new_users_notification_title'] ?? null }}">
                                        </div>
                                        <div class="form-group mb-3 col-md-12">
                                            <label class=""
                                                for="inactive_new_users_notification_body ">{{ trans('inactive new users notification body') }}</label>
                                            <input type="text" name="inactive_new_users_notification_body"
                                                class="form-control "
                                                placeholder="{{ trans('Enter inactive_new_users_notification_body') }} "
                                                value="{{ $settings['inactive_new_users_notification_body'] ?? null }}">
                                        </div>
                                        <div class="form-group mb-3 col-md-12">
                                            <label class=""
                                                for="inactive_after_order_notification_title ">{{ trans('inactive after order notification title') }}</label>
                                            <input type="text" name="inactive_after_order_notification_title"
                                                class="form-control "
                                                placeholder="{{ trans('Enter inactive_after_order_notification_title') }} "
                                                value="{{ $settings['inactive_after_order_notification_title'] ?? null }}">
                                        </div>
                                        <div class="form-group mb-3 col-md-12">
                                            <label class=""
                                                for="inactive_after_order_notification_body ">{{ trans('inactive after order notification body') }}</label>
                                            <input type="text" name="inactive_after_order_notification_body"
                                                class="form-control "
                                                placeholder="{{ trans('Enter inactive_after_order_notification_body') }} "
                                                value="{{ $settings['inactive_after_order_notification_body'] ?? null }}">
                                        </div>
                                        <div class="form-group mb-3 col-md-12">
                                            <label class=""
                                                for="feedback_after_completion_notification_title ">{{ trans('feedback after completion notification title') }}</label>
                                            <input type="text" name="feedback_after_completion_notification_title"
                                                class="form-control "
                                                placeholder="{{ trans('Enter feedback_after_completion_notification_title') }} "
                                                value="{{ $settings['feedback_after_completion_notification_title'] ?? null }}">
                                        </div>
                                        <div class="form-group mb-3 col-md-12">
                                            <label class=""
                                                for="feedback_after_completion_notification_body ">{{ trans('feedback after completion notification body') }}</label>
                                            <input type="text" name="feedback_after_completion_notification_body"
                                                class="form-control "
                                                placeholder="{{ trans('Enter feedback_after_completion_notification_body') }} "
                                                value="{{ $settings['feedback_after_completion_notification_body'] ?? null }}">
                                        </div>
                                        <div class="form-group mb-3 col-md-12">
                                            <label class=""
                                                for="celebrate_birthday_notification_title ">{{ trans('celebrate birthday notification title') }}</label>
                                            <input type="text" name="celebrate_birthday_notification_title"
                                                class="form-control "
                                                placeholder="{{ trans('Enter celebrate_birthday_notification_title') }} "
                                                value="{{ $settings['celebrate_birthday_notification_title'] ?? null }}">
                                        </div>
                                        <div class="form-group mb-3 col-md-12">
                                            <label class=""
                                                for="celebrate_birthday_notification_body ">{{ trans('celebrate birthday notification body') }}</label>
                                            <input type="text" name="celebrate_birthday_notification_body"
                                                class="form-control "
                                                placeholder="{{ trans('Enter celebrate_birthday_notification_body') }} "
                                                value="{{ $settings['celebrate_birthday_notification_body'] ?? null }}">
                                        </div>

                                        <!-- No Order Notification Messages Table -->
                                        <div class="form-group mb-3 col-md-12">
                                            <hr>
                                            <h5>{{ trans('No Order Notification Messages') }}</h5>
                                            <p class="text-muted">
                                                {{ trans('Configure notifications sent after periods without orders') }}
                                            </p>

                                            <div class="table-responsive">
                                                <table class="table table-bordered" id="no-order-notifications-table">
                                                    <thead class="table-primary">
                                                        <tr>
                                                            <th>{{ trans('Days') }}</th>
                                                            <th>{{ trans('Notification Title') }}</th>
                                                            <th>{{ trans('Notification Body') }}</th>
                                                            <th>{{ trans('Added Money if(does not have it)') }}</th>
                                                            <th>{{ trans('Money Expiry (Days)') }}</th>
                                                            <th>{{ trans('Notes') }}</th>
                                                            <th>{{ trans('Active') }}</th>
                                                            <th>{{ trans('Actions') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="no-order-notifications-tbody">
                                                        @if(isset($noOrderNotifications) && count($noOrderNotifications) > 0)
                                                            @foreach($noOrderNotifications as $index => $notification)
                                                                <tr data-index="{{ $index }}">
                                                                    <td>
                                                                        <input type="number"
                                                                            name="no_order_notifications[{{ $index }}][days]"
                                                                            class="form-control" value="{{ $notification['days'] }}"
                                                                            min="1" required>
                                                                    </td>
                                                                    <td>
                                                                        <input type="text"
                                                                            name="no_order_notifications[{{ $index }}][notification_title]"
                                                                            class="form-control"
                                                                            value="{{ $notification['notification_title'] }}"
                                                                            required>
                                                                    </td>
                                                                    <td>
                                                                        <textarea
                                                                            name="no_order_notifications[{{ $index }}][notification_body]"
                                                                            class="form-control" rows="2"
                                                                            required>{{ $notification['notification_body'] }}</textarea>
                                                                    </td>
                                                                    <td>
                                                                        <input type="number"
                                                                            name="no_order_notifications[{{ $index }}][added_points]"
                                                                            class="form-control"
                                                                            value="{{ $notification['added_points'] ?? 0 }}" min="0"
                                                                            placeholder="0" step="0.01">
                                                                    </td>
                                                                    <td>
                                                                        <input type="number"
                                                                            name="no_order_notifications[{{ $index }}][money_expiry_days]"
                                                                            class="form-control"
                                                                            value="{{ $notification['money_expiry_days'] ?? 30 }}"
                                                                            min="1" placeholder="30">
                                                                    </td>
                                                                    <td>
                                                                        <input type="text"
                                                                            name="no_order_notifications[{{ $index }}][notes]"
                                                                            class="form-control"
                                                                            value="{{ $notification['notes'] ?? '' }}"
                                                                            placeholder="Notes">
                                                                    </td>
                                                                    <td>
                                                                        <div class="form-check">
                                                                            <input type="checkbox"
                                                                                name="no_order_notifications[{{ $index }}][is_active]"
                                                                                class="form-check-input" value="1" {{ ($notification['is_active'] ?? false) ? 'checked' : '' }}>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <button type="button"
                                                                            class="btn btn-danger btn-sm remove-notification-row">
                                                                            <i class="fa fa-trash"></i>
                                                                        </button>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        @endif
                                                    </tbody>
                                                </table>
                                            </div>

                                            <button type="button" class="btn btn-success btn-sm" id="add-notification-row">
                                                <i class="fa fa-plus"></i> {{ trans('Add Notification') }}
                                            </button>
                                        </div>

                                    </div>
                                </div>
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
                <!--end::Card-->
            </div>
            <!--end::Container-->
        </div>
        <!--end::Post-->
    </div>
    <!--end::Content-->
    @include('media::mediaCenter.modal')
@endsection
@push('css')
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <link href="{{ asset('control') }}/js/custom/crud/form.css" rel="stylesheet" type="text/css" />
@endpush
@push('js')
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/TableDnD/1.0.5/jquery.tablednd.js"></script>
    <script src="{{ asset('control') }}/js/custom/crud/form.js"></script>
    <script>
        $(document).ready(function () {
            drawMediaCenters($('#setting-form'));

            $(document).on('submit', '#setting-form', function (e) {
                e.preventDefault();
                let form = $(this);
                let data = getFormData($(this));
                let itemsData = getItemsData(form);
                for (let key in itemsData) {
                    data[key] = itemsData[key];
                }
                let url = $(this).attr('action');

                $.ajax({
                    type: "POST",
                    url: url,
                    data: data,
                    dataType: "json",
                    beforeSend: function () {
                        // Code to run before the request is sent
                        $('.loader-rm-wrapper').removeClass('hide-loader')
                    },
                    success: function (response) {
                        Swal.fire({
                            text: response.message,
                            icon: "success",
                            buttonsStyling: false,
                            confirmButtonText: "Ok",
                            customClass: {
                                confirmButton: "btn fw-bold btn-success",
                            }
                        })
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
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
                        $.each(response.errors, function (key, array) {
                            $.each(array, function (index, error) {
                                toastr.error(error, key);
                            });
                        });
                    },
                    complete: function () {
                        $('.loader-rm-wrapper').addClass('hide-loader')
                    }
                });
            });
            $('#login_using').change(function (e) {
                e.preventDefault();
                if ($(this).val() == 'code') {
                    $('#notify_login_using').parent().show()
                } else {
                    $('#notify_login_using').parent().hide()
                }

            });
            $('#login_using').change();

            // No Order Notifications functionality
            let notificationIndex = {{ isset($noOrderNotifications) ? count($noOrderNotifications) : 0 }};

            // Add new notification row
            $('#add-notification-row').click(function () {
                const newRow = `
                            <tr data-index="${notificationIndex}">
                                <td>
                                    <input type="number" name="no_order_notifications[${notificationIndex}][days]" 
                                           class="form-control" min="1" required>
                                </td>
                                <td>
                                    <input type="text" name="no_order_notifications[${notificationIndex}][notification_title]" 
                                           class="form-control" required>
                                </td>
                                <td>
                                    <textarea name="no_order_notifications[${notificationIndex}][notification_body]" 
                                              class="form-control" rows="2" required></textarea>
                                </td>
                                <td>
                                    <input type="number" name="no_order_notifications[${notificationIndex}][added_points]" 
                                           class="form-control" min="0" placeholder="0" value="0" step="0.01">
                                </td>
                                <td>
                                    <input type="number" name="no_order_notifications[${notificationIndex}][money_expiry_days]" 
                                           class="form-control" min="1" placeholder="30" value="30">
                                </td>
                                <td>
                                    <input type="text" name="no_order_notifications[${notificationIndex}][notes]" 
                                           class="form-control" placeholder="Notes" value="">
                                </td>
                                <td>
                                    <div class="form-check">
                                        <input type="checkbox" name="no_order_notifications[${notificationIndex}][is_active]" 
                                               class="form-check-input" value="1" checked>
                                    </div>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-danger btn-sm remove-notification-row">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        `;
                $('#no-order-notifications-tbody').append(newRow);
                notificationIndex++;
            });

            // Remove notification row
            $(document).on('click', '.remove-notification-row', function () {
                $(this).closest('tr').remove();
                updateNotificationIndices();
            });

            // Update indices after row removal
            function updateNotificationIndices() {
                $('#no-order-notifications-tbody tr').each(function (index) {
                    $(this).attr('data-index', index);
                    $(this).find('input, textarea').each(function () {
                        const name = $(this).attr('name');
                        if (name) {
                            const newName = name.replace(/\[\d+\]/, `[${index}]`);
                            $(this).attr('name', newName);
                        }
                    });
                });
                notificationIndex = $('#no-order-notifications-tbody tr').length;
            }

            // Week Prices functionality
            let weekPriceIndex = {{ isset($settings['week_prices']) ? count(json_decode($settings['week_prices'], true)) : 0 }};

            // Function to get available days (not already selected)
            function getAvailableDays() {
                const selectedDays = [];
                $('.week-day-select').each(function () {
                    const selectedValue = $(this).val();
                    if (selectedValue) {
                        selectedDays.push(selectedValue);
                    }
                });
                return selectedDays;
            }

            // Function to update day options based on selected days
            function updateDayOptions() {
                const selectedDays = getAvailableDays();

                $('.week-day-select').each(function () {
                    const currentValue = $(this).val();
                    const $select = $(this);

                    // Store current value
                    const currentSelected = $select.val();

                    // Clear and rebuild options
                    $select.empty();
                    $select.append(`<option value="">{{ trans('select day') }}</option>`);

                    const days = [
                        { value: 'monday', label: '{{ trans("monday") }}' },
                        { value: 'tuesday', label: '{{ trans("tuesday") }}' },
                        { value: 'wednesday', label: '{{ trans("wednesday") }}' },
                        { value: 'thursday', label: '{{ trans("thursday") }}' },
                        { value: 'friday', label: '{{ trans("friday") }}' },
                        { value: 'saturday', label: '{{ trans("saturday") }}' },
                        { value: 'sunday', label: '{{ trans("sunday") }}' }
                    ];

                    days.forEach(day => {
                        const isSelected = selectedDays.includes(day.value) && day.value !== currentSelected;
                        if (!isSelected) {
                            $select.append(`<option value="${day.value}">${day.label}</option>`);
                        }
                    });

                    // Restore current value if it's still valid
                    if (currentSelected && !selectedDays.includes(currentSelected) || currentSelected === currentSelected) {
                        $select.val(currentSelected);
                    }
                });
            }

            // Add new week price row
            $(document).on('click', '#add-week-price', function () {
                const selectedDays = getAvailableDays();
                const availableDays = [
                    'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'
                ].filter(day => !selectedDays.includes(day));

                if (availableDays.length === 0) {
                    Swal.fire({
                        text: '{{ trans("All week days have been added") }}',
                        icon: "warning",
                        buttonsStyling: false,
                        confirmButtonText: "Ok",
                        customClass: {
                            confirmButton: "btn fw-bold btn-warning",
                        }
                    });
                    return;
                }

                const newRow = `
                            <tr class="week-price-row">
                                <td>
                                    <select name="week_prices[${weekPriceIndex}][day]" class="form-control week-day-select">
                                        <option value="">{{ trans('select day') }}</option>
                                        ${availableDays.map(day => `<option value="${day}">{{ trans('${day}') }}</option>`).join('')}
                                    </select>
                                </td>
                                <td>
                                    <input type="number" name="week_prices[${weekPriceIndex}][percentage]" class="form-control"
                                           placeholder="{{ trans('Enter percentage') }}"
                                           min="0" max="100" step="0.01">
                                </td>
                                <td>
                                    <button type="button" class="btn btn-danger btn-sm remove-week-price">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        `;
                $('#week-prices-tbody').append(newRow);
                weekPriceIndex++;
            });

            // Remove week price row
            $(document).on('click', '.remove-week-price', function () {
                $(this).closest('tr').remove();
                updateDayOptions();
            });

            // Handle day selection change
            $(document).on('change', '.week-day-select', function () {
                updateDayOptions();
            });

            // Initialize day options on page load
            updateDayOptions();
        });
    </script>
@endpush