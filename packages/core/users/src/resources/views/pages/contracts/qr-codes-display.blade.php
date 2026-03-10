@extends('admin::layouts.dashboard')
@section('content')
    <!--end::Header-->
    <!--begin::Content-->
    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        <!--begin::Toolbar-->
        <div class="toolbar my-3" id="kt_toolbar">
            <!--begin::Container-->
            <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
                <!--begin::Page title-->
                <div data-kt-swapper="true" data-kt-swapper-mode="prepend"
                    data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}"
                    class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
                    <!--begin::Title-->
                    <h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1">{{ $title }}</h1>
                    <!--end::Title-->
                    <!--begin::Separator-->
                    <span class="h-20px border-gray-200 border-start mx-4"></span>
                    <!--end::Separator-->
                    <!--begin::Breadcrumb-->
                    <ul class="breadcrumb breadcrumb-separatorless fw-bold fs-7 my-1">
                        <!--begin::Item-->
                        <li class="breadcrumb-item text-muted">
                            <a href="" class="text-muted text-hover-primary">@lang('Home')</a>
                        </li>
                        <!--end::Item-->

                        <!--begin::Item-->
                        <li class="breadcrumb-item text-muted">@lang("users")</li>
                        <!--end::Item-->

                        <!--begin::Item-->
                        <li class="breadcrumb-item text-dark">{{ $title }}</li>
                        <!--end::Item-->
                    </ul>
                    <!--end::Breadcrumb-->
                </div>
                <!--end::Page title-->

            </div>
            <!--end::Container-->
        </div>
        <!--end::Toolbar-->
        <!--begin::Post-->
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <!--begin::Container-->
            <div id="kt_content_container" class="container-fluid">
                <!--begin::Card-->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ trans('QR Codes') }} ({{ count($qrCodes) }})</h3>
                        <div class="card-toolbar">
                            <button type="button" class="btn btn-primary" onclick="window.print()">
                                <i class="fas fa-print"></i> {{ trans("Print") }}
                            </button>
                            <a href="{{ route('dashboard.contracts.qr-codes.form') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> {{ trans("Back") }}
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row qr-codes-container" id="qr-codes-container">
                            @foreach($qrCodes as $index => $qrCode)
                                <div class="col-md-3 col-sm-6 mb-4 qr-code-item">
                                    <div class="card border">
                                        <div class="card-body text-center">
                                            <div class="qr-code-canvas" id="qr-{{ $index }}" data-qr="{{ $qrCode['data'] }}"></div>
                                            <h5 class="mt-3 mb-2">{{ $qrCode['owner'] }}</h5>
                                            @if($qrCode['title'])
                                                <p class="mb-0 text-muted">{{ $qrCode['title'] }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <!--end::Card-->
            </div>
            <!--end::Container-->
        </div>
        <!--end::Post-->
    </div>
    <!--end::Content-->
@endsection
@push('css')
    <style>
        /* Print styles */
        @media print {
            @page {
                size: A4;
                margin: 10mm;
            }
            
            body * {
                visibility: hidden;
            }
            
            #qr-codes-container, #qr-codes-container * {
                visibility: visible;
            }
            
            #qr-codes-container {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }
            
            .qr-code-item {
                page-break-inside: avoid;
                break-inside: avoid;
            }
            
            .card-header, .toolbar, .breadcrumb, nav {
                display: none !important;
            }
            
            .card {
                border: 1px solid #000 !important;
                box-shadow: none !important;
            }
        }
        
        .qr-code-canvas {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 200px;
        }
        
        .qr-code-canvas canvas {
            max-width: 100%;
            height: auto !important;
        }
    </style>
@endpush
@push('js')
    <!-- QR Code Library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script>
        $(document).ready(function() {
            // Generate QR codes
            $('.qr-code-canvas').each(function() {
                var qrData = $(this).data('qr');
                var elementId = $(this).attr('id');
                
                new QRCode(document.getElementById(elementId), {
                    text: "https://cleanstation.app.link/dloadqr?secretKey="+qrData,
                    width: 200,
                    height: 200,
                    colorDark: "#000000",
                    colorLight: "#ffffff",
                    correctLevel: QRCode.CorrectLevel.H
                });
            });
        });
    </script>
@endpush

