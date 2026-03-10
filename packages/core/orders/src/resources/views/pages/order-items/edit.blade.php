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
                            <a href=""
                                class="text-muted text-hover-primary">@lang('Home')</a>
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
            <!--end::Container-->
        </div>
        <!--end::Toolbar-->
        <!--begin::Post-->
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <!--begin::Container-->
            <div id="kt_content_container" class="container-fluid">
                <!--begin::Card-->
                <div class="card">
                    
                    <form class="form" method="POST" id="operation-form"  redirect-to="{{route("dashboard.order-items.index")}}" data-id="{{$item->id ?? null}}"
                        @isset($item)
                            action="{{ route("dashboard.order-items.edit",$item->id) }}"
                            data-mode="edit"
                        @else
                            action="{{ route("dashboard.order-items.create") }}"
                            data-mode="new"
                        @endisset
                        >

                        @csrf
                        <div class="card-body row">
                        
                            <div class="form-group mb-3 col-md-6">
                                <label class="required" for="order_id">{{ trans("order") }}</label>
                                <select class="custom-select  form-select advance-select" name="order_id" id="order_id"  >
                                    
                                    <option   value="" >{{trans("select order")}}</option>
                                    @foreach($orders ?? [] as $sItem)
                                        <option data-id="{{$sItem->id }}" @selected(isset($item)  and $item->order_id == $sItem->id) value="{{$sItem->id }}" >{{$sItem->reference_id}}</option>
                                    @endforeach
            
                                </select>
                                            
                            </div>

                            <div class="form-group mb-3 col-md-6">
                                <label class="" for="product_id">{{ trans("product") }}</label>
                                <select class="custom-select  form-select advance-select" name="product_id" id="product_id"  >
                                    
                                    <option   value="" >{{trans("select product")}}</option>
                                    @foreach($products ?? [] as $sItem)
                                        <option data-id="{{$sItem->id }}" @selected(isset($item)  and $item->product_id == $sItem->id) value="{{$sItem->id }}" >{{$sItem->name}}</option>
                                    @endforeach
            
                                </select>
                                            
                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <label class="" for="product_data">{{ trans("product data") }}</label>
                <textarea type="number" name="product_data" class="form-control "
                    placeholder="{{ trans("Enter product data") }} " >@isset($item){{ $item->product_data }}@endisset</textarea>
                    
                            </div>

                            <div class="form-group mb-3 col-md-6">
                                <label class="" for="product_price">{{ trans("product price") }}</label>
                                <input type="number" name="product_price" class="form-control "
                                    placeholder="{{ trans("Enter product price") }} " value="{{ old("product_price" , $item->product_price ?? null) }}">
                                    
                            </div>

                            <div class="form-group mb-3 col-md-6">
                                <label class="" for="quantity">{{ trans("quantity") }}</label>
                                <input type="number" name="quantity" class="form-control "
                                    placeholder="{{ trans("Enter quantity") }} " value="{{ old("quantity" , $item->quantity ?? null) }}">
                                    
                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <label class="" for="carpet_size">{{ trans("carpet size") }}</label>
                                <input type="number" name="carpet_size" class="form-control "
                                    placeholder="{{ trans("Enter carpet size") }} " value="{{ old("carpet_size" , $item->carpet_size ?? null) }}">
                                    
                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <label class="" for="add_by_admin">{{ trans("add by admin") }}</label>
                                <input type="email" name="add_by_admin" class="form-control "
                                    placeholder="{{ trans("Enter add by admin") }} " value="{{ old("add_by_admin" , $item->add_by_admin ?? null) }}">
                                    
                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <label class="" for="update_by_admin">{{ trans("update by admin") }}</label>
                                <input type="email" name="update_by_admin" class="form-control "
                                    placeholder="{{ trans("Enter update by admin") }} " value="{{ old("update_by_admin" , $item->update_by_admin ?? null) }}">
                                    
                            </div>

                            <div class="form-group mb-3 col-md-6">
          <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" id="is_picked" name="is_picked" @checked(isset($item) and $item->is_picked) >
            <label class="form-check-label" for="is_picked">{{ trans("is picked") }}</label>
        </div>
        
                            </div>

                            <div class="form-group mb-3 col-md-6">
          <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" id="is_delivered" name="is_delivered" @checked(isset($item) and $item->is_delivered) >
            <label class="form-check-label" for="is_delivered">{{ trans("is delivered") }}</label>
        </div>
        
                            </div>

                            <div class="form-group mb-3 col-md-12">
                            <div class="mt-3 items-container"
                                data-items-on       =   "item_id"
                                data-items-name     =   "qtyUpdates"
                                data-items-from     =   "order-item-qty-updates">

                                <h3 class="text-dark">{{ trans("qtyUpdates") }}</h3>
                                <button class="btn-operation create-new-items"><i class="fas fa-plus"></i></button>
                                <hr>
                                <div class="table-responsive ">
                                    <table class="table table-striped table-hover text-center" >
                                        <thead class="table-primary text-white text-capitalize h6">
                                            <tr>
                                            
                                            <th  scope="col" data-name="from" data-type="number">{{ trans("from") }}</th>
                                            <th  scope="col" data-name="to" data-type="number">{{ trans("to") }}</th>
                                            <th  scope="col" data-name="updater_email" data-type="email">{{ trans("updater email") }}</th>
                                            <th  scope="col" data-name="actions" data-type="actions">{{ trans("actions") }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            
                                        @foreach ($item->qtyUpdates ?? [] as $sItem)
                                            <tr data-id="{{ $sItem->id }}" data-data="{{ json_encode($sItem->itemData) }}" >
                                            
                                            <td>{{ $sItem->from }}</td>
                                            <td>{{ $sItem->to }}</td>
                                            <td>{{ $sItem->updater_email }}</td>
                                            <td class="options">{!! $sItem->itemsActions !!}</td>
                                            </tr>
                                        @endforeach
        
                                        </tbody>
                                    </table>
                                </div>
                            </div>
        
                            </div>

                        </div>
                        <div class="card-footer">
                            <div class="row">
                                <div class="col-lg-9 ml-lg-auto">
                                    <button type="submit" class="btn btn-primary font-weight-bold mr-2">{{ trans('save') }}</button>
                                </div>
                            </div>
                        </div>
                    </form>
        
                <div class="modal fade" id="order-item-qty-updatesModal" aria-hidden="true" aria-labelledby="order-item-qty-updatesModalLabel" data-store="{{route("dashboard.order-item-qty-updates.create")}}" >
                    <div class="modal-dialog modal-fullscreen">
                    <div class="modal-content">
                        <div class="modal-header">
                        <h1 class="modal-title fs-5" id="order-item-qty-updatesModalLabel">{{ trans("OrderItemQtyUpdate") }}</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form  class="modal-form items-modal-form" >
                                <div class="row">
                                    
                            <div class="form-group mb-3 col-md-6">
                                <label class="" for="from">{{ trans("from") }}</label>
                                <input type="number" name="from" class="form-control "
                                    placeholder="{{ trans("Enter from") }} " value="">
                                    
                            </div>

                            <div class="form-group mb-3 col-md-6">
                                <label class="required" for="to">{{ trans("to") }}</label>
                                <input type="number" name="to" class="form-control "
                                    placeholder="{{ trans("Enter to") }} " value="">
                                    
                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <label class="" for="updater_email">{{ trans("updater email") }}</label>
                                <input type="email" name="updater_email" class="form-control "
                                    placeholder="{{ trans("Enter updater email") }} " value="">
                                    
                            </div>

                                <div class="col-lg-9 ml-lg-auto">
                                    <button type="submit" class="btn btn-primary font-weight-bold mr-2">{{ trans('save') }}</button>
                                </div>
                                </div>
                            </form>
                        </div>

                    </div>
                    </div>
                </div>
                <div class="modal fade" id="order-item-qty-updatesDeleteModel" tabindex="-1" aria-labelledby="order-item-qty-updatesDeleteModelLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="order-item-qty-updatesDeleteModelLabel">{{ trans("Delete OrderItemQtyUpdate") }} <span></span></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                {{ trans("Are you sure you want to delete the OrderItemQtyUpdate") }} <span></span>?
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ trans("Close") }}</button>
                                <button type="button" class="btn btn-danger items-final-delete">{{ trans("Delete") }}</button>
                            </div>
                        </div>
                    </div>
                </div>

        
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
@endpush
