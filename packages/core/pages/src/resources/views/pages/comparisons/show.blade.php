
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

            <!-- Point -->
            <div class="col-12 row mt-5">
                <ul class="nav nav-tabs" id="languageTabs" role="tablist">

                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="point-en-tab" data-bs-toggle="tab" data-bs-target="#point-en" type="button" role="tab" aria-controls="point-en" aria-selected="true">
                        {{ trans("English") }}
                    </button>
                </li>

                <li class="nav-item" role="presentation">
                    <button class="nav-link " id="point-ar-tab" data-bs-toggle="tab" data-bs-target="#point-ar" type="button" role="tab" aria-controls="point-ar" aria-selected="false">
                        {{ trans("arabic") }}
                    </button>
                </li>

                </ul>
                <div class="tab-content mt-3" id="languageTabsContent">

                <div class="tab-pane fade show active" id="point-en" role="tabpanel" aria-labelledby="en-tab">

            <div class="form-group mb-3 col-md-12">
                <label>{{ trans("comparison point") }}</label><p>{{ $item?->translate('en')?->point ?? 'N/A' }}</p>
            </div>

                </div>

                <div class="tab-pane fade " id="point-ar" role="tabpanel" aria-labelledby="ar-tab">

            <div class="form-group mb-3 col-md-12">
                <label>{{ trans("comparison point") }}</label><p>{{ $item?->translate('ar')?->point ?? 'N/A' }}</p>
            </div>

                </div>

                </div>
            </div>

            <!-- Us Text -->
            <div class="col-12 row mt-5">
                <ul class="nav nav-tabs" id="languageTabs2" role="tablist">

                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="us-en-tab" data-bs-toggle="tab" data-bs-target="#us-en" type="button" role="tab" aria-controls="us-en" aria-selected="true">
                        {{ trans("English") }}
                    </button>
                </li>

                <li class="nav-item" role="presentation">
                    <button class="nav-link " id="us-ar-tab" data-bs-toggle="tab" data-bs-target="#us-ar" type="button" role="tab" aria-controls="us-ar" aria-selected="false">
                        {{ trans("arabic") }}
                    </button>
                </li>

                </ul>
                <div class="tab-content mt-3" id="languageTabsContent2">

                <div class="tab-pane fade show active" id="us-en" role="tabpanel" aria-labelledby="en-tab">

            <div class="form-group mb-3 col-md-12">
                <label>{{ trans("our advantage") }}</label><p>{{ $item?->translate('en')?->us_text ?? 'N/A' }}</p>
            </div>

                </div>

                <div class="tab-pane fade " id="us-ar" role="tabpanel" aria-labelledby="ar-tab">

            <div class="form-group mb-3 col-md-12">
                <label>{{ trans("our advantage") }}</label><p>{{ $item?->translate('ar')?->us_text ?? 'N/A' }}</p>
            </div>

                </div>

                </div>
            </div>

            <!-- Them Text -->
            <div class="col-12 row mt-5">
                <ul class="nav nav-tabs" id="languageTabs3" role="tablist">

                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="them-en-tab" data-bs-toggle="tab" data-bs-target="#them-en" type="button" role="tab" aria-controls="them-en" aria-selected="true">
                        {{ trans("English") }}
                    </button>
                </li>

                <li class="nav-item" role="presentation">
                    <button class="nav-link " id="them-ar-tab" data-bs-toggle="tab" data-bs-target="#them-ar" type="button" role="tab" aria-controls="them-ar" aria-selected="false">
                        {{ trans("arabic") }}
                    </button>
                </li>

                </ul>
                <div class="tab-content mt-3" id="languageTabsContent3">

                <div class="tab-pane fade show active" id="them-en" role="tabpanel" aria-labelledby="en-tab">

            <div class="form-group mb-3 col-md-12">
                <label>{{ trans("their disadvantage") }}</label><p>{{ $item?->translate('en')?->them_text ?? 'N/A' }}</p>
            </div>

                </div>

                <div class="tab-pane fade " id="them-ar" role="tabpanel" aria-labelledby="ar-tab">

            <div class="form-group mb-3 col-md-12">
                <label>{{ trans("their disadvantage") }}</label><p>{{ $item?->translate('ar')?->them_text ?? 'N/A' }}</p>
            </div>

                </div>

                </div>
            </div>

            <div class="form-group mb-3 col-md-6">
                <label>{{ trans("order") }}</label>
                <p>{{ $item->order ?? 0 }}</p>
            </div>

            <div class="form-group mb-3 col-md-6">
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
                    <a href="{{ route('dashboard.comparisons.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> {{ trans('Back') }}</a>
                </div>
            </div>



                    @include('comment::inc.comment-section', [
                        'commentUrl' => route('dashboard.comparisons.comment', $item->id),
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


