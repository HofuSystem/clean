@extends('admin::layouts.dashboard')
@section('content')
    <!--begin::Content-->
    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        <div class="toolbar my-3" id="kt_toolbar">
            <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
                <div class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
                    <h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1">{{ $title }}</h1>
                    <span class="h-20px border-gray-200 border-start mx-4"></span>
                    <ul class="breadcrumb breadcrumb-separatorless fw-bold fs-7 my-1">
                        <li class="breadcrumb-item text-muted">
                            <a href="" class="text-muted text-hover-primary">@lang('Home')</a>
                        </li>
                        <li class="breadcrumb-item text-muted">@lang('orders')</li>
                        <li class="breadcrumb-item text-dark">{{ $title }}</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="post d-flex flex-column-fluid" id="kt_post">
            <div id="kt_content_container" class="container-fluid">

                <!-- Cart Info Card -->
                <div class="card show-page mb-4">
                    <div class="card">
                        <div class="card-body row">
                            <div class="form-group mb-3 col-md-12">
                                <label>{{ trans("user") }}</label>
                                @isset($item->user)
                                    <div class="alert alert-primary m-1" role="alert">
                                        <a href="{{ route('dashboard.users.show',$item->user->id) }}">{{ $item?->user?->fullname ?? 'N/A' }}</a>
                                    </div>
                                @endisset
                            </div>
                            <div class="form-group mb-3 col-md-12">
                                <label>{{ trans("phone") }}</label>
                                <p>{{ $item->phone ?? $item->user?->phone ?? 'N/A' }}</p>
                            </div>
                            <div class="form-group mb-3 col-md-12">
                                <label>{{ trans("data") }}</label>
                                <p>{{ $item->data ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="card-footer">
                            <a href="{{ route('dashboard.carts.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> {{ trans('Back') }}
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Follow-Up Card -->
                @can('dashboard.cart-follow-ups.store')
                <div class="card mb-4">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h3 class="card-title mb-0">
                            <i class="fas fa-phone-volume me-2 text-primary"></i>
                            @lang('Follow Ups')
                            <span class="badge badge-primary ms-2">{{ $item->followUps()->count() }}</span>
                        </h3>
                        @if(!$item->activeFollowUp()->exists())
                            <button class="btn btn-sm btn-primary" id="addFollowUpBtn">
                                <i class="fas fa-plus"></i> @lang('Add Follow Up')
                            </button>
                        @else
                            <span class="badge badge-warning">@lang('Active follow-up exists')</span>
                        @endif
                    </div>

                    <!-- Add Follow-Up Form (hidden by default) -->
                    <div class="card-body" id="followUpFormWrapper" style="display:none;">
                        <div class="alert alert-info" id="hoursDiffAlert">
                            <i class="fas fa-info-circle"></i>
                            @lang('If the user places an order within') <strong id="hoursDiffValue">...</strong> @lang('hours of this follow-up, it will automatically be marked as a sale.')
                        </div>

                        {{-- User phone display + action buttons --}}
                        @php $userPhone = $item->phone ?? $item->user?->phone; @endphp
                        @if($userPhone)
                        <div class="mb-3 d-flex gap-3 align-items-center flex-wrap">
                            <span class="fw-bold">@lang('User Phone'):</span>
                            <span class="text-muted fs-5">{{ $userPhone }}</span>
                            <a href="tel:{{ $userPhone }}" class="btn btn-sm btn-success">
                                <i class="fas fa-phone"></i> @lang('Call')
                            </a>
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $userPhone) }}" target="_blank" class="btn btn-sm btn-success">
                                <i class="fab fa-whatsapp"></i> @lang('WhatsApp')
                            </a>
                        </div>
                        @endif

                        <form id="addFollowUpForm">
                            @csrf
                            <input type="hidden" name="cart_id" value="{{ $item->id }}">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label>@lang('Phone')</label>
                                    <input type="text" name="phone" class="form-control" value="{{ $userPhone }}" placeholder="@lang('phone')">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>@lang('Notes')</label>
                                    <input type="text" name="notes" class="form-control" placeholder="@lang('notes')">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> @lang('Save Follow Up')
                            </button>
                            <button type="button" class="btn btn-secondary ms-2" id="cancelFollowUpBtn">@lang('Cancel')</button>
                        </form>
                    </div>

                    <!-- Follow-Up History -->
                    <div class="card-body pt-0">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover text-center" id="followUpsTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>@lang('admin')</th>
                                        <th>@lang('phone')</th>
                                        <th>@lang('status')</th>
                                        <th>@lang('notes')</th>
                                        <th>@lang('followed up at')</th>
                                        <th>@lang('order at')</th>
                                        <th>@lang('order ref')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($item->followUps()->with(['admin','order'])->latest()->get() as $fu)
                                    <tr>
                                        <td>{{ $fu->id }}</td>
                                        <td>{{ $fu->admin?->fullname ?? '-' }}</td>
                                        <td>{{ $fu->phone ?? '-' }}</td>
                                        <td>
                                            @if($fu->status === 'sale')
                                                <span class="badge bg-success">@lang('sale')</span>
                                            @elseif($fu->status === 'pending')
                                                <span class="badge bg-warning text-dark">@lang('status_pending')</span>
                                            @elseif($fu->status === 'no_answer')
                                                <span class="badge bg-secondary">@lang('no answer')</span>
                                            @else
                                                <span class="badge bg-info">@lang($fu->status)</span>
                                            @endif
                                        </td>
                                        <td>{{ $fu->notes ?? '-' }}</td>
                                        <td>{{ $fu->followed_up_at?->format('Y-m-d H:i') ?? '-' }}</td>
                                        <td>{{ $fu->order_at?->format('Y-m-d H:i') ?? '-' }}</td>
                                        <td>
                                            @if($fu->order)
                                                <a href="{{ route('dashboard.orders.show', $fu->order->reference_id) }}">
                                                    {{ $fu->order->reference_id }}
                                                </a>
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="8" class="text-muted">@lang('No follow-ups yet')</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endcan

                @include('comment::inc.comment-section',['commentUrl'=>route('dashboard.carts.comment',$item->id)])
            </div>
        </div>
    </div>
@endsection
@push('css')
<link href="{{ asset('control') }}/js/custom/crud/show.css" rel="stylesheet" type="text/css" />
<style>
    .gap-3 { gap: 0.75rem; }
    .badge-primary   { background:#007bff; color:#fff; padding:4px 8px; border-radius:4px; }
    .badge-warning   { background:#ffc107; color:#212529; padding:4px 8px; border-radius:4px; }
</style>
@endpush
@push('js')
<script>
$(document).ready(function () {

    // Toggle follow-up form
    $('#addFollowUpBtn').click(function () {
        $('#followUpFormWrapper').slideToggle();
    });
    $('#cancelFollowUpBtn').click(function () {
        $('#followUpFormWrapper').slideUp();
    });

    // Load hours diff setting
    $.get('{{ route("dashboard.cart-follow-ups.settings") }}', function (res) {
        if (res.status === 'success') {
            $('#hoursDiffValue').text(res.data.hours_diff);
        }
    });

    // Submit follow-up form
    $('#addFollowUpForm').submit(function (e) {
        e.preventDefault();
        let formData = $(this).serialize();
        $.ajax({
            url: '{{ route("dashboard.cart-follow-ups.store") }}',
            type: 'POST',
            data: formData,
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            success: function (res) {
                if (res.status === 'success') {
                    toastr.success(res.message || '@lang("Follow up created")');
                    location.reload();
                } else {
                    toastr.error(res.message || '@lang("Error")');
                }
            },
            error: function (xhr) {
                let msg = xhr.responseJSON?.message || '@lang("system Error please try again later")';
                toastr.error(msg);
            }
        });
    });
});
</script>
@endpush

