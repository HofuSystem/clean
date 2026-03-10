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
                        <li class="breadcrumb-item text-muted">@lang('cms')</li>
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
                            @if ($pageData->have_name)
                            <div class="col-12 mt-5">
                                <ul class="nav nav-tabs" id="languageTabs" role="tablist">

                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="name-en-tab" data-bs-toggle="tab"
                                            data-bs-target="#name-en" type="button" role="tab" aria-controls="name-en"
                                            aria-selected="true">
                                            {{ trans('English') }}
                                        </button>
                                    </li>

                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link " id="name-ar-tab" data-bs-toggle="tab"
                                            data-bs-target="#name-ar" type="button" role="tab" aria-controls="name-ar"
                                            aria-selected="false">
                                            {{ trans('العربية') }}
                                        </button>
                                    </li>

                                </ul>
                                <div class="tab-content mt-3" id="languageTabsContent">

                                    <div class="tab-pane fade show active" id="name-en" role="tabpanel"
                                        aria-labelledby="en-tab">

                                        <div class="form-group mb-3 col-md-12">
                                            <label>{{ trans('name') }}</label>
                                            <p>{{ $item?->translate('en')?->name ?? 'N/A' }}</p>
                                        </div>

                                    </div>

                                    <div class="tab-pane fade " id="name-ar" role="tabpanel" aria-labelledby="ar-tab">

                                        <div class="form-group mb-3 col-md-12">
                                            <label>{{ trans('name') }}</label>
                                            <p>{{ $item?->translate('ar')?->name ?? 'N/A' }}</p>
                                        </div>

                                    </div>

                                </div>
                            </div>
                            @endif
                            @if ($pageData->have_description)
                            <div class="col-12 mt-5">
                                <ul class="nav nav-tabs" id="languageTabs" role="tablist">

                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="description-en-tab" data-bs-toggle="tab"
                                            data-bs-target="#description-en" type="button" role="tab"
                                            aria-controls="description-en" aria-selected="true">
                                            {{ trans('English') }}
                                        </button>
                                    </li>

                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link " id="description-ar-tab" data-bs-toggle="tab"
                                            data-bs-target="#description-ar" type="button" role="tab"
                                            aria-controls="description-ar" aria-selected="false">
                                            {{ trans('العربية') }}
                                        </button>
                                    </li>

                                </ul>
                                <div class="tab-content mt-3" id="languageTabsContent">

                                    <div class="tab-pane fade show active" id="description-en" role="tabpanel"
                                        aria-labelledby="en-tab">

                                        <div class="form-group mb-3 col-md-12">
                                            <label>{{ trans('description') }}</label>
                                            <p>{{ $item?->translate('en')?->description ?? 'N/A' }}</p>
                                        </div>

                                    </div>

                                    <div class="tab-pane fade " id="description-ar" role="tabpanel"
                                        aria-labelledby="ar-tab">

                                        <div class="form-group mb-3 col-md-12">
                                            <label>{{ trans('description') }}</label>
                                            <p>{{ $item?->translate('ar')?->description ?? 'N/A' }}</p>
                                        </div>

                                    </div>

                                </div>
                            </div>
                            @endif
                            @if ($pageData->have_intro)
                            <div class="col-12 mt-5">
                                <ul class="nav nav-tabs" id="languageTabs" role="tablist">

                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="intro-en-tab" data-bs-toggle="tab"
                                            data-bs-target="#intro-en" type="button" role="tab"
                                            aria-controls="intro-en" aria-selected="true">
                                            {{ trans('English') }}
                                        </button>
                                    </li>

                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link " id="intro-ar-tab" data-bs-toggle="tab"
                                            data-bs-target="#intro-ar" type="button" role="tab"
                                            aria-controls="intro-ar" aria-selected="false">
                                            {{ trans('العربية') }}
                                        </button>
                                    </li>

                                </ul>
                                <div class="tab-content mt-3" id="languageTabsContent">

                                    <div class="tab-pane fade show active" id="intro-en" role="tabpanel"
                                        aria-labelledby="en-tab">

                                        <div class="form-group mb-3 col-md-12">
                                            <label>{{ trans('intro') }}</label>
                                            <p>{{ $item?->translate('en')?->intro ?? 'N/A' }}</p>
                                        </div>

                                    </div>

                                    <div class="tab-pane fade " id="intro-ar" role="tabpanel" aria-labelledby="ar-tab">

                                        <div class="form-group mb-3 col-md-12">
                                            <label>{{ trans('intro') }}</label>
                                            <p>{{ $item?->translate('ar')?->intro ?? 'N/A' }}</p>
                                        </div>

                                    </div>

                                </div>
                            </div>
                            @endif
                            @if ($pageData->have_point)
                            <div class="col-12 mt-5">
                                <ul class="nav nav-tabs" id="languageTabs" role="tablist">

                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="point-en-tab" data-bs-toggle="tab"
                                            data-bs-target="#point-en" type="button" role="tab"
                                            aria-controls="point-en" aria-selected="true">
                                            {{ trans('English') }}
                                        </button>
                                    </li>

                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link " id="point-ar-tab" data-bs-toggle="tab"
                                            data-bs-target="#point-ar" type="button" role="tab"
                                            aria-controls="point-ar" aria-selected="false">
                                            {{ trans('العربية') }}
                                        </button>
                                    </li>

                                </ul>
                                <div class="tab-content mt-3" id="languageTabsContent">

                                    <div class="tab-pane fade show active" id="point-en" role="tabpanel"
                                        aria-labelledby="en-tab">

                                        <div class="form-group mb-3 col-md-12">
                                            <label>{{ trans('point') }}</label>
                                            <p>{{ $item?->translate('en')?->point ?? 'N/A' }}</p>
                                        </div>

                                    </div>

                                    <div class="tab-pane fade " id="point-ar" role="tabpanel" aria-labelledby="ar-tab">

                                        <div class="form-group mb-3 col-md-12">
                                            <label>{{ trans('point') }}</label>
                                            <p>{{ $item?->translate('ar')?->point ?? 'N/A' }}</p>
                                        </div>

                                    </div>

                                </div>
                            </div>
                            @endif
                            @if ($pageData->have_image)
                            <div class="form-group mb-3 col-md-12">
                                <label>{{ trans('image') }}</label>
                                <div class="gallary-images">{!! Core\MediaCenter\Helpers\MediaCenterHelper::getImagesHtml($item->image) !!}</div>
                            </div>
                            @endif
                            @if ($pageData->have_tablet_image)
                            <div class="form-group mb-3 col-md-12">
                                <label>{{ trans('tablet image') }}</label>
                                <div class="gallary-images">{!! Core\MediaCenter\Helpers\MediaCenterHelper::getImagesHtml($item->tablet_image) !!}</div>
                            </div>
                            @endif
                            @if ($pageData->have_mobile_image)
                            <div class="form-group mb-3 col-md-12">
                                <label>{{ trans('mobile image') }}</label>
                                <div class="gallary-images">{!! Core\MediaCenter\Helpers\MediaCenterHelper::getImagesHtml($item->mobile_image) !!}</div>
                            </div>
                            @endif
                            @if ($pageData->have_icon)
                            <div class="form-group mb-3 col-md-12">
                                <label>{{ trans('icon') }}</label>
                                <div class="gallary-images">{!! Core\MediaCenter\Helpers\MediaCenterHelper::getImagesHtml($item->icon) !!}</div>
                            </div>
                            @endif
                            @if ($pageData->have_videon)
                            <div class="form-group mb-3 col-md-12">
                                <label>{{ trans('video') }}</label>
                                <p>{{ $item->video ?? 'N/A' }}</p>
                            </div>
                            @endif
                            @if ($pageData->have_link)
                            <div class="form-group mb-3 col-md-12">
                                <label>{{ trans('link') }}</label>
                                <p>{{ $item->link ?? 'N/A' }}</p>
                            </div>
                            @endif
                        </div>
                        <div class="card-footer">
                            <a href="{{ route('dashboard.cms-page-details.index', $slug) }}" class="btn btn-secondary"><i
                                    class="fas fa-arrow-left"></i> {{ trans('Back') }}</a>
                        </div>
                    </div>


                    @include('comment::inc.comment-section', [
                        'commentUrl' => route('dashboard.cms-page-details.comment', [
                            'slug' => $slug,
                            'id' => $item->id,
                        ]),
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
