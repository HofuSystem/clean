@extends('admin::layouts.dashboard')

@section('content')
    <!--begin::Content-->
    <div class="container-fluid flex-grow-1 container-p-y">
        <!--begin::Toolbar-->
        <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack mb-3">
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
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard.index') }}" class="text-muted text-hover-primary">@lang('Home')</a>
                    </li>
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard.fixed-costs.index') }}" class="text-muted text-hover-primary">{{ trans('Fixed Costs Management') }}</a>
                    </li>
                    <li class="breadcrumb-item text-dark">{{ $title }}</li>
                </ul>
                <!--end::Breadcrumb-->
            </div>
            <!--end::Page title-->
        </div>
        <!--end::Toolbar-->

        <!--begin::Container-->
        <div class="container-fluid">
            <div class="card shadow-sm border-0">
                <div class="card-header d-flex justify-content-between align-items-center py-3">
                    <h5 class="card-title mb-0 text-dark fw-bolder">{{ trans('Fixed Cost Details') }}</h5>
                    <div class="d-flex gap-2">
                        <a href="{{ route('dashboard.fixed-costs.edit', $fixedCost) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit me-1"></i> {{ trans('Edit') }}
                        </a>
                        <a href="{{ route('dashboard.fixed-costs.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left me-1"></i> {{ trans('Back to List') }}
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <table class="table table-borderless fs-6">
                                <tr>
                                    <th class="w-35 text-muted fw-bold">{{ trans('Name') }}:</th>
                                    <td class="fw-bolder text-dark">{{ $fixedCost->name }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted fw-bold">{{ trans('Description') }}:</th>
                                    <td>{{ $fixedCost->description ?: trans('No description provided') }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted fw-bold">{{ trans('Amount') }}:</th>
                                    <td class="fw-bold text-primary">{{ number_format($fixedCost->amount, 2) }} {{ trans('SAR') }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted fw-bold">{{ trans('Frequency') }}:</th>
                                    <td>
                                        <span class="badge bg-label-info px-3 py-2 fs-7">{{ trans(ucfirst($fixedCost->frequency)) }}</span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless fs-6">
                                <tr>
                                    <th class="w-35 text-muted fw-bold">{{ trans('Monthly Amount') }}:</th>
                                    <td class="fw-bold text-success">{{ number_format($fixedCost->monthly_amount, 2) }} {{ trans('SAR') }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted fw-bold">{{ trans('Date') }}:</th>
                                    <td><span class="badge bg-light text-dark border">{{ $fixedCost->date->format('Y-m-d') }}</span></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card bg-light border-0">
                                <div class="card-header bg-transparent border-bottom-0 pt-3">
                                    <h6 class="mb-0 fw-bolder text-dark">{{ trans('Cost Breakdown') }}</h6>
                                </div>
                                <div class="card-body pt-1">
                                    <div class="row text-center g-3">
                                        <div class="col-md-4">
                                            <div class="border rounded p-3 bg-white shadow-xs">
                                                <h4 class="text-primary fw-bolder mb-1">{{ number_format($fixedCost->amount, 2) }} {{ trans('SAR') }}</h4>
                                                <small class="text-muted fw-bold">{{ trans('Original Amount') }}</small>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="border rounded p-3 bg-white shadow-xs">
                                                <h4 class="text-info fw-bolder mb-1">{{ trans(ucfirst($fixedCost->frequency)) }}</h4>
                                                <small class="text-muted fw-bold">{{ trans('Frequency') }}</small>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="border rounded p-3 bg-white shadow-xs">
                                                <h4 class="text-success fw-bolder mb-1">{{ number_format($fixedCost->monthly_amount, 2) }} {{ trans('SAR') }}</h4>
                                                <small class="text-muted fw-bold">{{ trans('Monthly Equivalent') }}</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4 pt-3 border-top">
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <div>
                                    <small class="text-muted d-block">{{ trans('Created') }}: {{ $fixedCost->created_at->format('Y-m-d H:i:s') }}</small>
                                    @if($fixedCost->updated_at != $fixedCost->created_at)
                                        <small class="text-muted d-block">{{ trans('Last Updated') }}: {{ $fixedCost->updated_at->format('Y-m-d H:i:s') }}</small>
                                    @endif
                                </div>
                                <div class="d-flex gap-2">
                                    <form action="{{ route('dashboard.fixed-costs.destroy', $fixedCost) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('{{ trans('Are you sure you want to delete this fixed cost?') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash me-1"></i> {{ trans('Delete') }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
