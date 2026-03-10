@extends('admin::layouts.dashboard')
@section('content')
    <!--end::Header-->
    <!--begin::Content-->
    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        <!--begin::Toolbar-->
        <div class="toolbar my-3" id="kt_toolbar">
            <!--begin::Container-->
            <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
                <!--begin::Page title-->
                <div data-kt-swapper="true" data-kt-swapper-mode="prepend"
                    data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}"
                    class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
                    <!--begin::Title-->
                    <h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1"></h1>
                    <!--end::Title-->
                    <!--begin::Separator-->
                    <span class="h-20px border-gray-200 border-start mx-4"></span>
                    <!--end::Separator-->
                    <!--begin::Breadcrumb-->
                    <ul class="breadcrumb breadcrumb-separatorless fw-bold fs-7 my-1">
                        <!--begin::Item-->
                        <li class="breadcrumb-item text-muted">
                            <a href="" class="text-muted text-hover-primary">@lang('Home')</a>
                        </li>
                        <!--end::Item-->

                        <!--begin::Item-->
                        <li class="breadcrumb-item text-muted">@lang("users")</li>
                        <!--end::Item-->

                        <!--begin::Item-->
                        <li class="breadcrumb-item text-dark">{{ $title }}</li>
                        <!--end::Item-->
                    </ul>
                    <!--end::Breadcrumb-->
                </div>
                <!--end::Page title-->

            </div>
            <!--end::Container-->
        </div>
        <!--end::Toolbar-->
        <!--begin::Post-->
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <!--begin::Container-->
            <div id="kt_content_container" class="container-fluid">
                <!--begin::Card-->
                <div class="card">
                    <form class="form" method="POST" action="{{ route('dashboard.contracts.qr-codes.generate') }}">
                        @csrf
                        <div class="card-body row">
                            
                            <div class="form-group mb-3 col-md-12">
                                <label class="required" for="contract_id">{{ trans("Contract") }}</label>
                                <select class="custom-select form-select advance-select" name="contract_id" id="contract_id" required>
                                    <option value="">{{ trans("Select Contract") }}</option>
                                    @foreach($contracts ?? [] as $contract)
                                        <option @if($contract->id == request('contract_id')) selected @endif value="{{ $contract->id }}"> {{ trans('contract') }} : {{ $contract->title }} - {{ trans('client') }} : {{ $contract->client?->fullname }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <label class="required" for="has_titles">{{ trans("Does every QR code have its own title?") }}</label>
                                <select class="custom-select form-select" name="has_titles" id="has_titles" required>
                                    <option value="0">{{ trans("No") }}</option>
                                    <option value="1">{{ trans("Yes") }}</option>
                                </select>
                            </div>

                            <div class="form-group mb-3 col-md-12" id="count_field">
                                <label class="required" for="count">{{ trans("Number of QR Codes") }}</label>
                                <input type="number" name="count" id="count" class="form-control" 
                                    placeholder="{{ trans('Enter number of QR codes') }}" min="1" max="100" value="1" required>
                            </div>

                            <div class="form-group mb-3 col-md-12" id="titles_field" style="display: none;">
                                <label class="required">{{ trans("QR Code Titles") }}</label>
                                <div id="titles_container">
                                    <div class="input-group mb-2 title-input-group">
                                        <input type="text" name="titles[]" class="form-control" placeholder="{{ trans('Enter title') }}">
                                        <button type="button" class="btn btn-danger remove-title">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-success mt-2" id="add_title">
                                    <i class="fas fa-plus"></i> {{ trans("Add Another Title") }}
                                </button>
                            </div>

                        </div>
                        <div class="card-footer">
                            <div class="row">
                                <div class="col-lg-12">
                                    <a href="{{ route('dashboard.contracts.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left"></i> {{ trans("Back") }}
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-qrcode"></i> {{ trans("Generate QR Codes") }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <!--end::Card-->
            </div>
            <!--end::Container-->
        </div>
        <!--end::Post-->
    </div>
    <!--end::Content-->
@endsection
@push('css')
@endpush
@push('js')
    <script>
        $(document).ready(function() {
            // Toggle between count and titles fields
            function toggleFields() {
                if ($('#has_titles').val() == '1') {
                    // Show titles, hide count
                    $('#count_field').hide();
                    $('#titles_field').show();
                    $('#count').removeAttr('required').val('');
                    $('input[name="titles[]"]').attr('required', 'required');
                } else {
                    // Show count, hide titles
                    $('#count_field').show();
                    $('#titles_field').hide();
                    $('#count').attr('required', 'required');
                    if (!$('#count').val()) {
                        $('#count').val('1');
                    }
                    $('input[name="titles[]"]').removeAttr('required');
                }
            }
            
            // Initial state
            toggleFields();
            
            $('#has_titles').on('change', toggleFields);

            // Add new title input
            $('#add_title').on('click', function() {
                var isRequired = $('#has_titles').val() == '1';
                var newInput = `
                    <div class="input-group mb-2 title-input-group">
                        <input type="text" name="titles[]" class="form-control" placeholder="{{ trans('Enter title') }}" ${isRequired ? 'required' : ''}>
                        <button type="button" class="btn btn-danger remove-title">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                `;
                $('#titles_container').append(newInput);
            });

            // Remove title input
            $(document).on('click', '.remove-title', function() {
                if ($('.title-input-group').length > 1) {
                    $(this).closest('.title-input-group').remove();
                } else {
                    alert('{{ trans("At least one title is required") }}');
                }
            });

            // Form validation before submit
            $('form').on('submit', function(e) {
                var hasTitles = $('#has_titles').val();
                
                console.log('Form submitting with has_titles:', hasTitles);
                console.log('Count value:', $('#count').val());
                console.log('Titles:', $('input[name="titles[]"]').map(function() { return $(this).val(); }).get());
                
                // Validate based on selection
                if (hasTitles == '0') {
                    if (!$('#count').val() || $('#count').val() < 1) {
                        e.preventDefault();
                        alert('{{ trans("Please enter a valid number of QR codes") }}');
                        return false;
                    }
                } else if (hasTitles == '1') {
                    var titles = $('input[name="titles[]"]').filter(function() { 
                        return $(this).val().trim() !== ''; 
                    });
                    if (titles.length === 0) {
                        e.preventDefault();
                        alert('{{ trans("Please enter at least one title") }}');
                        return false;
                    }
                }
            });
        });
    </script>
@endpush

