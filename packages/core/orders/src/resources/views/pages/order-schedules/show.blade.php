
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
                        <li class="breadcrumb-item text-muted">@lang("orders")</li>
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
                <label>{{ trans("client") }}</label>
                @isset($item->client)
                    <div class="alert alert-primary m-1" role="alert"><a href="{{ route('dashboard.users.show',$item->client->id) }}">{{ $item?->client?->fullname ?? 'N/A' }}</a></div>
                @endisset
                
            </div>
        
            <div class="form-group mb-3 col-md-12">
                <label>{{ trans("type") }}</label><div class="alert alert-warning m-1" role="alert">{{ $item->type ?? 'N/A' }}</div>
            </div>
        
            <div class="form-group mb-3 col-md-12">
                <label>{{ trans("receiver day") }}</label><div class="alert alert-warning m-1" role="alert">{{ $item->receiver_day ?? 'N/A' }}</div>
            </div>
        
            <div class="form-group mb-3 col-md-12">
                <label>{{ trans("receiver date") }}</label><p>{{ $item->receiver_date ?? 'N/A' }}</p>
            </div>
        
            <div class="form-group mb-3 col-md-12">
                <label>{{ trans("receiver time") }}</label><p>{{ $item->receiver_time ?? 'N/A' }}</p>
            </div>
        
            <div class="form-group mb-3 col-md-12">
                <label>{{ trans("receiver to time") }}</label><p>{{ $item->receiver_to_time ?? 'N/A' }}</p>
            </div>
        
            <div class="form-group mb-3 col-md-12">
                <label>{{ trans("delivery day") }}</label><div class="alert alert-warning m-1" role="alert">{{ $item->delivery_day ?? 'N/A' }}</div>
            </div>
        
            <div class="form-group mb-3 col-md-12">
                <label>{{ trans("delivery date") }}</label><p>{{ $item->delivery_date ?? 'N/A' }}</p>
            </div>
        
            <div class="form-group mb-3 col-md-12">
                <label>{{ trans("delivery time") }}</label><p>{{ $item->delivery_time ?? 'N/A' }}</p>
            </div>
        
            <div class="form-group mb-3 col-md-12">
                <label>{{ trans("delivery to time") }}</label><p>{{ $item->delivery_to_time ?? 'N/A' }}</p>
            </div>
        
            <div class="form-group mb-3 col-md-12">
                <label>{{ trans("receiver address") }}</label>
                @isset($item->receiverAddress)
                    <div class="alert alert-primary m-1" role="alert"><a href="{{ route('dashboard.workers.show',$item->receiverAddress->id) }}">{{ $item?->receiverAddress?->name ?? 'N/A' }}</a></div>
                @endisset
                
            </div>
        
            <div class="form-group mb-3 col-md-12">
                <label>{{ trans("delivery address") }}</label>
                @isset($item->deliveryAddress)
                    <div class="alert alert-primary m-1" role="alert"><a href="{{ route('dashboard.workers.show',$item->deliveryAddress->id) }}">{{ $item?->deliveryAddress?->name ?? 'N/A' }}</a></div>
                @endisset
                
            </div>
        
            <div class="form-group mb-3 col-md-12">
                <label>{{ trans("note") }}</label><p>{{ $item->note ?? 'N/A' }}</p>
            </div>
        
                </div>
                <div class="card-footer">
                    <a href="{{ route('dashboard.order-schedules.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> {{ trans('Back') }}</a>
                </div>
            </div>
        


                    @include('comment::inc.comment-section', [
                        'commentUrl' => route('dashboard.order-schedules.comment', $item->id),
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
