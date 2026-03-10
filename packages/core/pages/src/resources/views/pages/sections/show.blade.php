
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

            <div class="col-12 row mt-5">
                <ul class="nav nav-tabs" id="languageTabs" role="tablist">

                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="title-en-tab" data-bs-toggle="tab" data-bs-target="#title-en" type="button" role="tab" aria-controls="title-en" aria-selected="true">
                        {{ trans("English") }}
                    </button>
                </li>

                <li class="nav-item" role="presentation">
                    <button class="nav-link " id="title-fr-tab" data-bs-toggle="tab" data-bs-target="#title-fr" type="button" role="tab" aria-controls="title-fr" aria-selected="false">
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

                <div class="tab-pane fade " id="title-fr" role="tabpanel" aria-labelledby="fr-tab">

            <div class="form-group mb-3 col-md-12">
                <label>{{ trans("title") }}</label><p>{{ $item?->translate('ar')?->title ?? 'N/A' }}</p>
            </div>

                </div>

                </div>
            </div>

            <div class="col-12 row mt-5">
                <ul class="nav nav-tabs" id="languageTabs" role="tablist">

                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="small_title-en-tab" data-bs-toggle="tab" data-bs-target="#small_title-en" type="button" role="tab" aria-controls="small_title-en" aria-selected="true">
                        {{ trans("English") }}
                    </button>
                </li>

                <li class="nav-item" role="presentation">
                    <button class="nav-link " id="small_title-fr-tab" data-bs-toggle="tab" data-bs-target="#small_title-fr" type="button" role="tab" aria-controls="small_title-fr" aria-selected="false">
                        {{ trans("arabic") }}
                    </button>
                </li>

                </ul>
                <div class="tab-content mt-3" id="languageTabsContent">

                <div class="tab-pane fade show active" id="small_title-en" role="tabpanel" aria-labelledby="en-tab">

            <div class="form-group mb-3 col-md-12">
                <label>{{ trans("small title") }}</label><p>{{ $item?->translate('en')?->small_title ?? 'N/A' }}</p>
            </div>

                </div>

                <div class="tab-pane fade " id="small_title-fr" role="tabpanel" aria-labelledby="fr-tab">

            <div class="form-group mb-3 col-md-12">
                <label>{{ trans("small title") }}</label><p>{{ $item?->translate('ar')?->small_title ?? 'N/A' }}</p>
            </div>

                </div>

                </div>
            </div>

            <div class="col-12 row mt-5">
                <ul class="nav nav-tabs" id="languageTabs" role="tablist">

                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="description-en-tab" data-bs-toggle="tab" data-bs-target="#description-en" type="button" role="tab" aria-controls="description-en" aria-selected="true">
                        {{ trans("English") }}
                    </button>
                </li>

                <li class="nav-item" role="presentation">
                    <button class="nav-link " id="description-fr-tab" data-bs-toggle="tab" data-bs-target="#description-fr" type="button" role="tab" aria-controls="description-fr" aria-selected="false">
                        {{ trans("arabic") }}
                    </button>
                </li>

                </ul>
                <div class="tab-content mt-3" id="languageTabsContent">

                <div class="tab-pane fade show active" id="description-en" role="tabpanel" aria-labelledby="en-tab">

            <div class="form-group mb-3 col-md-12">
                <label>{{ trans("description") }}</label><p>{{ $item?->translate('en')?->description ?? 'N/A' }}</p>
            </div>

                </div>

                <div class="tab-pane fade " id="description-fr" role="tabpanel" aria-labelledby="fr-tab">

            <div class="form-group mb-3 col-md-12">
                <label>{{ trans("description") }}</label><p>{{ $item?->translate('ar')?->description ?? 'N/A' }}</p>
            </div>

                </div>

                </div>
            </div>

            <div class="form-group mb-3 col-md-12">
                <label>{{ trans("images") }}</label><div class="input-gallery "><div class="img-row " >{!!  Core\MediaCenter\Helpers\MediaCenterHelper::getImagesHtml($item->images) !!}</div></div>
            </div>

            <div class="form-group mb-3 col-md-12">
                <label>{{ trans("video") }}</label><p>{{ $item->video ?? 'N/A' }}</p>
            </div>

            <div class="form-group mb-3 col-md-12">
                <label>{{ trans("template") }}</label><div class="alert alert-warning m-1" role="alert">{{ $item->template ?? 'N/A' }}</div>
            </div>

            <div class="form-group mb-3 col-md-12">
                <label>{{ trans("page") }}</label>
                @isset($item->page)
                    <div class="alert alert-primary m-1" role="alert"><a href="{{ route('dashboard.pages.show',$item->page->id) }}">{{ $item?->page?->title ?? 'N/A' }}</a></div>
                @endisset

            </div>

            <div class="form-group mb-3 col-md-12">
                <label>{{ trans("order") }}</label><p>{{ $item->order ?? 0 }}</p>
            </div>

                </div>
                <div class="card-footer">
                    <a href="{{ route('dashboard.sections.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> {{ trans('Back') }}</a>
                </div>
            </div>



                    @include('comment::inc.comment-section', [
                        'commentUrl' => route('dashboard.sections.comment', $item->id),
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
