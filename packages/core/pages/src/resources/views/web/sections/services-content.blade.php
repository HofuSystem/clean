<!-- Special Services Section -->
<section class="special-services-section v2">
    <div class="container">
        <!-- Content Section -->
        <div class="services-content">
            <div class="services-title-upper">
                <h2 class="services-title">{{ $section->title }}</h2>
                <img src="{{ asset('web/images/Vector-2.png') }}" alt="">
            </div>
            <p class="services-description">
                {!! $section->description !!}
            </p>
        </div>

        <!-- Service Cards -->
        <div class="service-cards">
            @foreach ($services as $service)
            <div class="service-card">
                <div class="card-image">
                    <img src="{{ $service->image_url }}" alt="Massage Service">
                </div>
                <div class="card-content">
                    <h3 class="card-title">{{ $service->title }}</h3>
                    <div class="card-price">
                        <span class="price">{{ $service->price }}</span>
                        <span class="duration">{{__('session')}}</span>
                    </div>
                    <a href="{{ route('service-post', $service->slug) }}" class="card-btn">
                        {{__('learn more')}}
                        <ion-icon name="arrow-forward" size="small"></ion-icon>
                    </a>
                </div>
            </div>
            @endforeach
      
        </div>
    </div>
</section> 