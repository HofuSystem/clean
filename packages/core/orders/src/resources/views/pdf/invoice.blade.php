<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <title>فاتورة ضريبية - {{ $invoice->invoice_number }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 20px;
        }
        .logo {
            width: 150px;
        }
        .company-info {
            text-align: left;
        }
        .invoice-details {
            margin-bottom: 30px;
        }
        .invoice-details table {
            width: 100%;
        }
        .invoice-details td {
            padding: 5px 0;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .table th, .table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }
        .table th {
            background-color: #f8f9fa;
        }
        .totals {
            width: 300px;
            margin-right: auto;
        }
        .totals table {
            width: 100%;
            border-collapse: collapse;
        }
        .totals td {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }
        .totals .grand-total {
            font-weight: bold;
            background-color: #f8f9fa;
        }
        .qr-code {
            margin-top: 30px;
            text-align: center;
        }
        .qr-code img {
            width: 120px;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="header">
        <table style="width: 100%;">
            <tr>
                <td style="width: 50%;">
                    @php 
                        $logo = \Core\Settings\Services\SettingsService::getDataBaseSetting('logo'); 
                    @endphp
                    @if($logo)
                        <img src="{{ public_path('storage/' . $logo) }}" class="logo" alt="Logo">
                    @else
                        <h1>CleanStation</h1>
                    @endif
                </td>
                <td style="width: 50%; text-align: left;">
                    <h2 style="margin-top: 0; color: #007bff;">فاتورة ضريبية مبسطة</h2>
                    <p style="margin: 0; font-size: 14px;">الرقم الضريبي للمنشأة: {{ \Core\Settings\Services\SettingsService::getDataBaseSetting('company_vat') ?: '300000000000003' }}</p>
                </td>
            </tr>
        </table>
    </div>

    <div class="invoice-details">
        <table style="width: 100%;">
            <tr>
                <td style="width: 50%;">
                    <strong>بيانات العميل:</strong><br>
                    الاسم: {{ $order->client?->fullname }}<br>
                    الهاتف: {{ $order->client?->phone }}<br>
                    @if($invoice->type === 'B2B')
                        الرقم الضريبي: {{ $taxNumber ?? 'N/A' }}
                    @endif
                </td>
                <td style="width: 50%; text-align: left;">
                    رقم الفاتورة: <strong>{{ $invoice->invoice_number }}</strong><br>
                    تاريخ الفاتورة: {{ $invoice->created_at->format('Y-m-d H:i') }}<br>
                    رقم الطلب: #{{ $order->reference_id }}
                </td>
            </tr>
        </table>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>الخدمة</th>
                <th>الكمية</th>
                <th>سعر الوحدة</th>
                <th>المجموع (شامل الضريبة)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
                <tr>
                    <td>{{ $item->product?->name }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ number_format($item->product_price, 2) }} ر.س</td>
                    <td>{{ number_format($item->total_price, 2) }} ر.س</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <table>
            <tr>
                <td>المجموع الفرعي (غير شامل ضريبة القيمة المضافة)</td>
                <td style="text-align: left;">{{ number_format($invoice->subtotal, 2) }} ر.س</td>
            </tr>
            <tr>
                <td>ضريبة القيمة المضافة (15%)</td>
                <td style="text-align: left;">{{ number_format($invoice->vat_amount, 2) }} ر.س</td>
            </tr>
            <tr>
                <td>سعر التوصيل</td>
                <td style="text-align: left;">{{ number_format($invoice->delivery_price, 2) }} ر.س</td>
            </tr>
            @if($invoice->total_coupon > 0)
            <tr>
                <td>إجمالي الخصم</td>
                <td style="text-align: left;">-{{ number_format($invoice->total_coupon, 2) }} ر.س</td>
            </tr>
            @endif
            <tr class="grand-total">
                <td><strong>الإجمالي (شامل ضريبة القيمة المضافة)</strong></td>
                <td style="text-align: left;"><strong>{{ number_format($invoice->total_price, 2) }} ر.س</strong></td>
            </tr>
        </table>
    </div>

    <div class="qr-code">
        <div style="width: 120px; margin: 0 auto;">
            {!! $qrCodeImage !!}
        </div>
        <p style="font-size: 10px; margin-top: 5px;">مسح الرمز للتحقق من الفاتورة</p>
    </div>

    <div class="footer">
        <p>شكراً لتعاملكم معنا - CleanStation</p>
        <p>هذه الفاتورة تم إنشاؤها آلياً وغير مطلوبة التوقيع</p>
    </div>
</body>
</html>
