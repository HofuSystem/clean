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
                    <!--begin::Card header-->
                    <div class="card-header border-0 pt-6 row">
                        <!--begin::Card title-->
                        <div class="card-title m-0 col-md-6 ">
                           <!--begin::cols-->
                           <div class="form-group mx-5 d-flex justify-content-center">
                            <label class="text-primary fw-bold" for="visible_cols"> @lang('visible cols')</label>
                            <select class="form-control mx-3" data-control="select2" name="visible_cols"
                                id="visible_cols" multiple></select>
                        </div>
                        <!--end::cols-->
                        </div>
                        <!--begin::Card title-->
                        <!--begin::Card toolbar-->
                        <div class="card-toolbar m-0 col-md-6">
                            <!--begin::Toolbar-->
                            
                            <div class="w-100" data-kt-user-table-toolbar="base">
                                <div class="row mb-2">
                                    <!--begin::Stat-->
                                    <div
                                        class="col-6 border border-gray-300 border-dashed rounded min-w-125px py-3 px-4">
                                        <!--begin::Number-->
                                        <div class="d-flex align-items-center">
                                            <!--begin::Svg Icon | path: icons/duotune/arrows/arr066.svg-->
                                            <span class="svg-icon svg-icon-3 svg-icon-success me-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none">
                                                    <rect opacity="0.5" x="13" y="6" width="13" height="2"
                                                        rx="1" transform="rotate(90 13 6)" fill="black" />
                                                    <path
                                                        d="M12.5657 8.56569L16.75 12.75C17.1642 13.1642 17.8358 13.1642 18.25 12.75C18.6642 12.3358 18.6642 11.6642 18.25 11.25L12.7071 5.70711C12.3166 5.31658 11.6834 5.31658 11.2929 5.70711L5.75 11.25C5.33579 11.6642 5.33579 12.3358 5.75 12.75C6.16421 13.1642 6.83579 13.1642 7.25 12.75L11.4343 8.56569C11.7467 8.25327 12.2533 8.25327 12.5657 8.56569Z"
                                                        fill="black" />
                                                </svg>
                                            </span>
                                            <!--end::Svg Icon-->
                                            <div class="fs-2 fw-bolder" data-kt-countup="true"
                                                data-kt-countup-value="{{ $total }}">0</div>
                                        </div>
                                        <!--end::Number-->
                                        <!--begin::Label-->
                                        <div class="fw-bold fs-6 text-gray-400">@lang('Total')</div>
                                        <!--end::Label-->
                                    </div>
                                    <!--end::Stat-->
                                    <!--begin::Stat-->
                                    <div
                                        class="col-6 border border-gray-300 border-dashed rounded min-w-125px py-3 px-4">
                                        <!--begin::Number-->
                                        <div class="d-flex align-items-center">
                                            <!--begin::Svg Icon | path: icons/duotune/arrows/arr065.svg-->
                                            <span class="svg-icon svg-icon-3 svg-icon-danger me-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none">
                                                    <rect opacity="0.5" x="11" y="18" width="13" height="2"
                                                        rx="1" transform="rotate(-90 11 18)" fill="black" />
                                                    <path
                                                        d="M11.4343 15.4343L7.25 11.25C6.83579 10.8358 6.16421 10.8358 5.75 11.25C5.33579 11.6642 5.33579 12.3358 5.75 12.75L11.2929 18.2929C11.6834 18.6834 12.3166 18.6834 12.7071 18.2929L18.25 12.75C18.6642 12.3358 18.6642 11.6642 18.25 11.25C17.8358 10.8358 17.1642 10.8358 16.75 11.25L12.5657 15.4343C12.2533 15.7467 11.7467 15.7467 11.4343 15.4343Z"
                                                        fill="black" />
                                                </svg>
                                            </span>
                                            <!--end::Svg Icon-->
                                            <div class="fs-2 fw-bolder" data-kt-countup="true"
                                                data-kt-countup-value="{{ $trash }}">0</div>
                                        </div>
                                        <!--end::Number-->
                                        <!--begin::Label-->
                                        <div class="fw-bold fs-6 text-gray-400">@lang('Trash')</div>
                                        <!--end::Label-->
                                    </div>
                                    <!--end::Stat-->
                                </div>
                                <div class="row">

                                    @can('dashboard.category-times.export')
                                        <a href="{{ route('dashboard.category-times.export') }}" id="export" type="button"
                                             class="btn btn-light-primary col-4">
                                            <i class="fas fa-upload"></i>
                                            @lang('Export Report') 
                                        </a>
                                    @endcan
                                    @can('dashboard.category-times.import')
                                    <a href="{{ route('dashboard.category-times.import') }}"
                                        class="btn btn-secondary align-middle col-4">
                                        <!--begin::Svg Icon | path: icons/duotune/arrows/arr075.svg-->
                                        <i class="fas fa-file-import"></i>
                                        <!--end::Svg Icon-->@lang('import list')
                                    </a>
                                    @endcan
                                    <!--begin::Add -->
                                    @can('dashboard.category-times.create')
                                        <a href="{{ route('dashboard.category-times.create') }}"
                                            class="btn btn-success align-middle col-4">
                                            <!--begin::Svg Icon | path: icons/duotune/arrows/arr075.svg-->
                                            <i class="fas fa-plus-circle"></i>
                                            <!--end::Svg Icon-->@lang('create new')
                                        </a>
                                    @endcan
                                  
                                    <!--end::Add -->
                                </div>
                               
                            </div>
                            <!--end::Toolbar-->
                            <!--begin::Group actions-->
                            <div class="d-flex justify-content-end align-items-center d-none"
                                data-kt-user-table-toolbar="selected">
                                <div class="fw-bolder me-5">
                                    <span class="me-2"
                                        data-kt-user-table-select="selected_count"></span>@lang('Selected')
                                </div>
                                <button type="button" class="btn btn-danger"
                                    data-kt-user-table-select="delete_selected">@lang('Delete Selected')</button>
                            </div>
                            <!--end::Group actions-->
                        </div>
                        
                        <!--end::Card toolbar-->
                    </div>
                   
                    <!--begin::Content-->
                    <div class="container-fluid mt-5">
                        <button class="btn btn-primary mb-3" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                            <i class="fas fa-filter"></i>
                            {{ trans('open filters of data') }}
                        </button>
                    
                        <div class="accordion" id="accordionExample">
                            <div class="accordion-item">
                                <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                   
                                    <div class="px-7 py-5 row" data-kt-user-table-filter="form">
                                        
                                        <div class="col-md-6 mb-1">
                                            <label for="category_id">@lang("category")</label>
                                            <select class="custom-select filter-input form-select advance-select" name="category_id" id="category_id">
                                                
                                                <option  value="" > @lang("select categories")</option>
                                                @foreach($categories as $item)
                                                    <option value="{{$item->id }}" @selected($item->id  == request("category_id")) >@lang($item->name)</option>
                                                @endforeach
            
                                            </select>
                                        </div>
                                    
                                        <div class="col-md-6 mb-1">
                                            <label for="day">@lang("day")</label>
                                            <select class="custom-select filter-input form-select advance-select" name="day" id="day">
                                                <option value=""> @lang("select day")</option><option value="saturday" @selected("saturday" == request("day")) >{{trans("saturday")}}</option><option value="sunday" @selected("sunday" == request("day")) >{{trans("sunday")}}</option><option value="monday" @selected("monday" == request("day")) >{{trans("monday")}}</option><option value="tuesday" @selected("tuesday" == request("day")) >{{trans("tuesday")}}</option><option value="wednesday" @selected("wednesday" == request("day")) >{{trans("wednesday")}}</option><option value="thursday" @selected("thursday" == request("day")) >{{trans("thursday")}}</option><option value="friday" @selected("friday" == request("day")) >{{trans("friday")}}</option>
                                            </select>
                                        </div>
                                    
                                        <div class="col-md-6 mb-1">
                                        <label for="from"> @lang("from from") </label>
                                        <input type="time" name="from_from" class="form-control filter-input"
                                            placeholder="@lang("search for from") " value="{{ request("from") }}">
                                            </div>
                                            <div class="col-md-6 mb-1">
                                            <label for="from"> @lang("from to") </label>
                                            <input type="time" name="to_from" class="form-control filter-input"
                                                placeholder="@lang("search for from") " value="{{ request("from") }}">
                                        </div>
                                        <div class="col-md-6 mb-1">
                                        <label for="to"> @lang("to from") </label>
                                        <input type="time" name="from_to" class="form-control filter-input"
                                            placeholder="@lang("search for to") " value="{{ request("to") }}">
                                            </div>
                                            <div class="col-md-6 mb-1">
                                            <label for="to"> @lang("to to") </label>
                                            <input type="time" name="to_to" class="form-control filter-input"
                                                placeholder="@lang("search for to") " value="{{ request("to") }}">
                                        </div>
                                        <div class="col-md-6 mb-1">
                                        <label for="created_at"> @lang("Create Date from") </label>
                                        <input type="datetime-local" name="from_created_at" class="form-control filter-input"
                                            placeholder="@lang("search for Create Date") " value="{{ request("created_at") }}">
                                            </div>
                                            <div class="col-md-6 mb-1">
                                            <label for="created_at"> @lang("Create Date to") </label>
                                            <input type="datetime-local" name="to_created_at" class="form-control filter-input"
                                                placeholder="@lang("search for Create Date") " value="{{ request("created_at") }}">
                                        </div>
                                        
                                        <!--begin::Actions-->
                                        <div class=" d-flex justify-content-end">
                                            <button type="reset"
                                                class="btn btn-light btn-active-light-primary fw-bold me-2 px-6"
                                                data-kt-menu-dismiss="true"
                                                data-kt-user-table-filter="reset">@lang('Reset')</button>
                                            <button type="submit" class="btn btn-primary fw-bold px-6"
                                                data-kt-menu-dismiss="true"
                                                data-kt-user-table-filter="filter">@lang('Apply')</button>
                                        </div>
                                        <!--end::Actions-->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                
                    <!--end::Content-->
             
                    <!--end::Card header-->
                    <!--begin::Card body-->
                    <div class="card-body pt-0 table-responsive">
                        <!--begin::Table-->
                        <table class="table align-middle text-center table-row-dashed fs-6 gy-5" id="view-datatable"
                                data-load="{{ route('dashboard.category-times.index',['trash' => request()->trash]) }}">
                            <!--begin::Table head-->
                            <thead>
                                <!--begin::Table row-->
                                <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">


                                    
                                    <th class="w-10px pe-2" data-name="select_switch">
                                        <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                        <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#view-datatable .form-check-input" value="1">
                                        </div>
                                    </th>
                                    <th class="text-center p-2" data-name="id">@lang("id")</th>

                                    
                                    <th class="text-center p-2" data-name="category_id">@lang("category")</th>
                                    <th class="text-center p-2" data-name="day">@lang("day")</th>
                                    <th class="text-center p-2" data-name="from">@lang("from")</th>
                                    <th class="text-center p-2" data-name="to">@lang("to")</th>
                                    <th class="text-center p-2" data-name="actions">@lang("Actions")</th>

                                </tr>
                                <!--end::Table row-->
                            </thead>
                            <!--end::Table head-->
                            <!--begin::Table body-->
                            <tbody class="text-gray-600 fw-bold">

                            </tbody>
                            <!--end::Table body-->
                        </table>
                        <!--end::Table-->
                    </div>
                    <!--end::Card body-->
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
    
    
    
   
@endpush
@push('js')
    <script>
        var deleteUrl = "{{ route('dashboard.category-times.delete', ['id'=>'%s','trash'=>request()->trash]) }}"
    </script>
    
@endpush

