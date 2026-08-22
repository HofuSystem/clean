@extends('admin::layouts.dashboard')

@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    
    <!-- Top Toolbar & Breadcrumb -->
    <div class="toolbar my-3" id="kt_toolbar">
        <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
            <div class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
                <h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1">
                    {{ isset($item) ? trans('تعديل معاملة المحفظة') : trans('تسوية إدارية لرصيد محفظة') }}
                </h1>
                <span class="h-20px border-gray-200 border-start mx-4"></span>
                <ul class="breadcrumb breadcrumb-separatorless fw-bold fs-7 my-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard.index') }}" class="text-muted text-hover-primary">{{ trans('Home') }}</a>
                    </li>
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard.wallet-transactions.index') }}" class="text-muted text-hover-primary">{{ trans('سجل المحفظة') }}</a>
                    </li>
                    <li class="breadcrumb-item text-dark">
                        {{ isset($item) ? trans('تعديل معاملة') : trans('إضافة حركة') }}
                    </li>
                </ul>
            </div>
            <div>
                <a href="{{ route('dashboard.wallet-transactions.index') }}" class="btn btn-sm btn-light-primary fw-bold">
                    <i class="fa fa-arrow-right me-1"></i> {{ trans('العودة لسجل المحفظة') }}
                </a>
            </div>
        </div>
    </div>

    <!-- Main Container Card -->
    <div class="post d-flex flex-column-fluid" id="kt_post">
        <div id="kt_content_container" class="container-fluid">
            
            <div class="card border-0 shadow-sm w-100" style="border-radius: 16px; overflow: hidden;">
                
                <!-- Card Header -->
                <div class="card-header border-bottom-0 pt-6 px-6 px-md-8 d-flex justify-content-between align-items-center bg-transparent">
                    <div class="d-flex align-items-center gap-2">
                        <span class="d-inline-flex align-items-center justify-content-center bg-light-primary text-primary rounded-3 p-2" style="width: 42px; height: 42px;">
                            <i class="fa fa-wallet fs-3 text-primary"></i>
                        </span>
                        <div>
                            <h4 class="card-title fw-bolder text-dark mb-0 fs-4">
                                {{ isset($item) ? trans('تعديل معاملة المحفظة') : trans('تسوية إدارية لرصيد محفظة') }}
                            </h4>
                            <span class="text-muted fs-8">
                                {{ isset($item) ? trans('تحديث بيانات الحركة وتأثيرها المالي على المحفظة') : trans('إضافة رصيد أو خصم تسوية لحساب العميل') }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Form -->
                <form class="form" method="POST" id="operation-form"
                    redirect-to="{{ route('dashboard.wallet-transactions.index') }}"
                    data-id="{{ $item->id ?? null }}"
                    @isset($item)
                        action="{{ route('dashboard.wallet-transactions.edit', $item->id) }}"
                        data-mode="edit"
                    @else
                        action="{{ route('dashboard.wallet-transactions.create') }}"
                        data-mode="new"
                    @endisset>

                    @csrf
                    @isset($item)
                        @method('PUT')
                    @endisset

                    <input type="hidden" name="type" id="page_action_type" value="{{ old('type', $item->type ?? 'deposit') }}">

                    <div class="card-body px-6 px-md-8 py-5">

                        <!-- Field 1: Client Selection & Current Balance -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label fw-bold fs-7 text-gray-700 mb-0 required">
                                    {{ trans('ابحث عن العميل') }}
                                </label>
                                <div class="px-3 py-1 fw-bold rounded-2" id="page_customer_balance_badge" style="background-color: #eff6ff !important; color: #1d4ed8 !important; border: 1px solid #bfdbfe !important; font-size: 12px;">
                                    {{ trans('الرصيد الحالي') }}: <span id="page_customer_balance_val" class="fw-bolder" style="color: #1d4ed8 !important;">{{ number_format($item->user->wallet ?? 0, 2) }}</span> {{ trans('ر.س') }}
                                </div>
                            </div>

                            <select class="form-select form-select-solid" name="user_id" id="page_user_select" required style="width: 100%;">
                                @if(isset($item) && $item->user)
                                    <option value="{{ $item->user->id }}" selected data-wallet="{{ $item->user->wallet }}">
                                        {{ $item->user->fullname }} ({{ $item->user->phone ?: $item->user->email }})
                                    </option>
                                @else
                                    <option value="">{{ trans('الاسم، رقم الجوال، أو البريد الإلكتروني...') }}</option>
                                @endif
                            </select>
                        </div>

                        <!-- Field 2: Action Type Segmented Toggle -->
                        <div class="mb-4">
                            <label class="form-label fw-bold fs-7 text-gray-700 mb-2 required">
                                {{ trans('نوع الإجراء') }}
                            </label>
                            @php
                                $currentType = old('type', $item->type ?? 'deposit');
                            @endphp
                            <div class="row g-2">
                                <div class="col-6">
                                    <button type="button" class="btn w-100 py-3 fw-bolder fs-6 d-flex align-items-center justify-content-center gap-2 page-type-btn {{ $currentType === 'deposit' ? 'active' : 'text-muted' }}" id="page_btn_type_deposit" data-type="deposit" style="border-radius: 10px; {{ $currentType === 'deposit' ? 'border: 2px solid #16a34a; background-color: #f0fdf4; color: #16a34a;' : 'border: 2px solid #e5e7eb; background-color: #ffffff; color: #6b7280;' }}">
                                        <span class="fs-4">↑</span> {{ trans('إضافة رصيد') }}
                                    </button>
                                </div>
                                <div class="col-6">
                                    <button type="button" class="btn w-100 py-3 fs-6 d-flex align-items-center justify-content-center gap-2 page-type-btn {{ $currentType === 'withdraw' ? 'active fw-bolder' : 'text-muted' }}" id="page_btn_type_withdraw" data-type="withdraw" style="border-radius: 10px; {{ $currentType === 'withdraw' ? 'border: 2px solid #dc2626; background-color: #fef2f2; color: #dc2626;' : 'border: 2px solid #e5e7eb; background-color: #ffffff; color: #6b7280;' }}">
                                        <span class="fs-4">↓</span> {{ trans('خصم رصيد') }}
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Field 3: Reason (سبب الإجراء) & Amount (المبلغ) -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold fs-7 text-gray-700 mb-1 required">
                                    {{ trans('سبب الإجراء') }}
                                </label>
                                <select class="form-select form-select-solid" name="transaction_type" id="page_transaction_type" required>
                                    <!-- Populated dynamically by JS -->
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold fs-7 text-gray-700 mb-1 required">
                                    {{ trans('المبلغ (ر.س)') }}
                                </label>
                                <div class="input-group">
                                    <input type="number" step="0.01" min="0.01" name="amount" id="page_amount" class="form-control form-control-solid fw-bold" placeholder="0.00" value="{{ old('amount', isset($item) ? abs($item->amount) : '') }}" required>
                                    <span class="input-group-text bg-light text-muted border-0 fw-bold">{{ trans('ر.س') }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Field 4: Order Reference (مرجع رقم الطلب) & Expiry Date (تاريخ الانتهاء) -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold fs-7 text-gray-700 mb-1">
                                    {{ trans('مرجع رقم الطلب (اختياري)') }}
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted border-0 fw-bold">#</span>
                                    <input type="text" name="order_reference" id="page_order_ref" class="form-control form-control-solid" placeholder="{{ trans('مثال: L738925558') }}" value="{{ old('order_reference', $item->order->reference_id ?? '') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold fs-7 text-gray-700 mb-1">
                                    {{ trans('تاريخ الانتهاء (للرصيد الترويجي)') }}
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted border-0"><i class="far fa-calendar-alt"></i></span>
                                    <input type="date" name="expired_at" id="page_expired_at" class="form-control form-control-solid" value="{{ old('expired_at', isset($item->expired_at) ? \Carbon\Carbon::parse($item->expired_at)->format('Y-m-d') : '') }}">
                                </div>
                            </div>
                        </div>

                        <!-- Field 5: Details for Review (تفاصيل العملية للمراجعة) -->
                        <div class="mb-4">
                            <label class="form-label fw-bold fs-7 text-gray-700 mb-1 required">
                                {{ trans('تفاصيل العملية للمراجعة') }}
                            </label>
                            <textarea name="notes" id="page_notes" rows="2" class="form-control form-control-solid" placeholder="{{ trans('اكتب سبب هذه العملية بالتفصيل للرجوع إليها لاحقاً...') }}" required>{{ old('notes', $item->notes ?? '') }}</textarea>
                        </div>

                        <!-- Field 6: Notify Customer Switch -->
                        <div class="d-flex align-items-center justify-content-between p-3 rounded-3 mb-2" style="background-color: #f8fafc; border: 1px solid #f1f5f9;">
                            <div class="d-flex flex-column">
                                <span class="fw-bolder fs-7 text-dark">{{ trans('إشعار العميل') }}</span>
                                <span class="text-muted fs-8">{{ trans('سيتم إرسال إشعار للمستخدم بتحديث رصيد المحفظة') }}</span>
                            </div>
                            <div class="form-check form-switch form-check-custom form-check-solid">
                                <input class="form-check-input" type="checkbox" name="send_notification" value="1" id="page_send_notification" checked style="cursor: pointer; width: 44px; height: 24px;">
                            </div>
                        </div>

                    </div>

                    <!-- Card Footer Actions -->
                    <div class="card-footer border-top-0 pt-0 pb-6 px-6 px-md-8 d-flex justify-content-start gap-2 bg-transparent">
                        <button type="submit" class="btn text-white fw-bold px-5 py-3" style="background-color: #244b7d !important; border-radius: 8px; font-size: 15px;">
                            <i class="fa fa-check text-white me-1"></i> {{ isset($item) ? trans('تأكيد وحفظ التعديل') : trans('تأكيد وحفظ') }}
                        </button>
                        <a href="{{ route('dashboard.wallet-transactions.index') }}" class="btn btn-light text-muted fw-bold px-4 py-3" style="border-radius: 8px; font-size: 15px;">
                            {{ trans('إلغاء') }}
                        </a>
                    </div>
                </form>

            </div>

        </div>
    </div>
</div>
@endsection

@push('js')
<script src="{{ asset('control') }}/js/custom/crud/form.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const depositReasons = [
        { value: 'promotional_add', label: '{{ trans("رصيد ترويجي (بونص)") }}' },
        { value: 'compensation_add', label: '{{ trans("تعويض عميل / شكوى") }}' },
        { value: 'charge', label: '{{ trans("شحن مباشر للمحفظة") }}' },
        { value: 'deposit', label: '{{ trans("إضافة إدارية أخرى") }}' }
    ];

    const withdrawReasons = [
        { value: 'manual_admin_deduction', label: '{{ trans("خصم إداري / تسوية خطأ") }}' },
        { value: 'expiry_deduction', label: '{{ trans("انتهاء صلاحية رصيد") }}' },
        { value: 'withdraw', label: '{{ trans("سحب / استرداد نقدي") }}' },
        { value: 'order_payment', label: '{{ trans("دفع طلب") }}' }
    ];

    const currentSelectedTxType = '{{ old("transaction_type", $item->transaction_type ?? "") }}';

    function updatePageReasons(type) {
        const select = document.getElementById('page_transaction_type');
        if (!select) return;
        select.innerHTML = '<option value="">{{ trans("اختر السبب...") }}</option>';
        const list = type === 'withdraw' ? withdrawReasons : depositReasons;
        let matched = false;
        list.forEach(item => {
            const opt = document.createElement('option');
            opt.value = item.value;
            opt.textContent = item.label;
            if (currentSelectedTxType && currentSelectedTxType === item.value) {
                opt.selected = true;
                matched = true;
            }
            select.appendChild(opt);
        });
        if (!matched && list.length > 0) {
            select.value = list[0].value;
        }
    }

    // Toggle Action Type
    const btnDeposit = document.getElementById('page_btn_type_deposit');
    const btnWithdraw = document.getElementById('page_btn_type_withdraw');
    const inputType = document.getElementById('page_action_type');

    function setPageActionType(type) {
        if (!inputType) return;
        inputType.value = type;
        if (type === 'deposit') {
            btnDeposit.style.border = '2px solid #16a34a';
            btnDeposit.style.backgroundColor = '#f0fdf4';
            btnDeposit.style.color = '#16a34a';
            btnDeposit.classList.add('fw-bolder');

            btnWithdraw.style.border = '2px solid #e5e7eb';
            btnWithdraw.style.backgroundColor = '#ffffff';
            btnWithdraw.style.color = '#6b7280';
            btnWithdraw.classList.remove('fw-bolder');
        } else {
            btnWithdraw.style.border = '2px solid #dc2626';
            btnWithdraw.style.backgroundColor = '#fef2f2';
            btnWithdraw.style.color = '#dc2626';
            btnWithdraw.classList.add('fw-bolder');

            btnDeposit.style.border = '2px solid #e5e7eb';
            btnDeposit.style.backgroundColor = '#ffffff';
            btnDeposit.style.color = '#6b7280';
            btnDeposit.classList.remove('fw-bolder');
        }
        updatePageReasons(type);
    }

    if (btnDeposit) btnDeposit.addEventListener('click', () => setPageActionType('deposit'));
    if (btnWithdraw) btnWithdraw.addEventListener('click', () => setPageActionType('withdraw'));

    const initialType = '{{ old("type", $item->type ?? "deposit") }}';
    setPageActionType(initialType);

    // Select2 User Search
    const userSelect = $('#page_user_select');
    if (userSelect.length > 0) {
        userSelect.select2({
            placeholder: '{{ trans("الاسم، رقم الجوال، أو البريد الإلكتروني...") }}',
            allowClear: true,
            ajax: {
                url: '{{ route("dashboard.wallet-transactions.search-users") }}',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return { q: params.term };
                },
                processResults: function(data) {
                    return {
                        results: data.results
                    };
                },
                cache: true
            }
        }).on('select2:select', function(e) {
            const data = e.params.data;
            if (data && typeof data.wallet !== 'undefined') {
                $('#page_customer_balance_val').text(parseFloat(data.wallet).toFixed(2));
            } else if (data && data.id) {
                $.get('{{ route("dashboard.wallet-transactions.user-balance", ":id") }}'.replace(':id', data.id), function(res) {
                    $('#page_customer_balance_val').text(parseFloat(res.wallet || 0).toFixed(2));
                });
            }
        }).on('select2:unselect select2:clearing select2:clear change', function() {
            if (!$(this).val()) {
                $('#page_customer_balance_val').text('0.00');
            }
        });
    }
});
</script>
@endpush
