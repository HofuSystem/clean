@extends('admin::layouts.dashboard')
@section('content')
    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
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
                        <li class="breadcrumb-item text-muted">@lang('Gifts')</li>
                        <li class="breadcrumb-item text-dark">{{ $title }}</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <div id="kt_content_container" class="container-fluid">
                <div class="card">
                    <form class="form" method="POST" id="operation-form"
                        redirect-to="{{ route('dashboard.gifts.index') }}" data-id="{{ $item->id ?? null }}"
                        @isset($item)
                            action="{{ route('dashboard.gifts.edit', $item->id) }}"
                            data-mode="edit"
                        @else
                            action="{{ route('dashboard.gifts.create') }}"
                            data-mode="new"
                        @endisset>
                        @csrf
                        @isset($item) @method('PUT') @endisset
                        <div class="card-body row">
                            <div class="col-12 mt-5">
                                <ul class="nav nav-tabs" id="languageTabs" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="lang-en-tab" data-bs-toggle="tab"
                                            data-bs-target="#lang-en" type="button" role="tab"
                                            aria-controls="lang-en" aria-selected="true">{{ trans('English') }}</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="lang-ar-tab" data-bs-toggle="tab"
                                            data-bs-target="#lang-ar" type="button" role="tab"
                                            aria-controls="lang-ar" aria-selected="false">{{ trans('العربية') }}</button>
                                    </li>
                                </ul>
                                <div class="tab-content mt-3" id="languageTabsContent">
                                    <div class="tab-pane fade show active" id="lang-en" role="tabpanel">
                                        <div class="form-group mb-3 col-md-12">
                                            <label class="required" for="title_en">{{ trans('title') }} (EN)</label>
                                            <input type="text" name="translations[en][title]" class="form-control"
                                                placeholder="{{ trans('Enter title') }}"
                                                value="{{ $item?->translate('en')?->title }}">
                                        </div>
                                        <div class="form-group mb-3 col-md-12">
                                            <label for="intro_en">{{ trans('intro') }} (EN)</label>
                                            <textarea name="translations[en][intro]" class="form-control"
                                                placeholder="{{ trans('Enter intro') }}">{{ $item?->translate('en')?->intro }}</textarea>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="lang-ar" role="tabpanel">
                                        <div class="form-group mb-3 col-md-12">
                                            <label class="required" for="title_ar">{{ trans('title') }} (AR)</label>
                                            <input type="text" name="translations[ar][title]" class="form-control"
                                                placeholder="{{ trans('Enter title') }}"
                                                value="{{ $item?->translate('ar')?->title }}">
                                        </div>
                                        <div class="form-group mb-3 col-md-12">
                                            <label for="intro_ar">{{ trans('intro') }} (AR)</label>
                                            <textarea name="translations[ar][intro]" class="form-control"
                                                placeholder="{{ trans('Enter intro') }}">{{ $item?->translate('ar')?->intro }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mb-3 col-md-4">
                                <label class="required" for="status">{{ trans('status') }}</label>
                                <select class="form-select advance-select" name="status" id="status">
                                    <option value="active" @selected(($item->status ?? 'active') == 'active')>{{ trans('active') }}</option>
                                    <option value="not-active" @selected(($item->status ?? '') == 'not-active')>{{ trans('not-active') }}</option>
                                </select>
                            </div>

                            <div class="form-group mb-3 col-md-4">
                                <label for="from">{{ trans('from date') }}</label>
                                <input type="date" name="from" class="form-control" value="{{ $item->from ?? '' }}">
                            </div>

                            <div class="form-group mb-3 col-md-4">
                                <label for="to">{{ trans('to date') }}</label>
                                <input type="date" name="to" class="form-control" value="{{ $item->to ?? '' }}">
                            </div>

                            <div class="form-group mb-3 col-md-4">
                                <label for="coupon_code">{{ trans('coupon code') }}</label>
                                <input type="text" name="coupon_code" class="form-control" value="{{ $item->coupon_code ?? '' }}">
                            </div>

                            <div class="form-group mb-3 col-md-4">
                                <label for="order_type">{{ trans('order type') }}</label>
                                <select class="form-select advance-select" name="order_type" id="order_type">
                                    <option value="">{{ trans('Select order type') }}</option>
                                    <option value="clothes" @selected(($item->order_type ?? '') == 'clothes')>{{ trans('clothes') }}</option>
                                    <option value="sales" @selected(($item->order_type ?? '') == 'sales')>{{ trans('sales') }}</option>
                                    <option value="services" @selected(($item->order_type ?? '') == 'services')>{{ trans('services') }}</option>

                                    <option value="maid" @selected(($item->order_type ?? '') == 'maid')>{{ trans('maid') }}</option>
                                    <option value="host" @selected(($item->order_type ?? '') == 'host')>{{ trans('host') }}</option>
                                </select>
                            </div>

                            <div class="form-group mb-3 col-md-4">
                                <label for="register_from">{{ trans('register from') }}</label>
                                <input type="date" name="register_from" class="form-control" value="{{ $item->register_from ?? '' }}">
                            </div>

                            <div class="form-group mb-3 col-md-4">
                                <label for="register_to">{{ trans('register to') }}</label>
                                <input type="date" name="register_to" class="form-control" value="{{ $item->register_to ?? '' }}">
                            </div>

                            <div class="form-group mb-3 col-md-4">
                                <label for="orders_from">{{ trans('orders from') }}</label>
                                <input type="date" name="orders_from" class="form-control" value="{{ $item->orders_from ?? '' }}">
                            </div>

                            <div class="form-group mb-3 col-md-4">
                                <label for="orders_to">{{ trans('orders to') }}</label>
                                <input type="date" name="orders_to" class="form-control" value="{{ $item->orders_to ?? '' }}">
                            </div>

                            <div class="form-group mb-3 col-md-6">
                                <label for="orders_min">{{ trans('orders min') }}</label>
                                <input type="number" name="orders_min" class="form-control" value="{{ $item->orders_min ?? '' }}">
                            </div>

                            <div class="form-group mb-3 col-md-6">
                                <label for="orders_max">{{ trans('orders max') }}</label>
                                <input type="number" name="orders_max" class="form-control" value="{{ $item->orders_max ?? '' }}">
                            </div>

                            <div class="form-group mb-3 col-md-4">
                                <label class="required" for="type">{{ trans('type') }}</label>
                                <select class="form-select advance-select" name="type" id="type">
                                    <option value="value" @selected(($item->type ?? 'value') == 'value')>{{ trans('fixed') }}</option>
                                    <option value="percentage" @selected(($item->type ?? '') == 'percentage')>{{ trans('percentage') }}</option>
                                </select>
                            </div>

                            <div class="form-group mb-3 col-md-4">
                                <label class="required" for="value">{{ trans('value') }}</label>
                                <input type="number" name="value" class="form-control" value="{{ $item->value ?? '' }}">
                            </div>

                            <div class="form-group mb-3 col-md-4" id="max_value_div">
                                <label for="max_value">{{ trans('max value') }}</label>
                                <input type="number" name="max_value" class="form-control" value="{{ $item->max_value ?? '' }}">
                            </div>
                        </div>


                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">{{ trans('save') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('js')
    <script src="{{ asset('control') }}/js/custom/crud/form.js"></script>
    <script>
        $(document).ready(function() {
            $('#type').change(function() {
                if ($(this).val() == 'percentage') {
                    $('#max_value_div').show();
                } else {
                    $('#max_value_div').hide();
                    $('input[name="max_value"]').val('');
                }
            });
            $('#type').change();
        });
    </script>
@endpush

