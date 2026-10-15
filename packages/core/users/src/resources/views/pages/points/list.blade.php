@extends('admin::layouts.dashboard')
@section('content')
    <!--end::Header-->

    <!--begin::Content-->
    <div class="container-fluid flex-grow-1 container-p-y points-page" >
     
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
        <!--begin::Container-->
        <div  class="container-fluid">
            <div class="row g-3 mb-4 points-summary">
                <div class="col-6 col-xl-3"><div class="card h-100 points-stat"><div class="card-body"><div class="points-stat-label">{{ trans('points_ui.movements') }}</div><div class="points-stat-value">{{ number_format($summary['movements']) }}</div></div></div></div>
                <div class="col-6 col-xl-3"><div class="card h-100 points-stat"><div class="card-body"><div class="points-stat-label">{{ trans('points_ui.total_added') }}</div><div class="points-stat-value text-success">+{{ number_format($summary['added'], 2) }}</div></div></div></div>
                <div class="col-6 col-xl-3"><div class="card h-100 points-stat"><div class="card-body"><div class="points-stat-label">{{ trans('points_ui.total_deducted') }}</div><div class="points-stat-value text-danger">−{{ number_format($summary['deducted'], 2) }}</div></div></div></div>
                <div class="col-6 col-xl-3"><div class="card h-100 points-stat"><div class="card-body"><div class="points-stat-label">{{ trans('points_ui.net_remaining') }}</div><div class="points-stat-value">{{ number_format($summary['net'], 2) }}</div></div></div></div>
            </div>
            <div class="alert alert-light-info d-flex align-items-center mb-4 points-help">
                <i class="fas fa-info-circle fs-2 me-3"></i>
                <div>{{ trans('points_ui.table_help') }} {{ trans('points_ui.legacy_note') }}</div>
            </div>
            <!--begin::Card-->
            <div class="card points-ledger-card">
                <!--begin::Card header-->
                <div class="card-header row">
                    <!--begin::Card title-->
                    <div class="card-title col-md-6 ">
                        <!--begin::cols-->
                        <div class="form-group d-flex justify-content-center">
                            <label class="text-dark fw-bold" for="visible_cols"> @lang('visible cols')</label>
                            <select class="form-control mx-3" data-control="select2" name="visible_cols"
                                id="visible_cols" multiple></select>
                        </div>
                        <!--end::cols-->
                    </div>
                    <!--begin::Card title-->
                    <!--begin::Card toolbar-->
                    <div class="card-toolbar col-md-6">
                        <!--begin::Toolbar-->

                        <div  data-kt-user-table-toolbar="base">
                            <div class="d-flex justify-content-between">
                                <div class="">
                                     <div class="d-flex">
                                        <!--begin::Stat-->
                                        <div class="border border-dashed border-success text-success rounded mx-1 p-2">
                                            <a href="{{ route('dashboard.points.index') }}">
                                            <div class="fw-bolder fs-5 text-success">
                                                {{ $total }}
                                                <i class="fas fa-list-alt"></i>
                                                @lang('total')
                                            </div>
                                            </a>
                                        </div>
                                        <!--end::Stat-->
                                        <!--begin::Stat-->
                                        <div class="border border-dashed border-danger  text-danger rounded mx-1 p-2">
                                            <div class="fw-bolder fs-5">
                                                {{ $trash }}
                                                <i class="fas fa-trash-alt"></i>
                                                @lang('Trash')
                                            </div>
                                        </div>
                                        <!--end::Stat-->
                                    </div>
                                </div>
                                <div class="d-flex flex-wrap">

                                    @can('dashboard.points.export')
                                        <a href="{{ route('dashboard.points.export') }}" id="export" type="button"
                                            class="btn-operation ">
                                            <i class="fas fa-upload"></i>
                                            <span>
                                                @lang('Export Report')
                                            </span>
                                        </a>
                                    @endcan
                                    @can('dashboard.points.import')
                                        <a href="{{ route('dashboard.points.import') }}"
                                            class="btn-operation">
                                            <i class="fas fa-file-import"></i>
                                            <span>
                                                @lang('import list')
                                            </span>
                                        </a>
                                    @endcan
                                    <!--begin::Add -->
                                    @can('dashboard.points.create')
                                        <a href="{{ route('dashboard.points.create') }}"
                                            class="btn-operation ">
                                            <i class="fas fa-plus-circle"></i>
                                            <span>
                                                @lang('create new')
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

                    <div class="accordion" id="accordionExample">
                        <div class="accordion-item">
                            <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne"
                                data-bs-parent="#accordionExample">

                                <div class="p-1 row" data-kt-user-table-filter="form">

                                    
                                        <div class="col-md-6 mb-1">
                                            <label for="search">{{ trans('points_ui.search_label') }}</label>
                                            <input type="text" name="search" class="form-control filter-input"
                                                placeholder="{{ trans('points_ui.search') }}" value="{{ request("search") }}">
                                        </div>
                                        <div class="col-md-6 mb-1">
                                            <label for="amount"> @lang("points.amount") </label>
                                            <input type="text" name="amount" class="form-control filter-input"
                                                placeholder="@lang("search for points.amount") " value="{{ request("amount") }}">
                                        </div>
                                        <div class="col-md-6 mb-1">
                                            <label for="operation">@lang("operation")</label>
                                            <select class="custom-select filter-input form-select advance-select" name="operation" id="operation">
                                                <option value=""> @lang("select operation")</option><option value="withdraw" @selected("withdraw" == request("operation")) >{{ trans('points_ui.points_deducted') }}</option><option value="deposit" @selected("deposit" == request("operation")) >{{ trans('points_ui.points_added') }}</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-1">
                                            <label for="user_id">@lang("user")</label>
                                            <select class="custom-select filter-input form-select advance-select" name="user_id" id="user_id">
                                                
                                                <option  value="" > @lang("select users")</option>
                                                @foreach($users as $item)
                                                    <option value="{{$item->id }}" @selected($item->id  == request("user_id")) >@lang($item->fullname)</option>
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
                <div class="card-body pt-0 table-responsive table-responsive">
                    <!--begin::Table-->
                    <table class="table align-middle text-center table-row-dashed fs-6 gy-5" id="view-datatable"
                        data-load="{{ route('dashboard.points.index',['trash' => request()->trash]) }}">
                        <!--begin::Table head-->
                        <thead class="table-primary">
                            <!--begin::Table row-->
                            <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                
                                    <th class="w-10px pe-2" data-name="select_switch">
                                        <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                        <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#view-datatable .form-check-input" value="1">
                                        </div>
                                    </th>
                                    <th class="text-center p-0" data-name="created_at">{{ trans('points_ui.date') }}</th>
                                    <th class="text-center p-0" data-name="user">{{ trans('points_ui.customer') }}</th>
                                    <th class="text-center p-0" data-name="details">{{ trans('points_ui.reason_expiry') }}</th>
                                    <th class="text-center p-0" data-name="operation">{{ trans('points_ui.movement') }}</th>
                                    <th class="text-center p-0" data-name="amount">{{ trans('points_ui.points') }}</th>
                                    <th class="text-center p-0" data-name="current_balance">{{ trans('points_ui.current_balance') }}</th>
                                    <th class="text-center p-0" data-name="order">{{ trans('points_ui.related_order') }}</th>
                                    <th class="text-center p-0" data-name="creator">{{ trans('points_ui.added_by') }}</th>
                                    <th class="text-center p-0" data-name="actions">@lang("Actions")</th>

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
<style>
.points-page { --points-ink: #1d1d1f; --points-muted: #86868b; --points-blue: #0071e3; color: var(--points-ink); }
.points-page .page-title h1 { letter-spacing: -.025em; }
.points-page .points-stat, .points-page .points-ledger-card { border: 1px solid rgba(29,29,31,.06); border-radius: 18px; background: rgba(255,255,255,.94); box-shadow: 0 4px 18px rgba(29,29,31,.045); }
.points-page .points-stat { transition: transform .18s ease, box-shadow .18s ease; }
.points-page .points-stat:hover { transform: translateY(-2px); box-shadow: 0 9px 24px rgba(29,29,31,.08); }
.points-page .points-stat .card-body { padding: 20px 22px; }
.points-page .points-stat-label { color: var(--points-muted); font-size: .875rem; font-weight: 600; margin-bottom: 8px; }
.points-page .points-stat-value { color: var(--points-ink); font-size: clamp(1.45rem, 2vw, 1.9rem); font-weight: 700; letter-spacing: -.035em; line-height: 1.15; font-variant-numeric: tabular-nums; }
.points-page .points-stat-value.text-success { color: #16884a !important; }
.points-page .points-stat-value.text-danger { color: #d93025 !important; }
.points-page .points-help { border: 1px solid rgba(0,113,227,.12); border-radius: 14px; padding: 14px 18px; color: #3d536b; background: #f5f9ff; }
.points-page .points-ledger-card { overflow: hidden; }
.points-page .points-ledger-card > .card-header { border-bottom: 1px solid rgba(29,29,31,.07); padding: 18px 22px; }
.points-page .points-ledger-card .card-title { margin-bottom: 0; }
.points-page .points-ledger-card .card-toolbar { align-items: center; }
.points-page .points-ledger-card .btn-operation, .points-page .points-ledger-card .btn { border-radius: 10px; }
.points-page .points-ledger-card [data-kt-user-table-toolbar="base"] > .d-flex { align-items: center; gap: 12px; flex-wrap: wrap; }
.points-page .points-ledger-card .container-fluid.mt-1 { padding: 16px 22px 0; }
.points-page .points-ledger-card .accordion-item { border: 0; }
.points-page .points-ledger-card .accordion-collapse .p-1.row { padding: 16px !important; margin-top: 10px; border: 1px solid rgba(29,29,31,.08); border-radius: 14px; background: #f8f8fa; }
.points-page .points-ledger-card .form-control, .points-page .points-ledger-card .form-select { min-height: 42px; border-radius: 10px; border-color: #d2d2d7; }
.points-page .points-ledger-card .card-body { padding: 14px 22px 22px; }
.points-page #view-datatable { border-collapse: separate; border-spacing: 0; }
.points-page #view-datatable thead th { background: #f5f5f7; color: #515154; border: 0; padding: 14px 10px !important; white-space: nowrap; }
.points-page #view-datatable thead th:first-child { border-radius: 0 10px 10px 0; }
.points-page #view-datatable thead th:last-child { border-radius: 10px 0 0 10px; }
.points-page #view-datatable tbody td { padding: 14px 10px; border-bottom: 1px solid #eeeef0; vertical-align: middle; }
.points-page #view-datatable tbody tr:hover { background: #f8f8fa; }
.points-page .dataTables_wrapper .dataTables_paginate .paginate_button { border-radius: 8px !important; }
@media (max-width: 767.98px) {
    .points-page .points-stat .card-body { padding: 16px; }
    .points-page .points-ledger-card > .card-header { padding: 16px; }
    .points-page .points-ledger-card > .card-header > .card-title, .points-page .points-ledger-card > .card-header > .card-toolbar { width: 100%; }
    .points-page .points-ledger-card .container-fluid.mt-1 { padding: 14px 16px 0; }
    .points-page .points-ledger-card .card-body { padding: 12px 16px 16px; }
}
</style>
@endpush
@push('js')
<script>
    var deleteUrl = "{{ route('dashboard.points.delete', ['id'=>'%s','trash'=>request()->trash]) }}"
</script>
@endpush
