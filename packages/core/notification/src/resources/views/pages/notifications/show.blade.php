@extends('admin::layouts.dashboard')
@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <!--begin::Toolbar-->
    <div class="toolbar my-3" id="kt_toolbar">
        <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
            <div class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
                <h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1">
                    {{ $title }}
                </h1>
                <span class="h-20px border-gray-200 border-start mx-4"></span>
                <ul class="breadcrumb breadcrumb-separatorless fw-bold fs-7 my-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard.index') }}" class="text-muted text-hover-primary">@lang('Home')</a>
                    </li>
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard.notifications.index') }}" class="text-muted text-hover-primary">@lang('notification')</a>
                    </li>
                    <li class="breadcrumb-item text-dark">{{ $title }}</li>
                </ul>
            </div>
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <a href="{{ route('dashboard.notifications.index') }}" class="btn btn-sm btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> @lang('Back')
                </a>
            </div>
        </div>
    </div>
    <!--end::Toolbar-->

    <!--begin::Post-->
    <div class="post d-flex flex-column-fluid" id="kt_post">
        <div id="kt_content_container" class="container-fluid">

            <!--begin::Executive Summary Cards-->
            <div class="row g-4 mb-4">
                <!-- Card 1: Channel & Status -->
                <div class="col-xl-3 col-md-6">
                    <div class="card card-flush shadow-sm h-100">
                        <div class="card-header pt-4">
                            <h3 class="card-title text-gray-800 fw-bold fs-5">
                                <i class="fas fa-satellite-dish text-primary me-2"></i> @lang('مسار وحالة الحملة')
                            </h3>
                        </div>
                        <div class="card-body pt-2">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">@lang('معرف الحملة'):</span>
                                <span class="fw-bolder text-dark">#{{ $item->id }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">@lang('الغرض'):</span>
                                <div>
                                    @if($item->purpose === 'marketing')
                                        <span class="badge bg-label-info badge-light-info">تسويقي (Marketing)</span>
                                    @elseif($item->purpose === 'transactional')
                                        <span class="badge bg-label-primary badge-light-primary">تشغيلي (Transactional)</span>
                                    @elseif($item->purpose === 'system')
                                        <span class="badge bg-label-dark badge-light-dark">نظام (System)</span>
                                    @else
                                        <span class="badge bg-label-secondary badge-light-secondary">{{ $item->purpose ?: '—' }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">@lang('قناة الإرسال'):</span>
                                <div>
                                    @if($isDirect)
                                        <span class="badge bg-label-success badge-light-success fs-7 fw-bold">
                                            <i class="fas fa-bolt text-success me-1"></i> Direct FCM
                                        </span>
                                    @else
                                        <span class="badge bg-label-primary badge-light-primary fs-7 fw-bold">
                                            <i class="fas fa-layer-group text-primary me-1"></i> Legacy Topic
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">@lang('حالة المعالجة'):</span>
                                <div>
                                    @php
                                        $pStatus = $item->processing_status ?: 'completed';
                                        $skippedTokensCount = $item->notificationTokens()->where('status', 'skipped_by_feature_flag')->count();
                                    @endphp
                                    @if($pStatus === 'completed')
                                        @if($skippedTokensCount > 0 && ($acceptedByFcm === 0 || $acceptedByFcm === '—'))
                                            <span class="badge bg-label-warning badge-light-warning text-warning fw-bold">مكتمل — تخطي بـ Flag</span>
                                        @elseif($skippedTokensCount > 0 && $acceptedByFcm > 0)
                                            <span class="badge bg-label-info badge-light-info text-info fw-bold">مكتمل جزئياً — تخطي بـ Flag</span>
                                        @else
                                            <span class="badge bg-label-success badge-light-success">مكتمل</span>
                                        @endif
                                    @elseif($pStatus === 'processing')
                                        <span class="badge bg-label-primary badge-light-primary"><i class="fas fa-spinner fa-spin me-1"></i>جاري</span>
                                    @elseif($pStatus === 'queued')
                                        <span class="badge bg-label-warning badge-light-warning">في الطابور</span>
                                    @else
                                        <span class="badge bg-label-secondary badge-light-secondary">{{ $pStatus }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Audience Metrics -->
                <div class="col-xl-3 col-md-6">
                    <div class="card card-flush shadow-sm h-100">
                        <div class="card-header pt-4">
                            <h3 class="card-title text-gray-800 fw-bold fs-5">
                                <i class="fas fa-users text-info me-2"></i> @lang('الجمهور والأهلية')
                            </h3>
                        </div>
                        <div class="card-body pt-2">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">@lang('الجمهور المستهدف'):</span>
                                <span class="fw-bolder text-dark fs-6">{{ number_format($totalTargeted) }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">@lang('المستخدمون المؤهلون'):</span>
                                <span class="fw-bolder text-success fs-6">{{ number_format($eligibleUsers) }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">@lang('الأجهزة المؤهلة'):</span>
                                <span class="fw-bolder text-info fs-6">{{ number_format($eligibleDevices) }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">@lang('سجلات المستخدمين'):</span>
                                <span class="fw-bolder text-dark">{{ number_format($usersCount) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 3: Delivery Results -->
                <div class="col-xl-3 col-md-6">
                    <div class="card card-flush shadow-sm h-100">
                        <div class="card-header pt-4">
                            <h3 class="card-title text-gray-800 fw-bold fs-5">
                                <i class="fas fa-paper-plane text-success me-2"></i> @lang('نتائج الإرسال')
                            </h3>
                        </div>
                        <div class="card-body pt-2">
                            @if($isDirect)
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">@lang('قُبل من FCM'):</span>
                                    <span class="badge bg-label-success badge-light-success fs-6 fw-bolder">{{ number_format($acceptedByFcm) }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">@lang('فشل مؤقت (Transient)'):</span>
                                    <span class="badge bg-label-warning badge-light-warning fs-6">{{ number_format($transFailed) }}</span>
                                </div>
                                <div class="d-flex justify-content-between {{ ($skippedTokensCount ?? 0) > 0 ? 'mb-2' : '' }}">
                                    <span class="text-muted">@lang('فشل دائم (Permanent)'):</span>
                                    <span class="badge bg-label-danger badge-light-danger fs-6">{{ number_format($permFailed) }}</span>
                                </div>
                                @if(($skippedTokensCount ?? 0) > 0)
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">@lang('تخطي Flag'):</span>
                                    <span class="badge bg-label-secondary badge-light-secondary fs-6 fw-bolder">{{ number_format($skippedTokensCount) }}</span>
                                </div>
                                @endif
                            @else
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Legacy Topic:</span>
                                    <span class="badge bg-label-primary badge-light-primary fs-6 fw-bolder">{{ is_numeric($legacyTopicCount) ? number_format($legacyTopicCount) : $legacyTopicCount }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">@lang('قُبل من FCM'):</span>
                                    <span class="text-muted">—</span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">@lang('الفشل الدقيق'):</span>
                                    <span class="text-muted">غير متاح (قناة عامة)</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Card 4: Engagement -->
                <div class="col-xl-3 col-md-6">
                    <div class="card card-flush shadow-sm h-100">
                        <div class="card-header pt-4">
                            <h3 class="card-title text-gray-800 fw-bold fs-5">
                                <i class="fas fa-chart-line text-warning me-2"></i> @lang('التفاعل والوصول')
                            </h3>
                        </div>
                        <div class="card-body pt-2">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">@lang('المستلم (Received)'):</span>
                                <span class="fw-bolder text-dark">{{ $receivedDisplay }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">@lang('المفتوح (Opened)'):</span>
                                <span class="fw-bolder text-dark">{{ $openedDisplay }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">@lang('معدل الاستلام'):</span>
                                <span class="fw-bolder text-primary">{{ $deliveryRateDisplay }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">@lang('معدل الفتح'):</span>
                                <span class="fw-bolder text-success">{{ $openRateDisplay }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Executive Summary Cards-->

            <!--begin::Action Bar-->
            <div class="card mb-4 shadow-sm">
                <div class="card-body py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div class="d-flex flex-wrap gap-2">
                        @if(auth()->user()?->can('dashboard.notifications.export') || auth()->user()?->can('notifications.export') || auth()->user()?->can('notifications export'))
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-light-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-file-export me-1"></i> @lang('تصدير البيانات (Export CSV)')
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" href="{{ route('dashboard.notifications.exportDetails', [$item->id, 'eligibility']) }}">
                                        <i class="fas fa-users me-2 text-primary"></i> @lang('تصدير أهلية المستخدمين')
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('dashboard.notifications.exportDetails', [$item->id, 'devices']) }}">
                                        <i class="fas fa-mobile-alt me-2 text-info"></i> @lang('تصدير نتائج الأجهزة (مع حجب الرموز)')
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('dashboard.notifications.exportDetails', [$item->id, 'errors']) }}">
                                        <i class="fas fa-exclamation-triangle me-2 text-danger"></i> @lang('تصدير سجل الأخطاء')
                                    </a>
                                </li>
                            </ul>
                        </div>
                        @endif

                        @if($isDirect && $transFailed > 0)
                            <button type="button" class="btn btn-sm btn-light-warning" data-bs-toggle="modal" data-bs-target="#modal-retry-transient">
                                <i class="fas fa-redo me-1"></i> @lang('إعادة محاولة الأخطاء المؤقتة') ({{ number_format($transFailed) }})
                            </button>
                        @endif
                    </div>

                    <div class="text-muted fs-7">
                        <span>@lang('تاريخ الإنشاء'): <strong>{{ $item->created_at?->format('Y-m-d H:i') }}</strong></span>
                        @if($item->started_at)
                            <span class="ms-3">@lang('البدء'): <strong>{{ $item->started_at?->format('Y-m-d H:i') }}</strong></span>
                        @endif
                        @if($item->completed_at)
                            <span class="ms-3">@lang('الاكتمال'): <strong>{{ $item->completed_at?->format('Y-m-d H:i') }}</strong></span>
                        @endif
                    </div>
                </div>
            </div>
            <!--end::Action Bar-->

            <!--begin::Tabs Navigation-->
            <div class="card card-flush shadow-sm">
                <div class="card-header card-header-stretch">
                    <ul class="nav nav-tabs nav-line-tabs nav-stretch fs-6 border-0 fw-bolder" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" data-bs-toggle="tab" href="#tab-summary" role="tab">
                                <i class="fas fa-info-circle me-1"></i> @lang('ملخص الرسالة والمحتوى')
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" data-bs-toggle="tab" href="#tab-eligibility" role="tab" id="link-tab-eligibility">
                                <i class="fas fa-user-check me-1"></i> @lang('أهلية المستخدمين')
                                <span class="badge bg-label-primary badge-light-primary ms-1">{{ number_format($usersCount) }}</span>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" data-bs-toggle="tab" href="#tab-devices" role="tab" id="link-tab-devices">
                                <i class="fas fa-mobile-screen me-1"></i> @lang('نتائج الأجهزة')
                                <span class="badge bg-label-info badge-light-info ms-1">{{ number_format($devicesCount) }}</span>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" data-bs-toggle="tab" href="#tab-engagement" role="tab">
                                <i class="fas fa-chart-pie me-1"></i> @lang('التفاعل والوصول')
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="card-body">
                    <div class="tab-content">
                        <!-- Tab 1: Summary & Content -->
                        <div class="tab-pane fade show active" id="tab-summary" role="tabpanel">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="border rounded p-3 bg-light">
                                        <h5 class="fw-bold text-dark mb-3"><i class="fas fa-envelope-open-text me-1 text-primary"></i> @lang('نص الإشعار')</h5>
                                        <div class="mb-3">
                                            <label class="text-muted fs-7 d-block">@lang('العنوان'):</label>
                                            <div class="fs-6 fw-bold text-dark">{{ $item->title ?: '—' }}</div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="text-muted fs-7 d-block">@lang('المحتوى'):</label>
                                            <div class="fs-6 text-gray-800 bg-white p-3 rounded border">{{ $item->body ?: '—' }}</div>
                                        </div>
                                        @if($item->media)
                                            <div class="mb-2">
                                                <label class="text-muted fs-7 d-block">@lang('الوسائط المرفقة'):</label>
                                                <div class="gallary-images mt-2">
                                                    {!! Core\MediaCenter\Helpers\MediaCenterHelper::getImagesHtml($item->media) !!}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="border rounded p-3 bg-light">
                                        <h5 class="fw-bold text-dark mb-3"><i class="fas fa-sliders-h me-1 text-primary"></i> @lang('معايير الاستهداف والفلاتر')</h5>
                                        <table class="table table-sm table-borderless mb-0">
                                            <tr>
                                                <td class="text-muted w-35">@lang('نوع الإشعار (Types)'):</td>
                                                <td class="fw-bold">{{ $item->types ?: 'apps' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">@lang('المستهدفون (For)'):</td>
                                                <td class="fw-bold">{{ $item->for ?: 'all' }}</td>
                                            </tr>
                                            @if($item->register_from || $item->register_to)
                                                <tr>
                                                    <td class="text-muted">@lang('تاريخ التسجيل'):</td>
                                                    <td class="fw-bold">{{ $item->register_from ?: '...' }} إلى {{ $item->register_to ?: '...' }}</td>
                                                </tr>
                                            @endif
                                            @if($item->orders_from || $item->orders_to || $item->orders_min || $item->orders_max)
                                                <tr>
                                                    <td class="text-muted">@lang('فلاتر الطلبات'):</td>
                                                    <td class="fw-bold">
                                                        العدد ({{ $item->orders_min ?: 0 }} - {{ $item->orders_max ?: '∞' }})
                                                    </td>
                                                </tr>
                                            @endif
                                            @if($item->sender)
                                                <tr>
                                                    <td class="text-muted">@lang('المرسل'):</td>
                                                    <td class="fw-bold">
                                                        <a href="{{ route('dashboard.users.show', $item->sender->id) }}">{{ $item->sender->fullname }}</a>
                                                    </td>
                                                </tr>
                                            @endif
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tab 2: Users Eligibility -->
                        <div class="tab-pane fade" id="tab-eligibility" role="tabpanel">
                            <!-- Filters -->
                            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
                                <div class="d-flex flex-wrap align-items-center gap-2">
                                    <select id="filter-eligibility-status" class="form-select form-select-sm w-200px">
                                        <option value="">@lang('جميع حالات الأهلية')</option>
                                        <option value="eligible">مؤهل (Eligible)</option>
                                        <option value="no_device">لا يوجد جهاز (No Device)</option>
                                        <option value="marketing_disabled">التسويق ملغى (Opted Out)</option>
                                        <option value="permission_denied">صلاحية مرفوضة (Permission Denied)</option>
                                        <option value="no_valid_token">رمز غير صالح (No Valid Token)</option>
                                        <option value="inactive">حساب غير نشط (Inactive)</option>
                                    </select>
                                    <input type="text" id="search-eligibility" class="form-control form-control-sm w-250px" placeholder="@lang('بحث بالاسم أو الهاتف...')">
                                    <button type="button" id="btn-filter-eligibility" class="btn btn-sm btn-primary">
                                        <i class="fas fa-search me-1"></i> @lang('تطبيق')
                                    </button>
                                </div>
                                @if(auth()->user()?->can('dashboard.notifications.export') || auth()->user()?->can('notifications.export') || auth()->user()?->can('notifications export'))
                                <div>
                                    <a href="{{ route('dashboard.notifications.exportDetails', [$item->id, 'eligibility']) }}" class="btn btn-sm btn-light-success">
                                        <i class="fas fa-file-csv me-1"></i> @lang('تصدير CSV')
                                    </a>
                                </div>
                                @endif
                            </div>

                            <!-- Table -->
                            <div class="table-responsive">
                                <table class="table table-striped table-row-bordered align-middle gs-4 gy-3" id="table-eligibility">
                                    <thead class="table-light text-muted fw-bolder fs-7 text-uppercase gs-0">
                                        <tr>
                                            <th>#</th>
                                            <th>@lang('المستخدم')</th>
                                            <th>@lang('رقم الهاتف')</th>
                                            <th>@lang('حالة الأهلية')</th>
                                            <th>@lang('سبب القرار')</th>
                                            <th>@lang('أجهزة مقبولة')</th>
                                            <th>@lang('أجهزة فاشلة')</th>
                                            <th>@lang('تاريخ القراءة')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Loaded via AJAX -->
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Tab 3: Device Results -->
                        <div class="tab-pane fade" id="tab-devices" role="tabpanel">
                            <!-- Filters -->
                            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
                                <div class="d-flex flex-wrap align-items-center gap-2">
                                    <select id="filter-device-status" class="form-select form-select-sm w-180px">
                                        <option value="">@lang('جميع حالات الإرسال')</option>
                                        <option value="accepted">مقبول (Accepted)</option>
                                        <option value="transient_failed">فشل مؤقت (Transient)</option>
                                        <option value="permanent_failed">فشل دائم (Permanent)</option>
                                        <option value="received">مستلم (Received)</option>
                                        <option value="opened">مفتوح (Opened)</option>
                                        <option value="queued">في الطابور (Queued)</option>
                                    </select>
                                    <select id="filter-device-platform" class="form-select form-select-sm w-150px">
                                        <option value="">@lang('جميع المنصات')</option>
                                        <option value="ios">iOS</option>
                                        <option value="android">Android</option>
                                        <option value="huawei">Huawei</option>
                                    </select>
                                    <input type="text" id="search-devices" class="form-control form-control-sm w-220px" placeholder="@lang('بحث بالمستخدم أو الخطأ...')">
                                    <button type="button" id="btn-filter-devices" class="btn btn-sm btn-primary">
                                        <i class="fas fa-search me-1"></i> @lang('تطبيق')
                                    </button>
                                </div>
                                @if(auth()->user()?->can('dashboard.notifications.export') || auth()->user()?->can('notifications.export') || auth()->user()?->can('notifications export'))
                                <div>
                                    <a href="{{ route('dashboard.notifications.exportDetails', [$item->id, 'devices']) }}" class="btn btn-sm btn-light-success">
                                        <i class="fas fa-file-csv me-1"></i> @lang('تصدير الأجهزة (CSV)')
                                    </a>
                                </div>
                                @endif
                            </div>

                            <!-- Table -->
                            <div class="table-responsive">
                                <table class="table table-striped table-row-bordered align-middle gs-4 gy-3" id="table-devices">
                                    <thead class="table-light text-muted fw-bolder fs-7 text-uppercase gs-0">
                                        <tr>
                                            <th>#</th>
                                            <th>@lang('الجهاز')</th>
                                            <th>@lang('المستخدم')</th>
                                            <th>@lang('المنصة')</th>
                                            <th>@lang('السياق')</th>
                                            <th>@lang('الرمز (مقنع)')</th>
                                            <th>@lang('النتيجة')</th>
                                            <th>@lang('رمز الخطأ')</th>
                                            <th>@lang('المحاولات')</th>
                                            <th>@lang('تاريخ القبول / الفشل')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Loaded via AJAX -->
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Tab 4: Engagement -->
                        <div class="tab-pane fade" id="tab-engagement" role="tabpanel">
                            @if(!$isDirect)
                                <div class="alert alert-primary d-flex align-items-center p-4">
                                    <i class="fas fa-info-circle fs-2 text-primary me-3"></i>
                                    <div>
                                        <h5 class="fw-bold mb-1">قياس التفاعل غير متاح لحملات Legacy Topic</h5>
                                        <p class="mb-0 fs-7">
                                            تم إرسال هذا الإشعار عبر قناة الموضوع القديمة (Legacy Topic Subscription). هذه القناة لا تدعم تسجيل أحداث الاستلام والفتح الفردية لكل جهاز، لذلك تُعرض القيم كـ <strong>"غير متاح"</strong> منعاً لأي قياس مضلل.
                                        </p>
                                    </div>
                                </div>
                            @elseif(!config('notification.delivery_events_enabled', true))
                                <div class="alert alert-warning d-flex align-items-center p-4">
                                    <i class="fas fa-exclamation-triangle fs-2 text-warning me-3"></i>
                                    <div>
                                        <h5 class="fw-bold mb-1">تتبع أحداث الوصول (Delivery Events) معطل حالياً</h5>
                                        <p class="mb-0 fs-7">
                                            ميزة تتبع أحداث التطبيق معطلة عبر الـ Feature Flag (<code class="text-danger">DELIVERY_EVENTS_ENABLED=false</code>).
                                        </p>
                                    </div>
                                </div>
                            @else
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <div class="border rounded p-4 text-center bg-light">
                                            <i class="fas fa-inbox fs-1 text-primary mb-2"></i>
                                            <h3 class="fw-bolder fs-2 text-dark">{{ $receivedDisplay }}</h3>
                                            <span class="text-muted fw-bold">الأجهزة التي استلمت الإشعار فعلياً (Delivered)</span>
                                            <div class="mt-2 text-primary fw-bold">نسبة الوصول: {{ $deliveryRateDisplay }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="border rounded p-4 text-center bg-light">
                                            <i class="fas fa-envelope-open-text fs-1 text-success mb-2"></i>
                                            <h3 class="fw-bolder fs-2 text-dark">{{ $openedDisplay }}</h3>
                                            <span class="text-muted fw-bold">الأجهزة التي فتحت الإشعار وتفاعلت معه (Opened)</span>
                                            <div class="mt-2 text-success fw-bold">معدل الفتح: {{ $openRateDisplay }}</div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Tabs Navigation-->

            <!-- Comments Section -->
            <div class="card mt-4 shadow-sm">
                @include('comment::inc.comment-section', ['commentUrl' => route('dashboard.notifications.comment', $item->id)])
            </div>

        </div>
    </div>
    <!--end::Post-->
</div>

<!-- Modal: Retry Transient -->
@if($isDirect)
<div class="modal fade" id="modal-retry-transient" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">
                    <i class="fas fa-sync text-warning me-2"></i> @lang('تأكيد إعادة المحاولة للأخطاء المؤقتة')
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="fs-6 text-gray-800">
                    أنت على وشك جدولة إعادة إرسال الإشعار لـ <strong>{{ number_format($transFailed) }}</strong> جهازاً واجهت أخطاء مؤقتة (Transient Failed).
                </p>
                <div class="alert alert-light-info p-3 mb-0 fs-7">
                    <i class="fas fa-shield-alt text-info me-1"></i>
                    <strong>ضمانات الأمان:</strong>
                    <ul class="mb-0 ps-3 mt-1">
                        <li>لن يتم تكرار الإرسال لأي أجهزة مقبولة مسبقاً (Accepted).</li>
                        <li>لن يتم إعادة محاولة الأجهزة ذات الأخطاء الدائمة (Permanent Failed).</li>
                        <li>تتم المعالجة عبر دفعات مجدولة (Batches) في الخلفية.</li>
                    </ul>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang('إلغاء')</button>
                <button type="button" class="btn btn-warning" id="btn-confirm-retry-transient">
                    <i class="fas fa-redo me-1"></i> @lang('تأكيد وبدء إعادة المحاولة')
                </button>
            </div>
        </div>
    </div>
</div>
@endif

@endsection

@push('js')
<script>
$(document).ready(function() {
    let eligibilityLoaded = false;
    let devicesLoaded = false;

    // Load Eligibility Table
    function loadEligibility(page = 0) {
        const status = $('#filter-eligibility-status').val();
        const search = $('#search-eligibility').val();

        $.ajax({
            url: "{{ route('dashboard.notifications.getEligibilityUsers', $item->id) }}",
            type: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                start: page * 15,
                length: 15,
                filter_status: status,
                search: search
            },
            success: function(res) {
                let html = '';
                if (res.data && res.data.length > 0) {
                    res.data.forEach(function(row) {
                        html += `<tr>
                            <td>#${row.user_id}</td>
                            <td>${row.user_name}</td>
                            <td>${row.user_phone}</td>
                            <td>${row.eligibility_status}</td>
                            <td><span class="text-muted fs-7">${row.eligibility_reason}</span></td>
                            <td><span class="badge bg-label-success badge-light-success">${row.accepted_devices_count}</span></td>
                            <td><span class="badge bg-label-danger badge-light-danger">${row.failed_devices_count}</span></td>
                            <td>${row.read_at}</td>
                        </tr>`;
                    });
                } else {
                    html = '<tr><td colspan="8" class="text-center text-muted py-4">@lang("لا توجد سجلات مطابقة")</td></tr>';
                }
                $('#table-eligibility tbody').html(html);
                eligibilityLoaded = true;
            },
            error: function() {
                $('#table-eligibility tbody').html('<tr><td colspan="8" class="text-center text-danger py-4">@lang("خطأ في تحميل البيانات")</td></tr>');
            }
        });
    }

    // Load Devices Table
    function loadDevices(page = 0) {
        const status = $('#filter-device-status').val();
        const platform = $('#filter-device-platform').val();
        const search = $('#search-devices').val();

        $.ajax({
            url: "{{ route('dashboard.notifications.getDeviceResults', $item->id) }}",
            type: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                start: page * 15,
                length: 15,
                filter_status: status,
                filter_platform: platform,
                search: search
            },
            success: function(res) {
                let html = '';
                if (res.data && res.data.length > 0) {
                    res.data.forEach(function(row) {
                        html += `<tr>
                            <td>#${row.id}</td>
                            <td><span class="fw-bold">${row.device_id}</span></td>
                            <td>${row.user}</td>
                            <td><span class="badge bg-label-dark badge-light-dark">${row.platform}</span></td>
                            <td><span class="text-muted fs-7">${row.app_context}</span></td>
                            <td>${row.masked_token}</td>
                            <td>${row.status}</td>
                            <td>${row.error_code}</td>
                            <td>${row.attempts}</td>
                            <td><span class="fs-7">${row.accepted_at !== '—' ? row.accepted_at : row.failed_at}</span></td>
                        </tr>`;
                    });
                } else {
                    html = '<tr><td colspan="10" class="text-center text-muted py-4">@lang("لا توجد أجهزة مطابقة")</td></tr>';
                }
                $('#table-devices tbody').html(html);
                devicesLoaded = true;
            },
            error: function() {
                $('#table-devices tbody').html('<tr><td colspan="10" class="text-center text-danger py-4">@lang("خطأ في تحميل البيانات")</td></tr>');
            }
        });
    }

    // Trigger loads on tab clicks
    $('#link-tab-eligibility').on('shown.bs.tab', function() {
        if (!eligibilityLoaded) {
            loadEligibility();
        }
    });

    $('#link-tab-devices').on('shown.bs.tab', function() {
        if (!devicesLoaded) {
            loadDevices();
        }
    });

    $('#btn-filter-eligibility').on('click', function() {
        loadEligibility();
    });

    $('#btn-filter-devices').on('click', function() {
        loadDevices();
    });

    // Retry Transient confirmation
    $('#btn-confirm-retry-transient').on('click', function() {
        const $btn = $(this);
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> @lang("جاري الجدولة...")');

        $.ajax({
            url: "{{ route('dashboard.notifications.retryTransient', $item->id) }}",
            type: 'POST',
            data: { _token: "{{ csrf_token() }}" },
            success: function(res) {
                $('#modal-retry-transient').modal('hide');
                alert(res.message || "@lang('تمت جدولة إعادة المحاولة بنجاح.')");
                location.reload();
            },
            error: function(xhr) {
                $btn.prop('disabled', false).html('<i class="fas fa-redo me-1"></i> @lang("تأكيد وبدء إعادة المحاولة")');
                const msg = xhr.responseJSON ? xhr.responseJSON.message : "@lang('حدث خطأ أثناء المحاولة')";
                alert(msg);
            }
        });
    });
});
</script>
@endpush

@push('css')
<style>
    .badge.bg-label-primary, .badge.badge-light-primary { background-color: #e7e7ff !important; color: #696cff !important; font-weight: 600; }
    .badge.bg-label-success, .badge.badge-light-success { background-color: #e8fadf !important; color: #71dd37 !important; font-weight: 600; }
    .badge.bg-label-info, .badge.badge-light-info { background-color: #d7f5fc !important; color: #03c3ec !important; font-weight: 600; }
    .badge.bg-label-warning, .badge.badge-light-warning { background-color: #fff2d6 !important; color: #ffab00 !important; font-weight: 600; }
    .badge.bg-label-danger, .badge.badge-light-danger { background-color: #ffe0db !important; color: #ff3e1d !important; font-weight: 600; }
    .badge.bg-label-secondary, .badge.badge-light-secondary { background-color: #ebeef0 !important; color: #8592a3 !important; font-weight: 600; }
    .badge.bg-label-dark, .badge.badge-light-dark { background-color: #435971 !important; color: #fff !important; font-weight: 600; }
</style>
@endpush
