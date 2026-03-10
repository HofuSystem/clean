@extends('layouts.client')

@section('content')
    <!-- Main Content -->
    <main class="main-content">
        <div class="feature-button-container">
            <button class="feature-btn">{{ trans('client.order_now') }}</button>
        </div>
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-title">{{ trans('client.welcome_message') }}</h1>
            <p class="page-subtitle">
                {{ trans('client.welcome_subtitle') }}
            </p>
        </div>

        <!-- Stats Section -->
        <div class="stats-section">
          
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon d-flex align-items-center gap-2">
                        <img src="{{ asset('website/client/image/users.png') }}" width="18" alt="">
                        <div class="stat-sublabel">{{ trans('client.total_orders') }}</div>
                    </div>
                    <div class="d-flex align-items-center gap-1">
                        <div class="stat-number">{{ $totalOrders ?? 0 }}</div>
                        <div class="stat-label">{{ trans('client.order') }}</div>
                    </div>
                </div>

                <div class="stat-card blue-medium">
                    <div class="stat-icon d-flex align-items-center gap-2">
                        <img src="{{ asset('website/client/image/users.png') }}" width="18" alt="">
                        <div class="stat-sublabel">{{ trans('client.completed_orders') }}</div>
                    </div>
                    <div class="d-flex align-items-center gap-1">
                        <div class="stat-number">{{ $completedOrders ?? 0 }}</div>
                        <div class="stat-label">{{ trans('client.order') }}</div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon d-flex align-items-center gap-2">
                        <img src="{{ asset('website/client/image/users.png') }}" width="18" alt="">
                        <div class="stat-sublabel">{{ trans('client.total_points') }}</div>
                    </div>
                    <div class="d-flex align-items-center gap-1">
                        <div class="stat-number">{{ $user->points_balance ?? 0 }}</div>
                        <div class="stat-label">{{ trans('client.points') }}</div>
                    </div>
                </div>


            </div>
        </div>


        <!-- Feature Section -->
        <div class="feature-section">
 

            <div class="feature-image-container">
                <img src="{{ asset('website/client/image/image1.jpg') }}" alt="Washing Machine Service"
                    class="feature-image">
            </div>
        </div>
        <!-- Order Now Modal -->
        <div class="order-now-modal-backdrop" id="orderNowModal">
            <div class="order-now-modal">
                <div class="modal-header">
                    <h3 class="modal-title">{{ trans('client.order_now') }}</h3>
                </div>

                <div class="modal-body">
   

                    <form class="order-form" id="orderNowForm" action="{{ route('client.order.store') }}" method="POST">
                        @csrf
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

                        <!-- Modal Actions -->
                        <div class="modal-actions">
                            <button type="submit" class="confirm-btn">{{ trans('client.confirm') }}</button>
                            <button type="button" class="cancel-btn"
                                id="cancelOrderNowModal">{{ trans('client.cancel') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

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

            console.log('jQuery is available, initializing order modal');

            // Listen for delivery address selection
            $(document).ready(function() {
                console.log('Document ready, setting up event listeners');

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
                    console.log('Direct binding - Delivery address changed:', $(this).val(), 'checked:',
                        this
                        .checked);
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
                            alert('{{ trans("client.receiving_date_cannot_be_after_delivery_date") }}');
                            $(this).val('');
                            $('#receiving_time').html('<option value="">{{ trans("client.select_receiving_time") }}</option>');
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
                            '<option value="">{{ trans("client.select_receiving_time") }}</option>');
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
                            alert('{{ trans("client.delivery_date_cannot_be_before_receiving_date") }}');
                            $(this).val('');
                            $('#delivery_time').html('<option value="">{{ trans("client.select_delivery_time") }}</option>');
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
                            '<option value="">{{ trans("client.select_delivery_time") }}</option>');
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
                            alert('Error fetching available dates and times. Please try again.');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX error:', error);
                        console.error('Response status:', xhr.status);
                        console.error('Response text:', xhr.responseText);
                        alert('Error fetching available dates and times. Please try again.');
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
                            const minGapHours = 24;
                            
                            if (timeGap < minGapHours) {
                                alert('{{ trans("client.delivery_must_be_at_least_hours_after_receiving", ["hours" => 2]) }}');
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
                            const minGapHours = 2;
                            
                            if (timeGap < minGapHours) {
                                alert('{{ trans("client.delivery_must_be_at_least_hours_after_receiving", ["hours" => 2]) }}');
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

    <style>
        .date-time-section {
            margin-top: 20px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .section-title {
            margin-bottom: 15px;
            font-size: 16px;
            font-weight: 600;
            color: #333;
        }

        .form-row {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
        }

        .form-group {
            flex: 1;
        }

        .form-label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: #333;
        }

        .order-summary {
            margin-top: 20px;
            padding: 15px;
            background: white;
            border-radius: 6px;
            border: 1px solid #e9ecef;
        }

        .order-summary h5 {
            margin-bottom: 10px;
            font-size: 14px;
            font-weight: 600;
            color: #333;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .summary-item:last-child {
            margin-bottom: 0;
        }
    </style>
@endsection