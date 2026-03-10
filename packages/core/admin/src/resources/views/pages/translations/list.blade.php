@extends('admin::layouts.dashboard')

@section('content')
    <!-- BEGIN: Content-->

    <div class="container-fluid flex-grow-1 container-p-y">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-start mb-0">{{ trans('Admin') }} / </h2>
                        <h2 class="content-header-title float-start mb-0">{{ $title }}</h2>

                    </div>
                </div>
            </div>
            <div class="content-header-right text-md-end col-md-3 col-12 d-md-block d-none">
                <div class="row">
                    <div class="col">

                        @can('dashboard.translation.create')
                        <h3 class="card-title"><a data-bs-toggle="modal" data-bs-target="#addTrans"
                            href="{{ route('dashboard.translation.create') }}"
                            class="btn btn-primary">{{ trans('Add Translation') }}</a></h3>
                            @endcan
                        @can('dashboard.translation.storeMultiple')
                        <h3 class="card-title"><a data-bs-toggle="modal" data-bs-target="#addMultiTrans"
                            href="{{ route('dashboard.translation.storeMultiple') }}"
                            class="btn btn-primary">{{ trans('addMultiTrans') }}</a></h3>
                        @endcan
                    </div>

                </div>

            </div>
        </div>
        <div class="content-body">
            <!-- Basic Tables start -->
            <div class="row" id="basic-table">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">{{ $title }}</h4>
                        </div>
                        <div class="card-body">

                            <div class="table-responsive">
                                <table id="transTable" class="table table-hover text-nowrap text-center">
                                    <thead class="table-primary">
                                        <tr>
                                            <th>{{ trans('index') }}</th>
                                            <th>{{ trans('KEY') }}</th>
                                            @foreach ($prefixes as $prefix)
                                                <th>{{ ucfirst($prefix) }}</th>
                                            @endforeach
                                            @canany(['dashboard.translation.create', 'dashboard.translation.destroy'])
                                                <th>{{ trans('Action') }}</th>
                                            @endcanany
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($keys as $index => $key)
                                            <tr>
                                                <th>{{ $index }}</th>

                                                <td data-name="key">{{ $key }}</td>
                                                @foreach ($langs as $prefix => $lang)
                                                    @php
                                                        $lang[$key] = !isset($lang[$key]) ? '' : $lang[$key];
                                                        $lang[$key] = !is_string($lang[$key])
                                                            ? json_encode($lang[$key])
                                                            : $lang[$key];
                                                    @endphp
                                                    <td data-name="{{ $prefix }}">{{ $lang[$key] ?? '' }}</td>
                                                @endforeach
                                                @canany(['dashboard.translation.create', 'dashboard.translation.destroy'])
                                                    <td>
                                                        @can('dashboard.translation.create')
                                                            <a href="" class="btn btn-outline-success btn-editTrans"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#editTrans">{{ trans('Edit') }}</a>
                                                        @endcan
                                                        @can('dashboard.translation.destroy')
                                                            <a href="" class="btn btn-outline-danger btn-destroy-trans"
                                                                data-key="{{ $key }}">{{ trans('Delete') }}</a>
                                                        @endcan
                                                    </td>
                                                @endcanany

                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>
                                                <input type="text" class="form-control" placeholder="Search index" />
                                            </th>
                                            <th>
                                                <input type="text" class="form-control" placeholder="Search KEY" />
                                            </th>
                                            @foreach ($prefixes as $prefix)
                                                <th>
                                                    <input type="text" class="form-control"
                                                        placeholder="Search {{ ucfirst($prefix) }}" />
                                                </th>
                                            @endforeach
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                </table>

                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <!-- Basic Tables end -->


        </div>
    </div>

    <!-- END: Content-->

    <div class="sidenav-overlay"></div>
    <div class="drag-target"></div>



    <!-- Modal -->
    <div class="modal fade" id="addTrans" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ trans('Modal title') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('dashboard.translation.create') }}" method="POST" id="storeTransForm">
                        @csrf
                        <div class="mb-3">
                            <label for="" class="form-label">{{ trans('KEY') }}</label>
                            <input type="text" class="form-control" name="key" id="key"
                                aria-describedby="helpId" placeholder="Add new KEY">
                        </div>
                        @foreach ($prefixes as $prefix)
                            <div class="mb-3">
                                <label for="" class="form-label">{{ $prefix }}</label>
                                <input type="text" class="form-control" name="langs[{{ $prefix }}]"
                                    aria-describedby="helpId" placeholder="Add new {{ $prefix }}">
                            </div>
                        @endforeach
                    </form>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                        aria-label="Close">{{ trans('Close') }}</button>
                    <button type="button" class="btn btn-primary"
                        onclick="document.getElementById('storeTransForm').submit()">{{ trans('Save changes') }}</button>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal -->
    <div class="modal fade" id="addMultiTrans" tabindex="-1" aria-labelledby="multiTransModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl"> <!-- wider modal -->
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="multiTransModalLabel">{{ trans('Add Multiple Translations') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('dashboard.translation.storeMultiple') }}" method="POST"
                        id="multiTransForm">
                        @csrf
                        <table class="table table-bordered" id="translationTable">
                            <thead class="table-primary">
                                <tr>
                                    <th>{{ trans('Key') }}</th>
                                    @foreach ($prefixes as $prefix)
                                        <th>{{ strtoupper($prefix) }}</th>
                                    @endforeach
                                    <th>{{ trans('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody id="translationRows">
                                <tr>
                                    <td><input type="text" name="translations[0][key]" class="form-control"
                                            placeholder="Key"></td>
                                    @foreach ($prefixes as $prefix)
                                        <td>
                                            <input type="text" name="translations[0][langs][{{ $prefix }}]"
                                                class="form-control" placeholder="{{ $prefix }}">
                                        </td>
                                    @endforeach
                                    <td><button type="button" class="btn btn-danger btn-sm"
                                            onclick="removeRow(this)">X</button></td>
                                </tr>
                            </tbody>
                        </table>
                        <button type="button" class="btn btn-success btn-sm"
                            onclick="addTranslationRow()">{{ trans('Add Row') }}</button>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">{{ trans('Close') }}</button>
                    <button type="button" class="btn btn-primary"
                        onclick="document.getElementById('multiTransForm').submit()">
                        {{ trans('Save All') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="editTrans" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ trans('add new one') }}</h5>
                </div>
                <div class="modal-body">
                    <form action="{{ route('dashboard.translation.create') }}" method="POST" id="updateTransForm">
                        @csrf
                        <input type="text" class="form-control" name="key" id="key"
                            aria-describedby="helpId" hidden placeholder="Add new KEY">
                        @foreach ($prefixes as $prefix)
                            <div class="mb-3">
                                <label for="" class="form-label">{{ $prefix }}</label>
                                <input type="text" class="form-control" name="langs[{{ $prefix }}]"
                                    id="{{ $prefix }}" aria-describedby="helpId"
                                    placeholder="Add new {{ $prefix }}">
                            </div>
                        @endforeach
                    </form>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                        aria-label="Close">{{ trans('Close') }}</button>
                    <button type="button" class="btn btn-primary"
                        onclick="document.getElementById('updateTransForm').submit()">{{ trans('Save changes') }}</button>
                </div>
            </div>
        </div>
    </div>

    <form id="delete-trans-form" action="{{ route('dashboard.translation.destroy') }}" method="POST">
        @csrf
        @method('DELETE')
        <input type="hidden" name="key">
    </form>
@endsection

@push('css')
@endpush
@push('js')
    <script>
        $(document).ready(function() {

            $('#transTable').DataTable({
                "autoWidth": true,
                initComplete: function() {
                    this.api().columns().every(function() {
                        var that = this;
                        $('input', this.footer()).on('keyup change clear', function() {
                            if (that.search() !== this.value) {
                                that
                                    .search(this.value)
                                    .draw();
                            }
                        });
                    });
                }
            });
        });
        let rowIndex = 1;

        function addTranslationRow() {
            const prefixes = @json($prefixes);
            const tbody = document.getElementById('translationRows');

            let row = document.createElement('tr');
            let html =
                `<td><input type="text" name="translations[${rowIndex}][key]" class="form-control" placeholder="Key"></td>`;

            prefixes.forEach(prefix => {
                html +=
                    `<td><input type="text" name="translations[${rowIndex}][langs][${prefix}]" class="form-control" placeholder="${prefix}"></td>`;
            });

            html +=
                `<td><button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)">X</button></td>`;
            row.innerHTML = html;

            tbody.appendChild(row);
            rowIndex++;
        }

        function removeRow(button) {
            button.closest('tr').remove();
        }
    </script>
@endpush
