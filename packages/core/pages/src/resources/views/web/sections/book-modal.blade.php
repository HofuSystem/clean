@php
    $modalServices = \Core\Services\Models\Service::get();
@endphp
<!-- Book Now Modal -->
<div class="modal-overlay" id="bookNowModal">
    <div class="modal-container">
        <button class="modal-close" id="modalClose">
            <ion-icon name="close"></ion-icon>
        </button>

        <div class="modal-content">
            <div>
                <div class="modal-header">
                    <h2 class="modal-title">{{ trans('book_now') }}</h2>
                    <div class="modal-title-underline">
                        <img src="{{ asset('web/images/Vector-1.png') }}" alt="">
                    </div>
                </div>

                <p class="modal-description">
                    {{ trans('book_now_description') }}
                </p>
            </div>
            <form class="modal-form" id="bookingForm" action="{{ route('contact-us-form') }}" method="post">
                <div class="modal-form-group">
                    <label for="modal-name" class="modal-form-label">{{ trans('your_name') }} *</label>
                    <input type="text" id="modal-name" name="name" class="modal-form-input" required>
                </div>

                <div class="modal-form-group">
                    <label for="modal-phone" class="modal-form-label">{{ trans('your_phone') }} *</label>
                    <div class="modal-phone-input-wrapper">
                        <select class="modal-country-code">
                            <option value="+1">🇨🇦 (+1)</option>

                        </select>
                        <input type="tel" id="modal-phone" name="phone" class="modal-form-input modal-phone-input" required>
                    </div>
                </div>

                <div class="modal-form-group">
                    <label for="modal-email" class="modal-form-label">{{ trans('your_email_address') }} *</label>
                    <input type="email" id="modal-email" name="email" class="modal-form-input" required>
                </div>

                <div class="modal-form-group">
                    <label for="modal-contact-type" class="modal-form-label">{{ trans('service') }}</label>
                    <select id="modal-contact-type" name="service_id" class="modal-form-select">
                        <option value="">{{ trans('select_service') }}</option>
                        @foreach($modalServices as $service)
                            <option value="{{ $service->id }}">{{ $service->title }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="modal-form-group">
                    <label for="modal-date" class="modal-form-label">
                        <ion-icon name="calendar" class="modal-input-icon"></ion-icon>
                        {{ trans('the_specified_date') }}
                    </label>
                    <input type="date" id="modal-date" name="date" class="modal-form-input">
                </div>

                <div class="modal-form-group">
                    <label for="modal-time" class="modal-form-label">
                        <ion-icon name="time" class="modal-input-icon"></ion-icon>
                        {{ trans('the_specified_time') }}
                    </label>
                    <input type="time" id="modal-time" name="time" class="modal-form-input">
                </div>

                <div class="modal-form-group">
                    <label for="modal-notes" class="modal-form-label">{{ trans('notes') }}</label>
                    <textarea id="modal-notes" name="notes" class="modal-form-textarea" rows="2"></textarea>
                </div>

                <button type="submit" class="modal-submit-btn">
                    {{ trans('book_now') }}
                    <ion-icon name="arrow-forward"></ion-icon>
                </button>
            </form>
        </div>
    </div>
</div> 