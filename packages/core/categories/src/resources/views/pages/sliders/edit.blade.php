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
                            <a href="{{ route('dashboard.index') }}" class="text-muted text-hover-primary">@lang('Home')</a>
                        </li>
                        <!--end::Item-->

                        <!--begin::Item-->
                        <li class="breadcrumb-item text-muted">@lang('categories')</li>
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

                    <form class="form" method="POST" id="operation-form"  redirect-to="{{route("dashboard.sliders.index")}}" data-id="{{ $item->id ?? null }}"
                        @isset($item)
                            action="{{ route('dashboard.sliders.edit', $item->id) }}"
                            data-mode="edit"
                        @else
                            action="{{ route('dashboard.sliders.create') }}"
                            data-mode="new"
                        @endisset>

                        @csrf
                        <div class="card-body row">
                            <div class="row">
                                <div class="form-group mb-3 col-md-6">
                                    <label class="" for="image_en">{{ trans('image en') }}</label>
                                    <div class="media-center-group form-control" data-max="1" data-type="image">
                                        <input type="text" hidden="hidden" class="form-control" name="image_en"
                                            @isset($item) value="{{ $item->image_en }}" @endisset>
                                        <button type="button" class="btn btn-secondary media-center-load"
                                            style="margin-top: 10px;"><i class="fa fa-file-upload"></i></button>
                                        <div class="input-gallery"></div>
                                    </div>
                                </div>
                                <div class="form-group mb-3 col-md-6">
                                    <label class="" for="image_ar">{{ trans('image ar') }}</label>
                                    <div class="media-center-group form-control" data-max="1" data-type="image">
                                        <input type="text" hidden="hidden" class="form-control" name="image_ar"
                                            @isset($item) value="{{ $item->image_ar }}" @endisset>
                                        <button type="button" class="btn btn-secondary media-center-load"
                                            style="margin-top: 10px;"><i class="fa fa-file-upload"></i></button>
                                        <div class="input-gallery"></div>
                                    </div>
                                </div>


                            </div>
                            <div class="form-group mb-3 col-md-6">
                                <label class="required" for="type">{{ trans('type') }}</label>
                                <select class="custom-select  form-select advance-select" name="type" id="type">

                                    <option value="">{{ trans('select type') }}</option>
                                    <option value="services" @selected(isset($item) and $item->type == 'services')>{{ trans('services') }}</option>
                                    <option value="sales" @selected(isset($item) and $item->type == 'sales')>{{ trans('sales') }}</option>
                                    <option value="clothes" @selected(isset($item) and $item->type == 'clothes')>{{ trans('clothes') }}</option>
                                    <option value="host" @selected(isset($item) and $item->type == 'host')>{{ trans('host') }}</option>
                                    <option value="maid" @selected(isset($item) and $item->type == 'maid')>{{ trans('maid') }}</option>

                                </select>

                            </div>
                            <div class="form-group mb-3 col-md-6">
                                <label class="" for="link">{{ trans('link') }}</label>
                                <input type="text" class="form-control" name="link" id="link"
                                    @isset($item) value="{{ $item->link }}" @endisset>
                            </div>
                            <div class="form-group mb-3 col-md-6">
                                <label class="required" for="category_id">{{ trans('category') }}</label>
                                <select class="custom-select  form-select advance-select" name="category_id"
                                    id="category_id">

                                    <option value="">{{ trans('select category') }}</option>
                                    @foreach ($categories ?? [] as $sItem)
                                        <option data-type="{{ $sItem->type }}"  data-id="{{ $sItem->id }}" @selected(isset($item) and $item->category_id == $sItem->id)
                                            value="{{ $sItem->id }}">{{ $sItem->name }}</option>
                                    @endforeach

                                </select>

                            </div>



                            <div class="form-group mb-3 col-md-6">
                                <label class="required" for="status">{{ trans('status') }}</label>
                                <select class="custom-select  form-select advance-select" name="status" id="status">

                                    <option value="">{{ trans('select status') }}</option>
                                    <option value="active" @selected(isset($item) and $item->status == 'active')>{{ trans('active') }}</option>
                                    <option value="not-active" @selected(isset($item) and $item->status == 'not-active')>{{ trans('not-active') }}
                                    </option>

                                </select>

                            </div>

                            <div class="form-group mb-3 col-md-6">
                                <label class="" for="city_id">{{ trans('city') }}</label>
                                <select class="custom-select  form-select advance-select" name="city_id" id="city_id">

                                    <option value="">{{ trans('select city') }}</option>
                                    @foreach ($cities ?? [] as $sItem)
                                        <option data-id="{{ $sItem->id }}" @selected(isset($item) and $item->city_id == $sItem->id)
                                            value="{{ $sItem->id }}">{{ $sItem->name }}</option>
                                    @endforeach

                                </select>

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

                @if(isset($item) && $item->sliderViews)
                @foreach ($item->sliderViews as $sliderView)
                <!--begin::Stats Card-->
                <div class="card mt-6">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bolder fs-3 mb-1">
                                <i class="fas fa-chart-bar text-primary me-2"></i>
                                {{ trans('Slider Link Statistics') }}
                            </span>
                            <span class="text-muted mt-1 fw-bold fs-7">{{ trans('Tracking info for the slider link') }}</span>
                        </h3>
                    </div>
                    <div class="card-body py-3">
                        <div class="row g-4">
                            {{-- Views count --}}
                            <div class="col-md-4">
                                <div class="bg-light-primary rounded p-4 text-center">
                                    <i class="fas fa-eye fs-2x text-primary mb-2"></i>
                                    <div class="fs-1 fw-boldest text-primary">{{ number_format($sliderView->views_count) }}</div>
                                    <div class="text-muted fw-bold fs-7 mt-1">{{ trans('Total Views') }}</div>
                                </div>
                            </div>

                            {{-- Original URL --}}
                            <div class="col-md-4">
                                <div class="bg-light-info rounded p-4 text-center">
                                    <i class="fas fa-link fs-2x text-info mb-2"></i>
                                    <div class="text-info fw-bold fs-7 text-break mt-1" style="word-break:break-all;">
                                        {{ $sliderView->url }}
                                    </div>
                                    <div class="text-muted fw-bold fs-7 mt-1">{{ trans('Original URL') }}</div>
                                </div>
                            </div>

                            {{-- UUID redirect URL --}}
                            <div class="col-md-4">
                                <div class="bg-light-success rounded p-4 text-center">
                                    <i class="fas fa-external-link-alt fs-2x text-success mb-2"></i>
                                    <div class="mt-1">
                                        <a href="{{ route('slider.redirect', ['uuid' => $sliderView->uuid]) }}" target="_blank"
                                           class="text-success fw-bold fs-7 text-break" style="word-break:break-all;">
                                            {{ route('slider.redirect', ['uuid' => $sliderView->uuid]) }}
                                        </a>
                                    </div>
                                    <div class="text-muted fw-bold fs-7 mt-1">{{ trans('Trackable URL (share this)') }}</div>
                                    <button type="button" class="btn btn-sm btn-light-success mt-2"
                                            onclick="copyToClipboard('{{ route('slider.redirect', ['uuid' => $sliderView->uuid]) }}')">
                                        <i class="fas fa-copy me-1"></i>{{ trans('Copy') }}
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- UUID info --}}
                        <div class="mt-4 p-3 bg-light rounded">
                            <span class="fw-bold text-muted fs-7">{{ trans('UUID') }}:</span>
                            <code class="ms-2 fs-7">{{ $sliderView->uuid }}</code>
                        </div>
                    </div>
                </div>
                @endforeach
                <!--end::Stats Card-->
                @endif
            </div>
            <!--end::Container-->
        </div>
        <!--end::Post-->
    </div>
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
            var allCategories = $('#category_id option').clone();
            $('#type').change(function() {
                var type        = $(this).val();
                var $Category   = $('#category_id');

                // Clear the current options
                $Category.empty();

                // Add only the options that belong to the selected category
                allCategories.each(function() {
                    if ($(this).data('type') == type) {
                        $Category.append($(this).clone()); // Add matching options
                    }

                });

                // Trigger Select2 to update the dropdown
                $Category.trigger('change');

            });
            $('#type').change();
        });

        function copyToClipboard(text) {
            if (navigator.clipboard) {
                navigator.clipboard.writeText(text).then(function() {
                    toastr.success('{{ trans('Copied to clipboard!') }}');
                });
            } else {
                var el = document.createElement('textarea');
                el.value = text;
                document.body.appendChild(el);
                el.select();
                document.execCommand('copy');
                document.body.removeChild(el);
                toastr.success('{{ trans('Copied to clipboard!') }}');
            }
        }
    </script>
@endpush
