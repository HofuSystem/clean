@extends('admin::layouts.dashboard')
@section('content')
    <!--end::Header-->

    <!--begin::Content-->
    <div class="container-fluid flex-grow-1 container-p-y ">

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
                        <a href="{{ route('dashboard.index') }}" class="text-muted text-hover-primary">@lang('Home')</a>
                    </li>
                    <!--end::Item-->

                    <!--begin::Item-->
                    <li class="breadcrumb-item text-muted">@lang('product')</li>
                    <!--end::Item-->

                    <!--begin::Item-->
                    <li class="breadcrumb-item text-dark">{{ $title }}</li>
                    <!--end::Item-->
                </ul>
                <!--end::Breadcrumb-->
            </div>
            <!--end::Page title-->

        </div>
        <!--begin::Container-->
        <div class="container-fluid">
            <!--begin::Card-->
            <div class="card">
                <!--begin::Card header-->
                <div class="card-header row">

                    <!--begin::Card title-->
                    <!--begin::Card toolbar-->
                    <div class="card-toolbar col-md-6">
                        <!--begin::Toolbar-->
                        <div data-kt-user-table-toolbar="base">
                            <div class="d-flex justify-content-between">
                                <div class="d-flex">
                                    @can('dashboard.best_sales.export')
                                        <a href="{{ route('dashboard.products.best_sales.export') }}" id="export"
                                            type="button" class="btn-operation ">
                                            <i class="fas fa-upload"></i>
                                            <span>
                                                @lang('Export Report')
                                            </span>
                                        </a>
                                    @endcan
                                </div>

                            </div>
                        </div>
                        <!--end::Toolbar-->
                        <!--begin::Group actions-->
                        <div class="d-flex justify-content-end align-items-center d-none"
                            data-kt-user-table-toolbar="selected">
                            <div class="border border-warning border-dashed rounded text-warning  p-2 mx-1">
                                <span class="me-2" data-kt-user-table-select="selected_count"></span>@lang('Selected')
                            </div>
                            <button type="button" class="btn btn-primary"
                                data-kt-user-table-select="delete_selected">@lang('Delete Selected')</button>
                        </div>
                        <!--end::Group actions-->
                    </div>

                    <!--end::Card toolbar-->
                </div>

                <!--begin::Content-->
                <div class="container-fluid mt-1">
                    <button class="btn btn-primary mb-1" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                        <i class="fas fa-filter"></i>
                        {{ trans('open filters of data') }}
                    </button>
                    <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link  sale-switch active" id="pills-all-best-sales-products-tab" data-bs-toggle="pill"
                                data-bs-target="#pills-best-sales-products" type="button" role="tab" aria-controls="pills-best-sales-products"
                                aria-selected="true" data-type="sales-products">{{ trans('best sales products') }} </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link  sale-switch" id="pills-best-sale-package-products-tab" data-bs-toggle="pill"
                                data-bs-target="#pills-best-sale-package-products" type="button" role="tab" aria-controls="pills-best-sale-package-products"
                                aria-selected="true" data-type="sales-package-products">{{ trans('best sale package products') }}</button>
                        </li>
                    </ul>

                    <div class="accordion" id="accordionExample">
                        <div class="accordion-item">
                            <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne"
                                data-bs-parent="#accordionExample">
                                <div class="p-1 row" data-kt-user-table-filter="form">
                                    <div class="col-md-6 mb-1">
                                        <label for="from"> @lang('From Date') </label>
                                        <input type="datetime-local" name="from"
                                            class="form-control filter-input" placeholder="@lang('search for from Date') "
                                            value="{{ request('from') }}">
                                    </div>
                                    <div class="col-md-6 mb-1">
                                        <label for="to"> @lang('To Date') </label>
                                        <input type="datetime-local" name="to" class="form-control filter-input"
                                            placeholder="@lang('search for to Date') " value="{{ request('to') }}">
                                    </div>
                                    <input type="hidden" name="type-filter" class="filter-input" id="type-filter">
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
                <div class="card-body pt-0 table-responsive table-responsive">
                    <!--begin::Table-->
                    <table class="table align-middle text-center table-row-dashed fs-6 gy-5" id="view-datatable"
                        data-load="{{ route('dashboard.products.index',['page'=>'best_sales']) }}">
                        <!--begin::Table head-->
                        <thead class="table-primary">
                            <!--begin::Table row-->
                            <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">

                                <th class="w-10px pe-2" data-name="select_switch">
                                    <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                        <input class="form-check-input" type="checkbox" data-kt-check="true"
                                            data-kt-check-target="#view-datatable .form-check-input" value="1">
                                    </div>
                                </th>
                                <th class="text-center p-0" data-name="id">@lang('id')</th>
                                <th class="text-center p-0" data-name="image">@lang('image')</th>
                                <th class="text-center p-0" data-name="name">@lang('name')</th>
                                <th class="text-center p-0" data-name="sku">@lang('sku')</th>
                                <th class="text-center p-0" data-name="price">@lang('price')</th>
                                <th class="text-center p-0" data-name="category_id">@lang('category')</th>
                                <th class="text-center p-0" data-name="sub_category_id">@lang('sub category')</th>
                                <th class="text-center p-0" data-name="total_quantity">@lang('number_of_sales')</th>
                                <th class="text-center p-0" data-name="created_at">@lang('added_date')</th>
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
        <!--end::Post-->
    </div>
    <!--end::Content-->
@endsection
@push('css')
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <link href="{{ asset('control') }}/js/custom/crud/form.css" rel="stylesheet" type="text/css" />
@endpush
@push('js')
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/TableDnD/1.0.5/jquery.tablednd.js"></script>
    <script src="{{ asset('control') }}/js/custom/crud/form.js"></script>
    <script>
        $(document).ready(function() {
        $('.sale-switch').click(function() {
            let type = $(this).data('type');
            $('#type-filter').val(type);
            $('[data-kt-user-table-filter="filter"]').click()
        })
    });
</script>
@endpush
