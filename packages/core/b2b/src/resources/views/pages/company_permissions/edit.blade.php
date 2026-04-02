@extends('admin::layouts.dashboard')
@section('content')
    <!--begin::Content-->
    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        <!--begin::Toolbar-->
        <div class="toolbar my-3" id="kt_toolbar">
            <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
                <div data-kt-swapper="true" data-kt-swapper-mode="prepend"
                    data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}"
                    class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
                    <h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1">{{ $title }}</h1>
                    <span class="h-20px border-gray-200 border-start mx-4"></span>
                    <ul class="breadcrumb breadcrumb-separatorless fw-bold fs-7 my-1">
                        <li class="breadcrumb-item text-muted">
                            <a href="" class="text-muted text-hover-primary">@lang('Home')</a>
                        </li>
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('dashboard.company-permissions.index') }}" class="text-muted text-hover-primary">@lang('company permissions')</a>
                        </li>
                        <li class="breadcrumb-item text-dark">{{ $title }}</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="post d-flex flex-column-fluid" id="kt_post">
            <div id="kt_content_container" class="container-fluid">
                <div class="card">
                    <form class="form" method="POST" id="operation-form" 
                        redirect-to="{{ route('dashboard.company-permissions.index') }}" 
                        data-id="{{ $item->id ?? null }}"
                        @isset($item)
                            action="{{ route('dashboard.company-permissions.edit', $item->id) }}"
                            data-mode="edit"
                        @else
                            action="{{ route('dashboard.company-permissions.create') }}"
                            data-mode="new"
                        @endisset>

                        @csrf
                        <div class="card-body row">

                            <!--– Slug –-->
                            <div class="form-group mb-3 col-md-12">
                                <label class="required" for="slug">{{ trans('slug') }}</label>
                                <input type="text" name="slug" class="form-control"
                                    placeholder="{{ trans('Enter slug') }}"
                                    value="{{ old('slug', $item->slug ?? null) }}">
                            </div>

                            <div class="col-12 mt-5">
                                <ul class="nav nav-tabs" id="languageTabs" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="name-en-tab" data-bs-toggle="tab" data-bs-target="#name-en" type="button" role="tab" aria-controls="name-en" aria-selected="true">{{ trans('English') }}</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="name-ar-tab" data-bs-toggle="tab" data-bs-target="#name-ar" type="button" role="tab" aria-controls="name-ar" aria-selected="false">{{ trans('Arabic') }}</button>
                                    </li>
                                </ul>
                                <div class="tab-content mt-3" id="languageTabsContent">
                                    <div class="tab-pane fade show active" id="name-en" role="tabpanel" aria-labelledby="name-en-tab">
                                        <div class="form-group mb-3 col-md-12">
                                            <label class="required" for="name">{{ trans('name') }}</label>
                                            <input type="text" name="translations[en][name]" class="form-control"
                                                placeholder="{{ trans('Enter name') }}" 
                                                value="{{ isset($item) ? $item->translate('en')?->name : '' }}">
                                        </div>
                                        <div class="form-group mb-3 col-md-12">
                                            <label for="description">{{ trans('description') }}</label>
                                            <textarea name="translations[en][description]" class="form-control" rows="3"
                                                placeholder="{{ trans('Enter description') }}">{{ isset($item) ? $item->translate('en')?->description : '' }}</textarea>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="name-ar" role="tabpanel" aria-labelledby="name-ar-tab">
                                        <div class="form-group mb-3 col-md-12">
                                            <label class="required" for="name">{{ trans('name') }}</label>
                                            <input type="text" name="translations[ar][name]" class="form-control"
                                                placeholder="{{ trans('Enter name') }}" 
                                                value="{{ isset($item) ? $item->translate('ar')?->name : '' }}">
                                        </div>
                                        <div class="form-group mb-3 col-md-12">
                                            <label for="description">{{ trans('description') }}</label>
                                            <textarea name="translations[ar][description]" class="form-control" rows="3"
                                                placeholder="{{ trans('Enter description') }}">{{ isset($item) ? $item->translate('ar')?->description : '' }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="card-footer">
                            <div class="row">
                                <div class="col-lg-9 ml-lg-auto">
                                    <button type="submit" class="btn btn-primary font-weight-bold mr-2">{{ trans('save') }}</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('js')
    <script src="{{ asset('control') }}/js/custom/crud/form.js"></script>
@endpush
