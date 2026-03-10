
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
                        <li class="breadcrumb-item text-muted">@lang("payment-gateways")</li>
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
                    
            <div class="form-group mb-3 col-md-12">
                <label>{{ trans("transaction id") }}</label><p>{{ $item->transaction_id ?? 'N/A' }}</p>
            </div>
        
            <div class="form-group mb-3 col-md-12">
                <label>{{ trans("for") }}</label><p>{{ $item->for ?? 'N/A' }}</p>
            </div>
        
            <div class="form-group mb-3 col-md-12">
                <label>{{ trans("status") }}</label><p>{{ $item->status ?? 'N/A' }}</p>
            </div>
        
            <div class="form-group mb-3 col-md-12">
                <label>{{ trans("request data") }}</label><p>{{ $item->request_data ?? 'N/A' }}</p>
            </div>
        
            <div class="form-group mb-3 col-md-12">
                <label>{{ trans("payment method") }}</label><p>{{ $item->payment_method ?? 'N/A' }}</p>
            </div>
        
            <div class="form-group mb-3 col-md-12">
                <label>{{ trans("payment data") }}</label><p>{{ $item->payment_data ?? 'N/A' }}</p>
            </div>
        
            <div class="form-group mb-3 col-md-12">
                <label>{{ trans("payment response") }}</label><p>{{ $item->payment_response ?? 'N/A' }}</p>
            </div>
        
                </div>
                <div class="card-footer">
                    <a href="{{ route('dashboard.payment-transactions.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> {{ trans('Back') }}</a>
                </div>
            </div>
        


                    @include('comment::inc.comment-section', [
                        'commentUrl' => route('dashboard.payment-transactions.comment', $item->id),
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
