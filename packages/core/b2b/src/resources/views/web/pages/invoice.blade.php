<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="utf-8">
    <title>فاتورة مبدئية | Proforma Invoice</title>
    <style>
        @font-face {
            font-family: 'Cairo';
            src: url('Cairo-Regular.ttf') format('truetype');
        }

        @page {
            size: A4 portrait;
            margin: 10mm 8mm;
        }

        html,
        body {
            background: #f6f7f9;
            margin: 0;
            padding: 0;
            color: #0b2239;
            font-family: 'Cairo', system-ui, -apple-system, 'Segoe UI', Roboto, Arial, sans-serif;
        }

        .sheet {
            width: 794px;
            margin: 24px auto;
            background: #fff;
            border: 1px solid #DBDEE3;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, .06);
        }

        .header {
            direction: ltr;
            display: grid;
            grid-template-columns: 220px 1fr;
            align-items: center;
            gap: 16px;
            padding: 32px 28px 16px 28px;
            background: linear-gradient(180deg, #FFFFFF 0%, #FAFBFE 100%);
        }

        .logo img {
            width: 200px;
            height: auto;
            display: block;
        }

        .brand {
            direction: rtl;
            text-align: right;
        }

        .brand .title-ar {
            font-size: 26px;
            font-weight: 800;
            color: #0f4da8;
            margin: 0;
            line-height: 1.2;
        }

        .brand .title-en {
            font-size: 14px;
            color: #6b7280;
            font-weight: 700;
            margin-top: 4px;
        }

        .brand .tag {
            font-size: 13px;
            color: #6b7280;
            margin-top: 2px;
        }

        .meta {
            width: calc(100% - 56px);
            margin: 0 28px 18px 28px;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
        }

        .card {
            background: #fff;
            border: 1px solid #DBDEE3;
            border-radius: 12px;
            padding: 10px 12px;
        }

        .card .k {
            font-size: 13px;
            color: #6b7280;
            display: flex;
            justify-content: space-between;
            line-height: 1.1;
        }

        .card .v {
            font-size: 14px;
            font-weight: 900;
            padding-top: 4px;
            line-height: 1.2;
        }

        table {
            width: calc(100% - 56px);
            margin: 0 28px 18px 28px;
            border-collapse: collapse;
            font-size: 14px;
        }

        thead th {
            background: #EEF2FF;
            color: #111827;
            font-weight: 800;
            border: 1px solid #E4E6EB;
            padding: 12px;
        }

        tbody td {
            border: 1px solid #E4E6EB;
            padding: 12px;
            text-align: center;
            vertical-align: middle;
        }

        td.name {
            text-align: right;
        }

        .suben {
            font-size: 12px;
            color: #6b7280;
            margin-inline-start: 8px;
        }

        .bottom {
            width: calc(100% - 56px);
            margin: 0 28px 24px 28px;
            display: grid;
            grid-template-columns: 1fr 320px;
            gap: 16px;
            align-items: start;
        }

        .pay .title {
            margin: 0 0 8px;
            font-size: 16px;
            font-weight: 800;
        }

        .paygrid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }

        .pill {
            background: #F8FAFC;
            border: 1px solid #E5E7EB;
            border-radius: 10px;
            padding: 12px;
        }

        .pill .k {
            font-size: 12px;
            color: #6b7280;
            display: flex;
            justify-content: space-between;
        }

        .pill .v {
            font-size: 15px;
            font-weight: 700;
        }

        .summary {
            border: 1px solid #DBDEE3;
            border-radius: 12px;
            padding: 12px;
        }

        .summary .row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            font-size: 14px;
        }

        .summary .row.total {
            border-top: 1px solid #E4E6EB;
            margin-top: 8px;
            padding-top: 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 14px;
            font-weight: 700;
            color: #0b2239;
            white-space: nowrap;
        }

        .summary .row.total .label-en {
            direction: ltr;
        }

        .summary .row.total .amount {
            margin: 0 24px;
            font-weight: 700;
        }

        /* Print styles - ensure content fits on page */
        @media print {

            #print-btn,
            #print-btn-container {
                display: none !important;
            }

            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            html,
            body {
                background: #fff;
                margin: 0;
                padding: 0;
                width: 100%;
                height: 100%;
            }

            .sheet {
                width: 100%;
                max-width: 100%;
                margin: 0;
                padding: 0;
                border: none;
                border-radius: 0;
                box-shadow: none;
                page-break-after: avoid;
            }

            .header {
                padding: 16px 20px 12px 20px;
                page-break-inside: avoid;
            }

            .logo img {
                width: 150px;
                max-width: 150px;
            }

            .brand .title-ar {
                font-size: 22px;
            }

            .brand .title-en {
                font-size: 12px;
            }

            .brand .tag {
                font-size: 11px;
            }

            .meta {
                width: calc(100% - 40px);
                margin: 0 20px 12px 20px;
                gap: 12px;
                page-break-inside: avoid;
            }

            .card {
                padding: 8px 10px;
            }

            .card .k {
                font-size: 11px;
            }

            .card .v {
                font-size: 12px;
            }

            table {
                width: calc(100% - 40px);
                margin: 0 20px 12px 20px;
                font-size: 12px;
                page-break-inside: auto;
            }

            thead {
                display: table-header-group;
            }

            thead th {
                padding: 8px;
                font-size: 11px;
            }

            tbody td {
                padding: 8px;
                font-size: 11px;
            }

            tbody tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }

            .suben {
                font-size: 10px;
            }

            .bottom {
                width: calc(100% - 40px);
                margin: 0 20px 16px 20px;
                gap: 12px;
                page-break-inside: avoid;
            }

            .pay .title {
                font-size: 14px;
                margin-bottom: 6px;
            }

            .paygrid {
                gap: 8px;
            }

            .pill {
                padding: 8px 10px;
            }

            .pill .k {
                font-size: 10px;
            }

            .pill .v {
                font-size: 13px;
            }

            .summary {
                padding: 10px;
            }

            .summary .row {
                padding: 6px 0;
                font-size: 12px;
            }

            .summary .row.total {
                padding-top: 8px;
                margin-top: 6px;
                font-size: 13px;
            }

            .summary .row.total .amount {
                margin: 0 12px;
            }
        }
        .text-danger {
            color: #dc3545 !important;
        }
    </style>
</head>

<body>
    <!-- Print Button -->
    <div id="print-btn-container" style="text-align: left; margin: 24px 28px 0 28px;">
        <button id="print-btn" onclick="handlePrint()"
            style="padding: 8px 20px; font-size: 15px; font-weight: 700; background: #0f4da8; color: #fff; border: none; border-radius: 8px; cursor: pointer;">
            🖨️ طباعة الفاتورة | Print Invoice
        </button>
    </div>
    <section class="sheet">
        <div class="header">
            <div class="logo"><img src="{{ config('app.logo') }}" alt="Clean Station logo"></div>
            <div class="brand">
                <h1 class="title-ar">فاتورة مبدئية</h1>
                <div class="title-en">Proforma Invoice</div>
                <div class="tag">Clean Station — Clean just click</div>
            </div>
        </div>

        <section class="meta">
            <div class="card">
                <div class="k"><span>رقم الفاتورة</span><span>Invoice No</span></div>
                <div class="v">{{ $order->reference_id ?? $order->id }}</div>
            </div>
            <div class="card">
                <div class="k"><span>رقم الطلب</span><span>Order ID</span></div>
                <div class="v">{{ $order->order_number ?? $order->id }}</div>
            </div>
            <div class="card">
                <div class="k"><span>تاريخ الاصدار</span><span>Invoice Date</span></div>
                <div class="v">{{ $order->created_at->format('Y-m-d') }}</div>
            </div>
            <div class="card">
                <div class="k"><span> العميل</span><span>Customer</span></div>
                <div class="v">{{ $order->client->fullname ?? 'N/A' }}</div>
            </div>
            <div class="card">
                <div class="k"><span>الجوال</span><span>Mobile</span></div>
                <div class="v">{{ $order->client->phone ?? 'N/A' }}</div>
            </div>
            <div class="card">
                <div class="k"><span>العنوان </span><span>Address</span></div>
                <div class="v">
                    {{ $order->orderRepresentatives()->where('type', 'delivery')->first()?->location . ' || ' . ($order->city->name ?? 'N/A') }}
                </div>
            </div>

        </section>

        <table>
            <thead>
                <tr>
                    <th>العنصر | Item</th>
                    <th>القسم الفرعي | Subcategory</th>
                    <th>سعر القطعة | Unit</th>
                    <th>الكمية | Qty</th>
                    <th>الإجمالي | Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->items ?? [] as $item)
                    <tr>
                        <td class="name">
                            {{ $item->product->translate('en')?->name . ' | ' . $item->product->translate('ar')?->name }}
                            <span class="suben">{{ $item->product_name_en ?? '' }}</span>

                            @if($item->width and $item->height)
                            <br>
                                <span class="suben" dir="ltr">Size: {{ $item->width." M" }} × {{ $item->height." M" }} = {{ $item->width * $item->height }} M²</span>
                            @endif
                        </td>
                        <td>{{ $item->product?->subcategory?->translate('en')?->name . ' | ' . $item->product?->subcategory?->translate('ar')?->name ?? '' }}
                        </td>
                        <td>{{ number_format($item->product_price, 2) }} ر.س</td>
                        <td>{{ $item->quantity }}</td>
                        <td>
                            {{ number_format($item->product_price * $item->quantity * ($item->width ?? 1) * ($item->height ?? 1), 2) }}
                            ر.س</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="bottom">
            <div class="pay">
                <div class="title">تفاصيل الدفع | Payment Details</div>
                <div class="paygrid">
                    <div class="pill">
                        <div class="k"><span>المبلغ</span><span>Amount</span></div>
                        <div class="v">{{ number_format($order->total_price, 2) }} ر.س</div>
                    </div>
                    <div class="pill">
                        <div class="k"><span>طريقة الدفع</span><span>Method</span></div>
                        <div class="v">
                            {{ $order->pay_type ?? ($order->pay_type == 'cash' ? 'نقدًا (Cash)' : $order->pay_type) }}
                            {{ $order->online_payment_method ?? ($order->online_payment_method == 'cash' ? '' : $order->online_payment_method) }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="summary">
                <div class="row">
                    <div class="label">المجموع الفرعي | Subtotal</div>
                    <div class="val">{{ number_format($order->order_price ?? 0, 2) }} ر.س</div>
                </div>

                @if($order->total_coupon > 0)
                    <div class="row">
                        <div class="label text-danger">
                            الخصم | Discount
                           
                        </div>
                            
                        <div class="val text-danger">-{{ number_format($order->total_coupon ?? 0, 2) }} ر.س</div>
                    </div>
                @endif
                <div class="row">
                    <div class="label">رسوم التوصيل | Delivery</div>
                    <div class="val">
                        {{ $order->delivery_price > 0 ? number_format($order->delivery_price, 2) . ' ر.س' : 'مجاني | Free' }}
                    </div>
                </div>
                <div class="row total">
                    <div class="label-en">Grand Total</div>
                    <div class="amount">{{ number_format($order->total_price, 2) }} ر.س</div>
                    <div class="label-ar">الإجمالي الكلي</div>
                </div>
            </div>
        </div>
    </section>
    <script>
        function handlePrint() {
            var btn = document.getElementById('print-btn');
            if (btn) btn.style.display = 'none';
            window.print();
            // After printing, show the button again (for browsers that support onafterprint)
            if (btn) {
                if ('onafterprint' in window) {
                    window.onafterprint = function() {
                        btn.style.display = '';
                    };
                } else {
                    // Fallback: show after a short delay
                    setTimeout(function() {
                        btn.style.display = '';
                    }, 1000);
                }
            }
        }
    </script>
</body>

</html>
