<!-- Beniamen Habshi Section -->
<section class="beniamen-section">
    <div class="container">
        <div class="beniamen-content">
            <!-- Left Side - Information -->
            <div class="beniamen-info">
                <div class="beniamen-title-container">
                    <h2 class="beniamen-main-title">{{ $section->title }}</h2>
                    <div class="beniamen-title-underline">
                        <img src="{{ asset('web/images/Vector-5.jpg') }}" alt="">
                    </div>
                    <div class="beniamen-description-box">
                        <p class="beniamen-description-text">
                            {!! $section->description !!}
                        </p>

                        
                    </div>
                </div>

                <div class="beniamen-stats">
                    @foreach ($counters as $counter)
                    <div class="stat-item">
                        <div class="stat-number">{{ $counter->count }}<span>+</span></div>
                        <div class="stat-label">{{ $counter->title }}</div>
                    </div>
                    @endforeach

                </div>
            </div>

            <!-- Right Side - Image -->
            <div class="beniamen-image">
                <div class="beniamen-image-circle">
                    <img src="{{ asset('web/images/placeholder5.jpg') }}" alt="Beniamen Habshi - Professional Massage Therapist">
                </div>
            </div>
        </div>
    </div>
</section> 