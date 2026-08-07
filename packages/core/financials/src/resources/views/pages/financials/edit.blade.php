@extends('admin::layouts.dashboard')
@section('content')
    <!--begin::Content-->
    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        <!--begin::Toolbar-->
        <div class="toolbar my-3" id="kt_toolbar">
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
                        <li class="breadcrumb-item text-muted">@lang("Financials")</li>
                        <li class="breadcrumb-item text-dark">{{ $title }}</li>
                    </ul>
                </div>
            </div>
        </div>
        <!--end::Toolbar-->
        <!--begin::Post-->
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <div id="kt_content_container" class="container-fluid">
                <!--begin::Card-->
                <div class="card">
                    <form class="form" method="POST" id="operation-form" redirect-to="{{ route("dashboard.financials.index") }}" data-id="{{ $item->id ?? null }}" enctype="multipart/form-data"
                        @isset($item)
                            action="{{ route("dashboard.financials.edit", $item->id) }}"
                            data-mode="edit"
                        @else
                            action="{{ route("dashboard.financials.create") }}"
                            data-mode="new"
                        @endisset
                        >
                        @csrf
                        @isset($item)
                            @method('PUT')
                        @endisset

                        <div class="card-body row">
                            <div class="form-group mb-3 col-md-6">
                                <label for="company_id">{{ trans("Company") }}</label>
                                <select class="custom-select form-select advance-select" name="company_id" id="company_id">
                                    <option value="">{{ trans("select company") }}</option>
                                    @foreach($companies ?? [] as $company)
                                        <option value="{{ $company->id }}" @selected(isset($item) && $item->company_id == $company->id)>{{ $company->fullname }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group mb-3 col-md-6">
                                <label for="user_id">{{ trans("User") }}</label>
                                <select class="custom-select form-select advance-select" name="user_id" id="user_id">
                                    <option value="">{{ trans("select user") }}</option>
                                    @foreach($users ?? [] as $user)
                                        <option value="{{ $user->id }}" @selected(isset($item) && $item->user_id == $user->id)>{{ $user->fullname }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group mb-3 col-md-6">
                                <label class="required" for="type">{{ trans("Type") }}</label>
                                <select class="custom-select form-select advance-select" name="type" id="type" required>
                                    <option value="">{{ trans("select type") }}</option>
                                    <option value="owed" @selected(isset($item) && $item->type == 'owed')>@lang('Add Owed (Credit)')</option>
                                    <option value="paid" @selected(isset($item) && $item->type == 'paid')>@lang('Add Paid (Debit)')</option>
                                </select>
                            </div>

                            <div class="form-group mb-3 col-md-6">
                                <label class="required" for="amount">{{ trans("Amount") }}</label>
                                <input type="number" step="0.01" name="amount" id="amount" class="form-control"
                                    placeholder="{{ trans("Enter amount") }}" value="{{ $item->amount ?? '' }}" required>
                            </div>

                            <div class="form-group mb-3 col-md-6">
                                <label for="reference_id">{{ trans("Reference ID") }}</label>
                                <input type="text" name="reference_id" id="reference_id" class="form-control"
                                    placeholder="{{ trans("Auto-generated if empty") }}" value="{{ $item->reference_id ?? '' }}">
                            </div>

                            <div class="form-group mb-3 col-md-6">
                                <label for="collection_date">{{ trans("Collection Date") }}</label>
                                <input type="date" name="collection_date" id="collection_date" class="form-control"
                                    value="{{ isset($item) && $item->collection_date ? $item->collection_date->format('Y-m-d') : '' }}">
                            </div>

                            <div class="form-group mb-3 col-md-6">
                                <label for="attachment" class="form-label fw-bold">{{ trans("Attachment") }}</label>
                                <input type="file" name="attachment" id="attachment" class="form-control">
                                @if(isset($item) && $item->attachment)
                                    @php
                                        $attachmentUrl = \Illuminate\Support\Str::startsWith($item->attachment, ['http://', 'https://']) 
                                            ? $item->attachment 
                                            : (\Illuminate\Support\Str::startsWith($item->attachment, 'storage/') ? asset($item->attachment) : asset('storage/' . $item->attachment));
                                        $ext = strtolower(pathinfo($item->attachment, PATHINFO_EXTENSION));
                                        $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg']);
                                    @endphp
                                    <div class="mt-3 p-3 border rounded bg-light shadow-sm">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <span class="fw-bold text-muted small"><i class="fas fa-paperclip me-1"></i>{{ trans('Current Attachment') }}</span>
                                            <a href="{{ $attachmentUrl }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-external-link-alt me-1"></i> {{ trans('View / Download Attachment') }}
                                            </a>
                                        </div>
                                        @if($isImage)
                                            <div class="text-center p-2 bg-white rounded border">
                                                <a href="{{ $attachmentUrl }}" target="_blank">
                                                    <img src="{{ $attachmentUrl }}" alt="{{ trans('Attachment') }}" class="img-fluid rounded shadow-sm" style="max-height: 180px; object-fit: contain;">
                                                </a>
                                            </div>
                                        @else
                                            <div class="p-3 bg-white rounded border d-flex align-items-center">
                                                <i class="fas {{ $ext === 'pdf' ? 'fa-file-pdf text-danger' : 'fa-file-alt text-primary' }} fa-2x me-3"></i>
                                                <div class="text-truncate">
                                                    <div class="fw-bold text-dark text-truncate" style="max-width: 250px;">{{ basename($item->attachment) }}</div>
                                                    <small class="text-muted text-uppercase">{{ $ext }} {{ trans('File') }}</small>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <label for="note">{{ trans("Note") }}</label>
                                <textarea name="note" id="note" class="form-control" rows="3"
                                    placeholder="{{ trans("Enter note") }}">{{ $item->note ?? '' }}</textarea>
                            </div>
                        </div>

                        <div class="card-footer">
                            <div class="row">
                                <div class="col-lg-9">
                                    <button type="submit" class="btn btn-primary font-weight-bold mr-2">{{ trans('save') }}</button>
                                    <a href="{{ route('dashboard.financials.index') }}" class="btn btn-light-primary font-weight-bold">@lang('Cancel')</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <!--end::Card-->
            </div>
        </div>
        <!--end::Post-->
    </div>
    <!--end::Content-->
@endsection
@push('css')
    <link href="{{ asset('control') }}/js/custom/crud/form.css" rel="stylesheet" type="text/css" />
@endpush
@push('js')
    <script src="{{ asset('control') }}/js/custom/crud/form.js"></script>
    <script>
        $(document).ready(function() {
            $('#company_id').on('change', function() {
                if ($(this).val()) {
                    $('#user_id').val('').trigger('change.select2');
                }
            });
            $('#user_id').on('change', function() {
                if ($(this).val()) {
                    $('#company_id').val('').trigger('change.select2');
                }
            });
        });
    </script>
@endpush
