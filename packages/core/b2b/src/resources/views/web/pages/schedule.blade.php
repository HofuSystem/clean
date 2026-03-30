@extends('b2b::web.layouts.app')

@section('content')
<div id="view-schedule" class="view-section active space-y-6">
    <div
        class="flex flex-col md:flex-row justify-between items-center bg-white p-6 md:p-8 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 gap-4 dir-dependent-flex">
        <div class="dir-dependent-text">
            <h2 class="text-3xl font-black text-gray-900 tracking-tight" data-i18n="schedule">{{ $title }}</h2>
            <p class="text-gray-500 font-medium text-sm">{{ $description }}</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <button onclick="openModal('schedule-modal')"
                class="px-5 py-3 bg-gray-900 text-white text-sm font-black rounded-xl shadow-lg hover:bg-black transition-transform hover:-translate-y-0.5 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                    </path>
                </svg>
                <span data-i18n="add_day_schedule">{{ trans('client.add_weekly_schedule') }}</span>
            </button>
            <button onclick="openModal('schedule-date-modal')"
                class="px-5 py-3 bg-white border border-gray-200 text-gray-800 text-sm font-black rounded-xl shadow-sm hover:bg-gray-50 transition-transform hover:-translate-y-0.5 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span data-i18n="add_extra_time">{{ trans('client.add_date_schedule') }}</span>
            </button>
        </div>
    </div>
    <div class="bg-white rounded-3xl border border-gray-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] overflow-hidden p-4">
        <div class="p-6 border-b border-gray-50">
            <h3 class="font-black text-gray-900 text-lg tracking-tight">{{ trans('client.active_schedules') }}</h3>
        </div>
        <div class="overflow-x-auto w-full">
            <table id="schedule-dataTable" class="w-full text-sm whitespace-nowrap text-right tbl-rtl-aware display">
                <thead>
                    <tr
                        class="bg-gray-50/50 text-gray-400 font-bold uppercase tracking-widest text-xs border-b border-gray-100">
                        <th class="py-4 px-6 text-center" data-i18n="actions">{{ trans('actions') }}</th>
                        <th class="py-4 px-6" data-i18n="th_notes">{{ trans('notes') }}</th>
                        <th class="py-4 px-6" data-i18n="th_delivery">{{ trans('delivery') }}</th>
                        <th class="py-4 px-6" data-i18n="th_pickup">{{ trans('pickup') }}</th>
                        <th class="py-4 px-6 text-right">{{ trans('client.branch') }}</th>
                        <th class="py-4 px-6 text-right">{{ trans('client.type') }}</th>
                    </tr>
                </thead>
                <tbody id="schedule-table" class="divide-y divide-gray-50">
                    @foreach($daySchedules as $schedule)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="py-4 px-6 text-center">
                            <form action="{{ route('client.schedule.delete', $schedule->id) }}" method="POST" class="delete-schedule-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                        </path>
                                    </svg>
                                </button>
                            </form>
                        </td>
                        <td class="py-4 px-6 text-gray-400 font-medium italic">{{ $schedule->note ?? '---' }}</td>
                        <td class="py-4 px-6">
                            <div class="flex flex-col">
                                <span class="font-black text-gray-900">{{ $schedule->delivery_time }} - {{ $schedule->delivery_to_time }}</span>
                                <span class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">{{
                                    trans($schedule->delivery_day) }}</span>
                            </div>
                        </td>
                        <td class="py-4 px-6">
                            <div class="flex flex-col">
                                <span class="font-black text-gray-900">{{ $schedule->receiver_time }} - {{ $schedule->receiver_to_time }}</span>
                                <span class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">{{
                                    trans($schedule->receiver_day) }}</span>
                            </div>
                        </td>
                        <td class="py-4 px-6 text-right font-bold text-gray-600">
                            {{ $schedule->branch?->name ?? '---' }}
                        </td>
                        <td class="py-4 px-6 text-right font-bold text-blue-600">{{ trans('client.weekly_recurring') }}
                        </td>
                    </tr>
                    @endforeach

                    @foreach($dateSchedules as $schedule)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="py-4 px-6 text-center">
                            <form action="{{ route('client.schedule.delete', $schedule->id) }}" method="POST" class="delete-schedule-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                        </path>
                                    </svg>
                                </button>
                            </form>
                        </td>
                        <td class="py-4 px-6 text-gray-400 font-medium italic">{{ $schedule->note ?? '---' }}</td>
                        <td class="py-4 px-6">
                            <div class="flex flex-col">
                                <span class="font-black text-gray-900">{{ $schedule->delivery_time }} - {{ $schedule->delivery_to_time }}</span>
                                <span class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">{{
                                    $schedule->delivery_date }}</span>
                            </div>
                        </td>
                        <td class="py-4 px-6">
                            <div class="flex flex-col">
                                <span class="font-black text-gray-900">{{ $schedule->receiver_time }} - {{ $schedule->receiver_to_time }}</span>
                                <span class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">{{
                                    $schedule->receiver_date }}</span>
                            </div>
                        </td>
                        <td class="py-4 px-6 text-right font-bold text-gray-600">
                            {{ $schedule->branch?->name ?? '---' }}
                        </td>
                        <td class="py-4 px-6 text-right font-bold text-purple-600 tracking-tight">
                            {{ trans('client.custom_date_schedule') }}</td>
                    </tr>
                    @endforeach

                    @if($daySchedules->isEmpty() && $dateSchedules->isEmpty())
                    <tr>
                        <td colspan="6" class="py-12 text-center text-gray-400 font-bold">{{ trans('client.no_data') }}
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@push('scripts')
<script>
$(document).ready(function() {
    // Handle AJAX Form Submissions (Weekly & Date)
    $('#weeklyScheduleForm, #dateScheduleForm').on('submit', function(e) {
        e.preventDefault();
        const $form = $(this);
        const $btn = $form.find('button[type="submit"]');
        const originalHtml = $btn.html();
        
        $btn.prop('disabled', true).addClass('opacity-70').html('<span class="flex items-center gap-2"><div class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div> {{ trans('client.saving') }}</span>');
        
        $.ajax({
            url: $form.attr('action'),
            method: 'POST',
            data: $form.serialize(),
            success: function(response) {
                if (response.status) {
                    showToast(response.message, 'success');
                    setTimeout(() => window.location.reload(), 1000);
                }
            },
            error: function(xhr) {
                $btn.prop('disabled', false).removeClass('opacity-70').html(originalHtml);
                let msg = '{{ trans('client.schedule_creation_failed') }}';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                showToast(msg, 'error');
            }
        });
    });

    $('.delete-schedule-form').on('submit', function(e) {
        e.preventDefault();
        const $form = $(this);
        const $btn = $form.find('button[type="submit"]');
        const $row = $form.closest('tr');

        Swal.fire({
            text: '{{ trans('client.confirm_delete') }}',
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "{{ trans('client.yes_delete') }}",
            cancelButtonText: "{{ trans('client.cancel') }}",
            customClass: {
                confirmButton: "btn btn-danger",
                cancelButton: "btn btn-light"
            }
        }).then(function (result) {
            if (result.isConfirmed) {
                $btn.prop('disabled', true).addClass('opacity-50');
                
                $.ajax({
                    url: $form.attr('action'),
                    method: 'POST',
                    data: $form.serialize(),
                    success: function(response) {
                        if (response.success) {
                            showToast(response.message, 'success');
                            $row.fadeOut(300, function() {
                                $(this).remove();
                                if ($('#schedule-table tr').length === 0) {
                                    window.location.reload();
                                }
                            });
                        }
                    },
                    error: function(xhr) {
                        $btn.prop('disabled', false).removeClass('opacity-50');
                        let msg = '{{ trans('client.schedule_deletion_failed') }}';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        showToast(msg, 'error');
                    }
                });
            }
        });
    });
});
</script>
@endpush
@endsection