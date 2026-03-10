<!-- Contact Section -->
<section class="contact-section">
    <div class="container">
        <div class="contact-content">
            <!-- Left Side - Contact Information -->
            <div class="contact-info-side">
                <div class="contact-info-header">
                    <div class="contact-info-title-upper">
                        <h2 class="contact-info-title">{{ $section->title }}</h2>
                        <img src="{{ asset('web/images/Vector-1.png') }}" alt="">
                    </div>
                    <p class="contact-info-description">
                        {!! $section->description !!}
                    </p>
                </div>
                <div class="location-cards-container">
                    <!-- Montreal Location -->
                    <div class="location-card">
                        <h3 class="location-title">{{ settings('location') }}</h3>

                        <div class="location-item">
                            <div class="location-icon">
                                <ion-icon name="location"></ion-icon>
                            </div>
                            <span class="location-text">{{ settings('address') }}</span>
                        </div>

                        <div class="location-item">
                            <div class="location-icon">
                                <ion-icon name="call"></ion-icon>
                            </div>
                            <span class="location-text">{{ settings('phone') }}</span>
                        </div>

                        <div class="location-item">
                            <div class="location-icon">
                                <ion-icon name="mail"></ion-icon>
                            </div>
                            <span class="location-text">{{ settings('email') }}</span>
                        </div>
                    </div>

                    <!-- Laval Location -->
                    <div class="location-card">
                        <h3 class="location-title">{{ settings('location') }}</h3>

                        <div class="location-item">
                            <div class="location-icon">
                                <ion-icon name="location"></ion-icon>
                            </div>
                            <span class="location-text">{{ settings('address') }}</span>
                        </div>

                        <div class="location-item">
                            <div class="location-icon">
                                <ion-icon name="call"></ion-icon>
                            </div>
                            <span class="location-text">{{ settings('phone') }}</span>
                        </div>

                        <div class="location-item">
                            <div class="location-icon">
                                <ion-icon name="mail"></ion-icon>
                            </div>
                            <span class="location-text">{{ settings('email') }}</span>
                        </div>
                    </div>

                    <!-- Follow Us Section -->
                    <div class="follow-section">
                        <h3 class="follow-title">{{ trans('follow_us') }}</h3>
                        <div class="social-links">
                            <a href="{{settings('instagram')}}" class="social-link">
                                <ion-icon name="logo-instagram"></ion-icon>
                            </a>
                            <a href="{{settings('youtube')}}" class="social-link">
                                <ion-icon name="logo-youtube"></ion-icon>
                            </a>
                            <a href="{{settings('facebook')}}" class="social-link">
                                <ion-icon name="logo-facebook"></ion-icon>
                            </a>
                            <a href="{{settings('whatsapp')}}" class="social-link">
                                <ion-icon name="logo-whatsapp"></ion-icon>
                            </a>
                           
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side - Contact Form -->
            <div class="contact-form-side">
                <div class="contact-form-container">
                    <form class="contact-form" id="contactForm">
                        <div class="form-group">
                            <label for="name" class="form-label">{{ trans('your_name') }} *</label>
                            <input type="text" id="name" name="name" class="form-input" required>
                        </div>

                        <div class="form-group">
                            <label for="phone" class="form-label">{{ trans('your_phone') }} *</label>
                            <div class="phone-input-wrapper">
                                <select class="country-code">
                                    <option value="+20">🇪🇬 (+20)</option>
                                    <option value="+1">🇺🇸 (+1)</option>
                                    <option value="+1">🇨🇦 (+1)</option>
                                    <option value="+44">🇬🇧 (+44)</option>
                                </select>
                                <input type="tel" id="phone" name="phone" class="form-input phone-input" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="email" class="form-label">{{ trans('your_email_address') }} *</label>
                            <input type="email" id="email" name="email" class="form-input" required>
                        </div>

                        <div class="form-group">
                            <label for="contact-type" class="form-label">{{ trans('contact_type') }}</label>
                            <select id="contact-type" name="contact-type" class="form-select">
                                <option value="">{{ trans('select_contact_type') }}</option>
                                <option value="booking">{{ trans('booking_inquiry') }}</option>
                                <option value="general">{{ trans('general_question') }}</option>
                                <option value="support">{{ trans('support') }}</option>
                                <option value="feedback">{{ trans('feedback') }}</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="message" class="form-label">{{ trans('your_message') }}</label>
                            <textarea id="message" name="message" class="form-textarea" rows="3"></textarea>
                        </div>

                        <button type="submit" class="contact-submit-btn">
                            {{ trans('send_message') }} →
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="map-section">
    <img src="{{ settings('map_item') }}" alt="">
</section> 