@extends('admin::layouts.dashboard')

@section('content')
    <div class="container-fluid flex-grow-1 container-p-y mx-auto">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="mb-0">{{ trans('Order Quantities Report Filters') }}</h5>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="{{ route('dashboard.order-quantities-report.index') }}">
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label for="from_date">{{ trans('From Date') }}</label>
                                    <input type="date" name="from_date" id="from_date" class="form-control" value="{{ request('from_date') }}">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="to_date">{{ trans('To Date') }}</label>
                                    <input type="date" name="to_date" id="to_date" class="form-control" value="{{ request('to_date') }}">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="type">{{ trans('Order Type') }}</label>
                                    <select name="type" id="type" class="form-select">
                                        <option value="">{{ trans('All') }}</option>
                                        <option value="client" {{ request('type') == 'client' ? 'selected' : '' }}>{{ trans('Client') }}</option>
                                        <option value="company" {{ request('type') == 'company' ? 'selected' : '' }}>{{ trans('Company') }}</option>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="status">{{ trans('Status') }}</label>
                                    <select name="status" id="status" class="form-select select2">
                                        <option value="">{{ trans('All Statuses') }}</option>
                                        @foreach($statuses as $status)
                                            <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>{{ trans($status) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="company_id">{{ trans('Company') }}</label>
                                    <select name="company_id" id="company_id" class="form-select ajax-select-company">
                                        <option value="">{{ trans('All Companies') }}</option>
                                        @if($selectedCompany)
                                            <option value="{{ $selectedCompany->id }}" selected>{{ $selectedCompany->fullname }}</option>
                                        @endif
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="client_id">{{ trans('Client') }}</label>
                                    <select name="client_id" id="client_id" class="form-select ajax-select-client">
                                        <option value="">{{ trans('All Clients') }}</option>
                                        @if($selectedClient)
                                            <option value="{{ $selectedClient->id }}" selected>{{ $selectedClient->fullname }} ({{ $selectedClient->phone }})</option>
                                        @endif
                                    </select>
                                </div>

                                <div class="col-md-3 mb-3 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fas fa-filter me-1"></i> {{ trans('Filter') }}
                                    </button>
                                </div>
                                <div class="col-md-3 mb-3 d-flex align-items-end">
                                    <a href="{{ route('dashboard.order-quantities-report.index') }}" class="btn btn-secondary w-100">
                                        <i class="fas fa-undo me-1"></i> {{ trans('Reset') }}
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        <div class="row mb-4">
            @foreach(['lab', 'washer'] as $type)
                <div class="col-md-6 mb-3">
                    <div class="card {{ $type == 'lab' ? 'bg-primary' : 'bg-success' }} text-white">
                        <div class="card-body py-3 text-center">
                            <div class="row">
                                <div class="col-6 border-end">
                                    <h6 class="text-white mb-1">{{ trans('Total') }} {{ trans($type) }} {{ trans('Quantity') }}</h6>
                                    <h3 class="text-white mb-0">{{ number_format($reportData->get($type)?->sum('total_quantity') ?? 0) }}</h3>
                                </div>
                                <div class="col-6">
                                    <h6 class="text-white mb-1">{{ trans('Total') }} {{ trans($type) }} {{ trans('Cost') }}</h6>
                                    <h3 class="text-white mb-0">{{ number_format($costsSummary->{'total_' . $type . '_cost'} ?? 0, 2) }} <small>{{ trans('SAR') }}</small></h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="row">
            @foreach(['lab', 'washer'] as $type)
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 text-capitalize">{{ trans($type) }} {{ trans('Report') }}</h5>
                        <div class="card-tools">
                            <a href="{{ route('dashboard.order-quantities-report.export', array_merge(request()->all(), ['wash_type' => $type])) }}" class="btn btn-outline-success btn-sm">
                                <i class="fas fa-file-excel me-1"></i> {{ trans('Export') }} {{ trans($type) }}
                            </a>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>{{ trans('Product Name') }}</th>
                                        <th>{{ trans('Category') }}</th>
                                        <th>{{ trans('Total Quantity') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($reportData->get($type) ?? [] as $row)
                                        <tr>
                                            <td>{{ $row->product_name }}</td>
                                            <td>{{ $row->category_name }}</td>
                                            <td class="fw-bold">{{ number_format($row->total_quantity) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center py-4">{{ trans('No data found') }}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        </div>
    </div>
@endsection

@push('js')
<script>
    $(document).ready(function() {
        $('.ajax-select-company').select2({
            placeholder: "{{ trans('Search for Company') }}",
            allowClear: true,
            ajax: {
                url: "{{ route('dashboard.order-quantities-report.select-companies') }}",
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term
                    };
                },
                processResults: function(data) {
                    return {
                        results: data.results
                    };
                },
                cache: true
            }
        });

        $('.ajax-select-client').select2({
            placeholder: "{{ trans('Search for Client') }}",
            allowClear: true,
            ajax: {
                url: "{{ route('dashboard.order-quantities-report.select-clients') }}",
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term
                    };
                },
                processResults: function(data) {
                    return {
                        results: data.results
                    };
                },
                cache: true
            }
        });
    });

    function exportTableToCSV(filename) {

        var csv = [];
        var rows = document.querySelectorAll("#reportTable tr");
        
        for (var i = 0; i < rows.length; i++) {
            var row = [], cols = rows[i].querySelectorAll("td, th");
            
            for (var j = 0; j < cols.length; j++) 
                row.push('"' + cols[j].innerText.trim() + '"');
            
            csv.push(row.join(","));        
        }

        // Download CSV file
        downloadCSV(csv.join("\n"), filename);
    }

    function downloadCSV(csv, filename) {
        var csvFile;
        var downloadLink;

        // CSV file
        csvFile = new Blob(["\ufeff" + csv], {type: "text/csv;charset=utf-8;"});

        // Download link
        downloadLink = document.createElement("a");

        // File name
        downloadLink.download = filename;

        // Create a link to the file
        downloadLink.href = window.URL.createObjectURL(csvFile);

        // Hide download link
        downloadLink.style.display = "none";

        // Add the link to DOM
        document.body.appendChild(downloadLink);

        // Click download link
        downloadLink.click();
    }
</script>
@endpush
