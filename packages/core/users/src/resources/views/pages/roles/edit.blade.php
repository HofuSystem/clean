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
                        <li class="breadcrumb-item text-muted">@lang('users')</li>
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

                    <form class="form" method="POST" id="operation-form"  redirect-to="{{route("dashboard.roles.index")}}" data-id="{{ $item->id ?? null }}"
                        @isset($item)
                            action="{{ route('dashboard.roles.edit', $item->id) }}"
                            data-mode="edit"
                        @else
                            action="{{ route('dashboard.roles.create') }}"
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
                                            <label class="required" for="title">{{ trans('role title') }}</label>
                                            <input type="text" name="translations[en][title]" class="form-control "
                                                placeholder="{{ trans('Enter title') }} "
                                                value="@isset($item) {{ $item?->translate('en')?->title }} @endisset">

                                        </div>

                                    </div>
                                    <div class="tab-pane fade " id="title-ar" role="tabpanel" aria-labelledby="ar-tab">

                                        <div class="form-group mb-3 col-md-12">
                                            <label class="required" for="title">{{ trans('role title') }}</label>
                                            <input type="text" name="translations[ar][title]" class="form-control "
                                                placeholder="{{ trans('Enter title') }} "
                                                value="@isset($item) {{ $item?->translate('ar')?->title }} @endisset">

                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="form-group mb-3 col-md-12">
                                <label class="required" for="name">{{ trans('name') }}</label>
                                <input type="text" name="name" class="form-control "
                                    placeholder="{{ trans('Enter name') }} "
                                    value="@isset($item){{ $item->name }}@endisset">

                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <ul class="nav nav-tabs" id="myTab" role="tablist">
                                    @foreach ($tabs as $key => $innerTabs)
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link @if ($loop->first) active @endif " id="{{ $key }}-tab" data-bs-toggle="tab" data-bs-target="#{{ $key }}-tab-pane" type="button" role="tab" aria-controls="{{ $key }}-tab-pane" aria-selected="true">{{ trans( $key) }}</button>
                                        </li>
                                        @endforeach
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="other-tab" data-bs-toggle="tab" data-bs-target="#other-tab-pane" type="button" role="tab" aria-controls="other-tab-pane" aria-selected="true">{{ trans( 'other') }}</button>
                                        </li>
                  
                                  </ul>
                                  
                                  <div class="tab-content" id="myTabContent">
                                    @foreach ($tabs as $key => $innerTabs)
                                        <div class="tab-pane fade  @if ($loop->first) show active @endif" id="{{ $key }}-tab-pane" role="tabpanel" aria-labelledby="{{ $key }}-tab" tabindex="0">
                                            <div class="switched-group mt-2">

                                                <label class="" for="permissions">{{ trans($key) }}</label>
                                                <div class="form-group row" multiple>
                                                    <div class="form-check form-switch col-md-12 mt-1">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input alls" type="checkbox"  name="alls">
                                                                {{ trans('select all of')." ".trans($key) }}
                                                        </label>
                                                    </div>
                                                        @foreach ($innerTabs as $innerTab)
                                                        @php
                                                            $innerTabPermissions = $permissions->filter(function($permission)use($innerTab){
                                                                return str_starts_with($permission->tab,$innerTab);
                                                            });
                                                        @endphp 
                                                            @foreach ($innerTabPermissions ?? [] as $sItem)
                                                                <div class="form-check form-switch col-md-4 mt-1">
                                                                    <input class="form-check-input" type="checkbox" @checked(isset($rolePermissions[$sItem->name]))
                                                                        id="permissions-{{ $sItem->id }}" name="permissions[]"
                                                                        value="{{ $sItem->name }}">
                                                                    <label class="form-check-label"
                                                                        for="permissions-{{ $sItem->id }}">{{ $sItem->title ?? $sItem->name }}</label>
                                                                </div>
                                                            @endforeach
                                                        @endforeach

                                                    </div>
                                                </div>

                                        </div>
                                    @endforeach 
                                    <div class="tab-pane fade" id="other-tab-pane" role="tabpanel" aria-labelledby="other-tab" tabindex="0">
                                        <div class="switched-group mt-2">

                                            <label class="" for="permissions">{{ trans('other') }}</label>
                                            <div class="form-group row" multiple>
                                                <div class="form-check form-switch col-md-12 mt-1">
                                                    <label class="form-check-label">
                                                        <input class="form-check-input alls" type="checkbox"  name="alls" data-tab="other">
                                                            {{ trans('select all of')." ".trans('other') }}
                                                        </label>
                                                </div>
                                                @php
                                                    $otherPermissions = $permissions->filter(function($permission)use($tabs){
                                                        $other = true;
                                                        foreach($tabs as $innerTabs){
                                                            foreach ($innerTabs as $key => $innerTab) {
                                                                if(str_starts_with($permission->tab,$innerTab) || $innerTab == 'other'){
                                                                    $other = false;
                                                                    break;
                                                                }
                                                            }
                                                        }
                                                        if($other){
                                                            return $other;
                                                        }
                                                        
                                                    });
                                                @endphp
                                                @foreach ($otherPermissions ?? [] as $sItem)
                                                    <div class="form-check form-switch col-md-4 mt-1">
                                                        <input class="form-check-input" type="checkbox" @checked(isset($rolePermissions[$sItem->name]))
                                                            id="permissions-{{ $sItem->id }}" name="permissions[]"
                                                            value="{{ $sItem->name }}">
                                                        <label class="form-check-label"
                                                            for="permissions-{{ $sItem->id }}">{{ $sItem->title ?? $sItem->name }}</label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                  </div>
                              
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
        $('.alls').change(function (e) { 
            e.preventDefault();
            let row = $(this).closest('.row'); // Get the closest table row
            let checkboxes = row.find('input[type="checkbox"]'); // Find all checkboxes in that row
            let isChecked = $(this).prop('checked'); // Get the state of the clicked checkbox

            checkboxes.prop('checked', isChecked); // Set all checkboxes in the row to the same state
        });
    </script>
@endpush
