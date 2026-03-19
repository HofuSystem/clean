@extends('b2b::web.layouts.app')

@section('content')
<!-- VIEW: Overprices -->
<div id="view-overprices" class="view-section active space-y-6">
    <form id="overpricesForm" action="{{ route('client.contracts.customer-prices.bulk') }}" method="POST">
        @csrf
        <div
            class="flex flex-col md:flex-row justify-between items-center bg-white p-6 md:p-8 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 gap-4 dir-dependent-flex">
            <div class="dir-dependent-text">
                <h2 class="text-3xl font-black text-gray-900 tracking-tight" data-i18n="overprices">{{ $title }}</h2>
                <p class="text-gray-500 font-medium text-sm">{{ $description }}</p>
            </div>
            <button type="submit" id="submit-btn"
                class="px-8 py-3 bg-gray-900 text-white text-sm font-black rounded-xl shadow-lg hover:bg-black transition-transform hover:-translate-y-0.5 flex items-center gap-2">
                <span class="btn-text">{{ trans('client.save_changes') }}</span>
                <span class="loading-spinner hidden"><i class="fas fa-spinner fa-spin"></i></span>
            </button>
        </div>

        <div id="overprices-container" class="space-y-6 mt-6">
            @foreach($groupedProducts as $categoryName => $products)
                <div class="bg-white rounded-3xl overflow-hidden shadow-[0_4px_20px_rgb(0,0,0,0.03)] border border-gray-100">
                    <div class="bg-gray-900 px-6 py-4 border-b border-gray-100 text-right dir-dependent-text">
                        <h3 class="text-sm font-black text-white uppercase tracking-widest">{{ $categoryName }}</h3>
                    </div>
                    <div class="overflow-x-auto w-full">
                        <table class="w-full text-sm text-right tbl-rtl-aware">
                            <thead class="bg-gray-50/50">
                                <tr class="text-gray-400 border-b border-gray-100 text-[10px] uppercase tracking-widest">
                                    <th class="py-4 px-6 w-2/5">{{ trans('client.item_service') }}</th>
                                    <th class="py-4 px-4">{{ trans('client.base_price') }}</th>
                                    <th class="py-4 px-4">{{ trans('client.margin_profit') }}</th>
                                    <th class="py-4 px-6">{{ trans('client.final_price') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($products as $product)
                                    <tr class="hover:bg-gray-50/50 transition-colors">
                                        <td class="py-4 px-6 dir-dependent-text">
                                            <div class="font-black text-gray-900 text-sm">
                                                {{ $product['name'] }} 
                                                @if($product['sub_category_name'])
                                                    <span class="text-gray-300 mx-2">|</span> 
                                                    <span class="text-[#1c75bc] font-bold text-xs">{{ $product['sub_category_name'] }}</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="py-4 px-4">
                                            <span class="bg-gray-50 px-3 py-1.5 rounded-lg border border-gray-200 text-xs font-black text-gray-500 shadow-inner">
                                                {{ $product['base_price'] }}
                                            </span>
                                        </td>
                                        <td class="py-4 px-4">
                                            <div class="flex items-center gap-2 dir-dependent-flex">
                                                <span class="text-gray-300 font-bold">+</span>
                                                <input type="number" 
                                                       name="overprices[{{ $product['id'] }}]"
                                                       data-base="{{ $product['base_price'] }}" 
                                                       value="{{ $product['over_price'] }}" 
                                                       step="0.01"
                                                       class="margin-input w-20 px-2 py-1.5 text-center border border-gray-200 focus:border-[#1c75bc] rounded-lg bg-gray-50 focus:bg-white outline-none font-black shadow-sm transition-all text-gray-900">
                                            </div>
                                        </td>
                                        <td class="py-4 px-6 dir-dependent-text-reverse final-price-display">
                                            <span class="final-value text-green-600 font-black text-lg">
                                                {{ number_format($product['base_price'] + $product['over_price'], 2) }}
                                            </span> 
                                            <span class="text-[10px] text-gray-400 font-bold ml-1">{{ trans('client.sar') }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach
        </div>
    </form>
</div>

<script>
$(document).ready(function() {
    // Handle input changes for live calculation
    $('.margin-input').on('input', function() {
        const $input = $(this);
        const basePrice = parseFloat($input.data('base')) || 0;
        const margin = parseFloat($input.val()) || 0;
        const finalPrice = basePrice + margin;
        
        $input.closest('tr').find('.final-value').text(finalPrice.toFixed(2));
    });

    // Handle form submission via AJAX
    $('#overpricesForm').on('submit', function(e) {
        e.preventDefault();
        
        const $form = $(this);
        const $btn = $('#submit-btn');
        const $text = $btn.find('.btn-text');
        const $spinner = $btn.find('.loading-spinner');
        
        // Disable button and show spinner
        $btn.prop('disabled', true).addClass('opacity-75');
        $text.addClass('hidden');
        $spinner.removeClass('hidden');

        $.ajax({
            url: $form.attr('action'),
            method: 'POST',
            data: $form.serialize(),
            success: function(response) {
                if (response.success) {
                    showToast(response.message || "{{ trans('client.customer_price_updated_success') }}", 'success');
                } else {
                    showToast(response.message || "{{ trans('client.customer_price_update_failed') }}", 'error');
                }
            },
            error: function(xhr) {
                const message = xhr.responseJSON?.message || "{{ trans('client.customer_price_update_failed') }}";
                showToast(message, 'error');
            },
            complete: function() {
                // Re-enable button and hide spinner
                $btn.prop('disabled', false).removeClass('opacity-75');
                $text.removeClass('hidden');
                $spinner.addClass('hidden');
            }
        });
    });
});
</script>
@endsection