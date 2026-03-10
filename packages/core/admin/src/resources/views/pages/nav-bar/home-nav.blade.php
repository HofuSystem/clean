@extends('admin::layouts.dashboard')

@section('content')
<div class="container mt-3">

    <form class="card card-primary card-outline " method="post" action="{{ route('dashboard.nav-bar.home-nav.save') }}"
        id="home-nav-form" data-layout="home-nav">

        <div class="card-header">
            <h3 class="card-title">{{ $title }}</h3>
        </div>
        <div class="card-body">
            <menu id="nestable-menu">
                <button class="btn btn-sm btn-primary" type="button" data-action="expand-all">Expand All</button>
                <button class="btn btn-sm btn-success" type="button" data-action="collapse-all">Collapse All</button>
            </menu>
            @csrf
            <div class="dd nestable">
                <ul class="dd-list">
                    @foreach ($navBar as $li)
                        @include('admin::pages.nav-bar.home-nav-edit-li', ['li' => $li])
                    @endforeach
                </ul>
            </div>
        </div>
        <!-- /.card-body -->
        <div class="card-footer">
            <button type="button" id="AddItemBtn" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                data-bs-target="#exampleModal">
                {{ __('Add item') }}
            </button>
            <button type="submit" class="btn btn-sm btn-success"><i class="fa fa-check"></i>
                {{ __('Save') }}</button>
        </div>
        <!-- /.card-footer-->
    </form>
</div>


    <!-- Modal -->
    <div class="modal fade" id="exampleModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ __('Add item') }}</h5>
                    <button type="button" class="close btn btn-danger" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">



                    <div class="form-group row mt-3">
                        <label for="slug" class="col-sm-2 col-form-label">{{ __('icon') }}</label>
                        <div class="col-sm-10">
                            <select name="icon" id="icon" class="icon">
                               
                            </select>
                        </div>
                    </div>
                  

                    <div class="form-group row mt-3">
                        <label for="page" class="col-sm-2 col-form-label">{{ __('page') }}</label>
                        <div class="col-sm-10">
                            <select name="page" id="page" class="select2">
                                @foreach (\Core\Admin\Models\Page::get() as $page)
                                    <option value="{{ $page->name }}">{{ $page->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>



                    <div class="form-group row mt-3">
                        <label for="slug" class="col-sm-2 col-form-label">{{ __('url') }}</label>
                        <div class="col-sm-10">
                            <input type="text" class=" form-control" id="url" name="url"
                                placeholder="{{ __('Enter Your url') }} ... " />
                        </div>
                    </div>

                    @foreach (\LaravelLocalization::getSupportedLocales() as $language)
                        <div class="form-group row mt-3">
                            <label for="title_{{ $language['prefix'] ?? '' }}"
                                class="col-sm-2 col-form-label">{{ __('title') }} [{{ $language['prefix'] }}]</label>
                            <div class="col-sm-10">
                                <input type="text" class=" form-control" id="title_{{ $language['prefix'] ?? '' }}"
                                    name="title_{{ $language['prefix'] ?? '' }}"
                                    placeholder="{{ __('Enter item title') }} ... " />
                            </div>
                        </div>
                    @endforeach

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <button type="button" id="saveHomeNavItem" data-bs-dismiss="modal"
                        class="btn btn-primary">{{ __('Save changes') }}</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('css')
    <link rel="stylesheet" href="{{ asset('control') }}/core-assets/nav-assets/style.css">
@endpush

@push('js')
    <script></script>
    <script src="{{ asset('control') }}/core-assets/nav-assets/script.js"></script>
@endpush
