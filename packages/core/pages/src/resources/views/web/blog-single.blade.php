@extends('pages::web.layout.app')

@php
    $section = \Core\Pages\Models\Section::where('template','newsletter')->first();
    $faqSection = \Core\Pages\Models\Section::where('template','why-choose')->first();
    $faqs = \Core\Pages\Models\Faq::get();
    $shareUrl = urlencode(request()->fullUrl());
    $shareTitle = urlencode($title ?? ($blog->title ?? ''));
@endphp

@section('title', $title)

@section('content')
    
    <!-- Blog Detail Hero Section with Integrated Image -->
    <section class="blog-detail-hero-integrated" style="background: linear-gradient(rgba(224, 122, 63, 0.2), rgba(212, 98, 43, 0.3)),
         url('{{ $blog->image_url }}');    background-size: cover; background-position: center;">
      <div class="container">
          <!-- Hero Title -->
          <div class="blog-hero-title-section">
              <h1 class="blog-hero-title">{{ $title }}</h1>
          </div>

          <!-- Integrated Featured Image -->
          <div class="blog-hero-image-container">
              <div class="blog-hero-featured-image">
                  <img src="{{ $blog->image_url }}" alt="Therapeutic Massage with Hot Stones">
              </div>
          </div>
      </div>
  </section>

  <!-- Blog Content Section -->
  <section class="blog-content-section">
      <div class="container">
          <!-- Header with Description and Share -->
          <div class="blog-content-header">
              <h2 class="blog-description-title">{{__('description')}}</h2>
              <div class="blog-share-horizontal">
                  <span class="share-label">{{__('share')}}</span>
                  <div class="share-icons-horizontal">
                      <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ $shareUrl }}&title={{ $shareTitle }}" target="_blank" rel="noopener" class="share-icon-horizontal">
                          <ion-icon name="logo-linkedin"></ion-icon>
                      </a>
                      <a href="https://www.instagram.com/?url={{ $shareUrl }}" target="_blank" rel="noopener" class="share-icon-horizontal">
                          <ion-icon name="logo-instagram"></ion-icon>
                      </a>
                      <a href="https://www.facebook.com/sharer/sharer.php?u={{ $shareUrl }}" target="_blank" rel="noopener" class="share-icon-horizontal">
                          <ion-icon name="logo-facebook"></ion-icon>
                      </a>
                      <a href="https://twitter.com/intent/tweet?url={{ $shareUrl }}&text={{ $shareTitle }}" target="_blank" rel="noopener" class="share-icon-horizontal">
                          <ion-icon name="logo-twitter"></ion-icon>
                      </a>
                  </div>
              </div>
          </div>

          <!-- Article Text Content -->
          <div class="blog-article-text">
              <p>{!! $blog->content !!}</p>
          </div>
      </div>
  </section>

@include('pages::web.sections.newsletter',['section' => $section])

@endsection 