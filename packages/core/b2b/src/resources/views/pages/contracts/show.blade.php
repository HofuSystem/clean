
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
                <label>{{ trans("title") }}</label><p>{{ $item->title ?? 'N/A' }}</p>
            </div>
        
            <div class="form-group mb-3 col-md-6">
                <label>{{ trans("months count") }}</label><p>{{ $item->months_count ?? 'N/A' }}</p>
            </div>
        
            <div class="form-group mb-3 col-md-6">
                <label>{{ trans("month fees") }}</label><p>{{ $item->month_fees ?? 'N/A' }}</p>
            </div>
        
            <div class="form-group mb-3 col-md-6">
                <label>{{ trans("unlimited days") }}</label><p>{{ $item->unlimited_days ? trans('Yes') : trans('No') }}</p>
            </div>
        
            <div class="form-group mb-3 col-md-6">
                <label>{{ trans("number of days") }}</label><p>{{ $item->number_of_days ?? 'N/A' }}</p>
            </div>
        
            <div class="form-group mb-3 col-md-12">
                <label>{{ trans("contract") }}</label><div class="input-gallery "><div class="img-row " >{!!  Core\MediaCenter\Helpers\MediaCenterHelper::getImagesHtml($item->contract) !!}</div></div>
            </div>
        
            <div class="form-group mb-3 col-md-12">
                <label>{{ trans("start date") }}</label><p>{{ $item->start_date ?? 'N/A' }}</p>
            </div>
        
            <div class="form-group mb-3 col-md-12">
                <label>{{ trans("end date") }}</label><p>{{ $item->end_date ?? 'N/A' }}</p>
            </div>
        
            <div class="form-group mb-3 col-md-12">
                <label>{{ trans("client") }}</label>
                @isset($item->client)
                    <div class="alert alert-primary m-1" role="alert"><a href="{{ route('dashboard.users.show',$item->client->id) }}">{{ $item?->client?->fullname ?? 'N/A' }}</a></div>
                @endisset
                
            </div>
        
        <div class="mt-3">
            <h3>{{ trans('contract prices') }}</h3>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>{{ trans('product') }}</th><th>{{ trans('price') }}</th><th>{{ trans('over price') }}</th><th>{{ trans('actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        
        @foreach ($item->contractPrices ?? [] as $sItem)
            <tr>
    
                                        <td><a href="{{route("dashboard.contracts-prices.show",$sItem->id)}}" target="_blank"> {{ $sItem?->product?->name }}</a></td><td>{{ $sItem->price ?? 'N/A' }}</td><td>{{ $sItem->over_price ?? 'N/A' }}</td>
            <td>{!! $sItem->showActions  !!}</td>
            </tr>
        @endforeach
    
                    </tbody>
                </table>
            </div>
        </div>
    
                </div>
                <div class="card-footer">
                    <a href="{{ route('dashboard.contracts.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> {{ trans('Back') }}</a>
                </div>
            </div>
        


                    @include('comment::inc.comment-section', [
                        'commentUrl' => route('dashboard.contracts.comment', $item->id),
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
