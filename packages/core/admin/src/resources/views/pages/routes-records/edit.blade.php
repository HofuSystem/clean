@extends('admin::layouts.dashboard')
@section('content')
    <!--end::Header-->
    <!--begin::Content-->
    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        <!--begin::Toolbar-->
        <div class="toolbar" id="kt_toolbar">
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
                            <a href=""
                                class="text-muted text-hover-primary">@lang('Home')</a>
                        </li>
                        <!--end::Item-->
                        <!--begin::Item-->
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-200 w-5px h-2px"></span>
                        </li>
                        <!--end::Item-->
                        <!--begin::Item-->
                        <li class="breadcrumb-item text-muted">@lang("admin")</li>
                        <!--end::Item-->
                        <!--begin::Item-->
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-200 w-5px h-2px"></span>
                        </li>
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
            <div id="kt_content_container" class="container-xxl">
                <!--begin::Card-->
                <div class="card">
                    
                    <form class="form" method="POST" id="operation-form" data-id="{{$item->id ?? null}}"
                        @isset($item)
                            action="{{ route("dashboard.routes-records.edit",$item->id) }}"
                            data-mode="edit"
                        @else
                            action="{{ route("dashboard.routes-records.store") }}"
                            data-mode="new"
                        @endisset
                        >

                        @csrf
                        <div class="card-body row">
                        
                            <div class="form-group mb-3 col-md-12">
                                <label class="" for="end_point">{{ trans("end point") }}</label>
                                <input type="text" name="end_point" class="form-control "
                                    placeholder="{{ trans("Enter end point") }} " value="@isset($item){{ $item->end_point }}@endisset">
                                    
                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <label>{{ trans("attributes") }}</label>
                                @if(isset($item) && $item->attributes)
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped" id="attributes-table">
                                            <thead class="table-primary">
                                                <tr>
                                                    <th>{{ trans('Key') }}</th>
                                                    <th>{{ trans('Value') }}</th>
                                                    <th style="width: 100px;">{{ trans('Action') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach(json_decode($item->attributes, true) ?? [] as $key => $value)
                                                    <tr>
                                                        <td>
                                                            <input type="text" class="form-control attr-key" value="{{ $key }}" name="attr_keys[]">
                                                        </td>
                                                        <td>
                                                            @if(is_array($value))
                                                                <textarea class="form-control attr-value" name="attr_values[]">{{ json_encode($value, JSON_PRETTY_PRINT) }}</textarea>
                                                            @else
                                                                <input type="text" class="form-control attr-value" value="{{ $value }}" name="attr_values[]">
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <button type="button" class="btn btn-danger btn-sm remove-attr"><i class="fas fa-trash"></i></button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="3">
                                                        <button type="button" class="btn btn-success btn-sm" id="add-attr">
                                                            <i class="fas fa-plus"></i> {{ trans('Add Attribute') }}
                                                        </button>
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                    <!-- Hidden input to store the final JSON -->
                                    <input type="hidden" name="attributes" id="attributes-json">
                                @else
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped" id="attributes-table">
                                            <thead class="table-primary">
                                                <tr>
                                                    <th>{{ trans('Key') }}</th>
                                                    <th>{{ trans('Value') }}</th>
                                                    <th style="width: 100px;">{{ trans('Action') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <input type="text" class="form-control attr-key" name="attr_keys[]">
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control attr-value" name="attr_values[]">
                                                    </td>
                                                    <td>
                                                        <button type="button" class="btn btn-danger btn-sm remove-attr"><i class="fas fa-trash"></i></button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="3">
                                                        <button type="button" class="btn btn-success btn-sm" id="add-attr">
                                                            <i class="fas fa-plus"></i> {{ trans('Add Attribute') }}
                                                        </button>
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                    <!-- Hidden input to store the final JSON -->
                                    <input type="hidden" name="attributes" id="attributes-json">
                                @endif
                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <label class="" for="user_id">{{ trans("user") }}</label>
                                <select class="custom-select  form-select advance-select" name="user_id" id="user_id"  >
                                    
                                    <option   value="" >{{trans("select user")}}</option>
                                    @foreach($users ?? [] as $sItem)
                                        <option data-id="{{$sItem->id }}" @selected(isset($item)  and $item->user_id == $sItem->id) value="{{$sItem->id }}" >{{$sItem->fullname}}</option>
                                    @endforeach
            
                                </select>
                                            
                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <label class="" for="ip_address">{{ trans("ip_address") }}</label>
                                <input type="text" name="ip_address" class="form-control" 
                                    placeholder="{{ trans('Enter IP address') }}" 
                                    value="@isset($item){{ $item->ip_address }}@endisset">
                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <label class="" for="headers">{{ trans("headers") }}</label>
                                <input type="text" name="headers" class="form-control" 
                                    placeholder="{{ trans('Enter headers') }}" 
                                    value="@isset($item){{ $item->headers }}@endisset">
                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <label class="" for="method">{{ trans("method") }}</label>
                                <input type="text" name="method" class="form-control" 
                                    placeholder="{{ trans('Enter method') }}" 
                                    value="@isset($item){{ $item->method }}@endisset">
                            </div>
                            <div class="form-group mb-3 col-md-12">
                                <label class="" for="version">{{ trans("version") }}</label>
                                <input type="text" name="version" class="form-control" 
                                    placeholder="{{ trans('Enter version') }}" 
                                    value="@isset($item){{ $item->version }}@endisset">
                            </div>
                        </div>
                        
                        <div class="card-footer">
                            <div class="row">
                                <div class="col-lg-9 ml-lg-auto">
                                    <button type="submit" class="btn btn-primary font-weight-bold mr-2">{{ trans("Submit") }}</button>
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
    @include('media::mediaCenter.modal')
@endsection
@push('css')
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <link href="{{ asset('control') }}/js/custom/crud/form.css" rel="stylesheet" type="text/css" />
@endpush
@push('js')
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/TableDnD/1.0.5/jquery.tablednd.js"></script>
    <script src="{{ asset('control') }}/js/custom/crud/form.js"></script>
    <script>
        $(document).ready(function() {
            // Function to update the hidden JSON input
            function updateAttributesJson() {
                var attributes = {};
                $('#attributes-table tbody tr').each(function() {
                    var key = $(this).find('.attr-key').val();
                    var value = $(this).find('.attr-value').val();
                    if (key) {
                        try {
                            // Try to parse the value as JSON
                            attributes[key] = JSON.parse(value);
                        } catch (e) {
                            // If not JSON, use the raw value
                            attributes[key] = value;
                        }
                    }
                });
                $('#attributes-json').val(JSON.stringify(attributes));
            }

            // Add new attribute row
            $('#add-attr').click(function() {
                var newRow = `
                    <tr>
                        <td>
                            <input type="text" class="form-control attr-key" name="attr_keys[]">
                        </td>
                        <td>
                            <input type="text" class="form-control attr-value" name="attr_values[]">
                        </td>
                        <td>
                            <button type="button" class="btn btn-danger btn-sm remove-attr"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                `;
                $('#attributes-table tbody').append(newRow);
            });

            // Remove attribute row
            $(document).on('click', '.remove-attr', function() {
                $(this).closest('tr').remove();
                updateAttributesJson();
            });

            // Update JSON when inputs change
            $(document).on('change', '.attr-key, .attr-value', function() {
                updateAttributesJson();
            });

            // Initial JSON update
            updateAttributesJson();

            // Update JSON before form submission
            $('#operation-form').submit(function() {
                updateAttributesJson();
            });
        });
    </script>
@endpush
