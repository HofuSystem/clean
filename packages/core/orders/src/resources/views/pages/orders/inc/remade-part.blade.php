@php
    $datas = $order->moreDatas->keyBy('key')->map(function ($item) {
        return json_decode($item->value, true);
    });
    $couponMinmum = json_decode($order->coupon_data)?->order_minimum ?? ($order->coupon?->minimum_price ?? 0);

@endphp

<div id="remade">
    <div class="form-group mb-3 col-md-12">
        <div class="mt-3 items-container" data-items-on = "order_id" data-items-name = "representatives"
            data-items-from     =   "order-representatives">

            <h3 class="card-title">{{ trans('representatives') }}</h3>
            <div class="table-responsive ">
                <table class="table table-bordered table-striped table-hover text-center">
                    <thead class="table-primary text-capitalize h6">
                        <tr>
                            <th scope="col" data-name="type" data-type="select">{{ trans('representative type') }}
                            </th>
                            <th scope="col" data-name="representative_id" data-type="select">
                                {{ trans('representative') }}</th>
                            <th scope="col" data-name="date" data-type="date">{{ trans('date') }}</th>
                            <th scope="col" data-name="time" data-type="time">{{ trans('Time') }}</th>
                            <th scope="col" data-name="address" data-type="text">{{ trans('address') }}</th>
                            <th scope="col" data-name="coordinates" data-type="text">{{ trans('coordinates') }}</th>
                            <th scope="col" data-name="for_all_items" data-type="checkbox">{{ trans('all items') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order->orderRepresentatives ?? [] as $sItem)
                            <tr data-id="{{ $sItem->id }}" data-data="{{ json_encode($sItem->itemData) }}">
                                <td class="p-4">{{ trans($sItem->type ?? '') }}</td>
                                <td class="p-4">
                                    @if (!empty($sItem->representative))
                                        <a
                                            href="{{ route('dashboard.users.show', ['id' => $sItem->representative->id]) }}">
                                            {{ $sItem->representative->fullname }}
                                        </a>
                                    @endif
                                </td>
                                <td class="p-4">{{ $sItem->date ?? '' }}</td>

                                <td class="p-4">
                                    @if (!empty($sItem->time))
                                        {{ \Carbon\Carbon::parse($sItem->time)->format('h:i A') }}
                                    @endif
                                    -
                                    @if (!empty($sItem->to_time))
                                        {{ \Carbon\Carbon::parse($sItem->to_time)->format('h:i A') }}
                                    @endif
                                </td>


                                <td class="p-4">{{ $sItem->location ?? '' }}</td>

                                <td class="p-4">
                                    @if (!empty($sItem->lat) && !empty($sItem->lng))
                                        <a href="https://www.google.com/maps?q={{ $sItem->lat }},{{ $sItem->lng }}"
                                            target="_blank">
                                            {{ $sItem->lat }}, {{ $sItem->lng }}
                                        </a>
                                    @else
                                        -
                                    @endif
                                </td>

                                <td class="p-4">
                                    @if (!empty($sItem->for_all_items))
                                        {{ trans('yes') }}
                                    @else
                                        {{ trans('no') }}
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>
        </div>
        <hr>

    </div>

    <div class="row" id="basic-table">
        @if (in_array($order->type, ['clothes', 'services', 'sales', 'fastorder']))
            <div class="card">
                <div class="card-header pb-0 px-2">
                    <div>
                        <h3 class="card-title">{{ trans('products') }}</h3>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped text-center" id="products-table">
                        <thead class="table-primary">
                            <tr>
                                <th>{{ trans('operations') }}</th>
                                <th>{{ trans('product name') }}</th>
                                <th>{{ trans('sku') }}</th>
                                <th>{{ trans('category') }}</th>
                                <th>{{ trans('sub category') }}</th>
                                <th>{{ trans('price') }}</th>
                                <th>{{ trans('quantity') }}</th>
                                <th>{{ trans('size') }}</th>
                                <th>{{ trans('picked up') }}</th>
                                <th>{{ trans('arrived') }}</th>
                                <th>{{ trans('total') }}</th>
                                @isset($editMode)
                                    <th>{{ trans('delete') }}</th>
                                @endisset
                            </tr>
                        </thead>
                        <tbody>

                            @foreach ($orderItems as $item)
                                <tr @if ($item->deleted_at == null) data-id="{{ $item->product_id }}" @endif>
                                    <td>
                                        @if ($item->deleted_at != null)
                                            <i class="fas fa-trash-alt text-danger"></i>
                                            {{ trans('deleted') }}
                                            <br>
                                        @endif
                                        @if ($item->add_by_admin != null)
                                            <i class="far fa-plus-square text-success"></i>
                                            {{ trans('added') }}
                                            <br>
                                        @endif
                                        @if ($item->update_by_admin != null)
                                            <i class="fas fa-edit text-warning"></i>
                                            {{ trans('updated') }}
                                            <br>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $item?->product?->name ?? json_decode($item->product_data)->name }}
                                    </td>
                                    <td>{{ $item?->product?->sku ?? (isset(json_decode($item->product_data)->sku) ? json_decode($item->product_data)->sku : '') }}
                                    </td>
                                    <td>{{ $item?->product?->category?->name ?? (isset(json_decode($item->product_data)->category_id) ? Core\Categories\Models\Category::whereId(json_decode($item->product_data)->category_id)->first()?->name : '') }}
                                    </td>
                                    <td>{{ $item?->product?->subCategory?->name ?? (isset(json_decode($item->product_data)->sub_category_id) ? Core\Categories\Models\Category::whereId(json_decode($item->product_data)->sub_category_id)->first()?->name : '') }}
                                    </td>
                                    <td>{{ trans('SAR') }} {{ $item->product_price }}</td>

                                    <td>
                                        <span class="edit-quantity text-success" data-id="{!! $item->id !!}"
                                            data-quantity="{!! $item->quantity !!}"
                                            data-updates='{!! json_encode($item->qtyUpdates) !!}'>
                                            {{ $item->quantity }}
                                            @isset($editMode)
                                                <i class="fas fa-edit"></i>
                                            @endisset
                                        </span>
                                    </td>
                                    <td>
                                        @if (isset($hasSize) and
                                                in_array($item->product?->category_id, $hasSize) || in_array($item->product?->sub_category_id, $hasSize))
                                            <span class="edit-size btn border border-primary text-primary"
                                                data-id="{!! $item->id !!}" data-width="{!! $item->width !!}"
                                                data-height="{!! $item->height !!}">
                                                {!! $item->width !!}*{!! $item->height !!} =
                                                {{ $item->width * $item->height }} M
                                                @isset($editMode)
                                                    <i class="fas fa-edit"></i>
                                                @endisset
                                            </span>
                                        @else
                                            {{ trans('no-size') }}
                                        @endif
                                    </td>
                                    <td>
                                        @if ($item->is_picked)
                                            {{ trans('yes') }}
                                        @else
                                            {{ trans('no') }}
                                        @endif
                                    </td>
                                    <td>
                                        @if ($item->is_delivered)
                                            {{ trans('yes') }}
                                        @else
                                            {{ trans('no') }}
                                        @endif
                                    </td>
                                    <td>{{ trans('SAR') }} {{ $item->total_price }}</td>
                                    <td>
                                        @if (isset($editMode) )
                                            @if ($item->deleted_at != null)
                                                @if(!$item->final_delete)
                                                    <a href="{{ route('dashboard.orders.item.destroy', ['id' => $order->id, 'itemId' => $item->id , 'final' => true]) }}"
                                                        class="btn-delete-item btn-operation" title="{!! trans('final delete') !!}">
                                                        <i class="fas fa-dumpster-fire text-danger "></i>
                                                    </a>
                                                @endif
                                            @else
                                                <a href="{{ route('dashboard.orders.item.destroy', ['id' => $order->id, 'itemId' => $item->id]) }}"
                                                    class="btn-delete-item btn-operation" title="{!! trans('delete') !!}">
                                                    <i class="fas fa-trash-alt "></i>
                                                </a>
                                                
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>

                            <tr>
                                <th colspan="5"></th>
                                <th>{{ trans('total quantity') }}</th>
                                <th>{{ $order->items->sum('quantity') }}</th>
                                <th colspan="2"></th>
                                <th>{{ trans('total price') }}</th>
                                <th>{{ trans('SAR') }} {{ $order->order_price }}</th>
                                <th>
                                    {!! trans('add') !!}
                                    @isset($editMode)
                                        <a data-toggle="modal" data-id="{{ $order->id }}"
                                            class="open-modal-add btn-operation" title="{!! trans('add') !!}"
                                            href="#addItem">
                                            <i class="fas fa-plus-circle "></i>
                                        </a>
                                    @endisset
                                </th>

                            </tr>
                        </tfoot>
                    </table>
                </div>

            </div>
        @else
            <div class="py-2">
                <h3 class="card-title">{{ trans('service data') }}</h3>
            </div>
            <div class="row justify-content-center">
                @if (isset($datas['service_data']))
                    <div class="card text-center col-md-3 ">
                        <div class="card-body  border border-dark rounded">
                            <h4 class="card-title">{{ trans('service name') }}</h4>
                            <p class="card-text">
                                {{ $datas['service_data']['name_' . config('app.locale')] ?? ($datas['service_data']['name'] ?? '**') }}
                            </p>
                        </div>
                    </div>
                @endif
                @if (isset($datas['worker_count_data']))
                    <div class="card text-center col-md-3 ">
                        <div class="card-body  border border-dark rounded">
                            <h4 class="card-title">{{ trans('worker count') }}</h4>
                            <p class="card-text">
                                {{ $datas['worker_count_data']['name_' . config('app.locale')] ?? ($datas['worker_count_data']['name'] ?? '**') }}
                            </p>
                        </div>
                    </div>
                @endif
                @if (isset($datas['hours_count_data']))
                    <div class="card text-center col-md-3 ">
                        <div class="card-body  border border-dark rounded">
                            <h4 class="card-title">{{ trans('hours count') }}</h4>
                            <p class="card-text">
                                {{ $datas['hours_count_data']['name_' . config('app.locale')] ?? ($datas['hours_count_data']['name'] ?? '**') }}
                            </p>
                        </div>
                    </div>
                @endif
                @if (isset($datas['period_data']))
                    <div class="card text-center col-md-3 ">
                        <div class="card-body  border border-dark rounded">
                            <h4 class="card-title">{{ trans('duration count') }}</h4>
                            <p class="card-text">
                                {{ $datas['period_data']['name_' . config('app.locale')] ?? ($datas['period_data']['name'] ?? '**') }}
                            </p>
                        </div>
                    </div>
                @endif
                @if (isset($datas['duration_data']))
                    <div class="card text-center col-md-3 ">
                        <div class="card-body  border border-dark rounded">
                            <h4 class="card-title">{{ trans('duration count') }}</h4>
                            <p class="card-text">
                                {{ $datas['duration_data']['name_' . config('app.locale')] ?? ($datas['duration_data']['name'] ?? '**') }}
                            </p>
                        </div>
                    </div>
                @endif
                @if (isset($order['days_per_week']))
                    <div class="card text-center col-md-3 ">
                        <div class="card-body  border border-dark rounded">
                            <h4 class="card-title">{{ trans('days per week') }}</h4>
                            <p class="card-text">{{ $order['days_per_week'] }}</p>
                            @if (isset($order['days_per_week_names']))
                                <p class="card-text">{{ implode(',', json_decode($order['days_per_week_names'])) }}
                                </p>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

        @endif

    </div>
    @if ($order->days_per_month_dates)
        @php
            $dates = json_decode($order->days_per_month_dates);
            $today = \Carbon\Carbon::today();

            $grouped = collect($dates)
                ->map(fn($date) => \Carbon\Carbon::parse($date))
                ->sort()
                ->groupBy(function ($date) {
                    $day = $date->day;
                    if ($day <= 7) {
                        return $date->format('Y-m') . '-week1';
                    }
                    if ($day <= 14) {
                        return $date->format('Y-m') . '-week2';
                    }
                    if ($day <= 21) {
                        return $date->format('Y-m') . '-week3';
                    }
                    if ($day <= 28) {
                        return $date->format('Y-m') . '-week4';
                    }
                    return $date->format('Y-m') . '-week5';
                })
                ->map(function ($week, $key) use ($today) {
                    $weekDates = $week->map->toDateString()->all();
                    $isCurrentWeek = $week->contains(fn($date) => $date->isSameDay($today));

                    return [
                        'week' => $key, // e.g., "2025-04-week2"
                        'dates' => $weekDates,
                        'is_current_week' => $isCurrentWeek,
                    ];
                })
                ->values() // Reset keys
                ->all();

            $counter = 1;

        @endphp
        <div class="row match-height mt-2">
            <div class="col-12">
                <h4 class="card-title">{{ trans('visits details') }}</h4>

                @foreach ($grouped as $weekIndex => $week)
                    <div class="row mb-2">

                        <div class="col-md-3">
                            <div class="appointment-card @if ($week['is_current_week']) active @endif">
                                {{ trans('week: ') . ($loop->index + 1) }}
                            </div>
                        </div>
                        @foreach ($week['dates'] as $day)
                            <div class="col-md-3">
                                <div class="appointment-card @if (Carbon\Carbon::parse($day)->isToday()) active @endif">
                                    {{ trans('visit: ') . $counter++ }}<br>
                                    <small>{{ $day . ' ' . \Carbon\Carbon::parse($day)->format('l') }}</small>
                                </div>
                            </div>
                        @endforeach

                    </div>
                @endforeach

            </div>


        </div>
    @endif

    <hr>
    <div class="row match-height">
        <div class="col-12">
            @if ($couponMinmum > $order->order_price)
                <div class="alert alert-danger">
                    <h3 class="card-title">{{ trans('the order price is less than the coupon minimum') }}</h3>
                </div>
            @endif

            <div class="table-responsive p-2">
                <table class="table table-bordered table-striped table-hover text-start">
                    <thead class="table-primary text-center">
                        <tr>
                            <th class="p-3" colspan="2" scope="col">
                                {{ trans('bill details') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th scope="row" class="p-2">{{ trans('total order') }}</th>
                            <td class="p-2">{{ number_format($order->order_price, 2) }} {{ trans('SAR') }} </td>
                        </tr>
                        <tr>
                            <th scope="row" class="p-2">{{ trans('discount') }}</th>
                            <td class="p-2">{{ number_format($order->total_coupon, 2) }} {{ trans('SAR') }} </td>
                        </tr>

                       

                        <tr>
                            <th scope="row" class="p-2">{{ trans('delivery charge') }}</th>
                            <td class="p-2"> {{ number_format($order->delivery_price, 2) }} {{ trans('SAR') }}
                            </td>
                        </tr>
                        <tr>
                            <th scope="row" class="p-2">{{ trans('total') }}</th>
                            <td class="p-2">{{ number_format($order->total_price, 2) }} {{ trans('SAR') }} </td>
                        </tr>

                    </tbody>
                </table>
                <hr class="my-4">
                <table class="table table-bordered table-striped table-hover text-start ">
                    <thead class="table-primary text-center">
                        <tr>
                            <th class="p-3" colspan="2" scope="col">
                                {{ trans('payment details') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($order->wallet_amount_used > 0)
                            <tr>
                                <th scope="row" class="p-2">{{ trans('paid with wallet') }}</th>
                                <td class="p-2">{{ number_format(abs($order->wallet_amount_used), 2) }}
                                    {{ trans('SAR') }} </td>
                            </tr>
                        @endif
                        @if ($order->points_amount_used > 0)
                            <tr>
                                <th scope="row" class="p-2">{{ trans('paid with points') }}</th>
                                <td class="p-2">{{ number_format(abs($order->points_amount_used), 2) }}
                                    {{ trans('SAR') }} </td>
                            </tr>
                        @endif
                        @if ($order->cash_amount_used > 0)
                            <tr>
                                <th scope="row" class="p-2">{{ trans('paid with cash') }}</th>
                                <td class="p-2">{{ number_format(abs($order->cash_amount_used), 2) }}
                                    {{ trans('SAR') }} </td>
                            </tr>
                        @endif
                        @if ($order->card_amount_used > 0)
                            <tr>
                                <th scope="row" class="p-2">{{ trans('paid with card') }}</th>
                                <td class="p-2">
                                    {{ number_format(abs($order->card_amount_used), 2) }}
                                    {{ trans('SAR') }} </td>
                            </tr>
                        @endif
                        @if ($order->has_been_refunded > 0)
                            <tr>
                                <th scope="row" class="p-2">{{ trans('has been refunded amount') }}</th>
                                <td class="p-2">
                                    {{ number_format(abs($order->has_been_refunded), 2) }}
                                    {{ trans('SAR') }} </td>
                            </tr>
                        @endif
                        <tr>
                            <th scope="row" class="p-2">{{ trans('total paid') }}</th>
                            <td class="p-2">{{ number_format(abs($order->paid), 2) }} {{ trans('SAR') }}
                            </td>
                        </tr>
                        @if ($order->total_price != $order->paid and $order->status != 'canceled')
                            @if ($order->total_price < $order->paid)
                                <tr>
                                    <th scope="row" class="p-2">{{ trans('remaining customer') }}</th>
                                    <td class="p-2">
                                        {{ number_format(abs($order->total_price - $order->paid), 2) }}
                                        {{ trans('SAR') }}
                                    </td>
                                </tr>
                            @else
                                <tr>
                                    <th scope="row" class="p-2">
                                        {{ trans('Payment is required upon receipt') }}</th>
                                    <td class="p-2">
                                        {{ number_format(abs($order->total_price - $order->paid), 2) }}
                                        {{ trans('SAR') }}
                                    </td>
                                </tr>
                            @endif

                        @endif


                    </tbody>
                </table>
              
            </div>

        </div>

    </div>
</div>

