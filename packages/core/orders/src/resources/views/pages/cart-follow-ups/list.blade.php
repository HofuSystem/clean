@extends('admin::layouts.dashboard')
@section('content')
    <!--begin::Content-->
    <div class="container-fluid flex-grow-1 container-p-y">

        <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
            <div data-kt-swapper="true" data-kt-swapper-mode="prepend"
                data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}"
                class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
                <h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1">{{ $title }}</h1>
                <span class="h-20px border-gray-200 border-start mx-4"></span>
                <ul class="breadcrumb breadcrumb-separatorless fw-bold fs-7 my-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard.index') }}" class="text-muted text-hover-primary">@lang('Home')</a>
                    </li>
                    <li class="breadcrumb-item text-muted">@lang('orders')</li>
                    <li class="breadcrumb-item text-dark">{{ $title }}</li>
                </ul>
            </div>
        </div>

        <div class="container-fluid">
            <div class="card">
                <div class="card-header row">
                    <div class="card-title col-md-6">
                        <div class="form-group d-flex justify-content-center">
                            <label class="text-dark fw-bold" for="visible_cols">@lang('visible cols')</label>
                            <select class="form-control mx-3" data-control="select2" name="visible_cols"
                                id="visible_cols" multiple></select>
                        </div>
                    </div>
                    <div class="card-toolbar col-md-6">
                        {{-- Base toolbar --}}
                        <div data-kt-user-table-toolbar="base">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <div class="border border-dashed border-success text-success rounded mx-1 p-2">
                                        <div class="fw-bolder fs-5 text-success">
                                            {{ $total }} <i class="fas fa-list-alt"></i> @lang('total')
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex flex-wrap align-items-center">
                                    <a href="{{ route('dashboard.cart-follow-ups.analysis') }}" class="btn btn-warning me-2 text-white">
                                        <i class="fas fa-chart-bar me-1"></i>
                                        <span>@lang('Follow Ups Analysis')</span>
                                    </a>
                                    <a href="{{ route('dashboard.carts.index') }}" class="btn-operation">
                                        <i class="fas fa-shopping-cart"></i>
                                        <span>@lang('Carts')</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                        {{-- Required by table.js toggleToolbars() — must exist even if empty --}}
                        <div class="d-flex justify-content-end align-items-center d-none"
                            data-kt-user-table-toolbar="selected">
                            <div class="border border-warning border-dashed rounded text-warning p-2 mx-1">
                                <span class="me-2" data-kt-user-table-select="selected_count"></span>@lang('Selected')
                            </div>
                        </div>
                    </div>
                </div>

                <!--begin::Filters-->
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
                                    <div class="col-md-4 mb-1">
                                        <label for="user_id">@lang('user')</label>
                                        <select class="custom-select filter-input form-select advance-select" name="user_id" id="user_id">
                                            <option value="">@lang('select users')</option>
                                            @foreach($users as $item)
                                                <option value="{{ $item->id }}" @selected($item->id == request('user_id'))>{{ $item->fullname }} - {{ $item->phone }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-1">
                                        <label for="admin_id">@lang('admin')</label>
                                        <select class="custom-select filter-input form-select advance-select" name="admin_id" id="admin_id">
                                            <option value="">@lang('select admin')</option>
                                            @foreach($admins as $item)
                                                <option value="{{ $item->id }}" @selected($item->id == request('admin_id'))>{{ $item->fullname }} - {{ $item->phone }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-1">
                                        <label for="status">@lang('status')</label>
                                        <select class="custom-select filter-input form-select" name="status" id="status">
                                            <option value="">@lang('all statuses')</option>
                                            @foreach($statuses as $s)
                                                <option value="{{ $s }}" @selected($s == request('status'))>@lang($s)</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-1">
                                        <label>@lang('From Date')</label>
                                        <input type="datetime-local" name="from_created_at" class="form-control filter-input" value="{{ request('from_created_at') }}">
                                    </div>
                                    <div class="col-md-6 mb-1">
                                        <label>@lang('To Date')</label>
                                        <input type="datetime-local" name="to_created_at" class="form-control filter-input" value="{{ request('to_created_at') }}">
                                    </div>
                                    <div class="d-flex justify-content-end mt-2">
                                        <button type="reset" class="btn btn-light btn-active-light-primary fw-bold me-2 px-6"
                                            data-kt-user-table-filter="reset">@lang('Reset')</button>
                                        <button type="submit" class="btn btn-primary fw-bold px-6"
                                            data-kt-user-table-filter="filter">@lang('Apply')</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end::Filters-->

                <div class="card-body pt-0 table-responsive">
                    <table class="table align-middle text-center table-row-dashed fs-6 gy-5" id="view-datatable"
                        data-load="{{ route('dashboard.cart-follow-ups.index') }}">
                        <thead class="table-primary">
                            <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                <th class="text-center p-0" data-name="id">@lang('id')</th>
                                <th class="text-center p-0" data-name="user_name">@lang('user')</th>
                                <th class="text-center p-0" data-name="user_phone">@lang('phone')</th>
                                <th class="text-center p-0" data-name="admin_name">@lang('admin')</th>
                                <th class="text-center p-0" data-name="status">@lang('status')</th>
                                <th class="text-center p-0" data-name="notes">@lang('notes')</th>
                                <th class="text-center p-0" data-name="followed_up_at">@lang('followed up at')</th>
                                <th class="text-center p-0" data-name="order_at">@lang('order at')</th>
                                <th class="text-center p-0" data-name="order_ref">@lang('order ref')</th>
                                <th class="text-center p-0" data-name="actions">@lang('Actions')</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 fw-bold"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!--end::Content-->

    <!-- Update Follow Up Status Modal -->
    <div class="modal fade" id="updateStatusModal" tabindex="-1" aria-labelledby="updateStatusModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateStatusModalLabel">
                        <i class="fas fa-edit me-2 text-primary"></i>
                        @lang('Update Follow Up Status')
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="updateStatusForm">
                        @csrf
                        <input type="hidden" id="updateFollowUpId">
                        <div class="mb-3">
                            <label class="form-label">@lang('status')</label>
                            <select name="status" id="updateStatusSelect" class="form-select">
                                <option value="pending">@lang('status_pending')</option>
                                <option value="no_answer">@lang('no_answer')</option>
                                <option value="not_interested">@lang('not_interested')</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">@lang('notes')</label>
                            <textarea name="notes" id="updateNotes" class="form-control" rows="3" placeholder="@lang('notes')"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang('Cancel')</button>
                    <button type="button" class="btn btn-primary" id="saveStatusBtn">
                        <i class="fas fa-save me-1"></i> @lang('Save Changes')
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('css')
<style>
    .badge-success  { background:#28a745; color:#fff; padding:4px 8px; border-radius:4px; }
    .badge-secondary{ background:#6c757d; color:#fff; padding:4px 8px; border-radius:4px; }
    .badge-warning  { background:#ffc107; color:#212529; padding:4px 8px; border-radius:4px; }
    .badge-info     { background:#17a2b8; color:#fff; padding:4px 8px; border-radius:4px; }
</style>
@endpush
@push('js')
<script>
$(document).ready(function () {
    // colour status badges after datatable renders
    $(document).on('draw.dt', '#view-datatable', function() {
        $('#view-datatable tbody td').each(function() {
            let val = $(this).text().trim();
            if (val === 'sale')           $(this).html('<span class="badge badge-success">'+val+'</span>');
            else if (val === 'pending')   $(this).html('<span class="badge badge-warning">'+val+'</span>');
            else if (val === 'no_answer') $(this).html('<span class="badge badge-secondary">'+val+'</span>');
            else if (val === 'not_interested') $(this).html('<span class="badge badge-info">'+val+'</span>');
        });
    });

    var updateStatusUrlTemplate = "{{ route('dashboard.cart-follow-ups.update-status', ['id' => '%id%']) }}";

    $(document).on('click', '.edit-status-btn', function () {
        let id = $(this).data('id');
        let status = $(this).data('status');
        let notes = $(this).data('notes') || '';

        $('#updateFollowUpId').val(id);
        $('#updateStatusSelect').val(status);
        $('#updateNotes').val(notes);

        $('#updateStatusModal').modal('show');
    });

    $('#saveStatusBtn').click(function () {
        let id = $('#updateFollowUpId').val();
        let url = updateStatusUrlTemplate.replace('%id%', id);
        let $btn = $(this).prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> ' + @json(__('Saving...')));

        $.ajax({
            url: url,
            type: 'POST',
            data: $('#updateStatusForm').serialize(),
            success: function (res) {
                if (res.status) {
                    toastr.success(res.message || @json(__('Status updated successfully')));
                    $('#updateStatusModal').modal('hide');
                    $('#view-datatable').DataTable().ajax.reload(null, false);
                } else {
                    toastr.error(res.message || @json(__('Error')));
                }
            },
            error: function (xhr) {
                let msg = xhr.responseJSON?.message || @json(__('system Error please try again later'));
                toastr.error(msg);
            },
            complete: function () {
                $btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i> ' + @json(__('Save Changes')));
            }
        });
    });
});
</script>
@endpush
