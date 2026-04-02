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
                            <a href="" class="text-muted text-hover-primary">@lang('Home')</a>
                        </li>
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('dashboard.company-employees.index') }}" class="text-muted text-hover-primary">@lang('company employees')</a>
                        </li>
                        <li class="breadcrumb-item text-dark">{{ $title }}</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="post d-flex flex-column-fluid" id="kt_post">
            <div id="kt_content_container" class="container-fluid">
                <div class="card">
                    <form class="form" method="POST" id="operation-form" 
                        redirect-to="{{ route('dashboard.company-employees.index') }}" 
                        data-id="{{ $item->id ?? null }}"
                        @isset($item)
                            action="{{ route('dashboard.company-employees.edit', $item->id) }}"
                            data-mode="edit"
                        @else
                            action="{{ route('dashboard.company-employees.create') }}"
                            data-mode="new"
                        @endisset>

                        @csrf
                        <div class="card-body row">

                            <!--– User –-->
                            <div class="form-group mb-3 col-md-6">
                                <label class="required" for="user_id">{{ trans('user') }}</label>
                                <select class="custom-select form-select advance-select" name="user_id" id="user_id">
                                    <option value="">{{ trans('select') . ' ' . trans('user') }}</option>
                                    @foreach($users ?? [] as $sItem)
                                        <option value="{{ $sItem->id }}"
                                            @selected(isset($item) && $item->user_id == $sItem->id)>
                                            {{ $sItem->fullname }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!--– Company –-->
                            <div class="form-group mb-3 col-md-6">
                                <label class="required" for="company_id">{{ trans('company') }}</label>
                                <select class="custom-select form-select advance-select" name="company_id" id="company_id">
                                    <option value="">{{ trans('select') . ' ' . trans('company') }}</option>
                                    @foreach($companies ?? [] as $sItem)
                                        <option value="{{ $sItem->id }}"
                                            @selected(isset($item) && $item->company_id == $sItem->id)>
                                            {{ $sItem->fullname }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!--– Permission –-->
                            <div class="form-group mb-3 col-md-6">
                                <label class="required" for="permission_id">{{ trans('permission') }}</label>
                                <select class="custom-select form-select advance-select" name="permission_id" id="permission_id">
                                    <option value="">{{ trans('select') . ' ' . trans('permission') }}</option>
                                    @foreach($permissions ?? [] as $sItem)
                                        <option value="{{ $sItem->id }}"
                                            @selected(isset($item) && $item->permission_id == $sItem->id)>
                                            {{ $sItem->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!--– Branch –-->
                            <div class="form-group mb-3 col-md-6">
                                <label for="branch_id">{{ trans('branch') }}</label>
                                <select class="custom-select form-select advance-select" name="branch_id" id="branch_id">
                                    <option value="">{{ trans('select') . ' ' . trans('branch') }}</option>
                                    <!-- Branches should be loaded dynamically based on company, but for simple CRUD we can list all or empty -->
                                </select>
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
            </div>
        </div>
    </div>
@endsection
@push('js')
    <script src="{{ asset('control') }}/js/custom/crud/form.js"></script>
    <script>
        $(document).on('change', '#company_id', function() {
            var companyId = $(this).val();
            // Implement AJAX to load branches if needed
        });
    </script>
@endpush
