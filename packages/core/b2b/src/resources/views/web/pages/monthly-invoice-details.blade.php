@php
    $monthName = \Carbon\Carbon::createFromDate($year, $month, 1)->locale(app()->getLocale())->translatedFormat('F');
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ trans('client.monthly_account_statement') }} - {{ trans('client.welcome_message') }}</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet" />

    <style>
        body {
            font-family: 'Cairo', sans-serif;
            background-color: #f3f4f6;
            -webkit-print-color-adjust: exact;
            color-adjust: exact;
        }



        .stat-table th,
        .stat-table td {
            padding: 0.35rem 0.5rem !important;
            border: 1px solid #e5e7eb;
            font-size: 11px;
            text-align: center;
        }

        .stat-table thead th {
            background-color: #1f2937;
            color: white;
            border-color: #374151;
        }

        .delete-btn {
            cursor: pointer;
            color: #fca5a5;
            font-size: 12px;
            padding: 4px 6px;
            border-radius: 4px;
            transition: 0.2s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .delete-btn:hover {
            color: #ef4444;
            background-color: #fee2e2;
        }

        @media print {
            body {
                background-color: white;
                padding: 0 !important;
                font-size: 12px !important;
            }

            .no-print {
                display: none !important;
            }

            .print-container {
                box-shadow: none !important;
                border: none !important;
                margin: 0 auto !important;
                padding: 0 !important;
                width: 100% !important;
                max-width: none !important;
            }



            .mb-8 { margin-bottom: 0.5rem !important; }
            .mb-6 { margin-bottom: 0.25rem !important; }
            .mb-4 { margin-bottom: 0.25rem !important; }
            .pb-4, .pb-3 { padding-bottom: 0.25rem !important; }
            .p-4, .p-3, .p-6 { padding: 0.25rem !important; }
            .pt-8 { padding-top: 0.5rem !important; }
            .mt-6 { margin-top: 0.25rem !important; }
            .mt-4 { margin-top: 0.15rem !important; }

            tr {
                break-inside: avoid;
                page-break-inside: avoid;
            }

            .print-footer {
                position: fixed !important;
                bottom: 0 !important;
                left: 0;
                right: 0;
                background-color: white !important;
                z-index: 50;
            }

            @page {
                size: A4 portrait;
                margin: 5mm;
            }

            * {
                line-height: 1.2 !important;
            }

            .compact-info-bar {
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

            .compact-info-bar > div {
                display: flex !important;
                flex-direction: column !important;
            }

            .info-divider {
                display: block !important;
                width: 1px !important;
                height: 20px !important;
                background-color: #e5e7eb !important;
            }

            .stat-table {
                width: 100% !important;
                table-layout: fixed !important;
                border-collapse: collapse !important;
            }

            .stat-table th,
            .stat-table td {
                font-size: 8pt !important;
                padding: 2px !important;
                word-wrap: break-word !important;
                overflow: hidden !important;
            }

            .header-logos img {
                max-height: 40px !important;
                width: auto !important;
            }

            .signature-area {
                display: flex !important;
                flex-direction: row !important;
                justify-content: space-between !important;
                page-break-inside: avoid !important;
                margin-top: 5px !important;
            }

            .stamp-img {
                height: 60px !important;
                bottom: -5px !important;
            }
        }
    </style>
</head>
<body class="py-4 md:py-8 overflow-x-hidden">

    <!-- شريط الأدوات -->
    <div class="max-w-4xl mx-auto mb-6 bg-white p-4 rounded-xl shadow-md flex flex-col md:flex-row justify-between items-center gap-4 no-print border-t-4 border-gray-800">
        <div class="text-lg font-bold text-gray-800">{{ trans('client.monthly_account_statement') }}</div>

        <div class="flex gap-2 w-full md:w-auto">
          

            <button onclick="printDocument()" class="bg-gray-800 hover:bg-gray-900 text-white px-6 py-2 rounded-lg font-bold shadow flex justify-center items-center gap-2 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M5 4v3H4a2 2 0 00-2 2v3a2 2 0 002 2h1v2a2 2 0 002 2h6a2 2 0 002-2v-2h1a2 2 0 002-2V9a2 2 0 00-2-2h-1V4a2 2 0 00-2-2H7a2 2 0 00-2 2zm8 0H7v3h6V4zm0 8H7v4h6v-4z" clip-rule="evenodd" />
                </svg>
                {{ trans('client.print_account_statement') }}
            </button>
        </div>
    </div>

    <!-- كشف الحساب -->
    <div class="statement-view">
        <div class="print-container max-w-4xl mx-auto bg-white p-6 md:p-10 shadow-2xl rounded-xl border-t-8 border-gray-800 relative overflow-hidden">

            <div class="flex justify-between items-start border-b-2 border-gray-100 pb-3 mb-4 relative z-10">
                <div class="text-right">
                    <h2 class="text-xl font-bold text-gray-800 mb-2">{{ trans('client.statement_of_account') }}</h2>
                    <div class="flex gap-4 text-xs text-gray-600 justify-end font-medium whitespace-nowrap" dir="ltr">
                        <div><span class="font-semibold text-gray-800">{{ trans('client.period') }}:</span> <span class="text-blue-700 font-bold">{{ $monthName }} {{ $year }}</span></div>
                        <div><span class="text-gray-300">|</span></div>
                        <div><span class="font-semibold text-gray-800">{{ trans('client.date') }}:</span> <span class="auto-date"></span></div>
                    </div>
                </div>

                <div class="flex items-center gap-4 header-logos">
                    <div class="flex flex-col justify-center items-center mt-1">
                        <span class="text-[8px] text-gray-400 font-bold mb-0 tracking-wider whitespace-nowrap">{{ trans('client.powered_by') }}</span>
                        <img src="https://i.postimg.cc/x1YqjNZQ/lwqww-hwfw-(1)-(1).png" alt="شعار الشركة الأم" class="h-7 w-auto object-contain opacity-80">
                    </div>
                    <div class="h-10 border-r-2 border-gray-200"></div>
                    <img src="https://i.postimg.cc/Cx8YsGLw/lwqw-msttyl-2-(1).png" alt="شعار كلين ستيشن" class="h-20 w-auto object-contain">
                </div>
            </div>

            <!-- معلومات العميل -->
            <div class="compact-info-bar bg-gray-50 border border-gray-100 rounded-lg p-3 mb-4 flex flex-wrap justify-between items-center text-sm shadow-sm">
                <div class="flex flex-col">
                    <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-0.5">{{ trans('client.customer_name') }}</span>
                    <span class="font-bold text-gray-800 text-[13px]">{{ $company->fullname }}</span>
                </div>

                <div class="info-divider w-px h-8 bg-gray-200 hidden md:block"></div>

                <div class="flex flex-col">
                    <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-0.5">{{ trans('client.mobile_number') }}</span>
                    <span class="font-semibold text-gray-800 text-[13px]" dir="ltr">{{ $company->phone }}</span>
                </div>

                <div class="info-divider w-px h-8 bg-gray-200 hidden md:block"></div>

              


                <div class="flex flex-col">
                    <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-0.5">{{ trans('client.payment_method') }}</span>
                    <span class="font-bold text-blue-700 bg-blue-100/50 px-2 py-0.5 rounded text-[11px] mt-0.5">{{ trans('client.monthly_contract') }}</span>
                </div>

                <div class="info-divider w-px h-8 bg-gray-200 hidden md:block"></div>

                <div class="flex flex-col">
                    <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-0.5">{{ trans('client.total_balance_due') }}</span>
                    <span class="font-bold text-red-600 text-[14px] mt-0.5" id="header-total-due">{{$totalAmount}} {{ trans('client.sar') }}</span>
                </div>
            </div>

            <!-- جدول كشف الحساب -->
            <div class="mb-4 relative">
                <div class="flex justify-between items-center mb-2">
                    <h3 class="text-base font-bold text-gray-800 border-b-2 border-gray-800 inline-block pb-1">{{ trans('client.statement_details') }}</h3>
                </div>

                <div class="overflow-x-visible">
                    <table class="w-full text-center border-collapse stat-table" id="statement-table">
                        <thead>
                            <tr>
                                <th class="w-24">{{ trans('client.invoice_number') }}</th>
                                <th class="w-24">{{ trans('client.details') }}</th>
                                <th class="w-24">{{ trans('client.delivery_date') }}</th>
                                <th class="w-24">{{ trans('client.debtor') }}</th>
                                <th class="w-20">{{ trans('client.creditor') }}</th>
                                <th class="w-32 bg-gray-700 text-white">{{ trans('client.credit') }}</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-700 bg-white" id="statement-tbody">
                            @php
                            $totalAmountCredit = 0;
                            @endphp
                            @foreach($items as $item)
                                @php
                                    $totalAmountCredit += $item->creditor - $item->debtor;
                                @endphp
                                <tr class="hover:bg-gray-50 transition stat-row">
                                    <td dir="ltr">
                                        @if(isset($item->url))
                                            <a href="{{ $item->url }}" target="_blank" class="text-blue-600 hover:text-blue-800 transition font-bold">{{ $item->reference_id }}</a>
                                        @else
                                            {{ $item->reference_id }}
                                        @endif
                                    </td>
                                    <td dir="ltr">{{ $item->note }}</td>
                                    <td dir="ltr">{{ $item->date }}</td>
                                    <td>{{ $item->debtor }}</td>
                                    <td>{{ $item->creditor }}</td>
                                    <td class="font-bold text-gray-900 bg-gray-100"><span class="stat-final">{{ $totalAmountCredit ?? 0 }}</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="border-t-4 border-gray-800 text-sm">
                            <tr class="bg-gray-100">
                                <td colspan="4" class="py-2 px-3 text-left font-extrabold text-gray-800">{{ trans('client.total_balance_due') }}:</td>
                                <td class="py-2 px-3 text-center font-extrabold text-blue-700 text-lg">
                                    <span id="stat-total-due">{{$totalAmount}}</span> <span class="text-xs">{{ trans('client.sar') }}</span>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- معلومات الدفع -->
            <div class="flex justify-between gap-4 mt-6">
                <div class="w-full bg-gray-50 border border-gray-200 p-3 rounded-lg">
                    <h3 class="text-sm font-bold text-gray-800 mb-2 border-b border-gray-400 inline-block pb-1">{{ trans('client.payment_details') }}</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-xs text-gray-700">
                        <div><strong class="text-gray-800">{{ trans('client.beneficiary') }}:</strong> <span class="font-bold">{{ trans('client.hofo_system_company') }} (HOFU SYSTEM COMPANY)</span></div>
                        <div><strong class="text-gray-800">{{ trans('client.bank') }}:</strong> <span class="font-bold">بنك الرياض</span></div>
                        <div><strong class="text-gray-800">{{ trans('client.current_account_number') }}:</strong> <span class="font-bold">2582503809940</span></div>
                        <div><strong class="text-gray-800">{{ trans('client.iban') }}:</strong> <span class="font-bold" dir="ltr">SA30 2000 0002 5825 0380 9940</span></div>
                    </div>
                </div>
            </div>

            <!-- التوقيع -->
            <div class="flex justify-between pt-6 mt-4 border-t-2 border-gray-100 signature-area">
                <div class="w-1/2 text-center pr-2">
                    <h4 class="font-bold text-gray-800 text-[11px] mb-1">{{ trans('client.stamp') }}</h4>
                    <div class="relative h-20 mt-6 w-1/2 mx-auto flex justify-center items-end border-b border-dashed border-gray-300 mb-1 stamp-container">
                        <img src="https://i.postimg.cc/cLmC5rHf/Adobe-Express-file.png" alt="ختم هوفو سيستم" class="h-36 w-auto absolute bottom-[-30px] pointer-events-none mix-blend-multiply opacity-90 -rotate-6 stamp-img">
                    </div>
                    <p class="text-[10px] text-gray-500 mt-1">{{ trans('client.hofo_system_company') }}</p>
                </div>

                <div class="w-1/2 text-center pl-2 border-r border-gray-100">
                    <h4 class="font-bold text-gray-800 text-[11px] mb-1">{{ trans('client.customer_confirmation') }}</h4>
                    <div class="relative h-20 mt-6 w-1/2 mx-auto flex justify-center items-end border-b border-dashed border-gray-300 mb-1 stamp-container">
                        <p class="text-[9px] text-gray-400 mb-1">{{ trans('client.acknowledge_balance_correctness') }}</p>
                    </div>
                    <p class="text-[10px] text-gray-500 mt-1">{{ trans('client.name_and_signature') }}</p>
                </div>
            </div>

            <!-- التذييل -->
            <div class="print-footer mt-6 w-full bg-white pb-2">
                <div class="h-[4px] w-full bg-gradient-to-l from-gray-800 via-gray-400 to-transparent mb-2 rounded-full print-color-adjust"></div>
                <div class="flex justify-between items-end">
                    <div class="flex items-center gap-4 header-logos">
                        <img src="https://i.postimg.cc/x1YqjNZQ/lwqww-hwfw-(1)-(1).png" alt="هوفو سيستم" class="h-6 w-auto object-contain">
                        <div class="border-r-2 border-gray-200 pr-3">
                            <p class="text-[10px] font-bold text-gray-800 leading-none mb-1">{{ trans('client.powered_technically_operationally_by') }}</p>
                            <p class="text-[8px] text-gray-500 leading-none">{{ trans('client.slogan') }}</p>
                        </div>
                    </div>
                    <div class="text-left text-[10px] text-gray-600 font-semibold leading-tight" dir="ltr">
                        <p class="mb-1 text-gray-800">www.cleanstation.app | support@cleanstation.app</p>
                        <p class="text-[7px] text-gray-400">© 2026 Powered by Hofo System. All rights reserved.</p>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
        window.addStatementRow = function () {
            const tbody = document.getElementById('statement-tbody');
            if (!tbody) return;

            const newRow = document.createElement('tr');
            newRow.className = 'hover:bg-gray-50 transition stat-row';
            newRow.innerHTML = `
                <td dir="ltr">L000000000</td>
                <td dir="ltr">DD/MM/YYYY</td>
                <td>0.00</td>
                <td>0.00%</td>
                <td class="font-bold text-gray-900 bg-gray-100"><span class="stat-final">0.00</span></td>
                <td class="no-print"><button class="delete-btn" onclick="removeStatementRow(this)" title="{{ trans('client.delete_row') }}">✖</button></td>
            `;
            tbody.appendChild(newRow);
            calculateStatementTotal();
        };

        window.removeStatementRow = function (button) {
            const row = button.closest('tr');
            if (row) {
                row.remove();
                calculateStatementTotal();
            }
        };

      

        window.printDocument = function () {
            window.print();
        };

        document.addEventListener('DOMContentLoaded', () => {
            const dates = document.querySelectorAll('.auto-date');
            const today = new Date().toLocaleDateString('en-GB', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });

            dates.forEach((el) => {
                el.innerText = today;
            });

            const statTable = document.querySelector('.stat-table');
            if (statTable) {
                statTable.addEventListener('input', (e) => {
                    if (e.target.classList.contains('stat-final')) {
                        calculateStatementTotal();
                    }
                });
            }

            calculateStatementTotal();


        });
    </script>
</body>
</html>