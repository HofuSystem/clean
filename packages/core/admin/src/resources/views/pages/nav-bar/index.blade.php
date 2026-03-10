@extends('admin::layouts.dashboard')
@section('content')

    <!-- BEGIN: Content-->
    <div class="app-content content ">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper container-fluid p-0">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <div class="post d-flex flex-column-fluid" id="kt_post">
                    <!--begin::Container-->
                    <div id="kt_content_container" class="container-fluid">
                     <div class="container mt-3">
             
                         <form class="card card-primary card-outline " method="post" action="{{ route('dashboard.nav-bar.nav.save',$slug) }}"
                             id="admin-nav-form" data-layout="admin-nav">
                     
                             <div class="card-header">
                                 <h3 class="card-title">{{ $title }}</h3>
                             </div>
                             <div class="card-body">
                                 <menu id="nestable-menu">
                                     <button class="btn btn-sm btn-primary" type="button" data-action="expand-all">{{ trans('Expand All')}}</button>
                                     <button class="btn btn-sm btn-success" type="button" data-action="collapse-all">{{ trans('Collapse All')}}</button>
                                 </menu>
                                 @csrf
                                 <div class="dd nestable">
                                     <ul class="dd-list">
                                         @foreach ($navBar as $li)
                                            @include('admin::pages.nav-bar.nav-edit-li', ['li' => $li])
                                         @endforeach
                                     </ul>
                                 </div>
                             </div>
                             <div class="row icons-list">
                                
                             </div>
                             <!-- /.card-body -->
                             <div class="card-footer">
                                 <button type="button" id="AddItemBtn" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                     data-bs-target="#navModal">
                                     {{ trans('Add item') }}
                                 </button>
                                 <button type="submit" class="btn btn-sm btn-success"><i class="fa fa-check"></i>
                                     {{ trans('Save') }}</button>
                             </div>
                             <!-- /.card-footer-->
                         </form>
                     </div>
                     
                     
                     <!-- Modal -->
                     <div class="modal fade" id="navModal" role="dialog" aria-labelledby="navModalLabel" aria-hidden="true">
                         <div class="modal-dialog" role="document">
                             <div class="modal-content">
                                 <div class="modal-header">
                                     <h5 class="modal-title" id="navModalLabel">{{ trans('Add item') }}</h5>
                                     <button type="button" class="close btn btn-danger" data-bs-dismiss="modal" aria-label="Close">
                                         <span aria-hidden="true">&times;</span>
                                     </button>
                                 </div>
                                 <div class="modal-body">
                 
             
                                     <div class="form-group row mt-1">
                                         <label for="slug" class="col-sm-2 col-form-label">{{ trans('type') }}</label>
                                         <div class="col-sm-10">
                                             <select name="type" id="type" class="type">
                                                <option value="li" selected>{{ trans("li") }}</option>
                                                <option value="title" >{{ trans("title") }}</option>

                                             </select>
                                         </div>
                                     </div>
                                    
                                     <div class="form-group row mt-1">
                                         <label for="slug" class="col-sm-2 col-form-label">{{ trans('icon') }}</label>
                                         <div class="col-sm-10">
                                             <select name="icon" id="icon" class="icon">
                                                                                                 <option value="">{{ trans("select") }}</option>

                                             </select>
                                         </div>
                                     </div>
                                    
                                     <div class="form-group row mt-3">
                                         <label for="slug" class="col-sm-2 col-form-label">{{ trans('route') }}</label>
                                         <div class="col-sm-10">
                                             <select name="route" id="route" class="route">
                                                 <option value="">{{ trans("select") }}</option>
                                                 @foreach ($routes as $key => $value) 
                                                     <option value="{{ $key }}">{{ $key }}</option>
                                                 @endforeach
                                             </select>
                                         </div>
                                         <div class="route-group col-12">

                                         </div>
                                     </div>
                                     <div class="form-group row mt-3">
                                         <label for="permission" class="col-sm-2 col-form-label">{{ trans('permission') }}</label>
                                         <div class="col-sm-10">
                                             <select name="permission" id="permission" class="permission">
                                                <option value="">{{ trans("select") }}</option>

                                                 @foreach ($permissions as $value) 
                                                     <option value="{{ $value }}">{{ $value }}</option>
                                                 @endforeach
                                             </select>
                                         </div>
                                     </div>

                                     <div class="form-group row mt-3">
                                         <label for="slug" class="col-sm-2 col-form-label">{{ trans('url') }}</label>
                                         <div class="col-sm-10">
                                             <input type="text" class=" form-control" id="url" name="url"
                                                 placeholder="{{ trans('Enter Your url') }} ... " />
                                         </div>
                                     </div>
                                     @foreach (config('app.activeLangs') as $language)
                                         <div class="form-group row mt-3">
                                             <label for="title_{{ $language['prefix'] ?? "" }}"
                                                 class="col-sm-2 col-form-label">{{ trans('title') }} [{{ $language['prefix'] }}]</label>
                                             <div class="col-sm-10">
                                                 <input type="text" class=" form-control" id="title_{{ $language['prefix'] ?? "" }}"
                                                     name="title_{{ $language['prefix'] ?? "" }}"
                                                     placeholder="{{ trans('Enter item title') }} ... " />
                                             </div>
                                         </div>
                                     @endforeach
                 
                                 </div>
                                 <div class="modal-footer">
                                     <button type="button" class="btn btn-secondary"
                                         data-bs-dismiss="modal">{{ trans('Close') }}</button>
                                     <button type="button" id="saveAdminNavItem" data-bs-dismiss="modal"
                                         class="btn btn-primary">{{ trans('Save changes') }}</button>
                                 </div>
                             </div>
                         </div>
                     </div>
                    </div>
                    <!--end::Container-->
                 </div>
            </div>
        </div>
    </div>
    <!-- END: Content-->
    <!-- Button trigger modal -->
@endsection
@push('css')
<link rel="stylesheet" href="{{ asset('control') }}/assets/vendor/libs/select2/select2.css" />
<link rel="stylesheet" href="{{ asset('custom') }}plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
    <link rel="stylesheet" href="{{ asset('custom') }}/nav-assets/style.css">
    <link rel="stylesheet" href="{{ asset('custom') }}/nav-assets/library/style.css">
@endpush

@push('js')
    <script>
        var languages   = {!! json_encode(config('app.activeLangs')) !!}
        var language    = "{!! config('app.locale') !!}"
        var routes      = {!! json_encode($routes) !!}
    </script>
    <script src="{{ asset('control') }}/assets/vendor/libs/select2/select2.js"></script>
    <script src="{{ asset('custom') }}/nav-assets/script.js"></script>
    <script src="{{ asset('custom') }}/nav-assets/library/script.js"></script>
@endpush
