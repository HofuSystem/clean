@extends('admin::layouts.dashboard')
@section('content')
    <!--end::Header-->
    <!--begin::Content-->
    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        <!--begin::Toolbar-->
        <div class="toolbar my-3" id="kt_toolbar">
            <!--begin::Container-->
            <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
                <!--begin::Page title-->
                <div data-kt-swapper="true" data-kt-swapper-mode="prepend"
                    data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}"
                    class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
                    <!--begin::Title-->
                    <h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1">{{ $title }}</h1>
                    <!--end::Title-->
                    <!--begin::Separator-->
                    <span class="h-20px border-gray-200 border-start mx-4"></span>
                    <!--end::Separator-->
                    <!--begin::Breadcrumb-->
                    <ul class="breadcrumb breadcrumb-separatorless fw-bold fs-7 my-1">
                        <!--begin::Item-->
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('dashboard.index') }}"
                                class="text-muted text-hover-primary">@lang('Home')</a>
                        </li>
                        <!--end::Item-->

                        <!--begin::Item-->
                        <li class="breadcrumb-item text-muted">@lang('notification')</li>
                        <!--end::Item-->

                        <!--begin::Item-->
                        <li class="breadcrumb-item text-dark">{{ $title }}</li>
                        <!--end::Item-->
                    </ul>
                    <!--end::Breadcrumb-->
                </div>
                <!--end::Page title-->

            </div>
            <!--end::Container-->
        </div>
        <!--end::Toolbar-->
        <!--begin::Post-->
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <!--begin::Container-->
            <div id="kt_content_container" class="container-fluid">
                <!--begin::Card-->
                <div class="card">

                    <form class="form" method="POST" id="operation-form"
                        redirect-to="{{ route('dashboard.notifications.index') }}" data-id="{{ $item->id ?? null }}"
                        @isset($item)
                            action="{{ route('dashboard.notifications.edit', $item->id) }}"
                            data-mode="edit"
                        @else
                            action="{{ route('dashboard.notifications.create') }}"
                            data-mode="new"
                        @endisset>

                        @csrf
                        <div class="card-body row">

                            <div class="form-group mb-3 col-md-4">
                                <label class="required" for="types">{{ trans('types') }}</label>
                                <select class="custom-select form-select advance-select" name="types" id="types"
                                    multiple>
                                    <option value="">{{ trans('select types') }}</option>
                                    <option value="apps" @selected(isset($item) and str_contains($item->types, 'apps'))>{{ trans('apps') }}</option>
                                    <option value="email" @selected(isset($item) and str_contains($item->types, 'email'))>{{ trans('email') }}</option>
                                    <option value="sms" @selected(isset($item) and str_contains($item->types, 'sms'))>{{ trans('sms') }}</option>
                                    <option value="whats_app" @selected(isset($item) and str_contains($item->types, 'whats_app'))>{{ trans('whats app') }}</option>
                                </select>
                            </div>

                            <div class="form-group mb-3 col-md-4">
                                <label class="required" for="purpose">{{ trans('الغرض (Purpose)') }}</label>
                                <select class="custom-select form-select advance-select" name="purpose" id="purpose">
                                    <option value="marketing" @selected(!isset($item) || $item->purpose == 'marketing')>{{ trans('تسويقي (Marketing) - للعملاء والموافقين') }}</option>
                                    <option value="transactional" @selected(isset($item) and $item->purpose == 'transactional')>{{ trans('تشغيلي (Transactional) - تحديثات العمليات') }}</option>
                                    <option value="system" @selected(isset($item) and $item->purpose == 'system')>{{ trans('نظام (System) - تنبيهات عامة وإدارية') }}</option>
                                </select>
                            </div>

                            <div class="form-group mb-3 col-md-4">
                                <label class="required" for="for">{{ trans('for') }}</label>
                                <select class="custom-select form-select advance-select" name="for" id="for">
                                    <option value="">{{ trans('select for') }}</option>
                                    <option value="all" @selected(isset($item) and $item->for == 'all')>{{ trans('الكل (جميع المستخدمين)') }}</option>
                                    <option value="users" @selected(isset($item) and $item->for == 'users')>{{ trans('users') }}</option>
                                    <option value="email" @selected(isset($item) and $item->for == 'email')>{{ trans('email') }}</option>
                                    <option value="phone" @selected(isset($item) and $item->for == 'phone')>{{ trans('phone') }}</option>
                                </select>
                            </div>

                            <div class="form-group mb-3 col-md-12" id="selected-users">
                                <label class="" for="selected_users">{{ trans('selected users') }}</label>
                                <select class="custom-select form-select" name="selected_users"
                                    id="selected_users" multiple>
                                    @foreach ($senders ?? [] as $sItem)
                                        <option @selected(isset($item) and in_array($sItem->id, $item->for_data_array))
                                            value="{{ $sItem->id }}"
                                            data-image="{{ $sItem->avatar_url }}">
                                            {{ $sItem->fullname . ' (' . $sItem->phone . ')' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group mb-3 col-md-12" id="for-data-group">
                                <label id="for_data_label" class="" for="for_data">{{ trans('for data') }}</label>
                                <textarea name="for_data" id="for_data" class="form-control" placeholder="{{ trans('Enter for data') }}">@isset($item){{ $item->for_data }}@endisset</textarea>
                            </div>

                            <div id="users-filters" class="row">
                                <div class="form-group mb-3 col-md-6">
                                    <label class="required" for="register_from">{{ trans('register from') }}</label>
                                    <input type="date" name="register_from" class="form-control"
                                        placeholder="{{ trans('Enter register_from') }}"
                                        value="@isset($item){{ $item->register_from }}@endisset">
                                </div>
                                <div class="form-group mb-3 col-md-6">
                                    <label class="required" for="register_to">{{ trans('register to') }}</label>
                                    <input type="date" name="register_to" class="form-control"
                                        placeholder="{{ trans('Enter register_to') }}"
                                        value="@isset($item){{ $item->register_to }}@endisset">
                                </div>
                                <div class="form-group mb-3 col-md-3">
                                    <label class="required" for="orders_from">{{ trans('orders from') }}</label>
                                    <input type="date" name="orders_from" class="form-control"
                                        placeholder="{{ trans('Enter orders_from') }}"
                                        value="@isset($item){{ $item->orders_from }}@endisset">
                                </div>
                                <div class="form-group mb-3 col-md-3">
                                    <label class="required" for="orders_to">{{ trans('orders to') }}</label>
                                    <input type="date" name="orders_to" class="form-control"
                                        placeholder="{{ trans('Enter orders_to') }}"
                                        value="@isset($item){{ $item->orders_to }}@endisset">
                                </div>
                                <div class="form-group mb-3 col-md-3">
                                    <label class="required" for="orders_min">{{ trans('orders min') }}</label>
                                    <input type="text" name="orders_min" class="form-control"
                                        placeholder="{{ trans('Enter orders_min') }}"
                                        value="@isset($item){{ $item->orders_min }}@endisset">
                                </div>
                                <div class="form-group mb-3 col-md-3">
                                    <label class="required" for="orders_max">{{ trans('orders max') }}</label>
                                    <input type="text" name="orders_max" class="form-control"
                                        placeholder="{{ trans('Enter orders_max') }}"
                                        value="@isset($item){{ $item->orders_max }}@endisset">
                                </div>
                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <label class="required" for="title">{{ trans('title') }}</label>
                                <input type="text" name="title" class="form-control"
                                    placeholder="{{ trans('Enter title') }}"
                                    value="@isset($item){{ $item->title }} @else {{ config('app.name') }}@endisset">
                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <label class="required" for="body">{{ trans('body') }}</label>
                                <textarea name="body" class="form-control" placeholder="{{ trans('Enter body') }}">@isset($item){{ $item->body }}@endisset</textarea>
                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <label class="" for="media">{{ trans('media') }}</label>
                                <div class="media-center-group form-control" data-max="5" data-type="gallery">
                                    <input type="text" hidden="hidden" class="form-control" name="media"
                                        value="{{ old('media', $item->media ?? null) }}">
                                    <button type="button" class="btn btn-secondary media-center-load"
                                        style="margin-top: 10px;"><i class="fa fa-file-upload"></i></button>
                                    <div class="input-gallery"></div>
                                </div>
                            </div>

                        </div>
                        <div class="card-footer">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <button type="submit"
                                        class="btn btn-primary font-weight-bold me-2">
                                        <i class="fas fa-paper-plane me-1"></i> {{ trans('send') }}
                                    </button>
                                    <button type="button" id="btn-preview-audience" class="btn btn-light-info font-weight-bold">
                                        <i class="fas fa-users-viewfinder me-1"></i> {{ trans('معاينة الجمهور قبل الإرسال (Preview Audience)') }}
                                    </button>
                                </div>
                                <div>
                                    <a href="{{ route('dashboard.notifications.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left me-1"></i> {{ trans('Back') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>

                </div>
                <!--end::Card-->

                <!--begin::Card for Selected Users preview-->
                <div class="card mt-3">
                    <div class="card-header row">
                        <h3 class="card-title font-weight-bold for">@lang('selected users')</h3>
                    </div>

                    <div class="container-fluid mt-1">
                        <button class="btn btn-primary mb-1" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                            <i class="fas fa-filter"></i>
                            {{ trans('open filters of data') }}
                        </button>

                        <div class="accordion" id="accordionExample">
                            <div class="accordion-item">
                                <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne"
                                    data-bs-parent="#accordionExample">

                                    <div class="p-1 row" data-kt-user-table-filter="form" id="filter-form">

                                        <div class="col-md-4 mb-1">
                                            <label for="filter_fullname"> @lang('full name') </label>
                                            <input type="text" name="filter_fullname" id="filter_fullname" class="form-control filter-input"
                                                placeholder="@lang('search for name') " value="">
                                        </div>

                                        <div class="col-md-4 mb-1">
                                            <label for="filter_phone"> @lang('phone') </label>
                                            <input type="text" name="filter_phone" id="filter_phone" class="form-control filter-input"
                                                placeholder="@lang('search for phone') " value="">
                                        </div>

                                        @isset($item)
                                            <div class="col-md-4 mb-1">
                                                <label for="filter_status"> @lang('status') </label>
                                                <select name="filter_status" id="filter_status" class="form-select filter-input">
                                                    <option value="">@lang('all statuses')</option>
                                                    <option value="sent">@lang('sent')</option>
                                                    <option value="pending">@lang('pending')</option>
                                                    <option value="failed">@lang('failed')</option>
                                                </select>
                                            </div>
                                        @endisset

                                        <div class="col-md-12 d-flex justify-content-end my-1">
                                            <button type="button" class="btn btn-primary me-2" id="filter-apply-btn">
                                                <i class="fas fa-filter"></i> @lang('apply')
                                            </button>
                                            <button type="button" class="btn btn-secondary" id="filter-reset-btn">
                                                <i class="fas fa-undo"></i> @lang('reset')
                                            </button>
                                        </div>

                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table align-middle table-row-dashed fs-6 gy-5"
                                id="view-datatable-notification-users">
                                <thead>
                                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                        <th class="w-10px pe-2" data-name="id">#</th>
                                        <th class="min-w-125px" data-name="fullname">@lang('fullname')</th>
                                        <th class="min-w-125px" data-name="phone">@lang('phone')</th>
                                        <th class="min-w-125px" data-name="orders_count">@lang('orders count')</th>
                                        @isset($item)
                                            <th class="min-w-100px" data-name="pivot_status">@lang('status')</th>
                                            <th class="min-w-100px" data-name="resend_action">@lang('action')</th>
                                        @endisset
                                    </tr>
                                </thead>
                                <tbody class="text-gray-600 fw-semibold">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!--end::Card-->

            </div>
            <!--end::Container-->
        </div>
        <!--end::Post-->
    </div>
    <!--end::Content-->

    <!-- Modal: Audience Preview -->
    <div class="modal fade" id="modal-audience-preview" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">
                        <i class="fas fa-users-viewfinder text-primary me-2"></i> @lang('معاينة الجمهور والأهلية قبل الإرسال (Audience Preview)')
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="preview-loading" class="text-center py-5">
                        <div class="spinner-border text-primary" role="status"></div>
                        <p class="mt-2 text-muted">@lang('جاري حساب الأهلية وفحص الأجهزة دون كتابة أي بيانات...')</p>
                    </div>
                    <div id="preview-content" style="display:none;">
                        <!-- Warning banner -->
                        <div id="preview-warning" class="alert alert-warning d-none mb-4">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <span id="preview-warning-text"></span>
                        </div>

                        <!-- Top KPI Cards -->
                        <div class="row g-3 mb-4">
                            <div class="col-sm-3 col-6">
                                <div class="border rounded p-3 text-center bg-light">
                                    <span class="text-muted fs-7 d-block">@lang('المطابقون للفلاتر')</span>
                                    <h3 id="prev-matching" class="fw-bolder mb-0 text-dark">0</h3>
                                </div>
                            </div>
                            <div class="col-sm-3 col-6">
                                <div class="border rounded p-3 text-center bg-light">
                                    <span class="text-muted fs-7 d-block">@lang('الحسابات النشطة')</span>
                                    <h3 id="prev-active" class="fw-bolder mb-0 text-primary">0</h3>
                                </div>
                            </div>
                            <div class="col-sm-3 col-6">
                                <div class="border rounded p-3 text-center bg-light">
                                    <span class="text-muted fs-7 d-block">@lang('المستخدمون المؤهلون')</span>
                                    <h3 id="prev-eligible-users" class="fw-bolder mb-0 text-success">0</h3>
                                </div>
                            </div>
                            <div class="col-sm-3 col-6">
                                <div class="border rounded p-3 text-center bg-light">
                                    <span class="text-muted fs-7 d-block">@lang('الأجهزة المؤهلة')</span>
                                    <h3 id="prev-eligible-devices" class="fw-bolder mb-0 text-info">0</h3>
                                </div>
                            </div>
                        </div>

                        <!-- Ineligibility Breakdown & Platforms -->
                        <div class="row g-3">
                            <div class="col-md-7">
                                <div class="card card-bordered p-3 h-100">
                                    <h6 class="fw-bold text-dark mb-3"><i class="fas fa-filter text-muted me-1"></i> @lang('تفصيل غير المؤهلين (Ineligibility Breakdown)')</h6>
                                    <table class="table table-sm table-borderless fs-7 mb-0">
                                        <tr>
                                            <td><span class="badge badge-light-warning">No Device</span> لا يوجد جهاز مسجل:</td>
                                            <td class="fw-bolder text-end" id="prev-no-device">0</td>
                                        </tr>
                                        <tr>
                                            <td><span class="badge badge-light-secondary">No Valid Token</span> لا يوجد رمز سارٍ:</td>
                                            <td class="fw-bolder text-end" id="prev-no-token">0</td>
                                        </tr>
                                        <tr>
                                            <td><span class="badge badge-light-danger">Marketing Disabled</span> إلغاء صريح للتسويق:</td>
                                            <td class="fw-bolder text-end" id="prev-marketing-optout">0</td>
                                        </tr>
                                        <tr>
                                            <td><span class="badge badge-light-danger">Permission Denied</span> صلاحية مرفوضة بالتطبيق:</td>
                                            <td class="fw-bolder text-end" id="prev-permission-denied">0</td>
                                        </tr>
                                        <tr>
                                            <td><span class="badge badge-light-info">Legacy Unknown</span> مستخدمون قدامى (غير مؤكد):</td>
                                            <td class="fw-bolder text-end" id="prev-legacy-unknown">0</td>
                                        </tr>
                                        <tr>
                                            <td><span class="badge badge-light-dark">Inactive</span> حسابات غير نشطة:</td>
                                            <td class="fw-bolder text-end" id="prev-inactive">0</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <div class="col-md-5">
                                <div class="card card-bordered p-3 h-100">
                                    <h6 class="fw-bold text-dark mb-3"><i class="fas fa-mobile-alt text-muted me-1"></i> @lang('توزيع أجهزة المؤهلين (Platforms)')</h6>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span><i class="fab fa-apple text-dark me-1"></i> iOS:</span>
                                        <span id="prev-plat-ios" class="fw-bolder badge badge-light-dark">0</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span><i class="fab fa-android text-success me-1"></i> Android:</span>
                                        <span id="prev-plat-android" class="fw-bolder badge badge-light-success">0</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span><i class="fas fa-mobile text-danger me-1"></i> Huawei:</span>
                                        <span id="prev-plat-huawei" class="fw-bolder badge badge-light-danger">0</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top">
                                        <span class="text-muted">نسبة الأجهزة المؤهلة:</span>
                                        <span id="prev-ratio" class="fw-bolder text-primary">0%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang('إغلاق')</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
    <link href="{{ asset('control') }}/js/custom/crud/form.css" rel="stylesheet" type="text/css" />
@endpush

@push('js')
    <script src="{{ asset('control') }}/js/custom/crud/form.js"></script>
    <script>
        function formatUser(user) {
            if (user.loading) {
                return user.text;
            }
            var markup = `
                <div class="d-flex align-items-center">
                    <img src="${user.image}" class="rounded-circle me-2" style="width: 30px; height: 30px;" />
                    <div>
                        <div class="fw-bold">${user.text}</div>
                        <div class="text-muted fs-7">${user.phone}</div>
                    </div>
                </div>`;
            return markup;
        }

        function formatUserSelection(user) {
            return user.text || user.id;
        }

        $('#selected_users').select2({
            ajax: {
                url: "{{ route('dashboard.users.search') }}",
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term
                    };
                },
                processResults: function(data) {
                    return {
                        results: data.results
                    };
                },
                cache: true
            },
            templateResult: formatUser,
            templateSelection: formatUserSelection,
            escapeMarkup: function(markup) { return markup; },
            minimumInputLength: 2,
            language: {
                inputTooShort: function() {
                    return "يرجى كتابة حرفين أو رقمين للبحث عن مستخدم بالاسم أو الجوال...";
                },
                noResults: function() {
                    return "لم يتم العثور على أي مستخدم";
                },
                searching: function() {
                    return "جاري البحث في قاعدة البيانات...";
                }
            },
            width: '100%',
            placeholder: "{{ trans('select users') }}"
        });

        function updateForFields() {
            let forVal = $('#for').val();
            if (forVal === 'users') {
                $('#selected-users').slideDown();
                $('#for-data-group').hide();
            } else if (forVal === 'email') {
                $('#selected-users').hide();
                $('#for-data-group').slideDown();
                $('#for_data_label').text("عناوين البريد الإلكتروني (مفصولة بفواصل ,)");
                $('#for_data').attr('placeholder', "مثال: a@example.com, b@example.com");
            } else if (forVal === 'phone') {
                $('#selected-users').hide();
                $('#for-data-group').slideDown();
                $('#for_data_label').text("أرقام الجوال (مفصولة بفواصل ,)");
                $('#for_data').attr('placeholder', "مثال: 0501234567, 0551234567");
            } else {
                // 'all' or empty
                $('#selected-users').slideUp();
                $('#for-data-group').slideUp();
            }
        }

        $('#for').on('change', function() {
            updateForFields();
        });
        updateForFields();

        $('#selected_users').change(function(e) {
            e.preventDefault();
            let value = $(this).val();
            $('[name=for_data]').val(JSON.stringify(value));
        });

        var cols = [];
        let url =
            @if (isset($item))
                '{{ route('dashboard.notifications.getSentToUsers', $item->id) }}'
            @else
                '{{ route('dashboard.notifications.getusers') }}'
            @endif ;
        let formData = getFormData($('#operation-form'));
        cols = [];
        $('table#view-datatable-notification-users thead th').each(function(index, element) {
            let data = $(this).data('name');
            let name = (index == 0) ? data : $(this).text();
            let col = {
                'name': name,
                'data': data
            };
            if ($(this).attr('orderable')) {
                col.orderable = ($(this).attr('orderable') == true);
            }
            cols.push(col);
        });
        let DataTable = $('#view-datatable-notification-users').DataTable({
            language: {
                "sProcessing": "جاري التحميل...",
                "sLengthMenu": "أظهر _MENU_ مدخلات",
                "sZeroRecords": "لم يُعثر على أية سجلات",
                "sInfo": "إظهار _START_ إلى _END_ من أصل _TOTAL_ مدخل",
                "sInfoEmpty": "يعرض 0 إلى 0 من أصل 0 سجل",
                "sInfoFiltered": "(منتقاة من مجموع _MAX_ مُدخل)",
                "sInfoPostFix": "",
                "sSearch": "ابحث:",
                "sUrl": "",
                "oPaginate": {
                    "sFirst": "الأول",
                    "sPrevious": "السابق",
                    "sNext": "التالي",
                    "sLast": "الأخير"
                }
            },
            dom: "<'d-flex justify-content-between align-items-center mb-2'<'dt-buttons'B><'dt-length'l>>" +
                "frtip",
            lengthMenu: [
                [25, 50, 100, 300, 500, -1],
                [25, 50, 100, 300, 500, trans('all')]
            ],
            pageLength: 25,
            buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
            orderable: false,
            searchDelay: 500,
            searching: false,
            processing: true,
            serverSide: true,
            stateSave: true,
            ajax: {
                url: url,
                type: 'POST',
                data: function(data) {
                    $.extend(data, formData);
                    return data;
                },
                error: function(xhr, error, thrown) {
                    console.log(xhr, error, thrown);
                }
            },
            columns: cols,
        });

        // Handle filter Apply button
        $('#filter-apply-btn').on('click', function(e) {
            e.preventDefault();
            let filterData = {
                filter_fullname: $('#filter_fullname').val(),
                filter_phone: $('#filter_phone').val(),
                filter_status: $('#filter_status').val()
            };
            $.extend(formData, filterData);
            DataTable.draw();
        });

        // Handle filter Reset button
        $('#filter-reset-btn').on('click', function(e) {
            e.preventDefault();
            $('#filter_fullname').val('');
            $('#filter_phone').val('');
            $('#filter_status').val('');
            delete formData.filter_fullname;
            delete formData.filter_phone;
            delete formData.filter_status;
            DataTable.draw();
        });

        @if (!isset($item))
            $('#for, #users-filters input, #selected_users, #purpose').change(function(e) {
                e.preventDefault();
                formData = getFormData($('#operation-form'));
                DataTable.draw();
            });
        @endisset

        // Audience Preview AJAX handler
        $('#btn-preview-audience').on('click', function() {
            const $modal = $('#modal-audience-preview');
            $('#preview-loading').show();
            $('#preview-content').hide();
            $('#preview-warning').addClass('d-none');
            $modal.modal('show');

            const postData = $('#operation-form').serialize();

            $.ajax({
                url: "{{ route('dashboard.notifications.previewAudience') }}",
                type: 'POST',
                data: postData,
                success: function(res) {
                    $('#preview-loading').hide();
                    $('#preview-content').show();
                    if (res.data) {
                        const d = res.data;
                        $('#prev-matching').text(Number(d.total_matching_users).toLocaleString());
                        $('#prev-active').text(Number(d.active_users).toLocaleString());
                        $('#prev-eligible-users').text(Number(d.eligible_users).toLocaleString());
                        $('#prev-eligible-devices').text(Number(d.eligible_devices).toLocaleString());

                        if (d.breakdown) {
                            $('#prev-no-device').text(Number(d.breakdown.no_device).toLocaleString());
                            $('#prev-no-token').text(Number(d.breakdown.no_valid_token).toLocaleString());
                            $('#prev-marketing-optout').text(Number(d.breakdown.marketing_disabled).toLocaleString());
                            $('#prev-permission-denied').text(Number(d.breakdown.permission_denied).toLocaleString());
                            $('#prev-legacy-unknown').text(Number(d.breakdown.legacy_unknown).toLocaleString());
                            $('#prev-inactive').text(Number(d.breakdown.inactive).toLocaleString());
                        }

                        if (d.platforms) {
                            $('#prev-plat-ios').text(Number(d.platforms.ios || 0).toLocaleString());
                            $('#prev-plat-android').text(Number(d.platforms.android || 0).toLocaleString());
                            $('#prev-plat-huawei').text(Number(d.platforms.huawei || 0).toLocaleString());
                        }

                        $('#prev-ratio').text((d.device_ratio || 0) + '%');

                        if (d.warning) {
                            $('#preview-warning-text').text(d.warning);
                            $('#preview-warning').removeClass('d-none');
                        }
                    }
                },
                error: function(xhr) {
                    $('#preview-loading').hide();
                    $('#preview-content').show();
                    const err = xhr.responseJSON ? xhr.responseJSON.message : "@lang('تعذر حساب المعاينة')";
                    $('#preview-warning-text').text(err);
                    $('#preview-warning').removeClass('d-none');
                }
            });
        });

        $(document).on('click', '.resend-user-btn', function(e) {
            e.preventDefault();
            var btn = $(this);
            if (btn.hasClass('disabled')) return;
            var userId = btn.data('id');
            var notificationId = btn.data('notification-id');

            Swal.fire({
                title: "هل أنت متأكد؟",
                text: "سيتم إعادة إرسال الإشعار لهذا المستخدم بالتحديد.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "نعم، أعد الإرسال!",
                cancelButtonText: "إلغاء"
            }).then(function(result) {
                if (result.value) {
                    btn.addClass('disabled').find('i').addClass('fa-spin');
                    $.ajax({
                        url: "{{ url('admin/notifications') }}/" + notificationId + "/resend-user/" + userId,
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            btn.removeClass('disabled').find('i').removeClass('fa-spin');
                            if (response.status) {
                                Swal.fire("نجاح", response.message, "success");
                                DataTable.ajax.reload();
                            } else {
                                Swal.fire("خطأ", response.message, "error");
                            }
                        },
                        error: function() {
                            btn.removeClass('disabled').find('i').removeClass('fa-spin');
                            Swal.fire("خطأ", "حدث خطأ في النظام، يرجى المحاولة لاحقاً", "error");
                        }
                    });
                }
            });
        });
    </script>
@endpush
@push('css')
@include('notification::pages.notifications.partials.theme-styles')
@endpush
