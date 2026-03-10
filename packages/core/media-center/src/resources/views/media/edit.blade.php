@extends('admin::layouts.dashboard')
@push('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('control') }}/app-assets/vendors/css/forms/select/select2.min.css">
    <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css"
  />
@endpush

@section('content')
   <!-- BEGIN: Content-->
   <div class="app-content content ">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper container-fluidp-0">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">

                    @isset($media)
                    @lang('Edit Media')
                    <h2 class="content-header-title float-start mb-0">@lang('Edit Media')</h2>
                    @else
                    <h2 class="content-header-title float-start mb-0">@lang('Add Media')</h2>
                    @endisset
                </div>

                </div>
            </div>

        </div>
        <div class="content-body">
            <!-- Basic Tables start -->


            <div class="row" id="basic-table">
                <div class="col-12">
                    <div class="card">

                        <div class="card-body">
                            <div class="row">
                                <form method="POST"
                                      enctype="multipart/form-data"

                                      @isset($media)
                                          action="{{ route('dashboard.mediacenter.edit',$media->id) }}"
                                      @else
                                          @lang('Add Media')
                                          action="{{ route('dashboard.mediacenter.create') }}"
                                      @endisset
                                      enctype="multipart/form-data">
                                    @csrf
                                    @isset($media)
                                        @method('PUT')
                                    @endisset


                                    <div class="card-body row">

                                        @isset($media)
                                            @if(!empty($media->image_list))
                                                @foreach(json_decode($media->image_list) as $k => $img)
                                                    @if($k == 'thumbnail')
                                                        <img src="{{ url($media->url.'/'.$img) }}"
                                                             class="img-thumbnail img-responsive"
                                                             alt="{{ $media->alt }}" srcset="">
                                                    @endif
                                                @endforeach
                                            @endif
                                        @endif


                                        <div class="form-gorup mb-1 col-md-12">
                                            <label for="avatar">@lang('Image')</label>
                                            <div class="input-group">
                                                <div class="custom-file">
                                                    <input type="file" name="url"
                                                           class="custom-file-input @error('url') is-invalid @enderror"
                                                           id="url"
                                                           value="{{ old('url',$media->file_name ?? '') }}">
                                                    <label class="custom-file-label" for="url" >

                                                    </label>
                                                    @if ($errors->has('url'))
                                                        {{ $errors->first('url') }}
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-gorup mb-1 col-md-4">
                                            <label for="name">@lang('Title')</label>
                                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                                                   value="{{ old('title',$media->title ?? '') }}" id="title" placeholder="Enter Title">
                                            @if ($errors->has('title'))
                                                {{ $errors->first('title') }}
                                            @endif
                                        </div>

                                        <div class="form-gorup mb-1 col-md-4">
                                            <label for="alt">@lang('Alt')</label>
                                            <input type="text" name="alt" class="form-control @error('alt') is-invalid @enderror"
                                                   value="{{ old('alt' , $media->alt ?? '') }}"
                                                   id="alt" placeholder="Enter Alt">
                                            @if ($errors->has('alt'))
                                                {{ $errors->first('alt') }}
                                            @endif
                                        </div>


                                        <div class="form-gorup mb-1 col-md-4">
                                            <label for="role">@lang('Users')</label>
                                            <select class="form-control select2" name="user_id" id="users">
                                                @foreach ($users as $user)
                                                    <option @selected(isset($media->user_id)) value="{{ $user->id }}">{{  $user->name }}</option>
                                                @endforeach
                                            </select>

                                        </div>

                                        <!-- /.card-body -->
                                    </div>
                                    <div class="card-footer">
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                    </div>
                                </form>
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
@endsection
@push('js')
    <!-- BEGIN: Page Vendor JS-->
    <script src="{{ asset('control') }}/app-assets/vendors/js/forms/select/select2.full.min.js"></script>
    <!-- END: Page Vendor JS-->
    <script>
        $(document).ready(function() {
            $('.select2').select2();
        });
    </script>
@endpush
