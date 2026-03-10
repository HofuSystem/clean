@extends('admin::layouts.dashboard-web-view-layout')
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
                            <h2 class="content-header-title float-start mb-2">{{ $title }}</h2>
                            <div class="breadcrumb-wrapper">

                            </div>
                        </div>
                    </div>
                </div>
                <div class="content-header-right text-md-end col-md-3 col-12 d-md-block d-none">

                </div>
            </div>
            <div class="content-body">
                <!-- Basic Tables start -->
                <div class="row" id="basic-table">
                    <div class="col-12">
                        <div class="card">

                            <div class="card-body">
                                <div id="general-form"></div>

                            </div>
                        </div>
                    </div>
                </div>
                <!-- Basic Tables end -->
                @if (isset($commets_status) and $commets_status)
                    <!-- Blog Comment -->
                    <div class="col-12 mt-1" id="blogComment">
                        <h6 class="section-label mt-25">@lang('Comments')</h6>
                        @foreach ($comments as $comment)
                            <div class="card">
                                <div class="card-body">
                                    <div class="media">
                                        <div class="avatar mr-75">
                                            <img src="{{ $comment->user->avatarUrl }}" width="38" height="38"
                                                alt="Avatar" />
                                        </div>
                                        <div class="media-body">
                                            <h6 class="font-weight-bolder mb-25">{{ $comment->user->name }}</h6>
                                            <p class="card-text">
                                                {{ \Carbon\Carbon::parse($comment->created_at)->diffForHumans() }}</p>
                                            <p class="card-text">
                                                {{ $comment->comment }}
                                            </p>
                                            <a data-toggle="collapse" href="#Commet-{{ $comment->id }}" role="button"
                                                aria-expanded="false" aria-controls="Commet-{{ $comment->id }}">
                                                <div class="d-inline-flex align-items-center">
                                                    <i data-feather="corner-up-left" class="font-medium-3 mr-50"></i>
                                                    <span>Reply</span>
                                                </div>
                                            </a>
                                            <p>

                                            </p>
                                            <div class="collapse" id="Commet-{{ $comment->id }}">
                                                <!-- Leave a Blog Comment -->
                                                <div class="col-12 mt-1">
                                                    <h6 class="section-label mt-25">Leave a Comment</h6>
                                                    <div class="card">
                                                        <div class="card-body">
                                                            <form action="{{ route('dashboard.comments.create') }}"
                                                                method="POST" class="form">
                                                                @csrf
                                                                <div class="row">
                                                                    <input type="hidden" class="form-control"
                                                                        name="parent_id" value="{{ $comment->id }}">
                                                                    <input type="hidden" class="form-control"
                                                                        name="post_type" value="{{ $post_type }}">
                                                                    <input type="hidden" class="form-control"
                                                                        name="post_id" value="{{ $id }}">
                                                                    <div class="col-12">
                                                                        <textarea class="form-control mb-2" rows="4" name="comment" placeholder="Comment"></textarea>
                                                                    </div>

                                                                    <div class="col-12">
                                                                        <button type="submit" class="btn btn-primary">
                                                                            @lang('Comment')</button>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--/ Leave a Blog Comment -->
                                            </div>
                                            @foreach ($comment->replays ?? [] as $replay)
                                                <div class="card">
                                                    <div class="card-body">
                                                        <div class="media">
                                                            <div class="avatar mr-75">
                                                                <img src="{{ $replay->user->avatarUrl }}" width="38"
                                                                    height="38" alt="Avatar" />
                                                            </div>
                                                            <div class="media-body">
                                                                <h6 class="font-weight-bolder mb-25">
                                                                    {{ $replay->user->name }}</h6>
                                                                <p class="card-text">
                                                                    {{ \Carbon\Carbon::parse($replay->created_at)->diffForHumans() }}
                                                                </p>
                                                                <p class="card-text">
                                                                    {{ $replay->comment }}
                                                                </p>

                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        <div class="card">
                            <div class="card-body">
                                <form action="{{ route('dashboard.comments.create') }}"
                                    method="POST" class="form">
                                    @csrf
                                    <div class="row">
                                        <input type="hidden" class="form-control"
                                            name="post_type" value="{{ $post_type }}">
                                        <input type="hidden" class="form-control"
                                            name="post_id" value="{{ $id }}">
                                        <div class="col-12">
                                            <textarea class="form-control mb-2" rows="4" name="comment" placeholder="Comment"></textarea>
                                        </div>

                                        <div class="col-12">
                                            <button type="submit" class="btn btn-primary">
                                                @lang('Comment')</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <!-- END: Content-->

    <div class="sidenav-overlay"></div>
    <div class="drag-target"></div>



@endsection
@push('css')
    <link href="{{ asset('control') }}/plugins/formio/formio.full.min.css" rel="stylesheet">
@endpush

@push('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.js"></script>


    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css"
        integrity="sha384-zCbKRCUGaJDkqS1kPbPd7TveP5iyJE0EjAuZQTgFLD2ylzuqKfdKlfG/eSrtxUkn" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-fQybjgWLrvvRgtW6bFlB7jaZrFsaBXjsOMm/tB9LTS58ONXgqbR9W8oWht/amnpF" crossorigin="anonymous">
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css"
        integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!--sortablejs-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-sortablejs@latest/jquery-sortable.js"></script>

    <script src="https://unpkg.com/filepond/dist/filepond.min.js"></script>
    <script src="https://unpkg.com/jquery-filepond/filepond.jquery.js"></script>
    <link href="https://unpkg.com/filepond/dist/filepond.min.css" rel="stylesheet">


    <script src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-size/dist/filepond-plugin-file-validate-size.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-validate-size/dist/filepond-plugin-image-validate-size.js">
    </script>

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
        Formio.createForm(document.getElementById('general-form'), {
            "components": {!! json_encode($fields) !!}
        }).then(function(form) {
            $("select").trigger("click");
            $("select").trigger("click");
            form.on('submit', function(submission) {
                window.onbeforeunload = null;
                $(".alert").remove();
                $($('[type=submit]')).removeClass('btn-danger btn-success');
                let formData = submission.data;
                formData['_token'] = "{{ csrf_token() }}";
                $.ajax({
                    type: "POST",
                    url: "{{ $url }}",
                    data: formData,
                    dataType: "json",
                    success: function(response) {
                        if (response.status) {
                            toastr.success(response.message);
                            if (response.next)
                                window.location = (response.next)
                        } else {
                            toastr.error(response.message)
                            $($('[type=submit]')).addClass('btn-danger');
                            $.each(response.errors, function(key, value) {
                                $.each(value, function(index, error) {
                                    toastr.error(error, key)
                                });
                            });
                        }
                        form.emit('submitDone', submission)
                    },
                    error: function(xhr, status, error) {
                        toastr.error(error);
                        $($('[type=submit]')).addClass('btn-danger');
                        form.emit('submitDone', submission)

                    }
                });
            });
            form.on('change', function(f) {
                var warning = true;
                window.onbeforeunload = function() {
                    if (warning) {
                        return "You have made changes on this page that you have not yet confirmed. If you navigate away from this page you will lose your unsaved changes";
                    }
                }

            });

        });

        @if (isset($edit_mode) and $edit_mode)
            function updateActicity() {
                $.ajax({
                    type: "POST",
                    url: "{{ route('dashboard.actvity') }}",
                    data: {
                        '_token': "{{ csrf_token() }}",
                        'url': '{{ url()->current() }}'
                    },
                    dataType: "json",
                    success: function(response) {

                    },
                    error: function(xhr, status, error) {
                        toastr.error(error);
                    }
                });
            };
            setInterval(function() {
                updateActicity();
            }, 20000);
            updateActicity();
        @endif
    </script>
@endpush
