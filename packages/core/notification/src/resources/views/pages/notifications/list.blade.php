@extends('admin::layouts.dashboard')
@section('content')
    <!--end::Header-->

    <!--begin::Content-->
    <div class="container-fluid flex-grow-1 container-p-y ">

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
                        <a href="{{ route('dashboard.index') }}" class="text-muted text-hover-primary">@lang('Home')</a>
                    </li>
                    <!--end::Item-->

                    <!--begin::Item-->
                    <li class="breadcrumb-item text-muted">@lang("notification")</li>
                    <!--end::Item-->

                    <!--begin::Item-->
                    <li class="breadcrumb-item text-dark">{{ $title }}</li>
                    <!--end::Item-->
                </ul>
                <!--end::Breadcrumb-->
            </div>
            <!--end::Page title-->

        </div>
        <!--begin::Container-->
        <div class="container-fluid">
            <!--begin::Card-->
            <div class="card">
                <!--begin::Card header-->
                <div class="card-header row">
                    <!--begin::Card title-->
                    <div class="card-title col-md-6 ">
                        <!--begin::cols-->
                        <div class="form-group d-flex justify-content-center">
                            <label class="text-dark fw-bold" for="visible_cols"> @lang('visible cols')</label>
                            <select class="form-control mx-3" data-control="select2" name="visible_cols" id="visible_cols"
                                multiple></select>
                        </div>
                        <!--end::cols-->
                    </div>
                    <!--begin::Card title-->
                    <!--begin::Card toolbar-->
                    <div class="card-toolbar col-md-6">
                        <!--begin::Toolbar-->

                        <div data-kt-user-table-toolbar="base">
                            <div class="d-flex justify-content-between">
                                <div class="">
                                    <div class="d-flex">
                                        <!--begin::Stat-->
                                        <div class="stat-pill-total rounded mx-1 p-2 px-3">
                                            <a href="{{ route('dashboard.notifications.index') }}" style="color: inherit; text-decoration: none;">
                                            <div class="fw-bolder fs-5 d-flex align-items-center gap-1">
                                                <i class="fas fa-list-alt"></i>
                                                <span>{{ $total }}</span>
                                                <span class="fs-7 fw-normal">@lang('total')</span>
                                            </div>
                                            </a>
                                        </div>
                                        <!--end::Stat-->
                                        <!--begin::Stat-->
                                        <div class="stat-pill-trash rounded mx-1 p-2 px-3">
                                            <a href="{{ route('dashboard.notifications.index',['trash' => 1]) }}" style="color: inherit; text-decoration: none;">
                                            <div class="fw-bolder fs-5 d-flex align-items-center gap-1">
                                                <i class="fas fa-trash-alt"></i>
                                                <span>{{ $trash }}</span>
                                                <span class="fs-7 fw-normal">@lang('Trash')</span>
                                            </div>
                                            </a>
                                        </div>
                                        <!--end::Stat-->
                                    </div>
                                </div>
                                <div class="d-flex flex-wrap">

                                    @can('dashboard.notifications.export')
                                        <a href="{{ route('dashboard.notifications.export') }}" id="export" type="button"
                                            class="btn-operation ">
                                            <i class="fas fa-upload"></i>
                                            <span>
                                                @lang('Export Report')
                                            </span>
                                        </a>
                                    @endcan
                                    @can('dashboard.notifications.import')
                                        <a href="{{ route('dashboard.notifications.import') }}" class="btn-operation">
                                            <i class="fas fa-file-import"></i>
                                            <span>
                                                @lang('import list')
                                            </span>
                                        </a>
                                    @endcan
                                    <!--begin::Add -->
                                    <a href="{{ route('dashboard.notifications.queueMonitor') }}" class="btn-operation btn-operation-warning" title="مراقبة الطابور">
                                        <i class="fas fa-tasks"></i>
                                        <span>
                                            مراقبة الطابور
                                        </span>
                                    </a>
                                    <a href="{{ route('dashboard.notifications.health') }}" class="btn-operation btn-operation-teal" title="صحة الأجهزة">
                                        <i class="fas fa-heartbeat"></i>
                                        <span>
                                            صحة الأجهزة
                                        </span>
                                    </a>
                                    @can('dashboard.notifications.create')
                                        <a href="{{ route('dashboard.notifications.create') }}" class="btn-operation ">
                                            <i class="fas fa-plus-circle"></i>
                                            <span>
                                                @lang('create new')
                                            </span>
                                        </a>
                                    @endcan
                                </div>

                            </div>
                        </div>
                        <!--end::Toolbar-->
                        <!--begin::Group actions-->
                        <div class="d-flex justify-content-end align-items-center d-none"
                            data-kt-user-table-toolbar="selected">
                            <div class="border border-warning border-dashed rounded text-warning  p-2 mx-1">
                                <span class="me-2" data-kt-user-table-select="selected_count"></span>@lang('Selected')
                            </div>
                            <button type="button" class="btn btn-primary"
                                data-kt-user-table-select="delete_selected">@lang('Delete Selected')</button>
                        </div>
                        <!--end::Group actions-->
                    </div>

                    <!--end::Card toolbar-->
                </div>

                <!--begin::Content-->
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

                                <div class="p-1 row" data-kt-user-table-filter="form">


                                    <div class="col-md-3 mb-1">
                                        <label for="channel">@lang("channel")</label>
                                        <select class="custom-select filter-input form-select advance-select" name="channel"
                                            id="channel">
                                            <option value=""> @lang("select channel")</option>
                                            <option value="app_fcm" @selected("app_fcm" == request("channel"))>App FCM (التطبيقات)</option>
                                            <option value="whatsapp" @selected("whatsapp" == request("channel"))>WhatsApp (واتساب)</option>
                                            <option value="sms" @selected("sms" == request("channel"))>SMS (رسائل نصية)</option>
                                            <option value="email" @selected("email" == request("channel"))>Email (البريد الإلكتروني)</option>
                                            <option value="in_app" @selected("in_app" == request("channel"))>In-App (داخل التطبيق)</option>
                                        </select>
                                    </div>

                                    <div class="col-md-3 mb-1">
                                        <label for="purpose">@lang("purpose")</label>
                                        <select class="custom-select filter-input form-select advance-select" name="purpose"
                                            id="purpose">
                                            <option value=""> @lang("select purpose")</option>
                                            <option value="marketing" @selected("marketing" == request("purpose"))>@lang("marketing")</option>
                                            <option value="transactional" @selected("transactional" == request("purpose"))>@lang("transactional")</option>
                                            <option value="authentication" @selected("authentication" == request("purpose"))>@lang("authentication")</option>
                                            <option value="system" @selected("system" == request("purpose"))>@lang("system")</option>
                                        </select>
                                    </div>

                                    <div class="col-md-3 mb-1">
                                        <label for="types">@lang("types")</label>
                                        <select class="custom-select filter-input form-select advance-select" name="types"
                                            id="types">
                                            <option value=""> @lang("select types")</option>
                                            <option value="apps" @selected("apps" == request("types"))>{{trans("apps")}}</option>
                                            <option value="whats_app" @selected("whats_app" == request("types"))>{{trans("whats_app")}}</option>
                                            <option value="sms" @selected("sms" == request("types"))>{{trans("sms")}}</option>
                                            <option value="email" @selected("email" == request("types"))>{{trans("email")}}</option>
                                        </select>
                                    </div>

                                    <div class="col-md-3 mb-1">
                                        <label for="processing_status">@lang("processing_status")</label>
                                        <select class="custom-select filter-input form-select advance-select" name="processing_status"
                                            id="processing_status">
                                            <option value=""> @lang("select processing_status")</option>
                                            <option value="completed" @selected("completed" == request("processing_status"))>مكتمل</option>
                                            <option value="processing" @selected("processing" == request("processing_status"))>جاري الإرسال</option>
                                            <option value="queued" @selected("queued" == request("processing_status"))>في الطابور</option>
                                            <option value="failed" @selected("failed" == request("processing_status"))>فشل</option>
                                            <option value="draft" @selected("draft" == request("processing_status"))>مسودة</option>
                                        </select>
                                    </div>

                                    <div class="col-md-4 mb-1">
                                        <label for="reference_id"> @lang("reference") </label>
                                        <input type="text" name="reference_id" class="form-control filter-input"
                                            placeholder="@lang("search for reference") " value="{{ request("reference_id") }}">
                                    </div>
                                    <div class="col-md-4 mb-1">
                                        <label for="phone"> @lang("phone") </label>
                                        <input type="text" name="phone" class="form-control filter-input"
                                            placeholder="@lang("search for phone") " value="{{ request("phone") }}">
                                    </div>
                                    <div class="col-md-4 mb-1">
                                        <label for="city_id">@lang("city")</label>
                                        <select class="custom-select filter-input form-select advance-select" name="city_id"
                                            id="city_id">

                                            <option value=""> @lang("select city")</option>
                                            @foreach($cities as $item)
                                                <option value="{{$item->id }}" @selected($item->id == request("city_id"))>
                                                    @lang($item->name)</option>
                                            @endforeach

                                        </select>
                                    </div>

                                    <div class="col-md-4 mb-1">
                                        <label for="title"> @lang("title") </label>
                                        <input type="text" name="title" class="form-control filter-input"
                                            placeholder="@lang("search for title") " value="{{ request("title") }}">
                                    </div>
                                    <div class="col-md-4 mb-1">
                                        <label for="body"> @lang("body") </label>
                                        <input type="text" name="body" class="form-control filter-input"
                                            placeholder="@lang("search for body") " value="{{ request("body") }}">
                                    </div>
                                    <div class="col-md-4 mb-1">
                                        <label for="sender_id">@lang("sender")</label>
                                        <select class="custom-select filter-input form-select advance-select"
                                            name="sender_id" id="sender_id">

                                            <option value=""> @lang("select senders")</option>
                                            @foreach($senders as $item)
                                                <option value="{{$item->id }}" @selected($item->id == request("sender_id"))>
                                                    @lang($item->fullname)</option>
                                            @endforeach

                                        </select>
                                    </div>

                                    <div class="col-md-6 mb-1">
                                        <label for="created_at"> @lang("Create Date from") </label>
                                        <input type="datetime-local" name="from_created_at"
                                            class="form-control filter-input" placeholder="@lang("search for Create Date") "
                                            value="{{ request("created_at") }}">
                                    </div>
                                    <div class="col-md-6 mb-1">
                                        <label for="created_at"> @lang("Create Date to") </label>
                                        <input type="datetime-local" name="to_created_at" class="form-control filter-input"
                                            placeholder="@lang("search for Create Date") "
                                            value="{{ request("created_at") }}">
                                    </div>

                                    <!--begin::Actions-->
                                    <div class=" d-flex justify-content-end">
                                        <button type="reset"
                                            class="btn btn-light btn-active-light-primary fw-bold me-2 px-6"
                                            data-kt-menu-dismiss="true"
                                            data-kt-user-table-filter="reset">@lang('Reset')</button>
                                        <button type="submit" class="btn btn-primary fw-bold px-6"
                                            data-kt-menu-dismiss="true"
                                            data-kt-user-table-filter="filter">@lang('Apply')</button>
                                    </div>
                                    <!--end::Actions-->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!--end::Content-->

                <!--end::Card header-->
                <!--begin::Card body-->
                <div class="card-body pt-0 table-responsive table-responsive">
                    <!--begin::Table-->
                    <table class="table align-middle text-center table-row-dashed fs-6 gy-5" id="view-datatable"
                        data-load="{{ route('dashboard.notifications.index',['trash' => request()->trash]) }}">
                        <!--begin::Table head-->
                        <thead class="table-primary">
                            <!--begin::Table row-->
                            <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">

                                <th class="w-10px pe-2" data-name="select_switch">
                                    <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                        <input class="form-check-input" type="checkbox" data-kt-check="true"
                                            data-kt-check-target="#view-datatable .form-check-input" value="1">
                                    </div>
                                </th>
                                <th class="text-center p-0" data-name="id">@lang("id")</th>


                                <th class="text-center p-0" data-name="channel">القناة</th>
                                <th class="text-center p-0" data-name="for">@lang("for")</th>
                                <th class="text-center p-0" data-name="purpose">الغرض</th>
                                <th class="text-center p-0" data-name="processing_status">الحالة</th>
                                <th class="text-center p-0" data-name="sent_count">حالة الإرسال / النقل</th>
                                <th class="text-center p-0" data-name="title">@lang("title")</th>
                                <th class="text-center p-0" data-name="body">@lang("body")</th>
                                <th class="text-center p-0" data-name="media">@lang("media")</th>
                                <th class="text-center p-0" data-name="sender_id">@lang("sender")</th>
                                <th class="text-center p-0" data-name="created_at">@lang("created_at")</th>
                                <th class="text-center p-0" data-name="actions">@lang("Actions")</th>

                            </tr>
                            <!--end::Table row-->
                        </thead>
                        <!--end::Table head-->
                        <!--begin::Table body-->
                        <tbody class="text-gray-600 fw-bold">

                        </tbody>
                        <!--end::Table body-->
                    </table>
                    <!--end::Table-->
                </div>
                <!--end::Card body-->
            </div>
            <!--end::Card-->
        </div>
        <!--end::Container-->
        <!--end::Post-->
    </div>
    <!--end::Content-->
@endsection
@push('css')
@include('notification::pages.notifications.partials.theme-styles')
@endpush
@push('js')
<script>
    var deleteUrl = "{{ route('dashboard.notifications.delete', ['id'=>'%s','trash'=>request()->trash]) }}";

    $(document).on('click', '.resend-pending-btn', function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        var btn = $(this);
        Swal.fire({
            title: "هل أنت متأكد؟",
            text: "سيتم إعادة إرسال الإشعار لجميع المستخدمين المعلقين.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "نعم، أعد الإرسال!",
            cancelButtonText: "إلغاء"
        }).then(function(result) {
            if (result.value) {
                btn.prop('disabled', true).find('i').addClass('fa-spin');
                $.ajax({
                    url: "{{ url('admin/notifications') }}/" + id + "/resend-pending",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        btn.prop('disabled', false).find('i').removeClass('fa-spin');
                        if (response.status) {
                            Swal.fire("نجاح", response.message, "success");
                            $('#view-datatable').DataTable().ajax.reload();
                        } else {
                            Swal.fire("خطأ", response.message, "error");
                        }
                    },
                    error: function() {
                        btn.prop('disabled', false).find('i').removeClass('fa-spin');
                        Swal.fire("خطأ", "حدث خطأ في النظام، يرجى المحاولة لاحقاً", "error");
                    }
                });
            }
        });
    });
</script>
@endpush