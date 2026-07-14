@extends('admin::layouts.dashboard')
@section('content')
    <!--begin::Content-->
    <div class="container-fluid flex-grow-1 container-p-y">
        <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack mb-3">
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
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard.index') }}" class="text-muted text-hover-primary">@lang('Home')</a>
                    </li>
                    <li class="breadcrumb-item text-muted">@lang("Financials")</li>
                    <li class="breadcrumb-item text-dark">{{ $title }}</li>
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
                <div class="card-header row align-items-center py-4">
                    <!--begin::Card title-->
                    <div class="card-title col-md-6 mb-2 mb-md-0">
                        <div class="form-group d-flex align-items-center">
                            <label class="text-dark fw-bold me-2 mb-0" for="visible_cols">@lang('visible cols')</label>
                            <select class="form-control" data-control="select2" name="visible_cols" id="visible_cols" multiple></select>
                        </div>
                    </div>
                    <!--end::Card title-->
                    <!--begin::Card toolbar-->
                    <div class="card-toolbar col-md-6 d-flex justify-content-end">
                        <!--begin::Toolbar-->
                        <div data-kt-user-table-toolbar="base">
                            <div class="d-flex align-items-center justify-content-between w-100 flex-wrap gap-2">
                                <div class="d-flex">
                                    <!--begin::Stat-->
                                    <div class="border border-dashed border-success text-success rounded mx-1 p-2">
                                        <a href="{{ route('dashboard.financials.index') }}">
                                            <div class="fw-bolder fs-5 text-success">
                                                {{ $total }}
                                                <i class="fas fa-list-alt text-success ms-1"></i>
                                                @lang('total')
                                            </div>
                                        </a>
                                    </div>
                                    <!--end::Stat-->
                                    <!--begin::Stat-->
                                    <div class="border border-dashed border-danger text-danger rounded mx-1 p-2">
                                        <a href="{{ route('dashboard.financials.index', ['trash' => 1]) }}">
                                            <div class="fw-bolder fs-5 text-danger">
                                                {{ $trash }}
                                                <i class="fas fa-trash-alt text-danger ms-1"></i>
                                                @lang('Trash')
                                            </div>
                                        </a>
                                    </div>
                                    <!--end::Stat-->
                                </div>
                                <div class="d-flex">
                                    <!--begin::Add-->
                                    <a href="{{ route('dashboard.financials.create') }}" class="btn-operation">
                                        <i class="fas fa-plus-circle me-1"></i>
                                        <span>@lang('create new')</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <!--end::Toolbar-->
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
                    <!--end::Card toolbar-->
                </div>

                <!--begin::Filters-->
                <div class="container-fluid mt-1">
                    <button class="btn btn-primary mb-3" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapseFilters" aria-expanded="false" aria-controls="collapseFilters">
                        <i class="fas fa-filter me-1"></i>
                        {{ trans('open filters of data') }}
                    </button>

                    <div class="accordion mb-3" id="filtersAccordion">
                        <div class="accordion-item">
                            <div id="collapseFilters" class="accordion-collapse collapse" aria-labelledby="headingOne"
                                data-bs-parent="#filtersAccordion">
                                <div class="p-4 row" data-kt-user-table-filter="form">
                                    <div class="col-md-4 mb-3">
                                        <label for="company_id" class="form-label">@lang("Company")</label>
                                        <select class="custom-select filter-input form-select advance-select" name="company_id" id="company_id">
                                            <option value="">@lang("select company")</option>
                                            @foreach($companies as $company)
                                                <option value="{{ $company->id }}" @selected($company->id == request("company_id"))>{{ $company->fullname }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="user_id" class="form-label">@lang("User")</label>
                                        <select class="custom-select filter-input form-select advance-select" name="user_id" id="user_id">
                                            <option value="">@lang("select user")</option>
                                            @foreach($users as $user)
                                                <option value="{{ $user->id }}" @selected($user->id == request("user_id"))>{{ $user->fullname }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="reference_id" class="form-label">@lang("Reference ID")</label>
                                        <input type="text" class="form-control filter-input" name="reference_id" id="reference_id" placeholder="@lang('Reference ID')">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="collection_date_from" class="form-label">@lang("Collection Date From")</label>
                                        <input type="date" class="form-control filter-input" name="collection_date_from" id="collection_date_from">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="collection_date_to" class="form-label">@lang("Collection Date To")</label>
                                        <input type="date" class="form-control filter-input" name="collection_date_to" id="collection_date_to">
                                    </div>
                                    <!--begin::Actions-->
                                    <div class="d-flex justify-content-end mt-2">
                                        <button type="reset" class="btn btn-light btn-active-light-primary fw-bold me-2 px-6"
                                            data-kt-menu-dismiss="true" data-kt-user-table-filter="reset">@lang('Reset')</button>
                                        <button type="submit" class="btn btn-primary fw-bold px-6"
                                            data-kt-menu-dismiss="true" data-kt-user-table-filter="filter">@lang('Apply')</button>
                                    </div>
                                    <!--end::Actions-->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end::Filters-->

                <!--begin::Card body-->
                <div class="card-body pt-0 table-responsive">
                    <!--begin::Table-->
                    <table class="table align-middle text-center table-row-dashed fs-6 gy-5" id="view-datatable"
                        data-load="{{ route('dashboard.financials.index', ['trash' => request()->trash]) }}">
                        <!--begin::Table head-->
                        <thead class="table-primary">
                            <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                <th class="w-10px pe-2" data-name="select_switch">
                                    <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                        <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#view-datatable .form-check-input" value="1">
                                    </div>
                                </th>
                                <th class="text-center p-0" data-name="id">@lang("id")</th>
                                <th class="text-center p-0" data-name="company">@lang("Company")</th>
                                <th class="text-center p-0" data-name="user">@lang("User")</th>
                                <th class="text-center p-0" data-name="reference_id">@lang("Reference ID")</th>
                                <th class="text-center p-0" data-name="amount">@lang("Amount")</th>
                                <th class="text-center p-0" data-name="type">@lang("Type")</th>
                                <th class="text-center p-0" data-name="collection_date">@lang("Collection Date")</th>
                                <th class="text-center p-0" data-name="actions">@lang("Actions")</th>
                            </tr>
                        </thead>
                        <!--end::Table head-->
                        <!--begin::Table body-->
                        <tbody class="text-gray-600 fw-bold"></tbody>
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
    <!--end::Content-->
@endsection
@push('js')
<script>
    var deleteUrl = "{{ route('dashboard.financials.delete', ['id' => '%s', 'trash' => request()->trash]) }}"
</script>
@endpush
