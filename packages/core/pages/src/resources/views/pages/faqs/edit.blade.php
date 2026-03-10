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
                        @if (isset($item)) action="{{ route('dashboard.faqs.edit', $item->id) }}"
                            data-mode="edit"
                        @else
                            action="{{ route('dashboard.faqs.create') }}"
                            data-mode="new" @endif>

                        @csrf
                        <div class="card-body row">

                            <div class="col-12 row mt-5">
                                <ul class="nav nav-tabs" id="languageTabs" role="tablist">

                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active " id="question-en-tab" data-bs-toggle="tab"
                                            data-bs-target="#question-en" type="button" role="tab"
                                            aria-controls="question-en" aria-selected=" true">{{ trans('English') }}</button>
                                    </li>

                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link  " id="question-ar-tab" data-bs-toggle="tab"
                                            data-bs-target="#question-ar" type="button" role="tab"
                                            aria-controls="question-ar" aria-selected=" false">{{ trans('arabic') }}</button>
                                    </li>

                                </ul>
                                <div class="tab-content mt-3" id="languageTabsContent">
                                    <div class="tab-pane fade show active" id="question-en" role="tabpanel"
                                        aria-labelledby="en-tab">

                                        <div class="form-group mb-3 col-md-12">
                                            <label class="" for="question">{{ trans('question') }}</label>
                                            <input type="text" name="translations[en][question]" class="form-control "
                                                placeholder="{{ trans('Enter question') }} "
                                                value="@if (isset($item)) {{ $item?->translate('en')?->question }} @endif">

                                        </div>

                                    </div>
                                    <div class="tab-pane fade " id="question-ar" role="tabpanel" aria-labelledby="ar-tab">

                                        <div class="form-group mb-3 col-md-12">
                                            <label class="" for="question">{{ trans('question') }}</label>
                                            <input type="text" name="translations[ar][question]" class="form-control "
                                                placeholder="{{ trans('Enter question') }} "
                                                value="@if (isset($item)) {{ $item?->translate('ar')?->question }} @endif">

                                        </div>

                                    </div>
                                </div>
                                
                            </div>
                            <div class="col-12 row mt-5">
                                <ul class="nav nav-tabs" id="languageTabs" role="tablist">

                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active " id="answer-en-tab" data-bs-toggle="tab"
                                            data-bs-target="#answer-en" type="button" role="tab"
                                            aria-controls="answer-en" aria-selected=" true">{{ trans('English') }}</button>
                                    </li>

                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link  " id="answer-ar-tab" data-bs-toggle="tab"
                                            data-bs-target="#answer-ar" type="button" role="tab"
                                            aria-controls="answer-ar" aria-selected=" false">{{ trans('arabic') }}</button>
                                    </li>

                                </ul>
                                <div class="tab-content mt-3" id="languageTabsContent">
                                    <div class="tab-pane fade show active" id="answer-en" role="tabpanel"
                                        aria-labelledby="en-tab">

                                        <div class="form-group mb-3 col-md-12">
                                            <label class="" for="answer">{{ trans('answer') }}</label>
                                            <input type="text" name="translations[en][answer]" class="form-control "
                                                placeholder="{{ trans('Enter answer') }} "
                                                value="@if (isset($item)) {{ $item?->translate('en')?->answer }} @endif">

                                        </div>

                                    </div>
                                    <div class="tab-pane fade " id="answer-ar" role="tabpanel" aria-labelledby="ar-tab">

                                        <div class="form-group mb-3 col-md-12">
                                            <label class="" for="answer">{{ trans('answer') }}</label>
                                            <input type="text" name="translations[ar][answer]" class="form-control "
                                                placeholder="{{ trans('Enter answer') }} "
                                                value="@if (isset($item)) {{ $item?->translate('ar')?->answer }} @endif">

                                        </div>

                                    </div>
                                </div>
                                
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
