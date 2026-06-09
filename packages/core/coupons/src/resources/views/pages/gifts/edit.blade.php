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
                                            <label class="required" for="title_en">{{ trans('gift_title_label') }} (EN)</label>
                                            <input type="text" name="translations[en][title]" class="form-control"
                                                placeholder="{{ trans('Enter title') }}"
                                                value="{{ $item?->translate('en')?->title }}">
                                            <div class="text-muted fs-7">{{ trans('gift_title_desc') }} (EN)</div>
                                        </div>
                                        <div class="form-group mb-3 col-md-12">
                                            <label for="intro_en">{{ trans('gift_intro_label') }} (EN)</label>
                                            <textarea name="translations[en][intro]" class="form-control"
                                                placeholder="{{ trans('Enter intro') }}">{{ $item?->translate('en')?->intro }}</textarea>
                                            <div class="text-muted fs-7">{{ trans('gift_intro_desc') }} (EN)</div>
                                        </div>
                                        <div class="form-group mb-3 col-md-12">
                                            <label for="btn_text_en">{{ trans('gift_btn_text_label') }} (EN)</label>
                                            <input type="text" name="translations[en][btn_text]" class="form-control"
                                                placeholder="{{ trans('Enter button text') }}"
                                                value="{{ $item?->translate('en')?->btn_text }}">
                                            <div class="text-muted fs-7">{{ trans('gift_btn_text_desc') }} (EN)</div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="lang-ar" role="tabpanel">
                                        <div class="form-group mb-3 col-md-12">
                                            <label class="required" for="title_ar">{{ trans('gift_title_label') }} (AR)</label>
                                            <input type="text" name="translations[ar][title]" class="form-control"
                                                placeholder="{{ trans('Enter title') }}"
                                                value="{{ $item?->translate('ar')?->title }}">
                                            <div class="text-muted fs-7">{{ trans('gift_title_desc') }} (AR)</div>
                                        </div>
                                        <div class="form-group mb-3 col-md-12">
                                            <label for="intro_ar">{{ trans('gift_intro_label') }} (AR)</label>
                                            <textarea name="translations[ar][intro]" class="form-control"
                                                placeholder="{{ trans('Enter intro') }}">{{ $item?->translate('ar')?->intro }}</textarea>
                                            <div class="text-muted fs-7">{{ trans('gift_intro_desc') }} (AR)</div>
                                        </div>
                                        <div class="form-group mb-3 col-md-12">
                                            <label for="btn_text_ar">{{ trans('gift_btn_text_label') }} (AR)</label>
                                            <input type="text" name="translations[ar][btn_text]" class="form-control"
                                                placeholder="{{ trans('Enter button text') }}"
                                                value="{{ $item?->translate('ar')?->btn_text }}">
                                            <div class="text-muted fs-7">{{ trans('gift_btn_text_desc') }} (AR)</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mb-3 col-md-4">
                                <label class="required" for="status">{{ trans('gift_status_label') }}</label>
                                <select class="form-select advance-select" name="status" id="status">
                                    <option value="active" @selected(($item->status ?? 'active') == 'active')>{{ trans('active') }}</option>
                                    <option value="not-active" @selected(($item->status ?? '') == 'not-active')>{{ trans('not-active') }}</option>
                                </select>
                                <div class="text-muted fs-7">{{ trans('gift_status_desc') }}</div>
                            </div>

                            <div class="form-group mb-3 col-md-4">
                                <label class="required" for="shown">{{ trans('gift_shown_label') }}</label>
                                <select class="form-select advance-select" name="shown" id="shown">
                                    <option value="1" @selected(($item->shown ?? 1) == 1)>{{ trans('Show') }}</option>
                                    <option value="0" @selected(isset($item) && $item->shown == 0)>{{ trans('Hide') }}</option>
                                </select>
                                <div class="text-muted fs-7">{{ trans('gift_shown_desc') }}</div>
                            </div>

                            <div class="form-group mb-3 col-md-4">
                                <label for="from">{{ trans('gift_from_label') }}</label>
                                <input type="date" name="from" class="form-control" value="{{ $item->from ?? '' }}">
                                <div class="text-muted fs-7">{{ trans('gift_from_desc') }}</div>
                            </div>

                            <div class="form-group mb-3 col-md-4">
                                <label for="to">{{ trans('gift_to_label') }}</label>
                                <input type="date" name="to" class="form-control" value="{{ $item->to ?? '' }}">
                                <div class="text-muted fs-7">{{ trans('gift_to_desc') }}</div>
                            </div>

                         

                            <div class="form-group mb-3 col-md-4">
                                <label for="order_type">{{ trans('gift_order_type_label') }}</label>
                                <select class="form-select advance-select" name="order_type[]" id="order_type" multiple>
                                    @php
                                        $selectedTypes = explode(',', $item->order_type ?? '');
                                    @endphp
                                    <option value="clothes" @selected(in_array('clothes', $selectedTypes))>{{ trans('clothes') }}</option>
                                    <option value="sales" @selected(in_array('sales', $selectedTypes))>{{ trans('sales') }}</option>
                                    <option value="services" @selected(in_array('services', $selectedTypes))>{{ trans('services') }}</option>
                                    <option value="maid" @selected(in_array('maid', $selectedTypes))>{{ trans('maid') }}</option>
                                    <option value="host" @selected(in_array('host', $selectedTypes))>{{ trans('host') }}</option>
                                </select>
                                <div class="text-muted fs-7">{{ trans('gift_order_type_desc') }}</div>
                            </div>

                            <div class="form-group mb-3 col-md-4">
                                <label for="register_from">{{ trans('gift_register_from_label') }}</label>
                                <input type="date" name="register_from" class="form-control" value="{{ $item->register_from ?? '' }}">
                                <div class="text-muted fs-7">{{ trans('gift_register_from_desc') }}</div>
                            </div>

                            <div class="form-group mb-3 col-md-4">
                                <label for="register_to">{{ trans('gift_register_to_label') }}</label>
                                <input type="date" name="register_to" class="form-control" value="{{ $item->register_to ?? '' }}">
                                <div class="text-muted fs-7">{{ trans('gift_register_to_desc') }}</div>
                            </div>

                            <div class="form-group mb-3 col-md-3">
                                <label for="orders_from">{{ trans('gift_orders_from_label') }}</label>
                                <input type="date" name="orders_from" class="form-control" value="{{ $item->orders_from ?? '' }}">
                                <div class="text-muted fs-7">{{ trans('gift_orders_from_desc') }}</div>
                            </div>

                            <div class="form-group mb-3 col-md-3">
                                <label for="orders_to">{{ trans('gift_orders_to_label') }}</label>
                                <input type="date" name="orders_to" class="form-control" value="{{ $item->orders_to ?? '' }}">
                                <div class="text-muted fs-7">{{ trans('gift_orders_to_desc') }}</div>
                            </div>

                            <div class="form-group mb-3 col-md-3">
                                <label for="orders_min">{{ trans('gift_orders_min_label') }}</label>
                                <input type="number" name="orders_min" class="form-control" value="{{ $item->orders_min ?? '' }}">
                                <div class="text-muted fs-7">{{ trans('gift_orders_min_desc') }}</div>
                            </div>

                            <div class="form-group mb-3 col-md-3">
                                <label for="orders_max">{{ trans('gift_orders_max_label') }}</label>
                                <input type="number" name="orders_max" class="form-control" value="{{ $item->orders_max ?? '' }}">
                                <div class="text-muted fs-7">{{ trans('gift_orders_max_desc') }}</div>
                            </div>
                            <div class="form-group mb-3 col-md-4">
                                <label for="coupon_code">{{ trans('gift_coupon_code_label') }}</label>
                                <input type="text" name="coupon_code" class="form-control" value="{{ $item->coupon_code ?? '' }}">
                                <div class="text-muted fs-7">{{ trans('gift_coupon_code_desc') }}</div>
                            </div>
                            <div class="form-group mb-3 col-md-4">
                                <label class="required" for="type">{{ trans('gift_type_label') }}</label>
                                <select class="form-select advance-select" name="type" id="type">
                                    <option value="value" @selected(($item->type ?? 'value') == 'value')>{{ trans('fixed') }}</option>
                                    <option value="percentage" @selected(($item->type ?? '') == 'percentage')>{{ trans('percentage') }}</option>
                                </select>
                                <div class="text-muted fs-7">{{ trans('gift_type_desc') }}</div>
                            </div>

                            <div class="form-group mb-3 col-md-4">
                                <label class="required" for="value">{{ trans('gift_value_label') }}</label>
                                <input type="number" name="value" class="form-control" value="{{ $item->value ?? '' }}">
                                <div class="text-muted fs-7">{{ trans('gift_value_desc') }}</div>
                            </div>

                            <div class="form-group mb-3 col-md-4" id="max_value_div">
                                <label for="max_value">{{ trans('gift_max_value_label') }}</label>
                                <input type="number" name="max_value" class="form-control" value="{{ $item->max_value ?? '' }}">
                                <div class="text-muted fs-7">{{ trans('gift_max_value_desc') }}</div>
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

