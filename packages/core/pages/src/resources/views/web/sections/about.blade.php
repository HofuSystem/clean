<!-- About Section -->
<section class="about-section">
    <div class="container">
        <div class="about-content">
            <div class="about-images">
                @foreach($section->images_urls as $image)
                <div class="image-container img-{{ $loop->iteration }}">
                    <img src="{{ $image }}" alt="Back massage therapy">
                </div>
                @endforeach
            </div>
            <div class="about-text">
                <h2 class="about-title">{{ $section->title }}</h2>
                <div class="title-underline">
                    <img src="{{ asset('web/images/Vector-1.png') }}" alt="">
                </div>
                <p class="about-description">
                   {!! $section->description !!}
                </p>
               
                <a href="{{ route('about-us') }}" class="about-btn">
                    {{__('about us')}}
                    <ion-icon name="arrow-forward" size="small"></ion-icon>
                </a>
            </div>
        </div>
    </div>
</section> 