@extends('pages::web.layout.app')

@php
    $section = \Core\Pages\Models\Section::where('template','newsletter')->first();
    $faqSection = \Core\Pages\Models\Section::where('template','why-choose')->first();
    $faqs = \Core\Pages\Models\Faq::get();
@endphp

@section('title', $title)

@section('content')
    
<section class="contact-hero-section services">
  <div class="container">
      <div class="services-hero-content">
          <div class="contact-title-with-border services">
              <h1 class="service-hero-title">{{ $title }}</h1>
          </div>
      </div>
  </div>
</section>

<!-- Service Detail Content Section -->
<section class="service-detail-content">
  <div class="container">
      <div class="service-detail-layout">
          <!-- Left Side - Service Image -->
          <div class="blogs-images v2">
              <div class="blog-image-container">
                  <img src="{{ asset('web/images/blogs-home.jpg') }}" alt="Massage Public Service">
              </div>
          </div>

          <!-- Right Side - Service Information -->
          <div class="service-detail-info">
              <!-- Description Section -->
              <div class="service-description">
                  <h2 class="service-section-title">{{__('description')}}</h2>
                  <div class="service-description-text">
                      <p>{!! $service->content !!}</p>
                  </div>
              </div>

           

              <!-- Price and Booking Section -->
              <div class="service-booking-section">
                  <div class="service-price">
                      <span class="price-amount">{{$service->price}}</span>
                      <span class="price-duration">/ {{__('session')}}</span>
                      <img src="{{ asset('web/images/Vector-3.png') }}" alt="">
                  </div>

                  <div class="booking-actions">
                      <a href="#" class="service-book-btn">
                          {{__('book_now')}}
                          <ion-icon name="arrow-forward"></ion-icon>
                      </a>
                      <div class="service-share">
                          <span class="service-share-label">{{__('share')}}</span>
                          @php
                              $shareUrl = urlencode(request()->fullUrl());
                              $shareTitle = urlencode($title ?? ($service->title ?? ''));
                          @endphp
                          <div class="service-share-icons">
                              <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ $shareUrl }}&title={{ $shareTitle }}" target="_blank" rel="noopener" class="service-share-icon">
                                  <ion-icon name="logo-linkedin"></ion-icon>
                              </a>
                              <a href="https://www.instagram.com/?url={{ $shareUrl }}" target="_blank" rel="noopener" class="service-share-icon">
                                  <ion-icon name="logo-instagram"></ion-icon>
                              </a>
                              <a href="https://www.facebook.com/sharer/sharer.php?u={{ $shareUrl }}" target="_blank" rel="noopener" class="service-share-icon">
                                  <ion-icon name="logo-facebook"></ion-icon>
                              </a>
                              <a href="https://twitter.com/intent/tweet?url={{ $shareUrl }}&text={{ $shareTitle }}" target="_blank" rel="noopener" class="service-share-icon">
                                  <ion-icon name="logo-twitter"></ion-icon>
                              </a>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
      </div>
  </div>
</section>

@include('pages::web.sections.why-choose',['section' => $faqSection])

@include('pages::web.sections.newsletter',['section' => $section])

@endsection 