@extends('b2b::web.layouts.app')

@section('content')
<div id="view-dashboard" class="view-section active space-y-6">
    <!-- Dashboard Header -->
    <div
        class="bg-gradient-to-br from-[#0f2027] via-[#1c75bc] to-[#203a43] text-white p-8 md:p-10 rounded-[32px] shadow-[0_20px_50px_rgba(28,117,188,0.2)] relative overflow-hidden flex flex-col justify-center min-h-[180px]">
        <div class="absolute top-0 right-0 w-96 h-96 bg-white/5 rounded-full blur-3xl -translate-y-1/2 translate-x-1/4">
        </div>
        <div
            class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-end gap-4 dir-dependent-flex">
            <div class="dir-dependent-text">
                <p class="text-blue-200 text-sm font-bold tracking-widest uppercase mb-1" data-i18n="welcome_back">
                    {{ trans('welcome back')  }}
                </p>
                <h1 class="text-3xl md:text-4xl font-black mb-2" data-i18n="hotel_name">{{ $user->fullname }}</h1>
                <p class="text-white/70 font-medium text-sm md:text-base" data-i18n="overview_text">
                    {{ trans('dashboard_subtitle') }}
                </p>
            </div>
            <button
                class="bg-white text-[#1c75bc] px-6 py-3 rounded-xl font-bold shadow-lg hover:scale-105 transition-transform text-sm whitespace-nowrap flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                    </path>
                </svg>
                <a href="{{ route('client.order.index') }}" data-i18n="view_orders_log_btn">{{ trans('order_tracking')
                    }}</a>
            </button>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="mb-4 pt-2">
        <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-3 px-2 dir-dependent-text"
            data-i18n="quick_actions">{{ trans('quick_actions') ?? 'إجراءات سريعة' }}</h3>
        <div class="flex flex-wrap gap-3 dir-dependent-flex">
            <button onclick="openModal('order-modal')"
                class="bg-white border border-gray-200 text-gray-800 hover:border-[#1c75bc] hover:text-[#1c75bc] px-5 py-2.5 rounded-xl font-bold shadow-sm transition-colors text-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span data-i18n="new_order">{{ trans('create_order') }}</span>
            </button>
            <button onclick="openModal('ticket-new-modal')"
                class="bg-white border border-gray-200 text-gray-800 hover:border-[#1c75bc] hover:text-[#1c75bc] px-5 py-2.5 rounded-xl font-bold shadow-sm transition-colors text-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z">
                    </path>
                </svg>
                <span data-i18n="open_ticket">{{ trans('open ticket') }}</span>
            </button>
            <a href="{{ route('client.monthly-invoices') }}"
                class="bg-white border border-gray-200 text-gray-800 hover:border-[#1c75bc] hover:text-[#1c75bc] px-5 py-2.5 rounded-xl font-bold shadow-sm transition-colors text-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                    </path>
                </svg>
                <span data-i18n="pay_now">{{ trans('pay now') }}</span>
                </a>
        </div>
    </div>

    <!-- Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4" id="dashboard-stats">
        <!-- Total Orders -->
        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-blue-50 text-blue-600 rounded-2xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                </div>
            </div>
            <h4 class="text-gray-500 text-xs font-bold uppercase tracking-widest">{{ trans('total_orders') }}
            </h4>
            <p class="text-2xl font-black text-gray-900 mt-1">{{ number_format($totalOrders) }}</p>
        </div>

        <!-- Monthly Invoice -->
        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-green-50 text-green-600 rounded-2xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 13h.01M12 13h.01M15 13h.01M12 17h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                        </path>
                    </svg>
                </div>
            </div>
            <h4 class="text-gray-500 text-xs font-bold uppercase tracking-widest">{{ trans('monthly_invoice') }}
            </h4>
            <p class="text-2xl font-black text-gray-900 mt-1">{{ number_format($monthlyInvoiceTotal, 2) }} <span
                    class="text-sm font-bold">{{ trans('SAR') }}</span></p>
        </div>
        <!-- Active Points -->
        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-amber-50 text-amber-600 rounded-2xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-7.714 2.143L11 21l-2.286-6.857L1 12l7.714-2.143L11 3z">
                        </path>
                    </svg>
                </div>
            </div>
            <h4 class="text-gray-500 text-xs font-bold uppercase tracking-widest">{{ trans('pending orders') }}
            </h4>
            <p class="text-2xl font-black text-gray-900 mt-1">{{ number_format($totalPendingOrders) }}</p>
        </div>
        <!-- Linked Branches -->
        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-purple-50 text-purple-600 rounded-2xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                        </path>
                    </svg>
                </div>
            </div>
            <h4 class="text-gray-500 text-xs font-bold uppercase tracking-widest">{{ trans('linked_branches') }}
            </h4>
            <p class="text-2xl font-black text-gray-900 mt-1">{{ number_format($branchCount) }}</p>
        </div>

       
    </div>

    <!-- Latest Orders -->
    <div class="bg-white rounded-3xl border border-gray-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] overflow-hidden mt-8">
        <div class="p-6 border-b border-gray-50 flex justify-between items-center dir-dependent-flex">
            <h3 class="font-black text-gray-900 text-lg tracking-tight" data-i18n="latest_orders">
                {{ trans('recent_orders') }}</h3>
            <a href="{{ route('client.order.index') }}" class="text-[#1c75bc] text-sm font-bold hover:underline flex items-center gap-1">
                <span data-i18n="view_all">{{ trans('view_all') }}</span> <span class="rtl-rotate">&rarr;</span>
                </a>
        </div>
        <div class="overflow-x-auto w-full p-1">
            <table id="dashboard-latest-orders-tbl"
                class="w-full text-sm whitespace-nowrap text-right tbl-rtl-aware display">
                <thead>
                    <tr
                        class="bg-gray-50/50 text-gray-400 font-bold uppercase tracking-widest text-xs border-b border-gray-100">
                        <th class="py-4 px-6 text-center" data-i18n="actions">{{ trans('actions') }}</th>
                        <th class="py-4 px-6" data-i18n="total_amount">{{ trans('total_amount') }}</th>
                        <th class="py-4 px-6" data-i18n="delivery_date">{{ trans('delivery_date') }}</th>
                        <th class="py-4 px-6" data-i18n="pickup_date">{{ trans('pickup_date') }}</th>
                        <th class="py-4 px-6 font-bold" data-i18n="order_id_type">{{ trans('order') }}</th>
                    </tr>
                </thead>
                <tbody id="dashboard-latest-orders" class="divide-y divide-gray-50">
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#dashboard-latest-orders-tbl').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('client.order.data') }}",
                data: function (d) {
                    d.length = 5; // Limit to 5 for dashboard
                }
            },
            columns: [
                { data: 'actions', className: 'py-4 px-6 text-center', orderable: false, searchable: false },
                { data: 'total', className: 'py-4 px-6' },
                { data: 'delivery_date', className: 'py-4 px-6' },
                { data: 'pickup_date', className: 'py-4 px-6' },
                { data: 'order_info', className: 'py-4 px-6' }
            ],
            pageLength: 5,
            lengthChange: false,
            searching: false,
            info: false,
            paging: false,
            language: {
                url: "{{ app()->getLocale() == 'ar' ? '//cdn.datatables.net/plug-ins/1.13.7/i18n/ar.json' : '' }}"
            },
            order: [[4, 'desc']]
        });
    });
</script>
@endpush