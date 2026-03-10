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
                            <a href="" class="text-muted text-hover-primary">@lang('Home')</a>
                        </li>
                        <!--end::Item-->

                        <!--begin::Item-->
                        <li class="breadcrumb-item text-muted">@lang('blog')</li>
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

                    <form class="form" method="POST" id="operation-form"
                        redirect-to="{{ route('dashboard.blogs.index') }}" data-id="{{ $item->id ?? null }}"
                        @isset($item)
                            action="{{ route('dashboard.blogs.edit', $item->id) }}"
                            data-mode="edit"
                        @else
                            action="{{ route('dashboard.blogs.create') }}"
                            data-mode="new"
                        @endisset>

                        @csrf
                        <div class="card-body row">

                            <div class="form-group mb-3 col-md-12">
                                <label class="" for="delivery_price">{{ trans('slug') }}</label>
                                <input type="text" name="slug" class="form-control "
                                    placeholder="{{ trans('Enter slug') }} "
                                    value="{{ old('slug', $item->slug ?? null) }}">

                            </div>

                            <div class="col-12 mt-5">
                                <ul class="nav nav-tabs" id="languageTabs" role="tablist">

                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active " id="title-en-tab" data-bs-toggle="tab"
                                            data-bs-target="#title-en" type="button" role="tab"
                                            aria-controls="title-en" aria-selected=" true">{{ trans('English') }}</button>
                                    </li>

                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link  " id="title-ar-tab" data-bs-toggle="tab"
                                            data-bs-target="#title-ar" type="button" role="tab"
                                            aria-controls="title-ar" aria-selected=" false">{{ trans('العربية') }}</button>
                                    </li>

                                </ul>
                                <div class="tab-content mt-3" id="languageTabsContent">
                                    <div class="tab-pane fade show active" id="title-en" role="tabpanel"
                                        aria-labelledby="en-tab">

                                        <div class="form-group mb-3 col-md-12">
                                            <label class="required" for="title">{{ trans('title') }}</label>
                                            <input type="text" name="translations[en][title]" class="form-control "
                                                placeholder="{{ trans('Enter title') }} "
                                                value="@isset($item) {{ $item?->translate('en')?->title }} @endisset">

                                        </div>

                                    </div>
                                    <div class="tab-pane fade " id="title-ar" role="tabpanel" aria-labelledby="ar-tab">

                                        <div class="form-group mb-3 col-md-12">
                                            <label class="required" for="title">{{ trans('title') }}</label>
                                            <input type="text" name="translations[ar][title]" class="form-control "
                                                placeholder="{{ trans('Enter title') }} "
                                                value="@isset($item) {{ $item?->translate('ar')?->title }} @endisset">

                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="form-group mb-3 col-md-12">
                                <label class="required" for="image">{{ trans('image') }}</label>
                                <div class="media-center-group form-control" data-max="1" data-type="image">
                                    <input type="text" hidden="hidden" class="form-control" name="image"
                                        value="{{ old('image', $item->image ?? null) }}">
                                    <button type="button" class="btn btn-secondary media-center-load"
                                        style="margin-top: 10px;"><i class="fa fa-file-upload"></i></button>
                                    <div class="input-gallery"></div>
                                </div>
                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <label class="" for="gallery">{{ trans('gallary') }}</label>
                                <div class="media-center-group form-control" data-max="5" data-type="gallery">
                                    <input type="text" hidden="hidden" class="form-control" name="gallery"
                                        value="{{ old('gallery', $item->gallery ?? null) }}">
                                    <button type="button" class="btn btn-secondary media-center-load"
                                        style="margin-top: 10px;"><i class="fa fa-file-upload"></i></button>
                                    <div class="input-gallery"></div>
                                </div>
                            </div>

                            <div class="col-12 mt-5">
                                <ul class="nav nav-tabs" id="languageTabs" role="tablist">

                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active " id="content-en-tab" data-bs-toggle="tab"
                                            data-bs-target="#content-en" type="button" role="tab"
                                            aria-controls="content-en"
                                            aria-selected=" true">{{ trans('English') }}</button>
                                    </li>

                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link  " id="content-ar-tab" data-bs-toggle="tab"
                                            data-bs-target="#content-ar" type="button" role="tab"
                                            aria-controls="content-ar"
                                            aria-selected=" false">{{ trans('العربية') }}</button>
                                    </li>

                                </ul>
                                <div class="tab-content mt-3" id="languageTabsContent">
                                    <div class="tab-pane fade show active" id="content-en" role="tabpanel"
                                        aria-labelledby="en-tab">

                                        <div class="form-group mb-3 col-md-12">
                                            <div class="editor-container">
                                                <div id="content-en" name="translations[en][content]">
                                                    @isset($item)
                                                        {!! $item?->translate('en')?->content !!}
                                                    @endisset
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="tab-pane fade " id="content-ar" role="tabpanel"
                                        aria-labelledby="ar-tab">

                                        <div class="form-group mb-3 col-md-12">
                                            <div class="editor-container">
                                                <div id="content-ar" name="translations[ar][content]">
                                                    @isset($item)
                                                        {!! $item?->translate('ar')?->content !!}
                                                    @endisset
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>


                            <div class="form-group mb-3 col-md-12">
                                <label class="required" for="status">{{ trans('status') }}</label>
                                <select class="custom-select  form-select advance-select" name="status" id="status">

                                    <option value="">{{ trans('select status') }}</option>
                                    <option value="pending" @selected(isset($item) and $item->status == 'pending')>{{ trans('blog.pending') }}
                                    </option>
                                    <option value="publish" @selected(isset($item) and $item->status == 'publish')>{{ trans('publish') }}</option>

                                </select>

                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <label class="" for="published_at">{{ trans('published_at') }}</label>
                                <input type="datetime-local" name="published_at" class="form-control "
                                    placeholder="{{ trans('Enter published_at') }} "
                                    value="@isset($item){{ $item->published_at }}@endisset">

                            </div>

                            <div class="col-12 mt-5">
                                <ul class="nav nav-tabs" id="languageTabs" role="tablist">

                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active " id="meta_title-en-tab" data-bs-toggle="tab"
                                            data-bs-target="#meta_title-en" type="button" role="tab"
                                            aria-controls="meta_title-en"
                                            aria-selected=" true">{{ trans('English') }}</button>
                                    </li>

                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link  " id="meta_title-ar-tab" data-bs-toggle="tab"
                                            data-bs-target="#meta_title-ar" type="button" role="tab"
                                            aria-controls="meta_title-ar"
                                            aria-selected=" false">{{ trans('العربية') }}</button>
                                    </li>

                                </ul>
                                <div class="tab-content mt-3" id="languageTabsContent">
                                    <div class="tab-pane fade show active" id="meta_title-en" role="tabpanel"
                                        aria-labelledby="en-tab">

                                        <div class="form-group mb-3 col-md-12">
                                            <label class="" for="meta_title">{{ trans('meta_title') }}</label>
                                            <input type="text" name="translations[en][meta_title]"
                                                class="form-control " placeholder="{{ trans('Enter meta_title') }} "
                                                value="@isset($item){{ $item?->translate('en')?->meta_title }}@endisset" />

                                        </div>

                                    </div>
                                    <div class="tab-pane fade " id="meta_title-ar" role="tabpanel"
                                        aria-labelledby="ar-tab">

                                        <div class="form-group mb-3 col-md-12">
                                            <label class="" for="meta_title">{{ trans('meta_title') }}</label>
                                            <input type="text" name="translations[ar][meta_title]"
                                                class="form-control " placeholder="{{ trans('Enter meta_title') }} "
                                                value="@isset($item){{ $item?->translate('ar')?->meta_title }}@endisset" />

                                        </div>

                                    </div>
                                </div>
                            </div>

                            <div class="col-12 mt-5">
                                <ul class="nav nav-tabs" id="languageTabs" role="tablist">

                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active " id="meta_description-en-tab"
                                            data-bs-toggle="tab" data-bs-target="#meta_description-en" type="button"
                                            role="tab" aria-controls="meta_description-en"
                                            aria-selected=" true">{{ trans('English') }}</button>
                                    </li>

                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link  " id="meta_description-ar-tab" data-bs-toggle="tab"
                                            data-bs-target="#meta_description-ar" type="button" role="tab"
                                            aria-controls="meta_description-ar"
                                            aria-selected=" false">{{ trans('العربية') }}</button>
                                    </li>

                                </ul>
                                <div class="tab-content mt-3" id="languageTabsContent">
                                    <div class="tab-pane fade show active" id="meta_description-en" role="tabpanel"
                                        aria-labelledby="en-tab">

                                        <div class="form-group mb-3 col-md-12">
                                            <label class=""
                                                for="meta_description">{{ trans('meta_description') }}</label>
                                            <input type="text" name="translations[en][meta_description]"
                                                class="form-control " placeholder="{{ trans('Enter meta_description') }} "
                                                value="@isset($item){{ $item?->translate('en')?->meta_description }}@endisset" />

                                        </div>

                                    </div>
                                    <div class="tab-pane fade " id="meta_description-ar" role="tabpanel"
                                        aria-labelledby="ar-tab">

                                        <div class="form-group mb-3 col-md-12">
                                            <label class=""
                                                for="meta_description">{{ trans('meta_description') }}</label>
                                            <input type="text" name="translations[ar][meta_description]"
                                                class="form-control " placeholder="{{ trans('Enter meta_description') }} "
                                                value="@isset($item){{ $item?->translate('ar')?->meta_description }}@endisset" />

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
@endpush
