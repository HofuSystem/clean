@extends('admin::layouts.dashboard')
@section('content')
    <div class="container-fluid flex-grow-1 container-p-y">
        <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
            <div data-kt-swapper="true" data-kt-swapper-mode="prepend"
                data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}"
                class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
                <h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1">{{ trans($title) }}</h1>
                <span class="h-20px border-gray-200 border-start mx-4"></span>
                <ul class="breadcrumb breadcrumb-separatorless fw-bold fs-7 my-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard.index') }}" class="text-muted text-hover-primary">@lang('Home')</a>
                    </li>
                    <li class="breadcrumb-item text-muted">@lang('Gifts')</li>
                    <li class="breadcrumb-item text-dark">{{ trans($title) }}</li>
                </ul>
            </div>
        </div>

        <div class="container-fluid">
            <div class="card">
                <div class="card-header row">
                    <div class="card-title col-md-6">
                        <div class="form-group d-flex justify-content-center">
                            <label class="text-dark fw-bold" for="visible_cols"> @lang('visible cols')</label>
                            <select class="form-control mx-3" data-control="select2" name="visible_cols"
                                id="visible_cols" multiple></select>
                        </div>
                    </div>
                    <div class="card-toolbar col-md-6">
                        <div data-kt-user-table-toolbar="base">
                            <div class="d-flex justify-content-between">
                                <div class="d-flex">
                                    <div class="border border-dashed border-success text-success rounded mx-1 p-2">
                                        <a href="{{ route('dashboard.gifts.index') }}">
                                            <div class="fw-bolder fs-5 text-success">
                                                {{ $total }}
                                                <i class="fas fa-list-alt"></i>
                                                @lang('total')
                                            </div>
                                        </a>
                                    </div>
                                    <div class="border border-dashed border-danger text-danger rounded mx-1 p-2">
                                        <a href="{{ route('dashboard.gifts.index', ['trash' => 1]) }}">
                                            <div class="fw-bolder fs-5 text-danger">
                                                {{ $trash }}
                                                <i class="fas fa-trash-alt"></i>
                                                @lang('Trash')
                                            </div>
                                        </a>
                                    </div>
                                </div>
                                <div class="d-flex flex-wrap">
                                    <a href="{{ route('dashboard.gifts.create') }}" class="btn-operation">
                                        <i class="fas fa-plus-circle"></i>
                                        <span>@lang('create new')</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <!--begin::Group actions-->
                        <div class="d-flex justify-content-end align-items-center d-none" data-kt-user-table-toolbar="selected">
                            <div class="border border-warning border-dashed rounded text-warning p-2 mx-1">
                                <span class="me-2" data-kt-user-table-select="selected_count"></span>@lang('Selected')
                            </div>
                            <button type="button" class="btn btn-primary" data-kt-user-table-select="delete_selected">@lang('Delete Selected')</button>
                        </div>
                        <!--end::Group actions-->
                    </div>
                </div>

                <div class="p-1 row d-none" data-kt-user-table-filter="form">
                    <button type="reset" data-kt-user-table-filter="reset"></button>
                    <button type="submit" data-kt-user-table-filter="filter"></button>
                </div>


                <div class="card-body pt-0 table-responsive ">
                    <table class="table align-middle text-center table-row-dashed fs-6 gy-5" id="view-datatable"
                        data-load="{{ route('dashboard.gifts.index', ['trash' => request()->trash]) }}">
                        <thead class="table-primary">
                            <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                <th class="w-10px pe-2" data-name="select_switch">
                                    <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                        <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#view-datatable .form-check-input" value="1">
                                    </div>
                                </th>
                                <th class="text-center p-0" data-name="id">@lang('id')</th>
                                <th class="text-center p-0" data-name="title">@lang('title')</th>
                                <th class="text-center p-0" data-name="coupon_code">@lang('coupon code')</th>
                                <th class="text-center p-0" data-name="order_type">@lang('order type')</th>
                                <th class="text-center p-0" data-name="from">@lang('from')</th>
                                <th class="text-center p-0" data-name="to">@lang('to')</th>
                                <th class="text-center p-0" data-name="value">@lang('value')</th>
                                <th class="text-center p-0" data-name="type">@lang('type')</th>
                                <th class="text-center p-0" data-name="status">@lang('status')</th>

                                <th class="text-center p-0" data-name="actions">@lang('Actions')</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 fw-bold"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('js')
<script>
    var deleteUrl = "{{ route('dashboard.gifts.delete', ['id'=>'%s','trash'=>request()->trash]) }}"
</script>
@endpush
