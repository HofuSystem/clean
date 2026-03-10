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
                    
            <div class="form-group mb-3 col-md-6">
                <label>{{ trans("order") }}</label>
                @isset($item->order)
                    <div class="alert alert-primary m-1" role="alert"><a href="{{ route('dashboard.orders.show',$item->order->id) }}">{{ $item?->order?->reference_id ?? 'N/A' }}</a></div>
                @endisset
                
            </div>
        
            <div class="form-group mb-3 col-md-6">
                <label>{{ trans("product") }}</label>
                @isset($item->product)
                    <div class="alert alert-primary m-1" role="alert"><a href="{{ route('dashboard.products.show',$item->product->id) }}">{{ $item?->product?->name ?? 'N/A' }}</a></div>
                @endisset
                
            </div>
        
            <div class="form-group mb-3 col-md-12">
                <label>{{ trans("product data") }}</label><p>{{ $item->product_data ?? 'N/A' }}</p>
            </div>
        
            <div class="form-group mb-3 col-md-6">
                <label>{{ trans("product price") }}</label><p>{{ $item->product_price ?? 'N/A' }}</p>
            </div>
        
            <div class="form-group mb-3 col-md-6">
                <label>{{ trans("quantity") }}</label><p>{{ $item->quantity ?? 'N/A' }}</p>
            </div>
        
            <div class="form-group mb-3 col-md-6">
                <label>{{ trans("carpet size") }}</label><p>{{ $item->width ?? 'N/A' }}</p>
            </div>
            <div class="form-group mb-3 col-md-6">
                <label>{{ trans("carpet size") }}</label><p>{{ $item->height ?? 'N/A' }}</p>
            </div>
        
            <div class="form-group mb-3 col-md-12">
                <label>{{ trans("add by admin") }}</label><p>{{ $item->add_by_admin ?? 'N/A' }}</p>
            </div>
        
            <div class="form-group mb-3 col-md-12">
                <label>{{ trans("update by admin") }}</label><p>{{ $item->update_by_admin ?? 'N/A' }}</p>
            </div>
        
            <div class="form-group mb-3 col-md-6">
                <label>{{ trans("is picked") }}</label><p>{{ $item->is_picked ?? 'N/A' }}</p>
            </div>
        
            <div class="form-group mb-3 col-md-6">
                <label>{{ trans("is delivered") }}</label><p>{{ $item->is_delivered ?? 'N/A' }}</p>
            </div>
        
        <div class="mt-3">
            <h3 class="text-dark">{{ trans('qtyUpdates') }}</h3>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-primary">
                        <tr>
                            <th>{{ trans('from') }}</th><th>{{ trans('to') }}</th><th>{{ trans('updater email') }}</th><th>{{ trans('actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        
        @foreach ($item->qtyUpdates ?? [] as $sItem)
            <tr>
    <td>{{ $sItem->from ?? 'N/A' }}</td><td>{{ $sItem->to ?? 'N/A' }}</td><td>{{ $sItem->updater_email ?? 'N/A' }}</td>
            <td>{!! $sItem->showActions  !!}</td>
            </tr>
        @endforeach
    
                    </tbody>
                </table>
            </div>
        </div>
    
                </div>
                <div class="card-footer">
                    <a href="{{ route('dashboard.order-items.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> {{ trans('Back') }}</a>
                </div>
            </div>
        

                    @include('comment::inc.comment-section',['commentUrl'=>route('dashboard.order-items.comment',$item->id)])
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
