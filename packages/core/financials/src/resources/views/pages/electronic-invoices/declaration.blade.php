@extends('admin::layouts.dashboard')

@section('content')
<div class="container-fluid flex-grow-1 container-p-y">
    <style>
        :root {
            --zatca-blue: #1e3a8a;
            --zatca-light-blue: #3b82f6;
            --zatca-accent: #00AEEF;
            --zatca-success: #10b981;
            --zatca-danger: #ef4444;
            --zatca-warning: #f59e0b;
        }

        .tax-header {
            background: linear-gradient(135deg, var(--zatca-blue) 0%, #172554 100%);
            border-radius: 1.25rem;
            padding: 2rem;
            margin-bottom: 2rem;
            color: white;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 30px -10px rgba(30, 58, 138, 0.5);
        }

        .tax-header::after {
            content: '';
            position: absolute;
            top: -20%;
            right: -5%;
            width: 250px;
            height: 250px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
        }

        .summary-card {
            background: white;
            border-radius: 1rem;
            padding: 1.5rem;
            height: 100%;
            border: 1px solid #e2e8f0;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
        }

        .summary-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05);
        }

        .summary-card.dark {
            background: #0f172a;
            color: white;
        }

        .summary-card .label {
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.5rem;
            opacity: 0.7;
        }

        .summary-card .value {
            font-size: 1.75rem;
            font-weight: 800;
            margin: 0.25rem 0;
        }

        .tax-table {
            background: white;
            border-radius: 1rem;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
        }

        .tax-table thead th {
            background: #f8fafc;
            color: #475569;
            font-weight: 700;
            font-size: 0.75rem;
            text-transform: uppercase;
            padding: 1rem;
            border-bottom: 2px solid #e2e8f0;
        }

        .tax-table tbody td {
            padding: 1rem;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
        }

        .quarter-row {
            background: #f8fafc;
            font-weight: 700;
            color: var(--zatca-blue);
        }

        .type-label {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.8rem;
            color: #64748b;
        }

        .type-label i {
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 4px;
            background: #f1f5f9;
        }

        .vat-box {
            background: #f1f5f9;
            border-radius: 0.5rem;
            padding: 0.5rem 0.75rem;
            display: inline-block;
        }

        .zatca-form-section {
            background: white;
            border-radius: 1rem;
            padding: 2rem;
            margin-top: 2rem;
            border: 1px solid #e2e8f0;
        }

        .zatca-row {
            display: flex;
            justify-content: space-between;
            padding: 1rem 0;
            border-bottom: 1px solid #f1f5f9;
        }

        .zatca-row:last-child {
            border-bottom: none;
        }

        .zatca-row .label-col {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .zatca-row .label-col .main-label {
            font-weight: 700;
            color: #1e293b;
        }

        .zatca-row .label-col .sub-label {
            font-size: 0.75rem;
            color: #64748b;
        }

        .zatca-row .value-col {
            width: 150px;
            text-align: right;
            font-weight: 700;
            font-size: 1.1rem;
        }

        .bg-zatca-light { background-color: #f0f9ff; }
        .bg-zatca-success-light { background-color: #ecfdf5; }

        .net-payable-banner {
            background: #0f172a;
            color: white;
            padding: 1.5rem;
            border-radius: 0.75rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 2rem;
        }
    </style>

    {{-- Year Switcher --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="btn-group shadow-sm">
            <a href="{{ route('dashboard.electronic-invoices.declaration', ['year' => $year - 1]) }}" class="btn btn-white"><i class="fas fa-chevron-left"></i></a>
            <button class="btn btn-white fw-bold px-4">{{ $year }}</button>
            <a href="{{ route('dashboard.electronic-invoices.declaration', ['year' => $year + 1]) }}" class="btn btn-white"><i class="fas fa-chevron-right"></i></a>
        </div>
        <div>
            <h1 class="h4 mb-0 fw-bold">{{ trans('Tax Declaration Dashboard') }}</h1>
        </div>
    </div>

    {{-- Top Stats --}}
    <div class="row g-4 mb-4 text-center">
        <div class="col-md-3">
            <div class="summary-card dark">
                <div class="label">{{ trans('Total VAT This Current Quarter') }}</div>
                <div class="value text-warning">{{ number_format($systemTotals['current_quarter_vat'] ?? 0, 2) }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="summary-card">
                <div class="label" style="color:var(--zatca-warning)">{{ trans('Total VAT Over All') }}</div>
                <div class="value" style="color:var(--zatca-warning)">{{ number_format($systemTotals['total_vat_overall'] ?? 0, 2) }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="summary-card">
                <div class="label" style="color:var(--zatca-blue)">{{ trans('Total B2B Sales') }}</div>
                <div class="value" style="color:var(--zatca-blue)">{{ number_format($systemTotals['total_b2b_sales'] ?? 0, 2) }}</div>
                <div class="small text-muted"><i class="fas fa-building me-1"></i> {{ trans('System All Time') }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="summary-card">
                <div class="label" style="color:var(--zatca-accent)">{{ trans('Total B2C Sales') }}</div>
                <div class="value" style="color:var(--zatca-accent)">{{ number_format($systemTotals['total_b2c_sales'] ?? 0, 2) }}</div>
                <div class="small text-muted"><i class="fas fa-user me-1"></i> {{ trans('System All Time') }}</div>
            </div>
        </div>
    </div>

    {{-- Quarterly Table --}}
    <div class="tax-table mb-4">
        <div class="p-3 bg-white border-bottom d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold"><i class="fas fa-calendar-alt me-2 text-primary"></i> {{ trans('Unified Quarterly Tax Declaration') }} — {{ $year }}</h5>
        </div>
        <div class="table-responsive">
            <table class="table mb-0 text-center">
                <thead class="table-dark">
                    <tr>
                        <th style="width: 80px;">{{ trans('Quarter') }}</th>
                        <th>{{ trans('Sales Type') }}</th>
                        <th>{{ trans('Net Sales') }}</th>
                        <th>{{ trans('Output VAT') }}</th>
                        <th>{{ trans('Adjustments / CN') }}</th>
                        <th>{{ trans('Purchases / Input VAT') }}</th>
                        <th>{{ trans('Net Authority') }}</th>
                        <th>{{ trans('Due Date') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($quarters as $qNum => $data)
                        @if($data)
                            <tr class="quarter-row">
                                <td rowspan="3" class="align-middle border-end">Q{{ $qNum }}</td>
                                <td class="text-start">
                                    <div class="type-label"><i class="fas fa-user"></i> {{ trans('Individuals (B2C)') }}</div>
                                </td>
                                <td>{{ number_format($data['b2c_sales'], 2) }}</td>
                                <td><span class="text-primary fw-bold">{{ number_format($data['b2c_vat'], 2) }}</span></td>
                                <td rowspan="2" class="align-middle bg-white border-start border-end">
                                    <div class="text-danger fw-bold">{{ number_format($data['adj_vat'] ?? 0, 2) }}</div>
                                </td>
                                <td rowspan="2" class="align-middle bg-white border-start border-end">
                                    <div class="text-success fw-bold">{{ number_format($data['purchases_vat'] ?? 0, 2) }}</div>
                                </td>
                                <td rowspan="2" class="align-middle bg-zatca-light fw-bold fs-5 text-warning border-start border-end">
                                    {{ number_format($data['net_vat'] - ($data['adj_vat'] ?? 0) - ($data['purchases_vat'] ?? 0), 2) }}
                                </td>
                                <td rowspan="3" class="align-middle border-start">{{ $data['due_date'] }}</td>
                            </tr>
                            <tr>
                                <td class="text-start">
                                    <div class="type-label"><i class="fas fa-building"></i> {{ trans('Companies (B2B)') }}</div>
                                </td>
                                <td>{{ number_format($data['b2b_sales'], 2) }}</td>
                                <td><span class="text-primary fw-bold">{{ number_format($data['b2b_vat'], 2) }}</span></td>
                            </tr>
                            <tr class="bg-light fw-bolder">
                                <td class="text-start">{{ trans('Total') }}</td>
                                <td>{{ number_format($data['b2c_sales'] + $data['b2b_sales'], 2) }}</td>
                                <td>{{ number_format($data['b2c_vat'] + $data['b2b_vat'], 2) }}</td>
                                <td>{{ number_format($data['adj_vat'] ?? 0, 2) }}</td>
                                <td class="text-success">{{ number_format($data['purchases_vat'] ?? 0, 2) }}</td>
                                <td class="bg-zatca-light text-warning">{{ number_format($data['net_vat'] - ($data['adj_vat'] ?? 0) - ($data['purchases_vat'] ?? 0), 2) }}</td>
                            </tr>
                        @else
                            <tr class="text-muted opacity-50">
                                <td>Q{{ $qNum }}</td>
                                <td colspan="7" class="py-4">{{ trans('Data for this quarter is not yet available') }}</td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Detailed VAT Return Section (ZATCA Form Format) --}}
    <div class="zatca-form-section">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="fw-bold mb-0 text-dark">
                <i class="fas fa-file-invoice-dollar me-2" style="color:var(--zatca-blue)"></i> 
                {{ trans('VAT Return (Annual) Comprehensive Summary') }}
            </h5>
            {{-- <button class="btn btn-sm btn-outline-secondary"><i class="fas fa-download me-1"></i> {{ trans('Export ZATCA Summary') }}</button> --}}
        </div>

        <table class="table table-sm text-center">
            <thead class="table-dark">
                <tr class="text-muted small">
                    <th class="text-start">{{ trans('ZATCA Form Item') }}</th>
                    <th>{{ trans('Original Amount (SAR)') }}</th>
                    <th>{{ trans('VAT Amount (SAR)') }}</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-start fw-bold">1. {{ trans('Total Sales Subject to 15% VAT') }}</td>
                    <td>{{ number_format(($summary['b2c_sales'] ?? 0) + ($summary['b2b_sales'] ?? 0), 2) }}</td>
                    <td class="text-primary">{{ number_format(($summary['b2c_vat'] ?? 0) + ($summary['b2b_vat'] ?? 0), 2) }}</td>
                </tr>
                <tr class="text-danger">
                    <td class="text-start fw-bold">2. {{ trans('Sales Adjustments / Credit Notes') }}</td>
                    <td>{{ number_format($summary['adj_amount'] ?? 0, 2) }} -</td>
                    <td class="text-primary">{{ number_format(($summary['adj_vat'] ?? 0), 2) }}</td>

                </tr>
                <tr class="bg-zatca-light">
                    <td class="text-start fw-bold text-primary">3. {{ trans('Net Output VAT Payable') }}</td>
                    <td>{{ number_format(($summary['net_sales'] ?? 0) - ($summary['adj_amount'] ?? 0), 2) }}</td>
                    <td class="text-primary">{{ number_format(($summary['net_vat'] ?? 0) - ($summary['adj_vat'] ?? 0), 2) }}</td>
                </tr>
                 <tr>
                    <td class="text-start fw-bold">4. {{ trans('Domestic Purchases Subject to 15% VAT') }}</td>
                    <td>{{ number_format($summary['purchases_amount'] ?? 0, 2) }}</td>
                    <td class="text-success">{{ number_format($summary['purchases_vat'] ?? 0, 2) }}</td>
                </tr>
                <tr class="bg-zatca-success-light">
                    <td class="text-start fw-bold text-success">5. {{ trans('Net Input VAT Deductible') }}</td>
                    <td>—</td>
                    <td class="text-success">{{ number_format($summary['purchases_vat'] ?? 0, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <div class="net-payable-banner">
            <div class="fw-bolder fs-4">{{ trans('Net VAT Due to ZATCA Authority') }}</div>
            <div class="d-flex align-items-center gap-4">
                <div class="text-end">
                    <div class="small opacity-50">{{ trans('Total Sum (SAR)') }}</div>
                    <div class="fs-2 fw-bolder">{{ number_format(($summary['net_vat'] ?? 0) - ($summary['adj_vat'] ?? 0) - ($summary['purchases_vat'] ?? 0), 2) }}</div>
                </div>
            </div>
        </div>
    </div>

   
</div>
@endsection
