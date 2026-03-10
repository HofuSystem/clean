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
                            
                            <div class="d-flex justify-content-end  w-100" data-kt-user-table-toolbar="base">
                                <!--begin::Export dropdown-->
                                <button type="button" class="btn btn-light-primary" data-kt-menu-trigger="click"
                                    data-kt-menu-placement="bottom-end">
                                    <i class="ki-duotone ki-exit-down fs-2"><span class="path1"></span><span
                                            class="path2"></span></i>
                                    @lang('Export Report')
                                </button>
                                <!--begin::Menu-->
                                <div id="kt_datatable_example_export_menu"
                                    class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-200px py-4"
                                    data-kt-menu="true">
                                    <!--begin::Menu item-->
                                    <div class="menu-item px-3">
                                        <a href="#" class="menu-link px-3" data-kt-export="copy">
                                            @lang('Copy to clipboard')
                                        </a>
                                    </div>
                                    <!--end::Menu item-->
                                    <!--begin::Menu item-->
                                    <div class="menu-item px-3">
                                        <a href="#" class="menu-link px-3" data-kt-export="excel">
                                            @lang('Export as Excel') 
                                        </a>
                                    </div>
                                    <!--end::Menu item-->
                                    <!--begin::Menu item-->
                                    <div class="menu-item px-3">
                                        <a href="#" class="menu-link px-3" data-kt-export="csv">
                                            @lang('Export as CSV')
                                        </a>
                                    </div>
                                    <!--end::Menu item-->
                                    <!--begin::Menu item-->
                                    <div class="menu-item px-3">
                                        <a href="#" class="menu-link px-3" data-kt-export="pdf">
                                            @lang('Export as PDF')
                                        </a>
                                    </div>
                                    <!--end::Menu item-->
                                </div>
                                <!--end::Menu-->
                                <!--end::Export dropdown-->

                                <!--begin::Hide default export buttons-->
                                <div id="kt_datatable_example_buttons" class="d-none"></div>
                                <!--end::Hide default export buttons-->
                                <!--begin::Add {{ $title }}-->
                                <a href="{{ route('dashboard.order-invoices.create') }}"
                                    class="btn btn-primary me-3 align-middle">
                                    <!--begin::Svg Icon | path: icons/duotune/arrows/arr075.svg-->
                                    <span class="svg-icon svg-icon-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none">
                                            <rect opacity="0.5" x="11.364" y="20.364" width="16" height="2"
                                                rx="1" transform="rotate(-90 11.364 20.364)" fill="black" />
                                            <rect x="4.36396" y="11.364" width="16" height="2" rx="1"
                                                fill="black" />
                                        </svg>
                                    </span>
                                    <!--end::Svg Icon-->@lang('create new')
                                </a>
                                <!--end::Add {{ $title }}-->
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
                            {{ trans('open filters of data') }}
                        </button>
                    
                        <div class="accordion" id="accordionExample">
                            <div class="accordion-item">
                                <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                   
                                    <div class="px-7 py-5 row" data-kt-user-table-filter="form">
                                        
                                        <div class="col-md-6 mb-1">
                                            <label for="invoice_num"> @lang("invoice num") </label>
                                            <input type="text" name="invoice_num" class="form-control filter-input"
                                                placeholder="@lang("search for invoice num") " value="{{ request("invoice_num") }}">
                                        </div>
                                        <div class="col-md-6 mb-1">
                                        <label for="data"> @lang("data") </label>
                                        <input type="number" name="data" class="form-control filter-input"
                                            placeholder="@lang("search for data") " value="{{ request("data") }}">
                                        </div>
                                        <div class="col-md-6 mb-1">
                                            <label for="order_id">@lang("order")</label>
                                            <select class="custom-select filter-input form-select advance-select" name="order_id" id="order_id">
                                                
                                                <option > @lang("select orders")</option>
                                                @foreach($orders as $item)
                                                    <option value="{{$item->id }}" @selected($item->value == request("order_id")) >@lang($item->reference_id)</option>
                                                @endforeach
            
                                            </select>
                                        </div>
                                    
                                        <div class="col-md-6 mb-1">
                                            <label for="user_id">@lang("user")</label>
                                            <select class="custom-select filter-input form-select advance-select" name="user_id" id="user_id">
                                                
                                                <option > @lang("select users")</option>
                                                @foreach($users as $item)
                                                    <option value="{{$item->id }}" @selected($item->value == request("user_id")) >@lang($item->fullname)</option>
                                                @endforeach
            
                                            </select>
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
                            data-load="{{ route('dashboard.order-invoices.index',['trash' => request()->trash]) }}">
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

                                    
                                    <th class="text-center p-2" data-name="invoice_num">@lang("invoice num")</th>
                                    <th class="text-center p-2" data-name="order_id">@lang("order")</th>
                                    <th class="text-center p-2" data-name="user_id">@lang("user")</th>
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
        var deleteUrl = "{{ route('dashboard.order-invocies.delete', ['id'=>'%s','trash'=>request()->trash]) }}"
    </script>
    
@endpush

