/**
 * B2B Client Dashboard Main JS
 * Handles common operations like Logout and Modals
 */

$(document).ready(function () {
    // Initialize common listeners
    initEventListeners();
});

/**
 * Initialize global event listeners
 */
function initEventListeners() {
    // Backdrop click to close modals
    $('#modal-overlay').on('click', function (e) {
        if (e.target === this) {
            closeAllModals();
        }
    });

    // ESC key to close modals
    $(document).on('keydown', function (e) {
        if (e.key === 'Escape') {
            closeAllModals();
        }
    });

    // Mobile menu toggle
    $('#mobile-menu-btn').on('click', function () {
        const isRtl = $('html').attr('dir') === 'rtl';
        const translateClass = isRtl ? 'translate-x-full' : '-translate-x-full';
        $('#main-sidebar').toggleClass(translateClass);
        $('#sidebar-overlay').toggleClass('hidden');
    });

    $('#sidebar-overlay').on('click', function () {
        const isRtl = $('html').attr('dir') === 'rtl';
        const translateClass = isRtl ? 'translate-x-full' : '-translate-x-full';
        $('#main-sidebar').addClass(translateClass);
        $(this).addClass('hidden');
    });
}

/**
 * Handle Logout operation
 */
window.handleLogout = function () {
    Swal.fire({
        title: 'تسجيل الخروج',
        text: 'هل أنت متأكد من تسجيل الخروج من النظام؟',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'نعم، تسجيل الخروج',
        cancelButtonText: 'إلغاء',
        buttonsStyling: false,
        customClass: {
            confirmButton: 'px-6 py-3 bg-red-500 text-white font-bold rounded-xl shadow-lg hover:bg-red-600 transition-all mx-2',
            cancelButton: 'px-6 py-3 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 transition-all mx-2'
        },
        showClass: {
            popup: 'animate__animated animate__fadeInDown animate__faster'
        },
        hideClass: {
            popup: 'animate__animated animate__fadeOutUp animate__faster'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            $('#logout-form').submit();
        }
    });
}

/**
 * Open a specific modal by ID
 * @param {string} modalId 
 */
window.openModal = function (modalId) {
    const $overlay = $('#modal-overlay');
    const $modal = $('#' + modalId);

    if ($modal.length) {
        // Show overlay first
        $overlay.removeClass('hidden').addClass('flex');

        // Use a small timeout to trigger CSS transitions
        setTimeout(() => {
            $overlay.removeClass('opacity-0').addClass('opacity-100');
            $modal.removeClass('hidden').addClass('flex');

            // Always auto-select the first branch if available (excluding empty option)
            const $branchSelect = $modal.find('select[name="branch_id"], select#order-branch-id');
            if ($branchSelect.length && !$branchSelect.val()) {
                const $firstOption = $branchSelect.find('option:not([value=""])').first();
                if ($firstOption.length) {
                    $branchSelect.val($firstOption.val()).trigger('change');
                }
            }

            // Trigger animation
            setTimeout(() => {
                $modal.removeClass('scale-95').addClass('scale-100');

                // If it's the order modal, auto-trigger date fetching if branch is set
                if (modalId === 'order-modal' && $('#order-branch-id').val()) {
                    fetchOrderDates();
                }
            }, 10);
        }, 10);

        // Prevent body scroll
        $('body').addClass('overflow-hidden');
    }
}

/**
 * Close a specific modal by ID
 * @param {string} modalId 
 */
window.closeModal = function (modalId) {
    const $overlay = $('#modal-overlay');
    const $modal = $('#' + modalId);

    if ($modal.length) {
        $modal.removeClass('scale-100').addClass('scale-95');

        setTimeout(() => {
            $modal.addClass('hidden').removeClass('flex');

            // If it's the last modal, hide overlay
            if ($('.modal-content:visible').length === 0) {
                $overlay.removeClass('opacity-100').addClass('opacity-0');
                setTimeout(() => {
                    $overlay.addClass('hidden').removeClass('flex');
                    $('body').removeClass('overflow-hidden');
                }, 300);
            }
        }, 200);
    }
}

/**
 * Close all open modals
 */
window.closeAllModals = function () {
    $('.modal-content:visible').each(function () {
        closeModal($(this).attr('id'));
    });
}

/**
 * Global Toast System
 * @param {string} message 
 * @param {string} type (success, error, info, warning)
 */
window.showToast = function (message, type = 'success') {
    const container = $('#toast-container');
    const id = 'toast-' + Date.now();

    let bgColor = 'bg-white';
    let borderColor = 'border-gray-200';
    let iconColor = 'text-green-500';
    let icon = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>';

    if (type === 'error') {
        iconColor = 'text-red-500';
        borderColor = 'border-red-100';
        icon = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>';
    }

    const toastHtml = `
        <div id="${id}" class="toast p-4 ${bgColor} border ${borderColor} rounded-2xl shadow-xl flex items-center gap-4 transition-all duration-500 transform translate-y-10 opacity-0 min-w-[300px] pointer-events-auto">
            <div class="${iconColor} bg-opacity-10 p-2 rounded-full">
                ${icon}
            </div>
            <div class="flex-1 font-bold text-sm text-gray-800">${message}</div>
            <button onclick="$('#${id}').fadeOut(300, function() { $(this).remove(); })" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
    `;

    container.append(toastHtml);

    const toast = $('#' + id);
    setTimeout(() => {
        toast.removeClass('translate-y-10 opacity-0');
    }, 100);

    // Auto remove
    setTimeout(() => {
        toast.addClass('opacity-0 -translate-y-2');
        setTimeout(() => {
            toast.remove();
        }, 500);
    }, 5000);
}

/**
 * Handle Order Type Toggle (Contract vs Guest)
 * @param {string} type 
 */
window.setOrderType = function (type) {
    const $btnContract = $('#btn-type-contract');
    const $btnGuest = $('#btn-type-guest');
    const $guestFields = $('.guest-fields');

    if (type === 'company') {
        $btnContract.addClass('bg-white text-[#1c75bc] shadow-[0_4px_15px_rgba(0,0,0,0.05)]').removeClass('text-gray-500 hover:text-gray-700');
        $btnGuest.removeClass('bg-white text-[#1c75bc] shadow-[0_4px_15px_rgba(0,0,0,0.05)]').addClass('text-gray-500 hover:text-gray-700');
        $guestFields.addClass('hidden');
        $('#order_type_input').val('company'); // Updated to company
    } else {
        $btnGuest.addClass('bg-white text-[#1c75bc] shadow-[0_4px_15px_rgba(0,0,0,0.05)]').removeClass('text-gray-500 hover:text-gray-700');
        $btnContract.removeClass('bg-white text-[#1c75bc] shadow-[0_4px_15px_rgba(0,0,0,0.05)]').addClass('text-gray-500 hover:text-gray-700');
        $guestFields.removeClass('hidden');
        $('#order_type_input').val('client'); // Updated to client
    }
}

/**
 * Submit Order Placeholder
 * @param {Event} e 
 */
window.submitOrder = function (e) {
    e.preventDefault();
    const $form = $('#orderForm');
    const $submitBtn = $form.closest('.modal-content').find('button[type="submit"]');
    const originalText = $submitBtn.html();
    const data = $form.serialize();
    $submitBtn.prop('disabled', true).addClass('opacity-70').html('<span class="flex items-center gap-2">جاري المعالجة...</span>');

    $.ajax({
        url: '/business/order',
        method: 'POST',
        data: data,
        dataType: 'json',
        success: function (response) {
            showToast(response.message, 'success');
            closeModal('order-modal');
            window.location.href = response.url;
        },
        error: function (xhr) {
            let msg = 'فشل في إرسال الطلب. يرجى المحاولة لاحقاً.';
            const errors = xhr.responseJSON?.errors;
            if (errors) {
                msg = Object.values(errors).flat().join('<br>');
            } else if (xhr.responseJSON?.message) {
                msg = xhr.responseJSON.message;
            }
            showToast(msg, 'error');
            $submitBtn.prop('disabled', false).removeClass('opacity-70').html(originalText);
        }
    });
}

/**
 * Variables to store fetched dates/times
 */
let orderDatesData = [];

/**
 * Fetch dates/times via AJAX
 */
window.fetchOrderDates = function () {
    const branchId = $('#order-branch-id').val();
    if (!branchId) return;

    const $loading = $('#order-modal-loading');
    $loading.removeClass('hidden').addClass('flex');

    $.ajax({
        url: '/business/order/get-dates-times',
        method: 'POST',
        data: {
            branch_id: branchId,
            b2b_type: $('#order_type_input').val(),
            order_type: 'clothes', // Default for now
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function (response) {
            if (response.data) {
                orderDatesData = response.data;
                populateDateSelects();
            }
        },
        complete: function () {
            $loading.addClass('hidden').removeClass('flex');
        }
    });
}

function populateDateSelects() {
    const $pickupDate = $('#order-pickup-date');
    const $deliveryDate = $('#order-delivery-date');

    $pickupDate.find('option:not(:first)').remove();
    $deliveryDate.find('option:not(:first)').remove();

    orderDatesData.forEach(item => {
        const optionHtml = `<option value="${item.date}">${item.date} (${item.day})</option>`;
        $pickupDate.append(optionHtml);
        $deliveryDate.append(optionHtml);
    });

    // Reset times
    $('#order-pickup-time').find('option:not(:first)').remove();
    $('#order-delivery-time').find('option:not(:first)').remove();
}

window.updatePickupTimes = function () {
    const selectedDate = $('#order-pickup-date').val();
    const $timeSelect = $('#order-pickup-time');
    $timeSelect.find('option:not(:first)').remove();

    if (!selectedDate) return;

    const dateData = orderDatesData.find(d => d.date === selectedDate);
    if (dateData && dateData.times) {
        dateData.times.forEach(t => {
            if (t.isAvailable) {
                const optionHtml = `<option value="${t.id}">${t.from} - ${t.to}</option>`;
                $timeSelect.append(optionHtml);
            }
        });
    }
}

window.updateDeliveryTimes = function () {
    const selectedDate = $('#order-delivery-date').val();
    const $timeSelect = $('#order-delivery-time');
    $timeSelect.find('option:not(:first)').remove();

    if (!selectedDate) return;

    const dateData = orderDatesData.find(d => d.date === selectedDate);
    if (dateData && dateData.times) {
        dateData.times.forEach(t => {
            if (t.isAvailable) {
                const optionHtml = `<option value="${t.id}">${t.from} - ${t.to}</option>`;
                $timeSelect.append(optionHtml);
            }
        });
    }
}

/**
 * View Order Details in a Modal
 * @param {number} orderId 
 */
window.viewOrderDetails = function (orderId) {
    const $modal = $('#order-details-modal');
    const $content = $('#order-details-content');

    $content.html('<div class="p-12 text-center"><div class="w-10 h-10 border-4 border-[#1c75bc] border-t-transparent rounded-full animate-spin mx-auto mb-4"></div><p class="font-bold text-gray-400">جاري تحميل تفاصيل الطلب...</p></div>');
    openModal('order-details-modal');

    $.ajax({
        url: '/business/order/' + orderId,
        method: 'GET',
        success: function (html) {
            $content.html(html);
        },
        error: function () {
            $content.html('<div class="p-12 text-center text-red-500 font-bold">فشل في تحميل تفاصيل الطلب. يرجى المحاولة لاحقاً.</div>');
        }
    });
}

