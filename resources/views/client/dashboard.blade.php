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
            <button href="{{ route('client.order.store') }}" class="btn btn-lg btn-light shadow-sm fw-bold feature-btn"
                id="pickup-now-btn">
                <i class="fa fa-truck me-2"></i> {{ __('client.create_order') }}
            </button>
        </div>

        <!-- Stats Cards -->
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body text-center">
                        <h6 class="text-muted">{{ __('client.total_orders') }}</h6>
                        <h3 class="fw-bold text-primary">{{ $totalOrders ?? 0 }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm border-0 h-100 ">
                    <div class="card-body text-center">
                        <h6 class="text-muted">{{ __('client.monthly_invoice') }}</h6>
                        <h3 class="fw-bold text-dark">{{ number_format($monthlyInvoiceTotal ?? 0, 2) }}
                            {{ __('client.SAR') }}</h3>
                        
                        <a href="{{ route('client.monthly-invoices') }}" class="btn btn-link btn-sm text-primary p-0">
                            <i class="fas fa-arrow-right me-1"></i> {{ trans('client.view_all') }}
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body text-center">
                        <h6 class="text-muted">{{ __('client.linked_branches') }}</h6>
                        <h3 class="fw-bold text-success">{{ $branchCount ?? 1 }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <!-- Filter Buttons -->
        <div class="header-section mb-1 mt-5">
            <div class="header-buttons">
                <button class="btn">{{ trans('client.order_number') }}</button>
                <button class="btn">{{ trans('client.pickup_date') }}</button>
                <button class="btn">{{ trans('client.delivery_date') }}</button>
                <button class="btn">{{ trans('client.total') }}</button>
                <button class="btn active">{{ trans('client.details') }}</button>
            </div>
        </div>
        <!-- Orders Table -->
        <div class="orders-section">
            <div class="orders-container">
                @foreach ($orders as $order)
                    <!-- Order Item -->
                    <div class="order-item">
                        <div class="order-id">
                            <span class="label">{{ trans('client.order_number') }} : </span>
                            <span>{{ $order->reference_id }}</span>
                        </div>
                        <div class="order-date">
                            <div class="date-created">
                                <span class="label">{{ __('client.pickup_date') }} : </span>
                                <span>{{ $order->orderRepresentatives->where('type', 'receiver')->first()?->date }}</span>
                                <br>
                                <span>{{ $order->orderRepresentatives->where('type', 'receiver')->first()?->time_12_hours_format }} :
                                    {{ $order->orderRepresentatives->where('type', 'receiver')->first()?->to_time_12_hours_format }}</span>
                            </div>
                        </div>
                        <div class="order-date">
                            <div class="date-created">
                                <span class="label">{{ __('client.delivery_date') }} : </span>
                                <span>{{ $order->orderRepresentatives->where('type', 'delivery')->first()?->date }}</span>
                                <br>
                                <span>{{ $order->orderRepresentatives->where('type', 'delivery')->first()?->time_12_hours_format  }} :
                                    {{ $order->orderRepresentatives->where('type', 'delivery')->first()?->to_time_12_hours_format   }}</span>
                            </div>
                        </div>
                        <div class="order-price">
                            <span class="label">{{ __('client.total') }} : </span>
                            <span class="price-amount">{{ $order->total_price ?? 0 }} {{ __('client.SAR') }}</span>
                        </div>
                        <div class="order-action">
                            <button class="order-details-btn"
                                data-order-id="{{ $order->id }}">{{ __('client.order_details') }}</button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        @foreach ($orders as $order)
            <!-- Order Details Modal -->
            <div class="order-modal-backdrop" id="orderDetailsModal{{ $order->id }}">
                <div class="order-modal">
                    @include('client.partials._order_modal_content', ['order' => $order])
                </div>
            </div>
        @endforeach
        <!-- Order Now Modal -->
        <div class="order-now-modal-backdrop" id="orderNowModal">
            <div class="order-now-modal">
                <div class="modal-header">
                    <h3 class="modal-title">{{ trans('client.order_now') }}</h3>
                </div>

                <div class="modal-body">


                    <form class="order-form" id="orderNowForm" action="{{ route('client.order.store') }}"
                        method="POST">
                        @csrf
                        <div class="mb-4">
                            <div class="form-group mb-3">
                                <label class="form-label d-block fw-bold">{{ trans('client.order_for') }}</label>
                                <div class="btn-group w-100" role="group">
                                    <input type="radio" class="btn-check" name="order_type" id="order_for_me" value="me"
                                        checked>
                                    <label class="btn btn-outline-primary"
                                        for="order_for_me">{{ trans('client.order_for_me') }}</label>

                                    <input type="radio" class="btn-check" name="order_type" id="order_for_customer"
                                        value="customer">
                                    <label class="btn btn-outline-primary"
                                        for="order_for_customer">{{ trans('client.order_for_customer') }}</label>
                                </div>
                            </div>

                            <div id="customer_fields_container" style="display: none;">
                                <div class="customer-info-card p-3 rounded-3 mb-3" style="background: #f8fbff; border: 1px dashed #213861;">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group mb-3">
                                                <label for="customer_name" class="form-label fw-bold">{{ trans('client.customer_name') }} <span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <span class="input-group-text bg-white border-end-0"><ion-icon name="person-outline"></ion-icon></span>
                                                    <input type="text" id="customer_name" name="customer_name" class="form-control custom border-start-0" placeholder="{{ trans('client.enter_name') ?? 'Full Name' }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group mb-3">
                                                <label for="customer_phone" class="form-label fw-bold">{{ trans('client.customer_phone') }} <span class="text-danger">*</span></label>
                                                <div class="input-group" dir="ltr">
                                                    <span class="input-group-text bg-white border-end-0">+966</span>
                                                    <input type="text" id="customer_phone" name="customer_phone" class="form-control custom border-start-0" placeholder="5XXXXXXXX">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group mb-3">
                                                <label for="customer_email" class="form-label fw-bold">{{ trans('client.customer_email') }}</label>
                                                <div class="input-group">
                                                    <span class="input-group-text bg-white border-end-0"><ion-icon name="mail-outline"></ion-icon></span>
                                                    <input type="email" id="customer_email" name="customer_email" class="form-control custom border-start-0" placeholder="example@domain.com">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Pickup Location -->

                        <!-- Pickup Location Section -->
                        <section class="position-relative">
                            <div class="form-group" id="pickupLocationToggle">
                                <div class="dropdown-group">
                                    <div class="form-control custom d-flex align-items-center justify-content-between"
                                        required>
                                        <span>{{ trans('client.enter_pickup_location') }}</span>
                                        <ion-icon name="chevron-down-outline" class="dropdown-arrow2"></ion-icon>
                                    </div>
                                </div>
                            </div>
                            <div class="location-list-container shadow-sm" id="pickupLocationList">
                                <ul class="location-list">
                                    <!-- First Location -->
                                    @foreach ($addresses as $address)
                                        <li class="location-item">
                                            <div class="location-info">
                                                <img src="{{ asset('website/client/image/icon2.png') }}"
                                                    alt="{{ trans('client.location_icon') }}" class="location-icon">
                                                <div>
                                                    <p>{{ $address->name }}</p>
                                                    <span class="location-description">{{ $address->location }}</span>
                                                </div>
                                            </div>
                                            <div class="location-radio">
                                                <input type="radio" name="receiving_address_id"
                                                    value="{{ $address->id }}" id="address{{ $address->id }}">
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </section>

                        <!-- Delivery Location Section -->
                        <section class="position-relative">
                            <div class="form-group" id="deliveryLocationToggle">
                                <div class="dropdown-group">
                                    <div class="form-control custom d-flex align-items-center justify-content-between"
                                        required>
                                        <span>{{ trans('client.enter_delivery_location') }}</span>
                                        <ion-icon name="chevron-down-outline" class="dropdown-arrow2"></ion-icon>
                                    </div>
                                </div>
                            </div>
                            <div class="location-list-container shadow-sm" id="deliveryLocationList">
                                <ul class="location-list">
                                    @foreach ($addresses as $address)
                                        <!-- First Location -->
                                        <li class="location-item">
                                            <div class="location-info">
                                                <img src="{{ asset('website/client/image/icon2.png') }}"
                                                    alt="{{ trans('client.location_icon') }}" class="location-icon">
                                                <div>
                                                    <p>{{ $address->name }}</p>
                                                    <span class="location-description">{{ $address->location }}</span>
                                                </div>
                                            </div>
                                            <div class="location-radio">
                                                <input type="radio" name="delivery_address_id"
                                                    value="{{ $address->id }}" id="address{{ $address->id }}">
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </section>


                        <!-- Date and Time Selection Section -->
                        <section class="date-time-section" id="dateTimeSection" style="display: none;">
                            <h4 class="section-title">{{ trans('client.select_dates_and_times') }}</h4>

                            <!-- Receiving Date and Time -->
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="receiving_date"
                                        class="form-label">{{ trans('client.receiving_date') }}</label>
                                    <select id="receiving_date" name="receiving_date" class="form-control custom"
                                        required>
                                        <option value="">{{ trans('client.select_receiving_date') }}</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="receiving_time"
                                        class="form-label">{{ trans('client.receiving_time') }}</label>
                                    <select id="receiving_time" name="receiving_time" class="form-control custom"
                                        required disabled>
                                        <option value="">{{ trans('client.select_receiving_time') }}</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Delivery Date and Time -->
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="delivery_date"
                                        class="form-label">{{ trans('client.delivery_date') }}</label>
                                    <select id="delivery_date" name="delivery_date" class="form-control custom" required>
                                        <option value="">{{ trans('client.select_delivery_date') }}</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="delivery_time"
                                        class="form-label">{{ trans('client.delivery_time') }}</label>
                                    <select id="delivery_time" name="delivery_time" class="form-control custom" required
                                        disabled>
                                        <option value="">{{ trans('client.select_delivery_time') }}</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Order Summary -->
                            <div class="order-summary" id="orderSummary" style="display: none;">
                                <h5>{{ trans('client.order_summary') }}</h5>
                                <div class="summary-item">
                                    <span>{{ trans('client.delivery_charge') }}:</span>
                                    <span id="deliveryCharge">0</span>
                                </div>
                                <div class="summary-item">
                                    <span>{{ trans('client.points_balance') }}:</span>
                                    <span id="pointsBalance">0</span>
                                </div>
                                <div class="summary-item">
                                    <span>{{ trans('client.wallet_balance') }}:</span>
                                    <span id="walletBalance">0</span>
                                </div>
                            </div>
                        </section>
                        <section class="date-time-section">
                            <h4 class="section-title">{{ trans('client.order_details') }}</h4>


                            <!-- Receiving Date and Time -->
                            <div class="form-group">
                                <label for="service_type" class="form-label">{{ trans('client.service_type') }}</label>
                                <select id="service_type" name="service_type" class="form-control custom" required>
                                    <option value="">{{ trans('client.select_service_type') }}</option>
                                    <option value="غسيل">{{ trans('client.washing') }}</option>
                                    <option value="غسيل و كوى">{{ trans('client.washing_and_ironing') }}</option>
                                    <option value="كوى">{{ trans('client.ironing') }}</option>
                                    <option value="دراى كلين">{{ trans('client.dry_clean') }}</option>

                                </select>
                            </div>
                            <div class="form-group">
                                <label for="notes" class="form-label">{{ trans('client.notes') }}</label>
                                <textarea id="notes" name="notes" class="form-control custom" placeholder="{{ trans('client.enter_room_number') }}"></textarea>
                            </div>

                        </section>
                        <!-- Modal Actions -->
                        <div class="modal-actions">
                            <button type="submit" class="confirm-btn"
                                id="confirmOrderNowModal">{{ trans('client.confirm') }}</button>
                            <button type="button" class="cancel-btn"
                                id="cancelOrderNowModal">{{ trans('client.cancel') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <style>
        #pickup-now-btn {
            background: #ffffff;
            color: #213861 !important;
        }

        #pickup-now-btn>* {
            background: #ffffff;
            color: #213861 !important
        }

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

        .bg-light-primary {
            background-color: #f0f8ff;
        }

        .cart-table th {
            color: #4876b2;
            padding: 15px;
            font-size: 0.9rem;
            white-space: nowrap;
        }

        .cart-table td {
            padding: 12px 15px;
        }

        .cart-table tr:not(:last-child) td {
            border-bottom: 1px solid #f1f1f1 !important;
        }
    </style>
    <script>
        // Global variables to store the dates and times data
        let datesData = null;
        let selectedReceivingDate = null;
        let selectedDeliveryDate = null;

        // Wait for jQuery to be available
        function initOrderModal() {
            if (typeof $ === 'undefined') {
                console.log('jQuery not available, retrying in 100ms');
                setTimeout(initOrderModal, 100);
                return;
            }


            // Listen for delivery address selection
            $(document).ready(function() {

                // Check if elements exist
                const deliveryAddressInputs = $('input[name="delivery_address_id"]');
                console.log('Found delivery address inputs:', deliveryAddressInputs.length);
                // Use event delegation to handle dynamically created elements
                $(document).on('change', 'input[name="delivery_address_id"]', function() {
                    console.log('Delivery address changed:', $(this).val(), 'checked:', this.checked);
                    if (this.checked) {
                        const deliveryAddressId = $(this).val();
                        console.log('Fetching dates and times for address ID:', deliveryAddressId);
                        fetchDatesAndTimes(deliveryAddressId);
                    }
                });

                // Also try direct binding for existing elements
                deliveryAddressInputs.on('change', function() {

                    if (this.checked) {
                        const deliveryAddressId = $(this).val();
                        console.log('Direct binding - Fetching dates and times for address ID:',
                            deliveryAddressId);
                        fetchDatesAndTimes(deliveryAddressId);
                    }
                });

                // Test if modal elements are accessible
                setTimeout(function() {
                    console.log('Testing modal elements:');
                    console.log('Modal backdrop:', $('#orderNowModal').length);
                    console.log('Delivery address inputs in modal:', $(
                        '#orderNowModal input[name="delivery_address_id"]').length);
                    console.log('All delivery address inputs:', $('input[name="delivery_address_id"]')
                        .length);
                }, 1000);

                // Toggle customer fields in order modal
                $(document).on('change', 'input[name="order_type"]', function() {
                    if ($(this).val() === 'customer') {
                        $('#customer_fields_container').slideDown();
                        $('#customer_name').prop('required', true);
                        $('#customer_phone').prop('required', true);
                    } else {
                        $('#customer_fields_container').slideUp();
                        $('#customer_name').prop('required', false);
                        $('#customer_phone').prop('required', false);
                    }
                });

                // Listen for modal opening
                $(document).on('click', '.feature-btn', function() {
                    console.log('Modal opening button clicked');
                    // Set up event listeners after modal is opened
                    setTimeout(function() {
                        console.log('Setting up modal event listeners');
                        setupModalEventListeners();
                    }, 100);
                });

                // Handle receiving date change
                $('#receiving_date').on('change', function() {
                    const selectedDate = $(this).val();
                    const deliveryDate = $('#delivery_date').val();

                    if (selectedDate) {
                        // Check if receiving date is after delivery date
                        if (deliveryDate && selectedDate > deliveryDate) {
                            toastr.error('{{ trans('client.receiving_date_cannot_be_after_delivery_date') }}');
                            $(this).val('');
                            $('#receiving_time').html(
                                '<option value="">{{ trans('client.select_receiving_time') }}</option>'
                            );
                            $('#receiving_time').prop('disabled', true);
                            return;
                        }

                        selectedReceivingDate = datesData.find(date => date.date === selectedDate);
                        populateReceivingTimes(selectedReceivingDate);

                        // If delivery date is selected, validate delivery times
                        if (deliveryDate) {
                            validateAndUpdateDeliveryTimes();
                        }
                    } else {
                        $('#receiving_time').html(
                            '<option value="">{{ trans('client.select_receiving_time') }}</option>');
                        $('#receiving_time').prop('disabled', true);
                    }
                });

                // Handle delivery date change
                $('#delivery_date').on('change', function() {
                    const selectedDate = $(this).val();
                    const receivingDate = $('#receiving_date').val();

                    if (selectedDate) {
                        // Check if delivery date is before receiving date
                        if (receivingDate && selectedDate < receivingDate) {
                            toastr.error('{{ trans('client.delivery_date_cannot_be_before_receiving_date') }}');
                            $(this).val('');
                            $('#delivery_time').html(
                                '<option value="">{{ trans('client.select_delivery_time') }}</option>'
                            );
                            $('#delivery_time').prop('disabled', true);
                            return;
                        }

                        selectedDeliveryDate = datesData.find(date => date.date === selectedDate);
                        populateDeliveryTimes(selectedDeliveryDate);

                        // If receiving date is selected, validate receiving times
                        if (receivingDate) {
                            validateAndUpdateReceivingTimes();
                        }
                    } else {
                        $('#delivery_time').html(
                            '<option value="">{{ trans('client.select_delivery_time') }}</option>');
                        $('#delivery_time').prop('disabled', true);
                    }
                });

                // Handle receiving time change
                $('#receiving_time').on('change', function() {
                    validateAndUpdateReceivingTimes();
                });

                // Handle delivery time change
                $('#delivery_time').on('change', function() {
                    validateAndUpdateDeliveryTimes();
                });
                $('#confirmOrderNowModal').on('click', function() {
                    $(this).html(
                        '<span id="orderConfirmSpinner" class="spinner-border spinner-border-sm ms-2" role="status" aria-hidden="true"></span>'
                        );
                    $(this).css('opacity', '0.5');
                    $(this).css('cursor', 'not-allowed');
                    $(this).css('pointer-events', 'none');
                    $(this).css('background-color', '#ccc');
                    $(this).css('color', '#fff');
                    $(this).css('border-color', '#ccc');
                    $(this).css('border-width', '1px');
                });
            });

            function fetchDatesAndTimes(deliveryAddressId) {
                console.log('fetchDatesAndTimes called with ID:', deliveryAddressId);

                // Show loading state
                $('#dateTimeSection').show();
                $('#orderSummary').hide();

                // Reset dropdowns
                resetDropdowns();

                // Make jQuery AJAX request
                $.ajax({
                    url: '{{ route('client.order.get-dates-times') }}',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        delivery_address_id: deliveryAddressId,
                        type: 'fastorder',
                        _token: $('meta[name="csrf-token"]').attr('content') || $('input[name="_token"]').val()
                    },

                    success: function(data) {
                        console.log('Dates and times response:', data);

                        if (data.status === 'success') {
                            datesData = data.data;
                            populateDateDropdowns(data.data);
                        } else {
                            console.error('Error fetching dates and times:', data.message);
                            toastr.error('Error fetching available dates and times. Please try again.');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX error:', error);
                        console.error('Response status:', xhr.status);
                        console.error('Response text:', xhr.responseText);
                        toastr.error('Error fetching available dates and times. Please try again.');
                    }
                });
            }

            function populateDateDropdowns(dates) {
                const $receivingDateSelect = $('#receiving_date');
                const $deliveryDateSelect = $('#delivery_date');

                // Clear existing options
                $receivingDateSelect.html('<option value="">{{ trans('client.select_receiving_date') }}</option>');
                $deliveryDateSelect.html('<option value="">{{ trans('client.select_delivery_date') }}</option>');

                // Add date options
                dates.forEach(date => {
                    const optionText = `${date.day} - ${formatDate(date.date)}`;

                    // Add to receiving date dropdown
                    $receivingDateSelect.append(`<option value="${date.date}">${optionText}</option>`);

                    // Add to delivery date dropdown
                    $deliveryDateSelect.append(`<option value="${date.date}">${optionText}</option>`);
                });
            }

            function populateReceivingTimes(selectedDate) {
                const $receivingTimeSelect = $('#receiving_time');
                $receivingTimeSelect.html('<option value="">{{ trans('client.select_receiving_time') }}</option>');

                if (selectedDate && selectedDate.times) {
                    selectedDate.times.forEach(time => {
                        if (time.isAvailable) {
                            $receivingTimeSelect.append(
                                `<option value="${time.id}">${time.from} - ${time.to}</option>`);
                        }
                    });
                    $receivingTimeSelect.prop('disabled', false);
                } else {
                    $receivingTimeSelect.prop('disabled', true);
                }
            }

            function populateDeliveryTimes(selectedDate) {
                const $deliveryTimeSelect = $('#delivery_time');
                $deliveryTimeSelect.html('<option value="">{{ trans('client.select_delivery_time') }}</option>');

                if (selectedDate && selectedDate.times) {
                    selectedDate.times.forEach(time => {
                        if (time.isAvailable) {
                            $deliveryTimeSelect.append(
                                `<option value="${time.id}">${time.from} - ${time.to}</option>`);
                        }
                    });
                    $deliveryTimeSelect.prop('disabled', false);
                } else {
                    $deliveryTimeSelect.prop('disabled', true);
                }
            }

            function updateOrderSummary(data) {
                $('#deliveryCharge').text(data.delivery_charge || '0');
                $('#pointsBalance').text(data.points || '0');
                $('#walletBalance').text(data.wallet || '0');
                $('#orderSummary').show();
            }

            function resetDropdowns() {
                // Reset all dropdowns
                $('#receiving_date').val('');
                $('#receiving_time').html('<option value="">{{ trans('client.select_receiving_time') }}</option>');
                $('#receiving_time').prop('disabled', true);
                $('#delivery_date').val('');
                $('#delivery_time').html('<option value="">{{ trans('client.select_delivery_time') }}</option>');
                $('#delivery_time').prop('disabled', true);

                // Reset global variables
                selectedReceivingDate = null;
                selectedDeliveryDate = null;
            }

            function setupModalEventListeners() {
                console.log('Setting up modal event listeners');

                // Remove existing listeners to avoid duplicates
                $('input[name="delivery_address_id"]').off('change');

                // Add new listeners
                $('input[name="delivery_address_id"]').on('change', function() {
                    console.log('Modal - Delivery address changed:', $(this).val(), 'checked:', this.checked);
                    if (this.checked) {
                        const deliveryAddressId = $(this).val();
                        console.log('Modal - Fetching dates and times for address ID:', deliveryAddressId);
                        fetchDatesAndTimes(deliveryAddressId);
                    }
                });

                console.log('Modal event listeners set up for', $('input[name="delivery_address_id"]').length, 'elements');
            }

            function validateAndUpdateReceivingTimes() {
                const receivingDate = $('#receiving_date').val();
                const deliveryDate = $('#delivery_date').val();
                const receivingTime = $('#receiving_time').val();
                const deliveryTime = $('#delivery_time').val();

                if (receivingDate && deliveryDate && receivingDate === deliveryDate) {
                    // Same date - validate times with minimum gap
                    if (receivingTime && deliveryTime) {
                        const receivingTimeData = selectedReceivingDate.times.find(t => t.id == receivingTime);
                        const deliveryTimeData = selectedDeliveryDate.times.find(t => t.id == deliveryTime);

                        if (receivingTimeData && deliveryTimeData) {
                            // Calculate time difference in hours
                            const receivingHour = parseInt(receivingTimeData.from.split(':')[0]);
                            const deliveryHour = parseInt(deliveryTimeData.to.split(':')[0]);
                            const timeGap = deliveryHour - receivingHour;

                            // Minimum gap required (e.g., 2 hours)
                            const minGapHours = {{ getSetting('clothes_hours') }};

                            if (timeGap < minGapHours) {
                                toastr.error(
                                    '{{ trans('client.delivery_must_be_at_least_hours_after_receiving', ['hours' => getSetting('clothes_hours')]) }}'
                                );
                                $('#receiving_time').val('');
                                return;
                            }
                        }
                    }
                }
            }

            function validateAndUpdateDeliveryTimes() {
                const receivingDate = $('#receiving_date').val();
                const deliveryDate = $('#delivery_date').val();
                const receivingTime = $('#receiving_time').val();
                const deliveryTime = $('#delivery_time').val();

                if (receivingDate && deliveryDate && receivingDate === deliveryDate) {
                    // Same date - validate times with minimum gap
                    if (receivingTime && deliveryTime) {
                        const receivingTimeData = selectedReceivingDate.times.find(t => t.id == receivingTime);
                        const deliveryTimeData = selectedDeliveryDate.times.find(t => t.id == deliveryTime);

                        if (receivingTimeData && deliveryTimeData) {
                            // Calculate time difference in hours
                            const receivingHour = parseInt(receivingTimeData.from.split(':')[0]);
                            const deliveryHour = parseInt(deliveryTimeData.to.split(':')[0]);
                            const timeGap = deliveryHour - receivingHour;

                            // Minimum gap required (e.g., 2 hours)
                            const minGapHours = {{ getSetting('clothes_hours') }};

                            if (timeGap < minGapHours) {
                                toastr.error(
                                    '{{ trans('client.delivery_must_be_at_least_hours_after_receiving', ['hours' => getSetting('clothes_hours')]) }}'
                                );
                                $('#delivery_time').val('');
                                return;
                            }
                        }
                    }
                }
            }

            function formatDate(dateString) {
                const date = new Date(dateString);
                return date.toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric'
                });
            }
        }
        // Initialize the order modal functionality
        initOrderModal();
    </script>
@endsection
