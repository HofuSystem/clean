<!-- About Us Hero Section -->
<section class="about-hero-exact" style="background: linear-gradient(rgba(224, 122, 63, 0.2), rgba(212, 98, 43, 0.3)),
         url('{{ $section->image_url }}');    background-size: cover; background-position: center;">
    <div class="container">
        <div class="about-hero-content-exact">
            <div class="about-title-with-border">
                <h1 class="about-hero-title-exact">{{ $section->title }}</h1>
                <img src="{{ asset('web/images/Vector-4.png') }}" alt="">
            </div>
        </div>
    </div>
</section> 