@extends('b2b::web.layouts.app')

@section('content')
    <div id="view-orders" class="view-section active space-y-6">
        
        <div
            class="flex flex-col md:flex-row justify-between items-center bg-white p-6 md:p-8 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 gap-4 dir-dependent-flex flex-row-reverse">
            <div class="dir-dependent-text text-right"><h2 class="text-3xl font-black text-gray-900 tracking-tight"
                    data-i18n="orders_log">{{$title}}</h2></div>
            <div class="flex gap-4 w-full md:w-auto dir-dependent-flex flex-row-reverse">
                <input type="text" id="custom-order-search"
                    class="w-full py-3 px-5 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:border-[#1c75bc] font-medium text-sm transition-all placeholder-translate text-right dir-dependent-text"
                    data-i18n-placeholder="search_placeholder" placeholder="{{ trans('search_placeholder') }}">
            </div>
        </div>

        <!-- Latest Orders -->
        <div class="bg-white rounded-3xl border border-gray-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] overflow-hidden mt-8">
            <div class="p-6 border-b border-gray-50 flex justify-between items-center dir-dependent-flex">
                <h3 class="font-black text-gray-900 text-lg tracking-tight" data-i18n="latest_orders">
                    {{ trans('recent_orders') }}
                </h3>
                
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
        $(document).ready(function () {
            const table = $('#dashboard-latest-orders-tbl').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('client.order.data') }}",
                    data: function (d) {
                    }
                },
                columns: [
                    { data: 'actions', className: 'py-4 px-6 text-center', orderable: false, searchable: false },
                    { data: 'total', className: 'py-4 px-6' },
                    { data: 'delivery_date', className: 'py-4 px-6' },
                    { data: 'pickup_date', className: 'py-4 px-6' },
                    { data: 'order_info', className: 'py-4 px-6 font-bold' }
                ],
                pageLength: 10,
                lengthChange: false,
                searching: true,
                info: true,
                paging: true,
                dom: 'rt<"flex flex-col md:flex-row justify-between items-center p-6 border-t border-gray-50 gap-4"ip>',
                language: {
                    url: "{{ app()->getLocale() == 'ar' ? '//cdn.datatables.net/plug-ins/1.13.7/i18n/ar.json' : '' }}"
                },
                order: [[4, 'desc']]
            });

            $('#custom-order-search').on('keyup', function () {
                table.search(this.value).draw();
            });
        });
    </script>
@endpush