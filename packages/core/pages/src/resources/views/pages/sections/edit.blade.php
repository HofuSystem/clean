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
                <div class="card">


                    <form class="form" method="POST" id="operation-form" data-id="{{ $item->id ?? null }}"
                        @if (isset($item)) action="{{ route('dashboard.sections.edit', $item->id) }}"
                            data-mode="edit"
                        @else
                            action="{{ route('dashboard.sections.create') }}"
                            data-mode="new" @endif>

                        @csrf
                        <div class="card-body row">

                            <div class="col-12 row mt-5">
                                <ul class="nav nav-tabs" id="languageTabs" role="tablist">

                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active " id="title-en-tab" data-bs-toggle="tab"
                                            data-bs-target="#title-en" type="button" role="tab"
                                            aria-controls="title-en" aria-selected=" true">{{ trans('English') }}</button>
                                    </li>

                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link  " id="title-ar-tab" data-bs-toggle="tab"
                                            data-bs-target="#title-ar" type="button" role="tab"
                                            aria-controls="title-ar" aria-selected=" false">{{ trans('arabic') }}</button>
                                    </li>

                                </ul>
                                <div class="tab-content mt-3" id="languageTabsContent">
                                    <div class="tab-pane fade show active" id="title-en" role="tabpanel"
                                        aria-labelledby="en-tab">

                                        <div class="form-group mb-3 col-md-12">
                                            <label class="required" for="title">{{ trans('title') }}</label>
                                            <input type="text" name="translations[en][title]" class="form-control "
                                                placeholder="{{ trans('Enter title') }} "
                                                value="@if (isset($item)) {{ $item?->translate('en')?->title }} @endif">

                                        </div>

                                    </div>
                                    <div class="tab-pane fade " id="title-ar" role="tabpanel" aria-labelledby="ar-tab">

                                        <div class="form-group mb-3 col-md-12">
                                            <label class="required" for="title">{{ trans('title') }}</label>
                                            <input type="text" name="translations[ar][title]" class="form-control "
                                                placeholder="{{ trans('Enter title') }} "
                                                value="@if (isset($item)) {{ $item?->translate('ar')?->title }} @endif">

                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="col-12 row mt-5">
                                <ul class="nav nav-tabs" id="languageTabs" role="tablist">

                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active " id="small_title-en-tab" data-bs-toggle="tab"
                                            data-bs-target="#small_title-en" type="button" role="tab"
                                            aria-controls="small_title-en"
                                            aria-selected=" true">{{ trans('English') }}</button>
                                    </li>

                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link  " id="small_title-ar-tab" data-bs-toggle="tab"
                                            data-bs-target="#small_title-ar" type="button" role="tab"
                                            aria-controls="small_title-ar"
                                            aria-selected=" false">{{ trans('arabic') }}</button>
                                    </li>

                                </ul>
                                <div class="tab-content mt-3" id="languageTabsContent">
                                    <div class="tab-pane fade show active" id="small_title-en" role="tabpanel"
                                        aria-labelledby="en-tab">

                                        <div class="form-group mb-3 col-md-12">
                                            <label class="" for="small_title">{{ trans('small title') }}</label>
                                            <input type="text" name="translations[en][small_title]"
                                                class="form-control " placeholder="{{ trans('Enter small title') }} "
                                                value="@if (isset($item)) {{ $item?->translate('en')?->small_title }} @endif">

                                        </div>

                                    </div>
                                    <div class="tab-pane fade " id="small_title-ar" role="tabpanel"
                                        aria-labelledby="ar-tab">

                                        <div class="form-group mb-3 col-md-12">
                                            <label class="" for="small_title">{{ trans('small title') }}</label>
                                            <input type="text" name="translations[ar][small_title]"
                                                class="form-control " placeholder="{{ trans('Enter small title') }} "
                                                value="@if (isset($item)) {{ $item?->translate('ar')?->small_title }} @endif">

                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="col-12 row mt-5">
                                <ul class="nav nav-tabs" id="languageTabs" role="tablist">

                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active " id="description-en-tab" data-bs-toggle="tab"
                                            data-bs-target="#description-en" type="button" role="tab"
                                            aria-controls="description-en"
                                            aria-selected=" true">{{ trans('English') }}</button>
                                    </li>

                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link  " id="description-ar-tab" data-bs-toggle="tab"
                                            data-bs-target="#description-ar" type="button" role="tab"
                                            aria-controls="description-ar"
                                            aria-selected=" false">{{ trans('arabic') }}</button>
                                    </li>

                                </ul>
                                <div class="tab-content mt-3" id="languageTabsContent">
                                    <div class="tab-pane fade show active" id="description-en" role="tabpanel"
                                        aria-labelledby="en-tab">

                                        <div class="form-group mb-3 col-md-12">
                                            <label class="" for="description">{{ trans('description') }}</label>
                                            <div class="editor-container">
                                                <div id="description" name="translations[en][description]">
                                                    @if (isset($item))
                                                        {!! $item?->translate('en')?->description !!}
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="tab-pane fade " id="description-ar" role="tabpanel"
                                        aria-labelledby="ar-tab">

                                        <div class="form-group mb-3 col-md-12">
                                            <label class="" for="description">{{ trans('description') }}</label>
                                            <div class="editor-container">
                                                <div id="description" name="translations[ar][description]">
                                                    @if (isset($item))
                                                        {!! $item?->translate('ar')?->description !!}
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="form-group mb-3 col-md-12">
                                <label class="" for="images">{{ trans('images') }}</label>
                                <div class="media-center-group form-control" data-max="3" data-type="gallery">
                                    <input type="text" hidden="hidden" class="form-control" name="images"
                                        value="{{ old('images', $item->images ?? null) }}">
                                    <button type="button" class="btn btn-secondary media-center-load"
                                        style="margin-top: 10px;"><i class="fa fa-file-upload"></i></button>
                                    <div class="input-gallery"></div>
                                </div>
                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <label class="" for="video">{{ trans('video') }}</label>
                                <input type="text" name="video" class="form-control "
                                    placeholder="{{ trans('Enter video') }} "
                                    value="@if (isset($item)) {{ $item->video }} @endif">

                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <label class="" for="template">{{ trans('template') }}</label>
                                <select class="custom-select  form-select advance-select" name="template" id="template">
                                    <option value="">{{ trans('Select template') }}</option>
                                    <option value="about-hero">{{ trans('about-hero') }}</option>
                                    <option value="about-content">{{ trans('about-content') }}</option>
                                    <option value="about-features-slider">{{ trans('about-features-slider') }}</option>
                                    <option value="about-video-banner">{{ trans('about-video-banner') }}</option>
                                    <option value="about-why-us">{{ trans('about-why-us') }}</option>
                                    <option value="about-stats">{{ trans('about-stats') }}</option>
                                    <option value="services-hero">{{ trans('services-hero') }}</option>
                                    <option value="services-why-us">{{ trans('services-why-us') }}</option>
                                    <option value="services-professional-services">
                                        {{ trans('services-professional-services') }}</option>
                                    <option value="services-vision-mission">{{ trans('services-vision-mission') }}
                                    </option>
                                    <option value="contact-hero">{{ trans('contact-hero') }}</option>
                                    <option value="contact-info">{{ trans('contact-info') }}</option>
                                    <option value="contact-form">{{ trans('contact-form') }}</option>
                                    <option value="contact-map">{{ trans('contact-map') }}</option>
                                    <option value="blogs-hero">{{ trans('blogs-hero') }}</option>
                                    <option value="blogs-intro-text">{{ trans('blogs-intro-text') }}</option>
                                    <option value="blogs-grid">{{ trans('blogs-grid') }}</option>
                                    <option value="b2b-hero">{{ trans('b2b-hero') }}</option>
                                    <option value="why-us">{{ trans('why-us') }}</option>
                                    <option value="b2b-services">{{ trans('b2b-services') }}</option>
                                    <option value="vision-mission">{{ trans('vision-mission') }}</option>
                                    <option value="how-to-start-with-us">{{ trans('how-to-start-with-us') }}</option>
                                    <option value="register-with-us">{{ trans('register-with-us') }}</option>

                                </select>

                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <label class="" for="page_id">{{ trans('page') }}</label>
                                <select class="custom-select  form-select advance-select" name="page_id" id="page_id">

                                    <option value="">{{ trans('select') . ' ' . trans('page') }}</option>
                                    @foreach ($pages ?? [] as $sItem)
                                        <option data-id="{{ $sItem->id }}" @selected(isset($item) and $item->page_id == $sItem->id)
                                            value="{{ $sItem->id }}">{{ $sItem->title }}</option>
                                    @endforeach

                                </select>

                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <label class="" for="order">{{ trans('order') }}</label>
                                <input type="number" name="order" class="form-control"
                                    placeholder="{{ trans('Enter display order') }}"
                                    value="{{ old('order', $item->order ?? 0) }}">
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
@endpush
@push('js')
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/TableDnD/1.0.5/jquery.tablednd.js"></script>
    <script src="{{ asset('control') }}/js/custom/crud/form.js"></script>
@endpush
