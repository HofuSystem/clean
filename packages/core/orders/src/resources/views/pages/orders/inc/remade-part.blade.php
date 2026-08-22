@php
$datas = $order->moreDatas->keyBy('key')->map(function ($item) {
return json_decode($item->value, true);
});
$couponMinmum = json_decode($order->coupon_data)?->order_minimum ?? ($order->coupon?->minimum_price ?? 0);

@endphp

<div id="remade">
    <div class="form-group mb-3 col-md-12">
        <div class="mt-3 items-container" data-items-on="order_id" data-items-name="representatives"
            data-items-from="order-representatives">

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
                                <a href="{{ route('dashboard.users.show', ['id' => $sItem->representative->id]) }}">
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
                            <th>{{ trans('wash type') }}</th>
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
                                @php
                                    $productObj = $item->product ?? json_decode($item->product_data);
                                    $currentLocale = app()->getLocale();

                                    // Resolve Product Name
                                    $pName = '';
                                    if ($item->product) {
                                        $pName = $item->product->name;
                                    } elseif (isset($productObj->name)) {
                                        $pName = $productObj->name;
                                    } elseif (isset($productObj->translations)) {
                                        $translations = (array) $productObj->translations;
                                        foreach ($translations as $translation) {
                                            $transArr = (array) $translation;
                                            if (($transArr['locale'] ?? '') == $currentLocale) {
                                                $pName = $transArr['name'] ?? '';
                                                break;
                                            }
                                        }
                                        if (empty($pName) && !empty($translations)) {
                                            $firstTrans = (array) reset($translations);
                                            $pName = $firstTrans['name'] ?? '';
                                        }
                                    }
                                    if (empty($pName)) {
                                        $pName = 'Product #' . ($item->product_id ?? '');
                                    }

                                    // Resolve Product Description
                                    $pDesc = '';
                                    if ($item->product) {
                                        $pDesc = $item->product->desc;
                                    } elseif (isset($productObj->desc)) {
                                        $pDesc = $productObj->desc;
                                    } elseif (isset($productObj->translations)) {
                                        $translations = (array) $productObj->translations;
                                        foreach ($translations as $translation) {
                                            $transArr = (array) $translation;
                                            if (($transArr['locale'] ?? '') == $currentLocale) {
                                                $pDesc = $transArr['desc'] ?? '';
                                                break;
                                            }
                                        }
                                        if (empty($pDesc) && !empty($translations)) {
                                            $firstTrans = (array) reset($translations);
                                            $pDesc = $firstTrans['desc'] ?? '';
                                        }
                                    }
                                    
                                    // Resolve Image
                                    $pImage = '';
                                    if (!empty($productObj->image)) {
                                        $pImage = Core\MediaCenter\Helpers\MediaCenterHelper::getImageUrl($productObj->image);
                                    }
                                    
                                    if (empty($pImage)) {
                                        $pImage = 'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" fill="%2394a3b8"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/></svg>';
                                    }
                                    
                                    $pDescClean = e(strip_tags($pDesc ?? ''));
                                    $customizations = is_string($item->customizations) ? json_decode($item->customizations, true) : $item->customizations;
                                @endphp

                                <div class="d-flex align-items-center text-start gap-2" style="gap: 10px;">
                                    <div class="product-thumbnail-container" style="flex-shrink: 0;">
                                        <img src="{{ $pImage }}" 
                                             alt="{{ $pName }}" 
                                             class="product-click-thumbnail" 
                                             data-name="{{ $pName }}" 
                                             data-image="{{ $pImage }}" 
                                             data-desc="{{ $pDescClean }}"
                                             style="width: 40px; height: 40px; object-fit: cover; border-radius: 8px; cursor: pointer; border: 1px solid #e2e8f0; transition: transform 0.2s ease, box-shadow 0.2s ease;"
                                             onmouseover="this.style.transform='scale(1.08)'; this.style.boxShadow='0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06)';"
                                             onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='none';"
                                        >
                                    </div>
                                    <div>
                                        <span class="fw-bold">{{ $pName }}</span>
                                    </div>
                                </div>

                                @if(!empty($customizations) && is_array($customizations))
                                    <div class="mt-2 text-start small border-top pt-1 text-muted">
                                        <div class="fw-bold">{{ trans('Customizations') }}:</div>
                                        @foreach($customizations as $cust)
                                            <div class="ms-2">
                                                • {{ app()->getLocale() == 'ar' ? ($cust['setting_name_ar'] ?: $cust['setting_name_en']) : ($cust['setting_name_en'] ?: $cust['setting_name_ar']) }}:
                                                <span class="text-dark">{{ app()->getLocale() == 'ar' ? ($cust['option_name_ar'] ?: $cust['option_name_en']) : ($cust['option_name_en'] ?: $cust['option_name_ar']) }}</span>
                                                @if(!empty($cust['price']))
                                                    <span class="text-success small fw-bold">(+{{ $cust['price'] }} {{ trans('SAR') }})</span>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                @if(!empty($item->card_note))
                                    <div class="mt-2 text-start small border-top pt-1">
                                        <span class="fw-bold text-primary"><i class="fas fa-sticky-note me-1 text-primary"></i>{{ trans('card note') }}:</span>
                                        <span class="text-dark fw-bold ms-1" style="font-style: italic;">"{{ $item->card_note }}"</span>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <span class="edit-wash-type text-success" data-id="{!! $item->id !!}"
                                    data-wash-type="{!! $item?->wash_type ?? (isset(json_decode($item->product_data)->wash_type) ? json_decode($item->product_data)->wash_type : '') !!}">
                                    {{ trans($item?->wash_type ?? (isset(json_decode($item->product_data)->wash_type) ?
                                    json_decode($item->product_data)->wash_type : '')) }}
                                    @isset($editMode)
                                    <i class="fas fa-edit"></i>
                                    @endisset
                                </span>
                            </td>

                            <td>{{ $item?->product?->sku ?? (isset(json_decode($item->product_data)->sku) ?
                                json_decode($item->product_data)->sku : '') }}
                            </td>
                            <td>{{ $item?->product?->category?->name ??
                                (isset(json_decode($item->product_data)->category_id) ?
                                ($categoryNames[json_decode($item->product_data)->category_id] ?? '')
                                : '') }}
                            </td>
                            <td>{{ $item?->product?->subCategory?->name ??
                                (isset(json_decode($item->product_data)->sub_category_id) ?
                                ($categoryNames[json_decode($item->product_data)->sub_category_id] ?? '')
                                : '') }}
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
                                in_array($item->product?->category_id, $hasSize) ||
                                in_array($item->product?->sub_category_id, $hasSize))
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
                                <a data-toggle="modal" data-id="{{ $order->id }}" class="open-modal-add btn-operation"
                                    title="{!! trans('add') !!}" href="#addItem">
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
                <div class="card-body  border border-dark rounded mt-2">
                    <h4 class="card-title">{{ trans('service name') }}</h4>
                    <p class="card-text">
                        {{ $datas['service_data']['name_' . config('app.locale')] ?? ($datas['service_data']['name'] ??
                        '**') }}
                    </p>
                </div>
            </div>
            @endif
            @if (isset($datas['contract_duration_data']))
            <div class="card text-center col-md-3 ">
                <div class="card-body  border border-dark rounded mt-2">
                    <h4 class="card-title">{{ trans('contract duration') }}</h4>
                    <p class="card-text">
                        {{ $datas['contract_duration_data']['name_' . config('app.locale')] ??
                        ($datas['contract_duration_data']['name'] ?? '**') }}
                    </p>
                </div>
            </div>
            @endif
            @if (isset($datas['nationality_data']))
            <div class="card text-center col-md-3 ">
                <div class="card-body  border border-dark rounded mt-2">
                    <h4 class="card-title">{{ trans('nationality') }}</h4>
                    <p class="card-text">
                        {{ $datas['nationality_data']['name_' . config('app.locale')] ??
                        ($datas['nationality_data']['name'] ?? '**') }}
                    </p>
                </div>
            </div>
            @endif
            @if (isset($datas['worker_count_data']))
            <div class="card text-center col-md-3 ">
                <div class="card-body  border border-dark rounded mt-2">
                    <h4 class="card-title">{{ trans('worker count') }}</h4>
                    <p class="card-text">
                        {{ $datas['worker_count_data']['name_' . config('app.locale')] ??
                        ($datas['worker_count_data']['name'] ?? '**') }}
                    </p>
                </div>
            </div>
            @endif
            @if (isset($datas['hours_count_data']))
            <div class="card text-center col-md-3 ">
                <div class="card-body  border border-dark rounded mt-2">
                    <h4 class="card-title">{{ trans('hours count') }}</h4>
                    <p class="card-text">
                        {{ $datas['hours_count_data']['name_' . config('app.locale')] ??
                        ($datas['hours_count_data']['name'] ?? '**') }}
                    </p>
                </div>
            </div>
            @endif
            @if (isset($datas['period_data']))
            <div class="card text-center col-md-3 ">
                <div class="card-body  border border-dark rounded mt-2">
                    <h4 class="card-title">{{ trans('duration count') }}</h4>
                    <p class="card-text">
                        {{ $datas['period_data']['name_' . config('app.locale')] ?? ($datas['period_data']['name'] ??
                        '**') }}
                    </p>
                </div>
            </div>
            @endif
            @if (isset($datas['duration_data']))
            <div class="card text-center col-md-3 ">
                <div class="card-body  border border-dark rounded mt-2">
                    <h4 class="card-title">{{ trans('duration count') }}</h4>
                    <p class="card-text">
                        {{ $datas['duration_data']['name_' . config('app.locale')] ?? ($datas['duration_data']['name']
                        ?? '**') }}
                    </p>
                </div>
            </div>
            @endif
            @if (isset($order['days_per_week']))
            <div class="card text-center col-md-3 ">
                <div class="card-body  border border-dark rounded mt-2">
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
    if ($day <= 7) { return $date->format('Y-m') . '-week1';
        }
        if ($day <= 14) { return $date->format('Y-m') . '-week2';
            }
            if ($day <= 21) { return $date->format('Y-m') . '-week3';
                }
                if ($day <= 28) { return $date->format('Y-m') . '-week4';
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
                                    <div
                                        class="appointment-card @if (Carbon\Carbon::parse($day)->isToday()) active @endif">
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
                                <h3 class="card-title">{{ trans('the order price is less than the coupon minimum') }}
                                </h3>
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
                                            <td class="p-2">{{ number_format((float)($order->order_price ?? 0), 2) }} {{ trans('SAR')
                                                }} </td>
                                        </tr>
                                        <tr>
                                            <th scope="row" class="p-2">{{ trans('discount') }}</th>
                                            <td class="p-2">{{ number_format((float)($order->total_coupon ?? 0), 2) }} {{ trans('SAR')
                                                }} </td>
                                        </tr>



                                        <tr>
                                            <th scope="row" class="p-2">{{ trans('delivery charge') }}</th>
                                            <td class="p-2"> {{ number_format((float)($order->delivery_price ?? 0), 2) }} {{
                                                trans('SAR') }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="row" class="p-2">{{ trans('total') }}</th>
                                            <td class="p-2">{{ number_format((float)($order->total_price ?? 0), 2) }} {{ trans('SAR')
                                                }} </td>
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
                                        @if (($order->wallet_amount_used ?? 0) > 0)
                                        <tr>
                                            <th scope="row" class="p-2">{{ trans('paid with wallet') }}</th>
                                            <td class="p-2">{{ number_format(abs((float)($order->wallet_amount_used ?? 0)), 2) }}
                                                {{ trans('SAR') }} </td>
                                        </tr>
                                        @endif
                                        @if (($order->points_amount_used ?? 0) > 0)
                                        <tr>
                                            <th scope="row" class="p-2">{{ trans('paid with points') }}</th>
                                            <td class="p-2">{{ number_format(abs((float)($order->points_amount_used ?? 0)), 2) }}
                                                {{ trans('SAR') }} </td>
                                        </tr>
                                        @endif
                                        @if (($order->cash_amount_used ?? 0) > 0)
                                        <tr>
                                            <th scope="row" class="p-2">{{ trans('paid with cash') }}</th>
                                            <td class="p-2">{{ number_format(abs((float)($order->cash_amount_used ?? 0)), 2) }}
                                                {{ trans('SAR') }} </td>
                                        </tr>
                                        @endif
                                        @if (($order->card_amount_used ?? 0) > 0)
                                        <tr>
                                            <th scope="row" class="p-2">{{ trans('paid with card') }}</th>
                                            <td class="p-2">
                                                {{ number_format(abs((float)($order->card_amount_used ?? 0)), 2) }}
                                                {{ trans('SAR') }} </td>
                                        </tr>
                                        @endif
                                        @if (($order->has_been_refunded ?? 0) > 0)
                                        <tr>
                                            <th scope="row" class="p-2">{{ trans('has been refunded amount') }}</th>
                                            <td class="p-2">
                                                {{ number_format(abs((float)($order->has_been_refunded ?? 0)), 2) }}
                                                {{ trans('SAR') }} </td>
                                        </tr>
                                        @endif
                                        <tr>
                                            <th scope="row" class="p-2">{{ trans('total paid') }}</th>
                                            <td class="p-2">{{ number_format(abs((float)($order->paid ?? 0)), 2) }} {{ trans('SAR') }}
                                            </td>
                                        </tr>
                                        @if ($order->total_price != $order->paid and $order->status != 'canceled')
                                        @if ($order->total_price < $order->paid)
                                            <tr>
                                                <th scope="row" class="p-2">{{ trans('remaining customer') }}</th>
                                                <td class="p-2">
                                                    {{ number_format(abs((float)(($order->total_price ?? 0) - ($order->paid ?? 0))), 2) }}
                                                    {{ trans('SAR') }}
                                                </td>
                                            </tr>
                                            @else
                                            <tr>
                                                <th scope="row" class="p-2">
                                                    {{ trans('Payment is required upon receipt') }}</th>
                                                <td class="p-2">
                                                    {{ number_format(abs((float)(($order->total_price ?? 0) - ($order->paid ?? 0))), 2) }}
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

<!-- Product Detail Modal -->
<div class="modal fade" id="productDetailInfoModal" tabindex="-1" aria-labelledby="productDetailInfoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1); overflow: hidden;">
            <div class="modal-header bg-primary text-white border-0 py-3 px-4">
                <h5 class="modal-title fw-bold text-white" id="productDetailInfoModalLabel">Product Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-4">
                <div class="mb-4 d-inline-block" style="border-radius: 8px; overflow: hidden; background: #f8fafc; border: 1px solid #e2e8f0; width: 100%; max-width: 280px; height: 200px; display: inline-flex; align-items: center; justify-content: center;">
                    <img id="productDetailModalImage" src="" alt="" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                </div>
                <div class="text-start mt-2">
                    <h6 class="fw-bold text-dark mb-2">{{ trans('description') }}</h6>
                    <p id="productDetailModalDesc" class="text-muted" style="line-height: 1.5; font-size: 0.9rem; white-space: pre-line; min-height: 50px;"></p>
                </div>
            </div>
            <div class="modal-footer border-0 bg-light py-2 px-4">
                <button type="button" class="btn btn-secondary px-4 btn-sm" style="border-radius: 6px;" data-bs-dismiss="modal">{{ trans('Close') }}</button>
            </div>
        </div>
    </div>
</div>

<!-- Script to handle clicking the thumbnail -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Function to attach click listeners
        function initProductImagePopups() {
            document.querySelectorAll('.product-click-thumbnail').forEach(function (thumb) {
                // Prevent duplicate listener attachment
                if (thumb.dataset.popupInitialized) return;
                thumb.dataset.popupInitialized = 'true';

                thumb.addEventListener('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();

                    var name = this.getAttribute('data-name') || '';
                    var image = this.getAttribute('data-image') || '';
                    var desc = this.getAttribute('data-desc') || '{{ trans('no-description') }}';

                    // Set values in the modal
                    document.getElementById('productDetailInfoModalLabel').textContent = name;
                    document.getElementById('productDetailModalImage').src = image;
                    document.getElementById('productDetailModalImage').alt = name;
                    document.getElementById('productDetailModalDesc').textContent = desc || '{{ trans('no-description') }}';

                    // Open modal dynamically, supporting both Bootstrap 5 and Bootstrap 4, or CSS fallback
                    var modalEl = document.getElementById('productDetailInfoModal');
                    if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                        var modalInstance = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                        modalInstance.show();
                    } else if (typeof jQuery !== 'undefined' && typeof jQuery.fn.modal !== 'undefined') {
                        jQuery(modalEl).modal('show');
                    } else {
                        // Fallback to custom CSS presentation
                        modalEl.style.display = 'block';
                        modalEl.style.opacity = '1';
                        modalEl.classList.add('show');
                        
                        // Create or show backdrop
                        var backdrop = document.getElementById('custom-modal-backdrop');
                        if (!backdrop) {
                            backdrop = document.createElement('div');
                            backdrop.id = 'custom-modal-backdrop';
                            backdrop.style.position = 'fixed';
                            backdrop.style.top = '0';
                            backdrop.style.left = '0';
                            backdrop.style.width = '100vw';
                            backdrop.style.height = '100vh';
                            backdrop.style.backgroundColor = 'rgba(0,0,0,0.5)';
                            backdrop.style.zIndex = '1050';
                            document.body.appendChild(backdrop);
                        }
                        backdrop.style.display = 'block';
                    }
                });
            });
        }

        initProductImagePopups();

        // Setup listeners for dismiss buttons (specifically for fallback method)
        document.querySelectorAll('#productDetailInfoModal [data-bs-dismiss="modal"], #productDetailInfoModal [data-dismiss="modal"]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var modalEl = document.getElementById('productDetailInfoModal');
                modalEl.style.display = 'none';
                modalEl.classList.remove('show');
                var backdrop = document.getElementById('custom-modal-backdrop');
                if (backdrop) {
                    backdrop.style.display = 'none';
                }
            });
        });

        // Close fallback modal if backdrop is clicked
        window.addEventListener('click', function (e) {
            var modalEl = document.getElementById('productDetailInfoModal');
            var backdrop = document.getElementById('custom-modal-backdrop');
            if (e.target === backdrop) {
                modalEl.style.display = 'none';
                modalEl.classList.remove('show');
                backdrop.style.display = 'none';
            }
        });

        // Re-initialize when AJAX updates table or content if applicable
        if (typeof jQuery !== 'undefined') {
            jQuery(document).ajaxComplete(function () {
                initProductImagePopups();
            });
        }
    });
</script>