
<div class="row">
    <div class="col-12">
        <div class="table-responsive p-2">
            <table class="table table-bordered table-striped table-hover text-center">
                <thead class="table-primary text-center">
                    <tr>
                        <th class="p-3" colspan="2" scope="col">
                            {{ trans('item summary') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th  class="p-2">{{ trans('Total Items') }}</th>
                        <td class="p-2">{{ trans('SKU Count') }}</td>
                    </tr>
                    <tr>
                        <td  class="p-2">{{ $order->items->sum('quantity') }}</td>
                        <td class="p-2">
                            {{ $order->items->pluck('product_id')->unique()->count() }}
                        </td>
                    </tr>

                </tbody>
            </table>
        </div>
    </div>
    <hr>

            
    <div class="card-body p-0">
        <div class="table-responsive p-2">
            <table class="table table-bordered table-striped table-hover text-center">
                <thead class="table-primary text-center">
                    <tr>
                        <th class="p-3" colspan="6" scope="col">
                            {{ trans('status history summary') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th class="p-2">{{ trans('notes') }}</th>
                        <td class="p-2">{{ trans('email') }}</td>
                        <td class="p-2">{{ trans('date') }}</td>
                        <td class="p-2">{{ trans('day') }}</td>
                        <th class="p-2">{{ trans('from status') }}</th>
                        <th class="p-2">{{ trans('to status') }}</th>
                    </tr>
                    @foreach ($order->statusChanges as $index =>  $statusChange)
                        <tr>
                            <td  class="p-2">{{ isset($statusChange?->notes) ?  trans($statusChange?->notes) : null }}</td>
                            <td  class="p-2">{{ (isset($statusChange->email) and !empty($statusChange->email)) ? $statusChange->email : trans('system') }}</td>
                            <td  class="p-2">{{ $statusChange->date  ? Carbon\Carbon::parse($statusChange->date)->format('n/j/y H:i') : null }}</td>
                            <td  class="p-2">{{ $statusChange->day }}</td>
                            <td  class="p-2">{{ isset($order->statusChanges[$index-1]) ? trans($order->statusChanges[$index-1]->status) : null }}</td>
                            <td  class="p-2">{{ trans($statusChange->status) }}</td>

                        </tr>
                    @endforeach

                </tbody>
            </table>
        </div>
        @if(isset($orderHistories) && $orderHistories->count() > 0)
            <div class="table-responsive p-4">
                <table class="table table-bordered table-striped table-hover align-middle">
                    <thead class="table-primary">
                        <tr>
                            <th class="text-center">{{ trans('#') }}</th>
                            <th class="text-center">{{ trans('Type/Notes') }}</th>
                            <th class="text-center">{{ trans('Before') }}</th>
                            <th class="text-center">{{ trans('After') }}</th>
                            <th class="text-center" style="width: 200px;">{{ trans('Changed By') }}</th>
                            <th class="text-center">{{ trans('Changed At') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orderHistories->reverse() as $history)
                            <tr>
                                <td>
                                    <span class="fw-bold text-white bg-primary rounded p-2">{{ $loop->iteration }}</span>
                                </td>
                                {{-- Type/Notes Column --}}
                                <td>
                                    <div class="fw-bold text-dark">
                                        <h1>{{ trans($history->action_type ?? 'Unknown Action') }}</h1>
                                        <p> <p class="fw-bold text-dark" style="direction: rtl; width: fit-content;"> {{ $history->notes }}</p></p>
                                    </div>
                                </td>
                                
                                {{-- Before Column --}}
                                <td>
                                    @if($history->old_value)
                                        <div class="bg-light-danger rounded p-2 border border-danger border-dashed">
                                            @foreach($history->old_value as $key => $value)
                                                <div class="mb-1">
                                                    <small class="fw-bold text-dark d-block">{{ trans($key) }}:</small>
                                                    <small class="text-gray-700">
                                                        @if(is_array($value))
                                                            @foreach($value as $key => $item)
                                                                <div class="mb-1">
                                                                    <small class="fw-bold text-dark d-block">{{ trans($key) }}:</small>
                                                                    <small class="text-danger">{{ $item }}</small>
                                                                </div>
                                                            @endforeach
                                                        @else
                                                            <span class="text-danger">{{ is_string($value) ? trans($value) : $value }}</span>
                                                        @endif
                                                    </small>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                
                                {{-- After Column --}}
                                <td>
                                    @if($history->new_value)
                                        <div class="bg-light-success rounded p-2 border border-success border-dashed">
                                            @foreach($history->new_value as $key => $value)
                                                <div class="mb-1">
                                                    <small class="fw-bold text-dark d-block">{{ trans($key) }}:</small>
                                                    <small class="text-gray-700">
                                                        @if(is_array($value))
                                                            @foreach($value as $key => $item)
                                                                <div class="mb-1">
                                                                    <small class="fw-bold text-dark d-block">{{ trans($key) }}:</small>
                                                                    <small class="text-success">{{ $item }}</small>
                                                                </div>
                                                            @endforeach
                                                        @else
                                                            <span class="text-success">{{ is_string($value) ? trans($value) : $value }}</span>
                                                        @endif
                                                    </small>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                
                                {{-- Changed By Column --}}
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="symbol symbol-circle symbol-35px me-2" style="background: #4a79b5;">
                                            <span class="symbol-label text-white fs-6 fw-bold">
                                                @if($history->changed_by)
                                                    {{ mb_substr($history->changed_by['name'] ?? trans('System'), 0, 1) }}
                                                @else
                                                    <i class="fas fa-robot fs-7"></i>
                                                @endif
                                            </span>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="fw-bold text-gray-800 fs-7">
                                                {{ $history->changed_by['name'] ?? trans('System') }}
                                            </div>
                                            @if($history->changed_by)
                                                @if(isset($history->changed_by['email']))
                                                    <div class="text-muted fs-8">
                                                        <i class="fas fa-envelope me-1"></i>{{ $history->changed_by['email'] }}
                                                    </div>
                                                @endif
                                                @if(isset($history->changed_by['phone']))
                                                    <div class="text-muted fs-8">
                                                        <i class="fas fa-phone me-1"></i>{{ $history->changed_by['phone'] }}
                                                    </div>
                                                @endif
                                            @else
                                                <div class="text-muted fs-8">
                                                    <i class="fas fa-info-circle me-1"></i>
                                                    {{ trans('Automatic system change') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                
                                <td>
                                    <div class="fw-bold text-dark">
                                        {{ $history->created_at->format('Y-m-d h:i:a') }}
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="alert alert-info m-6 d-flex align-items-center">
                <i class="fas fa-info-circle fs-2 me-3"></i> 
                <span>{{ trans('No history records found for this order.') }}</span>
            </div>
        @endif
    </div>

</div>
