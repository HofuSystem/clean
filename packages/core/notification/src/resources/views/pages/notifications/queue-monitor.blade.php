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
                    <i class="fas fa-list me-1"></i> @lang('قائمة الإشعارات')
                </a>
            </div>
        </div>
    </div>
    <!--end::Toolbar-->

    <!--begin::Post-->
    <div class="post d-flex flex-column-fluid" id="kt_post">
        <div id="kt_content_container" class="container-fluid">

            <!--begin::Overview Cards-->
            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <div class="card card-flush shadow-sm h-100">
                        <div class="card-body p-4 d-flex align-items-center">
                            <div class="symbol symbol-50px me-3">
                                <span class="symbol-label bg-light-warning">
                                    <i class="fas fa-clock fs-2 text-warning"></i>
                                </span>
                            </div>
                            <div>
                                <span class="text-muted fs-7 d-block">@lang('أقدم دفعة معلقة (Oldest Pending)')</span>
                                <span class="fs-5 fw-bolder text-dark">
                                    {{ $oldestPending ? $oldestPending->diffForHumans() : trans('لا توجد دفعات معلقة') }}
                                </span>
                                @if($oldestPending)
                                    <span class="text-muted fs-8 d-block">{{ $oldestPending->format('Y-m-d H:i') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card card-flush shadow-sm h-100">
                        <div class="card-body p-4 d-flex align-items-center">
                            <div class="symbol symbol-50px me-3">
                                <span class="symbol-label bg-light-danger">
                                    <i class="fas fa-exclamation-triangle fs-2 text-danger"></i>
                                </span>
                            </div>
                            <div>
                                <span class="text-muted fs-7 d-block">@lang('آخر خطأ آمن مسجل (Safe Error)')</span>
                                @if($lastErrorToken)
                                    <span class="fs-6 fw-bold text-danger d-block">
                                        <code>{{ $lastErrorToken->error_code }}</code>
                                    </span>
                                    <span class="text-muted fs-8 text-truncate d-inline-block" style="max-width: 280px;" title="{{ $lastErrorToken->error_message }}">
                                        {{ $lastErrorToken->error_message }}
                                    </span>
                                @else
                                    <span class="fs-6 text-muted">@lang('لا توجد أخطاء مسجلة')</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card card-flush shadow-sm h-100">
                        <div class="card-body p-4 d-flex align-items-center">
                            <div class="symbol symbol-50px me-3">
                                <span class="symbol-label bg-light-info">
                                    <i class="fas fa-shield-alt fs-2 text-info"></i>
                                </span>
                            </div>
                            <div>
                                <span class="text-muted fs-7 d-block">@lang('حالة Direct FCM Kill Switch')</span>
                                @if(config('notification.direct_fcm_enabled', false))
                                    <span class="badge badge-light-success fs-7 fw-bold">Active (Enabled)</span>
                                @else
                                    <span class="badge badge-light-warning fs-7 fw-bold">Standby (Direct FCM Disabled)</span>
                                @endif
                                <span class="text-muted fs-8 d-block mt-1">الوظائف تنهي بأمان عند تعطيل الـ Flag</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Overview Cards-->

            <!--begin::Campaigns Table Card-->
            <div class="card card-flush shadow-sm">
                <div class="card-header pt-4">
                    <h3 class="card-title fw-bold text-dark">
                        <i class="fas fa-tasks text-primary me-2"></i> @lang('مراقبة دفعات حملات الإشعارات')
                    </h3>
                </div>
                <div class="card-body pt-2">
                    <div class="table-responsive">
                        <table class="table table-striped table-row-bordered align-middle gs-4 gy-3">
                            <thead class="table-light text-muted fw-bolder fs-7 text-uppercase gs-0">
                                <tr>
                                    <th>#</th>
                                    <th>@lang('الحملة')</th>
                                    <th>@lang('الغرض')</th>
                                    <th>@lang('الحالة')</th>
                                    <th>@lang('الأجهزة')</th>
                                    <th>@lang('في الطابور')</th>
                                    <th>@lang('جاري')</th>
                                    <th>@lang('مقبول FCM')</th>
                                    <th>@lang('فشل مؤقت')</th>
                                    <th>@lang('فشل دائم')</th>
                                    <th>@lang('تخطي Flag')</th>
                                    <th>@lang('الإجراءات')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($notifications as $notif)
                                    @php
                                        $channel = $notif->getDeliveryChannel();
                                        $isDirect = ($channel === 'direct_fcm');
                                    @endphp
                                    <tr>
                                        <td>#{{ $notif->id }}</td>
                                        <td>
                                            <a href="{{ route('dashboard.notifications.show', $notif->id) }}" class="fw-bold text-dark text-hover-primary">
                                                {{ $notif->title ?: trans('بدون عنوان') }}
                                            </a>
                                            <div class="text-muted fs-8">
                                                {{ $notif->created_at?->format('Y-m-d H:i') }}
                                                @if($isDirect)
                                                    <span class="badge badge-light-success py-0 px-1 ms-1">Direct</span>
                                                @else
                                                    <span class="badge badge-light-primary py-0 px-1 ms-1">Legacy</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            @if($notif->purpose === 'marketing')
                                                <span class="badge badge-light-info">تسويقي</span>
                                            @elseif($notif->purpose === 'transactional')
                                                <span class="badge badge-light-primary">تشغيلي</span>
                                            @elseif($notif->purpose === 'system')
                                                <span class="badge badge-light-dark">نظام</span>
                                            @else
                                                <span class="badge badge-light-secondary">{{ $notif->purpose ?: '—' }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $st = $notif->processing_status ?: 'completed';
                                                $isSkipped = ($notif->skipped_count > 0 && ($notif->accepted_by_fcm_count ?? 0) == 0);
                                                $isPartialSkipped = ($notif->skipped_count > 0 && ($notif->accepted_by_fcm_count ?? 0) > 0);
                                            @endphp
                                            @if($st === 'completed')
                                                @if($isSkipped)
                                                    <span class="badge badge-light-warning text-warning fw-bold" title="Completed — skipped by feature flag">مكتمل — تخطي بـ Flag</span>
                                                @elseif($isPartialSkipped)
                                                    <span class="badge badge-light-info text-info fw-bold">مكتمل جزئياً — تخطي بـ Flag</span>
                                                @else
                                                    <span class="badge badge-light-success">مكتمل</span>
                                                @endif
                                            @elseif($st === 'processing')
                                                <span class="badge badge-light-primary"><i class="fas fa-spinner fa-spin me-1"></i>جاري</span>
                                            @elseif($st === 'queued')
                                                <span class="badge badge-light-warning">في الطابور</span>
                                            @else
                                                <span class="badge badge-light-secondary">{{ $st }}</span>
                                            @endif
                                        </td>
                                        <td><span class="fw-bold">{{ number_format($notif->total_tokens_count) }}</span></td>
                                        <td>
                                            @if($notif->queued_count > 0)
                                                <span class="badge badge-light-warning fw-bold">{{ $notif->queued_count }}</span>
                                            @else
                                                <span class="text-muted">0</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($notif->processing_count > 0)
                                                <span class="badge badge-light-primary fw-bold">{{ $notif->processing_count }}</span>
                                            @else
                                                <span class="text-muted">0</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($isDirect)
                                                <span class="badge badge-light-success">{{ number_format($notif->accepted_by_fcm_count) }}</span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($notif->transient_failed_count > 0)
                                                <span class="badge badge-light-warning">{{ number_format($notif->transient_failed_count) }}</span>
                                            @else
                                                <span class="text-muted">0</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($notif->permanent_failed_count > 0)
                                                <span class="badge badge-light-danger">{{ number_format($notif->permanent_failed_count) }}</span>
                                            @else
                                                <span class="text-muted">0</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($notif->skipped_count > 0)
                                                <span class="badge badge-light-secondary">{{ $notif->skipped_count }}</span>
                                            @else
                                                <span class="text-muted">0</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <a href="{{ route('dashboard.notifications.show', $notif->id) }}" class="btn btn-sm btn-icon btn-light-primary" title="@lang('عرض التفاصيل')">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @if($isDirect && $notif->transient_failed_count > 0)
                                                    <a href="{{ route('dashboard.notifications.show', $notif->id) }}#tab-devices" class="btn btn-sm btn-icon btn-light-warning" title="@lang('إعادة المحاولة')">
                                                        <i class="fas fa-redo"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="12" class="text-center text-muted py-5">
                                            @lang('لا توجد حملات إشعارات مسجلة.')
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-end mt-3">
                        {{ $notifications->links() }}
                    </div>
                </div>
            </div>
            <!--end::Campaigns Table Card-->

        </div>
    </div>
    <!--end::Post-->
</div>
@endsection
