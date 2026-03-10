@php
    $services = \Core\Pages\Models\Feature::where('section', 'services')->get();
@endphp
<section id="services" class="page-section bg-gray-50 py-20 md:py-32 relative overflow-hidden">
    
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none">
        <div class="absolute top-20 right-[-100px] w-96 h-96 bg-brand-200/40 rounded-full blur-3xl opacity-50 mix-blend-multiply animate-pulse-slow"></div>
        <div class="absolute bottom-20 left-[-100px] w-96 h-96 bg-accent-200/40 rounded-full blur-3xl opacity-50 mix-blend-multiply animate-pulse-slow" style="animation-delay: 1s;"></div>
    </div>

    <div class="max-w-7xl mx-auto px-4 relative z-10">
        
        <div class="text-center mb-20" data-aos="fade-up">
            <span class="text-brand-600 font-bold uppercase tracking-widest text-xs mb-3 block">{{ $section->small_title }}</span>
            <h2 class="text-4xl md:text-5xl font-black text-gray-900 mb-6">{{ $section->title }}</h2>
            <p class="text-gray-500 max-w-2xl mx-auto">{!! $section->description !!}</p>
        </div>

        {{-- Mobile Slider --}}
        <div class="swiper services-swiper md:hidden">
            <div class="swiper-wrapper">
                @foreach($services as $index => $service)
                <div class="swiper-slide">
                    <div class="group relative bg-white p-8 rounded-[2rem] shadow-sm hover:shadow-2xl transition-all duration-300 border border-gray-100 hover:border-brand-200 overflow-hidden h-full">
                        <div class="absolute inset-0 bg-gradient-to-br from-brand-50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <div class="relative z-10 flex flex-col h-full">
                            <div class="w-16 h-16 rounded-2xl bg-brand-50 flex items-center justify-center text-3xl text-brand-600 mb-6 group-hover:scale-110 group-hover:bg-brand-600 group-hover:text-white transition-all duration-300 shadow-sm group-hover:shadow-lg group-hover:shadow-brand-500/30">
                                <i class="{{ $service->icon }}"></i>
                            </div>
                            <h3 class="text-xl md:text-2xl font-bold text-gray-900 mb-3 group-hover:text-brand-700 transition-colors">{{ $service->title }}</h3>
                            <p class="text-gray-500 text-sm leading-relaxed mb-6">{!! $service->description !!}</p>
                            
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="swiper-pagination mt-6"></div>
        </div>

        {{-- Desktop Grid --}}
        <div class="hidden md:grid md:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
            @foreach($services as $index => $service)
            <div class="group relative bg-white p-8 rounded-[2rem] shadow-sm hover:shadow-2xl transition-all duration-300 border border-gray-100 hover:border-brand-200 overflow-hidden" data-aos="fade-up" data-aos-delay="{{ $index * 50 }}">
                <div class="absolute inset-0 bg-gradient-to-br from-brand-50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                <div class="relative z-10 flex flex-col h-full">
                    <div class="w-16 h-16 rounded-2xl bg-brand-50 flex items-center justify-center text-3xl text-brand-600 mb-6 group-hover:scale-110 group-hover:bg-brand-600 group-hover:text-white transition-all duration-300 shadow-sm group-hover:shadow-lg group-hover:shadow-brand-500/30">
                        <i class="{{ $service->icon }}"></i>
                    </div>
                    <h3 class="text-xl md:text-2xl font-bold text-gray-900 mb-3 group-hover:text-brand-700 transition-colors">{{ $service->title }}</h3>
                    <p class="text-gray-500 text-sm leading-relaxed mb-6">{!! $service->description !!}</p>
                    
                </div>
            </div>
            @endforeach
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                if (window.innerWidth < 768) {
                    new Swiper('.services-swiper', {
                        slidesPerView: 1.2,
                        spaceBetween: 16,
                        pagination: {
                            el: '.services-swiper .swiper-pagination',
                            clickable: true,
                        },
                        breakpoints: {
                            640: {
                                slidesPerView: 1.5,
                                spaceBetween: 20,
                            }
                        }
                    });
                }
            });
        </script>

    </div>
</section>
