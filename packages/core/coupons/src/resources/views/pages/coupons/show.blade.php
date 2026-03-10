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
                            <a href=""
                                class="text-muted text-hover-primary">@lang('Home')</a>
                        </li>
                        <!--end::Item-->
                        
                        <!--begin::Item-->
                        <li class="breadcrumb-item text-muted">@lang("coupons")</li>
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
                    
            <div class="col-12 mt-5">
                <ul class="nav nav-tabs" id="languageTabs" role="tablist">
                    
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="title-en-tab" data-bs-toggle="tab" data-bs-target="#title-en" type="button" role="tab" aria-controls="title-en" aria-selected="true">
                        {{ trans("English") }}
                    </button>
                </li>
            
                <li class="nav-item" role="presentation">
                    <button class="nav-link " id="title-ar-tab" data-bs-toggle="tab" data-bs-target="#title-ar" type="button" role="tab" aria-controls="title-ar" aria-selected="false">
                        {{ trans("العربية") }}
                    </button>
                </li>
            
                </ul>
                <div class="tab-content mt-3" id="languageTabsContent">
                    
                <div class="tab-pane fade show active" id="title-en" role="tabpanel" aria-labelledby="en-tab">
                    
            <div class="form-group mb-3 col-md-12">
                <label>{{ trans("title") }}</label><p>{{ $item?->translate('en')?->title ?? 'N/A' }}</p>
            </div>
        
                </div>
            
                <div class="tab-pane fade " id="title-ar" role="tabpanel" aria-labelledby="ar-tab">
                    
            <div class="form-group mb-3 col-md-12">
                <label>{{ trans("title") }}</label><p>{{ $item?->translate('ar')?->title ?? 'N/A' }}</p>
            </div>
        
                </div>
            
                </div>
            </div>
        
            <div class="form-group mb-3 col-md-4">
                <label>{{ trans("status") }}</label><div class="alert alert-warning m-1" role="alert">{{ $item->status ?? 'N/A' }}</div>
            </div>
        
            <div class="form-group mb-3 col-md-4">
                <label>{{ trans("applying") }}</label><div class="alert alert-warning m-1" role="alert">{{ $item->applying ?? 'N/A' }}</div>
            </div>
        
            <div class="form-group mb-3 col-md-4">
                <label>{{ trans("code") }}</label><p>{{ $item->code ?? 'N/A' }}</p>
            </div>
        
            <div class="form-group mb-3 col-md-6">
                <label>{{ trans("max use") }}</label><p>{{ $item->max_use ?? 'N/A' }}</p>
            </div>
        
            <div class="form-group mb-3 col-md-6">
                <label>{{ trans("max use per user") }}</label><p>{{ $item->max_use_per_user ?? 'N/A' }}</p>
            </div>
        
            <div class="form-group mb-3 col-md-12">
                <label>{{ trans("payment method") }}</label><div class="alert alert-warning m-1" role="alert">{{ $item->payment_method ?? 'N/A' }}</div>
            </div>
        
            <div class="form-group mb-3 col-md-6">
                <label>{{ trans("start at") }}</label><p>{{ $item->start_at ?? 'N/A' }}</p>
            </div>
        
            <div class="form-group mb-3 col-md-6">
                <label>{{ trans("end at") }}</label><p>{{ $item->end_at ?? 'N/A' }}</p>
            </div>
        
            <div class="form-group mb-3 col-md-12">
                <label>{{ trans("order Type") }}</label><div class="alert alert-warning m-1" role="alert">{{ $item->order_type ?? 'N/A' }}</div>
            </div>
        
            <div class="form-group mb-3 col-md-4">
                <label>{{ trans("all products") }}</label><p>{{ $item->all_products ?? 'N/A' }}</p>
            </div>
        
            <div class="form-group mb-3 col-md-4">
                <label>{{ trans("products") }}</label>
               <div class="row">
                    @foreach($item->products as $single)
                        <div class="col-md-2 col-sm-4 alert alert-success m-1" role="alert"> <a href="{{ route('dashboard.products.show',$single->id) }}"> {{$single->name}} </a></div>
                    @endforeach
                </div>
                
            </div>
        
            <div class="form-group mb-3 col-md-4">
                <label>{{ trans("categories") }}</label>
               <div class="row">
                    @foreach($item->categories as $single)
                        <div class="col-md-2 col-sm-4 alert alert-success m-1" role="alert"> <a href="{{ route('dashboard.categories.show',$single->id) }}"> {{$single->name}} </a></div>
                    @endforeach
                </div>
                
            </div>
        
            <div class="form-group mb-3 col-md-4">
                <label>{{ trans("all users") }}</label><p>{{ $item->all_users ?? 'N/A' }}</p>
            </div>
        
            <div class="form-group mb-3 col-md-4">
                <label>{{ trans("users") }}</label>
               <div class="row">
                    @foreach($item->users as $single)
                        <div class="col-md-2 col-sm-4 alert alert-success m-1" role="alert"> <a href="{{ route('dashboard.users.show',$single->id) }}"> {{$single->fullname}} </a></div>
                    @endforeach
                </div>
                
            </div>
        
            <div class="form-group mb-3 col-md-4">
                <label>{{ trans("roles") }}</label>
               <div class="row">
                    @foreach($item->roles as $single)
                        <div class="col-md-2 col-sm-4 alert alert-success m-1" role="alert"> <a href="{{ route('dashboard.roles.show',$single->id) }}"> {{$single->name}} </a></div>
                    @endforeach
                </div>
                
            </div>
        
            <div class="form-group mb-3 col-md-6">
                <label>{{ trans("order minimum") }}</label><p>{{ $item->order_minimum ?? 'N/A' }}</p>
            </div>
        
            <div class="form-group mb-3 col-md-6">
                <label>{{ trans("order maximum") }}</label><p>{{ $item->order_maximum ?? 'N/A' }}</p>
            </div>
        
            <div class="form-group mb-3 col-md-4">
                <label>{{ trans("type") }}</label><div class="alert alert-warning m-1" role="alert">{{ $item->type ?? 'N/A' }}</div>
            </div>
        
            <div class="form-group mb-3 col-md-4">
                <label>{{ trans("value") }}</label><p>{{ $item->value ?? 'N/A' }}</p>
            </div>
        
            <div class="form-group mb-3 col-md-4">
                <label>{{ trans("max value") }}</label><p>{{ $item->max_value ?? 'N/A' }}</p>
            </div>
        
                </div>
                <div class="card-footer">
                    <a href="{{ route('dashboard.coupons.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> {{ trans('Back') }}</a>
                </div>
            </div>
        

                    @include('comment::inc.comment-section',['commentUrl'=>route('dashboard.coupons.comment',$item->id)])
                </div>
                <!--end::Card-->
                @if(isset($item) and $item->orders->count() > 0) 
                <div class="card my-2">
                    <div class="card-header">
                         <h3 class="card-title">{{ trans('orders') }}</h3> 
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered text-center">
                                        <thead class="table-primary">
                                            <tr>
                                                <th>{{ trans('client name') }}</th>
                                                <th>{{ trans('client phone') }}</th>
                                                <th>{{ trans('order reference id') }}</th>
                                                <th>{{ trans('order total price') }}</th>
                                                <th>{{ trans('order discount') }}</th>
                                                <th>{{ trans('order status') }}</th>
                                                <th>{{ trans('order date') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($item->orders as $order)
                                                <tr>
                                                    <td>{{ $order->client?->fullname }}</td>
                                                    <td>{{ $order->client?->phone }}</td>
                                                    <td>{{ $order->reference_id }}</td>
                                                    <td>{{ $order->total_price }}</td>
                                                    <td>{{ $order->total_coupon }}</td>
                                                    <td>{{ $order->status }}</td>
                                                    <td>{{ $order->created_at }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <!--end::Container-->
                            </div>
                            <!--end::Post-->
                        </div>
                    </div>
                </div>
                @endif
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
