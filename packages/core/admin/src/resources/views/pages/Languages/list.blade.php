@extends('admin::layouts.dashboard')
@section('content')
    <!-- BEGIN: Content-->
    <div class="app-content content ">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper container-fluid p-0">
            <div class="content-header row">
                <div class="content-header-left col-md-9 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-12">
                            <h2 class="content-header-title float-start mb-0">{{ trans("Tables") }}</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">{{ trans("Home") }}</a>
                                    </li>
                                    <li class="breadcrumb-item active">{{ trans("languages") }}
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="content-header-right text-md-end col-md-3 col-12 d-md-block d-none">
                    @can('dashboard.languages.create')
                    <h3 class="card-title"><a href="{{ route('dashboard.languages.create') }}"
                            class="btn btn-primary">{{ trans('Add new')}}</a></h3>
                    @endcan
                </div>
            </div>
            <div class="content-body">
                <!-- Basic Tables start -->
                <div class="row" id="basic-table">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">{{ trans("languages") }}</h4>
                            </div>
                            <div class="card-body">
                                <p class="card-text">
                                    <form class="d-flex" method="GET"
                                        action="{{ route('dashboard.languages.index') }}">
                                        <div class="input-group input-group-sm" style="width: 150px;">
                                            <input type="text" name="search" class="form-control float-right"
                                                placeholder="Search" value="{{ request('search') ?? '' }}">

                                            <div class="input-group-append">
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="fas fa-search"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </p>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover text-nowrap text-center">
                                    <thead class="table-primary">
                                        <tr>
                                            <th>{{ trans("ID") }}</th>
                                            <th>{{ trans("prefix") }}</th>
                                            <th>{{ trans("name") }}</th>
                                            <th>{{ trans("native") }}</th>
                                            @can('dashboard.languages.toggle')
                                            <th>{{ trans("Active") }}</th>
                                            @endcan
                                            @canany(['dashboard.languages.edit', 'dashboard.languages.destroy'])
                                            <th>{{ trans("Action") }}</th>
                                            @endcanany


                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($langs as $lang)
                                            <tr>
                                                <td>{{ $lang->id }}</td>
                                                <td>{{ $lang->prefix }}</td>
                                                <td>{{ $lang->name }}</td>
                                                <td>{{ $lang->native }}</td>
                                                @can('dashboard.languages.toggle')
                                                <td>
                                                    <div class="form-check form-switch d-flex justify-content-center">
                                                        <input type="checkbox" class="form-check-input toggle-status" data-url="{{ route('dashboard.languages.toggle',$lang->id) }}"
                                                            data-id="{{ $lang->id }}"
                                                            @if ($lang->active) checked @endif>

                                                    </div>

                                                </td>                                                @endcan
                                                @canany(['dashboard.languages.edit', 'dashboard.languages.destroy'])
                                                <td>
                                                    @can('dashboard.languages.edit')
                                                    <a href="{{ route('dashboard.languages.edit', $lang->id) }}"
                                                        class="btn btn-outline-success">{{ trans("Edit") }}</a>
                                                    @endcan
                                                    @can('dashboard.languages.destroy')
                                                    <a href="{{ route('dashboard.languages.destroy', $lang->id) }}"
                                                        class="btn btn-outline-danger btn-destroy">{{ trans("Delete") }}</a>

                                                    @endcan
                                                </td>
                                                @endcanany
                                            </tr>
                                        @endforeach

                                    </tbody>
                                </table>
                                <div class=" d-flex justify-content-center">

                                    {{ $langs->appends($_GET)->links('pagination::bootstrap-4') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Basic Tables end -->


            </div>
        </div>
    </div>
    <!-- END: Content-->

    <div class="sidenav-overlay"></div>
    <div class="drag-target"></div>



  <!-- Modal -->
  <div class="modal fade" id="delete-recored" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="delete-recoredLabel">{{ trans('Remove recored')}}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <h5>{{ trans('Are you sure of deleteing this recored')}}</h5>
            <form action="" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">{{ trans("DElETE") }}</button>
            </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" aria-label="Close">{{ trans("Close") }}</button>
        </div>
      </div>
    </div>
  </div>

@endsection
