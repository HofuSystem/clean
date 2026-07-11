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
                            <a href="{{ route('dashboard.index') }}" class="text-muted text-hover-primary">@lang('Home')</a>
                        </li>
                        <li class="breadcrumb-item text-muted">@lang("Financials")</li>
                        <li class="breadcrumb-item text-dark">{{ $title }}</li>
                    </ul>
                </div>
            </div>
        </div>
        <!--end::Toolbar-->
        <!--begin::Post-->
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <div id="kt_content_container" class="container-fluid">
                <!--begin::Card-->
                <div class="card show-page">
                    <div class="card">
                        <div class="card-body row">
                            @if($item->company)
                            <div class="form-group mb-3 col-md-6">
                                <label class="fw-bold">@lang("Company")</label>
                                <p>{{ $item->company->fullname }}</p>
                            </div>
                            @endif

                            @if($item->user)
                            <div class="form-group mb-3 col-md-6">
                                <label class="fw-bold">@lang("User")</label>
                                <p>{{ $item->user->fullname }}</p>
                            </div>
                            @endif

                            <div class="form-group mb-3 col-md-6">
                                <label class="fw-bold">@lang("Reference ID")</label>
                                <p>{{ $item->reference_id ?? 'N/A' }}</p>
                            </div>

                            <div class="form-group mb-3 col-md-6">
                                <label class="fw-bold">@lang("Amount")</label>
                                <p>{{ $item->amount ?? 'N/A' }}</p>
                            </div>

                            <div class="form-group mb-3 col-md-6">
                                <label class="fw-bold">@lang("Type")</label>
                                <p>{{ ucfirst($item->type ?? 'N/A') }}</p>
                            </div>

                            <div class="form-group mb-3 col-md-6">
                                <label class="fw-bold">@lang("Collection Date")</label>
                                <p>{{ $item->collection_date ? $item->collection_date->format('Y-m-d') : 'N/A' }}</p>
                            </div>

                            <div class="form-group mb-3 col-md-6">
                                <label class="fw-bold">@lang("Attachment")</label>
                                <p>
                                    @if($item->attachment)
                                        <a href="{{ asset('storage/' . $item->attachment) }}" target="_blank" class="btn btn-sm btn-light-primary">
                                            <i class="fas fa-paperclip"></i> @lang('Download / View Attachment')
                                        </a>
                                    @else
                                        N/A
                                    @endif
                                </p>
                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <label class="fw-bold">@lang("Note")</label>
                                <p>{{ $item->note ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="card-footer">
                            <a href="{{ route('dashboard.financials.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i> {{ trans('Back') }}
                            </a>
                        </div>
                    </div>
                </div>
                <!--end::Card-->
            </div>
        </div>
        <!--end::Post-->
    </div>
    <!--end::Content-->
@endsection
@push('css')
<link href="{{ asset('control') }}/js/custom/crud/show.css" rel="stylesheet" type="text/css" />
@endpush
