<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ trans('client.contract_print_title') }} - {{ $contract->title }}</title>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        :root {
            --primary-bg: #F8FAFC;
            --card-bg: #FFFFFF;
            --text-main: #1E293B;
            --text-muted: #64748B;
            --accent-blue: #3B82F6;
            --accent-green: #10B981;
            --accent-green-light: #ECFDF5;
            --accent-blue-light: #EFF6FF;
            --border-color: #E2E8F0;
            --shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'IBM+Plex+Sans+Arabic', 'Inter', -apple-system, sans-serif;
        }

        body {
            background-color: var(--primary-bg);
            color: var(--text-main);
            line-height: 1.6;
            padding: 40px 20px;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
            background: var(--card-bg);
            border-radius: 24px;
            padding: 48px;
            box-shadow: var(--shadow);
            position: relative;
        }

        /* Header */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 48px;
        }

        .header-info h1 {
            font-size: 32px;
            font-weight: 700;
            color: #0F172A;
            margin-bottom: 8px;
        }

        .header-info p {
            font-size: 18px;
            color: var(--text-muted);
        }

        .header-actions {
            display: flex;
            gap: 12px;
        }

        .badge {
            padding: 8px 16px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .badge-success {
            background-color: var(--accent-green-light);
            color: var(--accent-green);
        }

        .badge-success::before {
            content: '';
            width: 8px;
            height: 8px;
            background-color: var(--accent-green);
            border-radius: 50%;
        }

        .btn-print {
            background: white;
            border: 1px solid var(--border-color);
            padding: 8px 20px;
            border-radius: 12px;
            font-weight: 500;
            color: var(--text-main);
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
            text-decoration: none;
        }

        .btn-print:hover {
            background: #F1F5F9;
            transform: translateY(-1px);
        }

        /* Grid Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 48px;
        }

        .stat-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 20px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 120px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .stat-card:nth-child(1) { border-left: 4px solid var(--accent-green); }
        .stat-card:nth-child(2) { border-left: 4px solid #CBD5E1; }
        .stat-card:nth-child(3) { border-left: 4px solid var(--accent-blue); }
        .stat-card:nth-child(4) { border-left: 4px solid #F1F5F9; }

        .stat-label {
            font-size: 12px;
            font-weight: 600;
            color: var(--text-muted);
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .stat-icon {
            width: 28px;
            height: 28px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
        }

        .stat-icon.green { background: var(--accent-green-light); color: var(--accent-green); }
        .stat-icon.blue { background: var(--accent-blue-light); color: var(--accent-blue); }
        .stat-icon.gray { background: #F1F5F9; color: var(--text-muted); }

        .stat-value {
            font-size: 18px;
            font-weight: 700;
            color: #0F172A;
            line-height: 1.2;
        }

        .date-range {
            background: var(--accent-green-light);
            color: var(--accent-green);
            padding: 4px 10px;
            border-radius: 8px;
            font-size: 13px;
            display: inline-block;
            margin-top: 4px;
            letter-spacing: -0.5px;
        }

        /* Table Section */
        .table-section {
            background: #1E293B;
            border-radius: 20px;
            overflow: hidden;
            margin-bottom: 48px;
        }

        .table-header {
            padding: 24px 32px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: white;
        }

        .table-header h2 {
            font-size: 20px;
            font-weight: 600;
        }

        .vat-badge {
            background: rgba(255, 255, 255, 0.1);
            padding: 6px 16px;
            border-radius: 10px;
            font-size: 12px;
            font-weight: 500;
        }

        .pricing-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }

        .pricing-table th {
            text-align: right;
            padding: 16px 32px;
            font-size: 14px;
            font-weight: 600;
            color: var(--text-muted);
            background: #F8FAFC;
            border-bottom: 1px solid var(--border-color);
        }

        .pricing-table td {
            padding: 20px 32px;
            border-bottom: 1px solid #F1F5F9;
            font-size: 15px;
            color: #334155;
        }

        .pricing-table tr:hover td {
            background: #FBFCFE;
        }

        .service-type {
            color: var(--accent-blue);
            font-weight: 500;
        }

        .item-name {
            font-weight: 600;
            color: #0F172A;
        }

        .price-col {
            text-align: left;
            font-weight: 700;
            color: #0F172A;
            font-size: 18px;
        }

        .currency {
            font-size: 13px;
            font-weight: 500;
            color: var(--text-muted);
            margin-right: 4px;
        }

        /* Notes Section */
        .notes-section {
            background: #FFFBEB;
            border: 1px solid #FEF3C7;
            border-radius: 16px;
            padding: 24px;
        }

        .notes-title {
            color: #92400E;
            font-weight: 700;
            font-size: 16px;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .notes-list {
            list-style: none;
        }

        .notes-list li {
            position: relative;
            padding-right: 20px;
            margin-bottom: 12px;
            font-size: 14px;
            color: #92400E;
            line-height: 1.6;
        }

        .notes-list li::before {
            content: '•';
            position: absolute;
            right: 0;
            color: #F59E0B;
            font-weight: bold;
        }

        /* Print Override */
        @media print {
            body { background: white; padding: 0; }
            .container { box-shadow: none; border-radius: 0; padding: 0; width: 100%; max-width: 100%; }
            .btn-print { display: none !important; }
            .no-print { display: none !important; }
            .badge-success { background: none !important; border: 1px solid var(--accent-green); }
            .table-section { break-inside: avoid; }
            .stat-card { break-inside: avoid; }
        }

        @media (max-width: 768px) {
            .container { padding: 24px; }
            .header { flex-direction: column; gap: 24px; }
            .stats-grid { grid-template-columns: 1fr 1fr; }
            .table-header { padding: 16px; flex-direction: column; gap: 12px; align-items: flex-start; }
            .pricing-table th, .pricing-table td { padding: 12px 16px; }
        }
    </style>
</head>
<body>

    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="header-info">
                <h1>{{ trans('client.contract_print_title') }} B2B</h1>
                <p>{{ $contract->title }}</p>
            </div>
            <div class="header-actions">
                <div class="badge badge-success">
                    {{ trans('client.active_contract') }}
                </div>
                <button class="btn-print no-print" onclick="window.print()">
                    <i class="fas fa-print"></i>
                    {{ trans('client.print_contract') }}
                </button>
            </div>
        </div>

        <!-- Entity Details -->
        <div class="row mb-5" style="display: flex; gap: 20px; margin-bottom: 40px;">
            <div class="col-6" style="flex: 1; padding: 24px; background: #F8FAFC; border-radius: 16px; border: 1px solid var(--border-color);">
                <h5 style="color: var(--accent-blue); margin-bottom: 15px; font-weight: 700; border-bottom: 2px solid var(--accent-blue); padding-bottom: 8px; display: inline-block;">
                    Clean Station ({{ trans('client.provider') }})
                </h5>
                <div style="font-size: 14px;">
                    <p style="margin-bottom: 8px;"><strong>{{ trans('client.commercial_registration') }}:</strong> {{ setting('clean_station_commercial_registration') }}</p>
                    <p><strong>{{ trans('client.tax_number') }}:</strong> {{ setting('clean_station_tax_number') }}</p>
                </div>
            </div>
            <div class="col-6" style="flex: 1; padding: 24px; background: #F8FAFC; border-radius: 16px; border: 1px solid var(--border-color);">
                <h5 style="color: var(--accent-green); margin-bottom: 15px; font-weight: 700; border-bottom: 2px solid var(--accent-green); padding-bottom: 8px; display: inline-block;">
                    {{ $contract->client?->fullname }} ({{ trans('client.client') }})
                </h5>
                <div style="font-size: 14px;">
                    <p style="margin-bottom: 8px;"><strong>{{ trans('client.commercial_registration') }}:</strong> {{ $contract->commercial_registration ?? trans('client.not_set') }}</p>
                    <p><strong>{{ trans('client.tax_number') }}:</strong> {{ $contract->tax_number ?? trans('client.not_set') }}</p>
                </div>
            </div>
        </div>

        <!-- 4 Cards -->
        <div class="stats-grid">
            <!-- Validity Period -->
            <div class="stat-card">
                <div class="stat-label">
                    <div class="stat-icon green"><i class="fas fa-shield-alt"></i></div>
                    {{ trans('client.start_date') }}
                </div>
                <div class="stat-value">
                    <div class="date-range">
                        {{ $contract->start_date ? $contract->start_date->format('Y-m-d') : trans('client.not_set') }} 
                        <i class="fas fa-long-arrow-alt-left" style="margin: 0 4px;"></i> 
                        {{ $contract->end_date ? $contract->end_date->format('Y-m-d') : trans('client.not_set') }}
                    </div>
                </div>
            </div>

            <!-- Contract Duration -->
            <div class="stat-card">
                <div class="stat-label">
                    <div class="stat-icon gray"><i class="far fa-calendar-alt"></i></div>
                    {{ trans('client.months_count') }}
                </div>
                <div class="stat-value">
                    {{ $contract->months_count }} {{ trans('client.months') }}
                </div>
            </div>

            <!-- Payment Terms -->
            <div class="stat-card">
                <div class="stat-label">
                    <div class="stat-icon blue"><i class="fas fa-hand-holding-usd"></i></div>
                    {{ trans('client.payment_details') }}
                </div>
                <div class="stat-value" style="font-size: 15px; font-weight: 600; color: var(--accent-blue);">
                    {{-- Mapping if current fee exists or defaulting to standard B2B terms --}}
                    {{ $contract->month_fees > 0 ? trans('client.monthly') . ' (' . $contract->month_fees . ' ' . trans('SAR') . ')' : 'شهري (دفع أجل 30 يوم)' }}
                </div>
            </div>

            <!-- Contract ID -->
            <div class="stat-card">
                <div class="stat-label">
                    <div class="stat-icon gray"><i class="fas fa-hashtag"></i></div>
                    {{ trans('client.contract_number') }}
                </div>
                <div class="stat-value" style="color: var(--text-muted); font-family: monospace;">
                    #CS-2026-00{{ $contract->id }}#
                </div>
            </div>
        </div>

        <!-- Table Section -->
        <div class="table-section">
            <div class="table-header">
                <h2>{{ trans('client.product_prices') }} والخدمات المتفق عليها</h2>
                <div class="vat-badge">
                    الأسعار شاملة ضريبة القيمة المضافة 15%
                </div>
            </div>
            <table class="pricing-table">
                <thead>
                    <tr>
                        <th>{{ trans('client.service_type') }}</th>
                        <th>{{ trans('client.product_name') }}</th>
                        <th style="text-align: left;">{{ trans('client.price') }} شامل الضريبة</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($contract->contractPrices as $price)
                        <tr>
                            <td class="service-type">
                                {{-- Attempt to get Service Type from Category --}}
                                {{ $price?->product?->category?->name ?? trans('client.washing_and_ironing') }}
                            </td>
                            <td class="item-name">
                                {{ $price?->product?->name ?? trans('client.product_not_found') }}
                                @if($price?->product?->subCategory)
                                    <span style="font-size: 12px; font-weight: 400; color: var(--text-muted); display: block;">
                                        ({{ $price?->product?->subCategory?->name }})
                                    </span>
                                @endif
                            </td>
                            <td class="price-col">
                                {{ number_format($price->price, 2) }}
                                <span class="currency">{{ trans('SAR') }}</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Notes Section -->
        <div class="notes-section">
            <div class="notes-title">
                <i class="fas fa-info-circle"></i>
                ملاحظات هامة للتعاقد:
            </div>
            <ul class="notes-list">
                <li>يلتزم الطرف الأول (كلين ستيشن) بتوفير خدمات الغسيل حسب الجدول المتفق عليه.</li>
                <li>في حالة فقدان أو تلف أي قطعة بسبب الغسيل، يتم التعويض حسب سياسة الشركة المرفقة بالعقد الأساسي.</li>
                <li>الأسعار أعلاه خاصة للطلبات الفندقية (شراشف، مناشف، الخ) ولا تشمل طلبات النزلاء الخاصة الموضحة في قسم تسعيرة النزلاء.</li>
            </ul>
        </div>

    </div>

</body>
</html>
