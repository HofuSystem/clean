@extends('layouts.client')

@section('title', trans('client.monthly_invoice'))

@section('content')
  <div class="container-fluid py-4">
    <div class="row">
      <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <div>
            <h2 class="h3 mb-1 fw-bold">{{ trans('client.monthly_invoice') }}</h2>
            <p class="text-muted mb-0">{{ trans('client.manage_your_monthly_invoices') }}</p>
          </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead class="bg-light">
                <tr>
                  <th class="border-0 px-4 py-3">{{ trans('client.month') }}</th>
                  <th class="border-0 px-4 py-3 text-center">{{ trans('client.total_orders') }}</th>
                  <th class="border-0 px-4 py-3 text-center">{{ trans('client.total_amount') }}</th>
                  <th class="border-0 px-4 py-3 text-end">{{ trans('client.actions') }}</th>
                </tr>
              </thead>
              <tbody>
                @forelse($monthlyInvoices as $invoice)
                  <tr>
                    <td class="px-4 py-3">
                      <div class="d-flex align-items-center">
                        <div
                          class="icon-shape bg-soft-primary text-primary rounded-3 me-3 d-flex align-items-center justify-content-center"
                          style="width: 40px; height: 40px;">
                          <ion-icon name="calendar-outline" style="font-size: 20px;"></ion-icon>
                        </div>
                        <div>
                          <h6 class="mb-0 fw-bold">
                            {{ Carbon\Carbon::createFromDate($invoice->year, $invoice->month, 1)->translatedFormat('F Y') }}
                          </h6>
                        </div>
                      </div>
                    </td>
                    <td class="px-4 py-3 text-center">
                      <span class="badge bg-soft-info text-info px-3 py-2 rounded-pill">
                        {{ $invoice->orders_count }} {{ trans('client.orders') }}
                      </span>
                    </td>
                    <td class="px-4 py-3 text-center">
                      <span class="fw-bold text-dark h5 mb-0">
                        {{ number_format($invoice->total_amount, 2) }} {{ trans('client.SAR') }}
                      </span>
                    </td>
                    <td class="px-4 py-3 text-end">
                      <a href="{{ route('client.monthly-invoice-details', ['year' => $invoice->year, 'month' => $invoice->month]) }}"
                        class="btn btn-sm btn-outline-primary rounded-pill px-3">
                        <i class="fas fa-eye me-1"></i> {{ trans('client.details') }}
                      </a>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="4" class="text-center py-5">
                      <div class="py-4">
                        <ion-icon name="document-text-outline" class="text-muted mb-3" style="font-size: 64px;"></ion-icon>
                        <h5 class="text-muted">{{ trans('client.no_invoices_found') }}</h5>
                      </div>
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  <style>
    .bg-soft-primary {
      background-color: rgba(13, 110, 253, 0.1);
    }

    .bg-soft-info {
      background-color: rgba(13, 202, 240, 0.1);
    }

    .icon-shape {
      flex-shrink: 0;
    }

    .rounded-4 {
      border-radius: 1rem !important;
    }
  </style>
@endsection