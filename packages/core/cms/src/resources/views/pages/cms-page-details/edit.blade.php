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
                <div class="card">

                    <form class="form" method="POST" id="operation-form"  redirect-to="{{route("dashboard.cms-page-details.index",['slug' => $slug])}}" data-id="{{ $item->id ?? null }}"
                        @isset($item)
                            action="{{ route('dashboard.cms-page-details.edit', ['slug' => $slug, 'id' => $item->id]) }}"
                            data-mode="edit"
                        @else
                            action="{{ route('dashboard.cms-page-details.create', ['slug' => $slug]) }}"
                            data-mode="new"
                        @endisset>

                        @csrf
                        <div class="card-body row">
                            @if ($pageData->have_name)
                            <div class="col-12 mt-5">
                                <ul class="nav nav-tabs" id="languageTabs" role="tablist">

                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active " id="name-en-tab" data-bs-toggle="tab"
                                            data-bs-target="#name-en" type="button" role="tab" aria-controls="name-en"
                                            aria-selected=" true">{{ trans('English') }}</button>
                                    </li>

                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link  " id="name-ar-tab" data-bs-toggle="tab"
                                            data-bs-target="#name-ar" type="button" role="tab" aria-controls="name-ar"
                                            aria-selected=" false">{{ trans('العربية') }}</button>
                                    </li>

                                </ul>
                                <div class="tab-content mt-3" id="languageTabsContent">
                                    <div class="tab-pane fade show active" id="name-en" role="tabpanel"
                                        aria-labelledby="en-tab">

                                        <div class="form-group mb-3 col-md-12">
                                            <label class="" for="name">{{ trans('name') }}</label>
                                            <input type="text" name="translations[en][name]" class="form-control "
                                                placeholder="{{ trans('Enter name') }} "
                                                value="@isset($item) {{ $item?->translate('en')?->name }} @endisset">

                                        </div>

                                    </div>
                                    <div class="tab-pane fade " id="name-ar" role="tabpanel" aria-labelledby="ar-tab">

                                        <div class="form-group mb-3 col-md-12">
                                            <label class="" for="name">{{ trans('name') }}</label>
                                            <input type="text" name="translations[ar][name]" class="form-control "
                                                placeholder="{{ trans('Enter name') }} "
                                                value="@isset($item) {{ $item?->translate('ar')?->name }} @endisset">

                                        </div>

                                    </div>
                                </div>
                            </div>
                            @endif
                            @if ($pageData->have_description)
                            <div class="col-12 mt-5">
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
                                            aria-selected=" false">{{ trans('العربية') }}</button>
                                    </li>

                                </ul>
                                <div class="tab-content mt-3" id="languageTabsContent">
                                    <div class="tab-pane fade show active" id="description-en" role="tabpanel"
                                        aria-labelledby="en-tab">

                                        <div class="form-group mb-3 col-md-12">
                                            <label class="" for="description">{{ trans('description') }}</label>
                                            <textarea type="number" name="translations[en][description]" class="form-control "
                                                placeholder="{{ trans('Enter description') }} ">@isset($item){{ $item?->translate('en')?->description }}@endisset</textarea>

                                        </div>

                                    </div>
                                    <div class="tab-pane fade " id="description-ar" role="tabpanel"
                                        aria-labelledby="ar-tab">

                                        <div class="form-group mb-3 col-md-12">
                                            <label class="" for="description">{{ trans('description') }}</label>
                                            <textarea type="number" name="translations[ar][description]" class="form-control "
                                                placeholder="{{ trans('Enter description') }} ">@isset($item){{ $item?->translate('ar')?->description }}@endisset</textarea>

                                        </div>

                                    </div>
                                </div>
                            </div>
                            @endif
                            @if ($pageData->have_intro)
                            <div class="col-12 mt-5">
                                <ul class="nav nav-tabs" id="languageTabs" role="tablist">

                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active " id="intro-en-tab" data-bs-toggle="tab"
                                            data-bs-target="#intro-en" type="button" role="tab"
                                            aria-controls="intro-en"
                                            aria-selected=" true">{{ trans('English') }}</button>
                                    </li>

                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link  " id="intro-ar-tab" data-bs-toggle="tab"
                                            data-bs-target="#intro-ar" type="button" role="tab"
                                            aria-controls="intro-ar"
                                            aria-selected=" false">{{ trans('العربية') }}</button>
                                    </li>

                                </ul>
                                <div class="tab-content mt-3" id="languageTabsContent">
                                    <div class="tab-pane fade show active" id="intro-en" role="tabpanel"
                                        aria-labelledby="en-tab">

                                        <div class="form-group mb-3 col-md-12">
                                            <label class="" for="intro">{{ trans('intro') }}</label>
                                            <textarea type="number" name="translations[en][intro]" class="form-control "
                                                placeholder="{{ trans('Enter intro') }} ">
                                            @isset($item)
                                            {{ $item?->translate('en')?->intro }}
                                            @endisset
                                            </textarea>

                                        </div>

                                    </div>
                                    <div class="tab-pane fade " id="intro-ar" role="tabpanel" aria-labelledby="ar-tab">

                                        <div class="form-group mb-3 col-md-12">
                                            <label class="" for="intro">{{ trans('intro') }}</label>
                                            <textarea type="number" name="translations[ar][intro]" class="form-control "
                                                placeholder="{{ trans('Enter intro') }} ">
                                            @isset($item)
                                            {{ $item?->translate('ar')?->intro }}
                                            @endisset
                                            </textarea>

                                        </div>

                                    </div>
                                </div>
                            </div>
                            @endif
                            @if ($pageData->have_point)
                            <div class="col-12 mt-5">
                                <ul class="nav nav-tabs" id="languageTabs" role="tablist">

                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active " id="point-en-tab" data-bs-toggle="tab"
                                            data-bs-target="#point-en" type="button" role="tab"
                                            aria-controls="point-en"
                                            aria-selected=" true">{{ trans('English') }}</button>
                                    </li>

                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link  " id="point-ar-tab" data-bs-toggle="tab"
                                            data-bs-target="#point-ar" type="button" role="tab"
                                            aria-controls="point-ar"
                                            aria-selected=" false">{{ trans('العربية') }}</button>
                                    </li>

                                </ul>
                                <div class="tab-content mt-3" id="languageTabsContent">
                                    <div class="tab-pane fade show active" id="point-en" role="tabpanel"
                                        aria-labelledby="en-tab">

                                        <div class="form-group mb-3 col-md-12">
                                            <label class="" for="point">{{ trans('point') }}</label>
                                            <textarea type="number" name="translations[en][point]" class="form-control "
                                                placeholder="{{ trans('Enter point') }} ">
                                            @isset($item)
                                            {{ $item?->translate('en')?->point }}
                                            @endisset
                                            </textarea>

                                        </div>

                                    </div>
                                    <div class="tab-pane fade " id="point-ar" role="tabpanel" aria-labelledby="ar-tab">

                                        <div class="form-group mb-3 col-md-12">
                                            <label class="" for="point">{{ trans('point') }}</label>
                                            <textarea type="number" name="translations[ar][point]" class="form-control "
                                                placeholder="{{ trans('Enter point') }} ">
                                            @isset($item)
                                            {{ $item?->translate('ar')?->point }}
                                            @endisset
                                            </textarea>

                                        </div>

                                    </div>
                                </div>
                            </div>
                            @endif
                            @if ($pageData->have_image)
                            <div class="form-group mb-3 col-md-12">
                                <label class="" for="image">{{ trans('image') }}</label>
                                <div class="media-center-group form-control" data-max="1" data-type="image">
                                    <input type="text" hidden="hidden" class="form-control" name="image"
                                        value="{{ old('image', $item->image ?? null) }}">
                                    <button type="button" class="btn btn-secondary media-center-load"
                                        style="margin-top: 10px;"><i class="fa fa-file-upload"></i></button>
                                    <div class="input-gallery"></div>
                                </div>
                            </div>
                            @endif
                            @if ($pageData->have_tablet_image)
                            <div class="form-group mb-3 col-md-12">
                                <label class="" for="tablet_image">{{ trans('tablet image') }}</label>
                                <div class="media-center-group form-control" data-max="1" data-type="image">
                                    <input type="text" hidden="hidden" class="form-control" name="tablet_image"
                                        value="{{ old('tablet_image', $item->tablet_image ?? null) }}">
                                    <button type="button" class="btn btn-secondary media-center-load"
                                        style="margin-top: 10px;"><i class="fa fa-file-upload"></i></button>
                                    <div class="input-gallery"></div>
                                </div>
                            </div>
                            @endif
                            @if ($pageData->have_mobile_image)
                            <div class="form-group mb-3 col-md-12">
                                <label class="" for="mobile_image">{{ trans('mobile image') }}</label>
                                <div class="media-center-group form-control" data-max="1" data-type="image">
                                    <input type="text" hidden="hidden" class="form-control" name="mobile_image"
                                        value="{{ old('mobile_image', $item->mobile_image ?? null) }}">
                                    <button type="button" class="btn btn-secondary media-center-load"
                                        style="margin-top: 10px;"><i class="fa fa-file-upload"></i></button>
                                    <div class="input-gallery"></div>
                                </div>
                            </div>
                            @endif
                            @if ($pageData->have_icon)
                            <div class="form-group mb-3 col-md-12">
                                <label class="" for="icon">{{ trans('icon') }}</label>
                                <div class="media-center-group form-control" data-max="1" data-type="image">
                                    <input type="text" hidden="hidden" class="form-control" name="icon"
                                        value="{{ old('icon', $item->icon ?? null) }}">
                                    <button type="button" class="btn btn-secondary media-center-load"
                                        style="margin-top: 10px;"><i class="fa fa-file-upload"></i></button>
                                    <div class="input-gallery"></div>
                                </div>
                            </div>
                            @endif
                            @if ($pageData->have_video)
                            <div class="form-group mb-3 col-md-12">
                                <label class="" for="video">{{ trans('video') }}</label>
                                <input type="text" name="video" class="form-control "
                                    placeholder="{{ trans('Enter video') }} "
                                    value="@isset($item){{ $item->video }}@endisset">
                            </div>
                            @endif
                            @if ($pageData->have_link)
                            <div class="form-group mb-3 col-md-12">
                                <label class="" for="link">{{ trans('link') }}</label>
                                <input type="text" name="link" class="form-control "
                                    placeholder="{{ trans('Enter link') }} "
                                    value="@isset($item){{ $item->link }}@endisset">
                            </div>
                            @endif
                            <div class="form-group mb-3 col-md-12">
                                <label class="required" for="cms_pages_id">{{ trans('cms pages') }}</label>
                                <select class="custom-select  form-select advance-select" name="cms_pages_id"
                                    id="cms_pages_id">
                                    <option value="">{{ trans('select cms pages') }}</option>
                                    @foreach ($cmsPages ?? [] as $sItem)
                                        <option data-id="{{ $sItem->id }}" @selected(isset($item) and $item->cms_pages_id == $sItem->id)
                                            value="{{ $sItem->id }}">{{ $sItem->name }}</option>
                                    @endforeach
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
