@extends('admin::layouts.dashboard')
@section('content')
    <!--end::Header-->

    <!--begin::Content-->
    <div class="container-fluid flex-grow-1 container-p-y " >
     
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
                        <a href="{{ route('dashboard.index') }}" class="text-muted text-hover-primary">@lang('Home')</a>
                    </li>
                    <!--end::Item-->

                    <!--begin::Item-->
                    <li class="breadcrumb-item text-muted">@lang("orders")</li>
                    <!--end::Item-->

                    <!--begin::Item-->
                    <li class="breadcrumb-item text-dark">{{ $title }}</li>
                    <!--end::Item-->
                </ul>
                <!--end::Breadcrumb-->
            </div>
            <!--end::Page title-->

        </div>
        <!--begin::Container-->
        <div  class="container-fluid">
            <!--begin::Card-->
            <div class="card">
                <!--begin::Card header-->
                <div class="card-header row">
                    <!--begin::Card title-->
                    <div class="card-title col-md-6 ">
                        <!--begin::cols-->
                        <div class="form-group d-flex justify-content-center">
                            <label class="text-dark fw-bold" for="visible_cols"> @lang('visible cols')</label>
                            <select class="form-control mx-3" data-control="select2" name="visible_cols"
                                id="visible_cols" multiple></select>
                        </div>
                        <!--end::cols-->
                    </div>
                    <!--begin::Card title-->
                    <!--begin::Card toolbar-->
                    <div class="card-toolbar col-md-6">
                        <!--begin::Toolbar-->

                        <div  data-kt-user-table-toolbar="base">
                            <div class="d-flex justify-content-between">
                                <div class="">
                                    <div class="d-flex">
                                     
                                        <!--begin::Stat-->
                                        <div class="border border-dashed border-success text-success rounded mx-1 p-2">
                                            <a href="{{ route('dashboard.carts.index') }}">
                                            <div class="fw-bolder fs-5 text-success" > 
                                                {{ $total }} 
                                                <i class="fas fa-list-alt"></i>
                                                @lang('total')
                                            </div>
                                            </a>
                                        </div>
                                        <!--end::Stat-->
                                        <!--begin::Stat-->
                                        <div class="border border-dashed border-danger  text-danger rounded mx-1 p-2">
                                            <a href="{{ route('dashboard.carts.index',['trash' => 1]) }}">
                                            <div class="fw-bolder fs-5 text-danger" >
                                                {{ $trash }}
                                                <i class="fas fa-trash-alt"></i>
                                                @lang('Trash')
                                            </div>
                                            </a>
                                        </div>
                                        <!--end::Stat-->
                                    </div>
                                </div>
                                <div class="d-flex flex-wrap">
                                    @can('dashboard.carts.export')
                                    <a href="{{ route('dashboard.carts.export') }}" id="export" type="button"
                                        class="btn-operation ">
                                        <i class="fas fa-upload"></i>
                                        <span>
                                            @lang('Export Report')
                                        </span>
                                    </a>
                                @endcan
                                @can('dashboard.carts.import')
                                    <a href="{{ route('dashboard.carts.import') }}"
                                        class="btn-operation">
                                        <i class="fas fa-file-import"></i>
                                        <span>
                                            @lang('import list')
                                        </span>
                                    </a>
                                @endcan
                                <!--begin::Add -->
                                @can('dashboard.carts.create')
                                    <a href="{{ route('dashboard.carts.create') }}"
                                        class="btn-operation ">
                                        <i class="fas fa-plus-circle"></i>
                                        <span>
                                            @lang('create new')
                                        </span>
                                    </a>
                                @endcan
                                </div>

                            </div>
                        </div>
                        <!--end::Toolbar-->
                        <!--begin::Group actions-->
                        <div class="d-flex justify-content-end align-items-center d-none"
                            data-kt-user-table-toolbar="selected">
                            <div class="border border-warning border-dashed rounded text-warning  p-2 mx-1">
                                <span class="me-2" data-kt-user-table-select="selected_count"></span>@lang('Selected')
                            </div>
                            <button type="button" class="btn btn-primary"
                                data-kt-user-table-select="delete_selected">@lang('Delete Selected')</button>
                            @can('dashboard.notifications.create')
                                <button type="button" class="btn btn-primary mx-2"
                                    id="notify_selected"><i class="fas fa-bell"></i> <span>@lang('notify') </span></button>
                            @endcan
                        </div>
                        <!--end::Group actions-->
                    </div>

                    <!--end::Card toolbar-->
                </div>

                <!--begin::Content-->
                <div class="container-fluid mt-1">
                    <button class="btn btn-primary mb-1" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                        <i class="fas fa-filter"></i>
                        {{ trans('open filters of data') }}
                    </button>

                    <div class="accordion" id="accordionExample">
                        <div class="accordion-item">
                            <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne"
                                data-bs-parent="#accordionExample">

                                <div class="p-1 row" data-kt-user-table-filter="form">

                                    
                                        <div class="col-md-4 mb-1">
                                            <label for="city_id">@lang("city")</label>
                                            <select class="custom-select filter-input form-select advance-select" name="city_id" id="city_id">
                                                
                                                <option  value="" > @lang("select cities")</option>
                                                @foreach($cities as $item)
                                                    <option value="{{$item->id }}" @selected($item->id  == request("city_id")) >@lang($item->name)</option>
                                                @endforeach
            
                                            </select>
                                        </div>
                                        <div class="col-md-4 mb-1">
                                            <label for="user_id">@lang("user")</label>
                                            <select class="custom-select filter-input form-select advance-select" name="user_id" id="user_id">
                                                
                                                <option  value="" > @lang("select users")</option>
                                                @foreach($users as $item)
                                                    <option value="{{$item->id }}" @selected($item->id  == request("user_id")) >{{ $item->fullname }} - {{ $item->phone }}</option>
                                                @endforeach
            
                                            </select>
                                        </div>
                                    
                                        <div class="col-md-4 mb-1">
                                            <label for="phone"> @lang("phone") </label>
                                            <input type="text" name="phone" class="form-control filter-input"
                                                placeholder="@lang("search for phone") " value="{{ request("phone") }}">
                                        </div>
                                        <div class="col-md-6 mb-1">
                                        <label for="created_at"> @lang("Create Date from") </label>
                                        <input type="datetime-local" name="from_created_at" class="form-control filter-input"
                                            placeholder="@lang("search for Create Date") " value="{{ request("created_at") }}">
                                            </div>
                                            <div class="col-md-6 mb-1">
                                            <label for="created_at"> @lang("Create Date to") </label>
                                            <input type="datetime-local" name="to_created_at" class="form-control filter-input"
                                                placeholder="@lang("search for Create Date") " value="{{ request("created_at") }}">
                                        </div>
                                        
                                    <!--begin::Actions-->
                                    <div class=" d-flex justify-content-end">
                                        <button type="reset"
                                            class="btn btn-light btn-active-light-primary fw-bold me-2 px-6"
                                            data-kt-menu-dismiss="true"
                                            data-kt-user-table-filter="reset">@lang('Reset')</button>
                                        <button type="submit" class="btn btn-primary fw-bold px-6"
                                            data-kt-menu-dismiss="true"
                                            data-kt-user-table-filter="filter">@lang('Apply')</button>
                                    </div>
                                    <!--end::Actions-->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!--end::Content-->

                <!--end::Card header-->
                <!--begin::Card body-->
                <div class="card-body pt-0 table-responsive table-responsive">
                    <!--begin::Table-->
                    <table class="table align-middle text-center table-row-dashed fs-6 gy-5" id="view-datatable"
                        data-load="{{ route('dashboard.carts.index',['trash' => request()->trash]) }}">
                        <!--begin::Table head-->
                        <thead class="table-primary">
                            <!--begin::Table row-->
                            <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                
                                    <th class="text-center p-0" data-name="select_switch">
                                        <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                        <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#view-datatable .form-check-input" value="1">
                                        </div>
                                    </th>
                                    <th class="text-center p-0" data-name="id">@lang("id")</th>

                                    
                                    <th class="text-center p-0" data-name="user_id">@lang("fullname")</th>
                                    <th class="text-center p-0" data-name="phone">@lang("phone")</th>
                                    <th class="text-center p-0" orderable="false" data-name="city">@lang("city")</th>
                                    <th class="text-center p-0" orderable="false" data-name="district">@lang("district")</th>
                                    <th class="text-center p-0" orderable="false" data-name="number_of_items">@lang("number of items")</th>
                                    <th class="text-center p-0" orderable="false" data-name="number_of_orders">@lang("number of orders")</th>
                                    <th class="text-center p-0" orderable="false" data-name="last_order">@lang("last order")</th>
                                    <th class="text-center p-0" orderable="false" data-name="order_total_price">@lang("order total price")</th>
                                    <th class="text-center p-0" orderable="false" data-name="follow_up_count">@lang("follow ups")</th>
                                    <th class="text-center p-0" orderable="false" data-name="has_active_follow_up">@lang("follow up status")</th>
                                    <th class="text-center p-0" data-name="updated_at">@lang("added at")</th>
                                    <th class="text-center p-0" data-name="actions">@lang("Actions")</th>
                            </tr>
                            <!--end::Table row-->
                        </thead>
                        <!--end::Table head-->
                        <!--begin::Table body-->
                        <tbody class="text-gray-600 fw-bold">

                        </tbody>
                        <!--end::Table body-->
                    </table>
                    <!--end::Table-->
                </div>
                <!--end::Card body-->
            </div>
            <!--end::Card-->
        </div>
        <!--end::Container-->
        <!--end::Post-->
    </div>
    <!--end::Content-->
    @include('notification::inc.notifyModal')

    <!-- Follow Up Quick Modal -->
    <div class="modal fade" id="followUpModal" tabindex="-1" aria-labelledby="followUpModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="followUpModalLabel">
                        <i class="fas fa-phone-volume me-2 text-primary"></i>
                        @lang('Add Follow Up')
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- User Info -->
                    <div class="alert alert-light border mb-3 d-flex align-items-center gap-3" id="followUpUserInfo">
                        <i class="fas fa-user-circle fs-2 text-primary"></i>
                        <div>
                            <div class="fw-bold" id="followUpUserName">—</div>
                            <div class="text-muted" id="followUpUserPhoneDisplay">—</div>
                        </div>
                        <div class="ms-auto d-flex gap-2">
                            <a href="#" id="followUpCallBtn" class="btn btn-sm btn-success">
                                <i class="fas fa-phone"></i> @lang('Call')
                            </a>
                            <a href="#" id="followUpWaBtn" target="_blank" class="btn btn-sm btn-success">
                                <i class="fab fa-whatsapp"></i> @lang('WhatsApp')
                            </a>
                        </div>
                    </div>

                    <form id="quickFollowUpForm">
                        @csrf
                        <input type="hidden" name="cart_id" id="followUpCartId">
                        <div class="mb-3">
                            <label class="form-label">@lang('Phone')</label>
                            <input type="text" name="phone" id="followUpPhone" class="form-control" placeholder="@lang('phone')">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">@lang('Notes')</label>
                            <textarea name="notes" id="followUpNotes" class="form-control" rows="3" placeholder="@lang('notes')"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang('Cancel')</button>
                    <button type="button" class="btn btn-primary" id="saveFollowUpBtn">
                        <i class="fas fa-save me-1"></i> @lang('Save Follow Up')
                    </button>
                </div>
            </div>
        </div>
    </div>

@endsection
@push('css')

@endpush
@push('js')
<script>
    var deleteUrl = "{{ route('dashboard.carts.delete', ['id'=>'%s','trash'=>request()->trash]) }}"

    var fuLang = {
        storeUrl:    @json(route('dashboard.cart-follow-ups.store')),
        csrf:        @json(csrf_token()),
        unknownUser: @json(__('Unknown User')),
        saving:      @json(__('Saving...')),
        created:     @json(__('Follow up created')),
        save:        @json(__('Save Follow Up')),
        error:       @json(__('Error')),
        systemError: @json(__('system Error please try again later')),
    };

    $(document).ready(function () {
        $(document).on('click','.notify-btn',function(e) {
            e.preventDefault();
            $('#notifyModal').modal('show')
            $('#notifyModal [name=for]').val('users')

            userId = $(this).data('user-id');
            let ids = [userId];
            ids = JSON.stringify(ids);
            
            $('#notifyModal [name=for_data]').val(ids)

        });
        $('#notify_selected').click(function(e) {
            e.preventDefault();
            $('#notifyModal').modal('show')
            $('#notifyModal [name=for]').val('users')

            let ids = [];
            $('input[name="table_selected"]:checked').each(function() {
                let checkboxValue = $(this).data('user-id');
                ids.push(checkboxValue);
            });
            ids = JSON.stringify(ids);
            
            $('#notifyModal [name=for_data]').val(ids)

        });

        // ── Follow Up Quick Modal ──────────────────────────────────────────────
        $(document).on('click', '.start-follow-up-btn', function () {
            let cartId = $(this).data('cart-id');
            let phone  = $(this).data('phone') || '';
            let user   = $(this).data('user')  || '';

            $('#followUpCartId').val(cartId);
            $('#followUpPhone').val(phone);
            $('#followUpNotes').val('');
            $('#followUpUserName').text(user || fuLang.unknownUser);
            $('#followUpUserPhoneDisplay').text(phone || '—');

            // Update call / WhatsApp links
            $('#followUpCallBtn').attr('href', phone ? 'tel:' + phone : '#');
            let clean = phone.replace(/[^0-9]/g, '');
            $('#followUpWaBtn').attr('href', clean ? 'https://wa.me/' + clean : '#');

            $('#followUpModal').modal('show');
        });

        $('#saveFollowUpBtn').click(function () {
            let $btn = $(this).prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> ' + fuLang.saving);

            $.ajax({
                url: fuLang.storeUrl,
                type: 'POST',
                data: $('#quickFollowUpForm').serialize(),
                headers: { 'X-CSRF-TOKEN': fuLang.csrf },
                success: function (res) {
                    if (res.status) {
                        toastr.success(res.message || fuLang.created);
                        $('#followUpModal').modal('hide');
                        // Reload datatable to refresh the actions column
                        if (typeof window.reloadDataTable === 'function') {
                            window.reloadDataTable();
                        } else {
                            $('#view-datatable').DataTable().ajax.reload(null, false);
                        }
                    } else {
                        toastr.error(res.message || fuLang.error);
                    }
                },
                error: function (xhr) {
                    let msg = xhr.responseJSON?.message || fuLang.systemError;
                    toastr.error(msg);
                },
                complete: function () {
                    $btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i> ' + fuLang.save);
                }
            });
        });
    });
</script>


@endpush
