@extends('admin::layouts.dashboard')

@section('content')
<div class="container-fluid flex-grow-1 container-p-y">
    {{-- Custom App Branding Shell --}}
    <style>
        .tax-dashboard-header {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            border-radius: 1rem;
            padding: 2.5rem;
            margin-bottom: 2rem;
            color: white;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 25px -5px rgba(59, 130, 246, 0.3);
        }
        .tax-dashboard-header::after {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 300px;
            height: 300px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            pointer-events: none;
        }
        .stat-card {
            background: white;
            border-radius: 1rem;
            padding: 1.5rem;
            height: 100%;
            transition: all 0.3s ease;
            border: 1px solid #e5e7eb;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }
        .stat-card .icon-box {
            width: 40px;
            height: 40px;
            border-radius: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            margin-bottom: 1rem;
        }
        .filter-bar {
            background: white;
            border-radius: 0.75rem;
            padding: 1rem;
            margin-bottom: 2rem;
            border: 1px solid #e5e7eb;
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            align-items: flex-end;
        }
        .custom-table-container {
            background: white;
            border-radius: 1rem;
            padding: 1.5rem;
            border: 1px solid #e5e7eb;
        }
        .table thead th {
            background-color: #f9fafb !important;
            color: #4b5563 !important;
            text-transform: uppercase;
            font-size: 0.75rem !important;
            font-weight: 700 !important;
            letter-spacing: 0.05em;
            padding: 1rem !important;
            border-bottom: 2px solid #e5e7eb !important;
        }
        .table tbody td {
            padding: 1rem !important;
            vertical-align: middle !important;
            font-size: 0.875rem;
        }
        .dataTables_wrapper .dataTables_filter {
            display: none;
        }
    </style>

    {{-- Page Header --}}
    <div class="tax-dashboard-header d-flex justify-content-between align-items-center">
        <div>
            <h1 class="fw-bolder mb-2 text-white" style="font-size: 2rem;">{{ trans('Electronic Invoices') }}</h1>
            <p class="opacity-75 mb-0">{{ trans('Consolidated log of all taxable invoices with VAT details.') }}</p>
        </div>
        <div class="d-none d-md-block text-end">
            <i class="fas fa-file-invoice-dollar opacity-25" style="font-size: 5rem;"></i>
        </div>
    </div>

    {{-- Stats Row --}}
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div>
                    <div class="text-muted small fw-bold mb-1">{{ trans('Total Including Tax') }}</div>
                    <div class="fs-2 fw-bolder">{{ number_format($totalIncludingTax, 2) }}</div>
                    <div class="text-success small mt-1">{{ trans('VAT Sum') }}: {{ number_format($totalVat, 2) }} {{ trans('SAR') }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="w-100">
                    <div class="text-muted small fw-bold mb-1">{{ trans('B2B Invoices') }}</div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fs-2 fw-bolder">{{ $countB2B }}</span>
                        <div class="icon-box bg-primary-subtle text-primary mb-0">
                            <i class="fas fa-building"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="w-100">
                    <div class="text-muted small fw-bold mb-1">{{ trans('B2C Invoices') }}</div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fs-2 fw-bolder">{{ $countB2C }}</span>
                        <div class="icon-box bg-info-subtle text-info mb-0">
                            <i class="fas fa-shopping-basket"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="w-100">
                    <div class="text-muted small fw-bold mb-1">{{ trans('Credit Notes') }}</div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fs-2 fw-bolder">{{ $countCredit }}</span>
                        <div class="icon-box bg-danger-subtle text-danger mb-0">
                            <i class="fas fa-file-invoice-dollar"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="filter-bar">
        <div class="flex-grow-1" style="min-width: 250px;">
            <label class="form-label small fw-bold">{{ trans('Search') }}</label>
            <div class="input-group">
                <span class="input-group-text bg-transparent border-end-0"><i class="fas fa-search text-muted"></i></span>
                <input type="text" id="search-filter" class="form-control border-start-0" placeholder="{{ trans('Invoice #, Order Ref...') }}">
            </div>
        </div>
        <div style="width: 150px;">
            <label class="form-label small fw-bold">{{ trans('Type') }}</label>
            <select id="type-filter" class="form-select">
                <option value="all">{{ trans('All') }}</option>
                <option value="B2B">B2B</option>
                <option value="B2C">B2C</option>
                <option value="CREDIT">Credit</option>
            </select>
        </div>
        <div style="width: 180px;">
            <label class="form-label small fw-bold">{{ trans('From Date') }}</label>
            <input type="date" id="from-date-filter" class="form-control">
        </div>
        <div style="width: 180px;">
            <label class="form-label small fw-bold">{{ trans('To Date') }}</label>
            <input type="date" id="to-date-filter" class="form-control">
        </div>
        <div>
            <button type="button" id="reset-filters" class="btn btn-light"><i class="fas fa-times me-1"></i> {{ trans('Reset') }}</button>
            <button type="button" id="apply-filters" class="btn btn-primary px-4">{{ trans('Filter') }}</button>
        </div>
    </div>

    {{-- Main Table --}}
    <div class="custom-table-container">
        <div class="table-responsive">
            <table class="table mb-0 text-center" id="invoices-table">
                <thead>
                    <tr>
                        <th class="text-dark">{{ trans('Order #') }}</th>
                        <th class="text-dark">{{ trans('Invoice #') }}</th>
                        <th class="text-dark">{{ trans('Customer') }}</th>
                        <th class="text-dark">{{ trans('Type') }}</th>
                        <th class="text-dark">{{ trans('Before Tax') }}</th>
                        <th class="text-dark">{{ trans('VAT 15%') }}</th>
                        <th class="text-dark">{{ trans('Total') }}</th>
                        <th class="text-dark">{{ trans('Status') }}</th>
                        <th class="text-dark">{{ trans('Receipt') }}</th>
                        <th class="text-dark">{{ trans('Actions') }}</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    $(function() {
        var table = $('#invoices-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('dashboard.electronic-invoices.data-table') }}",
                data: function(d) {
                    d.filters = {
                        search: $('#search-filter').val(),
                        type: $('#type-filter').val(),
                        from_date: $('#from-date-filter').val(),
                        to_date: $('#to-date-filter').val()
                    };
                }
            },
            columns: [
                { data: 'order_ref', name: 'order_ref' },
                { data: 'invoice_number', name: 'invoice_number' },
                { data: 'customer', name: 'customer' },
                { data: 'type', name: 'type' },
                { data: 'subtotal', name: 'subtotal' },
                { data: 'vat', name: 'vat' },
                { data: 'total', name: 'total' },
                { data: 'status', name: 'status' },
                { data: 'receipt', name: 'receipt' },
                { data: 'actions', name: 'actions', orderable: false, searchable: false }
            ],
            language: {
                url: "{{ config('app.locale') == 'ar' ? 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/ar.json' : '' }}"
            },
            pageLength: 25,
            order: [[0, 'desc']]
        });

        $('#apply-filters').click(function() {
            table.draw();
        });

        $('#reset-filters').click(function() {
            $('#search-filter').val('');
            $('#type-filter').val('all');
            $('#from-date-filter').val('');
            $('#to-date-filter').val('');
            table.draw();
        });
    });
</script>
@endpush
