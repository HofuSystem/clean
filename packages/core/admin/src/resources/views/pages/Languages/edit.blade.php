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
                        <h1>{{ trans('Languages Tables')}}</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">{{ trans("Home") }}</a></li>
                            <li class="breadcrumb-item active">{{ trans("Languages") }}</li>
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
                            <h3 class="card-title">{{ trans('Edit Laguage')}}</h3>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->
                        <form method="POST" action="{{ route('dashboard.languages.edit', $lang->id) }}">
                            @csrf
                            @method("PUT")
                            <div class="card-body row">
                                <div class="form-group col-md-6">
                                    <label for="prefix">{{ trans("Prefix") }}</label>
                                    <input type="text" name="prefix"
                                        class="form-control @error('prefix') is-invalid @enderror " id="prefix"
                                        value="{{ old('prefix', $lang->prefix) }}" placeholder="Enter prefix">
                                    @error('prefix')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="name">{{ trans("name") }}</label>
                                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror "
                                        value="{{ old('name', $lang->name) }}" id="name" placeholder="Enter name">
                                    @error('name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="script">{{ trans("script") }}</label>
                                    <input type="text" name="script"
                                        class="form-control @error('script') is-invalid @enderror " id="script"
                                        value="{{ old('script', $lang->script) }}" placeholder="Enter script">
                                    @error('script')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="native">{{ trans("native") }}</label>
                                    <input type="text" name="native"
                                        class="form-control @error('native') is-invalid @enderror " id="native"
                                        value="{{ old('native', $lang->native) }}" placeholder="Enter native">
                                    @error('native')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="regional">{{ trans("regional") }}</label>
                                    <input type="text" name="regional"
                                        class="form-control @error('regional') is-invalid @enderror " id="regional"
                                        value="{{ old('regional', $lang->regional) }}" placeholder="Enter regional">
                                    @error('regional')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group col-md-6">
                                    <label>{{ trans("Direction") }}</label>
                                    <select name="dir" class="form-control select2 @error('dir') is-invalid @enderror "
                                        style="width: 100%">
                                        <option @if ($lang->dir == 'rtl') selected @endif value="rtl">{{ trans("RTL") }}</option>
                                        <option @if ($lang->dir == 'ltr') selected @endif value="ltr">{{ trans("LTR") }}</option>
                                    </select>
                                    @error('dir')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                @can('dashboard.languages.toggle')
                                <div class="form-group col-md-6 ">
                                    <label for="active">{{ trans('Active the language')}}</label>
                                    <div class="custom-control custom-switch ">
                                        <input type="checkbox" name="active"
                                            class="custom-control-input @error('active') is-invalid @enderror " id="active"
                                            @checked(old('active',$lang->active)) >
                                        <label class="custom-control-label" for="active"></label>
                                    </div>
                                    @error('active')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                @endcan

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
