
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
                            <a href=""
                                class="text-muted text-hover-primary">@lang('Home')</a>
                        </li>
                        <!--end::Item-->

                        <!--begin::Item-->
                        <li class="breadcrumb-item text-muted">@lang("pages")</li>
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

            <!-- Title -->
            <div class="col-12 row mt-5">
                <ul class="nav nav-tabs" id="languageTabs" role="tablist">

                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="title-en-tab" data-bs-toggle="tab" data-bs-target="#title-en" type="button" role="tab" aria-controls="title-en" aria-selected="true">
                        {{ trans("English") }}
                    </button>
                </li>

                <li class="nav-item" role="presentation">
                    <button class="nav-link " id="title-ar-tab" data-bs-toggle="tab" data-bs-target="#title-ar" type="button" role="tab" aria-controls="title-ar" aria-selected="false">
                        {{ trans("arabic") }}
                    </button>
                </li>

                </ul>
                <div class="tab-content mt-3" id="languageTabsContent">

                <div class="tab-pane fade show active" id="title-en" role="tabpanel" aria-labelledby="en-tab">

            <div class="form-group mb-3 col-md-12">
                <label>{{ trans("title") }}</label><p>{{ $item?->translate('en')?->title ?? 'N/A' }}</p>
            </div>

                </div>

                <div class="tab-pane fade " id="title-ar" role="tabpanel" aria-labelledby="ar-tab">

            <div class="form-group mb-3 col-md-12">
                <label>{{ trans("title") }}</label><p>{{ $item?->translate('ar')?->title ?? 'N/A' }}</p>
            </div>

                </div>

                </div>
            </div>

            <!-- Description -->
            <div class="col-12 row mt-5">
                <ul class="nav nav-tabs" id="languageTabs2" role="tablist">

                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="description-en-tab" data-bs-toggle="tab" data-bs-target="#description-en" type="button" role="tab" aria-controls="description-en" aria-selected="true">
                        {{ trans("English") }}
                    </button>
                </li>

                <li class="nav-item" role="presentation">
                    <button class="nav-link " id="description-ar-tab" data-bs-toggle="tab" data-bs-target="#description-ar" type="button" role="tab" aria-controls="description-ar" aria-selected="false">
                        {{ trans("arabic") }}
                    </button>
                </li>

                </ul>
                <div class="tab-content mt-3" id="languageTabsContent2">

                <div class="tab-pane fade show active" id="description-en" role="tabpanel" aria-labelledby="en-tab">

            <div class="form-group mb-3 col-md-12">
                <label>{{ trans("description") }}</label><p>{{ $item?->translate('en')?->description ?? 'N/A' }}</p>
            </div>

                </div>

                <div class="tab-pane fade " id="description-ar" role="tabpanel" aria-labelledby="ar-tab">

            <div class="form-group mb-3 col-md-12">
                <label>{{ trans("description") }}</label><p>{{ $item?->translate('ar')?->description ?? 'N/A' }}</p>
            </div>

                </div>

                </div>
            </div>

            <div class="form-group mb-3 col-md-4">
                <label>{{ trans("icon") }}</label>
                <p>
                    @if($item->icon)
                        <i class="{{ $item->icon }} fa-2x"></i> 
                        <span class="ms-2">{{ $item->icon }}</span>
                    @else
                        N/A
                    @endif
                </p>
            </div>

            <div class="form-group mb-3 col-md-4">
                <label>{{ trans("order") }}</label>
                <p>{{ $item->order ?? 0 }}</p>
            </div>

            <div class="form-group mb-3 col-md-4">
                <label>{{ trans("is active") }}</label>
                <p>
                    @if($item->is_active)
                        <span class="badge bg-success">@lang('active')</span>
                    @else
                        <span class="badge bg-danger">@lang('inactive')</span>
                    @endif
                </p>
            </div>

                </div>
                <div class="card-footer">
                    <a href="{{ route('dashboard.work-steps.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> {{ trans('Back') }}</a>
                </div>
            </div>



                    @include('comment::inc.comment-section', [
                        'commentUrl' => route('dashboard.work-steps.comment', $item->id),
                    ])
                </div>
                <!--end::Card-->
            </div>
            <!--end::Container-->
        </div>
        <!--end::Post-->
    </div>
    <!--end::Content-->
@endsection
@push('css')
    <link href="{{ asset('control') }}/js/custom/crud/show.css" rel="stylesheet" type="text/css" />
@endpush
@push('js')
@endpush


