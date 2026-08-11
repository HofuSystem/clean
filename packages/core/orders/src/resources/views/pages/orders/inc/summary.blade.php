<div class="row">
    <div class="col-md-12">

      @if($order->company)
        <div class="table-responsive p-2">
            <table class="table table-bordered table-striped table-hover text-start">
                <thead class="table-primary text-center">
                    <tr>
                        <th class="p-3" colspan="2" scope="col">
                            {{ trans('company Details') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th scope="row" class="p-2">{{ trans('company name') }}</th>
                        <td class="p-2">{{ $order->company?->fullname }}</td>
                    </tr>
                    <tr>
                        <th scope="row" class="p-2">{{ trans('actions') }}</th>
                        <td class="p-2 d-flex justify-content-start">
                            @can('dashboard.companies.show')
                            <a href="{{ route('dashboard.companies.show',$order->company_id) }}" class="btn-operation"> <i
                                    class="fa fa-eye"></i> <span>{{ trans('show') }}</span></a>
                            @endcan
                            @can('dashboard.companies.edit')
                            <a href="{{ route('dashboard.companies.edit',$order->company_id) }}" class="btn-operation"> <i
                                    class="fa fa-edit"></i><span>{{ trans('edit') }}</span></a>
                            @endcan
                        </td>
                    </tr>
                    <tr>
                        <th scope="row" class="p-2">{{ trans('email') }}</th>
                        <td class="p-2">{{ $order->company?->email }}</td>
                    </tr>
                    <tr>
                        <th scope="row" class="p-2">{{ trans('phone') }}</th>
                        <td class="p-2">{{ $order->company?->phone }}</td>
                    </tr>
                
                </tbody>
            </table>
        </div>
        @endif
    </div>
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
                    @if($order->order_for)
                    <tr>
                        <th scope="row" class="p-2 text-info">{{ trans('Order For') }}</th>
                        <td class="p-2">{{ trans($order->order_for) }}</td>
                    </tr>
                    @endif
                    @if($order->recipient_name)
                    <tr>
                        <th scope="row" class="p-2 text-info">{{ trans('Recipient Name') }}</th>
                        <td class="p-2">{{ $order->recipient_name }}</td>
                    </tr>
                    @endif
                    @if($order->recipient_phone)
                    <tr>
                        <th scope="row" class="p-2 text-info">{{ trans('Recipient Phone') }}</th>
                        <td class="p-2">{{ $order->recipient_phone }}</td>
                    </tr>
                    @endif
                    @if($order->request_address !== null && $order->order_for === 'other')
                    <tr>
                        <th scope="row" class="p-2 text-info">{{ trans('Request Address From Recipient') }}</th>
                        <td class="p-2">{{ $order->request_address ? trans('yes') : trans('no') }}</td>
                    </tr>
                    @endif
                    @if($order->hide_identity !== null && $order->order_for === 'other')
                    <tr>
                        <th scope="row" class="p-2 text-info">{{ trans('Hide Sender Identity') }}</th>
                        <td class="p-2">{{ $order->hide_identity ? trans('yes') : trans('no') }}</td>
                    </tr>
                    @endif
                    <tr>
                        <th scope="row" class="p-2">{{ trans('total price') }}</th>
                        <td class="p-2">{{ number_format($order->total_price, 2) }}</td>
                    </tr>
                    @if($order->wash_type == 'washer' || $order->wash_type == 'mixed')
                    <tr>
                        <th scope="row" class="p-2 text-primary">{{ trans('washer cost') }}</th>
                        <td class="p-2 text-primary">{{ number_format($order->washer_cost, 2) }}</td>
                    </tr>
                    @endif
                    @if($order->wash_type == 'lab' || $order->wash_type == 'mixed')
                    <tr>
                        <th scope="row" class="p-2 text-info">{{ trans('lab cost') }}</th>
                        <td class="p-2 text-info">{{ number_format($order->lab_cost, 2) }}</td>
                    </tr>
                    @endif
                    <tr>
                        <th scope="row" class="p-2 text-danger">{{ trans('total cost') }}</th>
                        <td class="p-2 text-danger fw-bold">{{ number_format($order->total_cost, 2) }}</td>
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
                    @if(!empty($order->address?->image))
                    <tr>
                        <th scope="row" class="p-2">{{ trans('building image') }}</th>
                        <td class="p-2">
                            <a href="{{ Core\MediaCenter\Helpers\MediaCenterHelper::getImagesUrl($order->address->image) }}" target="_blank">
                                <img src="{{ Core\MediaCenter\Helpers\MediaCenterHelper::getImagesUrl($order->address->image) }}" class="img-thumbnail rounded" style="max-height: 120px;" alt="{{ trans('building image') }}">
                            </a>
                        </td>
                    </tr>
                    @endif
                    <tr>
                        <th scope="row" class="p-2">{{ trans('customer notes') }}</th>
                        <td class="p-2">{{ $order->note ?? "---------------------" }}</td>
                    </tr>
                    @if($order->company_id)
                    <tr>
                        <th scope="row" class="p-2">{{ trans('B2B Financial Note') }}</th>
                        <td class="p-2 text-primary fw-bold">{{ $order->b2b_financial_note ?? "---------------------" }}</td>
                    </tr>
                    @endif
                    <tr>
                        <th scope="row" class="p-2">{{ trans('operator') }}</th>
                        <td class="p-2">{{ $order->operator?->fullname ?? "---------------------" }}</td>
                    </tr>

                </tbody>
            </table>
        </div>

    </div>
    <div class="col-md-6">
      

        @if($order->client)
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
                            <a href="{{ route('dashboard.users.show',$order->client_id) }}" class="btn-operation"> <i
                                    class="fa fa-eye"></i> <span>{{ trans('show') }}</span></a>
                            @endcan
                            @can('dashboard.users.edit')
                            <a href="{{ route('dashboard.users.edit',$order->client_id) }}" class="btn-operation"> <i
                                    class="fa fa-edit"></i><span>{{ trans('edit') }}</span></a>
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
                        <td class="p-2"><span class="p-2    rounded"
                                style="background-color:{{ $customerTire['color'] }}; color:#fff  ">{{
                                trans($customerTire['type']) }}</span></td>
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

    @if($order->followUp)
    <div class="col-md-12 mt-3">
        <div class="table-responsive p-2">
            <table class="table table-bordered table-striped table-hover text-start">
                <thead class="text-center text-white" style="background-color: #28a745;">
                    <tr>
                        <th class="p-3 text-white" colspan="2" scope="col" style="background-color: #28a745;">
                            <i class="fas fa-phone-volume me-2"></i> {{ trans('Follow Up Details') }}
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th scope="row" class="p-2" style="width: 30%">{{ trans('Followed Up By (Admin)') }}</th>
                        <td class="p-2">{{ $order->followUp->admin?->fullname ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th scope="row" class="p-2">{{ trans('Followed Up At') }}</th>
                        <td class="p-2">{{ $order->followUp->followed_up_at ? Carbon\Carbon::parse($order->followUp->followed_up_at)->format('Y-m-d H:i') : '-' }}</td>
                    </tr>
                    <tr>
                        <th scope="row" class="p-2">{{ trans('Follow Up Phone') }}</th>
                        <td class="p-2">{{ $order->followUp->phone ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th scope="row" class="p-2">{{ trans('Follow Up Status') }}</th>
                        <td class="p-2">
                            <span class="badge bg-success text-white" style="background-color: #28a745; padding: 4px 8px; border-radius: 4px;">{{ trans($order->followUp->status) }}</span>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row" class="p-2">{{ trans('Follow Up Notes') }}</th>
                        <td class="p-2">{{ $order->followUp->notes ?? '-' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
<hr>
@include('orders::pages.orders.inc.remade-part')