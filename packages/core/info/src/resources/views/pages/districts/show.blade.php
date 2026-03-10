@extends('admin::layouts.dashboard')
@section('content')
    <!--end::Header-->
    <!--begin::Content-->
    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        <!--begin::Toolbar-->
        <div class="toolbar" id="kt_toolbar">
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
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-200 w-5px h-2px"></span>
                        </li>
                        <!--end::Item-->
                        <!--begin::Item-->
                        <li class="breadcrumb-item text-muted">@lang("info")</li>
                        <!--end::Item-->
                        <!--begin::Item-->
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-200 w-5px h-2px"></span>
                        </li>
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
            <div id="kt_content_container" class="container-xxl">
                <!--begin::Card-->
                <div class="card show-page">
                    
            <div class="card">
                <div class="card-body row">
                    
            <div class="form-group mb-3 col-md-12">
                <label>{{ trans("slug") }}</label><p>{{ $item->slug ?? 'N/A' }}</p>
            </div>
        
            <div class="col-12 row mt-5">
                <ul class="nav nav-tabs" id="languageTabs" role="tablist">
                    
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="name-en-tab" data-bs-toggle="tab" data-bs-target="#name-en" type="button" role="tab" aria-controls="name-en" aria-selected="true">
                        {{ trans("English") }}
                    </button>
                </li>
            
                <li class="nav-item" role="presentation">
                    <button class="nav-link " id="name-ar-tab" data-bs-toggle="tab" data-bs-target="#name-ar" type="button" role="tab" aria-controls="name-ar" aria-selected="false">
                        {{ trans("العربية") }}
                    </button>
                </li>
            
                </ul>
                <div class="tab-content mt-3" id="languageTabsContent">
                    
                <div class="tab-pane fade show active" id="name-en" role="tabpanel" aria-labelledby="en-tab">
                    
            <div class="form-group mb-3 col-md-12">
                <label>{{ trans("name") }}</label><p>{{ $item?->translate('en')?->name ?? 'N/A' }}</p>
            </div>
        
                </div>
            
                <div class="tab-pane fade " id="name-ar" role="tabpanel" aria-labelledby="ar-tab">
                    
            <div class="form-group mb-3 col-md-12">
                <label>{{ trans("name") }}</label><p>{{ $item?->translate('ar')?->name ?? 'N/A' }}</p>
            </div>
        
                </div>
            
                </div>
            </div>
        
            <div class="form-group mb-3 col-md-6">
                <label>{{ trans("lat") }}</label><p>{{ $item->lat ?? 'N/A' }}</p>
            </div>
        
            <div class="form-group mb-3 col-md-6">
                <label>{{ trans("lng") }}</label><p>{{ $item->lng ?? 'N/A' }}</p>
            </div>
        
            <div class="form-group mb-3 col-md-12">
                <label>{{ trans("postal code") }}</label><p>{{ $item->postal_code ?? 'N/A' }}</p>
            </div>
        
            <div class="form-group mb-3 col-md-12">
                <label>{{ trans("city") }}</label>
                @isset($item->city)
                    <div class="alert alert-primary m-1" role="alert"><a href="{{ route('dashboard.cities.show',$item->city->id) }}">{{ $item?->city?->name ?? 'N/A' }}</a></div>
                @endisset
                
            </div>
        

    
                </div>
                <div class="card-footer">
                    <a href="{{ route('dashboard.districts.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> {{ trans('Back') }}</a>
                </div>
            </div>
        

                    @include('comment::inc.comment-section',['commentUrl'=>route('dashboard.districts.comment',$item->id)])
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
