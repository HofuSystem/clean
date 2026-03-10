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
                            <a href="{{ route('dashboard.index') }}"
                                class="text-muted text-hover-primary">@lang('Home')</a>
                        </li>
                        <!--end::Item-->
                        
                     
                        <!--begin::Item-->
                        <li class="breadcrumb-item text-muted">@lang('entities')</li>
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
                  
                    <form class="form" method="POST" id="operation-form"  redirect-to="{{route("dashboard.entities.index")}}" data-id="{{$item->id}}"
                        @isset($item->id) 
                            action="{{ route("dashboard.entities.edit",$item->id) }}"
                            data-mode="edit"
                        @else
                            action="{{ route("dashboard.entities.create",$item->id) }}"
                            data-mode="new"
                        @endif
                        >
                      
                        @csrf
                        <div class="card-body row">
                        
                          

                            <div class="form-group mb-3 col-md-6">
                                <label for="name">@lang("name")</label>
                                <input type="text" name="name" class="form-control "
                                    placeholder="@lang("Enter name") " value="{{ old("name" , $item->name ?? null) }}">
                                    
                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <label for="table">@lang("table")</label>
                                <input type="text" name="table" class="form-control "
                                    placeholder="@lang("Enter table") " value="{{ old("table" , $item->table ?? null) }}">
                                    
                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <label for="operations">@lang("operations")</label>
                                <input type="text" name="operations" class="form-control "
                                    placeholder="@lang("Enter operations") " value="{{ old("operations" , $item->operations ?? null) }}">
                                    
                            </div>

                            <div class="form-group mb-3 col-md-6">
                                <label for="package">@lang("package")</label>
                                <input type="text" name="package" class="form-control "
                                    placeholder="@lang("Enter package") " value="{{ old("package" , $item->package ?? null) }}">
                                    
                            </div>

                            <div class="form-group mb-3 col-md-6">
                                <label for="model">@lang("model")</label>
                                <input type="text" name="model" class="form-control "
                                    placeholder="@lang("Enter model") " value="{{ old("model" , $item->model ?? null) }}">
                                    
                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <label for="table_fields">@lang("table_fields")</label>
                                <input type="text" name="table_fields" class="form-control "
                                    placeholder="@lang("Enter table_fields") " value="{{ old("table_fields" , $item->table_fields ?? null) }}">
                                    
                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <label for="table_filters">@lang("table_filters")</label>
                                <input type="text" name="table_filters" class="form-control "
                                    placeholder="@lang("Enter table_filters") " value="{{ old("table_filters" , $item->table_filters ?? null) }}">
                                    
                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <label for="api_fields_list">@lang("api_fields_list")</label>
                                <input type="text" name="api_fields_list" class="form-control "
                                    placeholder="@lang("Enter api_fields_list") " value="{{ old("api_fields_list" , $item->api_fields_list ?? null) }}">
                                    
                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <label for="api_fields_single">@lang("api_fields_single")</label>
                                <input type="text" name="api_fields_single" class="form-control "
                                    placeholder="@lang("Enter api_fields_single") " value="{{ old("api_fields_single" , $item->api_fields_single ?? null) }}">
                                    
                            </div>

                        </div>
                        <div class="card-footer">
                            <div class="row">
                                <div class="col-lg-9 ml-lg-auto">
                                    <button type="submit" class="btn btn-primary font-weight-bold mr-2">@lang("Submit")</button>
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
    <style>
        .myDragClass{
        background:#00fa1f !important;
        transform: scale(1.05); 
        }
        .myDragClass td{
            background:#00fa1f !important;
        }
    </style>
@endpush
@push('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/TableDnD/1.0.5/jquery.tablednd.js" ></script>

    <script src="{{ asset('control') }}/js/custom/crud/form.js"></script>
    <script>

    </script>
@endpush
