
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نظام إدارة المبيعات - كلين ستيشن</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Cairo', sans-serif;
            background-color: #f3f4f6;
            -webkit-print-color-adjust: exact;
            color-adjust: exact;
        }
        
        /* تصميم حقول الإدخال */
        [contenteditable="true"] { outline: none; transition: all 0.2s; border-radius: 4px; }
        [contenteditable="true"]:hover, [contenteditable="true"]:focus { background-color: #f0f9ff; box-shadow: 0 0 0 2px #bae6fd; }
        
        th, td { padding-top: 0.35rem; padding-bottom: 0.35rem; }
        
        /* إخفاء القوائم عند التبديل */
        .tab-content { display: none; }
        .tab-content.active { display: block; }
        
        /* تصميم خاص لجدول كشف الحساب */
        .stat-table th, .stat-table td { padding: 0.35rem 0.5rem !important; border: 1px solid #e5e7eb; font-size: 11px; text-align: center; }
        .stat-table thead th { background-color: #1f2937; color: white; border-color: #374151; }
        
        /* --- أزرار الحذف الخارجية --- */
        .row-wrapper {
            position: relative;
        }
        .delete-btn {
            position: absolute;
            right: -25px; /* خارج الجدول من اليمين */
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #fca5a5;
            font-size: 12px;
            padding: 4px;
        }
        .delete-btn:hover {
            color: #ef4444;
        }
        
        /* --- إعدادات الطباعة العامة --- */
        @media print {
            body { background-color: white; padding: 0 !important; font-size: 12px !important; }
            .no-print { display: none !important; }
            .hide-on-print { display: none !important; }
            
            /* إصلاح مشكلة انكماش العرض وتشوه الجداول */
            .print-container { 
                box-shadow: none !important; 
                border: none !important; 
                margin: 0 auto !important; 
                padding: 0 !important; 
                width: 100% !important; 
                max-width: none !important;
            }
            /* تعطيل السحب الجانبي لمنع تصغير محتوى الطباعة */
            .overflow-x-auto { overflow: visible !important; }

            [contenteditable="true"] { box-shadow: none !important; background-color: transparent !important; }
            
            /* ضغط المسافات للطباعة */
            .mb-8 { margin-bottom: 0.5rem !important; }
            .mb-6 { margin-bottom: 0.25rem !important; }
            .mb-4 { margin-bottom: 0.25rem !important; }
            .pb-4, .pb-3 { padding-bottom: 0.25rem !important; }
            .p-4, .p-3, .p-6 { padding: 0.25rem !important; }
            .pt-8 { padding-top: 0.5rem !important; }
            .mt-6 { margin-top: 0.25rem !important; }
            .mt-4 { margin-top: 0.15rem !important; }

            th, td { padding-top: 0.15rem !important; padding-bottom: 0.15rem !important; font-size: 0.75rem !important; }
            tr { break-inside: avoid; page-break-inside: avoid; }
            
            .print-footer { position: fixed !important; bottom: 0 !important; left: 0; right: 0; background-color: white !important; z-index: 50; }
            
            /* إخفاء أزرار الحذف الخارجية في الطباعة */
            .delete-btn { display: none !important; }
        }

        body.printing-invoice @page { size: A4; margin: 8mm; }
        body.printing-invoice .invoice-view { display: block !important; }
        
        body.printing-statement * { line-height: 1.2 !important; }
        
        /* إجبار شريط بيانات العميل الحديث على البقاء أفقياً في الطباعة */
        body.printing-statement .compact-info-bar {
            display: flex !important;
            flex-direction: row !important;
            justify-content: space-between !important;
            align-items: center !important;
            padding: 8px !important;
            margin-bottom: 10px !important;
            background-color: #f9fafb !important;
            border: 1px solid #f3f4f6 !important;
            border-radius: 6px !important;
            page-break-inside: avoid !important;
        }
        body.printing-statement .compact-info-bar > div {
            display: flex !important;
            flex-direction: column !important;
        }
        body.printing-statement .info-divider {
            display: block !important;
            width: 1px !important;
            height: 20px !important;
            background-color: #e5e7eb !important;
        }

        /* تثبيت قياسات الجدول بشكل صارم جداً */
        body.printing-statement .stat-table {
            width: 100% !important;
            table-layout: fixed !important;
            border-collapse: collapse !important;
        }
        body.printing-statement .stat-table th, 
        body.printing-statement .stat-table td { 
            font-size: 8pt !important; /* خط صغير جداً ليتسع */
            padding: 2px !important; 
            word-wrap: break-word !important;
            overflow: hidden !important;
        }
        
        /* تثبيت الشعارات العلوية لمنع تشوهها */
        body.printing-statement .header-logos img {
            max-height: 40px !important;
            width: auto !important;
        }

        /* تصغير منطقة التوقيع والختم */
        body.printing-statement .signature-area {
            display: flex !important;
            flex-direction: row !important;
            justify-content: space-between !important;
            page-break-inside: avoid !important;
            margin-top: 5px !important;
        }
        body.printing-statement .stamp-img {
            height: 60px !important;
            bottom: -5px !important;
        }
        body.printing-statement .print-footer { bottom: 0 !important; }
    </style>
</head>
<body class="py-4 md:py-8 overflow-x-hidden"> <!-- منع التمرير الأفقي للصفحة ككل -->

    <!-- شريط التحكم والأدوات (لا يظهر في الطباعة) -->
    <div class="max-w-4xl mx-auto mb-6 bg-white p-4 rounded-xl shadow-md flex flex-col md:flex-row justify-between items-center gap-4 no-print border-t-4 border-[#00AEEF]">
        
        <div></div>

        <!-- أزرار الطباعة -->
        <button onclick="printDocument()" class="w-full md:w-auto bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-bold shadow flex justify-center items-center gap-2 transition" id="main-print-btn">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5 4v3H4a2 2 0 00-2 2v3a2 2 0 002 2h1v2a2 2 0 002 2h6a2 2 0 002-2v-2h1a2 2 0 002-2V9a2 2 0 00-2-2h-1V4a2 2 0 00-2-2H7a2 2 0 00-2 2zm8 0H7v3h6V4zm0 8H7v4h6v-4z" clip-rule="evenodd" /></svg>
            <span id="print-text">طباعة الفاتورة</span>
        </button>
    </div>


    <div id="invoice-tab" class="invoice-view">

        <div class="print-container max-w-4xl mx-auto bg-white p-6 md:p-10 shadow-2xl rounded-xl border-t-8 border-[#00AEEF] relative overflow-hidden">
            <div class="flex justify-between items-start border-b-2 border-gray-100 pb-3 mb-4">
                <div class="text-right">
                    <h2 contenteditable="true" id="invoice-title" class="text-xl font-bold text-gray-800 mb-2">TAX INVOICE | فاتورة ضريبية</h2>
                    <div class="flex gap-4 text-xs text-gray-600 justify-end font-medium whitespace-nowrap" dir="ltr">
                        <div><span class="font-semibold text-[#00AEEF]">Invoice No:</span> <span contenteditable="true">INV-2026-001</span></div>
                        <div><span class="text-gray-300">|</span></div>
                        <div><span class="font-semibold text-[#00AEEF]">Date:</span> <span contenteditable="true" class="auto-date"></span></div>
                        <div><span class="text-gray-300">|</span></div>
                        <div><span class="font-semibold text-[#00AEEF]">Due Date:</span> <span contenteditable="true">26 Mar 2026</span></div>
                    </div>
                </div>
                <div class="flex items-center gap-4 header-logos">
                    {!! $qrCodeImage !!}
                    <div class="h-10 border-r-2 border-gray-200"></div>
                    <div class="flex flex-col justify-center items-center mt-1">
                        <span contenteditable="true" class="text-[8px] text-gray-400 font-bold mb-0 tracking-wider whitespace-nowrap">مدعوم من</span>
                        <img src="https://i.postimg.cc/x1YqjNZQ/lwqww-hwfw-(1)-(1).png" alt="هوفو سيستم" class="h-7 w-auto object-contain opacity-80">
                    </div>
                    <div class="h-10 border-r-2 border-gray-200"></div>
                    <img src="https://i.postimg.cc/Cx8YsGLw/lwqw-msttyl-2-(1).png" alt="كلين ستيشن" class="h-20 w-auto object-contain">
                
                </div>
            </div>

            <div class="flex justify-between text-gray-800 mb-4 border-b border-gray-100 pb-3">
                <div class="w-1/2 pr-2 border-l border-gray-100 text-right">
                    @php 
                        $companyName = \Core\Settings\Services\SettingsService::getDataBaseSetting('name_en') ?: 'CleanStation';
                        $companyNameAr = \Core\Settings\Services\SettingsService::getDataBaseSetting('name_ar') ?: 'كلين ستيشن';
                        $companyVat = \Core\Settings\Services\SettingsService::getDataBaseSetting('clean_station_tax_number') ?: '300000000000003';
                    @endphp
                    <h3 contenteditable="true" class="text-[10px] font-bold text-[#00AEEF] uppercase tracking-wider mb-1">البائع / Seller:</h3>
                    <p contenteditable="true" class="font-bold text-base leading-tight">{{ $companyName }} / {{ $companyNameAr }}</p>
                    <p class="text-sm mt-1">الرقم الضريبي: <span contenteditable="true" class="font-semibold">{{ $companyVat }}</span></p>
                </div>
                <div class="w-1/2 pl-4 text-right">
                    <h3 contenteditable="true" class="text-[10px] font-bold text-[#00AEEF] uppercase tracking-wider mb-1">فاتورة إلى / Billed To:</h3>
                    <p contenteditable="true" class="font-bold text-base leading-tight">{{ $invoice->order?->company?->fullname ?? $invoice->order?->client?->fullname }}</p>
                    <p class="text-sm mt-1 whitespace-nowrap"><span contenteditable="true">عناية:</span> <span contenteditable="true" class="font-semibold">{{ $invoice->order?->client?->fullname }}</span></p>
                    <p contenteditable="true" class="text-sm text-gray-500 mt-1">{{ $invoice->order?->client?->address }}</p>
                </div>
            </div>

            <div class="mb-4 relative">
                <h3 contenteditable="true" class="text-base font-bold text-gray-800 mb-2 border-b-2 border-[#00AEEF] inline-block pb-1">تفاصيل الفاتورة | Invoice Details</h3>
                <div class="overflow-x-visible">
                    <table class="w-full text-left border-collapse inv-table">
                        <thead>
                            <tr class="bg-gray-800 text-white text-xs">
                                <th class="py-1 px-2 rounded-tr-lg text-right w-8">#</th>
                                <th class="py-1 px-2 text-right border-r border-gray-600 whitespace-nowrap">البيان / Description</th>
                                <th class="py-1 px-2 text-center border-r border-gray-600 w-16">الكمية/Qty</th>
                                <th class="py-1 px-2 text-center border-r border-gray-600 w-24 leading-tight">سعر الوحدة<br><span class="text-[9px] text-gray-300 font-normal">Unit Price</span></th>
                                <th class="py-1 px-2 text-center border-r border-gray-600 w-20 leading-tight">الضريبة 15 %<br><span class="text-[9px] text-gray-300 font-normal">Disc.</span></th>
                                <th class="py-1 px-2 rounded-tl-lg text-center bg-[#00AEEF] w-28 leading-tight">المجموع<br><span class="text-[9px] text-[#e0f7fa] font-normal">Total</span></th>
                            </tr>
                        </thead>
                        @php
                            $totalBeforeTax = 0;
                            $totalTax = 0;
                            $total = 0;
                        @endphp
                        <tbody class="text-sm text-gray-700">
                            @foreach($invoice?->order?->items ?? [] as $index => $item)
                            @php
                                $price = $item->product_price;
                                $tax = (15 * $price / 100);
                                $beforeTax = $price - $tax;
                                $totalBeforeTax += $beforeTax * $item->quantity;
                                $totalTax += $tax * $item->quantity;
                                $total += $price * $item->quantity;
                            @endphp
                            <tr class="inv-row border-b border-gray-200 hover:bg-gray-50 transition {{ ($index % 2 != 0) ? 'bg-gray-50/50' : '' }}">
                                <td class="px-2 text-right font-bold text-gray-400">{{ $index + 1 }}</td>
                                <td class="px-2 text-right font-semibold leading-tight">
                                    <div contenteditable="true">{{ $item->product?->translate('ar')->name . ' ' . $item->product?->translate('en')->name }}</div>
                                    <div class="text-gray-400 text-[10px]">{{ $item->product?->translate('en')->name ?? $item->product?->sku ?? '' }}</div>
                                </td>
                                <td class="px-2 text-center text-gray-500">
                                    <span contenteditable="true" class="qty font-semibold">{{ $item->quantity }}</span>
                                </td>
                                <td class="px-2 text-center">
                                    <span contenteditable="true" class="base-price font-semibold">{{ number_format($beforeTax * $item->quantity, 2, '.', '') }}</span> 
                                    <span class="text-[10px]">ر.س</span>
                                </td>
                                <td class="px-2 text-center text-red-500">
                                    <span contenteditable="true" class="discount font-semibold">{{number_format($tax * $item->quantity, 2, '.', '')}}</span> ر.س
                                </td>
                                <td class="px-2 text-center font-bold text-[#00AEEF] bg-[#00AEEF]/5">
                                    <span class="final-price text-base">{{ number_format($price * $item->quantity, 2, '.', '') }}</span> 
                                    <span class="text-[10px]">ر.س</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="border-t-2 border-gray-800 text-sm">
                            <tr>
                                <td colspan="5" class="py-1 px-3 text-left font-semibold text-gray-600" id="sub-total-label">المجموع الفرعي (غير شامل الضريبة) | Subtotal:</td>
                                <td class="py-1 px-3 text-center font-bold text-gray-700"><span id="sub-total">{{ number_format($totalBeforeTax, 2, '.', '') }}</span> <span class="text-[10px]">ر.س</span></td>
                            </tr>
                            <tr id="vat-row">
                                <td colspan="5" class="py-1 px-3 text-left font-semibold text-gray-600 border-t border-gray-200">ضريبة القيمة المضافة (15%) | VAT 15%:</td>
                                <td class="py-1 px-3 text-center font-bold text-red-500 border-t border-gray-200">+ <span id="vat-amount">{{ number_format($totalTax, 2, '.', '') }}</span> <span class="text-[10px]">ر.س</span></td>
                            </tr>
                            <tr class="bg-[#00AEEF]/10">
                                <td colspan="5" class="py-2 px-3 text-left font-extrabold text-[#00AEEF] border-t-2 border-[#00AEEF]">الإجمالي النهائي المستحق | Grand Total:</td>
                                <td class="py-2 px-3 text-center font-extrabold text-[#00AEEF] border-t-2 border-[#00AEEF] text-lg"><span id="grand-total">{{ number_format($total, 2, '.', '') }}</span> <span class="text-[10px]">ر.س</span></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="flex justify-between gap-4 mt-2">
                <div class="w-1/2 bg-gray-50 border border-gray-200 p-2 rounded-lg">
                    <h3 contenteditable="true" class="text-sm font-bold text-gray-800 mb-1 border-b border-[#00AEEF] inline-block pb-1">طرق الدفع | Payment</h3>
                    <ul class="space-y-1 text-[10px] text-gray-700 list-disc list-inside">
                        <li><strong class="text-gray-800">اسم العميل:</strong> <span contenteditable="true" class="font-bold border-b border-dashed border-gray-300">{{ $invoice->order?->company?->fullname ?? $invoice->order?->client?->fullname }}</span></li>
                        <li><strong class="text-gray-800">رقم الحساب الجاري:</strong> <span contenteditable="true" class="font-bold border-b border-dashed border-gray-300">{{ $invoice->order?->company?->bank_account_number ?? $invoice->order?->client?->bank_account_number }}</span></li>
                        <li><strong class="text-gray-800">الآيبان (IBAN):</strong> <span contenteditable="true" class="font-bold border-b border-dashed border-gray-300" dir="ltr">{{ $invoice->order?->company?->iban ?? $invoice->order?->client?->iban }}</span></li>
                    </ul>
                </div>
                <div class="w-1/2 flex justify-between pt-2 border-t border-gray-100 signature-area">
                    <div class="w-1/2 text-center pr-2">
                        <h4 contenteditable="true" class="font-bold text-gray-800 text-[10px] mb-1">الختم / Stamp</h4>
                        <div class="relative h-16 mt-6 w-3/4 mx-auto flex justify-center items-end border-b border-dashed border-gray-300 mb-1 stamp-container">
                            <img src="https://i.postimg.cc/cLmC5rHf/Adobe-Express-file.png" alt="ختم هوفو سيستم" class="h-28 w-auto absolute bottom-[-20px] pointer-events-none mix-blend-multiply opacity-90 -rotate-6 stamp-img">
                        </div>
                        <p class="text-[9px] text-gray-400 mt-1">شركة هوفو سيستم</p>
                    </div>
                    <div class="w-1/2 text-center pl-2 border-r border-gray-100">
                        <h4 contenteditable="true" class="font-bold text-gray-800 text-[10px] mb-1">الاستلام / Received By</h4>
                        <div class="relative h-16 mt-6 w-3/4 mx-auto flex justify-center items-end border-b border-dashed border-gray-300 mb-1 stamp-container"></div>
                        <p class="text-[9px] text-gray-400 mt-1">التوقيع / Signature</p>
                    </div>
                </div>
            </div>

            <!-- تذييل ثابت للفاتورة -->
            <div class="print-footer mt-4 w-full bg-white pb-2">
                <div class="h-[4px] w-full bg-gradient-to-l from-[#00AEEF] via-blue-400 to-transparent mb-2 rounded-full print-color-adjust"></div>
                <div class="flex justify-between items-end">
                    <div class="flex items-center gap-4 header-logos">
                        <img src="https://i.postimg.cc/x1YqjNZQ/lwqww-hwfw-(1)-(1).png" alt="هوفو سيستم" class="h-6 w-auto object-contain">
                        <div class="border-r-2 border-gray-200 pr-3">
                            <p class="text-[10px] font-bold text-gray-800 leading-none mb-1">مدعوم تقنياً وتشغيلياً بواسطة شركة هوفو سيستم</p>
                            <p class="text-[8px] text-gray-500 leading-none">بالتقنية نصنع إتقان</p>
                        </div>
                    </div>
                    <div class="text-left text-[10px] text-gray-600 font-semibold leading-tight" dir="ltr">
                        <p class="mb-1 text-[#00AEEF]">www.cleanstation.app | support@cleanstation.app</p>
                        <p class="text-[7px] text-gray-400">© 2026 Powered by Hofo System. All rights reserved.</p>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>


    <!-- ========================================== -->
    <!-- سكربتات التحكم والمنطق البرمجي -->
    <!-- ========================================== -->
    <script>
        const currentTab = 'invoice';

        // دالة الطباعة الذكية
        window.printDocument = function() {
            document.body.className = 'printing-invoice';
            
            document.querySelectorAll('.inv-row').forEach(row => {
                const qty = parseFloat(row.querySelector('.qty')?.innerText) || 0;
                const finalPrice = parseFloat(row.querySelector('.final-price')?.innerText) || 0;
                if (finalPrice === 0 || qty === 0) {
                    row.classList.add('hide-on-print');
                }
            });

            window.print();

            setTimeout(() => {
                document.body.className = 'py-4 md:py-8 overflow-x-hidden';
                document.querySelectorAll('.hide-on-print').forEach(el => el.classList.remove('hide-on-print'));
            }, 100);
        };

     
        
    </script>
</body>
</html>
