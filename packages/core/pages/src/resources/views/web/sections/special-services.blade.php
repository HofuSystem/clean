<!-- Special Services Section -->
<section class="special-services-section">
    <div class="container">
        <!-- Video/Header Section -->
        <div class="services-header">
            <div class="video-container">
                <img src="{{ $section->image_url }}" alt="Massage therapy video">
                <div class="play-button">
                    {{-- <div class="play-icon">▶</div> --}}
                    <div class="circular-text">
                        {{-- <svg viewBox="0 0 100 100">
                            <path id="circle" d="M 50,50 m -37,0 a 37,37 0 1,1 74,0 a 37,37 0 1,1 -74,0" />
                            <text>
                                <textPath href="#circle">{{ trans('WATCH VIDEO • WATCH VIDEO • ') }}</textPath>
                            </text>
                        </svg> --}}
                    </div>
                </div>
            </div>
        </div>

        <!-- Content Section -->
        <div class="services-content">
            <div class="services-title-upper">
                <h2 class="services-title">{{ $section->title }}<br>{{ $section->smaill_title }}</h2>
                <img src="{{ asset('web/images/Vector-2.png') }}" alt="">
            </div>
            <p class="services-description">
                {!! strip_tags($section->description) !!}
            </p>
            <a href="{{ route('services') }}" class="services-btn">
                {{__('our services')}}
                <ion-icon name="arrow-forward" size="small"></ion-icon>
            </a>
        </div>

        <!-- Service Cards -->
        <div class="service-cards">
            @foreach($services as $item)
            <div class="service-card">
                <div class="card-image">
                    <img src="{{ $item->image_url }}" alt="Massage Service">
                </div>
                <div class="card-content">
                    <h3 class="card-title">{{ $item->title }}</h3>
                    <div class="card-price">
                        <span class="price">{{ $item->price }}</span>
                        <span class="duration">/ {{__('session')}}</span>
                    </div>
               
                    <a href="{{ route('service-post',$item->slug) }}" class="card-btn">
                        {{__('learn more')}}
                        <ion-icon name="arrow-forward" size="small"></ion-icon>
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section> 