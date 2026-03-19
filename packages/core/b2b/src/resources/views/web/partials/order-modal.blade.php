@php
    $receiver = $order->orderRepresentatives->where('type', 'receiver')->first();
    $delivery = $order->orderRepresentatives->where('type', 'delivery')->first();
    
    // Get status steps from OrderHelper
    $statusSteps = \Core\Orders\Helpers\OrderHelper::orderStatusTimes($order);
    
    // Find the current status index in the steps
    // We'll look for the last checked step in history, but also consider the order's current status
    $currentStatusIndex = -1;
    
    // Some statuses in OrderHelper have different translations for labels than the status key itself
    $statusKeyToLabel = [
        'pending' => trans('pending'),
        'order_has_been_delivered_to_admin' => trans('order_has_been_delivered_to_admin'),
        'ready_to_delivered' => trans('ready_to_delivered'),
        'in_the_way' => trans('in_the_way'),
        'delivered' => trans('delivered'),
        'finished' => trans('finished'),
        'started' => trans('service_started'),
    ];

    $currentStatusLabel = $statusKeyToLabel[$order->status] ?? trans($order->status);

    foreach($statusSteps as $index => $step) {
        // If the step is officially checked in history
        if ($step['is_checked']) {
            $currentStatusIndex = $index;
        }
        // OR if this step matches the current order status
        if ($step['status'] === $currentStatusLabel) {
            $currentStatusIndex = $index;
        }
    }

    // Now, force all steps up to currentStatusIndex to be active
    foreach($statusSteps as $index => $step) {
        if ($index <= $currentStatusIndex) {
            $statusSteps[$index]['is_active'] = true;
            // $statusSteps[$index]['is_checked'] = true;
        } else {
            $statusSteps[$index]['is_active'] = false;
        }
    }

    $totalSteps = count($statusSteps);
    $progressWidth = ($totalSteps > 1 && $currentStatusIndex >= 0) 
        ? ($currentStatusIndex / ($totalSteps - 1)) * 100 
        : 0;
        
    if ($order->status == 'canceled' || $order->status == 'rejected') {
        $progressWidth = 0;
    }
    $isGuest = $order->b2b_type === 'client';
    if($isGuest){
        $displayName = $order->client->fullname ?? '---';
    }else{
        $displayName = $order->company->fullname ?? '---';
    }
    if ($isGuest && $order->note && preg_match('/Room: (\w+)/', $order->note, $matches)) {
        $displayName .= ' (' . $matches[1] . ')';
    }
                  
@endphp

<div id="order-details-content">
    <div class="p-8 border-b border-gray-100/50 flex flex-col md:flex-row justify-between items-start bg-gradient-to-br from-gray-50/80 to-white rounded-t-[32px] dir-dependent-flex gap-4 print:bg-white print:border-b-2 print:border-black">
        <div class="flex items-center gap-5">
            <img src="https://i.postimg.cc/gxGfY6Z7/lwqw-msttyl-2-(1).png" alt="Clean Station" class="h-12 w-auto object-contain shrink-0">
            <div class="dir-dependent-text border-x px-5 border-gray-200 print:border-none">
                <h1 class="text-2xl font-black text-gray-900 tracking-tight">{{ trans('client.order_details') }}</h1>
                <p class="text-gray-500 text-sm mt-1 font-bold tracking-widest font-mono">#{{ $order->reference_id }}</p>
            </div>
        </div>
        <div class="flex gap-3 shrink-0">
            <a href="{{ route('client.order.invoice', $order->id) }}" class="print-hidden bg-white hover:bg-gray-50 text-gray-800 border border-gray-200 px-6 py-3 rounded-2xl text-sm font-black flex items-center gap-2 transition-all shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                {{ trans('client.print_receipt') }}
            </a>
            <button onclick="closeModal('order-details-modal')" class="p-3 bg-gray-50 border border-gray-200 text-gray-500 rounded-2xl hover:bg-red-50 hover:text-red-500 transition-all print-hidden">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
    </div>
    <div class="p-6 md:p-10 space-y-10 flex-grow">
        
        <!-- Progress Tracker -->
        <div class="relative print-hidden max-w-4xl mx-auto mb-16 px-4">
            <div class="absolute top-6 left-8 right-8 h-1.5 bg-gray-100 z-0 rounded-full">
                <div class="h-full bg-gradient-to-r from-blue-400 to-[#1c75bc] rounded-full transition-all duration-1000 ease-out shadow-[0_0_10px_rgba(28,117,188,0.5)]" style="width: {{ $progressWidth }}%"></div>
            </div>
            <div class="relative z-10 flex justify-between items-start w-full dir-dependent-flex">
                @foreach($statusSteps as $index => $step)
                    @php
                        $isStepActive = $step['is_active'] ?? false;
                        $isCurrentStatus = ($index === $currentStatusIndex);
                        $hasTime = !empty($step['time']);
                    @endphp
                    <div class="flex flex-col items-center gap-3 relative" style="width: {{ 100 / count($statusSteps) }}%">
                        <div class="w-12 h-12 rounded-2xl flex items-center justify-center font-black text-xs transition-all duration-500 border-4 {{ $isStepActive ? 'bg-white border-[#1c75bc] text-[#1c75bc] shadow-lg shadow-blue-500/30' : 'bg-white border-gray-100 text-gray-300' }} {{ $isCurrentStatus ? 'scale-110 ring-4 ring-blue-50' : '' }}">
                            @if($isStepActive && !$isCurrentStatus)
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                            @else
                                <span>{{ $index + 1 }}</span>
                            @endif
                        </div>
                        <div class="text-center px-1">
                            <span class="text-[10px] md:text-xs font-black tracking-tight leading-tight block {{ $isStepActive ? 'text-[#1c75bc]' : 'text-gray-400' }}">
                                {{ $step['status'] }}
                            </span>
                            @if($hasTime)
                                <span class="text-[8px] font-bold text-gray-400 block mt-1">{{ \Carbon\Carbon::parse($step['time'])->format('H:i') }}</span>
                            @elseif($isStepActive && $index < $currentStatusIndex)
                                 <span class="text-[8px] font-bold text-green-400 block mt-1">✓</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="space-y-4">
                <h3 class="text-gray-400 font-bold text-xs tracking-[0.2em] uppercase px-2">{{ trans('client.invoice_to') }}</h3>
                <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] h-full relative overflow-hidden group">
                    <div class="absolute top-0 right-0 w-2 h-full bg-[#1c75bc] print:hidden"></div>
                    
                   

                    <h4 class="font-black text-2xl text-gray-900 mb-2">{{ $displayName }}</h4>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg {{ $isGuest ? 'bg-purple-50 text-purple-700 border-purple-100' : 'bg-blue-50 text-blue-700 border-blue-100' }} text-xs font-black mb-4 border">
                        {{ $isGuest ? trans('guest_order') : trans('contract_order') }}
                    </span>
                    <p class="text-gray-500 font-bold text-sm bg-gray-50 p-3 rounded-xl border border-gray-100 inline-block">
                        {{ $order->branch->name ?? trans('Main Branch') }}
                    </p>
                </div>
            </div>
            <div class="space-y-4">
                <h3 class="text-gray-400 font-bold text-xs tracking-[0.2em] uppercase px-2">{{ trans('client.timing_details') }}</h3>
                <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] space-y-4 h-full">
                    <div class="p-4 rounded-2xl bg-blue-50/50 border border-blue-100/50 flex justify-between items-center dir-dependent-flex print:border-gray-200 print:bg-white">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-white shadow-sm flex items-center justify-center text-[#1c75bc] print:border print:border-gray-200"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg></div>
                            <span class="text-gray-700 font-black text-sm">{{ trans('client.pickup_date') }}</span>
                        </div>
                        <div class="dir-dependent-text text-left">
                            <div class="font-black text-gray-900">{{ $receiver->date ?? '---' }}</div>
                            <div class="text-xs text-gray-500 font-bold mt-0.5" dir="ltr">{{ $receiver->time_12_hours_format ?? '' }} - {{ $receiver->to_time_12_hours_format ?? '' }}</div>
                        </div>
                    </div>
                    <div class="p-4 rounded-2xl bg-green-50/50 border border-green-100/50 flex justify-between items-center dir-dependent-flex print:border-gray-200 print:bg-white">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-white shadow-sm flex items-center justify-center text-green-500 print:border print:border-gray-200"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></div>
                            <span class="text-gray-700 font-black text-sm">{{ trans('client.delivery_date') }}</span>
                        </div>
                        <div class="dir-dependent-text text-left">
                            <div class="font-black text-gray-900">{{ $delivery->date ?? '---' }}</div>
                            <div class="text-xs text-gray-500 font-bold mt-0.5" dir="ltr">{{ $delivery->time_12_hours_format ?? '' }} - {{ $delivery->to_time_12_hours_format ?? '' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-4">
            <h3 class="text-gray-400 font-bold text-xs tracking-[0.2em] uppercase px-2">{{ trans('client.order_items') }}</h3>
            <div class="bg-white rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 overflow-hidden print:border-black print:shadow-none print:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-right tbl-rtl-aware">
                        <thead class="bg-gray-50/80 print:bg-gray-100">
                            <tr>
                                <th class="py-5 px-8 font-black text-gray-500 uppercase tracking-wider text-xs print:text-black">{{ trans('client.order_items') }}</th>
                                <th class="py-5 px-6 font-black text-gray-500 uppercase tracking-wider text-xs text-center print:text-black">{{ trans('client.quantity') }}</th>
                                <th class="py-5 px-6 font-black text-gray-500 uppercase tracking-wider text-xs text-left dir-dependent-text-reverse print:text-black">{{ trans('client.unit_price') }}</th>
                                <th class="py-5 px-8 font-black text-gray-500 uppercase tracking-wider text-xs text-left dir-dependent-text-reverse print:text-black">{{ trans('client.total') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 print:divide-black">
                            @foreach($order->items as $item)
                                <tr class="hover:bg-gray-50/50 transition-colors group">
                                    <td class="py-6 px-8 font-bold text-gray-900 text-base">{{ $item->product?->name ?? '---' }}</td>
                                    <td class="py-6 px-6 text-center"><span class="bg-gray-100 text-gray-800 font-black px-3 py-1 rounded-lg">{{ $item->quantity }}</span></td>
                                    <td class="py-6 px-6 font-medium text-gray-500 dir-dependent-text-reverse text-left">
                                        @if($item->product_price > 0)
                                            {{ number_format($item->product_price, 2) }} {{ trans('client.SAR') }}
                                        @else
                                            <span class="text-[#1c75bc] bg-blue-50 px-2 py-1 rounded text-xs font-bold">{{ trans('included') }}</span>
                                        @endif
                                    </td>
                                    <td class="py-6 px-8 font-black text-[#1c75bc] text-lg dir-dependent-text-reverse text-left">
                                        {{ number_format($item->product_price * $item->quantity, 2) }} {{ trans('client.SAR') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="flex justify-end">
            <div class="w-full md:w-80 bg-gray-50 rounded-3xl p-8 border border-gray-200 shadow-[0_20px_50px_rgb(0,0,0,0.03)] space-y-4 relative overflow-hidden print:border-black print:shadow-none print:bg-white print:rounded-lg">
                <div class="flex justify-between items-center text-gray-500 font-bold text-sm dir-dependent-flex print:text-black">
                    <span>{{ trans('client.sub_total') }}:</span>
                    <span class="font-black text-gray-800 print:text-black">{{ number_format($order->order_price, 2) }} {{ trans('client.SAR') }}</span>
                </div>

                <div class="flex justify-between items-center text-gray-500 font-bold text-sm border-b border-gray-200 pb-5 dir-dependent-flex print:text-black print:border-black">
                    <span>{{ trans('delivery_charge') }}:</span>
                    <span class="font-black text-gray-800 print:text-black">{{ number_format($order->delivery_price, 2) }} {{ trans('client.SAR') }}</span>
                </div>
                <div class="flex justify-between items-end pt-2 dir-dependent-flex">
                    <span class="text-gray-900 font-black text-lg">{{ trans('client.total_amount') }}:</span>
                    <span class="text-3xl font-black text-transparent bg-clip-text bg-gradient-to-r from-[#1c75bc] to-[#2980b9] print:text-black">
                        {{ number_format($order->total_price, 2) }} <span class="text-base text-gray-500 print:text-black">{{ trans('client.SAR') }}</span>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>