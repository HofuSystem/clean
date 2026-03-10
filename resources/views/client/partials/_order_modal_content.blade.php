<div class="modal-header">
  <div class="d-flex gap-2 align-items-center">
    <h3 class="modal-title">{{ $order->reference_id }}</h3>
  </div>
  <div class="d-flex gap-2 align-items-center">
    <a href="{{ route('client.order.invoice', $order->id) }}" target="_blank"
      class="btn btn-sm btn-outline-primary shadow-sm">
      <i class="fas fa-file-invoice me-1"></i> {{ trans('client.invoice') }}
    </a>
    <button type="button" class="btn-close modal-close-btn" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
</div>

<div class="modal-body">
  <!-- Order Dates -->
  <div class="order-dates">
    <div class="date-item">
      <span class="date-label">{{ __('client.pickup_date') }}</span>
      <span class="date-value">
        {{ $order->orderRepresentatives->where('type', 'receiver')->first()?->date }}
        <br>
        {{ $order->orderRepresentatives->where('type', 'receiver')->first()?->time_12_hours_format }} :
        {{ $order->orderRepresentatives->where('type', 'receiver')->first()?->to_time_12_hours_format }}
      </span>
    </div>
    <div class="date-item">
      <span class="date-label">{{ __('client.delivery_date') }}</span>
      <span class="date-value">
        {{ $order->orderRepresentatives->where('type', 'delivery')->first()?->date }}
        <br>
        {{ $order->orderRepresentatives->where('type', 'delivery')->first()?->time_12_hours_format }} :
        {{ $order->orderRepresentatives->where('type', 'delivery')->first()?->to_time_12_hours_format }}
      </span>
    </div>
  </div>
  <div class="mb-3 d-flex justify-content-center align-items-center gap-2">
    <h4 class="section-title mb-0">{{ __('client.order_tracking') }}</h4>
    <div class="status-title">{{ __($order->status) }}</div>
  </div>
  <!-- Order Status -->
  <div class="order-status">
    <div class="status-timeline">
      @foreach ($order->orderTracking as $status)
        <div class="timeline-step {{ $status['is_checked'] ? 'active' : '' }}">
          <span class="step-label">{{ $status['status'] }}</span>
          <div class="step-circle"></div>
          @if (is_array($status['time']))
            <span class="step-date">{{ is_array($status['time']) ? $status['time'][0] : $status['time'] }}</span>
          @endif
        </div>
      @endforeach
    </div>
  </div>

  <!-- Order Content -->
  <div class="order-content">
    <!-- Order Tracking -->
    <div class="tracking-section">
      <h4 class="section-title">{{ __('client.payment_details') }}</h4>

      <!-- Payment Details -->
      <div class="payment-details">
        <div class="details-row">
          <span class="details-label">{{ __('client.sub_total') }}</span>
          <span class="details-value">{{ $order->total_price ?? 0 }}
            {{ __('client.SAR') }}</span>
        </div>

        <div class="details-row">
          <span class="details-label">{{ __('client.delivery_price') }}</span>
          <span class="details-value">{{ $order->delivery_price ?? 0 }}
            {{ __('client.SAR') }}</span>
        </div>
        <div class="details-row total">
          <span class="details-label">{{ __('client.total') }}</span>
          <span class="details-value">{{ $order->total_price + $order->delivery_price ?? 0 }}
            {{ __('client.SAR') }}</span>
        </div>
      </div>
    </div>

    <!-- Cart Section -->
    <div class="cart-section mt-4">
      <h4 class="section-title mb-3">{{ __('client.cart') }}</h4>
      <div class="table-responsive">
        <table class="table cart-table border-0">
          <thead>
            <tr>
              <th class="border-0 bg-light-primary rounded-start">
                {{ trans('client.product_name') }}
              </th>
              <th class="border-0 bg-light-primary text-center">{{ trans('client.quantity') }}
              </th>
              <th class="border-0 bg-light-primary text-center">{{ trans('client.price') }}</th>
              <th class="border-0 bg-light-primary text-center rounded-end">
                {{ trans('client.sub_total') }}
              </th>
            </tr>
          </thead>
          <tbody>
            @foreach ($order->items as $item)
              <tr>
                <td class="border-0">
                  <div class="d-flex align-items-center gap-3">
                    <div>
                      <h6 class="mb-0 fw-bold">{{ $item->product->name }}</h6>
                      <small class="text-muted">{{ $item->product->description }}</small>
                    </div>
                  </div>
                </td>
                <td class="border-0 text-center align-middle">
                  <span class="badge bg-light text-dark px-3 py-2 border">{{ $item->quantity }}</span>
                </td>
                <td class="border-0 text-center align-middle fw-medium">
                  {{ $item->product_price }} {{ __('client.SAR') }}
                </td>
                <td class="border-0 text-center align-middle fw-bold text-primary">
                  {{ $item->quantity * $item->product_price }} {{ __('client.SAR') }}
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>