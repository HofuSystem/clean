<!-- Why Choose Beniamen Section -->
<section class="why-choose-section">
    <div class="container">
        <div class="why-choose-content">
            <!-- Left Side - Content & Image -->
            <div class="why-choose-left">
                <div class="why-choose-title-container">
                    <h2 class="why-choose-title">{{ $section->title }}</h2>
                    <div class="why-choose-title-underline">
                        <img src="{{ asset('web/images/Vector-2.png') }}" alt="">
                    </div>
                </div>
                <p class="why-choose-description">
                    {!! $section->description !!}
                </p>
                <div class="why-choose-image">
                    <img src="{{ $section->image_url }}" alt="Professional Massage Therapy">
                </div>
            </div>

            <!-- Right Side - FAQ Accordion -->
            <div class="why-choose-right">
                @foreach ($faqs as $faq)
                <div class="faq-item @if($loop->first) default-active @endif" onclick="toggleFAQ(this)">
                    <button class="faq-question">
                        <span>{{ $faq->question }}</span>
                        <span class="faq-arrow">↓</span>
                    </button>
                    <div class="faq-answer @if($loop->first) active @endif">
                        <p class="faq-answer-text">
                            {!! $faq->answer !!}
                        </p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section> 