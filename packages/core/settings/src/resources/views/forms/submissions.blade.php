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
                            <h2 class="content-header-title float-start mb-0">@lang('Forms')</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">@lang('Home')</a>
                                    </li>
                                    <li class="breadcrumb-item active">{{ $form->name }}
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="content-header-right text-md-end col-md-3 col-12 d-md-block d-none">
                    <div class="row">
                        <h3 class="card-title">
                             <a href="{{ route('dashboard.forms.export',['slug'=>$form->slug ,'type'=>'xlsx']) }}"
                                class="btn btn-success">@lang('Export xlsx')</a>
                                <a href="{{ route('dashboard.forms.export',['slug'=>$form->slug ,'type'=>'csv']) }}"
                                    class="btn btn-primary">@lang('Export CSV')</a>
                            </h3>
                    </div>

                </div>
            </div>
            <div class="content-body">
                <!-- Basic Tables start -->
                <div class="row" id="basic-table">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">{{ $form->name }}</h4>
                            </div>
                            <div class="card-body">
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover text-nowrap text-center">
                                        <thead>
                                            <tr>
                                               @foreach ($form->fields as $item)
                                                   @if ($item->key == 'submit')
                                                       @continue
                                                   @endif
                                                   <th>{{ "$item->label ($item->key)" }}</th>
                                                @endforeach
                                                   <th>Submition Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($form->submissions as $submission)
                                            <tr>
                                                @foreach ($form->fields as $item)
                                                    @if ($item->key == 'submit')
                                                        @continue
                                                    @endif
                                                    <td>{{ $submission->submission[$item->key] ?? "" }}</td>
                                                 @endforeach
                                                    <td>{{ $submission->created_at }}</td>
                                             </tr>
                                            @endforeach
                                        </tbody>
                                    </table>

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
