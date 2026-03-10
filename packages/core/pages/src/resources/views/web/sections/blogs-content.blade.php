<!-- Special Blogs Section -->
<section class="special-blogs-section">
    <div class="container">
        <!-- Content Section -->
        <div class="blogs-header-content">
            <div class="blogs-main-title-upper">
                <h2 class="blogs-main-title">{{ $section->title }}</h2>
                <img src="{{ asset('web/images/Vector-2.png') }}" alt="">
            </div>
            <p class="blogs-main-description">
                {!! $section->description !!}
            </p>
        </div>

        <!-- Blog Cards -->
        <div class="service-cards blogs">
            @foreach ($blogs as $blog)
            <div class="service-card">
                <div class="card-image blogs">
                    <img src="{{ $blog->image_url }}" alt="Therapeutic Massage Blog">
                </div>
                <div class="card-content">
                    <p class="card-title">{{ $blog->title }}</p>
                    <a href="{{ route('blog-post', $blog->slug) }}" class="card-btn blogs">
                        {{__('learn more')}}
                        <ion-icon name="arrow-forward" size="small"></ion-icon>
                    </a>
                </div>
            </div>
            @endforeach
        </div>

        <!-- More Blogs Button -->
        {{-- <div class="more-blogs-container">
            <a href="{{ route('blog') }}" class="more-blogs-btn">
                {{__('more blogs')}}
                <ion-icon name="arrow-forward" size="small"></ion-icon>
            </a>
        </div> --}}
    </div>
</section> 