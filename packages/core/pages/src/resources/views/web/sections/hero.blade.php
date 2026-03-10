<!-- Hero Section -->
<section class="hero-section" style=" background: linear-gradient(rgba(224, 122, 63, 0.2), rgba(212, 98, 43, 0.3)),
         url('{{ $section->image_url }}');    background-size: cover; background-position: center;">
    <div class="container">
        <div class="hero-content">
            <div class="hero-title">
                <p>
                   {{$section->title}}
                </p>
            </div>
            <div class="hero-buttons">
                <a href="{{ route('services') }}" class="btn-primary-custom">
                    {{__('our services')}}
                    <span>→</span>
                </a>
                <a href="{{ route('contact-us') }}" class="btn-secondary-custom">
                    {{__('contact us')}}
                    <span>→</span>
                </a>
            </div>
        </div>
    </div>
</section> 