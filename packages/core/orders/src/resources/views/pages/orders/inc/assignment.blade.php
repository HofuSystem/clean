<div class="form-group mb-3 col-md-12">
    <div class="mt-3 items-container" data-items-on = "order_id" data-items-name = "representatives"
        data-items-from     =   "order-representatives">

        <h3 class="text-dark">{{ __('representatives') }}</h3>
        <button class="btn-operation create-new-items"><i class="fas fa-plus"></i></button>
        <hr>
        <div class="table-responsive ">
            <table class="table table-bordered table-striped table-hover text-center">
                <thead class="table-primary text-capitalize h6">
                    <tr>

                        <th scope="col" data-name="type" data-type="select">{{ __('Driver Type') }}</th>
                        <th scope="col" data-name="representative_id" data-type="select">
                            {{ __('representative') }}</th>
                        <th scope="col" data-name="date" data-type="date">{{ __('date') }}
                        </th>
                        <th scope="col" data-name="time" data-type="time">{{ __('From Time') }}
                        </th>
                        <th scope="col" data-name="to_time" data-type="time">{{ __('To Time') }}
                        </th>
                        <th scope="col" data-name="address" data-type="text">{{ __('address') }}</th>

                        <th scope="col" data-name="coordinates" data-type="text">{{ __('coordinates') }}</th>

                        <th scope="col" data-name="for_all_items" data-type="checkbox">{{ __('Items OK') }}</th>
                        <th scope="col" data-name="has_problem" data-type="checkbox">{{ __('Issue?') }}</th>
                        @isset($editMode)
                            <th scope="col" data-name="actions" data-type="actions">{{ __('actions') }}</th>
                        @endisset
                    </tr>
                </thead>
                <tbody>

                    @foreach ($order->orderRepresentatives ?? [] as $sItem)
                            <tr data-id="{{ $sItem->id }}" data-data="{{ json_encode($sItem->itemData) }}">

                            <td>{{ __($sItem->type) }}</td>
                            <td>{{ $sItem?->representative?->fullname }}</td>
                            <td>{{ $sItem?->date }}</td>
                            @if ($sItem?->time)
                                <td>{{ \Carbon\Carbon::parse($sItem?->time)->format('h:i A') }}</td>
                            @else
                                <td></td>
                            @endif
                            @if ($sItem?->to_time)
                                <td>{{ \Carbon\Carbon::parse($sItem?->to_time)->format('h:i A') }}</td>
                            @else
                                <td></td>
                            @endif
                            <td>{{ $sItem->location }}</td>
                            <td>
                                @if (!empty($sItem->lat) && !empty($sItem->lng))
                                    <a href="https://www.google.com/maps?q={{ $sItem->lat }},{{ $sItem->lng }}" target="_blank">
                                        {{ $sItem->lat }}, {{ $sItem->lng }}
                                    </a>
                                @else
                                    -
                                @endif
                            <td>
                                @if($sItem->for_all_items)
                                    <span class="p-1 rounded bg-success text-white">{{ __('yes') }}</span>
                                @else
                                    <span class="p-1 rounded bg-danger text-white">{{ __('no') }}</span>
                                @endif
                            </td>
                            <td>
                                @if($sItem->has_problem)
                                    <span class="p-1 rounded bg-success text-white">{{ __('yes') }}</span>
                                @else
                                    <span class="p-1 rounded bg-danger text-white">{{ __('no') }}</span>
                                @endif
                            </td>
                            @isset($editMode)
                            <td class="options">{!! $sItem->itemsActions !!}</td>
                            @endisset
                        </tr>
                    @endforeach

                </tbody>
            </table>
        </div>
    </div>

</div>
