@extends('layouts.client')

@section('content')
    <!-- Main Content -->
    <main class="main-content">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-title">{{ trans('client.contracts') }}</h1>
            <p class="page-subtitle">
                {{ trans('client.contracts_subtitle') }}
            </p>
        </div>

        <!-- Contracts Section -->
        <div class="contracts-section">
            <div class="contracts-container">
                @forelse ($contracts as $contract)
                    <!-- Contract Item -->
                    <div class="contract-item">
                        <div class="contract-header">
                            <div class="contract-title">
                                <h3>{{ $contract->title }}</h3>
                                <span class="contract-status {{ $contract->end_date >= now() ? 'active' : 'expired' }}">
                                    {{ $contract->end_date >= now() ? trans('client.active') : trans('client.expired') }}
                                </span>
                            </div>
                            <div class="contract-actions">
                                <a href="{{ route('client.contracts.customer-prices', $contract->id) }}" 
                                   class="btn btn-print">
                                    <i class="fas fa-dollar-sign"></i> {{ trans('client.customer_overprices') }}
                                </a>
                                <a href="{{ route('client.contracts.print', $contract->id) }}" 
                                   class="btn btn-print" target="_blank">
                                    <i class="fas fa-print"></i> {{ trans('client.print_contract') }}
                                </a>
                            </div>
                        </div>

                        <div class="contract-details">
                            <div class="contract-info-grid">
                                <div class="info-item">
                                    <span class="label">{{ trans('client.months_count') }}:</span>
                                    <span class="value">{{ $contract->months_count }} {{ trans('client.months') }}</span>
                                </div>
                                <div class="info-item">
                                    <span class="label">{{ trans('client.monthly_fees') }}:</span>
                                    <span class="value">{{ $contract->month_fees ?? 0 }} {{ trans('SAR') }}</span>
                                </div>
                                <div class="info-item">
                                    <span class="label">{{ trans('client.start_date') }}:</span>
                                    <span class="value">{{ $contract->start_date ? $contract->start_date->format('Y-m-d') : trans('client.not_set') }}</span>
                                </div>
                                <div class="info-item">
                                    <span class="label">{{ trans('client.end_date') }}:</span>
                                    <span class="value">{{ $contract->end_date ? $contract->end_date->format('Y-m-d') : trans('client.not_set') }}</span>
                                </div>
                            </div>

                            @if($contract->contract)
                                <div class="contract-file">
                                    <span class="label">{{ trans('client.contract_file') }}:</span>
                                    <a href="{{ asset('storage/' . $contract->contract) }}" 
                                       class="contract-link" target="_blank">
                                        <i class="fas fa-file-pdf"></i> {{ trans('client.view_contract') }}
                                    </a>
                                </div>
                            @endif

                            @if($contract->contractPrices->count() > 0)
                                <div class="contract-prices">
                                    <h4>{{ trans('client.product_prices') }}</h4>
                                    <div class="prices-table">
                                        <div class="table-header">
                                            <div class="header-cell">{{ trans('client.product_name') }}</div>
                                            <div class="header-cell">{{ trans('client.price') }}</div>
                                        </div>
                                        @foreach($contract->contractPrices as $price)
                                            <div class="table-row">
                                                <div class="table-cell">
                                                    {{ $price->product->name ?? trans('client.product_not_found') }}
                                                </div>
                                                <div class="table-cell price">
                                                    {{ $price->price }} {{ trans('SAR') }}
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="no-contracts">
                        <div class="no-contracts-icon">
                            <i class="fas fa-file-contract"></i>
                        </div>
                        <h3>{{ trans('client.no_contracts') }}</h3>
                        <p>{{ trans('client.no_contracts_message') }}</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{ $contracts->links('pagination::bootstrap-5') }}
    </main>

    <style>
        .contracts-section {
            padding: 20px 0;
        }

        .contract-item {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            overflow: hidden;
        }

        .contract-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            background: #4a79b5;
            color: white;
        }

        .contract-actions {
            display: flex;
            gap: 10px;
        }

        .contract-title h3 {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 600;
        }

        .contract-status {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 500;
            margin-top: 8px;
        }

        .contract-status.active {
            background: rgba(34, 197, 94, 0.2);
            color: #22c55e;
        }

        .contract-status.expired {
            background: rgba(239, 68, 68, 0.2);
            color: #ef4444;
        }

        .btn-print {
            background: rgba(255,255,255,0.2);
            color: white;
            border: 1px solid rgba(255,255,255,0.3);
            padding: 8px 16px;
            border-radius: 6px;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .btn-print:hover {
            background: rgba(255,255,255,0.3);
            color: white;
        }

        .contract-details {
            padding: 20px;
        }

        .contract-info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-bottom: 20px;
        }

        .info-item {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .info-item .label {
            font-weight: 600;
            color: #6b7280;
            font-size: 0.875rem;
        }

        .info-item .value {
            font-size: 1rem;
            color: #1f2937;
            font-weight: 500;
        }

        .contract-file {
            margin-bottom: 20px;
            padding: 12px;
            background: #f8fafc;
            border-radius: 8px;
        }

        .contract-link {
            color: #3b82f6;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-top: 4px;
        }

        .contract-link:hover {
            text-decoration: underline;
        }

        .contract-prices h4 {
            margin-bottom: 16px;
            color: #1f2937;
            font-weight: 600;
        }

        .prices-table {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            overflow: hidden;
        }

        .table-header {
            display: grid;
            grid-template-columns: 1fr auto;
            background: #f9fafb;
            border-bottom: 1px solid #e5e7eb;
        }

        .header-cell {
            padding: 12px 16px;
            font-weight: 600;
            color: #374151;
        }

        .table-row {
            display: grid;
            grid-template-columns: 1fr auto;
            border-bottom: 1px solid #e5e7eb;
        }

        .table-row:last-child {
            border-bottom: none;
        }

        .table-cell {
            padding: 12px 16px;
            display: flex;
            align-items: center;
        }

        .table-cell.price {
            font-weight: 600;
            color: #059669;
        }

        .no-contracts {
            text-align: center;
            padding: 60px 20px;
            color: #6b7280;
        }

        .no-contracts-icon {
            font-size: 4rem;
            margin-bottom: 20px;
            color: #d1d5db;
        }

        .no-contracts h3 {
            margin-bottom: 12px;
            color: #374151;
        }

        @media (max-width: 768px) {
            .contract-header {
                flex-direction: column;
                gap: 16px;
                text-align: center;
            }

            .contract-info-grid {
                grid-template-columns: 1fr;
            }

            .table-header,
            .table-row {
                grid-template-columns: 1fr;
            }

            .header-cell:last-child,
            .table-cell:last-child {
                border-top: 1px solid #e5e7eb;
            }
        }
    </style>
@endsection
