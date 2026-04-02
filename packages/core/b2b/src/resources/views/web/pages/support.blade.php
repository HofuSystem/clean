@extends('b2b::web.layouts.app')

@section('content')
<!-- VIEW: Support -->
<div id="view-support" class="view-section active space-y-6">
    <div
        class="flex flex-col md:flex-row justify-between items-center bg-white p-6 md:p-8 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 gap-4 dir-dependent-flex">
        <div class="dir-dependent-text">
            <h2 class="text-3xl font-black text-gray-900 tracking-tight" data-i18n="support">تذاكر الدعم الفني</h2>
        </div>
        <button
            class="px-6 py-3 bg-gray-900 text-white text-sm font-black rounded-xl shadow-lg hover:bg-black transition-transform hover:-translate-y-0.5 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6">
                </path>
            </svg>
            <span data-i18n="open_ticket">فتح تذكرة جديدة</span>
        </button>
    </div>
    <div class="bg-white rounded-3xl border border-gray-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] overflow-hidden p-4">
        <div class="overflow-x-auto w-full">
            <table id="tickets-dataTable" class="w-full text-sm whitespace-nowrap text-right tbl-rtl-aware display">
                <thead>
                    <tr
                        class="bg-gray-50/50 text-gray-400 font-bold uppercase tracking-widest text-xs border-b border-gray-100">
                        <th class="py-4 px-6" data-i18n="th_status">الحالة</th>
                        <th class="py-4 px-6" data-i18n="th_subject">الموضوع</th>
                        <th class="py-4 px-6" data-i18n="th_department">القسم</th>
                        <th class="py-4 px-6" data-i18n="actions">الإجراءات</th>
                    </tr>
                </thead>
                <tbody id="tickets-table" class="divide-y divide-gray-50"></tbody>
            </table>
        </div>
    </div>
</div>
@endsection