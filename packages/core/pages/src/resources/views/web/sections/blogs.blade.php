<!-- Special Blogs Section -->
<section class="blogs-section">
    <div class="container">
        <div class="blogs-content">
            <div class="blogs-text">
                <div class="blogs-title-upper">
                    <h2 class="blogs-title">{!! $section->title !!}<br>{!! $section->smaill_title !!}</h2>
                    <img src="{{ asset('web/images/Vector-1.png') }}" alt="">
                </div>
                <p class="blogs-description">
                    {!! $section->description !!}
                </p>
                <a href="{{ route('blog') }}" class="blogs-btn">
                    {{__('more blogs')}}
                    <ion-icon name="arrow-forward" size="small"></ion-icon>
                </a>
            </div>
            <div class="blogs-images">
                <div class="blog-image-container">
                    <img src="{{ $section->image_url }}" alt="Massage therapy blog">
                </div>
            </div>
        </div>
    </div>
</section> 