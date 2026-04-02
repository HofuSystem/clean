@extends('admin::layouts.dashboard')
@section('content')
    <!--begin::Content-->
    <div class="container-fluid flex-grow-1 container-p-y">

        <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
            <!--begin::Page title-->
            <div data-kt-swapper="true" data-kt-swapper-mode="prepend"
                data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}"
                class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
                <h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1">{{ $title }}</h1>
                <span class="h-20px border-gray-200 border-start mx-4"></span>
                <ul class="breadcrumb breadcrumb-separatorless fw-bold fs-7 my-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="" class="text-muted text-hover-primary">@lang('Home')</a>
                    </li>
                    <li class="breadcrumb-item text-dark">{{ $title }}</li>
                </ul>
            </div>
            <!--end::Page title-->
        </div>

        <!--begin::Container-->
        <div class="container-fluid">
            <!--begin::Card-->
            <div class="card">
                <!--begin::Card header-->
                <div class="card-header row">
                    <div class="card-title col-md-6">
                        <div class="form-group d-flex justify-content-center">
                            <label class="text-dark fw-bold" for="visible_cols">@lang('visible cols')</label>
                            <select class="form-control mx-3" data-control="select2" name="visible_cols"
                                id="visible_cols" multiple></select>
                        </div>
                    </div>
                    <div class="card-toolbar col-md-6">
                        <div data-kt-user-table-toolbar="base">
                            <div class="d-flex justify-content-between">
                                <div class="d-flex">
                                    <div class="border border-dashed border-success text-success rounded mx-1 p-2">
                                        <a href="{{ route('dashboard.company-employees.index') }}">
                                            <div class="fw-bolder fs-5 text-success">
                                                {{ $total }}
                                                <i class="fas fa-list-alt text-success"></i>
                                                @lang('total')
                                            </div>
                                        </a>
                                    </div>
                                    <div class="border border-dashed border-danger text-danger rounded mx-1 p-2">
                                        <a href="{{ route('dashboard.company-employees.index', ['trash' => 1]) }}">
                                            <div class="fw-bolder fs-5 text-danger">
                                                {{ $trash }}
                                                <i class="fas fa-trash-alt text-danger"></i>
                                                @lang('Trash')
                                            </div>
                                        </a>
                                    </div>
                                </div>
                                <div class="d-flex">
                                    @can('dashboard.company-employees.create')
                                        <a href="{{ route('dashboard.company-employees.create') }}" class="btn-operation">
                                            <i class="fas fa-plus-circle"></i>
                                            <span>@lang('create new')</span>
                                        </a>
                                    @endcan
                                </div>
                            </div>
                        </div>
                        <!--begin::Group actions-->
                        <div class="d-flex justify-content-end align-items-center d-none"
                            data-kt-user-table-toolbar="selected">
                            <div class="border border-warning border-dashed rounded text-warning p-2 mx-1">
                                <span class="me-2" data-kt-user-table-select="selected_count"></span>@lang('Selected')
                            </div>
                            <button type="button" class="btn btn-primary"
                                data-kt-user-table-select="delete_selected">@lang('Delete Selected')</button>
                        </div>
                        <!--end::Group actions-->
                    </div>
                </div>

                <!--begin::Card body-->
                <div class="card-body pt-0 table-responsive">
                    <table class="table align-middle text-center table-row-dashed fs-6 gy-5" id="view-datatable"
                        data-load="{{ route('dashboard.company-employees.index', ['trash' => request()->trash]) }}">
                        <thead class="table-primary">
                            <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                <th class="w-10px pe-2" data-name="select_switch">
                                    <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                        <input class="form-check-input" type="checkbox" data-kt-check="true"
                                            data-kt-check-target="#view-datatable .form-check-input" value="1">
                                    </div>
                                </th>
                                <th class="text-center p-0" data-name="id">@lang('id')</th>
                                <th class="text-center p-0" data-name="user">@lang('user')</th>
                                <th class="text-center p-0" data-name="company">@lang('company')</th>
                                <th class="text-center p-0" data-name="permission">@lang('permission')</th>
                                <th class="text-center p-0" data-name="branch">@lang('branch')</th>
                                <th class="text-center p-0" data-name="actions">@lang('Actions')</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 fw-bold"></tbody>
                    </table>
                </div>
                <!--end::Card body-->
            </div>
            <!--end::Card-->
        </div>
        <!--end::Container-->
    </div>
    <!--end::Content-->
@endsection
@push('js')
<script>
    var deleteUrl = "{{ route('dashboard.company-employees.delete', ['id' => '%s', 'trash' => request()->trash]) }}"
</script>
@endpush
