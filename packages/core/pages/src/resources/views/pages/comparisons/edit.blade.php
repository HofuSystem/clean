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
                        @if (isset($item)) action="{{ route('dashboard.comparisons.edit', $item->id) }}"
                            data-mode="edit"
                        @else
                            action="{{ route('dashboard.comparisons.create') }}"
                            data-mode="new" @endif>

                        @csrf
                        <div class="card-body row">

                            <!-- Point Field -->
                            <div class="col-12 row mt-5">
                                <ul class="nav nav-tabs" id="languageTabs" role="tablist">

                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active " id="point-en-tab" data-bs-toggle="tab"
                                            data-bs-target="#point-en" type="button" role="tab"
                                            aria-controls="point-en" aria-selected=" true">{{ trans('English') }}</button>
                                    </li>

                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link  " id="point-ar-tab" data-bs-toggle="tab"
                                            data-bs-target="#point-ar" type="button" role="tab"
                                            aria-controls="point-ar" aria-selected=" false">{{ trans('arabic') }}</button>
                                    </li>

                                </ul>
                                <div class="tab-content mt-3" id="languageTabsContent">
                                    <div class="tab-pane fade show active" id="point-en" role="tabpanel"
                                        aria-labelledby="en-tab">

                                        <div class="form-group mb-3 col-md-12">
                                            <label class="required" for="point">{{ trans('comparison point') }}</label>
                                            <input type="text" name="translations[en][point]" class="form-control "
                                                placeholder="{{ trans('Enter comparison point') }} "
                                                value="@if (isset($item)) {{ $item?->translate('en')?->point }} @endif">

                                        </div>

                                    </div>
                                    <div class="tab-pane fade " id="point-ar" role="tabpanel" aria-labelledby="ar-tab">

                                        <div class="form-group mb-3 col-md-12">
                                            <label class="required" for="point">{{ trans('comparison point') }}</label>
                                            <input type="text" name="translations[ar][point]" class="form-control "
                                                placeholder="{{ trans('Enter comparison point') }} "
                                                value="@if (isset($item)) {{ $item?->translate('ar')?->point }} @endif">

                                        </div>

                                    </div>
                                </div>
                            </div>

                            <!-- Us Text Field -->
                            <div class="col-12 row mt-5">
                                <ul class="nav nav-tabs" id="languageTabs2" role="tablist">

                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active " id="us-en-tab" data-bs-toggle="tab"
                                            data-bs-target="#us-en" type="button" role="tab"
                                            aria-controls="us-en"
                                            aria-selected=" true">{{ trans('English') }}</button>
                                    </li>

                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link  " id="us-ar-tab" data-bs-toggle="tab"
                                            data-bs-target="#us-ar" type="button" role="tab"
                                            aria-controls="us-ar"
                                            aria-selected=" false">{{ trans('arabic') }}</button>
                                    </li>

                                </ul>
                                <div class="tab-content mt-3" id="languageTabsContent2">
                                    <div class="tab-pane fade show active" id="us-en" role="tabpanel"
                                        aria-labelledby="en-tab">

                                        <div class="form-group mb-3 col-md-12">
                                            <label class="required" for="us_text">{{ trans('our advantage') }}</label>
                                            <textarea name="translations[en][us_text]" class="form-control " rows="3"
                                                placeholder="{{ trans('Enter our advantage') }} ">@if (isset($item)){{ $item?->translate('en')?->us_text }}@endif</textarea>

                                        </div>

                                    </div>
                                    <div class="tab-pane fade " id="us-ar" role="tabpanel"
                                        aria-labelledby="ar-tab">

                                        <div class="form-group mb-3 col-md-12">
                                            <label class="required" for="us_text">{{ trans('our advantage') }}</label>
                                            <textarea name="translations[ar][us_text]" class="form-control " rows="3"
                                                placeholder="{{ trans('Enter our advantage') }} ">@if (isset($item)){{ $item?->translate('ar')?->us_text }}@endif</textarea>

                                        </div>

                                    </div>
                                </div>
                            </div>

                            <!-- Them Text Field -->
                            <div class="col-12 row mt-5">
                                <ul class="nav nav-tabs" id="languageTabs3" role="tablist">

                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active " id="them-en-tab" data-bs-toggle="tab"
                                            data-bs-target="#them-en" type="button" role="tab"
                                            aria-controls="them-en"
                                            aria-selected=" true">{{ trans('English') }}</button>
                                    </li>

                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link  " id="them-ar-tab" data-bs-toggle="tab"
                                            data-bs-target="#them-ar" type="button" role="tab"
                                            aria-controls="them-ar"
                                            aria-selected=" false">{{ trans('arabic') }}</button>
                                    </li>

                                </ul>
                                <div class="tab-content mt-3" id="languageTabsContent3">
                                    <div class="tab-pane fade show active" id="them-en" role="tabpanel"
                                        aria-labelledby="en-tab">

                                        <div class="form-group mb-3 col-md-12">
                                            <label class="required" for="them_text">{{ trans('their disadvantage') }}</label>
                                            <textarea name="translations[en][them_text]" class="form-control " rows="3"
                                                placeholder="{{ trans('Enter their disadvantage') }} ">@if (isset($item)){{ $item?->translate('en')?->them_text }}@endif</textarea>

                                        </div>

                                    </div>
                                    <div class="tab-pane fade " id="them-ar" role="tabpanel"
                                        aria-labelledby="ar-tab">

                                        <div class="form-group mb-3 col-md-12">
                                            <label class="required" for="them_text">{{ trans('their disadvantage') }}</label>
                                            <textarea name="translations[ar][them_text]" class="form-control " rows="3"
                                                placeholder="{{ trans('Enter their disadvantage') }} ">@if (isset($item)){{ $item?->translate('ar')?->them_text }}@endif</textarea>

                                        </div>

                                    </div>
                                </div>
                            </div>

                            <!-- Order Field -->
                            <div class="form-group mb-3 col-md-6 mt-5">
                                <label class="" for="order">{{ trans('order') }}</label>
                                <input type="number" name="order" class="form-control"
                                    placeholder="{{ trans('Enter display order') }}"
                                    value="{{ old('order', $item->order ?? 0) }}">
                            </div>

                            <!-- Is Active Field -->
                            <div class="form-group mb-3 col-md-6 mt-5">
                                <label class="" for="is_active">{{ trans('is active') }}</label>
                                <select name="is_active" class="form-control">
                                    <option value="1" {{ (isset($item) && $item->is_active) ? 'selected' : 'selected' }}>@lang('active')</option>
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


