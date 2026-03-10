<!-- Newsletter Section -->
<section class="newsletter-section">
    <div class="container">
        <div class="newsletter-content">
            <h2 class="newsletter-title">{{ $section->title }}</h2>


            <div class="newsletter-form">
                <form class="email-form" action="{{ route('newsletter-subscribe') }}" method="post">
                    @csrf
                    <div class="form-wrapper">
                        <input name="email" type="email" class="email-input" placeholder="Your Email" required>
                        <button type="submit" class="subscribe-btn">
                            {{__('subscribe')}}
                            <ion-icon name="arrow-forward" size="small"></ion-icon>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section> 