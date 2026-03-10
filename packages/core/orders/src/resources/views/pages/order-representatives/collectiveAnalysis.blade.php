@extends('admin::layouts.dashboard')

@section('content')
    <!--begin::Content-->
    <div class="container-fluid flex-grow-1 container-p-y mx-auto">
        <div class="row">
            <form class="d-flex" method="GET" action="{{ route('dashboard.order-representatives.collectiveAnalysis') }}">
                <div class="col-10">
                   <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="" for="representative_id">{{ trans("representative") }}</label>
                            <select class="custom-select  form-select advance-select" name="representative_id" id="representative_id"  >
                                <option   value="" >{{trans("select city")}}</option>
                                @foreach($allRepresentatives ?? [] as $allRepresentative)
                                    <option data-id="{{$allRepresentative->id }}" @selected(request('representative_id') == $allRepresentative->id) value="{{$allRepresentative->id }}" >{{$allRepresentative->fullname}}</option>
                                @endforeach
        
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="" for="city_id">{{ trans("city") }}</label>
                            <select class="custom-select  form-select advance-select" name="city_id" id="city_id"  >
                                <option   value="" >{{trans("select city")}}</option>
                                @foreach($cities ?? [] as $sItem)
                                    <option data-id="{{$sItem->id }}" @selected(request('city_id') == $sItem->id) value="{{$sItem->id }}" >{{$sItem->name}}</option>
                                @endforeach
        
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="from">{{ trans('from') }}</label>
                            <input value="{{ request()->from }}" type="date" name="from" class="form-control">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="to">{{ trans('to') }}</label>
                            <input value="{{ request()->to }}" type="date" name="to" class="form-control">
                        </div>
                   </div>
                </div>
                <div class="col-2 p-2 mt-3">
                    <button class="btn btn-success mx-auto" type="submit"> {{ trans('filter') }}</button>

                </div>

            </form>

            <div class="col-12 mt-4">
                <div class="card h-100">
                    <div class="card-header">
                        <div class="d-flex justify-content-between mb-3">
                            <h5 class="card-title mb-0">{{ trans(  'collective Analysis') }}</h5>
                            <h5 class="text-muted"></h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table data-table">
                                <thead class=" table-primary">
                                    <tr>
                                        <th scope="col">{{ trans('fullname') }}</th>
                                        <th scope="col">{{ trans('total_orders_count') }}</th>
                                        <th scope="col">{{ trans('total_orders_before_discount') }}</th>
                                        <th scope="col">{{ trans('total_orders_discount') }}</th>
                                        <th scope="col">{{ trans('total_orders_delivery') }}</th>
                                        <th scope="col">{{ trans('total_orders_visa') }}</th>
                                        <th scope="col">{{ trans('total_orders_wallet') }}</th>
                                        <th scope="col">{{ trans('total_orders_points') }}</th>
                                        <th scope="col">{{ trans('total_orders_remaining') }}</th>                                      
                                        <th scope="col">{{ trans('actions') }}</th>                                            
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($representatives ?? [] as $representative)
                                        <tr class="">
                                            <td scope="row">{{ $representative->fullname }}</td>
                                            <td>{{ $representative->total_orders_count }}</td>
                                            <td>{{ $representative->total_orders_before_discount }} {{ trans('SAR') }}</td>
                                            <td>{{ $representative->total_orders_discount }} {{ trans('SAR') }}</td>
                                            <td>{{ $representative->total_orders_delivery }} {{ trans('SAR') }}</td>
                                            <td>{{ $representative->total_orders_visa }} {{ trans('SAR') }}</td>
                                            <td>{{ $representative->total_orders_wallet }} {{ trans('SAR') }}</td>
                                            <td>{{ $representative->total_orders_points }} {{ trans('SAR') }}</td>
                                            <td>{{ $representative->total_orders_remaining }} {{ trans('SAR') }}</td>
                                           
                                            <td>
                                                <div class="d-flex">
                                                    <a class="btn-operation d-flex justify-content-center align-items-center mx-1 " href="{{ route('dashboard.order-representatives.analysis', [
                                                        'representative_id' => $representative->id,
                                                        'city_id' => request('city_id'),
                                                        'from' => request('from'),
                                                        'to' => request('to')  
                                                    ]) }}"> 
                                                        <i class="fa fa-eye"></i> <span>{{ trans('view analysis') }}</span>
                                                    </a>
                                                    <a class="btn-operation d-flex justify-content-center align-items-center mx-1 " href="{{ route('dashboard.users.edit', ['id' => $representative->id]) }}"> 
                                                        <i class="fa fa-edit"></i> <span>{{ trans('edit') }}</span>
                                                    </a>

                                                </div>
                                            </td>

                                        </tr>
                                        
                                    @endforeach
                                
                                </tbody>
                             
                            </table>
                        </div>
                    </div>
                </div>
            </div>
                
            </div>

        </div>
    </div>
    <!--end::Content-->
@endsection
@push('css')
    <link rel="stylesheet" href="{{ asset('control') }}/assets/vendor/libs/apex-charts/apex-charts.css" />
    <style>
        .fab,
        .fas,
        .ti {
            font-size: 25px
        }
    </style>
@endpush
@push('js')
    <script src="{{ asset('control') }}/assets/vendor/libs/apex-charts/apexcharts.js"></script>
    <script>
        (function() {
            $('.data-table').DataTable({
                "paging": true,        
                "searching": true,    
                "ordering": true,     
                "info": true,
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf'
                ],     
            });

        })();
    </script>
@endpush
