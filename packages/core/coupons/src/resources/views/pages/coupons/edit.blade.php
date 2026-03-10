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
                        <li class="breadcrumb-item text-muted">@lang('coupons')</li>
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
                        redirect-to="{{ route('dashboard.coupons.index') }}" data-id="{{ $item->id ?? null }}"
                        @isset($item)
                            action="{{ route('dashboard.coupons.edit', $item->id) }}"
                            data-mode="edit"
                        @else
                            action="{{ route('dashboard.coupons.create') }}"
                            data-mode="new"
                        @endisset>

                        @csrf
                        <div class="card-body row">

                            <div class="col-12 mt-5">
                                <ul class="nav nav-tabs" id="languageTabs" role="tablist">

                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active " id="title-en-tab" data-bs-toggle="tab"
                                            data-bs-target="#title-en" type="button" role="tab"
                                            aria-controls="title-en" aria-selected=" true">{{ trans('English') }}</button>
                                    </li>

                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link  " id="title-ar-tab" data-bs-toggle="tab"
                                            data-bs-target="#title-ar" type="button" role="tab"
                                            aria-controls="title-ar" aria-selected=" false">{{ trans('العربية') }}</button>
                                    </li>

                                </ul>
                                <div class="tab-content mt-3" id="languageTabsContent">
                                    <div class="tab-pane fade show active" id="title-en" role="tabpanel"
                                        aria-labelledby="en-tab">

                                        <div class="form-group mb-3 col-md-12">
                                            <label class="required" for="title">{{ trans('title') }}</label>
                                            <input type="text" name="translations[en][title]" class="form-control "
                                                placeholder="{{ trans('Enter title') }} "
                                                value="@isset($item) {{ $item?->translate('en')?->title }} @endisset">

                                        </div>

                                    </div>
                                    <div class="tab-pane fade " id="title-ar" role="tabpanel" aria-labelledby="ar-tab">

                                        <div class="form-group mb-3 col-md-12">
                                            <label class="required" for="title">{{ trans('title') }}</label>
                                            <input type="text" name="translations[ar][title]" class="form-control "
                                                placeholder="{{ trans('Enter title') }} "
                                                value="@isset($item) {{ $item?->translate('ar')?->title }} @endisset">

                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="form-group mb-3 col-md-4">
                                <label class="required" for="status">{{ trans('status') }}</label>
                                <select class="custom-select  form-select advance-select" name="status" id="status">

                                    <option value="">{{ trans('select status') }}</option>
                                    <option value="active" @selected(isset($item) and $item->status == 'active')>{{ trans('active') }}</option>
                                    <option value="not-active" @selected(isset($item) and $item->status == 'not-active')>{{ trans('not-active') }}
                                    </option>

                                </select>

                            </div>

                            <div class="form-group mb-3 col-md-4">
                                <label class="required" for="applying">{{ trans('applying') }}</label>
                                <select class="custom-select  form-select advance-select" name="applying" id="applying">

                                    <option value="">{{ trans('select applying') }}</option>
                                    <option value="auto" @selected(isset($item) and $item->applying == 'auto')>{{ trans('auto') }}</option>
                                    <option value="manual" @selected(isset($item) and $item->applying == 'manual')>{{ trans('manual') }}</option>

                                </select>

                            </div>

                            <div class="form-group mb-3 col-md-4">
                                <label class="" for="code">{{ trans('code') }}</label>
                                <input type="text" name="code" class="form-control " id="code"
                                    placeholder="{{ trans('Enter code') }} "
                                    value="@isset($item){{ $item->code }}@endisset">

                            </div>

                            <div class="form-group mb-3 col-md-6">
                                <label class="" for="max_use">{{ trans('max use') }}</label>
                                <input type="number" name="max_use" class="form-control "
                                    placeholder="{{ trans('Enter max use') }} "
                                    value="{{ old('max_use', $item->max_use ?? null) }}">

                            </div>

                            <div class="form-group mb-3 col-md-6">
                                <label class="" for="max_use_per_user">{{ trans('max use per user') }}</label>
                                <input type="number" name="max_use_per_user" class="form-control "
                                    placeholder="{{ trans('Enter max use per user') }} "
                                    value="{{ old('max_use_per_user', $item->max_use_per_user ?? null) }}">

                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <label class="" for="payment_method">{{ trans('payment method') }}</label>
                                <select class="custom-select  form-select advance-select" name="payment_method"
                                    id="payment_method">

                                    <option value="">{{ trans('select payment method') }}</option>
                                    <option value="cash" @selected(isset($item) and $item->payment_method == 'cash')>{{ trans('cash') }}</option>
                                    <option value="card" @selected(isset($item) and $item->payment_method == 'card')>{{ trans('card') }}</option>

                                </select>

                            </div>

                            <div class="form-group mb-3 col-md-6">
                                <label class="" for="start_at">{{ trans('start at') }}</label>
                                <input type="date" name="start_at" class="form-control "
                                    placeholder="{{ trans('Enter start at') }} "
                                    value="@isset($item){{ $item->start_at }}@endisset">

                            </div>

                            <div class="form-group mb-3 col-md-6">
                                <label class="" for="end_at">{{ trans('end at') }}</label>
                                <input type="date" name="end_at" class="form-control "
                                    placeholder="{{ trans('Enter end at') }} "
                                    value="@isset($item){{ $item->end_at }}@endisset">

                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <label class="" for="order_type">{{ trans('order Type') }}</label>
                                <select class="custom-select  form-select advance-select" name="order_type"
                                    id="order_type">

                                    <option value="">{{ trans('select order Type') }}</option>
                                    <option value="clothes" @selected(isset($item) and $item->order_type == 'clothes')>{{ trans('clothes') }}</option>
                                    <option value="sales" @selected(isset($item) and $item->order_type == 'sales')>{{ trans('sales') }}</option>
                                    <option value="services" @selected(isset($item) and $item->order_type == 'services')>{{ trans('services') }}</option>
                                    <option value="maid" @selected(isset($item) and $item->order_type == 'maid')>{{ trans('maid') }}</option>
                                    <option value="host" @selected(isset($item) and $item->order_type == 'host')>{{ trans('host') }}</option>

                                </select>

                            </div>
                            <div id="product-div" class="row">

                                <div class="form-group mb-3 col-md-4">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="all_products"
                                            name="all_products" @checked(isset($item) and $item->all_products)>
                                        <label class="form-check-label"
                                            for="all_products">{{ trans('all products') }}</label>
                                    </div>

                                </div>

                                <div class="form-group mb-3 col-md-4">
                                    <label class="" for="products">{{ trans('products') }}</label>
                                    <select class="custom-select  form-select advance-select" name="products"
                                        id="products" multiple>

                                        <option value="">{{ trans('select products') }}</option>
                                        @foreach ($products ?? [] as $sItem)
                                            <option data-id="{{ $sItem->id }}" @selected(isset($item) and $item->products->where('id', $sItem->id)->first())
                                                value="{{ $sItem->id }}">{{ $sItem->name }}</option>
                                        @endforeach

                                    </select>

                                </div>

                                <div class="form-group mb-3 col-md-4">
                                    <label class="" for="categories">{{ trans('categories') }}</label>
                                    <select class="custom-select  form-select advance-select" name="categories"
                                        id="categories" multiple>

                                        <option value="">{{ trans('select categories') }}</option>
                                        @foreach ($categories ?? [] as $sItem)
                                            <option data-id="{{ $sItem->id }}" @selected(isset($item) and $item->categories->where('id', $sItem->id)->first())
                                                value="{{ $sItem->id }}">{{ $sItem->name }}</option>
                                        @endforeach

                                    </select>

                                </div>
                            </div>
                            <div class="row">

                                <div class="form-group mb-3 col-md-4">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="all_users" name="all_users"
                                            @checked(isset($item) and $item->all_users)>
                                        <label class="form-check-label" for="all_users">{{ trans('all users') }}</label>
                                    </div>

                                </div>

                                <div class="form-group mb-3 col-md-4">
                                    <label class="" for="users">{{ trans('users') }}</label>
                                    <select class="custom-select  form-select advance-select" name="users"
                                        id="users" multiple>

                                        <option value="">{{ trans('select users') }}</option>
                                        @foreach ($users ?? [] as $sItem)
                                            <option data-id="{{ $sItem->id }}" @selected(isset($item) and $item->users->where('id', $sItem->id)->first())
                                                value="{{ $sItem->id }}">{{ $sItem->fullname }}</option>
                                        @endforeach

                                    </select>

                                </div>

                                <div class="form-group mb-3 col-md-4">
                                    <label class="" for="roles">{{ trans('roles') }}</label>
                                    <select class="custom-select  form-select advance-select" name="roles"
                                        id="roles" multiple>

                                        <option value="">{{ trans('select roles') }}</option>
                                        @foreach ($roles ?? [] as $sItem)
                                            <option data-id="{{ $sItem->id }}" @selected(isset($item) and $item->roles->where('id', $sItem->id)->first())
                                                value="{{ $sItem->id }}">{{ $sItem->name }}</option>
                                        @endforeach

                                    </select>

                                </div>
                            </div>

                            <div class="form-group mb-3 col-md-6">
                                <label class="" for="order_minimum">{{ trans('order minimum') }}</label>
                                <input type="number" name="order_minimum" class="form-control "
                                    placeholder="{{ trans('Enter order minimum') }} "
                                    value="{{ old('order_minimum', $item->order_minimum ?? null) }}">

                            </div>

                            <div class="form-group mb-3 col-md-6">
                                <label class="" for="order_maximum">{{ trans('order maximum') }}</label>
                                <input type="number" name="order_maximum" class="form-control "
                                    placeholder="{{ trans('Enter order maximum') }} "
                                    value="{{ old('order_maximum', $item->order_maximum ?? null) }}">

                            </div>

                            <div class="form-group mb-3 col-md-4">
                                <label class="required" for="type">{{ trans('type') }}</label>
                                <select class="custom-select  form-select advance-select" name="type" id="type">

                                    <option value="">{{ trans('select type') }}</option>
                                    <option value="value" @selected(isset($item) and $item->type == 'value')>{{ trans('fixed') }}</option>
                                    <option value="percentage" @selected(isset($item) and $item->type == 'percentage')>{{ trans('percentage') }}
                                    <option value="cashback" @selected(isset($item) and $item->type == 'cashback')>{{ trans('cashback') }}
                                    </option>

                                </select>

                            </div>

                            <div class="form-group mb-3 col-md-4">
                                <label class="required" for="value">{{ trans('value') }}</label>
                                <input type="number" name="value" class="form-control "
                                    placeholder="{{ trans('Enter value') }} "
                                    value="{{ old('value', $item->value ?? null) }}">

                            </div>

                            <div class="form-group mb-3 col-md-4">
                                <label class="" for="max_value">{{ trans('max value') }}</label>
                                <input type="number" name="max_value" class="form-control " id="max_value"
                                    placeholder="{{ trans('Enter max value') }} "
                                    value="{{ old('max_value', $item->max_value ?? null) }}">

                            </div>

                        </div>
                        <div class="card-footer">
                            <div class="row">
                                <div class="col-lg-9 ml-lg-auto">
                                    <button type="submit"
                                        class="btn btn-primary font-weight-bold mr-2">{{ trans('save') }}</button>
                                </div>
                            </div>
                        </div>
                    </form>

                </div>
                <!--end::Card-->
                @if(isset($item) and $item->orders->count() > 0) 
                <div class="card my-2">
                    <div class="card-header">
                         <h3 class="card-title">{{ trans('orders') }}</h3> 
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered text-center">
                                        <thead class="table-primary">
                                            <tr>
                                                <th>{{ trans('client name') }}</th>
                                                <th>{{ trans('client phone') }}</th>
                                                <th>{{ trans('order reference id') }}</th>
                                                <th>{{ trans('order total price') }}</th>
                                                <th>{{ trans('order discount') }}</th>
                                                <th>{{ trans('order status') }}</th>
                                                <th>{{ trans('order date') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($item->orders as $order)
                                                <tr>
                                                    <td>{{ $order->client?->fullname }}</td>
                                                    <td>{{ $order->client?->phone }}</td>
                                                    <td>{{ $order->reference_id }}</td>
                                                    <td>{{ $order->total_price }}</td>
                                                    <td>{{ $order->total_coupon }}</td>
                                                    <td>{{ $order->status }}</td>
                                                    <td>{{ $order->created_at }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <!--end::Container-->
                            </div>
                            <!--end::Post-->
                        </div>
                    </div>
                </div>
                @endif
                <!--end::Content-->
                @include('media::mediaCenter.modal')
            @endsection
            @push('css')
                <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
                <link href="{{ asset('control') }}/js/custom/crud/form.css" rel="stylesheet" type="text/css" />
            @endpush
            @push('js')
                <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
                <script src="https://cdnjs.cloudflare.com/ajax/libs/TableDnD/1.0.5/jquery.tablednd.js"></script>
                <script src="{{ asset('control') }}/js/custom/crud/form.js"></script>
                <script>
                    $(document).ready(function() {
                        $('#order_type').change(function() {

                            if ($(this).val() == 'maid' || $(this).val() == 'host') {
                                $('#product-div').hide();
                            } else {
                                $('#product-div').show();
                            }
                        });
                        $('#all_products').change(function() {
                            if ($(this).is(':checked')) {
                                $('#products').parent().hide();
                                $('#categories').parent().hide();
                                $('#products , #categories').val(null).trigger('change')
                            } else {
                                $('#products').parent().show();
                                $('#categories').parent().show();
                            }
                        });
                        $('#all_users').change(function() {
                            if ($(this).is(':checked')) {
                                $('#users').parent().hide();
                                $('#roles').parent().hide();
                                $('#users,#roles').val(null).trigger('change')
                            } else {
                                $('#users').parent().show();
                                $('#roles').parent().show();
                            }
                        });
                        $('#applying').change(function() {
                            if ($(this).val() == 'manual') {
                                $('#code').parent().show();

                            } else {
                                $('#code').parent().hide();
                            }
                        });
                        $('#applying').change();
                        $('#type').change(function() {
                            if ($(this).val() != 'percentage') {
                                $('#max_value').parent().hide();
                                $('#max_value').val(null);
                            } else {
                                $('#max_value').parent().show();
                            }
                        });
                        $('#type').change();
                    });
                </script>
            @endpush
