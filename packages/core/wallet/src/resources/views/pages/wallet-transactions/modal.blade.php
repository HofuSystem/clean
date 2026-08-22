<div class="modal fade" id="wallet-adjustment-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" style="max-width: 680px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            
            <!-- Modal Header -->
            <div class="modal-header border-bottom-0 pb-0 pt-4 px-4 px-md-5 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <span class="d-inline-flex align-items-center justify-content-center bg-light-primary text-primary rounded-3 p-2" style="width: 38px; height: 38px;">
                        <i class="fa fa-wallet fs-4 text-primary"></i>
                    </span>
                    <h5 class="modal-title fw-bolder text-dark mb-0 fs-5" id="walletModalTitle">
                        {{ trans('تسوية إدارية لرصيد محفظة') }}
                    </h5>
                </div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Modal Form -->
            <form id="wallet-adjustment-form" method="POST" action="{{ route('dashboard.wallet-transactions.create') }}">
                @csrf
                <input type="hidden" name="type" id="modal_action_type" value="deposit">
                <input type="hidden" name="transaction_id_record" id="modal_record_id" value="">

                <div class="modal-body px-4 px-md-5 py-4">

                    <!-- Field 1: Client Selection & Current Balance -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label fw-bold fs-7 text-gray-700 mb-0 required">
                                {{ trans('ابحث عن العميل') }}
                            </label>
                            <div class="px-3 py-1 fw-bold rounded-2" id="customer_balance_badge" style="background-color: #eff6ff !important; color: #1d4ed8 !important; border: 1px solid #bfdbfe !important; font-size: 12px;">
                                {{ trans('الرصيد الحالي') }}: <span id="customer_balance_val" class="fw-bolder" style="color: #1d4ed8 !important;">0.00</span> {{ trans('ر.س') }}
                            </div>
                        </div>

                        @if(isset($fixedUser) && $fixedUser)
                            <input type="hidden" name="user_id" id="modal_user_id" value="{{ $fixedUser->id }}">
                            <input type="text" class="form-control form-control-solid bg-light fw-bold" value="{{ $fixedUser->fullname }} ({{ $fixedUser->phone ?: $fixedUser->email }})" readonly>
                        @else
                            <select class="form-select form-select-solid" name="user_id" id="modal_user_select" required style="width: 100%;">
                                <option value="">{{ trans('الاسم، رقم الجوال، أو البريد الإلكتروني...') }}</option>
                            </select>
                        @endif
                    </div>

                    <!-- Field 2: Action Type Segmented Toggle -->
                    <div class="mb-4">
                        <label class="form-label fw-bold fs-7 text-gray-700 mb-2 required">
                            {{ trans('نوع الإجراء') }}
                        </label>
                        <div class="row g-2">
                            <div class="col-6">
                                <button type="button" class="btn w-100 py-3 fw-bolder fs-6 d-flex align-items-center justify-content-center gap-2 action-type-btn active" id="btn_type_deposit" data-type="deposit" style="border-radius: 10px; border: 2px solid #16a34a; background-color: #f0fdf4; color: #16a34a;">
                                    <span class="fs-4">↑</span> {{ trans('إضافة رصيد') }}
                                </button>
                            </div>
                            <div class="col-6">
                                <button type="button" class="btn w-100 py-3 fw-bold fs-6 d-flex align-items-center justify-content-center gap-2 action-type-btn text-muted" id="btn_type_withdraw" data-type="withdraw" style="border-radius: 10px; border: 2px solid #e5e7eb; background-color: #ffffff; color: #6b7280;">
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
                            <select class="form-select form-select-solid" name="transaction_type" id="modal_transaction_type" required>
                                <!-- Populated dynamically by JS -->
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold fs-7 text-gray-700 mb-1 required">
                                {{ trans('المبلغ (ر.س)') }}
                            </label>
                            <div class="input-group">
                                <input type="number" step="0.01" min="0.01" name="amount" id="modal_amount" class="form-control form-control-solid fw-bold" placeholder="0.00" required>
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
                                <input type="text" name="order_reference" id="modal_order_ref" class="form-control form-control-solid" placeholder="{{ trans('مثال: L738925558') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold fs-7 text-gray-700 mb-1">
                                {{ trans('تاريخ الانتهاء (للرصيد الترويجي)') }}
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted border-0"><i class="far fa-calendar-alt"></i></span>
                                <input type="date" name="expired_at" id="modal_expired_at" class="form-control form-control-solid">
                            </div>
                        </div>
                    </div>

                    <!-- Field 5: Details for Review (تفاصيل العملية للمراجعة) -->
                    <div class="mb-4">
                        <label class="form-label fw-bold fs-7 text-gray-700 mb-1 required">
                            {{ trans('تفاصيل العملية للمراجعة') }}
                        </label>
                        <textarea name="notes" id="modal_notes" rows="2" class="form-control form-control-solid" placeholder="{{ trans('اكتب سبب هذه العملية بالتفصيل للرجوع إليها لاحقاً...') }}" required></textarea>
                    </div>

                    <!-- Field 6: Notify Customer Switch -->
                    <div class="d-flex align-items-center justify-content-between p-3 rounded-3" style="background-color: #f8fafc; border: 1px solid #f1f5f9;">
                        <div class="d-flex flex-column">
                            <span class="fw-bolder fs-7 text-dark">{{ trans('إشعار العميل') }}</span>
                            <span class="text-muted fs-8">{{ trans('سيتم إرسال إشعار للمستخدم بتحديث رصيد المحفظة') }}</span>
                        </div>
                        <div class="form-check form-switch form-check-custom form-check-solid">
                            <input class="form-check-input" type="checkbox" name="send_notification" value="1" id="modal_send_notification" checked style="cursor: pointer; width: 44px; height: 24px;">
                        </div>
                    </div>

                </div>

                <!-- Modal Footer -->
                <div class="modal-footer border-top-0 pt-0 pb-4 px-4 px-md-5 d-flex justify-content-start gap-2">
                    <button type="submit" id="btn_submit_wallet_adjustment" class="btn text-white fw-bold px-4 py-2" style="background-color: #244b7d !important; border-radius: 8px; font-size: 14px;">
                        <i class="fa fa-check text-white me-1"></i> {{ trans('تأكيد وحفظ') }}
                    </button>
                    <button type="button" class="btn btn-light text-muted fw-bold px-4 py-2" data-bs-dismiss="modal" style="border-radius: 8px; font-size: 14px;">
                        {{ trans('إلغاء') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

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
        { value: 'withdraw', label: '{{ trans("سحب / استرداد نقدي") }}' }
    ];

    function updateReasons(type) {
        const select = document.getElementById('modal_transaction_type');
        if (!select) return;
        select.innerHTML = '<option value="">{{ trans("اختر السبب...") }}</option>';
        const list = type === 'withdraw' ? withdrawReasons : depositReasons;
        list.forEach(item => {
            const opt = document.createElement('option');
            opt.value = item.value;
            opt.textContent = item.label;
            select.appendChild(opt);
        });
        if (list.length > 0) {
            select.value = list[0].value;
        }
    }

    // Toggle Action Type
    const btnDeposit = document.getElementById('btn_type_deposit');
    const btnWithdraw = document.getElementById('btn_type_withdraw');
    const inputType = document.getElementById('modal_action_type');

    function setActionType(type) {
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
        updateReasons(type);
    }

    if (btnDeposit) btnDeposit.addEventListener('click', () => setActionType('deposit'));
    if (btnWithdraw) btnWithdraw.addEventListener('click', () => setActionType('withdraw'));
    setActionType('deposit');

    // Select2 User Search if available
    const userSelect = $('#modal_user_select');
    if (userSelect.length > 0) {
        userSelect.select2({
            dropdownParent: $('#wallet-adjustment-modal'),
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
                $('#customer_balance_val').text(parseFloat(data.wallet).toFixed(2));
            } else if (data && data.id) {
                $.get('{{ route("dashboard.wallet-transactions.user-balance", ":id") }}'.replace(':id', data.id), function(res) {
                    $('#customer_balance_val').text(parseFloat(res.wallet || 0).toFixed(2));
                });
            }
        }).on('select2:unselect select2:clearing select2:clear change', function() {
            if (!$(this).val()) {
                $('#customer_balance_val').text('0.00');
            }
        });
    }

    // Fixed user balance
    @if(isset($fixedUser) && $fixedUser)
        $('#customer_balance_val').text(parseFloat('{{ (float) $fixedUser->wallet }}').toFixed(2));
    @endif

    // Ajax Form Submit
    $('#wallet-adjustment-form').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        const submitBtn = $('#btn_submit_wallet_adjustment');
        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> {{ trans("جاري الحفظ...") }}');

        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: form.serialize(),
            success: function(res) {
                submitBtn.prop('disabled', false).html('<i class="fa fa-check text-white me-1"></i> {{ trans("تأكيد وحفظ") }}');
                $('#wallet-adjustment-modal').modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: '{{ trans("تم بنجاح") }}',
                    text: res.message || '{{ trans("تم حفظ الحركة وتحديث المحفظة بنجاح") }}',
                    timer: 2000,
                    showConfirmButton: false
                });
                if (typeof window.LaravelDataTables !== 'undefined') {
                    for (const dt in window.LaravelDataTables) {
                        window.LaravelDataTables[dt].ajax.reload(null, false);
                    }
                } else if ($('#table').length && $.fn.DataTable.isDataTable('#table')) {
                    $('#table').DataTable().ajax.reload(null, false);
                } else {
                    location.reload();
                }
            },
            error: function(xhr) {
                submitBtn.prop('disabled', false).html('<i class="fa fa-check text-white me-1"></i> {{ trans("تأكيد وحفظ") }}');
                let err = '{{ trans("حدث خطأ أثناء الحفظ") }}';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    err = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    err = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                }
                Swal.fire({
                    icon: 'error',
                    title: '{{ trans("خطأ") }}',
                    html: err
                });
            }
        });
    });
});
</script>
