@extends('b2b::web.layouts.app')

@push('styles')
<style>
    @media print {
        body {
            background: white !important;
            padding: 0 !important;
            margin: 0 !important;
        }
        .modal-content{
            display: none !important;
        }

        #app-wrapper {
            display: block !important;
        }

        header, 
        aside, 
        .print-hidden, 
        .whatsapp-btn, 
        .hovo-watermark {
            display: none !important;
        }

        main {
            height: auto !important;
            overflow: visible !important;
            padding: 0 !important;
            margin: 0 !important;
            background: white !important;
        }

        #view-subscription {
            margin: 0 !important;
            padding: 0 !important;
            box-shadow: none !important;
            border: none !important;
            max-width: 100% !important;
            width: 100% !important;
        }

        .dir-dependent-flex {
            display: flex !important;
        }

        .md\:p-12 {
            padding: 0 !important;
        }
        
        table {
            border-collapse: collapse !important;
            width: 100% !important;
        }
        
        tr {
            page-break-inside: avoid !important;
        }
        
     
        
        .bg-gray-50 {
            background-color: #f9fafb !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .bg-blue-50\/70 {
            background-color: #eff6ff !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .bg-green-50\/70 {
            background-color: #f0fdf4 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .bg-blue-100 {
            background-color: #dbeafe !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .bg-green-100 {
            background-color: #dcfce7 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .text-white {
            color: white !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        
        .text-blue-600 { color: #2563eb !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        .text-green-600 { color: #16a34a !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        .text-blue-800 { color: #1e40af !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        .text-gray-900 { color: #111827 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        .text-\[#1c75bc\] { color: #1c75bc !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }

        .grid {
            display: grid !important;
            gap: 1rem !important;
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        }
    }
</style>
@endpush

@section('content')
<!-- VIEW: Subscription (Contract) -->
<div id="view-subscription"
    class="view-section active bg-white p-8 md:p-12 shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 mx-auto max-w-5xl rounded-3xl text-gray-800 relative mt-4 print:shadow-none print:border-none print:p-0">
    <!-- Header -->
    <div
        class="flex flex-col md:flex-row justify-between items-center mb-10 border-b border-gray-100 pb-6 dir-dependent-flex print-hidden">
        <div class="text-right dir-dependent-text">
            <h1 class="text-3xl font-black mb-1 text-gray-900 tracking-tight" data-i18n="subscription">{{ $title }}
            </h1>
            <p class="text-gray-400 font-bold text-xs tracking-widest uppercase" data-i18n="hotel_name">{{
                Auth::user()->fullname }}
            </p>
        </div>
        <div class="flex items-center gap-3 mt-4 md:mt-0">
            @if($contract->status === 'active')
            <span
                class="bg-green-50 text-green-600 px-4 py-2 rounded-xl text-xs font-black border border-green-100 flex items-center gap-2 print-hidden">
                <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                {{ trans('active_contract') }}
            </span>
            @else
            <span
                class="bg-red-50 text-red-600 px-4 py-2 rounded-xl text-xs font-black border border-red-100 flex items-center gap-2 print-hidden">
                <span class="w-2 h-2 rounded-full bg-red-500"></span>
                {{ trans('expired_contract') }}
            </span>
            @endif
            <button onclick="window.print()"
                class="bg-gray-50 border border-gray-200 text-gray-700 hover:bg-gray-100 px-5 py-2.5 rounded-xl flex items-center gap-2 shadow-sm font-bold print-hidden transition-all text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                    </path>
                </svg>
                <span data-i18n="print_contract">{{ trans('print_contract') }}</span>
            </button>
        </div>
    </div>

    <!-- Contract Info Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-10 text-right dir-dependent-text">
        <div class="bg-gray-50 p-6 rounded-2xl border border-gray-100 hover:border-gray-200 transition-colors">
            <div class="flex items-center gap-2.5 mb-3 dir-dependent-flex">
                <div class="w-8 h-8 rounded-lg bg-gray-200/60 flex items-center justify-center shrink-0">
                    <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path>
                    </svg>
                </div>
                <p class="text-gray-400 text-[9px] font-bold uppercase tracking-widest" data-i18n="contract_number">{{
                    trans('contract_number') }}</p>
            </div>
            <p class="font-black text-sm text-gray-900 font-mono">#{{ $contract->contract_number ?? $contract->id }}</p>
        </div>
        <div class="bg-blue-50/70 p-6 rounded-2xl border border-blue-100 hover:border-blue-200 transition-colors">
            <div class="flex items-center gap-2.5 mb-3 dir-dependent-flex">
                <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center shrink-0">
                    <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                        </path>
                    </svg>
                </div>
                <p class="text-blue-500 text-[9px] font-bold uppercase tracking-widest" data-i18n="payment_terms">{{
                    trans('payment_method') }}</p>
            </div>
            <p class="font-bold text-xs text-blue-800 leading-relaxed">{{ $contract->payment_method ??
                trans('monthly_fees') }}</p>
        </div>
        <div class="bg-gray-50 p-6 rounded-2xl border border-gray-100 hover:border-gray-200 transition-colors">
            <div class="flex items-center gap-2.5 mb-3 dir-dependent-flex">
                <div class="w-8 h-8 rounded-lg bg-gray-200/60 flex items-center justify-center shrink-0">
                    <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                        </path>
                    </svg>
                </div>
                <p class="text-gray-400 text-[9px] font-bold uppercase tracking-widest" data-i18n="contract_duration">
                    {{ trans('months_count') }}</p>
            </div>
            <p class="font-black text-base text-gray-900">{{ $contract->duration ?? 12 }} {{ trans('months') }}</p>
        </div>
        <div class="bg-green-50/70 p-6 rounded-2xl border border-green-100 hover:border-green-200 transition-colors">
            <div class="flex items-center gap-2.5 mb-3 dir-dependent-flex">
                <div class="w-8 h-8 rounded-lg bg-green-100 flex items-center justify-center shrink-0">
                    <svg class="w-3.5 h-3.5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                        </path>
                    </svg>
                </div>
                <p class="text-green-500 text-[9px] font-bold uppercase tracking-widest">{{ trans('start_date') }}</p>
            </div>
            <p class="text-[11px] text-gray-600 font-bold bg-white px-2.5 py-1.5 rounded-lg border border-green-100 inline-block"
                dir="ltr">{{ $contract->start_date?->format('Y-m-d') }} &rarr; {{ $contract->end_date?->format('Y-m-d') }}</p>
        </div>
    </div>

    <!-- Agreed Prices Table -->
    <div class="border border-gray-100 rounded-2xl overflow-hidden shadow-sm">
        <div class="bg-gray-900 px-6 py-5 flex justify-between items-center flex-row-reverse dir-dependent-flex">
            <h2 class="text-sm font-bold text-white tracking-wide" data-i18n="agreed_prices">{{ trans('product_prices_agreed')
                }}</h2>
            <span class="bg-white/20 text-white px-3 py-1.5 rounded-lg text-[10px] font-bold border border-white/10"
                data-i18n="vat_inclusive">{{ trans('vat_inclusive') }}</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-right text-sm tbl-rtl-aware">
                <thead class="bg-gray-50/80 border-b border-gray-200">
                    <tr>
                        <th class="py-4 px-6 font-bold text-gray-500 text-xs tracking-wider uppercase"
                            data-i18n="th_item_name">{{ trans('service_type') }}</th>
                        <th class="py-4 px-6 font-bold text-gray-500 text-xs tracking-wider uppercase"
                            data-i18n="th_item_name">{{ trans('product_name') }}</th>
                        <th class="py-4 px-6 font-bold text-gray-500 text-xs tracking-wider uppercase text-left dir-dependent-text-reverse"
                            data-i18n="th_price">{{ trans('price') }}</th>
                    </tr>
                </thead>
                <tbody id="contract-products-table" class="divide-y divide-gray-100">
                    @foreach($contract->contractPrices as $price)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-3">
                                <span class="font-bold  text-[#1c75bc]">{{ $price->product->subCategory ? $price->product?->subCategory?->name : $price->product?->category?->name }}</span>
                            </div>
                        </td>
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-3">
                                <span class="font-bold text-gray-900">{{ $price->product->name }}</span>
                            </div>
                        </td>
                        <td class="py-4 px-6 text-left font-black ">{{ number_format($price->price, 2) }}
                            {{ trans('SAR') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Contract Footer Note -->
    <div class="mt-8 p-6 bg-yellow-50/50 border border-yellow-100 rounded-2xl text-right dir-dependent-text print-hidden">
        <h4 class="font-bold text-yellow-800 text-sm mb-2">{{ trans('important_contract_notes_title') }}</h4>
        <ul class="list-disc list-inside text-xs text-yellow-700 space-y-1 pr-4">
            <li>{{ trans('important_contract_notes_point_1') }}</li>
            <li>{{ trans('important_contract_notes_point_2') }}</li>
            <li>{{ trans('important_contract_notes_point_3') }}</li>
        </ul>
    </div>
</div>
@endsection