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
                        <li class="breadcrumb-item text-muted">@lang("notification")</li>
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
                <div class="card">
                    
                    <form class="form" method="POST" id="operation-form"  redirect-to="{{route("dashboard.banner-notifications.index")}}" data-id="{{$item->id ?? null}}"
                        @isset($item)
                            action="{{ route("dashboard.banner-notifications.edit",$item->id) }}"
                            data-mode="edit"
                        @else
                            action="{{ route("dashboard.banner-notifications.create") }}"
                            data-mode="new"
                        @endisset
                        >

                        @csrf
                        <div class="card-body row">
                        
                            <div class="form-group mb-3 col-md-12">
                                <label class="" for="image">{{ trans("image") }}</label>
                                <div class="media-center-group form-control" data-max="1" data-type="image">
                                    <input type="text" hidden="hidden" class="form-control" name="image" value="{{ old("image" , $item->image ?? null) }}">
                                    <button  type="button" class="btn btn-secondary media-center-load" style="margin-top: 10px;"><i class="fa fa-file-upload"></i></button>
                                    <div class="input-gallery"></div>
                                </div>
                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <label class="required" for="publish_date">{{ trans("publish date") }}</label>
                                <input type="date" name="publish_date" class="form-control "
                                    placeholder="{{ trans("Enter publish date") }} " value="@isset($item){{ $item->publish_date }}@endisset">
                                    
                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <label class="" for="expired_date">{{ trans("expired date") }}</label>
                                <input type="date" name="expired_date" class="form-control "
                                    placeholder="{{ trans("Enter expired date") }} " value="@isset($item){{ $item->expired_date }}@endisset">
                                    
                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <label class="required" for="next_vision_hour">{{ trans("next vision hour") }}</label>
                                <input type="number" name="next_vision_hour" class="form-control "
                                    placeholder="{{ trans("Enter next vision hour") }} " value="{{ old("next_vision_hour" , $item->next_vision_hour ?? null) }}">
                                    
                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <label class="required" for="status">{{ trans("status") }}</label>
                                <select class="custom-select  form-select advance-select" name="status" id="status"  >
                                    
                <option  value="" >{{trans("select status")}}</option>
            <option value="active" @selected(isset($item) and $item->status == "active" ) >{{trans("active")}}</option>
                                    <option value="not-active" @selected(isset($item) and $item->status == "not-active" ) >{{trans("not active")}}</option>
                                    
                                </select>
                                            
                            </div>

                        </div>
                        <div class="card-footer">
                            <div class="row">
                                <div class="col-lg-9 ml-lg-auto">
                                    <button type="submit" class="btn btn-primary font-weight-bold mr-2">{{ trans('save') }}</button>
                                </div>
                            </div>
                        </div>
                    </form>
        
                </div>
                <!--end::Card-->
            </div>
            <!--end::Container-->
        </div>
        <!--end::Post-->
    </div>
    <!--end::Content-->
    @include('media::mediaCenter.modal')
@endsection
@push('css')
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <link href="{{ asset('control') }}/js/custom/crud/form.css" rel="stylesheet" type="text/css" />
@endpush
@push('js')
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/TableDnD/1.0.5/jquery.tablednd.js"></script>
    <script src="{{ asset('control') }}/js/custom/crud/form.js"></script>
@endpush
