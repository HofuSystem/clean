@extends('admin::layouts.dashboard')

@section('content')
    <!--begin::Content-->
    <div class="container-fluid flex-grow-1 container-p-y mx-auto">
        <div class="row">
            <form class="d-flex" method="GET" action="{{ route('dashboard.order-representatives.analysis') }}">
                <div class="col-10">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="" for="representative_id">{{ trans('representative') }}</label>
                            <select class="custom-select  form-select advance-select" name="representative_id"
                                id="representative_id">
                                <option value="">{{ trans('select city') }}</option>
                                @foreach ($allRepresentatives ?? [] as $allRepresentative)
                                    <option data-id="{{ $allRepresentative->id }}" @selected(request('representative_id') == $allRepresentative->id)
                                        value="{{ $allRepresentative->id }}">{{ $allRepresentative->fullname }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="" for="city_id">{{ trans('city') }}</label>
                            <select class="custom-select  form-select advance-select" name="city_id" id="city_id">
                                <option value="">{{ trans('select city') }}</option>
                                @foreach ($cities ?? [] as $sItem)
                                    <option data-id="{{ $sItem->id }}" @selected(request('city_id') == $sItem->id)
                                        value="{{ $sItem->id }}">{{ $sItem->name }}</option>
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

            @foreach ($representatives as $representative)
                @php
                    $hasToPay = 0;
                @endphp
                <div class="col-12 mt-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <div class="d-flex justify-content-between mb-3">
                                <h5 class="card-title mb-0">{{ $representative->fullname }}
                                    ({{ $representative->total_orders . ' ' . trans('orders') }})
                                </h5>
                                <h5 class="text-muted"></h5>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table data-table">
                                    <thead class=" table-primary">
                                        <tr>
                                            <th scope="col">{{ trans('added at') }}</th>
                                            <th scope="col">{{ trans('delivery or receiver') }}</th>
                                            <th scope="col">{{ trans('reference_id') }}</th>
                                            <th scope="col">{{ trans('city') }}</th>
                                            <th scope="col">{{ trans('client') }}</th>
                                            <th scope="col">{{ trans('status') }}</th>
                                            <th scope="col">{{ trans('paid') }}</th>
                                            <th scope="col">{{ trans('card') }}</th>
                                            <th scope="col">{{ trans('cash') }}</th>
                                            <th scope="col">{{ trans('wallet') }}</th>
                                            <th scope="col">{{ trans('points') }}</th>
                                            <th scope="col">{{ trans('discount') }}</th>
                                            <th scope="col">{{ trans('total order') }}</th>
                                            <th scope="col">{{ trans('notes') }}</th>
                                            <th scope="col">{{ trans('actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($representative->representativeOrders ?? [] as $order)
                                        @php
                                            $delivery = $order->orderRepresentatives->where('type','delivery')->where('representative_id',$representative->id)->first();
                                            $receiver = $order->orderRepresentatives->where('type','receiver')->where('representative_id',$representative->id)->first();
                                            if($delivery){
                                                $hasToPay += $order->cash_amount_used;
                                            }
                                        @endphp
                                            <tr class="">
                                                <td scope="row">{{ $order->created_at?->format('Y-m-d h:i: a') }}</td>
                                                <td>
                                                    @if ($receiver)
                                                        <span class="bg-primary text-white p-1 rounded-pill"> {{ trans('receiver') }}</span>
                                                    @endif

                                                    @if ($delivery)
                                                       <span class="bg-success text-white p-1 rounded-pill"> {{ trans('delivery') }} </span>
                                                    @endif
                                                </td>
                                                <td><a href="{{ route('dashboard.orders.edit', ['id' => $order->reference_id]) }}">{{ $order->reference_id ?? '---' }}</a></td>
                                                <td>{{ $order?->city?->name }}</td>
                                                <td><a href="{{ route('dashboard.users.edit', ['id' => $order?->client?->id]) }}">{{ $order?->client?->fullname }}</a></td>
                                                <td>{!! \Core\Orders\Helpers\OrderHelper::getStatusColor($order->status) !!}</td>
                                                @if ($order->paid >= 0)
                                                    <td> {{ $order->paid ?? 0 }} {{ trans('SAR') }} </td>
                                                @else
                                                    <td> <del class="text-secondary"> {{ $order->paid ?? 0 }}
                                                            {{ trans('SAR') }} </del> </td>
                                                @endif
                                                @if ($order->card_amount_used > 0)
                                                    <td> {{ $order->card_amount_used ?? 0 }} {{ trans('SAR') }} </td>
                                                @else
                                                    <td> <span class="text-secondary"> {{ $order->card_amount_used ?? 0 }}
                                                            {{ trans('SAR') }} </span> </td>
                                                @endif
                                                @if ($order->cash_amount_used > 0)
                                                    <td> {{ $order->cash_amount_used ?? 0 }} {{ trans('SAR') }} </td>
                                                @else
                                                    <td> <span class="text-secondary"> {{ $order->cash_amount_used ?? 0 }}
                                                            {{ trans('SAR') }} </span> </td>
                                                @endif
                                                @if ($order->wallet_amount_used > 0)
                                                    <td> {{ $order->wallet_amount_used ?? 0 }} {{ trans('SAR') }} </td>
                                                @else
                                                    <td> <span class="text-secondary"> {{ $order->wallet_amount_used ?? 0 }}
                                                            {{ trans('SAR') }} </span> </td>
                                                @endif
                                                @if ($order->points_amount_used > 0)
                                                    <td> {{ $order->points_amount_used ?? 0 }} {{ trans('SAR') }} </td>
                                                @else
                                                    <td> <span class="text-secondary"> {{ $order->points_amount_used ?? 0 }}
                                                            {{ trans('SAR') }} </span> </td>
                                                @endif
                                                @if ($order->total_coupon > 0)
                                                    <td> {{ $order->total_coupon ?? 0 }} {{ trans('SAR') }} </td>
                                                @else
                                                    <td> <span class="text-secondary"> {{ $order->total_coupon ?? 0 }}
                                                            {{ trans('SAR') }} </span> </td>
                                                @endif
                                                @if ($order->total_price > 0)
                                                    <td> {{ $order->total_price ?? 0 }} {{ trans('SAR') }} </td>
                                                @else
                                                    <td> <span class="text-secondary"> {{ $order->total_price ?? 0 }}
                                                            {{ trans('SAR') }} </span> </td>
                                                @endif
                                                <td>{{ $order->note ?? '---' }}</td>
                                                <td>
                                                    <a class="btn-operation d-flex justify-content-center align-items-center mx-1 "
                                                        href="{{ route('dashboard.orders.edit', ['id' => $order->id]) }}">
                                                        <i class="fa fa-edit"></i> <span>{{ trans('edit') }}</span>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="14" class="text-end">
                                                @lang('Has To Pay') : {{ $hasToPay }} @lang('SAR')
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
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
