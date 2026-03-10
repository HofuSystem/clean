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
                            @isset($entity)
                            <h2 class="content-header-title float-start mb-0">@lang('Edit entity')</h2>
                            @else
                            <h2 class="content-header-title float-start mb-0">@lang('Add entity')</h2>
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

                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#entity">
                    @lang('Save the Entity')
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
                            <h4 class="card-title">@lang('Entity json Schema')</h4>
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
  <div class="modal fade" id="entity" tabindex="-1" role="dialog" aria-labelledby="entityLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="entityLabel">@lang('Save entity')</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form id="submit-entity"
          @isset($entity)
          action="{{ route('dashboard.entities.edit',$entity->id) }}"
          @else
          action="{{ route('dashboard.entities.create') }}"
          @endisset
          method="post">
          @isset($entity)
          <input type="hidden" name="_method" value="PUT">
          <input type="hidden" name="_token" value="{{ csrf_token() }}">
            @endisset
            <div class="mb-3">
              <label for="Slug" class="form-label">Slug</label>
              <input type="text" class="form-control" value="{{ $entity->slug ?? "" }}" name="slug" id="slug" aria-describedby="helpId" placeholder="">
            </div>
            <div class="mb-3">
              <label for="type" class="form-label">Type of Entity</label>
              <select class="form-control" name="type" id="type">
                <option @selected(isset($entity) and $entity->type=="resource") value="resource">Resource</option>
                <option @selected(isset($entity) and $entity->type=="form")  value="form">Form</option>
              </select>
            </div>
            @foreach (LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                 <div class="mb-3">
                    <label for="name" class="form-label"><i class="flag-icon flag-icon-{{ $properties['flag'] }}"></i> {{ $properties['native'] }} @lang('name') </label>
                    <input type="text" class="form-control" name="name[{{ $localeCode }}]" aria-describedby="helpId" value="{{ $entity->names[$localeCode] ?? "" }}" placeholder="">
                  </div>
            @endforeach
            <input id="entity" name="entity" type="text" hidden>
            <button id="confirm-entity" type="submit" class="btn btn-primary">Save changes</button>

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
<script type="module">

let Buildereditor = new JsonEditor('#builder-json #json');
let Formeditor = new JsonEditor('#form-json #json');

Formio.builder(document.getElementById('builder'), {!! $entity->entity ?? '{}' !!}).then((builder) => {

   builder.on('change', function (components) {
    $("#submit-entity #entity").val(JSON.stringify(builder.schema));
      Buildereditor.load(builder.schema);
      Formio.createForm(document.getElementById('form'), builder.schema).then((form) => {
        form.on('change',function(submission){
            Formeditor.load(submission.data);
        });
      });;

   });

   builder.emit('change', builder.component)
});

$('#submit-entity').submit(function (e) {
    e.preventDefault();
    let url = $(this).attr('action');
    const formData = new FormData($('#submit-entity')[0]);
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
            // setTimeout(() => {
            //     location.reload();
            // }, 1000);
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
