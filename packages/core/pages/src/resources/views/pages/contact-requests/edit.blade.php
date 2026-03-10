
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
                        <li class="breadcrumb-item text-muted">@lang("pages")</li>
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

                    
                    <form class="form" method="POST" id="operation-form" data-id="{{$item->id ?? null}}"
                        @if(isset($item))
                            action="{{ route("dashboard.contact-requests.edit",$item->id) }}"
                            data-mode="edit"
                        @else
                            action="{{ route("dashboard.contact-requests.create") }}"
                            data-mode="new"
                        @endif
                        >

                        @csrf
                        <div class="card-body row">
                        
                            <div class="form-group mb-3 col-md-12">
                                <label class="required" for="name">{{ trans("name") }}</label>
                                <input type="text" name="name" class="form-control "
                                    placeholder="{{ trans("Enter name") }} " value="@if(isset($item)){{ $item->name }}@endif">
                                    
                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <label class="required" for="phone">{{ trans("phone") }}</label>
                                <input type="text" name="phone" class="form-control "
                                    placeholder="{{ trans("Enter phone") }} " value="@if(isset($item)){{ $item->phone }}@endif">
                                    
                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <label class="" for="email">{{ trans("email") }}</label>
                                <input type="email" name="email" class="form-control "
                                    placeholder="{{ trans("Enter email") }} " value="{{ old("email" , $item->email ?? null) }}">
                                    
                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <label class="" for="service_id">{{ trans("service") }}</label>
                                <select class="custom-select  form-select advance-select" name="service_id" id="service_id"  >
                                    
                                    <option   value="" >{{trans("select")." ".trans("service")}}</option>
                                    @foreach($services ?? [] as $sItem)
                                        <option data-id="{{$sItem->id }}" @selected(isset($item)  and $item->service_id == $sItem->id) value="{{$sItem->id }}" >{{$sItem->title}}</option>
                                    @endforeach
            
                                </select>
                                            
                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <label class="" for="date">{{ trans("date") }}</label>
                                <input type="date" name="date" class="form-control "
                                     " value="@if(isset($item)){{ $item->date }}@endif">
                                    
                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <label class="" for="time">{{ trans("time") }}</label>
                                <input type="time" name="time" class="form-control "
                                     value="@if(isset($item)){{ $item->time }}@endif">
                                    
                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <label class="" for="notes">{{ trans("notes") }}</label>
                <textarea type="number" name="notes" class="form-control "
                    placeholder="{{ trans("Enter notes") }} " >@if(isset($item)){{ $item->notes }}@endif</textarea>
                    
                            </div>

                        </div>
                        <div class="card-footer">
                            <div class="row">
                                <div class="col-lg-9 ml-lg-auto">
                                    <button type="submit" class="btn btn-primary font-weight-bold mr-2">{{ trans("Submit") }}</button>
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