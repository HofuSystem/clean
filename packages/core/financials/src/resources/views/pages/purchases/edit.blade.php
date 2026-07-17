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
                    <h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1">{{ $title }}</h1>
                    <!--end::Title-->
                    <!--begin::Separator-->
                    <span class="h-20px border-gray-200 border-start mx-4"></span>
                    <!--end::Separator-->
                    <!--begin::Breadcrumb-->
                    <ul class="breadcrumb breadcrumb-separatorless fw-bold fs-7 my-1">
                        <!--begin::Item-->
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('dashboard.index') }}"
                                class="text-muted text-hover-primary">@lang('Home')</a>
                        </li>
                        <!--end::Item-->
                        
                        <!--begin::Item-->
                        <li class="breadcrumb-item text-muted">@lang("Purchases")</li>
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
                    
                    <!-- Wizard step indicator -->
                    <div class="card-header border-0 pt-5 mb-3">
                        <div class="w-100 wizard-steps-container">
                            <div class="d-flex justify-content-between position-relative align-items-center">
                                <!-- Progress bar line -->
                                <div class="position-absolute start-0 end-0 bg-secondary" style="height: 4px; top: 50%; transform: translateY(-50%); z-index: 1;">
                                    <div class="wizard-progress-bar bg-success h-100" style="width: 0%; transition: width 0.3s ease;"></div>
                                </div>
                                <!-- Step 1 -->
                                <div class="wizard-step-indicator active text-center position-relative" data-step="1" style="z-index: 2; width: 33%">
                                    <div class="step-num rounded-circle d-flex align-items-center justify-content-center mx-auto">1</div>
                                    <div class="step-title mt-2">@lang('Basic Info')</div>
                                </div>
                                <!-- Step 2 -->
                                <div class="wizard-step-indicator text-center position-relative" data-step="2" style="z-index: 2; width: 33%">
                                    <div class="step-num rounded-circle d-flex align-items-center justify-content-center mx-auto">2</div>
                                    <div class="step-title mt-2">@lang('Financials & Details')</div>
                                </div>
                                <!-- Step 3 -->
                                <div class="wizard-step-indicator text-center position-relative" data-step="3" style="z-index: 2; width: 33%">
                                    <div class="step-num rounded-circle d-flex align-items-center justify-content-center mx-auto">3</div>
                                    <div class="step-title mt-2">@lang('Attachments & Bank Transfer')</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form class="form" method="POST" id="operation-form" enctype="multipart/form-data" redirect-to="{{route("dashboard.purchases.index")}}" data-id="{{$item->id ?? null}}"
                        @isset($item)
                            action="{{ route("dashboard.purchases.edit",$item->id) }}"
                            data-mode="edit"
                        @else
                            action="{{ route("dashboard.purchases.create") }}"
                            data-mode="new"
                        @endisset
                        >

                        @csrf
                        @isset($item)
                            @method('PUT')
                        @endisset

                        <div class="card-body">
                            
                            <!-- Step 1: Basic Info -->
                            <div class="wizard-step row" data-step="1">
                                <div class="form-group mb-3 col-md-6">
                                    <label class="required" for="item_id">{{ trans("item") }}</label>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="flex-grow-1">
                                            <select class="custom-select form-select advance-select" name="item_id" id="item_id">
                                                <option value="">{{trans("select item")}}</option>
                                                @foreach($items ?? [] as $iItem)
                                                    <option value="{{$iItem->id}}" @selected(isset($item) and $item->item_id == $iItem->id)>{{$iItem->name}}</option>
                                                @endforeach
                                                <option value="other">{{ trans("Other") }}</option>
                                            </select>
                                        </div>
                                       
                                    </div>
                                </div>

                                <div class="form-group mb-3 col-md-6">
                                    <label class="required" for="provider_id">{{ trans("provider") }}</label>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="flex-grow-1">
                                            <select class="custom-select form-select advance-select" name="provider_id" id="provider_id">
                                                <option value="">{{trans("select provider")}}</option>
                                                @foreach($providers ?? [] as $pItem)
                                                    <option value="{{$pItem->id}}" @selected(isset($item) and $item->provider_id == $pItem->id)>{{$pItem->name}}</option>
                                                @endforeach
                                                <option value="other">{{ trans("Other") }}</option>
                                            </select>
                                        </div>
                                        
                                    </div>
                                </div>

                                <div class="form-group mb-3 col-md-6">
                                    <label for="collection_date">{{ trans("Collection Date") }}</label>
                                    <input type="date" name="collection_date" id="collection_date" class="form-control"
                                        value="{{ isset($item) && $item->collection_date ? $item->collection_date->format('Y-m-d') : '' }}">
                                </div>

                                <div class="form-group mb-3 col-md-6">
                                    <label for="reference_id">{{ trans("Reference ID") }}</label>
                                    <input type="text" name="reference_id" id="reference_id" class="form-control"
                                        placeholder="{{ trans("Auto-generated if empty") }}" value="{{ $item->reference_id ?? '' }}">
                                </div>
                            </div>

                            <!-- Step 2: Financials & Details -->
                            <div class="wizard-step row d-none" data-step="2">
                                <div class="form-group mb-3 col-md-4">
                                    <label class="required" for="value_before_tax">{{ trans("value before tax") }}</label>
                                    <input type="number" step="0.01" name="value_before_tax" id="value_before_tax" class="form-control calc-tax"
                                        placeholder="{{ trans("Enter value before tax") }} " value="{{ $item->value_before_tax ?? '' }}">
                                </div>

                                <div class="form-group mb-3 col-md-4">
                                    <label class="required" for="tax_value">{{ trans("tax value") }}</label>
                                    <input type="number" step="0.01" name="tax_value" id="tax_value" class="form-control calc-tax"
                                        placeholder="{{ trans("Enter tax value") }} " value="{{ $item->tax_value ?? '' }}">
                                </div>

                                <div class="form-group mb-3 col-md-4">
                                    <label class="required" for="value_after_tax">{{ trans("value after tax") }}</label>
                                    <input type="number" step="0.01" name="value_after_tax" id="value_after_tax" class="form-control calc-tax"
                                        placeholder="{{ trans("Enter value after tax") }} " value="{{ $item->value_after_tax ?? '' }}">
                                </div>

                                <div class="form-group mb-3 col-md-12">
                                    <label class="" for="notes">{{ trans("notes") }}</label>
                                    <textarea name="notes" class="form-control "
                                        placeholder="{{ trans("Enter notes") }} ">{{ $item->notes ?? '' }}</textarea>
                                </div>
                            </div>

                            <!-- Step 3: Attachments & Bank Transfer Files -->
                            <div class="wizard-step row d-none" data-step="3">
                                <div class="form-group mb-3 col-md-12">
                                    <label for="attachment">{{ trans("Attachment") }}</label>
                                    <div class="media-center-group form-control" data-max="1" data-type="file">
                                        <input type="text" hidden="hidden" class="form-control" name="attachment" id="attachment" value="{{ old("attachment" , $item->attachment ?? null) }}">
                                        <button type="button" class="btn btn-secondary media-center-load" style="margin-top: 10px;"><i class="fa fa-file-upload"></i></button>
                                        <div class="input-gallery"></div>
                                    </div>
                                </div>

                                <div class="form-group mb-3 col-md-12" id="bank-transfer-files-group">
                                    <label for="bank_transfer_files">{{ trans("Bank Transfer Files (List of PDFs)") }}</label>
                                    <div class="media-center-group form-control" data-max="10" data-type="file">
                                        <input type="text" hidden="hidden" class="form-control" name="bank_transfer_files" id="bank_transfer_files" value="{{ old("bank_transfer_files" , $item->bank_transfer_files ?? null) }}">
                                        <button type="button" class="btn btn-secondary media-center-load" style="margin-top: 10px;"><i class="fa fa-file-upload"></i></button>
                                        <div class="input-gallery"></div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        
                        <div class="card-footer d-flex justify-content-between">
                            <button type="button" class="btn btn-secondary font-weight-bold" id="prev-step-btn">{{ trans('Previous') }}</button>
                            <button type="button" class="btn btn-primary font-weight-bold" id="next-step-btn">{{ trans('Next') }}</button>
                            <button type="submit" class="btn btn-success font-weight-bold d-none" id="submit-wizard-btn">{{ trans('save') }}</button>
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
    
    <!-- Add Item Modal -->
    <div class="modal fade" id="add-item-modal" tabindex="-1" aria-labelledby="addItemModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addItemModalLabel">{{ trans("Create Purchase Item") }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label class="required" for="new_item_name">{{ trans("Item Name") }}</label>
                        <input type="text" id="new_item_name" class="form-control" placeholder="{{ trans("Enter item name") }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ trans("Cancel") }}</button>
                    <button type="button" class="btn btn-primary" id="save-new-item-btn">{{ trans("Save") }}</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Provider Modal -->
    <div class="modal fade" id="add-provider-modal" tabindex="-1" aria-labelledby="addProviderModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addProviderModalLabel">{{ trans("Create Purchase Provider") }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label class="required" for="new_provider_name">{{ trans("Provider Name") }}</label>
                        <input type="text" id="new_provider_name" class="form-control" placeholder="{{ trans("Enter provider name") }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ trans("Cancel") }}</button>
                    <button type="button" class="btn btn-primary" id="save-new-provider-btn">{{ trans("Save") }}</button>
                </div>
            </div>
        </div>
    </div>

    @include('media::mediaCenter.modal')
@endsection
@push('css')
    <link href="{{ asset('control') }}/js/custom/crud/form.css" rel="stylesheet" type="text/css" />
    <style>
        .wizard-steps-container {
            padding: 1.5rem 0;
            border-bottom: 1px solid #eff2f5;
        }
        .wizard-step-indicator {
            flex: 1;
            text-align: center;
            position: relative;
        }
        .wizard-step-indicator .step-num {
            width: 40px;
            height: 40px;
            line-height: 34px;
            border: 3px solid #fff;
            background-color: #e4e6ef;
            color: #7e8299;
            font-weight: 700;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            margin: 0 auto;
            z-index: 2;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .wizard-step-indicator.active .step-num {
            background-color: #009ef7;
            color: #fff;
            box-shadow: 0 0 0 3px rgba(0, 158, 247, 0.15);
        }
        .wizard-step-indicator.completed .step-num {
            background-color: #50cd89;
            color: #fff;
            box-shadow: 0 0 0 3px rgba(80, 205, 137, 0.15);
        }
        .wizard-step-indicator .step-title {
            margin-top: 0.5rem;
            font-size: 0.85rem;
            font-weight: 600;
            color: #a1a5b7;
            transition: color 0.3s ease;
        }
        .wizard-step-indicator.active .step-title {
            color: #009ef7;
            font-weight: 700;
        }
        .wizard-step-indicator.completed .step-title {
            color: #50cd89;
        }
        .wizard-step {
            transition: opacity 0.3s ease, transform 0.3s ease;
        }
        .wizard-step.d-none {
            display: none !important;
            opacity: 0;
            transform: translateY(10px);
        }
        .wizard-step:not(.d-none) {
            opacity: 1;
            transform: translateY(0);
        }
        .gap-2 {
            gap: 0.5rem !important;
        }
    </style>
@endpush
@push('js')
    <script src="{{ asset('control') }}/js/custom/crud/form.js"></script>
    <script>
        $(document).ready(function() {
            // Auto calculate value_after_tax or tax_value etc, or just helpful hooks if they edit them
            $('.calc-tax').on('input', function() {
                var before = parseFloat($('#value_before_tax').val()) || 0;
                var tax = parseFloat($('#tax_value').val()) || 0;
                if ($(this).attr('id') !== 'value_after_tax') {
                    $('#value_after_tax').val((before + tax).toFixed(2));
                }
            });

            // Wizard step state variables
            var currentStep = 1;
            var totalSteps = 3;

            function updateWizard() {
                // Update steps visibility
                $('.wizard-step').addClass('d-none');
                $('.wizard-step[data-step="' + currentStep + '"]').removeClass('d-none');

                // Update indicators
                $('.wizard-step-indicator').each(function() {
                    var step = parseInt($(this).data('step'));
                    if (step < currentStep) {
                        $(this).removeClass('active').addClass('completed');
                    } else if (step === currentStep) {
                        $(this).addClass('active').removeClass('completed');
                    } else {
                        $(this).removeClass('active completed');
                    }
                });

                // Update progress line
                var progress = ((currentStep - 1) / (totalSteps - 1)) * 100;
                $('.wizard-progress-bar').css('width', progress + '%');

                // Update buttons
                if (currentStep === 1) {
                    $('#prev-step-btn').prop('disabled', true);
                } else {
                    $('#prev-step-btn').prop('disabled', false);
                }

                if (currentStep === totalSteps) {
                    $('#next-step-btn').addClass('d-none');
                    $('#submit-wizard-btn').removeClass('d-none');
                } else {
                    $('#next-step-btn').removeClass('d-none');
                    $('#submit-wizard-btn').addClass('d-none');
                }
            }

            function validateStep(step) {
                var isValid = true;
                if (step === 1) {
                    if (!$('#item_id').val()) {
                        toastr.error('{{ trans("Please select an item") }}');
                        isValid = false;
                    }
                    if (!$('#provider_id').val()) {
                        toastr.error('{{ trans("Please select a provider") }}');
                        isValid = false;
                    }
                } else if (step === 2) {
                    if (!$('#value_before_tax').val()) {
                        toastr.error('{{ trans("Please enter value before tax") }}');
                        isValid = false;
                    }
                    if (!$('#tax_value').val()) {
                        toastr.error('{{ trans("Please enter tax value") }}');
                        isValid = false;
                    }
                    if (!$('#value_after_tax').val()) {
                        toastr.error('{{ trans("Please enter value after tax") }}');
                        isValid = false;
                    }
                }
                return isValid;
            }

            $('#next-step-btn').on('click', function() {
                if (validateStep(currentStep)) {
                    currentStep++;
                    updateWizard();
                }
            });

            $('#prev-step-btn').on('click', function() {
                if (currentStep > 1) {
                    currentStep--;
                    updateWizard();
                }
            });

            // Re-initialize select2 with custom matcher for Other
            function initSelect2WithOther(selector) {
                var selectEl = $(selector);
                var placeholderText = selectEl.attr('name') + "...";
                var dropdownParentEl = null;
                if (selectEl.parents('.modal').length) {
                    var modalId = selectEl.parents('.modal').first().attr('id');
                    dropdownParentEl = $('#' + modalId);
                }

                selectEl.select2('destroy').select2({
                    allowClear: true,
                    placeholder: placeholderText,
                    dropdownParent: dropdownParentEl,
                    width: '100%',
                    matcher: function(params, data) {
                        if ($.trim(params.term) === '') {
                            return data;
                        }
                        if (typeof data.text === 'undefined') {
                            return null;
                        }
                        if (data.id === 'other') {
                            return data;
                        }
                        if (data.text.toLowerCase().indexOf(params.term.toLowerCase()) > -1) {
                            return data;
                        }
                        return null;
                    }
                }).trigger('change.select2');
            }

            initSelect2WithOther('#item_id');
            initSelect2WithOther('#provider_id');

            // Open Modals when Other is selected
            $('#item_id').on('change', function() {
                if ($(this).val() === 'other') {
                    $(this).val('').trigger('change.select2');
                    $('#add-item-modal').modal('show');
                }
            });

            $('#provider_id').on('change', function() {
                if ($(this).val() === 'other') {
                    $(this).val('').trigger('change.select2');
                    $('#add-provider-modal').modal('show');
                }
            });

            // Payment method dynamic toggle removed

            // Quick Add Item Modal Save Handler
            $('#save-new-item-btn').on('click', function() {
                var name = $('#new_item_name').val().trim();
                if (!name) {
                    toastr.error('{{ trans("Item name is required") }}');
                    return;
                }
                var btn = $(this);
                btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
                
                $.ajax({
                    url: '{{ route("dashboard.purchase-items.create") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        name: name
                    },
                    dataType: 'json',
                    success: function(response) {
                        btn.prop('disabled', false).text('{{ trans("Save") }}');
                        if (response.entity) {
                            var newOption = new Option(response.entity.name, response.entity.id, true, true);
                            $('#item_id option[value="other"]').before(newOption);
                            $('#item_id').val(response.entity.id);
                            initSelect2WithOther('#item_id');
                            $('#add-item-modal').modal('hide');
                            $('#new_item_name').val('');
                            toastr.success('{{ trans("Item added successfully") }}');
                        } else {
                            toastr.error(response.message || '{{ trans("Error adding item") }}');
                        }
                    },
                    error: function(xhr) {
                        btn.prop('disabled', false).text('{{ trans("Save") }}');
                        var msg = '{{ trans("Error adding item") }}';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        toastr.error(msg);
                    }
                });
            });

            // Quick Add Provider Modal Save Handler
            $('#save-new-provider-btn').on('click', function() {
                var name = $('#new_provider_name').val().trim();
                if (!name) {
                    toastr.error('{{ trans("Provider name is required") }}');
                    return;
                }
                var btn = $(this);
                btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
                
                $.ajax({
                    url: '{{ route("dashboard.purchase-providers.create") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        name: name
                    },
                    dataType: 'json',
                    success: function(response) {
                        btn.prop('disabled', false).text('{{ trans("Save") }}');
                        if (response.entity) {
                            var newOption = new Option(response.entity.name, response.entity.id, true, true);
                            $('#provider_id option[value="other"]').before(newOption);
                            $('#provider_id').val(response.entity.id);
                            initSelect2WithOther('#provider_id');
                            $('#add-provider-modal').modal('hide');
                            $('#new_provider_name').val('');
                            toastr.success('{{ trans("Provider added successfully") }}');
                        } else {
                            toastr.error(response.message || '{{ trans("Error adding provider") }}');
                        }
                    },
                    error: function(xhr) {
                        btn.prop('disabled', false).text('{{ trans("Save") }}');
                        var msg = '{{ trans("Error adding provider") }}';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        toastr.error(msg);
                    }
                });
            });

            // Initialize wizard on page load
            updateWizard();
        });
    </script>
@endpush