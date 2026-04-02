@extends('b2b::web.layouts.app')

@section('content')
<!-- VIEW: Invoices -->
<div id="view-invoices" class="view-section active space-y-6">
    <div
        class="flex flex-col md:flex-row justify-between items-center bg-white p-6 md:p-8 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 gap-4 dir-dependent-flex">
        <div class="dir-dependent-text">
            <h2 class="text-3xl font-black text-gray-900 tracking-tight" data-i18n="invoices">
                {{ trans('client.monthly_invoices_and_payment') }}
            </h2>
        </div>
        <div class="w-full md:w-64">
            <select id="invoice-month-select"
                class="select-premium w-full p-4 bg-white border border-gray-200 rounded-xl outline-none focus:border-[#1c75bc] transition-all font-bold text-gray-800">
                @forelse($monthlyInvoices as $invoice)
                @php
                $monthName = \Carbon\Carbon::createFromDate($invoice->year, $invoice->month,
                1)->locale(app()->getLocale())->translatedFormat('F');
                @endphp
                <option value="{{ $invoice->year }}-{{ $invoice->month }}"
                    data-contract="{{ number_format($invoice->contract_cost, 2) }}"
                    data-guest="{{ number_format($invoice->guest_cost, 2) }}"
                    data-profit="{{ number_format($invoice->hotel_profit, 2) }}"
                    data-coupon="{{ number_format($invoice->total_coupon, 2) }}"
                    data-total="{{ number_format($invoice->total_amount, 2) }}" data-month-name="{{ $monthName }}"
                    data-year="{{ $invoice->year }}">
                    {{ $monthName }} {{ $invoice->year }}
                </option>
                @empty
                <option value="">{{ trans('client.no_invoices') }}</option>
                @endforelse
            </select>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4" id="invoice-stats">
        <div
            class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm relative overflow-hidden dir-dependent-text">
            <div class="absolute top-0 right-0 w-1 h-full bg-blue-400"></div>
            <p class="text-gray-400 font-bold text-[10px] uppercase tracking-widest mb-2">{{
                trans('client.contract_orders_cost') }}</p>
            <p class="text-2xl font-black" id="stat-contract">0.00</p>
        </div>
        <div
            class="bg-green-50 p-6 rounded-2xl border border-green-100/50 shadow-sm relative overflow-hidden dir-dependent-text">
            <div class="absolute top-0 right-0 w-1 h-full bg-green-500"></div>
            <p class="text-green-700 font-bold text-[10px] uppercase tracking-widest mb-2">{{
                trans('client.discounts_and_compensations') }}</p>
            <p class="text-2xl font-black text-green-600" id="stat-coupon">0.00</p>
        </div>

        <div
            class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm relative overflow-hidden dir-dependent-text">
            <div class="absolute top-0 right-0 w-1 h-full bg-purple-400"></div>
            <p class="text-gray-400 font-bold text-[10px] uppercase tracking-widest mb-2">{{
                trans('client.total_guest_orders') }}</p>
            <p class="text-2xl font-black" id="stat-guest">0.00</p>
        </div>
        <div
            class="bg-green-50 p-6 rounded-2xl border border-green-100/50 shadow-sm relative overflow-hidden dir-dependent-text">
            <div class="absolute top-0 right-0 w-1 h-full bg-green-500"></div>
            <p class="text-green-700 font-bold text-[10px] uppercase tracking-widest mb-2">{{
                trans('client.hotel_profit_guests') }}</p>
            <p class="text-2xl font-black text-green-600" id="stat-profit">0.00</p>
        </div>
        <div class="bg-gray-900 p-6 rounded-2xl shadow-lg relative overflow-hidden dir-dependent-text">
            <p class="text-gray-400 font-bold text-[10px] uppercase tracking-widest mb-2">{{ trans('client.due_invoice')
                }}</p>
            <p class="text-2xl font-black text-white"><span id="stat-total">0.00</span> <span
                    class="text-sm text-gray-500">{{ trans('client.sar') }}</span></p>
        </div>
    </div>

    <div
        class="bg-gradient-to-br from-[#0a192f] to-[#112240] p-8 md:p-10 rounded-3xl shadow-[0_20px_50px_rgba(0,0,0,0.3)] flex flex-col md:flex-row justify-between items-center gap-8 relative overflow-hidden border border-white/10 dir-dependent-flex">
        <div class="absolute -left-20 -top-20 w-64 h-64 bg-blue-500/20 rounded-full blur-3xl"></div>
        <div class="text-white text-center md:text-right z-10 dir-dependent-text">
            <h3 class="font-black text-2xl mb-2 tracking-tight">{{ trans('client.pay_invoice_for') }} <span
                    id="pay-month-name"></span></h3>
            <p class="text-gray-400 font-medium text-sm">{{ trans('client.secure_payment_gateways_message') }}</p>
        </div>
        <div class="flex flex-col gap-4 w-full md:w-auto z-10">
            <a id="view-details-btn" href="#"
                class="bg-white/10 text-white border border-white/20 px-10 py-4 rounded-xl font-black shadow-xl hover:bg-white/20 transition-all w-full md:w-auto flex items-center justify-center gap-2">
                <span>{{ trans('client.view_details') }}</span>
            </a>
            <!-- <button onclick="showToast('جاري التحويل لبوابة الدفع...', 'success')"
                class="bg-white text-gray-900 px-10 py-4 rounded-xl font-black shadow-xl hover:scale-105 transition-all w-full md:w-auto flex items-center justify-center gap-2">
                <span data-i18n="pay_now">{{ trans('client.pay_dues_now') }}</span>
            </button> -->
            <div class="flex gap-2 justify-center bg-black/30 p-2 rounded-xl backdrop-blur-md">
                <div class="bg-white px-2 py-1 rounded text-blue-800 font-black text-[10px] italic">VISA</div>
                <div class="bg-white px-2 py-1 rounded text-red-500 font-black text-[10px] italic">MasterCard</div>
                <div
                    class="bg-gradient-to-r from-green-400 to-green-600 px-2 py-1 rounded text-white font-black text-[10px]">
                    mada</div>
                <div class="bg-black text-white px-2 py-1 rounded font-black text-[10px] flex items-center"> Pay</div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        const $select = $('#invoice-month-select');

        function updateStats() {
            const $option = $select.find('option:selected');
            if ($option.length && $option.val() !== "") {
                $('#stat-contract').text($option.data('contract'));
                $('#stat-guest').text($option.data('guest'));
                $('#stat-profit').text($option.data('profit'));
                $('#stat-coupon').text($option.data('coupon'));
                $('#stat-total').text($option.data('total'));
                $('#pay-month-name').text($option.data('month-name') + ' ' + $option.data('year'));

                // Update "View Details" button link
                const year = $option.data('year');
                const month = $option.val().split('-')[1];
                const url = "{{ route('client.monthly-invoice-details', ['year' => '__YEAR__', 'month' => '__MONTH__']) }}"
                    .replace('__YEAR__', year)
                    .replace('__MONTH__', month);
                $('#view-details-btn').attr('href', url);
            }
        }

        $select.on('change', updateStats);

        // Initial call
        updateStats();
    });
</script>
@endpush