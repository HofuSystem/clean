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
                <div class="card">
                    
                    <form class="form" method="POST" id="operation-form"  redirect-to="{{route("dashboard.bans.index")}}" data-id="{{$item->id ?? null}}"
                        @isset($item)
                            action="{{ route("dashboard.bans.edit",$item->id) }}"
                            data-mode="edit"
                        @else
                            action="{{ route("dashboard.bans.create") }}"
                            data-mode="new"
                        @endisset
                        >

                        @csrf
                        <div class="card-body row">
                        
                            <div class="form-group mb-3 col-md-6">
                                <label class="required" for="level">{{ trans("level") }}</label>
                                <select class="custom-select  form-select advance-select" name="level" id="level"  >
                                    
                <option  value="" >{{trans("select level")}}</option>
            <option value="ip" @selected(isset($item) and $item->level == "ip" ) >{{trans("ip")}}</option>
                                    <option value="device" @selected(isset($item) and $item->level == "device" ) >{{trans("device")}}</option>
                                    <option value="user" @selected(isset($item) and $item->level == "user" ) >{{trans("user")}}</option>
                                    
                                </select>
                                            
                            </div>

                            <div class="form-group mb-3 col-md-6">
                                <label class="required" for="value">{{ trans("value") }}</label>
                                <input type="text" name="value" class="form-control "
                                    placeholder="{{ trans("Enter value") }} " value="@isset($item){{ $item->value }}@endisset">
                                    
                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <label class="" for="admin_id">{{ trans("admin") }}</label>
                                <select class="custom-select  form-select advance-select" name="admin_id" id="admin_id"  >
                                    
                                    <option   value="" >{{trans("select admin")}}</option>
                                    @foreach($admins ?? [] as $sItem)
                                        <option data-id="{{$sItem->id }}" @selected(isset($item)  and $item->admin_id == $sItem->id) value="{{$sItem->id }}" >{{$sItem->fullname}}</option>
                                    @endforeach
            
                                </select>
                                            
                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <label class="required" for="reason">{{ trans("reason") }}</label>
                <textarea type="number" name="reason" class="form-control "
                    placeholder="{{ trans("Enter reason") }} " >@isset($item){{ $item->reason }}@endisset</textarea>
                    
                            </div>

                            <div class="form-group mb-3 col-md-6">
                                <label class="" for="from">{{ trans("starts") }}</label>
                                <input type="datetime-local" name="from" class="form-control "
                                    placeholder="{{ trans("Enter starts") }} " value="@isset($item){{ $item->from }}@endisset">
                                    
                            </div>

                            <div class="form-group mb-3 col-md-6">
                                <label class="" for="to">{{ trans("ends") }}</label>
                                <input type="datetime-local" name="to" class="form-control "
                                    placeholder="{{ trans("Enter ends") }} " value="@isset($item){{ $item->to }}@endisset">
                                    
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
