@extends('layouts.client')

@section('content')
    <!-- Main Content -->
    <main class="main-content">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-title">{{ trans('client.orders') }}</h1>
            <p class="page-subtitle">
            </p>
        </div>


        <!-- Orders Table -->
        <div class="orders-section">
            <div class="card shadow-sm border-0 p-3">
                <div class="table-responsive">
                    <table id="ordersTable" class="table table-hover dt-responsive nowrap w-100 text-center">
                        <thead class="text-center">
                            <tr>
                                <th class="text-center">{{ trans('client.created_at') }}</th>
                                <th class="text-center">{{ trans('client.order_number') }}</th>
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

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ajaxModal = document.getElementById('orderAjaxModal');
            const modalContent = document.getElementById('orderModalContent');

            if (typeof jQuery !== 'undefined' && typeof $.fn.DataTable !== 'undefined') {
                const table = $('#ordersTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: "{{ route('client.order.data') }}",
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
                        { data: 'pickup_date', name: 'pickup_date', orderable: true },
                        { data: 'delivery_date', name: 'delivery_date', orderable: false },
                        { data: 'total', name: 'total_price' },
                        { data: 'status', name: 'status' },
                        { data: 'actions', name: 'actions', orderable: false, searchable: false }
                    ],
                    drawCallback: function () {
                        // Re-initialize modal triggers after each table draw (sort, page, search)
                        const openBtns = this.api().table().container().querySelectorAll('.order-details-btn');
                        openBtns.forEach(btn => {
                            btn.onclick = function (e) {
                                e.preventDefault();
                                const orderId = this.getAttribute('data-order-id');

                                // Show loading state or clear previous content
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