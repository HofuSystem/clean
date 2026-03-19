
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
                        <li class="breadcrumb-item text-muted">@lang("users")</li>
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
                <label>{{ trans("contract") }}</label>
                @isset($item->contract)
                    <div class="alert alert-primary m-1" role="alert"><a href="{{ route('dashboard.contracts.show',$item->contract->id) }}">{{ $item?->contract?->title ?? 'N/A' }}</a></div>
                @endisset
                
            </div>
        
            <div class="form-group mb-3 col-md-6">
                <label>{{ trans("product") }}</label>
                @isset($item->product)
                    <div class="alert alert-primary m-1" role="alert"><a href="{{ route('dashboard.products.show',$item->product->id) }}">{{ $item?->product?->name ?? 'N/A' }}</a></div>
                @endisset
                
            </div>
        
            <div class="form-group mb-3 col-md-6">
                <label>{{ trans("price") }}</label><p>{{ $item->price ?? 'N/A' }}</p>
            </div>
        
                </div>
                <div class="card-footer">
                    <a href="{{ route('dashboard.contracts-prices.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> {{ trans('Back') }}</a>
                </div>
            </div>
        


                    @include('comment::inc.comment-section', [
                        'commentUrl' => route('dashboard.contracts-prices.comment', $item->id),
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
