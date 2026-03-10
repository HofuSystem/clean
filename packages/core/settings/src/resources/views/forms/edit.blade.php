@extends('admin::layouts.dashboard')

@section("styles")

<link rel="stylesheet" href="https://unpkg.com/formiojs@3.0.0-rc.20/dist/formio.full.min.css">

@endsection

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
                            @isset($form)
                            <h2 class="content-header-title float-start mb-0">@lang('Edit form')</h2>
                            @else
                            <h2 class="content-header-title float-start mb-0">@lang('Add form')</h2>
                            @endisset
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div class="content-body">
        <!-- Button trigger modal -->
        <div class="row mb-2">
            <div class="col-2">

                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#form-model">
                    @lang('Save the form')
                </button>
            </div>
        </div>


            <div class="row row-match" id="basic-table">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body">
                            <div id="builder">

                            </div>
                         </div>
                        </div>
                    </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">@lang('form json Schema')</h4>
                            <div id="builder-json">
                                <pre class="json-viewer" id="json"></pre>
                            </div>
                         </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="row row-match" id="basic-table">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body">
                            <div id="form">

                            </div>
                         </div>
                        </div>
                    </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">@lang('form data exmaple')</h4>
                            <div id="form-json">
                                <pre class="json-viewer" id="json"></pre>
                            </div>
                         </div>
                        </div>
                    </div>
                </div>

            </div>


        </div>
    </div>
</div>
<!-- END: Content-->

<div class="sidenav-overlay"></div>
<div class="drag-target"></div>

  <!-- Modal -->
  <div class="modal fade" id="form-model" tabindex="-1" role="dialog" aria-labelledby="formLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="formLabel">@lang('Save form')</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form id="submit-form"
          @isset($form)
          action="{{ route('dashboard.forms.edit',$form->id) }}"
          @else
          action="{{ route('dashboard.forms.create') }}"
          @endisset
          method="post">
          @isset($form)
          <input type="hidden" name="_method" value="PUT">
          <input type="hidden" name="_token" value="{{ csrf_token() }}">
            @endisset
          

            @foreach (LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                 <div class="mb-3">
                    <label for="name" class="form-label"><i class="flag-icon flag-icon-{{ $properties['flag'] }}"></i> {{ $properties['native'] }} @lang('name') </label>
                    <input type="text" class="form-control" name="name[{{ $localeCode }}]" aria-describedby="helpId" value="{{ $form->names[$localeCode] ?? "" }}" placeholder="">
                  </div>
            @endforeach
            <input id="form" name="form" type="text" hidden>
            <div class="mb-3">
                <div  class="custom-control custom-switch">
                    <input  @checked($form->auth ?? false) name="auth" type="checkbox" class="custom-control-input" id="authButtoon">
                    <label  class="custom-control-label" for="authButtoon">Auth Requierd</label>
                </div>
            </div>
            <div class="mb-3 roles">
                <label for="name" class="form-label">@lang('chooes a role')</label>
                <select name="roles[]" class="custom-select select2" multiple>
                    @foreach (\Spatie\Permission\Models\Role::get() as $item)
                        <option @selected(in_array($item->name,$form->roles ?? [])) value="{{ $item->name }}">{{ $item->name }}</option>
                    @endforeach
                </select>
            </div>
            <button id="confirm-form" type="submit" class="btn btn-primary">Save changes</button>

            </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
@endsection


@push('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('control') }}/app-assets/vendors/css/forms/select/select2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link href="{{ asset('control') }}/plugins/formio/formio.full.min.css" rel="stylesheet">
    <style>
        .json-viewer{
                overflow:scroll;
                max-height: 400px;
        }
        .form-builder-panel{
            margin-bottom: 5px;
        }
    </style>
@endpush

@push('js')
<script src="{{ asset('control') }}/app-assets/vendors/js/forms/select/select2.full.min.js"></script>
<script src="{{ asset('control') }}/app-assets/vendors/js/forms/select/select2.full.min.js"></script>
<!-- END: Page Vendor JS-->
<script>
    $(document).ready(function() {
        $('.select2').select2();
        $('#authButtoon').change();
    });
    $('#authButtoon').change(function (e) {
        e.preventDefault();
        if($(this).is(':checked')){
            $('.mb-3.roles').show(500);
        }else{
            $('.mb-3.roles').hide(500);
        }
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
<script src="{{ asset('control') }}/plugins/jsonviewer/dist/jquery.json-editor.min.js"></script>



<!--sortablejs-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-sortablejs@latest/jquery-sortable.js"></script>

<script src="https://unpkg.com/filepond/dist/filepond.min.js"></script>
<script src="https://unpkg.com/jquery-filepond/filepond.jquery.js"></script>
<link href="https://unpkg.com/filepond/dist/filepond.min.css" rel="stylesheet">


<script src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.js"></script>
<script src="https://unpkg.com/filepond-plugin-file-validate-size/dist/filepond-plugin-file-validate-size.js"></script>
<script src="https://unpkg.com/filepond-plugin-image-validate-size/dist/filepond-plugin-image-validate-size.js"></script>

<script>
    // Register the plugin
        //FilePond.registerPlugin(FilePondPluginImagePreview);
        FilePond.registerPlugin(FilePondPluginFileValidateType);
        FilePond.registerPlugin(FilePondPluginFileValidateSize);
        FilePond.registerPlugin(FilePondPluginImageValidateSize);




        var mediaCenterApis = {
            get :   '{{ route('mediacenterapi.index') }}',
            add :   '{{ route('mediacenterapi.create') }}',
            delete : '{{ route('mediacenterapi.destroy') }}',
            edit :"{{ route('dashboard.mediacenter.edit', '%s') }}",
        };
  </script>

<script type="module" crossorigin src="{{ asset('control') }}/plugins/formio/formio.full.js"></script>
<script type="module" src="{{ asset("control/plugins/formio/components.js") }}"></script>
<script type="module">

let Buildereditor = new JsonEditor('#builder-json #json');
let Formeditor = new JsonEditor('#form-json #json');

Formio.builder(document.getElementById('builder'), {!! $form->form ?? '{}' !!},
{
    builder: {
        custom: {
            title: 'Rightmind',
            weight: 999,
            components: {
                youtube_list: true,
                image: true,
            }
        }
    }

}).then((builder) => {

   builder.on('change', function (components) {
    $("#submit-form #form").val(JSON.stringify(builder.schema));
      Buildereditor.load(builder.schema);
      Formio.createForm(document.getElementById('form'), builder.schema).then((form) => {
        form.on('change',function(submission){
            Formeditor.load(submission.data);
        });
      });;

   });

   builder.emit('change', builder.component)
});

$('#submit-form').submit(function (e) {
    e.preventDefault();
    let url = $(this).attr('action');
    const formData = new FormData($('#submit-form')[0]);
    $.ajax({
    type: "POST",
    url: url,
    enctype :'multipart/form-data',
    dataType: "JSON",
    data: formData,
    contentType: false,
    processData: false,
    success: function (response) {
        if(response.status){
            toastr.success(response.message)
            setTimeout(() => {
                location.reload();
            }, 1000);
        }else{
            toastr.error(response.message)
            $.each(response.errors, function (key, messages) {
                $.each(messages, function (index , message) {
                    toastr.error(message,key);
                });
            });
        }
    }

    });
});

</script>

@endpush
