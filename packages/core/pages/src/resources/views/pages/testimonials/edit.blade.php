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
                    <h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1"></h1>
                    <!--end::Title-->
                    <!--begin::Separator-->
                    <span class="h-20px border-gray-200 border-start mx-4"></span>
                    <!--end::Separator-->
                    <!--begin::Breadcrumb-->
                    <ul class="breadcrumb breadcrumb-separatorless fw-bold fs-7 my-1">
                        <!--begin::Item-->
                        <li class="breadcrumb-item text-muted">
                            <a href="" class="text-muted text-hover-primary">@lang('Home')</a>
                        </li>
                        <!--end::Item-->

                        <!--begin::Item-->
                        <li class="breadcrumb-item text-muted">@lang('pages')</li>
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
                <div class="card testimonial-editor-card">


                    <form class="form testimonial-editor-form" method="POST" id="operation-form" data-id="{{ $item->id ?? null }}"
                        redirect-to="{{ route('dashboard.testimonials.index') }}"
                        @if (isset($item)) action="{{ route('dashboard.testimonials.edit', $item->id) }}"
                            data-mode="edit"
                        @else
                            action="{{ route('dashboard.testimonials.create') }}"
                            data-mode="new" @endif>

                        @csrf
                        <div class="card-body row">

                            <div class="col-12 d-flex justify-content-start mb-2 testimonial-back-row">
                                <a href="{{ route('dashboard.testimonials.index') }}" class="btn btn-light testimonial-back-button" id="testimonial-back-button">
                                    <i class="fas fa-arrow-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}"></i>
                                    {{ app()->getLocale() === 'ar' ? 'رجوع' : 'Back' }}
                                </a>
                            </div>

                            <!-- Name Field -->
                            <div class="col-12 row mt-3">
                                <ul class="nav nav-tabs" id="languageTabs" role="tablist">

                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link {{ app()->getLocale() === 'en' ? 'active' : '' }}" id="name-en-tab" data-bs-toggle="tab"
                                            data-bs-target="#name-en" type="button" role="tab"
                                            aria-controls="name-en" aria-selected=" true">{{ trans('English') }}</button>
                                    </li>

                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link {{ app()->getLocale() === 'ar' ? 'active' : '' }}" id="name-ar-tab" data-bs-toggle="tab"
                                            data-bs-target="#name-ar" type="button" role="tab"
                                            aria-controls="name-ar" aria-selected=" false">{{ trans('arabic') }}</button>
                                    </li>

                                </ul>
                                <div class="tab-content mt-3" id="languageTabsContent">
                                    <div class="tab-pane fade {{ app()->getLocale() === 'en' ? 'show active' : '' }}" id="name-en" role="tabpanel"
                                        aria-labelledby="en-tab">

                                        <div class="form-group mb-3 col-md-12">
                                            <label class="required" for="name">{{ trans('name') }}</label>
                                            <input type="text" name="translations[en][name]" class="form-control "
                                                placeholder="{{ trans('Enter name') }} "
                                                value="@if (isset($item)) {{ $item?->translate('en')?->name }} @endif">

                                        </div>

                                    </div>
                                    <div class="tab-pane fade {{ app()->getLocale() === 'ar' ? 'show active' : '' }}" id="name-ar" role="tabpanel" aria-labelledby="ar-tab">

                                        <div class="form-group mb-3 col-md-12">
                                            <label class="required" for="name">{{ trans('name') }}</label>
                                            <input type="text" name="translations[ar][name]" class="form-control "
                                                placeholder="{{ trans('Enter name') }} "
                                                value="@if (isset($item)) {{ $item?->translate('ar')?->name }} @endif">

                                        </div>

                                    </div>
                                </div>
                            </div>

                            <!-- Title/Position Field -->
                            <div class="col-12 row mt-3">
                                <ul class="nav nav-tabs" id="languageTabs2" role="tablist">

                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link {{ app()->getLocale() === 'en' ? 'active' : '' }}" id="title-en-tab" data-bs-toggle="tab"
                                            data-bs-target="#title-en" type="button" role="tab"
                                            aria-controls="title-en" aria-selected="{{ app()->getLocale() === 'en' ? 'true' : 'false' }}">{{ trans('English') }}</button>
                                    </li>

                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link {{ app()->getLocale() === 'ar' ? 'active' : '' }}" id="title-ar-tab" data-bs-toggle="tab"
                                            data-bs-target="#title-ar" type="button" role="tab"
                                            aria-controls="title-ar" aria-selected="{{ app()->getLocale() === 'ar' ? 'true' : 'false' }}">{{ app()->getLocale() === 'ar' ? 'العربية' : 'Arabic' }}</button>
                                    </li>

                                </ul>
                                <div class="tab-content mt-3" id="languageTabsContent2">
                                    <div class="tab-pane fade {{ app()->getLocale() === 'en' ? 'show active' : '' }}" id="title-en" role="tabpanel"
                                        aria-labelledby="en-tab">

                                        <div class="form-group mb-3 col-md-12">
                                            <label class="required" for="title">{{ app()->getLocale() === 'ar' ? 'المسمى الوظيفي / الصفة' : 'Title / Position' }}</label>
                                            <input type="text" name="translations[en][title]" class="form-control "
                                                placeholder="{{ trans('Enter title or position') }} "
                                                value="@if (isset($item)) {{ $item?->translate('en')?->title }} @endif">

                                        </div>

                                    </div>
                                    <div class="tab-pane fade {{ app()->getLocale() === 'ar' ? 'show active' : '' }}" id="title-ar" role="tabpanel" aria-labelledby="ar-tab">

                                        <div class="form-group mb-3 col-md-12">
                                            <label class="required" for="title">{{ app()->getLocale() === 'ar' ? 'المسمى الوظيفي / الصفة' : 'Title / Position' }}</label>
                                            <input type="text" name="translations[ar][title]" class="form-control "
                                                placeholder="{{ trans('Enter title or position') }} "
                                                value="@if (isset($item)) {{ $item?->translate('ar')?->title }} @endif">

                                        </div>

                                    </div>
                                </div>
                            </div>

                            <!-- Body/Content Field -->
                            <div class="col-12 row mt-3">
                                <ul class="nav nav-tabs" id="languageTabs3" role="tablist">

                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link {{ app()->getLocale() === 'en' ? 'active' : '' }}" id="body-en-tab" data-bs-toggle="tab"
                                            data-bs-target="#body-en" type="button" role="tab"
                                            aria-controls="body-en"
                                            aria-selected=" true">{{ trans('English') }}</button>
                                    </li>

                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link {{ app()->getLocale() === 'ar' ? 'active' : '' }}" id="body-ar-tab" data-bs-toggle="tab"
                                            data-bs-target="#body-ar" type="button" role="tab"
                                            aria-controls="body-ar"
                                            aria-selected=" false">{{ trans('arabic') }}</button>
                                    </li>

                                </ul>
                                <div class="tab-content mt-3" id="languageTabsContent3">
                                    <div class="tab-pane fade {{ app()->getLocale() === 'en' ? 'show active' : '' }}" id="body-en" role="tabpanel"
                                        aria-labelledby="en-tab">

                                        <div class="form-group mb-3 col-md-12">
                                            <label class="" for="body">{{ app()->getLocale() === 'ar' ? 'نص الرأي' : 'Review' }}</label>
                                            <textarea name="translations[en][body]" class="form-control " rows="4"
                                                placeholder="{{ trans('Enter testimonial content') }} ">@if (isset($item)){{ $item?->translate('en')?->body }}@endif</textarea>

                                        </div>

                                    </div>
                                    <div class="tab-pane fade {{ app()->getLocale() === 'ar' ? 'show active' : '' }}" id="body-ar" role="tabpanel"
                                        aria-labelledby="ar-tab">

                                        <div class="form-group mb-3 col-md-12">
                                            <label class="" for="body">{{ app()->getLocale() === 'ar' ? 'نص الرأي' : 'Review' }}</label>
                                            <textarea name="translations[ar][body]" class="form-control " rows="4"
                                                placeholder="{{ trans('Enter testimonial content') }} ">@if (isset($item)){{ $item?->translate('ar')?->body }}@endif</textarea>

                                        </div>

                                    </div>
                                </div>
                            </div>

                            <!-- Location Field -->
                            <div class="col-12 row mt-3">
                                <ul class="nav nav-tabs" id="languageTabs4" role="tablist">

                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link {{ app()->getLocale() === 'en' ? 'active' : '' }}" id="location-en-tab" data-bs-toggle="tab"
                                            data-bs-target="#location-en" type="button" role="tab"
                                            aria-controls="location-en"
                                            aria-selected=" true">{{ trans('English') }}</button>
                                    </li>

                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link {{ app()->getLocale() === 'ar' ? 'active' : '' }}" id="location-ar-tab" data-bs-toggle="tab"
                                            data-bs-target="#location-ar" type="button" role="tab"
                                            aria-controls="location-ar"
                                            aria-selected=" false">{{ trans('arabic') }}</button>
                                    </li>

                                </ul>
                                <div class="tab-content mt-3" id="languageTabsContent4">
                                    <div class="tab-pane fade {{ app()->getLocale() === 'en' ? 'show active' : '' }}" id="location-en" role="tabpanel"
                                        aria-labelledby="en-tab">

                                        <div class="form-group mb-3 col-md-12">
                                            <label class="required" for="location">{{ trans('location') }}</label>
                                            <input type="text" name="translations[en][location]" class="form-control "
                                                placeholder="{{ trans('Enter location') }} "
                                                value="@if (isset($item)) {{ $item?->translate('en')?->location }} @endif">

                                        </div>

                                    </div>
                                    <div class="tab-pane fade {{ app()->getLocale() === 'ar' ? 'show active' : '' }}" id="location-ar" role="tabpanel"
                                        aria-labelledby="ar-tab">

                                        <div class="form-group mb-3 col-md-12">
                                            <label class="required" for="location">{{ trans('location') }}</label>
                                            <input type="text" name="translations[ar][location]" class="form-control "
                                                placeholder="{{ trans('Enter location') }} "
                                                value="@if (isset($item)) {{ $item?->translate('ar')?->location }} @endif">

                                        </div>

                                    </div>
                                </div>
                            </div>

                            <!-- Avatar Field -->
                            <div class="form-group mb-3 col-md-6 mt-3">
                                <label class="" for="avatar">{{ trans('avatar') }}</label>
                                <div class="media-center-group form-control" data-max="1" data-type="image">
                                    <input type="text" hidden="hidden" class="form-control" name="avatar"
                                        value="{{ old('avatar', $item->avatar ?? null) }}">
                                    <button type="button" class="btn btn-secondary media-center-load"
                                        style="margin-top: 10px;"><i class="fa fa-file-upload"></i></button>
                                    <div class="input-gallery"></div>
                                </div>
                            </div>

                            <!-- Is Active Field -->
                            <div class="form-group mb-3 col-md-3 mt-3">
                                <label class="" for="is_active">{{ trans('is active') }}</label>
                                <select name="is_active" class="form-control">
                                    <option value="1" {{ (isset($item) && $item->is_active) ? 'selected' : '' }}>@lang('active')</option>
                                    <option value="0" {{ (isset($item) && !$item->is_active) ? 'selected' : '' }}>@lang('inactive')</option>
                                </select>
                            </div>

                            

                        </div>
                        <div class="card-footer">
                            <div class="row">
                                <div class="col-lg-9 ml-lg-auto">
                                    <button type="submit"
                                        class="btn btn-primary font-weight-bold mr-2">{{ trans('Submit') }}</button>
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
    <style>
        .testimonial-editor-card { border: 0; border-radius: 16px; box-shadow: 0 8px 28px rgba(31, 51, 100, .07); }
        .testimonial-back-row { direction: ltr; }
        .testimonial-back-button { margin-right: auto; }
        .testimonial-editor-form .card-body { padding: clamp(16px, 2.4vw, 30px); row-gap: 8px; }
        .testimonial-editor-form .form-group { margin-bottom: 14px !important; }
        .testimonial-editor-form .nav-tabs { border-bottom-color: #e7ebf2; }
        .testimonial-editor-form .tab-content { padding-top: 4px; }
        .testimonial-editor-form textarea { min-height: 104px; resize: vertical; }
        .testimonial-editor-form .card-footer { position: sticky; bottom: 0; z-index: 3; background: rgba(255,255,255,.96); border-top: 1px solid #edf0f5; padding: 12px 24px; }
        .testimonial-editor-form .card-footer .col-lg-9 { width: 100%; text-align: end; }
        @media (max-width: 767px) { .testimonial-editor-form .card-body { padding: 14px; } .testimonial-editor-form .nav-tabs .nav-link { padding: 8px 12px; } }
    </style>
@endpush
@push('js')
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/TableDnD/1.0.5/jquery.tablednd.js"></script>
    <script src="{{ asset('control') }}/js/custom/crud/form.js"></script>
    <script>
        (function () {
            var backButton = document.getElementById('testimonial-back-button');
            var form = document.getElementById('operation-form');
            if (!backButton || !form) return;

            try {
                var previousPage = new URL(document.referrer);
                if (previousPage.origin === window.location.origin) {
                    backButton.href = previousPage.href;
                    form.setAttribute('redirect-to', previousPage.href);
                }
            } catch (error) {
                // Keep the testimonials list as the safe fallback destination.
            }
        })();
    </script>
@endpush

