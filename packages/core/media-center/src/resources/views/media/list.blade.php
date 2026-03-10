@extends('admin::layouts.dashboard')
@section('content')

    <!-- BEGIN: Content-->
    <div class="app-content content ">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper container-fluidp-0">
            <div class="content-header row">
                <div class="content-header-left col-md-9 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-12">
                            <h2 class="content-header-title float-left mb-0">@lang('Tables')</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('dashboard.index') }}">@lang('Home')
                                        </a>
                                    </li>
                                    <li class="breadcrumb-item active">{{ trans("Media") }}
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="content-header-right text-md-right col-md-3 col-12 d-md-block d-none">
                    <div class="form-group breadcrumb-right">

                        <div class="row">
                            <div class="col-md-6">
                                @can('dashboard.users.create')
                                    <h3 class="card-title">
                                        <a href="{{ route('dashboard.mediacenter.create') }}"
                                           class="btn btn-primary">Add new</a></h3>
                                @endcan
                            </div>


                            <div class="col-md-6">
                                @can('dashboard.users.create')
                                    <h3 class="card-title">
                                        <a href="{{ route('dashboard.mediacenter.upload_multi') }}"
                                           class="btn btn-primary">Add Mutli</a>
                                    </h3>
                                @endcan
                            </div>
                        </div>


                    </div>
                </div>
            </div>
            <div class="content-body">
                <!-- Basic Tables start -->
                <div class="card p-2">
                    <form method="GET" action="{{ route('dashboard.mediacenter.index') }}">
                        <div class="row justify-content-center">
                            <div class="form-group col-md">
                                <label for="exampleInputEmail1">Search</label>
                                <input value="{{ old('search') }}" type="text" name="search" class="form-control" placeholder="Search...." value="">
                            </div>
                            <div class="form-group col-md-2">
                                <button type="submit" class="btn btn-primary col-12 waves-effect waves-float waves-light"><i class="fa-solid fa-magnifying-glass"></i></button>
                            </div>

                        </div>
                    </form>
                </div>
                <div class="row" id="basic-table">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">{{trans("Media") }}</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                    </div>

                                    <div class="col-md-2 mt-1">
                                    </div>

                                </div>
                            </div>
                            <div class="table-responsive bg-white">
                                <table class="table table-hover text-nowrap text-center">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>File</th>
                                            <th>Title</th>
                                            <th>Alt</th>
                                            <th>Size</th>
                                            <th>User</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($medias as $i)
                                            <tr>
                                                <td>{{ $i->id }}</td>
                                                <td>
                                                    <img width='200' src="{{ url(str_contains($i->file_type, "image") ? $i->url.$i->getRawOriginal('file_name') : "control/icons/".explode('/', $i->file_type)[1]) }}" class="img-thumbnail img-responsive" alt="{{ $i->alt }}" srcset="">
                                                </td>
                                                <td>{{ $i->title }}</td>
                                                <td>{{ $i->alt }}</td>
                                                <td>
                                                    {{ $i->size }}
                                                </td>
                                                <td>
                                                    @isset($i->user)
                                                    {{ $i->user->name }}
                                                    @endisset
                                                </td>

                                                @can('dashboard.mediacenter.toggle')
                                                <td>
                                                    <div class="custom-control custom-switch d-flex justify-content-center">
                                                        <input type="checkbox" class="custom-control-input toggle-status" data-url="{{ route('dashboard.users.toggle',$user->id) }}"
                                                            data-id="{{ $i->id }}"
                                                            @if ($i->id) checked @endif
                                                            id="customSwitch{{ $user->id }}">
                                                            <label class="custom-control-label"
                                                                   for="customSwitch{{ $user->id }}"></label>

                                                    </div>

                                                </td>
                                                @endcan
                                                    @canany(['dashboard.mediacenter.edit','dashboard.mediacenter.destroy'])
                                                    <td>
                                                        @can('dashboard.mediacenter.edit')
                                                        <a href="{{ route('dashboard.mediacenter.edit', $i->id) }}"
                                                            class="btn btn-outline-success">Edit</a>
                                                        @endcan
                                                        @can('dashboard.mediacenter.destroy')
                                                            <a href="{{ route('dashboard.mediacenter.destroy', $i->id) }}"
                                                               class="btn btn-outline-danger btn-destroy">Delete</a>
                                                        @endcan
                                                    </td>
                                                    @endcan

                                            </tr>
                                        @endforeach

                                    </tbody>
                                </table>
                                <div class=" d-flex justify-content-center">

                                    {{ $medias->appends($_GET)->links('pagination::bootstrap-4') }}
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

  <!-- Modal -->
  <div class="modal fade" id="delete-recored" tabindex="-1"
       aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="delete-recoredLabel">Remove recored</h5>
            <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <h5>Are you sure of deleteing this recored</h5>
            <form action="" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">DElETE</button>
            </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal" aria-label="Close">Close</button>
        </div>
      </div>
    </div>
  </div>

@endsection
