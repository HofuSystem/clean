<div class="row">
    <div class="col-md-6">
        <div class="table-responsive p-2">
            <table class="table table-bordered table-striped table-hover text-start">
                <thead class="table-primary text-center">
                    <tr>
                        <th class="p-3" colspan="2" scope="col">
                            {{ trans('Order Details') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th scope="row" class="p-2">{{ trans('Order Number') }}</th>
                        <td class="p-2">{{ $order->reference_id }}</td>
                    </tr>
                    <tr>
                        <th scope="row" class="p-2">{{ trans('status') }}</th>
                        <td class="p-2">{{ trans($order->status) }}</td>
                    </tr>
                    <tr>
                        <th scope="row" class="p-2">{{ trans('auto approval') }}</th>
                        <td class="p-2">
                            @if ($order->is_admin_accepted)
                                <span class="p-1 ">{{ trans('yes') }}</span>
                            @else
                                <span class="p-1 ">{{ trans('no') }}</span>
                            @endif
                        </td>
                    </tr>
                    @if ($order->status == 'canceled')
                        <tr>
                            <th scope="row" class="p-2">{{ trans('admin reason') }}</th>
                            <td class="p-2">{{ $order->admin_cancel_reason }}</td>
                        </tr>
                    @endif
                    <tr>
                        <th scope="row" class="p-2">{{ trans('added date') }}</th>
                        <td class="p-2">{{ Carbon\Carbon::parse($order->created_at)->format('y-m-d') }}</td>
                    </tr>
                    <tr>
                        <th scope="row" class="p-2">{{ trans('category') }}</th>
                        <td class="p-2">{{ trans($order->type) }}</td>
                    </tr>
                    <tr>
                        <th scope="row" class="p-2">{{ trans('total price') }}</th>
                        <td class="p-2">{{ number_format($order->total_price, 2) }}</td>
                    </tr>
                    <tr>
                        <th scope="row" class="p-2">{{ trans('Payment Method') }}</th>
                        <td class="p-2">{{ trans($order->pay_type) }}</td>
                    </tr>
                    <tr>
                        <th scope="row" class="p-2">{{ trans('Discount Data') }}</th>
                        <td class="p-2">{{ $order->coupon_string }}</td>
                    </tr>
                    @if (isset($order->online_payment_method))
                        <tr>
                            <th scope="row" class="p-2">{{ trans('online Payment Method') }}</th>
                            <td class="p-2">{{ trans($order->online_payment_method) }}</td>
                        </tr>

                    @endif

                    <tr>
                        <th scope="row" class="p-2">{{ trans('address description') }}</th>
                        <td class="p-2">{{ $order->addressDescription ?? "---------------------" }}</td>
                    </tr>
                    <tr>
                        <th scope="row" class="p-2">{{ trans('customer notes') }}</th>
                        <td class="p-2">{{ $order->note ?? "---------------------" }}</td>
                    </tr>
                    <tr>
                        <th scope="row" class="p-2">{{ trans('operator') }}</th>
                        <td class="p-2">{{ $order->operator?->fullname ?? "---------------------" }}</td>
                    </tr>

                </tbody>
            </table>
        </div>

    </div>
    <div class="col-md-6">
        @isset($order->client)
            <div class="table-responsive p-2">
                <table class="table table-bordered table-striped table-hover text-start">
                    <thead class="table-primary text-center">
                        <tr>
                            <th class="p-3" colspan="2" scope="col">
                                {{ trans('client Details') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th scope="row" class="p-2">{{ trans('client name') }}</th>
                            <td class="p-2">{{ $order->client?->fullname }}</td>
                        </tr>
                        <tr>
                            <th scope="row" class="p-2">{{ trans('actions') }}</th>
                            <td class="p-2 d-flex justify-content-start">
                                @can('dashboard.users.show')
                                    <a href="{{ route('dashboard.users.show',$order->client_id) }}" class="btn-operation"> <i class="fa fa-eye"></i> <span>{{ trans('show') }}</span></a>
                                @endcan
                                @can('dashboard.users.edit')
                                    <a href="{{ route('dashboard.users.edit',$order->client_id) }}" class="btn-operation">  <i class="fa fa-edit"></i><span>{{ trans('edit') }}</span></a>
                                @endcan
                            </td>
                        </tr>
                        <tr>
                            <th scope="row" class="p-2">{{ trans('email') }}</th>
                            <td class="p-2">{{ $order->client?->email }}</td>
                        </tr>
                        <tr>
                            <th scope="row" class="p-2">{{ trans('phone') }}</th>
                            <td class="p-2">{{ $order->client?->phone }}</td>
                        </tr>
                        <tr>
                            <th scope="row" class="p-2">{{ trans('city') }}</th>
                            <td class="p-2">{{ $order->client?->profile?->city?->name }}</td>
                        </tr>
                        <tr>
                            <th scope="row" class="p-2">{{ trans('district') }}</th>
                            <td class="p-2">{{ $order->client?->profile?->district?->name }}</td>
                        </tr>
                        <tr>
                            <th scope="row" class="p-2">{{ trans('order count') }}</th>
                            <td class="p-2">{{ $customerOrdersCount }}</td>
                        </tr>
                        <tr>
                            <th scope="row" class="p-3">{{ trans('class') }}</th>
                            <td class="p-2"><span class="p-2    rounded"  style="background-color:{{ $customerTire['color'] }}; color:#fff  ">{{ trans($customerTire['type']) }}</span></td>
                        </tr>
                        <tr>
                            <th scope="row" class="p-2">{{ trans('wallet balance') }}</th>
                            <td class="p-2">{{ $order->client?->wallet }}</td>
                        </tr>
                        <tr>
                            <th scope="row" class="p-2">{{ trans('points balance') }}</th>
                            <td class="p-2">{{ $order->client?->points_balance }}</td>
                        </tr>
                        <tr>
                            <th scope="row" class="p-2">{{ trans('order report') }}</th>
                            <td class="p-2">{{ $order->report?->reportReason?->name ?? "---------------------" }}</td>
                        </tr>



                    </tbody>
                </table>
            </div>

        @endisset

    </div>
</div>
<hr>
@include('orders::pages.orders.inc.remade-part')
