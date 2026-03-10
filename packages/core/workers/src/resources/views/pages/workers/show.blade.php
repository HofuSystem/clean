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
                        <li class="breadcrumb-item text-muted">@lang("workers")</li>
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
                <label>{{ trans("image") }}</label><div class="gallary-images">{!!  Core\MediaCenter\Helpers\MediaCenterHelper::getImagesHtml($item->image) !!}</div>
            </div>
        
            <div class="form-group mb-3 col-md-12">
                <label>{{ trans("name") }}</label><p>{{ $item->name ?? 'N/A' }}</p>
            </div>
        
            <div class="form-group mb-3 col-md-12">
                <label>{{ trans("phone") }}</label><p>{{ $item->phone ?? 'N/A' }}</p>
            </div>
        
            <div class="form-group mb-3 col-md-12">
                <label>{{ trans("email") }}</label><p>{{ $item->email ?? 'N/A' }}</p>
            </div>
        
            <div class="form-group mb-3 col-md-12">
                <label>{{ trans("years experience") }}</label><p>{{ $item->years_experience ?? 'N/A' }}</p>
            </div>
        
            <div class="form-group mb-3 col-md-12">
                <label>{{ trans("address") }}</label><p>{{ $item->address ?? 'N/A' }}</p>
            </div>
        
            <div class="form-group mb-3 col-md-12">
                <label>{{ trans("birth date") }}</label><p>{{ $item->birth_date ?? 'N/A' }}</p>
            </div>
        
            <div class="form-group mb-3 col-md-12">
                <label>{{ trans("hour price") }}</label><p>{{ $item->hour_price ?? 'N/A' }}</p>
            </div>
        
            <div class="form-group mb-3 col-md-12">
                <label>{{ trans("gender") }}</label><div class="alert alert-warning m-1" role="alert">{{ $item->gender ?? 'N/A' }}</div>
            </div>
        
            <div class="form-group mb-3 col-md-12">
                <label>{{ trans("status") }}</label><div class="alert alert-warning m-1" role="alert">{{ $item->status ?? 'N/A' }}</div>
            </div>
        
            <div class="form-group mb-3 col-md-12">
                <label>{{ trans("identity") }}</label><div class="alert alert-warning m-1" role="alert">{{ $item->identity ?? 'N/A' }}</div>
            </div>
        
            <div class="form-group mb-3 col-md-12">
                <label>{{ trans("nationality") }}</label>
                @isset($item->nationality)
                    <div class="alert alert-primary m-1" role="alert"><a href="{{ route('dashboard.nationalities.show',$item->nationality->id) }}">{{ $item?->nationality?->name ?? 'N/A' }}</a></div>
                @endisset
                
            </div>
        
            <div class="form-group mb-3 col-md-12">
                <label>{{ trans("city") }}</label>
                @isset($item->city)
                    <div class="alert alert-primary m-1" role="alert"><a href="{{ route('dashboard.cities.show',$item->city->id) }}">{{ $item?->city?->name ?? 'N/A' }}</a></div>
                @endisset
                
            </div>
        
            <div class="form-group mb-3 col-md-12">
                <label>{{ trans("categories") }}</label>
               <div class="row">
                    @foreach($item->categories as $single)
                        <div class="col-md-2 col-sm-4 alert alert-success m-1" role="alert"> <a href="{{ route('dashboard.categories.show',$single->id) }}"> {{$single->name}} </a></div>
                    @endforeach
                </div>
                
            </div>
        
            <div class="form-group mb-3 col-md-12">
                <label>{{ trans("leader_id") }}</label>
               <div class="row">
                @isset($item->leader)
                    <div class="col-md-2 col-sm-4 alert alert-success m-1" role="alert"> <a href="{{ route('dashboard.users.show',$item->leader->id) }}"> {{$item->leader->fullname}} </a></div>
                    
                @endisset
                </div>
                
            </div>
        
        <div class="mt-3">
            <h3 class="text-dark">{{ trans('work days') }}</h3>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-primary">
                        <tr>
                            <th>{{ trans('date') }}</th><th>{{ trans('type') }}</th><th>{{ trans('actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        
        @foreach ($item->workdays ?? [] as $sItem)
            <tr>
    <td>{{ $sItem->date ?? 'N/A' }}</td><td>{{ $sItem->type ?? 'N/A' }}</td>
            <td>{!! $sItem->showActions  !!}</td>
            </tr>
        @endforeach
    
                    </tbody>
                </table>
            </div>
        </div>
    
                </div>
                <div class="card-footer">
                    <a href="{{ route('dashboard.workers.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> {{ trans('Back') }}</a>
                </div>
            </div>
        

                    @include('comment::inc.comment-section',['commentUrl'=>route('dashboard.workers.comment',$item->id)])
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
