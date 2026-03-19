@extends('admin::layouts.dashboard')
@section('content')
    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        <div class="toolbar my-3" id="kt_toolbar">
            <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
                <div class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
                    <h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1">{{ $title }}</h1>
                    <span class="h-20px border-gray-200 border-start mx-4"></span>
                    <ul class="breadcrumb breadcrumb-separatorless fw-bold fs-7 my-1">
                        <li class="breadcrumb-item text-muted">
                            <a href="" class="text-muted text-hover-primary">@lang('Home')</a>
                        </li>
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('dashboard.companies.index') }}" class="text-muted text-hover-primary">@lang('companies')</a>
                        </li>
                        <li class="breadcrumb-item text-dark">{{ $title }}</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="post d-flex flex-column-fluid" id="kt_post">
            <div id="kt_content_container" class="container-fluid">
                <div class="card">
                    <div class="card-body">

                        <!--– Summary –-->
                        <div class="row mb-5">
                            <div class="col-md-3 text-center">
                                @if($item->avatar)
                                    <img src="{{ asset($item->avatar) }}" alt="{{ $item->fullname }}"
                                        class="rounded-circle" style="width:120px;height:120px;object-fit:cover;">
                                @else
                                    <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center mx-auto"
                                        style="width:120px;height:120px;">
                                        <i class="fas fa-building fa-3x text-white"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-9">
                                <table class="table table-bordered">
                                    <tr>
                                        <th>@lang('company name')</th>
                                        <td>{{ $item->fullname }}</td>
                                    </tr>
                                    <tr>
                                        <th>@lang('line of business')</th>
                                        <td>{{ $item->line_of_business ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>@lang('email')</th>
                                        <td>{{ $item->email ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>@lang('phone')</th>
                                        <td>{{ $item->phone ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>@lang('owner')</th>
                                        <td>{{ $item->owner?->fullname ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>@lang('created at')</th>
                                        <td>{{ $item->created_at?->format('Y-m-d H:i') }}</td>
                                    </tr>
                                </table>
                                <a href="{{ route('dashboard.companies.edit', $item->id) }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-edit"></i> @lang('edit')
                                </a>
                            </div>
                        </div>

                        <!--– Branches –-->
                        <h4 class="mt-4">@lang('branches')</h4>
                        <hr>
                        @if($item->branches->isEmpty())
                            <p class="text-muted">@lang('No branches found')</p>
                        @else
                            <div class="table-responsive">
                                <table class="table table-striped table-hover text-center">
                                    <thead class="table-primary">
                                        <tr>
                                            <th>#</th>
                                            <th>@lang('branch name')</th>
                                            <th>@lang('location')</th>
                                            <th>@lang('city')</th>
                                            <th>@lang('district')</th>
                                            <th>@lang('default')</th>
                                            <th>@lang('created at')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($item->branches as $branch)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $branch->name }}</td>
                                                <td>{{ $branch->location ?? '-' }}</td>
                                                <td>{{ $branch->city?->name ?? '-' }}</td>
                                                <td>{{ $branch->district?->name ?? '-' }}</td>
                                                <td>{!! $branch->is_default ? 'Yes' : 'No' !!}</td>
                                                <td>{{ $branch->created_at?->format('Y-m-d') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif

                        <!--– Contracts –-->
                        <h4 class="mt-5">@lang('contracts')</h4>
                        <hr>
                        @php $contracts = $item->contracts ?? collect(); @endphp
                        @if($contracts->isEmpty())
                            <p class="text-muted">@lang('No contracts found')</p>
                        @else
                            <div class="table-responsive">
                                <table class="table table-striped table-hover text-center">
                                    <thead class="table-primary">
                                        <tr>
                                            <th>#</th>
                                            <th>@lang('title')</th>
                                            <th>@lang('start date')</th>
                                            <th>@lang('end date')</th>
                                            <th>@lang('Actions')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($contracts as $contract)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $contract->title }}</td>
                                                <td>{{ $contract->start_date?->format('Y-m-d') ?? '-' }}</td>
                                                <td>{{ $contract->end_date?->format('Y-m-d') ?? '-' }}</td>
                                                <td>
                                                    <a href="{{ route('dashboard.contracts.show', $contract->id) }}"
                                                        class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
