@extends('layouts.client')

@section('title', trans('client.monthly_invoice_report'))

@section('content')
  <main class="main-content">
    <div class="container-fluid py-4">
      <!-- Dashboard Actions -->
      <div class="d-flex justify-content-between align-items-center mb-4 d-print-none">
        <div>
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
              <li class="breadcrumb-item"><a href="{{ route('client.monthly-invoices') }}"
                  class="text-decoration-none text-muted">{{ trans('client.monthly_invoice') }}</a></li>
              <li class="breadcrumb-item active" aria-current="page">
                {{ Carbon\Carbon::createFromDate($year, $month, 1)->translatedFormat('F Y') }}
              </li>
            </ol>
          </nav>
          <h2 class="h4 mb-0 fw-bold">{{ trans('client.monthly_invoice_report') }} -
            {{ Carbon\Carbon::createFromDate($year, $month, 1)->translatedFormat('F Y') }}
          </h2>
        </div>
        <div class="d-flex gap-2">
          <button onclick="window.print()" class="btn btn-primary rounded-pill px-4">
            <i class="fas fa-print me-1"></i> {{ trans('client.print') }}
          </button>
          <a href="{{ route('client.monthly-invoices') }}" class="btn btn-outline-secondary rounded-pill px-4">
            <i class="fas fa-arrow-left me-1"></i> {{ trans('client.back') }}
          </a>
        </div>
      </div>

      <!-- Statement Sheet -->
      <div class="invoice-sheet shadow-sm rounded-4 bg-white mx-auto overflow-hidden">
        <!-- Header Section -->
        <div class="p-4 p-md-5">
          <div class="row align-items-center mb-4">
            <div class="col-8">
              <div class="d-flex align-items-center gap-4">
                <img src="{{ asset('website/client/image/logo.png') }}" alt="Clean Station" class="invoice-logo">
                <div class="logo-divider"></div>
                <div class="hofu-brand text-muted">
                  <small>مدعوم من</small>
                  <div class="h5 fw-bold mb-0">HOFU</div>
                  <small>SYSTEM</small>
                </div>
              </div>
            </div>
            <div class="col-4 text-start">
              <h1 class="h3 fw-bold text-dark mb-1">كشف حساب شهري</h1>
              <div class="text-muted small">
                <span>التاريخ: {{ now()->format('d/m/Y') }}</span>
                <span class="mx-2">|</span>
                <span>الفترة: {{ sprintf('%02d/%d', $month, $year) }}</span>
              </div>
            </div>
          </div>

          <div class="header-divider mb-5"></div>

          <!-- Info Grid -->
          <div class="row g-4 mb-5">
            <!-- Client Data -->
            <div class="col-md-6 border-start-md">
              <h5 class="section-title mb-4">بيانات العميل</h5>
              <div class="info-row d-flex justify-content-between mb-3 pb-2 border-bottom border-light">
                <span class="text-muted fw-medium">اسم العميل:</span>
                <span class="text-dark fw-bold">{{ Auth::user()->fullname }}</span>
              </div>
              <div class="info-row d-flex justify-content-between mb-3 pb-2 border-bottom border-light">
                <span class="text-muted fw-medium">رقم الجوال:</span>
                <span class="text-dark fw-bold">{{ Auth::user()->phone }}</span>
              </div>
              <div class="info-row d-flex justify-content-between mb-3 pb-2 border-bottom border-light">
                <span class="text-muted fw-medium">المدينة والحي:</span>
                <span class="text-dark fw-bold">
                  {{ Auth::user()->cityName ?? '' }} - {{ Auth::user()->districtName ?? '' }}
                </span>
              </div>
              @if(isset($contract))
                <div class="info-row d-flex justify-content-between mb-3 pb-2 border-bottom border-light">
                  <span class="text-muted fw-medium">{{ trans('client.commercial_registration') }}:</span>
                  <span class="text-dark fw-bold">{{ $contract->commercial_registration ?? '-' }}</span>
                </div>
                <div class="info-row d-flex justify-content-between mb-3 pb-2 border-bottom border-light">
                  <span class="text-muted fw-medium">{{ trans('client.tax_number') }}:</span>
                  <span class="text-dark fw-bold">{{ $contract->tax_number ?? '-' }}</span>
                </div>
              @endif
            </div>

            <!-- Account Status -->
            <div class="col-md-6">
              <h5 class="section-title mb-4">حالة الحساب</h5>
              <div class="info-row d-flex justify-content-between mb-3 pb-2 border-bottom border-light">
                <span class="text-muted fw-medium">طريقة الدفع:</span>
                <span class="text-dark fw-bold">عقد شهري</span>
              </div>
              <div class="info-row d-flex justify-content-between mb-3 pb-2 border-bottom border-light">
                <span class="text-muted fw-medium">حالة الطلبات:</span>
                <span class="text-dark fw-bold">تم التسليم بنجاح</span>
              </div>
              <div class="info-row d-flex justify-content-between mb-3 pb-2 border-bottom border-light">
                <span class="text-muted fw-medium">الرصيد المستحق:</span>
                <span class="text-primary h5 mb-0 fw-bold">{{ number_format($totalAmount, 2) }}
                  {{ trans('client.SAR') }}</span>
              </div>
            </div>
          </div>

          <!-- Operational Details -->
          <div class="mb-5">
            <div class="d-flex align-items-center mb-3">
              <h5 class="section-title mb-0">تفاصيل العمليات التشغيلية</h5>
              <div class="ms-3">
                <span class="badge bg-success-soft text-success border border-success-subtle rounded-pill px-3 py-1">
                  <i class="fas fa-check-circle me-1 small"></i> خدمة الاستلام والتسليم مجانية
                </span>
              </div>
            </div>
            <div class="table-responsive rounded-3 border">
              <table class="table table-hover align-middle mb-0 text-center">
                <thead class="table-dark-custom">
                  <tr>
                    <th>رقم الطلب</th>
                    <th>التاريخ</th>
                    <th>اجمالي الطلب</th>
                    <th>الخصم</th>
                    <th>الصافي (ر.س)</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($orders as $order)
                    @php
                      $grossPrice = $order->order_price > 0 ? $order->order_price : $order->total_price;
                      $discountAmount = $grossPrice - $order->total_price;
                      $discountPercent = $grossPrice > 0 ? (round(($discountAmount / $grossPrice) * 100)) : 0;
                    @endphp
                    <tr class="{{ $loop->even ? 'bg-light-soft' : '' }}">
                      <td class="fw-bold text-dark">{{ $order->reference_id }}</td>
                      <td class="text-muted">{{ $order->created_at->format('d/m/Y') }}</td>
                      <td class="text-dark">{{ number_format($grossPrice, 2) }}</td>
                      <td class="text-dark">
                        @if($discountPercent > 0)
                          {{ $discountPercent }}%
                        @else
                          0.00%
                        @endif
                      </td>
                      <td class="fw-bold text-primary">{{ number_format($order->total_price, 2) }}</td>
                    </tr>
                  @endforeach
                </tbody>
                <tfoot class="bg-light fw-bold text-dark">
                  <tr>
                    <td colspan="4" class="text-start ps-4">اجمالي العمليات التشغيلية:</td>
                    <td>{{ number_format($totalAmount, 2) }}</td>
                  </tr>
                </tfoot>
              </table>
            </div>
          </div>

          <!-- Compensations Section -->
          <div class="mb-5">
            <h5 class="section-title mb-3">{{ trans('client.adjustments_and_compensations') }}</h5>
            <div class="table-responsive rounded-3 border">
              <table class="table align-middle mb-0 text-center">
                <thead class="table-dark-custom">
                  <tr>
                    <th>مذكرة تسوية تعويض</th>
                    <th>التاريخ</th>
                    <th>السعر الفردي</th>
                    <th>العدد</th>
                    <th>الاجمالي (ر.س)</th>
                  </tr>
                </thead>
                <tbody>
                  <tr class="bg-white">
                    <td colspan="5" class="py-5 text-muted">
                      <div class="d-flex flex-column align-items-center">
                        <i class="fas fa-info-circle mb-2 opacity-50 fs-4"></i>
                        <span class="fst-italic small">لا توجد تسويات أو تعويضات مسجلة لهذه الفترة</span>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Bottom Grid: Bank Info & Summary -->
          <div class="row g-4 align-items-end">
            <!-- Bank Info -->
            <div class="col-md-7">
              <div class="bank-info-box p-4 rounded-4 border bg-white">
                <h6 class="fw-bold text-dark mb-4 border-bottom pb-2">معلومات التحويل البنكي</h6>
                <div class="row g-3">
                  <div class="col-4 text-muted fw-medium">المستفيد:</div>
                  <div class="col-8 text-dark fw-bold">{{ $settings['name_ar'] ?? 'شركة ستيشن للنظافة' }}</div>

                  <div class="col-4 text-muted fw-medium">البنك:</div>
                  <div class="col-8 text-dark fw-bold">بنك الرياض</div>

                  <div class="col-4 text-muted fw-medium">رقم الحساب:</div>
                  <div class="col-8 text-dark fw-bold">2582503809940</div>

                  <div class="col-4 text-muted fw-medium">الايبان:</div>
                  <div class="col-8 text-dark fw-bold">SA30 2000 0002 5825 0380 9940</div>

                  <div class="col-12 mt-3 pt-2 border-top">
                    <div class="row">
                      <div class="col-6">
                        <span class="text-muted small">س.ت للشركة: </span>
                        <span class="fw-bold small">{{ $settings['clean_station_commercial_registration'] ?? '-' }}</span>
                      </div>
                      <div class="col-6">
                        <span class="text-muted small">رقم ضريبي: </span>
                        <span class="fw-bold small">{{ $settings['clean_station_tax_number'] ?? '-' }}</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Final Summary -->
            <div class="col-md-5">
              <div class="summary-final-box">
                <div class="d-flex justify-content-between p-3 border-bottom">
                  <span class="text-muted fw-bold">اجمالي العمليات التشغيلية:</span>
                  <span class="text-dark fw-bold">{{ number_format($totalAmount, 2) }} ر.س</span>
                </div>
                <div class="d-flex justify-content-between p-3 border-bottom text-danger">
                  <span class="fw-bold">اجمالي التسويات والتعويضات:</span>
                  <span class="fw-bold">{{ number_format($compensationTotal ?? 0, 2) }} ر.س-</span>
                </div>
                <div class="final-total bg-dark text-white p-3 rounded-bottom-4 d-flex justify-content-between">
                  <span class="fs-5 fw-bold">الرصيد النهائي المستحق:</span>
                  <span class="fs-5 fw-bold">{{ number_format($totalAmount - ($compensationTotal ?? 0), 2) }} ر.س</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Signatures -->
          <div class="row mt-5 pt-5 text-center">
            <div class="col-6">
              <div class="mb-5 pb-5">
                <h6 class="fw-bold text-dark">مصادقة العميل | الاسم والتوقيع</h6>
                <small class="text-muted">نقر بصحة الرصيد اعلاه</small>
              </div>
            </div>
            <div class="col-6">
              <div class="stamp-area d-flex flex-column align-items-center justify-content-center">
                <div class="stamp-placeholder rounded-circle d-flex align-items-center justify-content-center">
                  <div class="stamp-inner text-primary text-center">
                    <div class="fw-bold small mb-0">HOFU SYSTEMS</div>
                    <div class="fw-bold x-small">شركة هوفو سيستمز</div>
                    <div class="x-small">مؤسسة 10101921175 س.ت</div>
                  </div>
                </div>
                <h6 class="fw-bold text-dark mt-3">الختم</h6>
              </div>
            </div>
          </div>
        </div>

        <!-- Footer Section -->
        <div class="bg-light text-center py-4 border-top">
          <div class="text-muted x-small mb-1">
            مدعوم تقنياً وتشغيلياً بواسطة شركة هوفو سيستم
          </div>
          <div class="text-muted xsmall">
            www.cleanstation.app | support@cleanstation.app
          </div>
        </div>
      </div>
    </div>
  </main>

  <style>
    @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800&display=swap');

    .main-content {
      font-family: 'Cairo', sans-serif !important;
      background-color: #f8fafc;
      color: #2d3436;
      font-weight: 500;
    }

    .invoice-sheet {
      max-width: 1000px;
      background: #fff;
      border: 1px solid #e2e8f0;
      box-shadow: 0 20px 50px rgba(0, 0, 0, 0.05) !important;
      position: relative;
      overflow: hidden;
    }

    /* Elegant Watermark */
    .invoice-sheet::after {
      content: "CLEAN STATION";
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%) rotate(-30deg);
      font-size: 10rem;
      font-weight: 900;
      color: rgba(0, 0, 0, 0.012);
      pointer-events: none;
      white-space: nowrap;
      z-index: 0;
      letter-spacing: 20px;
    }

    .invoice-sheet>* {
      position: relative;
      z-index: 1;
    }

    .invoice-sheet::before {
      content: "";
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 12px;
      background: #2d3436;
    }

    .invoice-logo {
      max-height: 70px;
      width: auto;
    }

    .logo-divider {
      width: 2px;
      height: 50px;
      background-color: #f1f5f9;
    }

    .header-divider {
      height: 4px;
      background: repeating-linear-gradient(45deg,
          #2d3436,
          #2d3436 10px,
          #3d4446 10px,
          #3d4446 20px);
      width: 100%;
      margin-top: 2rem;
      border-radius: 4px;
    }

    .section-title {
      color: #1e293b;
      font-weight: 800;
      font-size: 1.35rem;
      border-right: 6px solid #2d3436;
      padding-right: 18px;
      margin-bottom: 2.5rem !important;
      display: inline-block;
    }

    .info-row {
      font-size: 0.95rem;
      padding: 12px 0;
      border-bottom: 1px dotted #e2e8f0;
    }

    .info-row:last-child {
      border-bottom: none;
    }

    .info-row .text-muted {
      color: #64748b !important;
      font-weight: 600;
    }

    .table-dark-custom {
      background-color: #2d3436;
      color: #ffffff;
    }

    .table thead th {
      font-weight: 700;
      font-size: 0.85rem;
      padding: 16px;
      border: none;
      vertical-align: middle;
      text-transform: uppercase;
    }

    .table tbody td {
      padding: 15px;
      font-size: 0.95rem;
      border-bottom: 1px solid #f1f5f9;
      color: #334155;
    }

    .table tfoot td {
      padding: 18px;
      font-size: 1.05rem;
      background-color: #f8fafc;
      font-weight: 700;
    }

    .bg-light-soft {
      background-color: #fbfcfd;
    }

    .bank-info-box {
      background-color: #ffffff;
      border: 2px solid #f1f5f9 !important;
      border-radius: 16px !important;
      transition: all 0.3s ease;
    }

    .summary-final-box {
      border: 2px solid #2d3436;
      border-radius: 14px;
      overflow: hidden;
      background: #fff;
    }

    .final-total {
      background-color: #2d3436 !important;
      color: #fff !important;
    }

    .stamp-placeholder {
      width: 160px;
      height: 160px;
      border: 3px solid #3b82f622;
      background-color: #eff6ff44;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      position: relative;
    }

    .stamp-inner {
      transform: rotate(-15deg);
      line-height: 1.1;
      color: #2563eb;
      border: 3px double #2563eb66;
      border-radius: 50%;
      width: 88%;
      height: 88%;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 10px;
      font-weight: 800;
    }

    .x-small {
      font-size: 9px;
    }

    .xsmall {
      font-size: 10px;
    }

    @media print {
      body {
        background-color: white !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
      }

      .sidebar,
      .navbar,
      .main-navbar,
      .footer,
      .d-print-none,
      .breadcrumb {
        display: none !important;
      }

      .main-content {
        margin: 0 !important;
        padding: 0 !important;
        background: white !important;
      }

      .container-fluid {
        padding: 0 !important;
        width: 100% !important;
        max-width: 100% !important;
      }

      .invoice-sheet {
        box-shadow: none !important;
        border: 1px solid #eee !important;
        max-width: 100% !important;
        width: 100% !important;
        border-radius: 0 !important;
        margin: 0 !important;
        position: relative !important;
      }

      .invoice-sheet::before {
        display: block !important;
        height: 10px !important;
        background: #2d3436 !important;
      }

      .table-dark-custom {
        background-color: #2d3436 !important;
        color: white !important;
      }

      .table-dark-custom th {
        background-color: #2d3436 !important;
        color: white !important;
      }

      .final-total {
        background-color: #2d3436 !important;
        color: white !important;
      }

      .section-title {
        border-right-color: #2d3436 !important;
      }

      .info-row {
        border-bottom-style: dotted !important;
        border-bottom-color: #ccc !important;
      }

      .summary-final-box {
        page-break-inside: avoid;
        border: 1px solid #2d3436 !important;
      }

      .stamp-area {
        page-break-inside: avoid;
      }

      @page {
        margin: 0.5cm;
        size: A4 portrait;
      }
    }

    [dir="rtl"] .border-start-md {
      border-right: 1.5px solid #e2e8f0;
      border-left: none;
    }

    [dir="rtl"] .section-title {
      border-right: 8px solid #2d3436;
      border-left: none;
      padding-right: 15px;
    }

    @media print {
      [dir="rtl"] .border-start-md {
        border-right: 1px solid #ccc !important;
      }
    }

@endsection