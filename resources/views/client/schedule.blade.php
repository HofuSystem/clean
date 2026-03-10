@extends('layouts.client')

@section('content')
    <main class="main-content">

        <h1 class="page-title text-center">{{ trans('client.schedules') }}</h1>

        <!-- Success/Error Messages -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Day Schedules Section -->
        <div class="schedule-section mb-5">
            <div class="section-header d-flex justify-content-between align-items-center mb-3">
                <h2 class="section-title">{{ trans('client.day_schedules') }}</h2>
                <button class="btn btn-primary" id="addDayScheduleBtn">
                    <i class="fas fa-plus me-2"></i>{{ trans('client.add_day_schedule') }}
                </button>
            </div>

            <div class="table-responsive ">
                <table class="table table-hover text-center">
                    <thead class="table-primary">
                        <tr>
                            <th>{{ trans('client.receiver') }}</th>
                            <th>{{ trans('client.delivery') }}</th>
                            <th>{{ trans('client.note') }}</th>
                            <th>{{ trans('client.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($daySchedules as $schedule)
                            <tr>
                                <td>
                                    {{ trans('client.' . strtolower($schedule->receiver_day)) }}
                                    <br>
                                    {{ \Carbon\Carbon::parse($schedule->receiver_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($schedule->receiver_to_time)->format('h:i A') }}
                                    <br>
                                    {{ $schedule->receiverAddress?->name }} - {{ $schedule->receiverAddress?->location }}
                                </td>
                                <td>
                                    {{ trans('client.' . strtolower($schedule->delivery_day)) }}
                                    <br>
                                    {{ \Carbon\Carbon::parse($schedule->delivery_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($schedule->delivery_to_time)->format('h:i A') }}
                                    <br>
                                    {{ $schedule->deliveryAddress?->name }} - {{ $schedule->deliveryAddress?->location }}
                                </td>
                                <td>{{ $schedule->note }}</td>
                                <td>
                                    <form action="{{ route('client.schedule.delete', $schedule->id) }}" method="POST"
                                        class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger"
                                            onclick="return confirm('{{ trans('client.confirm_delete') }}')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">{{ trans('client.no_day_schedules') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Date Schedules Section -->
        <div class="schedule-section">
            <div class="section-header d-flex justify-content-between align-items-center mb-3">
                <h2 class="section-title">{{ trans('client.date_schedules') }}</h2>
                <button class="btn btn-primary" id="addDateScheduleBtn">
                    <i class="fas fa-plus me-2"></i>{{ trans('client.add_date_schedule') }}
                </button>
            </div>

            <div class="table-responsive text-center">
                <table class="table table-hover">
                    <thead class="table-primary">
                        <tr>
                            <th>{{ trans('client.receiver') }}</th>
                            <th>{{ trans('client.delivery') }}</th>
                            <th>{{ trans('client.note') }}</th>
                            <th>{{ trans('client.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dateSchedules as $schedule)
                            <tr>
                                <td>
                                    {{  strtolower($schedule->receiver_date) }}
                                    <br>
                                    {{ \Carbon\Carbon::parse($schedule->receiver_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($schedule->receiver_to_time)->format('h:i A') }}
                                    <br>
                                    {{ $schedule->receiverAddress?->name }} - {{ $schedule->receiverAddress?->location }}
                                </td>
                                <td>
                                    {{ $schedule->delivery_date }}
                                    <br>
                                    {{ \Carbon\Carbon::parse($schedule->delivery_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($schedule->delivery_to_time)->format('h:i A') }}
                                    <br>
                                    {{ $schedule->deliveryAddress?->name }} - {{ $schedule->deliveryAddress?->location }}
                                </td>
                                <td>{{ $schedule->note }}</td>
                                <td>
                                    <form action="{{ route('client.schedule.delete', $schedule->id) }}" method="POST"
                                        class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger"
                                            onclick="return confirm('{{ trans('client.confirm_delete') }}')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">{{ trans('client.no_date_schedules') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Day Schedule Modal -->
        <div class="modal fade" id="dayScheduleModal" tabindex="-1" aria-labelledby="dayScheduleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="dayScheduleModalLabel">{{ trans('client.add_day_schedule') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('client.schedule.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="type" value="day">

                        <div class="modal-body">
                            <div class="row">
                                <!-- Address Selection Section -->
                                <div class="col-md-6 mb-4">
                                    <div class="form-group">
                                        <label for="day_receiver_address_id"
                                            class="form-label">{{ trans('client.enter_pickup_location') }}</label>
                                        <select name="receiver_address_id" id="day_receiver_address_id" class="form-control"
                                            required>
                                            <option value="">{{ trans('client.enter_pickup_location') }}</option>
                                            @foreach ($addresses as $address)
                                                <option value="{{ $address->id }}">
                                                    {{ $address->name }} - {{ $address->location }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <div class="form-group">
                                        <label for="day_delivery_address_id"
                                            class="form-label">{{ trans('client.enter_delivery_location') }}</label>
                                        <select name="delivery_address_id" id="day_delivery_address_id" class="form-control"
                                            required>
                                            <option value="">{{ trans('client.enter_delivery_location') }}</option>
                                            @foreach ($addresses as $address)
                                                <option value="{{ $address->id }}">
                                                    {{ $address->name }} - {{ $address->location }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="day_receiver_day"
                                            class="form-label">{{ trans('client.receiver_day') }}</label>
                                        <select name="receiver_day" id="day_receiver_day" class="form-control" required>
                                            <option value="">{{ trans('client.select_day') }}</option>
                                            <option value="sunday">{{ trans('client.sunday') }}</option>
                                            <option value="monday">{{ trans('client.monday') }}</option>
                                            <option value="tuesday">{{ trans('client.tuesday') }}</option>
                                            <option value="wednesday">{{ trans('client.wednesday') }}</option>
                                            <option value="thursday">{{ trans('client.thursday') }}</option>
                                            <option value="friday">{{ trans('client.friday') }}</option>
                                            <option value="saturday">{{ trans('client.saturday') }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="day_delivery_day"
                                            class="form-label">{{ trans('client.delivery_day') }}</label>
                                        <select name="delivery_day" id="day_delivery_day" class="form-control" required>
                                            <option value="">{{ trans('client.select_day') }}</option>
                                            <option value="sunday">{{ trans('client.sunday') }}</option>
                                            <option value="monday">{{ trans('client.monday') }}</option>
                                            <option value="tuesday">{{ trans('client.tuesday') }}</option>
                                            <option value="wednesday">{{ trans('client.wednesday') }}</option>
                                            <option value="thursday">{{ trans('client.thursday') }}</option>
                                            <option value="friday">{{ trans('client.friday') }}</option>
                                            <option value="saturday">{{ trans('client.saturday') }}</option>
                                        </select>
                                    </div>
                                </div>

                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="day_receiver_time"
                                            class="form-label">{{ trans('client.receiver_time') }}</label>
                                        <input type="time" name="receiver_time" id="day_receiver_time"
                                            class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="day_receiver_to_time"
                                            class="form-label">{{ trans('client.receiver_to_time') }}</label>
                                        <input type="time" name="receiver_to_time" id="day_receiver_to_time"
                                            class="form-control" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="day_delivery_time"
                                            class="form-label">{{ trans('client.delivery_time') }}</label>
                                        <input type="time" name="delivery_time" id="day_delivery_time"
                                            class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="day_delivery_to_time"
                                            class="form-label">{{ trans('client.delivery_to_time') }}</label>
                                        <input type="time" name="delivery_to_time" id="day_delivery_to_time"
                                            class="form-control" required>
                                    </div>
                                </div>
                            </div>
                            <section class="date-time-section">
                                <h4 class="section-title">{{ trans('client.order_details') }}</h4>

                                <!-- Receiving Date and Time -->
                                <div class="form-group">
                                    <label for="day_service_type"
                                        class="form-label">{{ trans('client.service_type') }}</label>
                                    <select id="day_service_type" name="service_type" class="form-control custom" required>
                                        <option value="">{{ trans('client.select_service_type') }}</option>
                                        <option value="غسيل">{{ trans('client.washing') }}</option>
                                        <option value="غسيل و كوى">{{ trans('client.washing_and_ironing') }}</option>
                                        <option value="كوى">{{ trans('client.ironing') }}</option>
                                        <option value="دراى كلين">{{ trans('client.dry_clean') }}</option>

                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="day_notes" class="form-label">{{ trans('client.notes') }}</label>
                                    <textarea id="day_notes" name="notes" class="form-control custom"></textarea>
                                </div>

                            </section>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary"
                                data-bs-dismiss="modal">{{ trans('client.cancel') }}</button>
                            <button type="submit" class="btn btn-primary">{{ trans('client.confirm') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Date Schedule Modal -->
        <div class="modal fade" id="dateScheduleModal" tabindex="-1" aria-labelledby="dateScheduleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="dateScheduleModalLabel">{{ trans('client.add_date_schedule') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('client.schedule.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="type" value="date">
                        <div class="modal-body">
                            <!-- Address Selection Section -->
                            <div class="row">
                                <!-- Address Selection Section -->
                                <div class="col-md-6 mb-4">
                                    <div class="form-group">
                                        <label for="date_receiver_address_id"
                                            class="form-label">{{ trans('client.enter_pickup_location') }}</label>
                                        <select name="receiver_address_id" id="date_receiver_address_id" class="form-control"
                                            required>
                                            <option value="">{{ trans('client.enter_pickup_location') }}</option>
                                            @foreach ($addresses as $address)
                                                <option value="{{ $address->id }}">
                                                    {{ $address->name }} - {{ $address->location }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <div class="form-group">
                                        <label for="date_delivery_address_id"
                                            class="form-label">{{ trans('client.enter_delivery_location') }}</label>
                                        <select name="delivery_address_id" id="date_delivery_address_id" class="form-control"
                                            required>
                                            <option value="">{{ trans('client.enter_delivery_location') }}</option>
                                            @foreach ($addresses as $address)
                                                <option value="{{ $address->id }}">
                                                    {{ $address->name }} - {{ $address->location }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="date_receiver_date"
                                            class="form-label">{{ trans('client.receiver_date') }}</label>
                                        <select id="date_receiver_date" name="receiver_date" class="form-control custom"
                                            required>
                                            <option value="">{{ trans('client.select_receiving_date') }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="date_delivery_date"
                                            class="form-label">{{ trans('client.delivery_date') }}</label>
                                        <select id="date_delivery_date" name="delivery_date" class="form-control custom"
                                            required>
                                            <option value="">{{ trans('client.select_delivery_date') }}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group mb-3">
                                        <label for="date_receiver_time_option"
                                            class="form-label">{{ trans('client.receiver_time_option') }}</label>
                                        <select id="date_receiver_time_option" name="receiver_time_option" class="form-control custom"
                                            required disabled>
                                            <option value="">{{ trans('client.select_receiving_time') }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="date_receiver_time"
                                            class="form-label">{{ trans('client.receiver_time') }}</label>
                                        <input type="time" name="receiver_time" id="date_receiver_time" class="form-control custom" required readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="date_receiver_to_time"
                                            class="form-label">{{ trans('client.receiver_to_time') }}</label>
                                        <input type="time" name="receiver_to_time" id="date_receiver_to_time" class="form-control custom" required readonly>
                                    </div>
                                </div>
                            </div>
                       
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group mb-3">
                                        <label for="date_delivery_time_option"
                                            class="form-label">{{ trans('client.delivery_time_option') }}</label>
                                        <select id="date_delivery_time_option" name="delivery_time_option" class="form-control custom"
                                            required disabled>
                                            <option value="">{{ trans('client.select_receiving_time') }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="date_delivery_time"
                                            class="form-label">{{ trans('client.delivery_time') }}</label>
                                        <input type="time" name="delivery_time" id="date_delivery_time" class="form-control custom" required readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="date_delivery_to_time"
                                            class="form-label">{{ trans('client.delivery_to_time') }}</label>
                                        <input type="time" name="delivery_to_time" id="date_delivery_to_time" class="form-control custom" required readonly>
                                    </div>
                                </div>
                            </div>
                            <section class="date-time-section">
                                <h4 class="section-title">{{ trans('client.order_details') }}</h4>

                                <!-- Receiving Date and Time -->
                                <div class="form-group">
                                    <label for="date_service_type"
                                        class="form-label">{{ trans('client.service_type') }}</label>
                                    <select id="date_service_type" name="service_type" class="form-control custom" required>
                                        <option value="">{{ trans('client.select_service_type') }}</option>
                                        <option value="غسيل">{{ trans('client.washing') }}</option>
                                        <option value="غسيل و كوى">{{ trans('client.washing_and_ironing') }}</option>
                                        <option value="كوى">{{ trans('client.ironing') }}</option>
                                        <option value="دراى كلين">{{ trans('client.dry_clean') }}</option>

                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="date_notes" class="form-label">{{ trans('client.notes') }}</label>
                                    <textarea id="date_notes" name="notes" class="form-control custom"></textarea>
                                </div>

                            </section>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary"
                                data-bs-dismiss="modal">{{ trans('client.cancel') }}</button>
                            <button type="submit" class="btn btn-primary">{{ trans('client.confirm') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <style>
        .page-header {
            background: linear-gradient(to right, #1a7fbe, #4876b2);
            color: white;
            padding: 2rem;
            border-radius: 10px;
            margin-bottom: 2rem;
        }

        .page-title {
            font-size: 2rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .page-subtitle {
            font-size: 1rem;
            opacity: 0.9;
            margin-bottom: 0;
        }

        .schedule-section {
            background: white;
            border-radius: 10px;
            padding: 1rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .section-header {
            border-bottom: 2px solid #f8f9fa;
            padding-bottom: 1rem;
        }

        .section-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 0;
        }

        .table {
            margin-bottom: 0;
        }

        .table th {
            background-color: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
            font-weight: 600;
            color: #495057;
        }

        .table td {
            vertical-align: middle;
        }

        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }

        .modal-content {
            border-radius: 10px;
            border: none;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .modal-header {
            color: white;
            border-radius: 10px 10px 0 0;
        }

        .modal-title {
            font-weight: 600;
        }

        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.5rem;
        }

        .form-control {
            border-radius: 8px;
            border: 2px solid #e9ecef;
            padding: 0.75rem;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }

        .form-control:focus {
            border-color: #1a7fbe;
            box-shadow: 0 0 0 0.2rem rgba(26, 127, 190, 0.25);
        }

        .btn-primary {
            background: linear-gradient(to right, #1a7fbe, #4876b2);
            border: none;
            border-radius: 8px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(26, 127, 190, 0.4);
        }

        .btn-danger {
            border-radius: 6px;
            transition: all 0.3s ease;
        }

        .btn-danger:hover {
            transform: scale(1.05);
        }

        .alert {
            border-radius: 8px;
            border: none;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
        }

        /* Location dropdown styles for schedule modal */
        .location-list-container {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            max-height: 200px;
            overflow-y: auto;
            z-index: 1000;
            display: none;
        }

        .location-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .location-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 16px;
            border-bottom: 1px solid #f0f0f0;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .location-item:hover {
            background-color: #f8f9fa;
        }

        .location-item:last-child {
            border-bottom: none;
        }

        .location-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .location-icon {
            width: 20px;
            height: 20px;
        }

        .location-description {
            font-size: 0.875rem;
            color: #6c757d;
        }

        .location-radio input[type="radio"] {
            margin: 0;
        }

        .dropdown-arrow2 {
            transition: transform 0.2s;
        }

        .form-control.custom {
            cursor: pointer;
            user-select: none;
        }
    </style>

    <script>
        // Global variables for schedule modal
        let scheduleDatesData = null;
        let selectedScheduleReceivingDate = null;
        let selectedScheduleDeliveryDate = null;

        document.addEventListener('DOMContentLoaded', function() {
            // Day Schedule Modal
            const addDayScheduleBtn = document.getElementById('addDayScheduleBtn');
            const dayScheduleModal = new bootstrap.Modal(document.getElementById('dayScheduleModal'));

            addDayScheduleBtn.addEventListener('click', function() {
                dayScheduleModal.show();
            });

            // Date Schedule Modal
            const addDateScheduleBtn = document.getElementById('addDateScheduleBtn');
            const dateScheduleModal = new bootstrap.Modal(document.getElementById('dateScheduleModal'));

            addDateScheduleBtn.addEventListener('click', function() {
                dateScheduleModal.show();
                // Reset dropdowns when modal opens
                resetScheduleDropdowns();
            });

            // Schedule modal location toggles
            const schedulePickupLocationToggle = document.getElementById('schedulePickupLocationToggle');
            const schedulePickupLocationList = document.getElementById('schedulePickupLocationList');
            const scheduleDeliveryLocationToggle = document.getElementById('scheduleDeliveryLocationToggle');
            const scheduleDeliveryLocationList = document.getElementById('scheduleDeliveryLocationList');

            if (schedulePickupLocationToggle) {
                schedulePickupLocationToggle.addEventListener('click', function() {
                    schedulePickupLocationList.style.display = schedulePickupLocationList.style.display ===
                        'block' ? 'none' : 'block';
                    scheduleDeliveryLocationList.style.display = 'none';
                });
            }

            if (scheduleDeliveryLocationToggle) {
                scheduleDeliveryLocationToggle.addEventListener('click', function() {
                    scheduleDeliveryLocationList.style.display = scheduleDeliveryLocationList.style
                        .display === 'block' ? 'none' : 'block';
                    schedulePickupLocationList.style.display = 'none';
                });
            }

            // Handle schedule delivery address selection - Date Modal Only
            $(document).on('change', '#dateScheduleModal select[name="delivery_address_id"]', function() {
                const deliveryAddressId = $(this).val();
                fetchScheduleDatesAndTimes(deliveryAddressId);
            });

            // Handle schedule receiving date change - Date Modal Only
            $('#date_receiver_date').on('change', function() {
                const selectedDate = $(this).val();
                const deliveryDate = $('#date_delivery_date').val();

                if (selectedDate) {
                    // Check if receiving date is after delivery date
                    if (deliveryDate && selectedDate > deliveryDate) {
                        toastr.error(
                            '{{ trans('client.receiving_date_cannot_be_after_delivery_date') }}');
                        $(this).val('');
                        $('#date_receiver_time_option').html(
                            '<option value="">{{ trans('client.select_receiving_time') }}</option>');
                        $('#date_receiver_time_option').prop('disabled', true);
                       
                        return;
                    }

                    selectedScheduleReceivingDate = scheduleDatesData.find(date => date.date ===
                        selectedDate);
                    populateScheduleReceivingTimes(selectedScheduleReceivingDate);

                } else {
                    $('#date_receiver_time_option').html(
                        '<option value="">{{ trans('client.select_receiving_time') }}</option>').prop('disabled', true) ;
                    $('#date_receiver_time_option').prop('disabled', true);
                    $('#date_receiver_to_time').html(
                        '<option value="">{{ trans('client.select_receiving_time') }}</option>').prop('readonly', true);
                    $('#date_receiver_to_time').prop('readonly', true);
                }
            });

            // Handle schedule delivery date change - Date Modal Only
            $('#date_delivery_date').on('change', function() {
                const selectedDate = $(this).val();
                const receivingDate = $('#date_receiver_date').val();

                if (selectedDate) {
                    // Check if delivery date is before receiving date
                    if (receivingDate && selectedDate < receivingDate) {
                        toastr.error(
                            '{{ trans('client.delivery_date_cannot_be_before_receiving_date') }}');
                        $(this).val('');
                        $('#date_delivery_time_option').html(
                            '<option value="">{{ trans('client.select_delivery_time') }}</option>').prop('disabled', true);
                        $('#date_delivery_time_option').prop('disabled', true);
                        $('#date_delivery_to_time').html(
                            '<option value="">{{ trans('client.select_delivery_time') }}</option>')
                            .prop('readonly', true);
                        return;
                    }

                    selectedScheduleDeliveryDate = scheduleDatesData.find(date => date.date ===
                        selectedDate);
                    populateScheduleDeliveryTimes(selectedScheduleDeliveryDate);

                 
                } else {
                    $('#date_delivery_time_option').html(
                        '<option value="">{{ trans('client.select_delivery_time') }}</option>').prop('disabled', true);
                    $('#date_delivery_time_option').prop('disabled', true);
                    $('#date_delivery_to_time').html(
                        '<option value="">{{ trans('client.select_delivery_time') }}</option>').prop('readonly', true);
                }
            });

            // Date Modal time option handlers
            $('#date_receiver_time_option').change(function (e) { 
                e.preventDefault();
                let time = $(this).val();
                let [from, to] = time.split('-');
                $('#date_receiver_time').val(from);
                $('#date_receiver_to_time').val(to);
            });

            $('#date_delivery_time_option').change(function (e) { 
                e.preventDefault();
                let time = $(this).val();
                let [from, to] = time.split('-');
                $('#date_delivery_time').val(from);
                $('#date_delivery_to_time').val(to);
            });

            // Day schedule time validation
            function validateDayScheduleTimes() {
                const receiverDay = document.getElementById('day_receiver_day').value;
                const deliveryDay = document.getElementById('day_delivery_day').value;
                const receiverTime = document.getElementById('day_receiver_time').value;
                const receiverToTime = document.getElementById('day_receiver_to_time').value;
                const deliveryTime = document.getElementById('day_delivery_time').value;
                const deliveryToTime = document.getElementById('day_delivery_to_time').value;
                const minGapHours = {{ getSetting('clothes_hours') }};

                // Helper: Map day string to number (0=Sunday, 1=Monday, ..., 6=Saturday)
                const dayToNum = {
                    'sunday': 0,
                    'monday': 1,
                    'tuesday': 2,
                    'wednesday': 3,
                    'thursday': 4,
                    'friday': 5,
                    'saturday': 6
                };

                if (receiverTime && receiverToTime && receiverTime >= receiverToTime) {
                    toastr.error('{{ trans('client.receiver_end_time_must_be_after_start_time') }}');
                    return false;
                }

                if (deliveryTime && deliveryToTime && deliveryTime >= deliveryToTime) {
                    toastr.error('{{ trans('client.delivery_end_time_must_be_after_start_time') }}');
                    return false;
                }
                // if the day is the same, then we need to check time gap between delivery and receiver

                if (receiverDay === deliveryDay) {
                    if (deliveryTime && receiverTime && deliveryTime <= receiverTime) {
                        const rTime = parseTime(receiverTime);
                        const dTime = parseTime(deliveryTime);
                        const diffMs = dTime.getTime() - rTime.getTime();
                        const diffHours = diffMs / (1000 * 60 * 60);
                        console.log(diffHours, minGapHours);
                        return false;
                        if (diffHours < minGapHours) {
                            toastr.error(
                                '{{ trans('client.delivery_must_be_at_least_hours_after_receiving', ['hours' => getSetting('clothes_hours')]) }}'
                            );
                            return false;
                        }
                        return false;
                    }
                }

                // Check min gap between receiver and delivery
                if (
                    receiverDay && deliveryDay &&
                    receiverTime && deliveryTime &&
                    dayToNum.hasOwnProperty(receiverDay.toLowerCase()) &&
                    dayToNum.hasOwnProperty(deliveryDay.toLowerCase())
                ) {
                    // Calculate the next occurrence of delivery day after receiver day
                    let rDay = dayToNum[receiverDay.toLowerCase()];
                    let dDay = dayToNum[deliveryDay.toLowerCase()];

                    // Parse times
                    function parseTime(t) {
                        const [h, m] = t.split(':').map(Number);
                        return {
                            h,
                            m
                        };
                    }
                    const rTime = parseTime(receiverTime);
                    const dTime = parseTime(deliveryTime);

                    // Use a fixed base date (Sunday)
                    const base = new Date(2020, 0, 5); // 2020-01-05 is a Sunday

                    // Receiver datetime
                    const receiverDate = new Date(base);
                    receiverDate.setDate(base.getDate() + rDay);
                    receiverDate.setHours(rTime.h, rTime.m, 0, 0);

                    // Delivery datetime
                    let deliveryDate = new Date(base);
                    let dayDiff = dDay - rDay;
                    if (dayDiff < 0) dayDiff += 7;
                    deliveryDate.setDate(base.getDate() + rDay + dayDiff);
                    deliveryDate.setHours(dTime.h, dTime.m, 0, 0);

                    // If delivery is on the same day, just compare times
                    if (dayDiff === 0) {
                        // If delivery time is less than or equal to receiver time, treat as next week
                        if (
                            dTime.h < rTime.h ||
                            (dTime.h === rTime.h && dTime.m <= rTime.m)
                        ) {
                            deliveryDate.setDate(deliveryDate.getDate() + 7);
                        }
                    }

                    const diffMs = deliveryDate.getTime() - receiverDate.getTime();
                    const diffHours = diffMs / (1000 * 60 * 60);

                    // Remove debug console.log
                    // console.log(diffHours, minGapHours);

                    if (diffHours < minGapHours) {
                        toastr.error(
                            '{{ trans('client.delivery_must_be_at_least_hours_after_receiving', ['hours' => getSetting('clothes_hours')]) }}'
                        );
                        return false;
                    }
                }

                return true;
            }

            // Add validation to day schedule form
            const dayScheduleForm = document.querySelector('#dayScheduleModal form');
            if (dayScheduleForm) {
                dayScheduleForm.addEventListener('submit', function(e) {
                    if (!validateDayScheduleTimes()) {
                        e.preventDefault();
                    }
                });
            }
              // Date schedule time validation
            function validateDateScheduleTimes() {
                const receivingDate = $('#date_receiver_date').val();
                const deliveryDate = $('#date_delivery_date').val();
                const receivingTime = $('#date_receiver_time').val();
                const deliveryTime = $('#date_delivery_time').val();

                if (receivingDate && deliveryDate && receivingDate === deliveryDate) {
                    // Same date - validate times with minimum gap
                    if (receivingTime && deliveryTime) {
                        const receivingTimeData = selectedScheduleReceivingDate.times.find(t => t.id == receivingTime);
                        const deliveryTimeData = selectedScheduleDeliveryDate.times.find(t => t.id == deliveryTime);

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
                                return false;
                            }
                        }
                    }
                }
                return true;
            }

            // Add validation to date schedule form
            const dateScheduleForm = document.querySelector('#dateScheduleModal form');
            if (dayScheduleForm) {
                dateScheduleForm.addEventListener('submit', function(e) {
                    if (!validateDateScheduleTimes()) {
                        e.preventDefault();
                    }
                });
            }
        });

        // Schedule modal functions
        function fetchScheduleDatesAndTimes(deliveryAddressId) {
            console.log('fetchScheduleDatesAndTimes called with ID:', deliveryAddressId);

            // Reset dropdowns
            resetScheduleDropdowns();

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
                    console.log('Schedule dates and times response:', data);

                    if (data.status === 'success') {
                        scheduleDatesData = data.data;
                        populateScheduleDateDropdowns(data.data);
                    } else {
                        console.error('Error fetching schedule dates and times:', data.message);
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

        function populateScheduleDateDropdowns(dates) {
            const $receivingDateSelect = $('#date_receiver_date');
            const $deliveryDateSelect = $('#date_delivery_date');

            // Clear existing options
            $receivingDateSelect.html('<option value="">{{ trans('client.select_receiving_date') }}</option>');
            $deliveryDateSelect.html('<option value="">{{ trans('client.select_delivery_date') }}</option>');

            // Add date options
            dates.forEach(date => {
                const optionText = `${date.day} - ${formatScheduleDate(date.date)}`;

                // Add to receiving date dropdown
                $receivingDateSelect.append(`<option value="${date.date}">${optionText}</option>`);

                // Add to delivery date dropdown
                $deliveryDateSelect.append(`<option value="${date.date}">${optionText}</option>`);
            });
        }

        function populateScheduleReceivingTimes(selectedDate) {
            const $receivingTimeSelect = $('#date_receiver_time_option');
            const $receivingToTimeSelect = $('#date_receiver_to_time');

            $receivingTimeSelect.html('<option value="">{{ trans('client.select_receiving_time') }}</option>');
            $receivingToTimeSelect.html('<option value="">{{ trans('client.select_receiving_time') }}</option>');

            if (selectedDate && selectedDate.times) {
                selectedDate.times.forEach(time => {
                    if (time.isAvailable) {
                        $receivingTimeSelect.append(
                            `<option value="${time.from}-${time.to}">${time.from} - ${time.to}</option>`);
                        $receivingToTimeSelect.append(
                            `<option value="${time.from}-${time.to}">${time.from} - ${time.to}</option>`);
                    }
                });
                $receivingTimeSelect.prop('disabled', false);
                // Keep date_receiver_to_time always readonly as it's populated automatically
                $receivingToTimeSelect.prop('readonly', true);
            } else {
                $receivingTimeSelect.prop('disabled', true);
                $receivingToTimeSelect.prop('readonly', true);
            }
        }

        function populateScheduleDeliveryTimes(selectedDate) {
            const $deliveryTimeSelect = $('#date_delivery_time_option');
            const $deliveryToTimeSelect = $('#date_delivery_to_time');

            $deliveryTimeSelect.html('<option value="">{{ trans('client.select_delivery_time') }}</option>');
            $deliveryToTimeSelect.html('<option value="">{{ trans('client.select_delivery_time') }}</option>');

            if (selectedDate && selectedDate.times) {
                selectedDate.times.forEach(time => {
                    if (time.isAvailable) {
                        $deliveryTimeSelect.append(
                            `<option value="${time.from}-${time.to}">${time.from} - ${time.to}</option>`);
                        $deliveryToTimeSelect.append(
                            `<option value="${time.from}-${time.to}">${time.from} - ${time.to}</option>`);
                    }
                });
                $deliveryTimeSelect.prop('disabled', false);
                // Keep date_delivery_to_time always readonly as it's populated automatically
                $deliveryToTimeSelect.prop('readonly', true);
            } else {
                $deliveryTimeSelect.prop('disabled', true);
                $deliveryToTimeSelect.prop('readonly', true);
            }
        }

        function resetScheduleDropdowns() {
            // Reset all dropdowns
            $('#date_receiver_date').val('');
            $('#date_receiver_time_option').html('<option value="">{{ trans('client.select_receiving_time') }}</option>');
            $('#date_receiver_time_option').prop('disabled', true);
            $('#date_receiver_to_time').html('<option value="">{{ trans('client.select_receiving_time') }}</option>');
            $('#date_receiver_to_time').prop('readonly', true);
            $('#date_delivery_date').val('');
            $('#date_delivery_time_option').html('<option value="">{{ trans('client.select_delivery_time') }}</option>');
            $('#date_delivery_time_option').prop('disabled', true);
            $('#date_delivery_to_time').html('<option value="">{{ trans('client.select_delivery_time') }}</option>');
            $('#date_delivery_to_time').prop('readonly', true);

            // Reset global variables
            selectedScheduleReceivingDate = null;
            selectedScheduleDeliveryDate = null;
        }


    
        function formatScheduleDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
        }
    </script>
@endsection 
