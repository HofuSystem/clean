@php
    $testimonials = \Core\Pages\Models\Testimonial::all();
@endphp
<section id="testimonials" class="page-section bg-white py-16 md:py-24">
    <div class="max-w-7xl mx-auto px-4 text-center">
        <h2 class="text-3xl font-bold mb-12"><span class="lang-ar">{{ $section->translate('ar')->title }}</span><span class="lang-en">{{ $section->translate('en')->title }}</span></h2>

        <div class="relative">
            <div class="swiper testimonials-swiper">
                <div class="swiper-wrapper">
                    @foreach($testimonials as $t)
                    <div class="swiper-slide">
                        <div class="premium-card p-8 h-full">
                            <div class="text-yellow-400 mb-4">
                                @for($i=0; $i<$t->rating; $i++) <i class="fa-solid fa-star"></i> @endfor
                            </div>
                            <p class="italic text-gray-600 mb-4">"<span class="lang-ar">{{ $t->translate('ar')->body }}</span><span class="lang-en">{{ $t->translate('en')->body }}</span>"</p>
                            <div class="font-bold"><span class="lang-ar">{{ $t->translate('ar')->name }}</span><span class="lang-en">{{ $t->translate('en')->name }}</span></div>
                            <div class="text-xs text-gray-400"><span class="lang-ar">{{ $t->role_ar }}</span><span class="lang-en">{{ $t->role_en }}</span></div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="swiper-pagination mt-6"></div>
            </div>
            <button type="button" class="testimonials-swiper-prev absolute left-0 top-1/2 -translate-y-1/2 z-10 w-12 h-12 rounded-full bg-white shadow-lg border border-gray-200 flex items-center justify-center text-brand-600 hover:bg-brand-50 transition-colors -translate-x-2 md:translate-x-0" aria-label="Previous">
                <i class="fa-solid fa-chevron-left"></i>
            </button>
            <button type="button" class="testimonials-swiper-next absolute right-0 top-1/2 -translate-y-1/2 z-10 w-12 h-12 rounded-full bg-white shadow-lg border border-gray-200 flex items-center justify-center text-brand-600 hover:bg-brand-50 transition-colors translate-x-2 md:translate-x-0" aria-label="Next">
                <i class="fa-solid fa-chevron-right"></i>
            </button>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                new Swiper('.testimonials-swiper', {
                    slidesPerView: 1.1,
                    slidesPerGroup: 1,
                    spaceBetween: 16,
                    loop: true,
                    direction: 'horizontal',
                    pagination: {
                        el: '.testimonials-swiper .swiper-pagination',
                        clickable: true,
                    },
                    navigation: {
                        nextEl: '.testimonials-swiper-next',
                        prevEl: '.testimonials-swiper-prev',
                    },
                    breakpoints: {
                        640: {
                            slidesPerView: 1.5,
                            slidesPerGroup: 1,
                            spaceBetween: 20,
                        },
                        768: {
                            slidesPerView: 2,
                            slidesPerGroup: 2,
                            spaceBetween: 24,
                        },
                        1024: {
                            slidesPerView: 3,
                            slidesPerGroup: 3,
                            spaceBetween: 28,
                        },
                    },
                });
            });
        </script>
    </div>
</section>
