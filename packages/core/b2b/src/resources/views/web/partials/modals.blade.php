@php
    $b2bRole = request()->attributes->get('b2b_role');
    $b2bBranches = \Core\B2B\Models\CompanyBranch::b2bUnderManagement('manage-orders')->get();
    $b2bPermissions = \Core\B2B\Models\CompanyPermission::all();
    $b2bContext = \Core\B2B\Helpers\B2BHelper::getCreationContext();
@endphp

<style>
    /* Premium Select Styling */
    select.select-premium {
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%231c75bc' stroke-width='3'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 1.25rem center;
        background-size: 1.25rem;
        padding-right: 3.5rem !important;
    }

    [dir="rtl"] select.select-premium {
        background-position: left 1.25rem center;
        padding-right: 1.25rem !important;
        padding-left: 3.5rem !important;
    }

    select.select-premium:focus {
        box-shadow: 0 0 0 4px rgba(28, 117, 188, 0.1);
    }
</style>

<!-- ========================================== -->
<div id="modal-overlay"
    class="fixed inset-0 bg-[#0a192f]/50 backdrop-blur-sm z-[70] hidden items-center justify-center p-4 transition-opacity opacity-0 print:bg-transparent print:p-0">

    <!-- 1. Order Modal -->
    <div id="order-modal"
        class="modal-content hidden bg-white/95 backdrop-blur-2xl rounded-[32px] shadow-[0_30px_60px_-15px_rgba(0,0,0,0.4)] border border-white/50 w-full max-w-3xl max-h-[90vh] overflow-y-auto flex-col relative transform scale-95 transition-transform duration-300 print-hidden text-right dir-dependent-text">
        <div
            class="p-8 border-b border-gray-100/50 bg-gradient-to-b from-gray-50/50 to-white flex justify-between items-center rounded-t-[32px] sticky top-0 z-20 dir-dependent-flex">
            <button type="button" onclick="closeModal('order-modal')"
                class="p-2.5 bg-white border border-gray-200 text-gray-500 rounded-full hover:bg-gray-100 transition-colors shadow-sm"><svg
                    class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg></button>
            <h2 class="text-2xl font-black text-gray-900 tracking-tight" data-i18n="new_order">إنشاء طلب جديد</h2>
        </div>
        <div class="p-8 flex-grow">
            <div class="flex justify-center mb-10">
                <div
                    class="inline-flex bg-gray-100/80 p-1.5 rounded-2xl w-full max-w-md shadow-inner border border-gray-200/50 backdrop-blur-sm flex-row-reverse dir-dependent-flex">
                    <button type="button" onclick="setOrderType('company')" id="btn-type-contract"
                        class="flex-1 py-3 px-4 rounded-xl text-sm font-black transition-all duration-300 bg-white text-[#1c75bc] shadow-[0_4px_15px_rgba(0,0,0,0.05)]"
                        data-i18n="contract_order">عقد الفندق</button>
                    <button type="button" onclick="setOrderType('client')" id="btn-type-guest"
                        class="flex-1 py-3 px-4 rounded-xl text-sm font-black transition-all duration-300 text-gray-500 hover:text-gray-700"
                        data-i18n="guest_order">نزيل الفندق</button>
                </div>
            </div>
            <form id="orderForm" onsubmit="submitOrder(event)" class="space-y-8">
                @csrf
                <input type="hidden" name="type" id="type" value="clothes">
                <input type="hidden" name="b2b_type" id="order_type_input" value="company">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2 md:col-span-2">
                        <label
                            class="text-xs font-bold text-gray-400 uppercase tracking-widest px-1 block mb-1">{{ trans('client.branch') }}
                            *</label>
                        <select name="branch_id" id="order-branch-id"
                            class="select-premium w-full p-4 bg-gray-50 border border-gray-200 rounded-2xl outline-none focus:border-[#1c75bc] focus:bg-white transition-all font-bold text-gray-800"
                            required onchange="fetchOrderDates()">
                            <option value="">{{ trans('client.select_branch') }}</option>
                            @foreach($b2bBranches as $branch)
                                <option value="{{ $branch->id }}" >{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>
                  
                    <div class="space-y-2 hidden guest-fields">
                        <label class="text-xs font-bold text-gray-400 uppercase tracking-widest px-1 block mb-1">اسم
                            النزيل *</label>
                        <input type="text" name="customer_name"
                            class="w-full p-4 bg-gray-50 border border-gray-200 rounded-2xl outline-none focus:border-[#1c75bc] focus:bg-white transition-all font-bold text-gray-800"
                            placeholder="الاسم الكامل" />
                    </div>
                    <div class="space-y-2 hidden guest-fields">
                        <label class="text-xs font-bold text-gray-400 uppercase tracking-widest px-1 block mb-1">رقم
                            الغرفة *</label>
                        <input type="text" name="room_number"
                            class="w-full p-4 bg-gray-50 border border-gray-200 rounded-2xl outline-none focus:border-[#1c75bc] focus:bg-white transition-all font-black text-[#1c75bc]"
                            placeholder="مثال: 405" />
                    </div>
                    <div class="space-y-2 hidden guest-fields md:col-span-2">
                        <label class="text-xs font-bold text-gray-400 uppercase tracking-widest px-1 block mb-1">رقم
                            جوال النزيل</label>
                        <input type="tel" name="customer_phone" dir="ltr" placeholder="+966 5X XXX XXXX"
                            class="w-full p-4 bg-gray-50 border border-gray-200 rounded-2xl outline-none focus:border-[#1c75bc] focus:bg-white transition-all font-bold text-gray-800 text-left" />
                    </div>
                    <div class="space-y-2 md:col-span-2" id="service-type-container">
                        <label class="text-xs font-bold text-gray-400 uppercase tracking-widest px-1 block mb-1">نوع
                            الخدمة *</label>
                        <select name="service_type"
                            class="select-premium w-full p-4 bg-gray-50 border border-gray-200 rounded-2xl outline-none focus:border-[#1c75bc] focus:bg-white transition-all font-bold text-gray-800"
                            required>
                            <option value="">{{ trans('client.select_service_type') }}</option>
                            <option value="غسيل و كوي">{{ trans('client.washing_and_ironing') }}</option>
                            <option value="تنظيف جاف">{{ trans('client.dry_clean') }}</option>
                            <option value="كوي فقط">{{ trans('client.ironing') }}</option>
                            <option value="غسيل فقط">{{ trans('client.washing') }}</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-blue-50/50 p-6 rounded-3xl border border-blue-100/50 space-y-4">
                        <h3
                            class="font-black text-[#1c75bc] text-sm flex items-center gap-2 justify-start dir-dependent-flex">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg><span data-i18n="pickup_date">موعد الاستلام</span></h3>
                        <div class="flex flex-col gap-3">
                            <select id="order-pickup-date" name="receiving_date"
                                class="select-premium w-full p-4 bg-gray-50 border border-gray-200 rounded-2xl outline-none focus:border-[#1c75bc] focus:bg-white transition-all font-bold text-gray-800"
                                required data-placeholder="تاريخ الاستلام" onchange="updatePickupTimes()">
                                <option value="">تاريخ الاستلام</option>
                            </select>
                            <select id="order-pickup-time" name="receiving_time"
                                class="select-premium w-full p-4 bg-gray-50 border border-gray-200 rounded-2xl outline-none focus:border-[#1c75bc] focus:bg-white transition-all font-bold text-gray-800"
                                required data-placeholder="فترة الاستلام">
                                <option value="">فترة الاستلام</option>
                            </select>
                        </div>
                    </div>
                    <div class="bg-green-50/50 p-6 rounded-3xl border border-green-100/50 space-y-4">
                        <h3
                            class="font-black text-green-600 text-sm flex items-center gap-2 justify-start dir-dependent-flex">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg><span data-i18n="delivery_date">موعد التسليم</span></h3>
                        <div class="flex flex-col gap-3">
                            <select id="order-delivery-date" name="delivery_date"
                                class="select-premium w-full p-4 bg-gray-50 border border-gray-200 rounded-2xl outline-none focus:border-[#1c75bc] focus:bg-white transition-all font-bold text-gray-800"
                                required data-placeholder="تاريخ التسليم" onchange="updateDeliveryTimes()">
                                <option value="">تاريخ التسليم</option>
                            </select>
                            <select id="order-delivery-time" name="delivery_time"
                                class="select-premium w-full p-4 bg-gray-50 border border-gray-200 rounded-2xl outline-none focus:border-[#1c75bc] focus:bg-white transition-all font-bold text-gray-800"
                                required data-placeholder="فترة التسليم">
                                <option value="">فترة التسليم</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div id="order-modal-loading"
                    class="hidden absolute inset-0 bg-white/60 backdrop-blur-[2px] z-[30] items-center justify-center rounded-[32px]">
                    <div class="flex flex-col items-center gap-4">
                        <div class="w-12 h-12 border-4 border-[#1c75bc] border-t-transparent rounded-full animate-spin">
                        </div>
                        <p class="font-bold text-[#1c75bc] text-sm">{{ trans('client.loading') }}</p>
                    </div>
                </div>
                <div class="space-y-2">
                    <label class="text-xs font-bold text-gray-400 uppercase tracking-widest px-1 block mb-1">ملاحظات
                        الطلب (اختياري)</label>
                    <textarea name="notes" rows="3"
                        class="w-full p-4 bg-gray-50 border border-gray-200 rounded-2xl outline-none focus:border-[#1c75bc] focus:bg-white transition-all resize-none font-medium text-sm text-gray-800"
                        placeholder="اكتب أي ملاحظات إضافية تخص الغسيل، الكوي، أو التغليف..."></textarea>
                </div>
            </form>
        </div>
        <div
            class="p-6 border-t border-gray-100/50 bg-gray-50/30 flex justify-end gap-3 rounded-b-[32px] dir-dependent-flex">
            <button type="submit" form="orderForm"
                class="px-10 py-4 bg-gradient-to-r from-[#1c75bc] to-[#155a91] text-white font-black tracking-wide rounded-2xl shadow-lg shadow-blue-500/30 hover:shadow-blue-500/50 transition-all hover:-translate-y-0.5">
                <span data-i18n="confirm_order">تأكيد الطلب</span>
            </button>
            <button type="button" onclick="closeModal('order-modal')"
                class="px-8 py-4 bg-white border border-gray-200 text-gray-600 font-bold rounded-2xl hover:bg-gray-50 transition-all shadow-sm">
                <span data-i18n="cancel">إلغاء</span>
            </button>
        </div>
    </div>

    <!-- 2. Add User / Role Modal -->
    <div id="user-modal"
        class="modal-content hidden bg-white rounded-3xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-y-auto flex-col relative transform scale-95 transition-transform duration-300 text-right dir-dependent-text custom-scrollbar">
        <div
            class="p-6 border-b border-gray-100 bg-gray-50 flex justify-between items-center sticky top-0 z-10 rounded-t-3xl dir-dependent-flex">
            <button type="button" onclick="closeModal('user-modal')"
                class="text-gray-400 hover:text-red-500 transition-colors"><svg class="w-6 h-6" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg></button>
            <h2 id="user-modal-title" class="text-2xl font-black text-[#1c75bc]">إضافة مستخدم وتحديد صلاحيات النظام</h2>
        </div>
        <form id="userForm" class="p-8 space-y-8" action="{{ route('client.employees.store') }}" method="POST">
            @csrf
            <div id="user-method-field"></div>
            <div class="space-y-6">
                <h3 class="font-black text-gray-800 border-b border-gray-100 pb-3 text-lg">البيانات الأساسية ومعلومات الدخول</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-sm font-bold text-gray-700 block mb-1">اسم الموظف <span class="text-red-500">*</span></label>
                        <input name="fullname" id="user-fullname-input" type="text"
                            class="w-full p-4 bg-white border border-gray-200 rounded-xl outline-none focus:border-[#1c75bc] transition-all font-bold text-gray-800"
                            required placeholder="الاسم الكامل" />
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-bold text-gray-700 block mb-1">رقم الجوال <span class="text-red-500">*</span></label>
                        <input name="phone" id="user-phone-input" type="tel"
                            class="w-full p-4 bg-white border border-gray-200 rounded-xl outline-none focus:border-[#1c75bc] transition-all text-left font-bold"
                            dir="ltr" required placeholder="+966 5X XXX XXXX" />
                    </div>
                    <div class="space-y-2 md:col-span-2">
                        <label class="text-sm font-bold text-gray-700 block mb-2">صلاحيات النظام (Permissions) <span class="text-red-500">*</span></label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($b2bPermissions as $permission)
                                <label class="flex items-start gap-3 p-4 bg-white border border-gray-100 rounded-xl cursor-pointer hover:bg-blue-50/30 transition-colors shadow-sm">
                                    <input type="checkbox" name="permission_ids[]" value="{{ $permission->id }}" 
                                        class="user-permission-checkbox mt-1 w-5 h-5 text-[#1c75bc] rounded border-gray-300 focus:ring-[#1c75bc]">
                                    <div>
                                        <span class="font-black text-gray-800 block text-xs">{{ $permission->name }}</span>
                                        <span class="text-[10px] text-gray-400 font-bold leading-relaxed">
                                            {{ $permission->description ?? trans('client.permission_desc_' . $permission->slug) }}
                                        </span>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    <div class="space-y-2 md:col-span-2">
                        <label class="text-sm font-bold text-gray-700 block mb-1">الفرع المسند إليه</label>
                        <select name="branch_id" id="user-branch-id"
                            class="select-premium w-full p-4 bg-white border border-gray-200 rounded-xl outline-none focus:border-[#1c75bc] transition-all font-bold text-gray-800">
                            <option value="">كافة الفروع</option>
                            @foreach($b2bBranches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-6 border-t border-gray-100 dir-dependent-flex">
                <button type="submit"
                    class="px-10 py-3.5 bg-[#1c75bc] text-white font-bold rounded-xl shadow-md hover:bg-[#155a91] transition-transform hover:-translate-y-0.5">حفظ
                    بيانات المستخدم</button>
                <button type="button" onclick="closeModal('user-modal')"
                    class="px-8 py-3.5 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 transition-colors">إلغاء</button>
            </div>
        </form>
    </div>

    <!-- 3. Support Ticket New Modal -->
    <div id="ticket-new-modal"
        class="modal-content hidden bg-white rounded-3xl shadow-2xl w-full max-w-lg overflow-hidden flex-col relative transform scale-95 transition-transform duration-300 text-center p-12">
        <div class="mb-6 flex justify-center">
            <div class="p-4 bg-blue-50 text-[#1c75bc] rounded-full">
                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z">
                    </path>
                </svg>
            </div>
        </div>
        <h2 class="text-3xl font-black text-gray-900 mb-2">{{ trans('client.support_coming_soon') }}</h2>
        <p class="text-gray-500 font-medium mb-8">{{ trans('client.support_coming_soon_desc') }}</p>
        <button type="button" onclick="closeModal('ticket-new-modal')"
            class="w-full py-4 bg-gray-900 text-white font-black rounded-2xl hover:bg-black transition-all shadow-lg">{{ trans('client.excellent_understanding') }}</button>
    </div>

    <!-- 4. Support Ticket VIEW Chat Modal -->
    <div id="ticket-view-modal"
        class="modal-content hidden bg-white rounded-3xl shadow-2xl w-full max-w-3xl overflow-hidden flex-col relative transform scale-95 transition-transform duration-300 text-right dir-dependent-text h-[85vh]">
        <div
            class="p-6 border-b border-gray-100 bg-gray-50 flex justify-between items-center dir-dependent-flex shrink-0">
            <div class="flex items-center gap-3">
                <button type="button" onclick="closeModal('ticket-view-modal')"
                    class="p-2 bg-white border border-gray-200 text-gray-500 rounded-full hover:bg-gray-100 transition-colors shadow-sm"><svg
                        class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg></button>
                <div>
                    <h2 class="text-lg font-black text-gray-900" id="view-ticket-title">تأخير استلام الطلبات</h2>
                    <p class="text-xs font-bold text-gray-500">التذكرة #T-2041 | القسم: العمليات</p>
                </div>
            </div>
            <button type="button"
                onclick="showToast('تم إغلاق التذكرة بنجاح', 'success'); closeModal('ticket-view-modal');"
                class="bg-red-50 text-red-600 hover:bg-red-100 px-5 py-2 rounded-xl text-sm font-black transition-colors border border-red-100">إغلاق
                التذكرة</button>
        </div>

        <div class="flex-1 p-6 overflow-y-auto bg-gray-50/50 space-y-6 flex flex-col custom-scrollbar">
            <!-- Chat Bubbles -->
            <div class="flex flex-col max-w-[85%] self-end">
                <span class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-1.5 px-1">أنت • 10:00
                    AM</span>
                <div class="p-4 rounded-2xl text-sm font-medium shadow-sm bg-[#1c75bc] text-white rounded-tr-sm">
                    السلام عليكم، السائق لم يصل لاستلام طلبات العقد حتى الآن، الرجاء المتابعة بشكل عاجل.
                </div>
            </div>

            <div class="flex flex-col max-w-[85%] self-start">
                <span class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-1.5 px-1">دعم كلين ستيشن •
                    10:15 AM</span>
                <div
                    class="p-4 rounded-2xl text-sm font-medium shadow-sm bg-white border border-gray-200 text-gray-800 rounded-tl-sm">
                    وعليكم السلام، نعتذر عن هذا التأخير. تم التواصل مع مندوب التوصيل وسيكون عندكم خلال 15 دقيقة كحد
                    أقصى. شكراً لصبركم.
                </div>
            </div>
        </div>

        <div class="p-6 border-t border-gray-100 bg-white shrink-0">
            <form onsubmit="event.preventDefault(); showToast('تم إرسال الرد', 'success'); this.reset();"
                class="flex gap-3 dir-dependent-flex">
                <input type="text" placeholder="اكتب ردك هنا..."
                    class="flex-1 p-4 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:border-[#1c75bc] focus:bg-white transition-all text-sm font-medium"
                    required />
                <button type="submit"
                    class="bg-gray-900 text-white px-8 rounded-xl font-black shadow-lg hover:bg-black transition-transform hover:-translate-y-0.5">إرسال</button>
            </form>
        </div>
    </div>

    <!-- 5. Add Address Modal -->
    <div id="address-modal"
        class="modal-content hidden bg-white rounded-3xl shadow-2xl w-full max-w-2xl overflow-hidden flex-col relative transform scale-95 transition-transform duration-300 text-right dir-dependent-text">
        <div class="p-6 border-b border-gray-100 bg-gray-50 flex justify-between items-center dir-dependent-flex">
            <button type="button" onclick="closeModal('address-modal')"
                class="text-gray-400 hover:text-red-500 transition-colors"><svg class="w-6 h-6" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg></button>
            <h2 class="text-2xl font-black text-[#1c75bc]">إضافة عنوان فرع جديد</h2>
        </div>
        <form class="p-8 space-y-6"
            onsubmit="event.preventDefault(); showToast('تم إضافة العنوان بنجاح', 'success'); closeModal('address-modal');">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="text-sm font-bold text-gray-700 block mb-1">اسم الفرع <span
                            class="text-red-500">*</span></label>
                    <input type="text"
                        class="w-full p-4 bg-white border border-gray-200 rounded-xl outline-none focus:border-[#1c75bc] transition-all font-bold"
                        placeholder="مثال: فرع الشمال" required />
                </div>
                <div class="space-y-2">
                    <label class="text-sm font-bold text-gray-700 block mb-1">المدينة <span
                            class="text-red-500">*</span></label>
                    <select
                        class="select-premium w-full p-4 bg-white border border-gray-200 rounded-xl outline-none focus:border-[#1c75bc] transition-all font-bold text-gray-800"
                        required>
                        <option value="">اختر المدينة...</option>
                        <option>الرياض</option>
                        <option>جدة</option>
                        <option>الدمام</option>
                    </select>
                </div>
                <div class="space-y-2">
                    <label class="text-sm font-bold text-gray-700 block mb-1">الحي <span
                            class="text-red-500">*</span></label>
                    <input type="text"
                        class="w-full p-4 bg-white border border-gray-200 rounded-xl outline-none focus:border-[#1c75bc] transition-all font-bold"
                        required />
                </div>
                <div class="space-y-2">
                    <label class="text-sm font-bold text-gray-700 block mb-1">الشارع / رقم المبنى</label>
                    <input type="text"
                        class="w-full p-4 bg-white border border-gray-200 rounded-xl outline-none focus:border-[#1c75bc] transition-all font-bold" />
                </div>
            </div>
            <div class="space-y-2">
                <label class="text-sm font-bold text-gray-700 block mb-1">رابط الموقع على الخريطة (Google Maps)</label>
                <input type="url" dir="ltr"
                    class="w-full p-4 bg-white border border-gray-200 rounded-xl outline-none focus:border-[#1c75bc] transition-all font-bold text-left"
                    placeholder="https://maps.google.com/..." />
            </div>
            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100 dir-dependent-flex">
                <button type="submit"
                    class="px-10 py-3.5 bg-[#1c75bc] text-white font-bold rounded-xl shadow-md hover:bg-[#155a91] transition-transform hover:-translate-y-0.5">حفظ
                    العنوان</button>
                <button type="button" onclick="closeModal('address-modal')"
                    class="px-8 py-3.5 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 transition-colors">إلغاء</button>
            </div>
        </form>
    </div>

    <!-- 6. Add Weekly Schedule Modal -->
    <div id="schedule-modal"
        class="modal-content hidden bg-white rounded-3xl shadow-2xl w-full max-w-2xl overflow-hidden flex-col relative transform scale-95 transition-transform duration-300 text-right dir-dependent-text">
        <div class="p-6 border-b border-gray-100 bg-gray-50 flex justify-between items-center dir-dependent-flex">
            <button type="button" onclick="closeModal('schedule-modal')"
                class="text-gray-400 hover:text-red-500 transition-colors"><svg class="w-6 h-6" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg></button>
            <h2 class="text-2xl font-black text-[#1c75bc]">{{ trans('client.add_weekly_schedule') }}</h2>
        </div>
        <form id="weeklyScheduleForm" action="{{ route('client.schedule.store') }}" method="POST" class="p-8 space-y-6">
            @csrf
            <input type="hidden" name="type" value="day">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @if($b2bRole === 'owner' || $b2bRole === 'manager_all_branches')
                    <div class="space-y-2 md:col-span-2">
                        <label class="text-sm font-bold text-gray-700 block mb-1">{{ trans('client.branch') }} *</label>
                        <select name="branch_id" class="select-premium w-full p-4 bg-white border border-gray-200 rounded-xl outline-none focus:border-[#1c75bc] transition-all font-bold text-gray-800" required>
                            <option value="">{{ trans('client.select_branch') }}</option>
                            @foreach($b2bBranches as $branch)
                                <option value="{{ $branch->id }}" >{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div class="space-y-2">
                    <label class="text-sm font-bold text-gray-700 block mb-1">{{ trans('client.receiver_day') }} *</label>
                    <select name="receiver_day" class="select-premium w-full p-4 bg-white border border-gray-200 rounded-xl outline-none focus:border-[#1c75bc] transition-all font-bold text-gray-800" required>
                        <option value="sunday">{{ trans('Sunday') }}</option>
                        <option value="monday">{{ trans('Monday') }}</option>
                        <option value="tuesday">{{ trans('Tuesday') }}</option>
                        <option value="wednesday">{{ trans('Wednesday') }}</option>
                        <option value="thursday">{{ trans('Thursday') }}</option>
                        <option value="friday">{{ trans('Friday') }}</option>
                        <option value="saturday">{{ trans('Saturday') }}</option>
                    </select>
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-bold text-gray-700 block mb-1">{{ trans('client.pickup_time') }} *</label>
                    <div class="grid grid-cols-2 gap-2">
                        <input type="time" name="receiver_time" class="w-full p-4 bg-white border border-gray-200 rounded-xl outline-none focus:border-[#1c75bc] transition-all font-bold text-gray-800" required>
                        <input type="time" name="receiver_to_time" class="w-full p-4 bg-white border border-gray-200 rounded-xl outline-none focus:border-[#1c75bc] transition-all font-bold text-gray-800" required>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-bold text-gray-700 block mb-1">{{ trans('client.delivery_day') }} *</label>
                    <select name="delivery_day" class="select-premium w-full p-4 bg-white border border-gray-200 rounded-xl outline-none focus:border-[#1c75bc] transition-all font-bold text-gray-800" required>
                        <option value="sunday">{{ trans('Sunday') }}</option>
                        <option value="monday">{{ trans('Monday') }}</option>
                        <option value="tuesday">{{ trans('Tuesday') }}</option>
                        <option value="wednesday">{{ trans('Wednesday') }}</option>
                        <option value="thursday">{{ trans('Thursday') }}</option>
                        <option value="friday">{{ trans('Friday') }}</option>
                        <option value="saturday">{{ trans('Saturday') }}</option>
                    </select>
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-bold text-gray-700 block mb-1">{{ trans('client.delivery_time') }} *</label>
                    <div class="grid grid-cols-2 gap-2">
                        <input type="time" name="delivery_time" class="w-full p-4 bg-white border border-gray-200 rounded-xl outline-none focus:border-[#1c75bc] transition-all font-bold text-gray-800" required>
                        <input type="time" name="delivery_to_time" class="w-full p-4 bg-white border border-gray-200 rounded-xl outline-none focus:border-[#1c75bc] transition-all font-bold text-gray-800" required>
                    </div>
                </div>

                <div class="space-y-2 md:col-span-2">
                    <label class="text-sm font-bold text-gray-700 block mb-1">{{ trans('client.notes') }}</label>
                    <input type="text" name="note" class="w-full p-4 bg-white border border-gray-200 rounded-xl outline-none focus:border-[#1c75bc] transition-all font-bold" placeholder="{{ trans('client.example_only_washing_and_ironing') }}" />
                </div>
            </div>
            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100 dir-dependent-flex">
                <button type="submit" class="px-10 py-3.5 bg-[#1c75bc] text-white font-bold rounded-xl shadow-md hover:bg-[#155a91] transition-transform hover:-translate-y-0.5">{{ trans('client.save') }}</button>
                <button type="button" onclick="closeModal('schedule-modal')" class="px-8 py-3.5 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 transition-colors">{{ trans('client.cancel') }}</button>
            </div>
        </form>
    </div>


    <!-- 6b. Add Date Schedule Modal -->
    <div id="schedule-date-modal"
        class="modal-content hidden bg-white rounded-3xl shadow-2xl w-full max-w-2xl overflow-hidden flex-col relative transform scale-95 transition-transform duration-300 text-right dir-dependent-text">
        <div class="p-6 border-b border-gray-100 bg-gray-50 flex justify-between items-center dir-dependent-flex">
            <button type="button" onclick="closeModal('schedule-date-modal')"
                class="text-gray-400 hover:text-red-500 transition-colors"><svg class="w-6 h-6" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg></button>
            <h2 class="text-2xl font-black text-green-600">{{ trans('client.add_date_schedule') }}</h2>
        </div>
        <form id="dateScheduleForm" action="{{ route('client.schedule.store') }}" method="POST" class="p-8 space-y-6">
            @csrf
            <input type="hidden" name="type" value="date">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @if($b2bRole === 'owner' || $b2bRole === 'manager_all_branches')
                    <div class="space-y-2 md:col-span-2">
                        <label class="text-sm font-bold text-gray-700 block mb-1">{{ trans('client.branch') }} *</label>
                        <select name="branch_id" class="select-premium w-full p-4 bg-white border border-gray-200 rounded-xl outline-none focus:border-[#1c75bc] transition-all font-bold text-gray-800" required>
                            <option value="">{{ trans('client.select_branch') }}</option>
                            @foreach($b2bBranches as $branch)
                                <option value="{{ $branch->id }}" >{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div class="space-y-2">
                    <label class="text-sm font-bold text-gray-700 block mb-1">{{ trans('client.receiver_date') }} *</label>
                    <input type="date" name="receiver_date" class="w-full p-4 bg-white border border-gray-200 rounded-xl outline-none focus:border-[#1c75bc] transition-all font-bold text-gray-800" required min="{{ date('Y-m-d') }}">
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-bold text-gray-700 block mb-1">{{ trans('client.pickup_time') }} *</label>
                    <div class="grid grid-cols-2 gap-2">
                        <input type="time" name="receiver_time" class="w-full p-4 bg-white border border-gray-200 rounded-xl outline-none focus:border-[#1c75bc] transition-all font-bold text-gray-800" required>
                        <input type="time" name="receiver_to_time" class="w-full p-4 bg-white border border-gray-200 rounded-xl outline-none focus:border-[#1c75bc] transition-all font-bold text-gray-800" required>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-bold text-gray-700 block mb-1">{{ trans('client.delivery_date') }} *</label>
                    <input type="date" name="delivery_date" class="w-full p-4 bg-white border border-gray-200 rounded-xl outline-none focus:border-[#1c75bc] transition-all font-bold text-gray-800" required min="{{ date('Y-m-d') }}">
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-bold text-gray-700 block mb-1">{{ trans('client.delivery_time') }} *</label>
                    <div class="grid grid-cols-2 gap-2">
                        <input type="time" name="delivery_time" class="w-full p-4 bg-white border border-gray-200 rounded-xl outline-none focus:border-[#1c75bc] transition-all font-bold text-gray-800" required>
                        <input type="time" name="delivery_to_time" class="w-full p-4 bg-white border border-gray-200 rounded-xl outline-none focus:border-[#1c75bc] transition-all font-bold text-gray-800" required>
                    </div>
                </div>

                <div class="space-y-2 md:col-span-2">
                    <label class="text-sm font-bold text-gray-700 block mb-1">{{ trans('client.notes') }}</label>
                    <input type="text" name="note" class="w-full p-4 bg-white border border-gray-200 rounded-xl outline-none focus:border-[#1c75bc] transition-all font-bold" placeholder="{{ trans('client.example_only_washing_and_ironing') }}" />
                </div>
            </div>
            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100 dir-dependent-flex">
                <button type="submit" class="px-10 py-3.5 bg-green-600 text-white font-bold rounded-xl shadow-md hover:bg-green-700 transition-transform hover:-translate-y-0.5">{{ trans('client.save') }}</button>
                <button type="button" onclick="closeModal('schedule-date-modal')" class="px-8 py-3.5 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 transition-colors">{{ trans('client.cancel') }}</button>
            </div>
        </form>
    </div>

    <div id="order-details-modal"
        class="modal-content hidden invoice-print-area bg-white/95 backdrop-blur-3xl rounded-[32px] shadow-[0_20px_60px_-15px_rgba(0,0,0,0.3)] border border-white/50 w-full max-w-4xl max-h-[90vh] overflow-y-auto flex-col relative transform scale-95 transition-transform duration-300 print:shadow-none print:rounded-none print:border-none text-right dir-dependent-text custom-scrollbar">
        <div id="order-details-content">
            <!-- Injected via JS -->
        </div>
    </div>

    <!-- 3. Change Employee Password Modal -->
    <div id="password-modal"
        class="modal-content hidden bg-white rounded-3xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto flex-col relative transform scale-95 transition-transform duration-300 text-right dir-dependent-text custom-scrollbar">
        <div class="p-6 border-b border-gray-100 bg-gray-50 flex justify-between items-center sticky top-0 z-10 rounded-t-3xl dir-dependent-flex">
            <button type="button" onclick="closeModal('password-modal')"
                class="text-gray-400 hover:text-red-500 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
            <h2 id="password-modal-title-user" class="text-xl font-black text-[#1c75bc]">تغيير كلمة المرور</h2>
        </div>
        <form id="passwordForm" class="p-8 space-y-6" action="" method="POST">
            @csrf
            <div class="space-y-4">
                <div class="space-y-2">
                    <label class="text-sm font-bold text-gray-700 block mb-1">كلمة المرور الجديدة <span class="text-red-500">*</span></label>
                    <input name="password" type="password" required
                        class="w-full p-4 bg-white border border-gray-200 rounded-xl outline-none focus:border-[#1c75bc] transition-all font-bold text-gray-800"
                        placeholder="*************" />
                </div>
                <div class="space-y-2">
                    <label class="text-sm font-bold text-gray-700 block mb-1">تأكيد كلمة المرور <span class="text-red-500">*</span></label>
                    <input name="password_confirmation" type="password" required
                        class="w-full p-4 bg-white border border-gray-200 rounded-xl outline-none focus:border-[#1c75bc] transition-all font-bold text-gray-800"
                        placeholder="*************" />
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-6 border-t border-gray-100 dir-dependent-flex">
                <button type="submit"
                    class="px-10 py-3.5 bg-[#1c75bc] text-white font-bold rounded-xl shadow-md hover:bg-[#155a91] transition-transform hover:-translate-y-0.5">
                    تحديث كلمة المرور
                </button>
                <button type="button" onclick="closeModal('password-modal')"
                    class="px-8 py-3.5 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 transition-colors">
                    إلغاء
                </button>
            </div>
        </form>
    </div>
</div>