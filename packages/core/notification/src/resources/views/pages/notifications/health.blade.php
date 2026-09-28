@extends('admin::layouts.dashboard')
@section('content')
<div class="container-fluid flex-grow-1 container-p-y">
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack mb-5">
        <div class="page-title d-flex align-items-center flex-wrap me-3">
            <h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1">
                <i class="fas fa-heartbeat text-danger me-2"></i> {{ $title }}
            </h1>
            <span class="h-20px border-gray-200 border-start mx-4"></span>
            <ul class="breadcrumb breadcrumb-separatorless fw-bold fs-7 my-1">
                <li class="breadcrumb-item text-muted">
                    <a href="{{ route('dashboard.index') }}" class="text-muted text-hover-primary">@lang('Home')</a>
                </li>
                <li class="breadcrumb-item"><span class="bullet bg-gray-200 w-5px h-2px"></span></li>
                <li class="breadcrumb-item text-muted">
                    <a href="{{ route('dashboard.notifications.index') }}" class="text-muted text-hover-primary">@lang('Notifications')</a>
                </li>
                <li class="breadcrumb-item"><span class="bullet bg-gray-200 w-5px h-2px"></span></li>
                <li class="breadcrumb-item text-dark">{{ $title }}</li>
            </ul>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('dashboard.notifications.index') }}" class="btn btn-sm btn-light">
                <i class="fas fa-arrow-left me-1"></i> العودة للحملات
            </a>
        </div>
    </div>

    <!-- KPI Summary Row -->
    <div class="row g-5 g-xl-8 mb-5">
        <!-- Total Users -->
        <div class="col-xl-3 col-md-6">
            <div class="card bg-body hoverable card-xl-stretch mb-xl-8 border border-gray-200">
                <div class="card-body">
                    <i class="fas fa-users text-primary fs-2x mb-3"></i>
                    <div class="text-gray-900 fw-bolder fs-2 mb-1">{{ number_format($totalUsers) }}</div>
                    <div class="fw-bold text-gray-500">إجمالي العملاء</div>
                    <div class="text-muted fs-8 mt-2">
                        <span class="text-success fw-bold">{{ number_format($activeUsers) }} نشط</span> |
                        <span class="text-danger fw-bold">{{ number_format($inactiveUsers) }} غير نشط</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Users With Devices -->
        <div class="col-xl-3 col-md-6">
            <div class="card bg-body hoverable card-xl-stretch mb-xl-8 border border-gray-200">
                <div class="card-body">
                    <i class="fas fa-mobile-alt text-success fs-2x mb-3"></i>
                    <div class="text-gray-900 fw-bolder fs-2 mb-1">{{ number_format($usersWithDevices) }}</div>
                    <div class="fw-bold text-gray-500">عملاء لديهم أجهزة مسجلة</div>
                    <div class="text-muted fs-8 mt-2">
                        نسبة التغطية: <span class="fw-bold text-success">{{ $totalUsers > 0 ? round(($usersWithDevices / $totalUsers) * 100, 1) : 0 }}%</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Users Without Devices (The Gap) -->
        <div class="col-xl-3 col-md-6">
            <div class="card bg-body hoverable card-xl-stretch mb-xl-8 border border-gray-200">
                <div class="card-body">
                    <i class="fas fa-user-slash text-danger fs-2x mb-3"></i>
                    <div class="text-gray-900 fw-bolder fs-2 mb-1">{{ number_format($usersWithoutDevices) }}</div>
                    <div class="fw-bold text-gray-500">عملاء بلا أي جهاز مسجل</div>
                    <div class="text-muted fs-8 mt-2">
                        <span class="badge badge-light-danger fs-8">غير قابلين للوصول عبر Push</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Devices & Recent Activity -->
        <div class="col-xl-3 col-md-6">
            <div class="card bg-body hoverable card-xl-stretch mb-xl-8 border border-gray-200">
                <div class="card-body">
                    <i class="fas fa-satellite-dish text-info fs-2x mb-3"></i>
                    <div class="text-gray-900 fw-bolder fs-2 mb-1">{{ number_format($totalDevices) }}</div>
                    <div class="fw-bold text-gray-500">إجمالي سجلات الأجهزة</div>
                    <div class="text-muted fs-8 mt-2">
                        <span class="text-info fw-bold">{{ number_format($active30Days) }} نشطة آخر شهر</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 2: Platforms & Permissions -->
    <div class="row g-5 g-xl-8 mb-5">
        <!-- Platforms Breakdown -->
        <div class="col-xl-6">
            <div class="card card-xl-stretch mb-5 mb-xl-8">
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bolder fs-3 mb-1">توزيع المنصات (Platforms)</span>
                        <span class="text-muted mt-1 fw-bold fs-7">حسب نوع نظام التشغيل</span>
                    </h3>
                </div>
                <div class="card-body pt-3">
                    @php
                        $iosCount = $platforms['ios'] ?? 0;
                        $androidCount = $platforms['android'] ?? 0;
                        $huaweiCount = $platforms['huawei'] ?? 0;
                        $devTotal = max(1, $totalDevices);
                    @endphp
                    <div class="d-flex align-items-center mb-4">
                        <div class="symbol symbol-45px me-3"><span class="symbol-label bg-light-primary"><i class="fab fa-apple text-primary fs-3"></i></span></div>
                        <div class="d-flex flex-column flex-grow-1">
                            <span class="text-dark fw-bolder fs-6">Apple iOS</span>
                            <div class="progress h-6px w-100 bg-light-primary mt-1">
                                <div class="progress-bar bg-primary" role="progressbar" style="width: {{ round(($iosCount/$devTotal)*100) }}%"></div>
                            </div>
                        </div>
                        <span class="fw-bold text-dark fs-6 ms-3">{{ number_format($iosCount) }} ({{ round(($iosCount/$devTotal)*100, 1) }}%)</span>
                    </div>

                    <div class="d-flex align-items-center mb-4">
                        <div class="symbol symbol-45px me-3"><span class="symbol-label bg-light-success"><i class="fab fa-android text-success fs-3"></i></span></div>
                        <div class="d-flex flex-column flex-grow-1">
                            <span class="text-dark fw-bolder fs-6">Android</span>
                            <div class="progress h-6px w-100 bg-light-success mt-1">
                                <div class="progress-bar bg-success" role="progressbar" style="width: {{ round(($androidCount/$devTotal)*100) }}%"></div>
                            </div>
                        </div>
                        <span class="fw-bold text-dark fs-6 ms-3">{{ number_format($androidCount) }} ({{ round(($androidCount/$devTotal)*100, 1) }}%)</span>
                    </div>

                    <div class="d-flex align-items-center">
                        <div class="symbol symbol-45px me-3"><span class="symbol-label bg-light-danger"><i class="fas fa-mobile text-danger fs-3"></i></span></div>
                        <div class="d-flex flex-column flex-grow-1">
                            <span class="text-dark fw-bolder fs-6">Huawei</span>
                            <div class="progress h-6px w-100 bg-light-danger mt-1">
                                <div class="progress-bar bg-danger" role="progressbar" style="width: {{ round(($huaweiCount/$devTotal)*100) }}%"></div>
                            </div>
                        </div>
                        <span class="fw-bold text-dark fs-6 ms-3">{{ number_format($huaweiCount) }} ({{ round(($huaweiCount/$devTotal)*100, 1) }}%)</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Permissions Breakdown -->
        <div class="col-xl-6">
            <div class="card card-xl-stretch mb-5 mb-xl-8">
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bolder fs-3 mb-1">صلاحيات الإشعارات في النظام (OS Permission)</span>
                        <span class="text-muted mt-1 fw-bold fs-7">صلاحية Push المسجلة على أجهزة العملاء</span>
                    </h3>
                </div>
                <div class="card-body pt-3">
                    <div class="table-responsive">
                        <table class="table align-middle table-row-dashed fs-6 gy-3">
                            <thead>
                                <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                    <th>حالة الصلاحية</th>
                                    <th>الوصف</th>
                                    <th class="text-end">العدد</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><span class="badge badge-light-success">موافق عليها (Authorized)</span></td>
                                    <td class="text-muted fs-7">العميل منح صلاحية Push للنظام</td>
                                    <td class="text-end fw-bold">{{ number_format($permissions['authorized'] ?? 0) }}</td>
                                </tr>
                                <tr>
                                    <td><span class="badge badge-light-danger">مرفوضة (Denied)</span></td>
                                    <td class="text-muted fs-7">العميل عطّل الإشعارات من إعدادات الجوال</td>
                                    <td class="text-end fw-bold text-danger">{{ number_format($permissions['denied'] ?? 0) }}</td>
                                </tr>
                                <tr>
                                    <td><span class="badge badge-light-warning">غير محدد (Not Determined)</span></td>
                                    <td class="text-muted fs-7">لم يُعرض طلب الصلاحية بعد</td>
                                    <td class="text-end fw-bold">{{ number_format($permissions['not_determined'] ?? 0) }}</td>
                                </tr>
                                <tr>
                                    <td><span class="badge badge-light-secondary">غير معروفة (Legacy / Unknown)</span></td>
                                    <td class="text-muted fs-7">أجهزة قديمة لم ترسل بعد تقرير الصلاحية</td>
                                    <td class="text-end fw-bold">{{ number_format($permissions['unknown'] ?? 0) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 3: Marketing Preferences & Token Recency -->
    <div class="row g-5 g-xl-8 mb-5">
        <!-- Marketing Preference -->
        <div class="col-xl-6">
            <div class="card card-xl-stretch mb-5 mb-xl-8">
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bolder fs-3 mb-1">التفضيل التسويقي (Marketing Preference)</span>
                        <span class="text-muted mt-1 fw-bold fs-7">رغبة العميل داخل التطبيق (is_allow_notify)</span>
                    </h3>
                </div>
                <div class="card-body pt-3">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-check-circle text-success me-2"></i>
                                <span class="fw-bold">موافقة مؤكدة حديثة</span>
                                <small class="text-muted d-block">أكد العميل رغبته في استلام العروض</small>
                            </div>
                            <span class="badge bg-success rounded-pill fs-7">{{ number_format($marketingConfirmedAllowed) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-times-circle text-danger me-2"></i>
                                <span class="fw-bold">رفض صريح مؤكد</span>
                                <small class="text-muted d-block">عطّل العميل الإشعارات التسويقية صراحة</small>
                            </div>
                            <span class="badge bg-danger rounded-pill fs-7">{{ number_format($marketingConfirmedBlocked) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-history text-secondary me-2"></i>
                                <span class="fw-bold">حالة قديمة غير مؤكدة (Legacy Unknown)</span>
                                <small class="text-muted d-block">قيمة افتراضية مسبقة لم يتم تأكيدها صراحة</small>
                            </div>
                            <span class="badge bg-secondary rounded-pill fs-7">{{ number_format($marketingLegacy) }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Token Health & Activity Recency -->
        <div class="col-xl-6">
            <div class="card card-xl-stretch mb-5 mb-xl-8">
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bolder fs-3 mb-1">حداثة نشاط الأجهزة (Last Seen Recency)</span>
                        <span class="text-muted mt-1 fw-bold fs-7">مدى تواجد الأجهزة على السيرفر مؤخراً</span>
                    </h3>
                </div>
                <div class="card-body pt-3">
                    <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded mb-3">
                        <span class="fw-bold"><i class="fas fa-clock text-success me-2"></i> شوهدت خلال آخر 7 أيام</span>
                        <span class="badge badge-light-success fs-7 fw-bold">{{ number_format($active7Days) }}</span>
                    </div>
                    <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded mb-3">
                        <span class="fw-bold"><i class="fas fa-calendar-check text-info me-2"></i> شوهدت خلال آخر 30 يوماً</span>
                        <span class="badge badge-light-info fs-7 fw-bold">{{ number_format($active30Days) }}</span>
                    </div>
                    <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded mb-3">
                        <span class="fw-bold"><i class="fas fa-calendar-alt text-primary me-2"></i> شوهدت خلال آخر 90 يوماً</span>
                        <span class="badge badge-light-primary fs-7 fw-bold">{{ number_format($active90Days) }}</span>
                    </div>
                    <div class="d-flex align-items-center justify-content-between p-3 bg-light-danger rounded">
                        <span class="fw-bold text-danger"><i class="fas fa-exclamation-triangle text-danger me-2"></i> غير نشطة لأكثر من 90 يوماً أو قديمة</span>
                        <span class="badge badge-danger fs-7 fw-bold">{{ number_format($stale90Days) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
