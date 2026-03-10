
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

            <!-- Name -->
            <div class="col-12 row mt-5">
                <ul class="nav nav-tabs" id="languageTabs" role="tablist">

                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="name-en-tab" data-bs-toggle="tab" data-bs-target="#name-en" type="button" role="tab" aria-controls="name-en" aria-selected="true">
                        {{ trans("English") }}
                    </button>
                </li>

                <li class="nav-item" role="presentation">
                    <button class="nav-link " id="name-ar-tab" data-bs-toggle="tab" data-bs-target="#name-ar" type="button" role="tab" aria-controls="name-ar" aria-selected="false">
                        {{ trans("arabic") }}
                    </button>
                </li>

                </ul>
                <div class="tab-content mt-3" id="languageTabsContent">

                <div class="tab-pane fade show active" id="name-en" role="tabpanel" aria-labelledby="en-tab">

            <div class="form-group mb-3 col-md-12">
                <label>{{ trans("name") }}</label><p>{{ $item?->translate('en')?->name ?? 'N/A' }}</p>
            </div>

                </div>

                <div class="tab-pane fade " id="name-ar" role="tabpanel" aria-labelledby="ar-tab">

            <div class="form-group mb-3 col-md-12">
                <label>{{ trans("name") }}</label><p>{{ $item?->translate('ar')?->name ?? 'N/A' }}</p>
            </div>

                </div>

                </div>
            </div>

            <!-- Title/Position -->
            <div class="col-12 row mt-5">
                <ul class="nav nav-tabs" id="languageTabs2" role="tablist">

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
                <div class="tab-content mt-3" id="languageTabsContent2">

                <div class="tab-pane fade show active" id="title-en" role="tabpanel" aria-labelledby="en-tab">

            <div class="form-group mb-3 col-md-12">
                <label>{{ trans("title") }} / {{ trans("position") }}</label><p>{{ $item?->translate('en')?->title ?? 'N/A' }}</p>
            </div>

                </div>

                <div class="tab-pane fade " id="title-ar" role="tabpanel" aria-labelledby="ar-tab">

            <div class="form-group mb-3 col-md-12">
                <label>{{ trans("title") }} / {{ trans("position") }}</label><p>{{ $item?->translate('ar')?->title ?? 'N/A' }}</p>
            </div>

                </div>

                </div>
            </div>

            <!-- Body/Content -->
            <div class="col-12 row mt-5">
                <ul class="nav nav-tabs" id="languageTabs3" role="tablist">

                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="body-en-tab" data-bs-toggle="tab" data-bs-target="#body-en" type="button" role="tab" aria-controls="body-en" aria-selected="true">
                        {{ trans("English") }}
                    </button>
                </li>

                <li class="nav-item" role="presentation">
                    <button class="nav-link " id="body-ar-tab" data-bs-toggle="tab" data-bs-target="#body-ar" type="button" role="tab" aria-controls="body-ar" aria-selected="false">
                        {{ trans("arabic") }}
                    </button>
                </li>

                </ul>
                <div class="tab-content mt-3" id="languageTabsContent3">

                <div class="tab-pane fade show active" id="body-en" role="tabpanel" aria-labelledby="en-tab">

            <div class="form-group mb-3 col-md-12">
                <label>{{ trans("body") }} / {{ trans("content") }}</label><p>{{ $item?->translate('en')?->body ?? 'N/A' }}</p>
            </div>

                </div>

                <div class="tab-pane fade " id="body-ar" role="tabpanel" aria-labelledby="ar-tab">

            <div class="form-group mb-3 col-md-12">
                <label>{{ trans("body") }} / {{ trans("content") }}</label><p>{{ $item?->translate('ar')?->body ?? 'N/A' }}</p>
            </div>

                </div>

                </div>
            </div>

            <!-- Location -->
            <div class="col-12 row mt-5">
                <ul class="nav nav-tabs" id="languageTabs4" role="tablist">

                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="location-en-tab" data-bs-toggle="tab" data-bs-target="#location-en" type="button" role="tab" aria-controls="location-en" aria-selected="true">
                        {{ trans("English") }}
                    </button>
                </li>

                <li class="nav-item" role="presentation">
                    <button class="nav-link " id="location-ar-tab" data-bs-toggle="tab" data-bs-target="#location-ar" type="button" role="tab" aria-controls="location-ar" aria-selected="false">
                        {{ trans("arabic") }}
                    </button>
                </li>

                </ul>
                <div class="tab-content mt-3" id="languageTabsContent4">

                <div class="tab-pane fade show active" id="location-en" role="tabpanel" aria-labelledby="en-tab">

            <div class="form-group mb-3 col-md-12">
                <label>{{ trans("location") }}</label><p>{{ $item?->translate('en')?->location ?? 'N/A' }}</p>
            </div>

                </div>

                <div class="tab-pane fade " id="location-ar" role="tabpanel" aria-labelledby="ar-tab">

            <div class="form-group mb-3 col-md-12">
                <label>{{ trans("location") }}</label><p>{{ $item?->translate('ar')?->location ?? 'N/A' }}</p>
            </div>

                </div>

                </div>
            </div>

            <div class="form-group mb-3 col-md-6">
                <label>{{ trans("avatar") }}</label>
                @if($item->image_url)
                    <div><img src="{{ $item->image_url }}" alt="{{ $item->name }}" style="max-width: 150px; max-height: 150px; border-radius: 50%;"></div>
                @else
                    <p>N/A</p>
                @endif
            </div>

            <div class="form-group mb-3 col-md-3">
                <label>{{ trans("rating") }}</label>
                <p>
                    @for($i = 0; $i < $item->rating; $i++)
                        <i class="fas fa-star text-warning"></i>
                    @endfor
                    @for($i = $item->rating; $i < 5; $i++)
                        <i class="fas fa-star text-muted"></i>
                    @endfor
                </p>
            </div>

            <div class="form-group mb-3 col-md-3">
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
                    <a href="{{ route('dashboard.testimonials.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> {{ trans('Back') }}</a>
                </div>
            </div>



                    @include('comment::inc.comment-section', [
                        'commentUrl' => route('dashboard.testimonials.comment', $item->id),
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

