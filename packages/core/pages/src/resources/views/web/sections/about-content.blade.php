<!-- About Content Section -->
<section class="about-content-exact">
    <div class="container">
        <div class="about-main-grid">
            <!-- Left Side - Images -->
            <div class="about-images-exact">
                <img src="{{ $section->images_url }}" alt="">
            </div>

            <!-- Right Side - Content -->
            <div class="about-text-exact">
                <div class="title-underline-exact">
                    <h2 class="about-main-title-exact">{!! $section->title !!}</h2>
                    <div class="underline-decoration"><img src="{{ asset('web/images/Vector-1.png') }}" alt=""></div>
                </div>

                <p class="about-paragraph-exact">
                   {!! $section->description !!}
                </p>

            </div>
        </div>
    </div>
</section> 