@extends('admin::layouts.dashboard')
@section('content')
    <!--end::Header-->

    <!--begin::Content-->
    <div class="container-fluid flex-grow-1 container-p-y ">

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
                    <li class="breadcrumb-item text-muted">@lang('users')</li>
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
        <div class="container-fluid">
            <!--begin::Card-->  
            <div class="card">
                <!--begin::Card header-->
                <div class="card-header row">
                    <!--begin::Card title-->
                    <div class="card-title col-md-6 ">
                        <!--begin::cols-->
                        <div class="form-group d-flex justify-content-center">
                            <label class="text-dark fw-bold" for="visible_cols"> @lang('visible cols')</label>
                            <select class="form-control mx-3" data-control="select2" name="visible_cols" id="visible_cols"
                                multiple></select>
                        </div>
                        <!--end::cols-->
                    </div>
                    <!--begin::Card title-->
                    <!--begin::Card toolbar-->
                    <div class="card-toolbar col-md-6">
                        <!--begin::Toolbar-->
                        
                        <div data-kt-user-table-toolbar="base">
                            <div class="d-flex justify-content-between">
                                <div class="">
                                    @if(!isset($forCompany))
                                    <table class="table table-bordered  mb-0">
                                        <thead class="table-primary">
                                            <tr>
                                                <th>@lang('Role')</th>
                                                <th>@lang('Total')</th>
                                                <th>@lang('Trash')</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($rolesWithUserCounts as $roleWithUserCounts)
                                                <tr>
                                                    <td>{{ $roleWithUserCounts->title }}</td>
                                                    <td class="text-success fw-bolder text-success">
                                                        <a href="{{ route('dashboard.users.index',['roles' => $roleWithUserCounts->id]) }}">
                                                        <span class="text-success"> 
                                                        {{ $roleWithUserCounts->users_count }}
                                                        <i class="fas fa-list-alt"></i>
                                                        </span>
                                                        </a>
                                                    </td>
                                                    <td class="text-danger fw-bolder text-danger">
                                                        <a href="{{ route('dashboard.users.index',['roles' => $roleWithUserCounts->id, 'trash' => 1]) }}">
                                                        <span class="text-danger">
                                                            {{ $roleWithUserCounts->users_trash_count }}
                                                        <i class="fas fa-trash-alt"></i>
                                                        </span>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    @endif
                                  
                                </div>
                                <div class="d-flex flex-wrap">

                                    @can('dashboard.users.export')
                                        <a href="{{ route('dashboard.users.export') }}" id="export" type="button"
                                            class="btn-operation ">
                                            <i class="fas fa-upload"></i>
                                            <span>
                                                @lang('Export Report')
                                            </span>
                                        </a>
                                    @endcan
                                    @can('dashboard.users.import')
                                        <a href="{{ route('dashboard.users.import') }}" class="btn-operation">
                                            <i class="fas fa-file-import"></i>
                                            <span>
                                                @lang('import list')
                                            </span>
                                        </a>
                                    @endcan
                                    <!--begin::Add -->
                                    @can('dashboard.users.create')
                                        <a href="{{ route('dashboard.users.create') }}" class="btn-operation ">
                                            <i class="fas fa-plus-circle"></i>
                                            <span>
                                                @lang('create new')
                                            </span>
                                        </a>
                                    @endcan
                                    @can('dashboard.notifications.create')
                                        <a href="{{ route('dashboard.notifications.create') }}" class="btn-operation "
                                            id ="notify-all-users">
                                            <i class="fas fa-bell"></i>
                                            <span>
                                                @lang('notify all')
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
                                <button type="button" class="btn btn-warning mx-2" id="notify_selected">@lang('notify') <i
                                        class="fas fa-bell"></i></button>
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
                    @if(!isset($forCompany))
                        <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link  role-switch active" id="pills-all-tab" data-bs-toggle="pill"
                                    data-bs-target="#pills-all" type="button" role="tab" aria-controls="pills-all"
                                    aria-selected="true">{{ trans('All') }}</button>
                            </li>
                            @foreach ($roles as $role)
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link role-switch" data-role-id="{{ $role->id }}"
                                        id="pills-{{ $role->name }}-tab" data-bs-toggle="pill"
                                        data-bs-target="#pills-{{ $role->name }}" type="button" role="tab"
                                        aria-controls="pills-{{ $role->name }}"
                                        aria-selected="true">{{ $role->title }}</button>
                                </li>
                            @endforeach

                        </ul>
                    @endif

                    <div class="accordion" id="accordionExample">
                        <div class="accordion-item">
                            <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne"
                                data-bs-parent="#accordionExample">

                                <div class="p-1 row" data-kt-user-table-filter="form">


                                    <div class="col-md-6 mb-1">
                                        <label for="fullname"> @lang('full name') </label>
                                        <input type="text" name="fullname" class="form-control filter-input"
                                            placeholder="@lang('search for full name') " value="{{ request('fullname') }}">
                                    </div>
                                    <div class="col-md-6 mb-1">
                                        <label for="email"> @lang('email') </label>
                                        <input type="email" name="email" class="form-control filter-input"
                                            placeholder="@lang('search for email') " value="{{ request('email') }}">
                                    </div>

                                    <div class="col-md-6 mb-1">
                                        <label for="phone"> @lang('phone') </label>
                                        <input type="text" name="phone" class="form-control filter-input"
                                            placeholder="@lang('search for phone') " value="{{ request('phone') }}">
                                    </div>
                                    <div class="col-md-6 mb-1">
                                        <label for="roles">@lang('roles')</label>
                                        <select class="custom-select filter-input form-select advance-select"
                                            name="roles" id="roles">

                                            <option value=""> @lang('select roles')</option>
                                            @foreach ($roles as $item)
                                                <option value="{{ $item->id }}" @selected($item->id == request('roles'))>
                                                    @lang($item->name)</option>
                                            @endforeach

                                        </select>
                                    </div>

                                    <div class="col-md-12 mb-1">
                                        <label for="gender">@lang('gender')</label>
                                        <select class="custom-select filter-input form-select advance-select"
                                            name="gender" id="gender">
                                            <option value=""> @lang('select gender')</option>
                                            <option value="male" @selected('male' == request('gender'))>{{ trans('male') }}
                                            </option>
                                            <option value="female" @selected('female' == request('gender'))>{{ trans('female') }}
                                            </option>
                                        </select>
                                    </div>

                                    <div class="col-md-6 mb-1">
                                        <label for="created_at"> @lang('Register from') </label>
                                        <input type="datetime-local" name="from_created_at"
                                            class="form-control filter-input" placeholder="@lang('search for Create Date') "
                                            value="{{ request('created_at') }}">
                                    </div>
                                    <div class="col-md-6 mb-1">
                                        <label for="created_at"> @lang('Register to') </label>
                                        <input type="datetime-local" name="to_created_at"
                                            class="form-control filter-input" placeholder="@lang('search for Create Date') "
                                            value="{{ request('created_at') }}">
                                    </div>
                                    <div class="col-md-6 mb-1">
                                        <label for="orders_min">@lang('Orders Min')</label>
                                        <input type="number" name="orders_min" class="form-control filter-input"
                                            placeholder="@lang('Minimum orders')" value="{{ request('orders_min') }}">
                                    </div>
                                    <div class="col-md-6 mb-1">
                                        <label for="orders_max">@lang('Orders Max')</label>
                                        <input type="number" name="orders_max" class="form-control filter-input"
                                            placeholder="@lang('Maximum orders')" value="{{ request('orders_max') }}">
                                    </div>
                                    <div class="col-md-6 mb-1">
                                        <label for="city_id">@lang("city")</label>
                                        <select class="custom-select filter-input form-select advance-select" name="city_id" id="city_id">
                                            
                                            <option  value="" > @lang("select cities")</option>
                                            @foreach($cities as $item)
                                                <option value="{{$item->id }}" @selected($item->id  == request("city_id")) >@lang($item->name)</option>
                                            @endforeach
        
                                        </select>
                                    </div>
                                
                                    <div class="col-md-6 mb-1">
                                        <label for="district_id">@lang("district")</label>
                                        <select class="custom-select filter-input form-select advance-select" name="district_id" id="district_id">
                                            
                                            <option  value="" > @lang("select districts")</option>
                                            @foreach($districts as $item)
                                                <option value="{{$item->id }}" @selected($item->id  == request("district_id")) >@lang($item->name)</option>
                                            @endforeach
        
                                        </select>
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
                        data-load="{{ route('dashboard.users.index',['trash' => request()->trash, 'forCompany' => $forCompany ?? false ]) }}">
                        <!--begin::Table head-->
                        <thead class="table-primary">
                            <!--begin::Table row-->
                            <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">

                                <th class="w-10px pe-2" data-name="select_switch">
                                    <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                        <input class="form-check-input" type="checkbox" data-kt-check="true"
                                            data-kt-check-target="#view-datatable .form-check-input" value="1">
                                    </div>
                                </th>
                                <th class="text-center p-0" data-name="id">@lang('id')</th>


                                <th class="text-center p-0" data-name="image">@lang('avatar')</th>
                                <th class="text-center p-0" data-name="fullname">@lang('full name')</th>
                                <th class="text-center p-0" data-name="email">@lang('email')</th>
                                <th class="text-center p-0" data-name="phone">@lang('phone')</th>
                                <th class="text-center p-0" data-name="roles">@lang('roles')</th>
                                <th class="text-center p-0" data-name="orders_count">@lang('orders count')</th>
                                <th class="text-center p-0" data-name="gender">@lang('gender')</th>
                                <th class="text-center p-0" data-name="city">@lang('city')</th>
                                <th class="text-center p-0" data-name="district">@lang('district')</th>
                                <th class="text-center p-0" data-name="created_at">@lang('register date')</th>
                                <th class="text-center p-0" data-name="latest_order_at">@lang('last order date')</th>
                                <th class="text-center p-0" data-name="class">@lang('class')</th>
                                <th class="text-center p-0" data-name="actions">@lang('Actions')</th>

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
@endsection
@push('css')
@endpush
@push('js')
    <script>
        var deleteUrl = "{{ route('dashboard.users.delete', ['id'=>'%s','trash'=>request()->trash]) }}"
        $(document).ready(function() {
            let allDistricts        = {!! json_encode($districts) !!};
            $('#city_id').change(function(e) {
                e.preventDefault();
                $('#district_id').empty();
                $('#district_id').append(
                    '<option value="">{{ trans('district') }}</option>');

                districts = allDistricts.filter(function(item) {
                    return (item.city_id == $('#city_id').val())
                })

                if (districts.length) {
                    districts.forEach(item => {
                        $('#district_id').append('<option value="' + item
                            .id + '">' + item.name + '</option>');
                    });
                }

            });
           
            $(document).on('click', '.notify-btn', function(e) {
                e.preventDefault()
                $('#notifyModal').modal('show')
                $('#notifyModal [name=for]').val('users')
                let id = $(this).data('id');
                id = `[${id}]`;
                $('#notifyModal [name=for_data]').val(id)
            })
            $('.role-switch').click(function() {
                var roleId = $(this).data('role-id');
                if (roleId) {
                    $('[name=roles]').val(roleId).trigger('change');
                } else {
                    $('[name=roles]').val('').trigger('change');
                }

                $('[data-kt-user-table-filter="filter"]').trigger('click');
            });

            //notify users
            $('#notify-all-users').click(function(e) {
                e.preventDefault()
                $('#notifyModal').modal('show')
                $('#notifyModal [name=for]').val('all')
                $('#notifyModal [name=for_data]').val('')
            })
            $('#notify_selected').click(function(e) {
                e.preventDefault();
                $('#notifyModal').modal('show')
                $('#notifyModal [name=for]').val('users')

                let ids = [];
                $('input[name="table_selected"]:checked').each(function() {
                    let checkboxValue = $(this).val();
                    ids.push(checkboxValue);
                });
                ids = JSON.stringify(ids);

                $('#notifyModal [name=for_data]').val(ids)

            });

        });
    </script>
@endpush
