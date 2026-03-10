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
                        <li class="breadcrumb-item text-muted">@lang('products')</li>
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
                <div class="card show-page">

                    <div class="card">
                        <div class="card-body row">

                            <div class="col-12 mt-5">
                                <ul class="nav nav-tabs" id="languageTabs" role="tablist">

                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="name-en-tab" data-bs-toggle="tab"
                                            data-bs-target="#name-en" type="button" role="tab" aria-controls="name-en"
                                            aria-selected="true">
                                            {{ trans('English') }}
                                        </button>
                                    </li>

                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link " id="name-ar-tab" data-bs-toggle="tab"
                                            data-bs-target="#name-ar" type="button" role="tab" aria-controls="name-ar"
                                            aria-selected="false">
                                            {{ trans('العربية') }}
                                        </button>
                                    </li>

                                </ul>
                                <div class="tab-content mt-3" id="languageTabsContent">

                                    <div class="tab-pane fade show active" id="name-en" role="tabpanel"
                                        aria-labelledby="en-tab">

                                        <div class="form-group mb-3 col-md-12">
                                            <label>{{ trans('name') }}</label>
                                            <p>{{ $item?->translate('en')?->name ?? 'N/A' }}</p>
                                        </div>

                                    </div>

                                    <div class="tab-pane fade " id="name-ar" role="tabpanel" aria-labelledby="ar-tab">

                                        <div class="form-group mb-3 col-md-12">
                                            <label>{{ trans('name') }}</label>
                                            <p>{{ $item?->translate('ar')?->name ?? 'N/A' }}</p>
                                        </div>

                                    </div>

                                </div>
                            </div>

                            <div class="col-12 mt-5">
                                <ul class="nav nav-tabs" id="languageTabs" role="tablist">

                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="desc-en-tab" data-bs-toggle="tab"
                                            data-bs-target="#desc-en" type="button" role="tab" aria-controls="desc-en"
                                            aria-selected="true">
                                            {{ trans('English') }}
                                        </button>
                                    </li>

                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link " id="desc-ar-tab" data-bs-toggle="tab"
                                            data-bs-target="#desc-ar" type="button" role="tab" aria-controls="desc-ar"
                                            aria-selected="false">
                                            {{ trans('العربية') }}
                                        </button>
                                    </li>

                                </ul>
                                <div class="tab-content mt-3" id="languageTabsContent">

                                    <div class="tab-pane fade show active" id="desc-en" role="tabpanel"
                                        aria-labelledby="en-tab">

                                        <div class="form-group mb-3 col-md-12">
                                            <label>{{ trans('desc') }}</label>
                                            <p>{{ $item?->translate('en')?->desc ?? 'N/A' }}</p>
                                        </div>

                                    </div>

                                    <div class="tab-pane fade " id="desc-ar" role="tabpanel" aria-labelledby="ar-tab">

                                        <div class="form-group mb-3 col-md-12">
                                            <label>{{ trans('desc') }}</label>
                                            <p>{{ $item?->translate('ar')?->desc ?? 'N/A' }}</p>
                                        </div>

                                    </div>

                                </div>
                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <label>{{ trans('cover image') }}</label>
                                <div class="gallary-images">{!! Core\MediaCenter\Helpers\MediaCenterHelper::getImagesHtml($item->image) !!}</div>
                            </div>


                            <div class="form-group mb-3 col-md-12">
                                <label>{{ trans('type') }}</label>
                                <div class="alert alert-warning m-1" role="alert">{{ $item->type ?? 'N/A' }}</div>
                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <label>{{ trans('sku') }}</label>
                                <p>{{ $item->sku ?? 'N/A' }}</p>
                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <label>{{ trans('is package') }}</label>
                                <p>{{ $item->is_package ?? 'N/A' }}</p>
                            </div>

                            <div class="form-group mb-3 col-md-6">
                                <label>{{ trans('category') }}</label>
                                @isset($item->category)
                                    <div class="alert alert-primary m-1" role="alert"><a
                                            href="{{ route('dashboard.categories.show', $item->category->id) }}">{{ $item?->category?->name ?? 'N/A' }}</a>
                                    </div>
                                @endisset

                            </div>

                            <div class="form-group mb-3 col-md-6">
                                <label>{{ trans('sub category') }}</label>
                                @isset($item->subCategory)
                                    <div class="alert alert-primary m-1" role="alert"><a
                                            href="{{ route('dashboard.categories.show', $item->subCategory->id) }}">{{ $item?->subCategory?->name ?? 'N/A' }}</a>
                                    </div>
                                @endisset

                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <label>{{ trans('price') }}</label>
                                <p>{{ $item->price ?? 'N/A' }}</p>
                            </div>

                            <div class="mt-3">
                                <h3 class="text-dark">{{ trans('prices') }}</h3>
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead class="table-primary">
                                            <tr>
                                                <th>{{ trans('city') }}</th>
                                                <th>{{ trans('price') }}</th>
                                                <th>{{ trans('cost') }}</th>
                                                <th>{{ trans('actions') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                            @foreach ($item->prices ?? [] as $sItem)
                                                <tr>

                                                    <td><a href="{{ route('dashboard.prices.show', $sItem->id) }}"
                                                            target="_blank"> {{ $sItem?->city?->name }}</a></td>
                                                    <td>{{ $sItem->price ?? 'N/A' }}</td>
                                                    <td>{{ $sItem->cost ?? 'N/A' }}</td>
                                                    <td>{!! $sItem->showActions !!}</td>
                                                </tr>
                                            @endforeach

                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <label>{{ trans('quantity') }}</label>
                                <p>{{ $item->quantity ?? 'N/A' }}</p>
                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <label>{{ trans('status') }}</label>
                                <div class="alert alert-warning m-1" role="alert">{{ $item->status ?? 'N/A' }}</div>
                            </div>

                        </div>
                        <div class="card-footer">
                            <a href="{{ route('dashboard.products.index') }}" class="btn btn-secondary"><i
                                    class="fas fa-arrow-left"></i> {{ trans('Back') }}</a>
                        </div>
                    </div>


                    @include('comment::inc.comment-section', [
                        'commentUrl' => route('dashboard.products.comment', $item->id),
                    ])
                </div>
                <!--end::Card-->
            </div>
            <!--end::Container-->
        </div>
        <!--end::Post-->
    </div>
    <!--end::Content-->
@endsection
@push('css')
    <link href="{{ asset('control') }}/js/custom/crud/show.css" rel="stylesheet" type="text/css" />
@endpush
@push('js')
@endpush
