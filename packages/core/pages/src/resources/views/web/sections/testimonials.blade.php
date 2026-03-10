<!-- Testimonials Section -->
<section class="testimonials-section">
    <div class="container">
        <div class="testimonials-header">
            <div class="testimonials-title-upper">
                        <h2 class="testimonials-title">{{ $section->title }}</h2>
                <img src="{{ asset('web/images/Vector-3.png') }}" alt="">
            </div>
            <p class="testimonials-description">
               {!! $section->description !!}
            </p>
        </div>

        <!-- Testimonials Slider -->
        <div class="testimonials-slider">
            <div class="testimonials-wrapper" id="testimonialsWrapper">
                @foreach($testimonials as $item)
                <!-- Testimonial Slide 1 -->
                <div class="testimonial-slide">
                    <div class="testimonial-card">
                        <div class="testimonial-content">
                            <!-- Orange background with customer image -->
                            <div class="customer-section">
                                <div class="orange-bg-element2"></div>
                                <div class="orange-bg-element"></div>
                                <div class="customer-image-container">
                                    <div class="customer-image-circle">
                                        <img src="{{ $item->avatar_url }}" alt="Leslie Alexander">
                                    </div>
                                </div>
                            </div>

                            <!-- Quote and customer details -->
                            <div class="testimonial-main">
                                <div class="quote-mark">
                                    <img src="{{ asset('web/images/Heading.png') }}" alt="">
                                </div>

                                <div class="customer-details">
                                    <h3 class="customer-name">{{ $item->name }}</h3>
                                    <p class="customer-location">{{ $item->location }}</p>
                                    <div class="rating">
                                        <span class="star">★</span>
                                        <span class="star">★</span>
                                        <span class="star">★</span>
                                        <span class="star">★</span>
                                        <span class="star ">★</span>
                                    </div>
                                </div>

                                <div class="testimonial-text">
                                    <h4 class="testimonial-heading">{{ $item->title }}</h4>
                                    <p class="testimonial-quote">
                                        {!! $item->body !!}
                                    </p>
                                </div>

                                <div class="quote-mark-right">
                                    <img src="{{ asset('web/images/Heading.png') }}" alt="">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach

            </div>

            <!-- Navigation -->
            <div class="testimonial-navigation">
                <button class="nav-btn prev-btn" id="prevBtn">
                    <ion-icon name="chevron-back"></ion-icon>
                </button>
                <button class="nav-btn next-btn" id="nextBtn">
                    <ion-icon name="chevron-forward"></ion-icon>
                </button>
            </div>

            <!-- Slider Indicators -->
            <div class="slider-indicators" id="sliderIndicators">
                <span class="indicator active" data-slide="0"></span>
                <span class="indicator" data-slide="1"></span>
                <span class="indicator" data-slide="2"></span>
            </div>
        </div>
    </div>
</section> 