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
                            <a href="{{ route('dashboard.index') }}"
                                class="text-muted text-hover-primary">@lang('Home')</a>
                        </li>
                        <!--end::Item-->
                        
                        <!--begin::Item-->
                        <li class="breadcrumb-item text-muted">@lang("Purchases")</li>
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
                            
                            <div class="form-group mb-3 col-md-6">
                                <label class="fw-bold">{{ trans("item") }}</label>
                                <p>{{ $item->item?->name ?? 'N/A' }}</p>
                            </div>
                        
                            <div class="form-group mb-3 col-md-6">
                                <label class="fw-bold">{{ trans("provider") }}</label>
                                <p>{{ $item->provider?->name ?? 'N/A' }}</p>
                            </div>
                        
                            <div class="form-group mb-3 col-md-4">
                                <label class="fw-bold">{{ trans("value before tax") }}</label>
                                <p>{{ $item->value_before_tax ?? 'N/A' }}</p>
                            </div>

                            <div class="form-group mb-3 col-md-4">
                                <label class="fw-bold">{{ trans("tax value") }}</label>
                                <p>{{ $item->tax_value ?? 'N/A' }}</p>
                            </div>

                            <div class="form-group mb-3 col-md-4">
                                <label class="fw-bold">{{ trans("value after tax") }}</label>
                                <p>{{ $item->value_after_tax ?? 'N/A' }}</p>
                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <label class="fw-bold">{{ trans("notes") }}</label>
                                <p>{{ $item->notes ?? 'N/A' }}</p>
                            </div>

                            <div class="form-group mb-3 col-md-6">
                                <label class="fw-bold">{{ trans("Collection Date") }}</label>
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
                        
                        </div>
                        <div class="card-footer">
                            <a href="{{ route('dashboard.purchases.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> {{ trans('Back') }}</a>
                        </div>
                    </div>
                
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