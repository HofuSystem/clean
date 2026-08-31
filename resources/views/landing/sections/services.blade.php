@php
    $isRtl = app()->getLocale() == 'ar';

    $servicesList = [
        [
            'slug' => 'wash-and-iron',
            'route' => route('services.single', ['slug' => 'wash-and-iron']),
            'icon' => 'fa-solid fa-shirt',
            'badge' => $isRtl ? 'تسليم خلال 24 ساعة' : '24-Hour Turnaround',
            'title' => $isRtl ? 'غسيل وكوي' : 'Wash & Iron',
            'desc' => $isRtl ? 'للملابس اليومية والثياب والقمصان مع غسيل منفصل 100% وكي دقيق بالبخار.' : 'Daily wear, thobes, and shirts washed 100% separately and crisp steam pressed.',
            'link_text' => $isRtl ? 'تفاصيل الخدمة والأسعار' : 'View Details & Pricing',
        ],
        [
            'slug' => 'dry-cleaning',
            'route' => route('services.single', ['slug' => 'dry-cleaning']),
            'icon' => 'fa-solid fa-wand-magic-sparkles',
            'badge' => $isRtl ? 'حماية تامة للأنسجة' : 'Fiber Protection',
            'title' => $isRtl ? 'التنظيف الجاف (دراي كلين)' : 'Professional Dry Cleaning',
            'desc' => $isRtl ? 'عناية فائقة بالأقمشة الحساسة، البدل الرسمية، وفساتين السهرة بمذيبات عضوية آمنة.' : 'Premium care for suits, delicate fabrics, and evening gowns using eco-safe solvents.',
            'link_text' => $isRtl ? 'تفاصيل الخدمة والأسعار' : 'View Details & Pricing',
        ],
        [
            'slug' => 'carpet-upholstery-cleaning',
            'route' => route('services.single', ['slug' => 'carpet-upholstery-cleaning']),
            'icon' => 'fa-solid fa-rug',
            'badge' => $isRtl ? 'تعقيم ومعالجة بقع' : 'Deep Disinfection',
            'title' => $isRtl ? 'تنظيف السجاد والموكيت' : 'Carpet & Rug Cleaning',
            'desc' => $isRtl ? 'تنظيف عميق وتعقيم بالبخار لإزالة الأتربة والبقع والروائح واستعادة رونق السجاد.' : 'Deep steam cleaning and sanitization removing allergens, dust, and stubborn spots.',
            'link_text' => $isRtl ? 'تفاصيل الخدمة والأسعار' : 'View Details & Pricing',
        ],
        [
            'slug' => 'carpet-upholstery-cleaning',
            'route' => route('services.single', ['slug' => 'carpet-upholstery-cleaning']),
            'icon' => 'fa-solid fa-couch',
            'badge' => $isRtl ? 'بخار حراري مكثف' : 'Thermal Steam',
            'title' => $isRtl ? 'تنظيف المفروشات والستائر' : 'Upholstery & Curtains',
            'desc' => $isRtl ? 'تجفيف وتعقيم متكامل للمفروشات والستائر والبطانيات لضمان بيئة صحية خالية من العث.' : 'Thorough thermal sanitization for curtains, duvets, and linens ensuring pristine freshness.',
            'link_text' => $isRtl ? 'تفاصيل الخدمة والأسعار' : 'View Details & Pricing',
        ],
        [
            'slug' => 'shoe-care',
            'route' => route('services.single', ['slug' => 'shoe-care']),
            'icon' => 'fa-solid fa-shoe-prints',
            'badge' => $isRtl ? 'تنظيف وترميم يدوي' : 'Hand Care',
            'title' => $isRtl ? 'سبا والعناية بالأحذية' : 'Shoe & Sneaker Spa',
            'desc' => $isRtl ? 'تنظيف يدوي متقدم وترميم للأحذية الرياضية والجلدية والشنط لتعود كالجديدة.' : 'Hand restoration and deep cleaning for sneakers, leather footwear, and luxury bags.',
            'link_text' => $isRtl ? 'تفاصيل الخدمة والأسعار' : 'View Details & Pricing',
        ],
        [
            'slug' => 'b2b',
            'route' => route('b2b'),
            'icon' => 'fa-solid fa-building',
            'badge' => $isRtl ? 'عقود وباقات مخصصة' : 'Custom Retainers',
            'title' => $isRtl ? 'حلول الشركات وقطاع الأعمال B2B' : 'B2B Corporate & Hospitality',
            'desc' => $isRtl ? 'خدمات غسيل دورية وشاملة للفنادق والمستشفيات والشقق المخدومة مع نظام LMS لتتبع الطلبات.' : 'Bulk linen management and uniform laundering for hotels, clinics, and enterprises.',
            'link_text' => $isRtl ? 'طلب عرض سعر للشركات' : 'Request Corporate Proposal',
        ],
    ];
@endphp

<section id="services" class="page-section bg-gray-50 py-12 md:py-20 relative overflow-hidden font-['Tajawal']">
    <div class="max-w-7xl mx-auto px-4 relative z-10">
        
        <!-- Hero Header -->
        <div class="text-center mb-12 md:mb-16" data-aos="fade-up">
            <span class="inline-block py-1.5 px-4 rounded-full bg-brand-50 border border-brand-200 text-brand-700 text-xs font-bold uppercase tracking-wider mb-3">
                {{ $isRtl ? 'خدمات متكاملة' : 'All Services' }}
            </span>
            <h1 class="text-3xl md:text-5xl font-black text-gray-900 leading-tight mb-4">
                {{ $isRtl ? 'خدمات الغسيل والكي والتنظيف الجاف الشاملة' : 'Comprehensive Laundry & Garment Care Services' }}
            </h1>
            <p class="text-gray-500 text-base md:text-lg max-w-2xl mx-auto leading-relaxed">
                {{ $isRtl ? 'نجمع بين أحدث تقنيات الغسيل والعناية الفائقة بالتفاصيل مع خدمة الاستلام والتوصيل من باب بيتك.' : 'Combining state-of-the-art fabric care technologies with seamless door-to-door collection and delivery.' }}
            </p>
        </div>

        <!-- Services Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
            @foreach($servicesList as $index => $s)
            <div class="bg-white rounded-3xl p-8 border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-1.5 transition-all duration-300 flex flex-col justify-between group hover:border-brand-300 {{ $isRtl ? 'text-right' : 'text-left' }}" data-aos="fade-up" data-aos-delay="{{ $index * 50 }}">
                <div>
                    <div class="flex items-center justify-between mb-6">
                        <div class="w-14 h-14 rounded-2xl bg-brand-50 flex items-center justify-center text-2xl text-brand-600 group-hover:scale-110 group-hover:bg-brand-600 group-hover:text-white transition-all duration-300 shadow-sm">
                            <i class="{{ $s['icon'] }}"></i>
                        </div>
                        <span class="text-[11px] font-bold text-brand-700 bg-brand-50 border border-brand-100 px-3 py-1 rounded-full">
                            {{ $s['badge'] }}
                        </span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3 group-hover:text-brand-600 transition-colors">
                        {{ $s['title'] }}
                    </h3>
                    <p class="text-gray-500 text-sm leading-relaxed mb-6">
                        {{ $s['desc'] }}
                    </p>
                </div>
                <div class="pt-4 border-t border-gray-100 flex items-center justify-between">
                    <a href="{{ $s['route'] }}" class="inline-flex items-center gap-2 text-xs font-bold text-brand-600 hover:text-brand-700 transition-colors">
                        {{ $s['link_text'] }} <i class="fa-solid fa-arrow-{{ $isRtl ? 'left' : 'right' }} text-[10px]"></i>
                    </a>
                </div>
            </div>
            @endforeach
        </div>

    </div>
</section>
