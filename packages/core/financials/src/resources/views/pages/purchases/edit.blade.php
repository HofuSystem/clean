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
                        <li class="breadcrumb-item text-muted">@lang("Purchases")</li>
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
                    
                    <form class="form" method="POST" id="operation-form" enctype="multipart/form-data" redirect-to="{{route("dashboard.purchases.index")}}" data-id="{{$item->id ?? null}}"
                        @isset($item)
                            action="{{ route("dashboard.purchases.edit",$item->id) }}"
                            data-mode="edit"
                        @else
                            action="{{ route("dashboard.purchases.create") }}"
                            data-mode="new"
                        @endisset
                        >

                        @csrf
                        @isset($item)
                            @method('PUT')
                        @endisset

                        <div class="card-body row">
                        
                            <div class="form-group mb-3 col-md-6">
                                <label class="required" for="item_id">{{ trans("item") }}</label>
                                <select class="custom-select form-select advance-select" name="item_id" id="item_id">
                                    <option value="">{{trans("select item")}}</option>
                                    @foreach($items ?? [] as $iItem)
                                        <option value="{{$iItem->id}}" @selected(isset($item) and $item->item_id == $iItem->id)>{{$iItem->name}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group mb-3 col-md-6">
                                <label class="required" for="provider_id">{{ trans("provider") }}</label>
                                <select class="custom-select form-select advance-select" name="provider_id" id="provider_id">
                                    <option value="">{{trans("select provider")}}</option>
                                    @foreach($providers ?? [] as $pItem)
                                        <option value="{{$pItem->id}}" @selected(isset($item) and $item->provider_id == $pItem->id)>{{$pItem->name}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group mb-3 col-md-4">
                                <label class="required" for="value_before_tax">{{ trans("value before tax") }}</label>
                                <input type="number" step="0.01" name="value_before_tax" id="value_before_tax" class="form-control calc-tax"
                                    placeholder="{{ trans("Enter value before tax") }} " value="{{ $item->value_before_tax ?? '' }}">
                            </div>

                            <div class="form-group mb-3 col-md-4">
                                <label class="required" for="tax_value">{{ trans("tax value") }}</label>
                                <input type="number" step="0.01" name="tax_value" id="tax_value" class="form-control calc-tax"
                                    placeholder="{{ trans("Enter tax value") }} " value="{{ $item->tax_value ?? '' }}">
                            </div>

                            <div class="form-group mb-3 col-md-4">
                                <label class="required" for="value_after_tax">{{ trans("value after tax") }}</label>
                                <input type="number" step="0.01" name="value_after_tax" id="value_after_tax" class="form-control calc-tax"
                                    placeholder="{{ trans("Enter value after tax") }} " value="{{ $item->value_after_tax ?? '' }}">
                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <label class="" for="notes">{{ trans("notes") }}</label>
                                <textarea name="notes" class="form-control "
                                    placeholder="{{ trans("Enter notes") }} ">{{ $item->notes ?? '' }}</textarea>
                            </div>

                            <div class="form-group mb-3 col-md-6">
                                <label for="attachment">{{ trans("Attachment") }}</label>
                                <input type="file" name="attachment" id="attachment" class="form-control">
                                @if(isset($item) && $item->attachment)
                                    <div class="mt-2">
                                        <a href="{{ asset('storage/' . $item->attachment) }}" target="_blank" class="btn btn-sm btn-light-primary">
                                            <i class="fas fa-paperclip"></i> @lang('View current attachment')
                                        </a>
                                    </div>
                                @endif
                            </div>

                            <div class="form-group mb-3 col-md-6">
                                <label for="collection_date">{{ trans("Collection Date") }}</label>
                                <input type="date" name="collection_date" id="collection_date" class="form-control"
                                    value="{{ isset($item) && $item->collection_date ? $item->collection_date->format('Y-m-d') : '' }}">
                            </div>

                        </div>
                        <div class="card-footer">
                            <div class="row">
                                <div class="col-lg-9 ml-lg-auto">
                                    <button type="submit" class="btn btn-primary font-weight-bold mr-2">{{ trans('save') }}</button>
                                </div>
                            </div>
                        </div>
                    </form>
        
                </div>
                <!--end::Card-->
            </div>
            <!--end::Container-->
        </div>
        <!--end::Post-->
    </div>
    <!--end::Content-->
    @include('media::mediaCenter.modal')
@endsection
@push('css')
    <link href="{{ asset('control') }}/js/custom/crud/form.css" rel="stylesheet" type="text/css" />
@endpush
@push('js')
    <script src="{{ asset('control') }}/js/custom/crud/form.js"></script>
    <script>
        $(document).ready(function() {
            // Auto calculate value_after_tax or tax_value etc, or just helpful hooks if they edit them
            $('.calc-tax').on('input', function() {
                var before = parseFloat($('#value_before_tax').val()) || 0;
                var tax = parseFloat($('#tax_value').val()) || 0;
                if ($(this).attr('id') !== 'value_after_tax') {
                    $('#value_after_tax').val((before + tax).toFixed(2));
                }
            });
        });
    </script>
@endpush