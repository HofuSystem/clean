<!-- Our Blogs Hero Section -->
<section class="blogs-hero-section" style="background: linear-gradient(rgba(224, 122, 63, 0.2), rgba(212, 98, 43, 0.3)),
         url('{{ $section->image_url }}');    background-size: cover; background-position: center;">
    <div class="container">
        <div class="blogs-hero-content">
            <div class="blogs-title-with-border">
                <h1 class="blogs-hero-title">{{ $section->title }}</h1>
                <img src="{{ asset('web/images/Vector-4.png') }}" alt="">
            </div>
        </div>
    </div>
</section> 