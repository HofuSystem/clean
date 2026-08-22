@extends('admin::layouts.dashboard')

@section('content')
<div class="container-fluid flex-grow-1 container-p-y px-4">

    <!-- Top Header & Actions -->
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4 gap-3">
        <!-- Right: Breadcrumb & Title -->
        <div>
            <ul class="breadcrumb fw-bold fs-7 my-1 mb-2">
                <li class="breadcrumb-item text-muted">
                    <a href="{{ route('dashboard.index') }}" class="text-muted text-hover-primary">{{ trans('Home') }}</a>
                </li>
                <li class="breadcrumb-item text-muted">{{ trans('Financial Wallet') }}</li>
                <li class="breadcrumb-item text-dark fw-bold">{{ trans('Wallet Transactions Log') }}</li>
            </ul>
            <h1 class="text-dark fw-bolder fs-2 my-1" style="color: #1e293b !important; font-size: 26px;">{{ trans('Wallet Transactions Summary') }}</h1>
        </div>

        <!-- Left: Action Button & Period Selector -->
        <div class="d-flex flex-column align-items-end gap-2 position-relative">
            @can('dashboard.wallet-transactions.create')
                <a href="{{ route('dashboard.wallet-transactions.create') }}" class="btn text-white fw-bold px-4 py-2" style="background-color: #244b7d; border-radius: 8px; font-size: 14px;">
                    <span class="me-1 fw-bolder">+</span> {{ trans('Add / Deduct Balance') }}
                </a>
            @endcan

            <!-- Period dropdown -->
            <div class="position-relative" style="min-width: 160px;">
                <select id="period_selector" class="form-select form-select-sm fw-bold ps-4 pe-9 py-2 border text-dark" style="border-radius: 8px; background-color: #fff; border-color: #cbd5e1; font-size: 13px;">
                    <option value="all" @selected($period == 'all')>📅 {{ trans('All') }}</option>
                    <option value="today" @selected($period == 'today')>📅 {{ trans('Today') }}</option>
                    <option value="this_month" @selected($period == 'this_month')>📅 {{ trans('This Month') }}</option>
                    <option value="last_month" @selected($period == 'last_month')>📅 {{ trans('Last Month') }}</option>
                    <option value="last_3_months" @selected($period == 'last_3_months')>📅 {{ trans('Last 3 Months') }}</option>
                    <option value="this_year" @selected($period == 'this_year')>📅 {{ trans('This Year') }}</option>
                    <option value="custom" @selected($period == 'custom')>📅 {{ trans('Custom Period') }}</option>
                </select>

                <!-- Custom Date Range Popup (Aligned to left so it extends inward to the right) -->
                <div id="custom_date_range_wrapper" class="d-none p-3 bg-white border rounded-3 shadow position-absolute mt-1 z-3" style="width: 280px; left: 0 !important; right: auto !important; box-shadow: 0 10px 25px rgba(0,0,0,0.15) !important;">
                    <div class="mb-2">
                        <label class="form-label fs-8 text-muted fw-bold mb-1">{{ trans('From Date') }}</label>
                        <input type="date" id="custom_from_date" class="form-control form-control-sm">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fs-8 text-muted fw-bold mb-1">{{ trans('To Date') }}</label>
                        <input type="date" id="custom_to_date" class="form-control form-control-sm">
                    </div>
                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" id="btn_cancel_custom_date" class="btn btn-light btn-sm py-1 px-3 fs-8">{{ trans('Cancel') }}</button>
                        <button type="button" id="btn_apply_custom_date" class="btn text-white btn-sm py-1 px-3 fs-8 fw-bold" style="background-color: #244b7d;">{{ trans('Apply') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Stats Cards Grid (5 Cards in 1 Row) -->
    <div class="row g-3 mb-4">
        <!-- Card 1: Total Client Balances -->
        <div class="col-12 col-sm-6 col-lg">
            <div class="stat-card">
                <div class="stat-icon-box" style="background-color: #e0edfc; color: #2563eb;">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 7V4a1 1 0 0 0-1-1H5a2 2 0 0 0 0 4h15a1 1 0 0 1 1 1v4h-3a2 2 0 0 0 0 4h3a1 1 0 0 0 1-1v-2a1 1 0 0 0-1-1"/><path d="M3 5v14a2 2 0 0 0 2 2h15a1 1 0 0 0 1-1v-4"/></svg>
                </div>
                <div class="stat-content">
                    <span class="stat-title">{{ trans('Total Client Balances') }}</span>
                    <h3 class="stat-value">
                        <span id="stat_total_clients_balance">{{ number_format($stats['total_clients_balance'], 2) }}</span>
                        <span class="stat-unit">{{ trans('SAR') }}</span>
                    </h3>
                    <span class="stat-subtext text-muted">{{ trans('Currently available balance (unlinked to recharge)') }}</span>
                </div>
            </div>
        </div>

        <!-- Card 2: Total Recharge -->
        <div class="col-12 col-sm-6 col-lg">
            <div class="stat-card">
                <div class="stat-icon-box" style="background-color: #dcfce7; color: #16a34a;">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
                </div>
                <div class="stat-content">
                    <span class="stat-title">{{ trans('Total Recharge') }}</span>
                    <h3 class="stat-value">
                        <span id="stat_total_recharge">{{ number_format($stats['total_recharge'], 2) }}</span>
                        <span class="stat-unit">{{ trans('SAR') }}</span>
                    </h3>
                    <span class="stat-subtext" style="color: #16a34a;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1 inline-block"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        <span id="stat_recharge_users_subtext">{{ trans('From :count clients', ['count' => $stats['recharge_users_count']]) }}</span>
                    </span>
                </div>
            </div>
        </div>

        <!-- Card 3: Paid from Wallet -->
        <div class="col-12 col-sm-6 col-lg">
            <div class="stat-card">
                <div class="stat-icon-box" style="background-color: #f3e8ff; color: #9333ea;">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><line x1="3" x2="21" y1="6" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                </div>
                <div class="stat-content">
                    <span class="stat-title">{{ trans('Paid from Wallet') }}</span>
                    <h3 class="stat-value">
                        <span id="stat_total_paid_from_wallet">{{ number_format($stats['total_paid_from_wallet'], 2) }}</span>
                        <span class="stat-unit">{{ trans('SAR') }}</span>
                    </h3>
                    <span class="stat-subtext" style="color: #9333ea;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1 inline-block"><rect width="16" height="20" x="4" y="2" rx="2"/><line x1="8" x2="16" y1="6" y2="6"/><line x1="8" x2="16" y1="10" y2="10"/><line x1="8" x2="12" y1="14" y2="14"/></svg>
                        <span id="stat_paid_orders_subtext">{{ trans(':count paid orders', ['count' => $stats['paid_orders_count']]) }}</span>
                    </span>
                </div>
            </div>
        </div>

        <!-- Card 4: Promotional Balances -->
        <div class="col-12 col-sm-6 col-lg">
            <div class="stat-card">
                <div class="stat-icon-box" style="background-color: #ffedd5; color: #ea580c;">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 11 18-5v12L3 14v-3z"/><path d="M11.6 16.8a3 3 0 1 1-5.8-1.6"/></svg>
                </div>
                <div class="stat-content">
                    <span class="stat-title">{{ trans('Promotional Balances (Added)') }}</span>
                    <h3 class="stat-value">
                        <span id="stat_total_promotional">{{ number_format($stats['total_promotional'], 2) }}</span>
                        <span class="stat-unit">{{ trans('SAR') }}</span>
                    </h3>
                    <span class="stat-subtext" style="color: #ea580c;">
                        <span id="stat_avg_promotional_subtext">{{ trans('Average :avg SAR per client', ['avg' => number_format($stats['avg_promotional_per_user'], 2)]) }}</span>
                    </span>
                </div>
            </div>
        </div>

        <!-- Card 5: Customer Compensations -->
        <div class="col-12 col-sm-6 col-lg">
            <div class="stat-card">
                <div class="stat-icon-box" style="background-color: #ffe4e6; color: #e11d48;">
                    <svg width="22" height="22" viewBox="0 0 24 24">
                        <path fill="#e11d48" d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                        <path fill="none" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M12 4.5l-2.5 4.5 3.5 2.5-3 3.5 2 4"/>
                    </svg>
                </div>
                <div class="stat-content">
                    <span class="stat-title">{{ trans('Customer Compensations') }}</span>
                    <h3 class="stat-value">
                        <span id="stat_total_compensations">{{ number_format($stats['total_compensations'], 2) }}</span>
                        <span class="stat-unit">{{ trans('SAR') }}</span>
                    </h3>
                    <span class="stat-subtext" style="color: #e11d48;">
                        <span id="stat_compensations_subtext">{{ trans(':count compensation cases', ['count' => $stats['compensations_count']]) }}</span>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Table Container Card -->
    <div class="main-table-wrapper">
        
        <!-- Row 1: Quick Filter Pills (Aligned Right in RTL) -->
        <div class="d-flex flex-wrap gap-2 mb-4 justify-content-start">
            <button type="button" class="btn filter-tab-btn active" data-tab="all">
                {{ trans('All') }}
            </button>
            <button type="button" class="btn filter-tab-btn" data-tab="recharges">
                {{ trans('Recharge Operations') }}
            </button>
            <button type="button" class="btn filter-tab-btn" data-tab="payments">
                {{ trans('Payments') }}
            </button>
            <button type="button" class="btn filter-tab-btn" data-tab="compensations">
                {{ trans('Compensations') }}
            </button>
            <button type="button" class="btn filter-tab-btn" data-tab="promotions">
                {{ trans('Promotional Balances') }}
            </button>
        </div>

        <!-- Row 2: Total Badge on Right & Search on Left -->
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
            <!-- Right: Total Badge -->
            <div class="total-badge-box">
                <span>{{ trans('Total Transactions') }}</span>
                <strong id="total_records_count" class="ms-1">{{ $total }}</strong>
            </div>

            <!-- Left: Search Input -->
            <div style="min-width: 280px; width: 320px; max-width: 100%;">
                <div class="position-relative">
                    <input type="text" id="custom_table_search" class="form-control custom-search-input" 
                           placeholder="{{ trans('Search by customer name, phone, or reference...') }}">
                    <span class="position-absolute top-50 translate-middle-y" style="right: 14px; pointer-events: none; color: #94a3b8;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" x2="16.65" y1="21" y2="16.65"/></svg>
                    </span>
                </div>
            </div>
        </div>

        <!-- Row 3: Show Entries on Right & Export Group on Left -->
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
            <!-- Right: Show Entries -->
            <div class="d-flex align-items-center gap-2">
                <span class="text-muted fs-7">{{ trans('Show') }}</span>
                <select id="custom_page_length" class="form-select form-select-sm py-1 px-2 text-center fw-bold" style="width: 70px; border-radius: 6px; border-color: #cbd5e1;">
                    <option value="25" selected>25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                    <option value="300">300</option>
                    <option value="-1">{{ trans('All') }}</option>
                </select>
                <span class="text-muted fs-7">{{ trans('entries') }}</span>
            </div>

            <!-- Left: Export Buttons Group (Seamless Border Joined) -->
            <div class="export-btn-group">
                <button type="button" class="btn btn-export" id="btn_export_copy">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="14" height="14" x="8" y="8" rx="2" ry="2"/><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/></svg>
                    {{ trans('Copy') }}
                </button>
                <button type="button" class="btn btn-export" id="btn_export_csv">
                    CSV
                </button>
                <button type="button" class="btn btn-export" id="btn_export_excel">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="8" x2="16" y1="13" y2="13"/><line x1="8" x2="16" y1="17" y2="17"/></svg>
                    Excel
                </button>
                <button type="button" class="btn btn-export" id="btn_export_pdf">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                    PDF
                </button>
                <button type="button" class="btn btn-export" id="btn_export_print">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect width="12" height="8" x="6" y="14"/></svg>
                    {{ trans('Print') }}
                </button>
            </div>
        </div>

        <!-- Base Toolbar (Hidden/Managed by table.js) -->
        <div data-kt-user-table-toolbar="base" class="d-none"></div>

        <!-- Selected Rows Bulk Actions Toolbar -->
        <div class="d-flex justify-content-between align-items-center rounded-3 p-3 mb-3 d-none" 
             data-kt-user-table-toolbar="selected" 
             style="background-color: #fff5f8; border: 1px dashed #f1416c;">
            <div class="d-flex align-items-center gap-2">
                <span class="badge text-white fs-7 px-3 py-2 fw-bold" style="background-color: #f1416c; border-radius: 6px;">
                    <span data-kt-user-table-select="selected_count" class="me-1">0</span> {{ trans('Selected') }}
                </span>
                <span class="text-dark fw-bold fs-7">{{ trans('تم تحديد العناصر وجاهزة للحذف') }}</span>
            </div>
            <button type="button" class="btn text-white btn-sm px-4 py-2 fw-bold d-flex align-items-center gap-2" 
                    data-kt-user-table-select="delete_selected" 
                    style="background-color: #f1416c; border-radius: 8px;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>
                {{ trans('Delete Selected') }}
            </button>
        </div>

        <div data-kt-user-table-filter="form" class="d-none">
            <button data-kt-user-table-filter="reset"></button>
            <button data-kt-user-table-filter="filter"></button>
        </div>

        <!-- Hidden Inputs for DataTables Filters Payload -->
        <input type="hidden" class="filter-input" name="tab" id="filter_tab" value="all">
        <input type="hidden" class="filter-input" name="period" id="filter_period" value="{{ $period }}">
        <input type="hidden" class="filter-input" name="from_created_at" id="filter_from_created_at" value="">
        <input type="hidden" class="filter-input" name="to_created_at" id="filter_to_created_at" value="">
        <input type="hidden" class="filter-input" name="search" id="filter_search" value="">

        <!-- Datatable Element -->
        <div class="table-responsive">
            <table class="table table-hover align-middle table-row-dashed fs-6 gy-4 mb-0" id="view-datatable"
                   data-load="{{ route('dashboard.wallet-transactions.index', ['trash' => request()->trash]) }}">
                <thead>
                    <tr class="table-header-row">
                        <th class="w-10px pe-2 text-center" data-name="select_switch">
                            <div class="form-check form-check-sm form-check-custom form-check-solid justify-content-center">
                                <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#view-datatable .form-check-input" value="1">
                            </div>
                        </th>
                        <th class="text-center" data-name="created_at">{{ trans('Date and Time') }}</th>
                        <th class="text-center" data-name="user_id">{{ trans('Client') }}</th>
                        <th class="text-center" data-name="transaction_type">{{ trans('Operation Type') }}</th>
                        <th class="text-center" data-name="details">{{ trans('Transaction Details') }}</th>
                        <th class="text-center" data-name="amount">{{ trans('Net Amount (+/-)') }}</th>
                        <th class="text-center" data-name="wallet_after">{{ trans('Ending Balance') }}</th>
                        <th class="text-center" data-name="reference">{{ trans('Reference') }}</th>
                        <th class="text-center" data-name="added_by_id">{{ trans('Added By') }}</th>
                        <th class="text-center" data-name="status">{{ trans('Status') }}</th>
                        <th class="text-center" data-name="actions">{{ trans('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 fw-bold">
                </tbody>
            </table>
        </div>

    </div>
</div>
@endsection

@push('css')
<style>
/* Stat Cards */
.stat-card {
    background: #ffffff;
    border: 1px solid #f1f5f9;
    border-radius: 14px;
    padding: 16px 18px;
    height: 100%;
    display: flex;
    justify-content: flex-start;
    align-items: center;
    gap: 14px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
}
.stat-content {
    display: flex;
    flex-direction: column;
    justify-content: center;
}
.stat-title {
    color: #64748b;
    font-size: 13px;
    font-weight: 600;
    margin-bottom: 4px;
}
.stat-value {
    color: #0f172a;
    font-size: 20px;
    font-weight: 700;
    margin: 0 0 4px 0;
    line-height: 1.2;
}
.stat-unit {
    font-size: 13px;
    color: #64748b;
    font-weight: normal;
}
.stat-subtext {
    font-size: 11px;
    font-weight: 600;
}
.stat-icon-box {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

/* Main Table Container */
.main-table-wrapper {
    background: #ffffff;
    border: 1px solid #f1f5f9;
    border-radius: 14px;
    padding: 22px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
}

/* Filter Tab Buttons */
.filter-tab-btn {
    background-color: #ffffff;
    color: #64748b;
    border: 1px solid #e2e8f0;
    border-radius: 25px;
    font-size: 13px;
    font-weight: 600;
    padding: 7px 22px;
    transition: all 0.2s ease-in-out;
}
.filter-tab-btn:hover {
    background-color: #f8fafc;
    color: #244b7d;
    border-color: #cbd5e1;
}
.filter-tab-btn.active {
    background-color: #244b7d !important;
    color: #ffffff !important;
    border-color: #244b7d !important;
    box-shadow: 0 2px 6px rgba(36, 75, 125, 0.25);
}

/* Custom Search Input */
.custom-search-input {
    background-color: #f8fafc !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 10px !important;
    padding: 9px 40px 9px 16px !important;
    font-size: 13px !important;
    color: #1e293b !important;
    transition: border-color 0.2s, box-shadow 0.2s;
}
.custom-search-input:focus {
    background-color: #ffffff !important;
    border-color: #244b7d !important;
    box-shadow: 0 0 0 3px rgba(36, 75, 125, 0.1) !important;
}

/* Total Badge Box */
.total-badge-box {
    background-color: #ffffff;
    border: 1px solid #e2e8f0;
    color: #0f172a;
    border-radius: 8px;
    padding: 7px 16px;
    font-size: 13px;
    font-weight: 600;
    white-space: nowrap;
    box-shadow: 0 1px 2px rgba(0,0,0,0.02);
}

/* Export Buttons Group */
.export-btn-group {
    display: inline-flex;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    overflow: hidden;
    background: #ffffff;
    box-shadow: 0 1px 2px rgba(0,0,0,0.02);
}
.export-btn-group .btn-export {
    border: none;
    border-left: 1px solid #e2e8f0;
    border-radius: 0;
    padding: 6px 14px;
    font-size: 12px;
    font-weight: 600;
    color: #475569;
    background: #ffffff;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    transition: all 0.15s ease-in-out;
}
.export-btn-group .btn-export:last-child {
    border-left: none;
}
.export-btn-group .btn-export:hover {
    background-color: #f8fafc;
    color: #0f172a;
}

/* DataTables Overrides */
.table-header-row th {
    background-color: #2b5693 !important;
    color: #ffffff !important;
    font-size: 12.5px !important;
    font-weight: 700 !important;
    padding: 12px 10px !important;
    vertical-align: middle;
    border: none !important;
}
.table-header-row th:first-child {
    border-top-right-radius: 8px;
    border-bottom-right-radius: 8px;
}
.table-header-row th:last-child {
    border-top-left-radius: 8px;
    border-bottom-left-radius: 8px;
}
#view-datatable tbody tr td {
    padding: 14px 10px !important;
    vertical-align: middle;
    border-bottom: 1px solid #f1f5f9;
}
/* Hide default DataTables search & buttons bar */
.dt-buttons, .dataTables_filter, .dataTables_length {
    display: none !important;
}
</style>
@endpush

@push('js')
<script>
    var deleteUrl = "{{ route('dashboard.wallet-transactions.delete', ['id'=>'%s','trash'=>request()->trash]) }}";

    function triggerDataTableFilter() {
        if ($('[data-kt-user-table-filter="filter"]').length) {
            $('[data-kt-user-table-filter="filter"]').trigger('click');
        } else if (typeof datatable !== 'undefined') {
            datatable.draw();
        }
    }

    function fetchStats(period, fromDate, toDate) {
        $.ajax({
            url: "{{ route('dashboard.wallet-transactions.stats') }}",
            type: "GET",
            data: { 
                period: period,
                from_date: fromDate || '',
                to_date: toDate || ''
            },
            success: function(res) {
                if (res) {
                    $('#stat_total_clients_balance').text(Number(res.total_clients_balance).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                    $('#stat_total_recharge').text(Number(res.total_recharge).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                    $('#stat_recharge_users_subtext').text("من " + res.recharge_users_count + " عميل");

                    $('#stat_total_paid_from_wallet').text(Number(res.total_paid_from_wallet).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                    $('#stat_paid_orders_subtext').text(res.paid_orders_count + " طلب مدفوع");

                    $('#stat_total_promotional').text(Number(res.total_promotional).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                    $('#stat_avg_promotional_subtext').text("متوسط " + Number(res.avg_promotional_per_user).toFixed(2) + " ر.س للعميل");

                    $('#stat_total_compensations').text(Number(res.total_compensations).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                    $('#stat_compensations_subtext').text(res.compensations_count + " حالة تعويض");
                }
            }
        });
    }

    $(document).ready(function() {

        // Quick Tab Switcher
        $('.filter-tab-btn').on('click', function() {
            $('.filter-tab-btn').removeClass('active');
            $(this).addClass('active');

            var tab = $(this).data('tab');
            $('#filter_tab').val(tab);
            triggerDataTableFilter();
        });

        // Period Dropdown Change
        $('#period_selector').on('change', function() {
            var period = $(this).val();

            if (period === 'custom') {
                $('#custom_date_range_wrapper').removeClass('d-none');
            } else {
                $('#custom_date_range_wrapper').addClass('d-none');
                $('#filter_period').val(period);
                $('#filter_from_created_at').val('');
                $('#filter_to_created_at').val('');

                fetchStats(period, null, null);
                triggerDataTableFilter();
            }
        });

        // Apply Custom Date Range
        $('#btn_apply_custom_date').on('click', function() {
            var fromDate = $('#custom_from_date').val();
            var toDate = $('#custom_to_date').val();

            $('#filter_period').val('custom');
            $('#filter_from_created_at').val(fromDate);
            $('#filter_to_created_at').val(toDate);
            $('#custom_date_range_wrapper').addClass('d-none');

            fetchStats('custom', fromDate, toDate);
            triggerDataTableFilter();
        });

        // Cancel Custom Date Range
        $('#btn_cancel_custom_date').on('click', function() {
            $('#custom_date_range_wrapper').addClass('d-none');
            $('#period_selector').val($('#filter_period').val() || 'all');
        });

        // Search Input Debounce
        var searchTimeout = null;
        $('#custom_table_search').on('keyup', function() {
            var val = $(this).val();
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function() {
                $('#filter_search').val(val);
                triggerDataTableFilter();
            }, 300);
        });

        // Page Length Change
        $('#custom_page_length').on('change', function() {
            var len = parseInt($(this).val());
            if (typeof datatable !== 'undefined') {
                datatable.page.len(len).draw();
            }
        });

        // Hook Custom Export Buttons
        $('#btn_export_copy').on('click', function() {
            if (typeof datatable !== 'undefined') {
                datatable.button('.buttons-copy').trigger();
            }
        });
        $('#btn_export_csv').on('click', function() {
            if (typeof datatable !== 'undefined') {
                datatable.button('.buttons-csv').trigger();
            }
        });
        $('#btn_export_excel').on('click', function() {
            if (typeof datatable !== 'undefined') {
                datatable.button('.buttons-excel').trigger();
            }
        });
        $('#btn_export_pdf').on('click', function() {
            if (typeof datatable !== 'undefined') {
                datatable.button('.buttons-pdf').trigger();
            }
        });
        $('#btn_export_print').on('click', function() {
            if (typeof datatable !== 'undefined') {
                datatable.button('.buttons-print').trigger();
            }
        });

        // Update Total Records on Draw
        if (typeof datatable !== 'undefined') {
            datatable.on('draw', function() {
                var info = datatable.page.info();
                if (info && info.recordsDisplay !== undefined) {
                    $('#total_records_count').text(info.recordsDisplay.toLocaleString());
                }
            });
        }
    });
</script>
@endpush
