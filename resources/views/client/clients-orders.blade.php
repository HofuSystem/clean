@extends('layouts.client')

@section('content')
    <main class="main-content">
        <!-- Quick Action Banner -->
        <div
            class="hero-banner bg-gradient-primary text-white p-4 rounded mb-4 d-flex justify-content-between align-items-center">
            <div>
                <h1 class="hero-title mb-1">{{ __('client.welcome') }}, {{ $user->fullname ?? __('client.valued_partner') }}
                </h1>
                <p class="hero-subtitle">{{ __('client.dashboard_subtitle') }}</p>
            </div>

        </div>

        <!-- Stats Cards -->
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body text-center">
                        <h6 class="text-muted">{{ __('client.total_orders') }}</h6>
                        <h3 class="fw-bold text-primary">{{ $totalOrdersCount ?? 0 }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm border-0 h-100 ">
                    <div class="card-body text-center">
                        <h6 class="text-muted">{{ __('client.total_clients_orders') }}</h6>
                        <h3 class="fw-bold text-dark">{{ number_format($ordersTotalPrice ?? 0, 2) }}
                            {{ __('client.SAR') }}
                        </h3>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body text-center">
                        <h6 class="text-muted">{{ __('client.total_profit') }}</h6>
                        <h3 class="fw-bold text-success">{{ number_format($totalProfit ?? 0, 2) }}
                            {{ __('client.SAR') }}
                        </h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Orders Table -->
        <div class="orders-section mt-5">
            <div class="card shadow-sm border-0 p-3">
                <div class="table-responsive">
                    <table id="clientsOrdersTable" class="table table-hover dt-responsive nowrap w-100 text-center">
                        <thead class="text-center">
                            <tr>
                                <th class="text-center">{{ trans('client.created_at') }}</th>
                                <th class="text-center">{{ trans('client.order_number') }}</th>
                                <th class="text-center">{{ trans('client.customer_name') }}</th>
                                <th class="text-center">{{ trans('client.customer_phone') }}</th>
                                <th class="text-center">{{ trans('client.note') }}</th>
                                <th class="text-center">{{ trans('client.pickup_date') }}</th>
                                <th class="text-center">{{ trans('client.delivery_date') }}</th>
                                <th class="text-center">{{ trans('client.total') }}</th>
                                <th class="text-center">{{ trans('client.status') }}</th>
                                <th class="text-center">{{ trans('client.details') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <!-- AJAX Order Modal -->
    <div class="order-modal-backdrop" id="orderAjaxModal">
        <div class="order-modal" id="orderModalContent">
            <!-- Content will be loaded via AJAX -->
        </div>
    </div>

    <style>
        .bg-gradient-primary {
            background: linear-gradient(to right, #1a7fbe, #4876b2);
        }

        .text-white h1,
        .text-white p {
            color: #fff !important;
        }

        .hero-title {
            font-size: 28px;
        }

        .hero-subtitle {
            font-size: 16px;
            opacity: 0.9;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ajaxModal = document.getElementById('orderAjaxModal');
            const modalContent = document.getElementById('orderModalContent');

            if (typeof jQuery !== 'undefined' && typeof $.fn.DataTable !== 'undefined') {
                const table = $('#clientsOrdersTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: "{{ route('client.clientsOrders.data') }}",
                    responsive: true,
                    order: [[0, 'desc']],
                    language: {
                        url: "{{ LaravelLocalization::getCurrentLocale() == 'ar' ? 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/ar.json' : '' }}"
                    },
                    pageLength: 10,
                    lengthMenu: [10, 25, 50, 100],
                    columnDefs: [
                        { targets: "_all", className: "text-center" }
                    ],
                    columns: [
                        { data: 'created_at', name: 'created_at' },
                        { data: 'reference_id', name: 'reference_id' },
                        { data: 'customer_name', name: 'customer_name' },
                        { data: 'customer_phone', name: 'customer_phone' },
                        { data: 'note', name: 'note' },
                        { data: 'pickup_date', name: 'pickup_date', orderable: true },
                        { data: 'delivery_date', name: 'delivery_date', orderable: false },
                        { data: 'total', name: 'total_price' },
                        { data: 'status', name: 'status' },
                        { data: 'actions', name: 'actions', orderable: false, searchable: false }
                    ],
                    drawCallback: function () {
                        // Re-initialize modal triggers after each table draw
                        const openBtns = this.api().table().container().querySelectorAll('.order-details-btn');
                        openBtns.forEach(btn => {
                            btn.onclick = function (e) {
                                e.preventDefault();
                                const orderId = this.getAttribute('data-order-id');

                                // Show loading state
                                modalContent.innerHTML = '<div class="p-5 text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>';
                                ajaxModal.classList.add('active');
                                document.body.style.overflow = 'hidden';

                                // Fetch order details via AJAX
                                const showRoute = "{{ route('client.order.show', ['id' => ':id']) }}".replace(':id', orderId);
                                fetch(showRoute)
                                    .then(response => response.text())
                                    .then(html => {
                                        modalContent.innerHTML = html;

                                        // Re-bind close button
                                        const closeBtn = modalContent.querySelector('.modal-close-btn');
                                        if (closeBtn) {
                                            closeBtn.onclick = function () {
                                                ajaxModal.classList.remove('active');
                                                document.body.style.overflow = 'auto';
                                            };
                                        }
                                    })
                                    .catch(error => {
                                        console.error('Error fetching order details:', error);
                                        modalContent.innerHTML = '<div class="p-5 text-center text-danger">Error loading details. Please try again.</div>';
                                    });
                            };
                        });
                    }
                });
            }

            // Close modal on backdrop click
            ajaxModal.addEventListener('click', function (e) {
                if (e.target === ajaxModal) {
                    ajaxModal.classList.remove('active');
                    document.body.style.overflow = 'auto';
                }
            });
        });
    </script>
@endsection