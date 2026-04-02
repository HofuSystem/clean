@extends('b2b::web.layouts.app')

@section('content')
<div id="view-analytics" class="view-section active space-y-6">
    <div
        class="flex flex-col md:flex-row justify-between items-center bg-white p-6 md:p-8 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 gap-4 dir-dependent-flex">
        <div class="dir-dependent-text">
            <h2 class="text-3xl font-black text-gray-900 tracking-tight" data-i18n="analytics">{{ trans('client.analytics') }}</h2>
            <p class="text-gray-500 font-medium text-sm mt-1">{{ trans('client.analytics_description') }}</p>
        </div>
        <button onclick="window.print()"
            class="px-6 py-3 bg-gray-900 text-white text-sm font-black rounded-xl shadow-lg hover:bg-black transition-transform hover:-translate-y-0.5 flex items-center gap-2 print:hidden">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                </path>
            </svg>
            <span data-i18n="export_pdf">{{ trans('client.print_receipt') }}</span>
        </button>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Revenue Chart -->
        <div class="bg-white p-6 md:p-8 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 w-full">
            <h3 class="font-bold text-gray-400 text-xs tracking-[0.2em] uppercase mb-4 dir-dependent-text"
                data-i18n="revenue_trend">{{ trans('client.monthly_invoice') }}</h3>
            <div class="relative h-64 w-full">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <!-- Distribution Chart -->
        <div class="bg-white p-6 md:p-8 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 w-full">
            <h3 class="font-bold text-gray-400 text-xs tracking-[0.2em] uppercase mb-4 dir-dependent-text"
                data-i18n="orders_distribution">{{ trans('client.order_tracking') }}</h3>
            <div class="relative h-64 w-full flex justify-center">
                <canvas id="distributionChart"></canvas>
            </div>
        </div>

        <!-- Top Selling Items -->
        <div class="bg-white p-6 md:p-8 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 w-full lg:col-span-2">
            <h3 class="font-bold text-gray-400 text-xs tracking-[0.2em] uppercase mb-6 dir-dependent-text"
                data-i18n="top_items">{{ trans('client.product_prices') }}</h3>
            <div class="space-y-6" id="analytics-top-items">
                @php
                    $colors = ['bg-blue-500', 'bg-indigo-500', 'bg-purple-500', 'bg-red-500'];
                    $maxQty = $topItemsData->max('qty') ?: 1;
                @endphp
                @foreach($topItemsData as $index => $item)
                    @php
                        $percentage = ($item['qty'] / $maxQty) * 100;
                    @endphp
                    <div class="space-y-2">
                        <div class="flex justify-between items-center font-bold text-sm">
                            <span class="text-gray-700">{{ $item['name'] }}</span>
                            <span class="text-gray-400">{{ $item['qty'] }} {{ trans('client.unit') }}</span>
                        </div>
                        <div class="w-full h-2 bg-gray-50 rounded-full overflow-hidden">
                            <div class="h-full bg-purple-500 rounded-full" style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                @endforeach
                @if($topItemsData->isEmpty())
                     <p class="text-center text-gray-400 py-8 font-bold">{{ trans('client.no_data') }}</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    // Shared Chart Options
    const chartOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        }
    };

    // 1. Revenue Trend Chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    const revenueData = @json($revenueTrend);
    
    new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: revenueData.map(d => d.date),
            datasets: [{
                label: 'Revenue',
                data: revenueData.map(d => d.total),
                borderColor: '#1c75bc',
                backgroundColor: 'rgba(28, 117, 188, 0.1)',
                fill: true,
                tension: 0.4,
                pointRadius: 4,
                pointBackgroundColor: '#1c75bc'
            }]
        },
        options: {
            ...chartOptions,
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: '#f3f4f6' },
                    ticks: { color: '#9ca3af', font: { weight: 'bold' } }
                },
                x: {
                    grid: { display: false },
                    ticks: { color: '#9ca3af', font: { weight: 'bold' } }
                }
            }
        }
    });

    // 2. Orders Distribution Chart
    const distCtx = document.getElementById('distributionChart').getContext('2d');
    const distData = @json($distribution);
    
    const labelMap = {
        'company': '{{ trans('contract_order') }}',
        'client': '{{ trans('guest_order') }}'
    };

    new Chart(distCtx, {
        type: 'doughnut',
        data: {
            labels: distData.map(d => labelMap[d.type] || d.type),
            datasets: [{
                data: distData.map(d => d.count),
                backgroundColor: ['#1c75bc', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'],
                borderWidth: 0,
                hoverOffset: 10
            }]
        },
        options: {
             ...chartOptions,
             plugins: {
                 legend: {
                     display: true,
                     position: 'bottom',
                     labels: {
                         padding: 20,
                         usePointStyle: true,
                         font: { family: 'Tajawal', weight: 'bold' }
                     }
                 }
             },
             cutout: '70%'
        }
    });
});
</script>
<style>
@media print {
    .view-section {
        margin: 0 !important;
        padding: 0 !important;
    }
    .grid {
        display: block !important;
    }
    .bg-white {
        border: none !important;
        box-shadow: none !important;
    }
    canvas {
        max-height: 250px !important;
        width: 100% !important;
    }
    .modal-content{
        display: none !important;
    }

}
</style>
@endpush