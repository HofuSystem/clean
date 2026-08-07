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
                    <h5 class="card-title mb-0 text-dark fw-bolder">{{ trans('Edit Fixed Cost') }}: {{ $fixedCost->name }}</h5>
                    <a href="{{ route('dashboard.fixed-costs.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i> {{ trans('Back to List') }}
                    </a>
                </div>
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger mb-4">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('dashboard.fixed-costs.update', $fixedCost) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label fw-bold">{{ trans('Name') }} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                       id="name" name="name" value="{{ old('name', $fixedCost->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="amount" class="form-label fw-bold">{{ trans('Amount') }} <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" min="0" class="form-control @error('amount') is-invalid @enderror"
                                       id="amount" name="amount" value="{{ old('amount', $fixedCost->amount) }}" required>
                                @error('amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="frequency" class="form-label fw-bold">{{ trans('Frequency') }} <span class="text-danger">*</span></label>
                                <select class="form-select advance-select @error('frequency') is-invalid @enderror" id="frequency" name="frequency" required>
                                    <option value="">{{ trans('Select Frequency') }}</option>
                                    <option value="monthly" {{ old('frequency', $fixedCost->frequency) == 'monthly' ? 'selected' : '' }}>{{ trans('Monthly') }}</option>
                                    <option value="quarterly" {{ old('frequency', $fixedCost->frequency) == 'quarterly' ? 'selected' : '' }}>{{ trans('Quarterly') }}</option>
                                    <option value="yearly" {{ old('frequency', $fixedCost->frequency) == 'yearly' ? 'selected' : '' }}>{{ trans('Yearly') }}</option>
                                </select>
                                @error('frequency')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="date" class="form-label fw-bold">{{ trans('Date') }} <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('date') is-invalid @enderror"
                                       id="date" name="date" value="{{ old('date', $fixedCost->date->format('Y-m-d')) }}" required>
                                @error('date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="description" class="form-label fw-bold">{{ trans('Description') }}</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description" name="description" rows="3">{{ old('description', $fixedCost->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('dashboard.fixed-costs.index') }}" class="btn btn-secondary">
                                {{ trans('Cancel') }}
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> {{ trans('Update Fixed Cost') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
