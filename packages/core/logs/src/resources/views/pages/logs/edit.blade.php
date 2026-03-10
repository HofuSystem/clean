@extends('admin::layouts.dashboard')
@push('css')
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('control') }}/plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="{{ asset('control') }}plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
@endpush

@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>{{ trans('logs Tables')}}</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">{{ trans("Home") }}</a></li>
                            <li class="breadcrumb-item active">{{ trans("logs") }}</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">

                <div class="row">

                    <!-- general form elements -->
                    <div class="card card-primary col-12">
                        <div class="card-header">
                            <h3 class="card-title">{{ trans('Edit logs')}}</h3>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->
                        <form method="POST"
                        @isset($log)
                        action="{{ route('dashboard.logs-sys.edit', $log->id) }}">
                        @method("PUT")
                        @else
                        action="{{ route('dashboard.logs-sys.create') }}">
                        @endisset 
                        @csrf
                            <div class="card-body row">
                                <div class="form-group col-md-12">
                                    <label for="prefix">{{ trans("type") }}</label>
                                    <input type="text" name="type"
                                        class="form-control @error('type') is-invalid @enderror " id="type"
                                        value="{{ old('type', $log->type ?? "") }}" placeholder="Enter type">
                                    @error('type')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="data">{{ trans("data") }}</label>
                                    <textarea name="data"   class="form-control @error('data') is-invalid @enderror " id="data" cols="30" rows="10">{{ old('data', $log->data ?? "") }}</textarea>
                                 
                                    @error('data')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                          

                                <!-- /.card-body -->
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">{{ trans('save') }}</button>
                            </div>
                        </form>
                    </div>
                    <!-- /.card -->

                </div>
            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>
@endsection
@push('js')
    <!-- Select2 -->
    <script src="{{ asset('control') }}/plugins/select2/js/select2.full.min.js"></script>
    <script>
        $(function() {
            //Initialize Select2 Elements
            $('.select2').select2()
        })
    </script>
@endpush
