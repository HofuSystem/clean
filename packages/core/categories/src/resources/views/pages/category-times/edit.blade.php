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
                        <li class="breadcrumb-item text-muted">@lang("categories")</li>
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
                    
                    <form class="form" method="POST" id="operation-form"  redirect-to="{{route("dashboard.category-times.index")}}" data-id="{{$item->id ?? null}}"
                        @isset($item)
                            action="{{ route("dashboard.category-times.edit",$item->id) }}"
                            data-mode="edit"
                        @else
                            action="{{ route("dashboard.category-times.create") }}"
                            data-mode="new"
                        @endisset
                        >

                        @csrf
                        <div class="card-body row">
                        
                            <div class="form-group mb-3 col-md-6">
                                <label class="required" for="category_id">{{ trans("category") }}</label>
                                <select class="custom-select  form-select advance-select" name="category_id" id="category_id"  >
                                    
                                    <option   value="" >{{trans("select category")}}</option>
                                    @foreach($categories ?? [] as $sItem)
                                        <option data-id="{{$sItem->id }}" @selected(isset($item)  and $item->category_id == $sItem->id) value="{{$sItem->id }}" >{{$sItem->name}}</option>
                                    @endforeach
            
                                </select>
                                            
                            </div>

                            <div class="form-group mb-3 col-md-6">
                                <label class="required" for="day">{{ trans("day") }}</label>
                                <select class="custom-select  form-select advance-select" name="day" id="day"  >
                                    
                <option  value="" >{{trans("select day")}}</option>
            <option value="saturday" @selected(isset($item) and $item->day == "saturday" ) >{{trans("saturday")}}</option>
                                    <option value="sunday" @selected(isset($item) and $item->day == "sunday" ) >{{trans("sunday")}}</option>
                                    <option value="monday" @selected(isset($item) and $item->day == "monday" ) >{{trans("monday")}}</option>
                                    <option value="tuesday" @selected(isset($item) and $item->day == "tuesday" ) >{{trans("tuesday")}}</option>
                                    <option value="wednesday" @selected(isset($item) and $item->day == "wednesday" ) >{{trans("wednesday")}}</option>
                                    <option value="thursday" @selected(isset($item) and $item->day == "thursday" ) >{{trans("thursday")}}</option>
                                    <option value="friday" @selected(isset($item) and $item->day == "friday" ) >{{trans("friday")}}</option>
                                    
                                </select>
                                            
                            </div>

                            <div class="form-group mb-3 col-md-6">
                                <label class="required" for="from">{{ trans("from") }}</label>
                                <input type="time" name="from" class="form-control "
                                    placeholder="{{ trans("Enter from") }} " value="@isset($item){{ $item->from }}@endisset">
                                    
                            </div>

                            <div class="form-group mb-3 col-md-6">
                                <label class="required" for="to">{{ trans("to") }}</label>
                                <input type="time" name="to" class="form-control "
                                    placeholder="{{ trans("Enter to") }} " value="@isset($item){{ $item->to }}@endisset">
                                    
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
