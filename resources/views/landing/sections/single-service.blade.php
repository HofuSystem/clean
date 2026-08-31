@php
    $isRtl = app()->getLocale() == 'ar';
    $name = $serviceConfig['name'] ?? ($category ? $category->name : $title);
    $tagline = $serviceConfig['tagline'] ?? ($isRtl ? 'خدمات كلين ستيشن المتميزة' : 'Clean Station Premium Services');
    $headline = $serviceConfig['headline'] ?? $title;
    $mainDescription = $serviceConfig['description'] ?? ($category ? $category->description : $description);
    $suitableTitle = $serviceConfig['suitable_title'] ?? ($isRtl ? 'القطع المناسبة للخدمة' : 'Suitable Items');
    $suitableItems = $serviceConfig['suitable_items'] ?? [];
    $sections = $serviceConfig['sections'] ?? [];
    $faqs = $serviceConfig['faqs'] ?? [];
    $serviceIcon = $icon ?? 'fa-solid fa-shirt';
@endphp

<div class="bg-gray-50 font-['Tajawal'] pb-16 min-h-screen">
    <main class="max-w-4xl mx-auto px-4 py-8 md:py-12">
        <!-- Breadcrumbs -->
        <nav class="text-sm text-gray-500 mb-6 flex items-center gap-2 {{ $isRtl ? 'text-right' : 'text-left' }}" aria-label="Breadcrumb">
            <a href="{{ url('/') }}" class="hover:text-sky-600 transition">{{ $isRtl ? 'الرئيسية' : 'Home' }}</a> 
            <span class="text-gray-300">/</span>
            <a href="{{ route('services') }}" class="hover:text-sky-600 transition">{{ $isRtl ? 'الخدمات' : 'Services' }}</a> 
            <span class="text-gray-300">/</span>
            <span class="text-gray-800 font-medium">{{ $name }}</span>
        </nav>

        <!-- Main Article -->
        <article class="bg-white rounded-3xl p-6 sm:p-8 md:p-12 shadow-sm border border-gray-100 space-y-8 {{ $isRtl ? 'text-right' : 'text-left' }}">
            <!-- Header -->
            <header>
                <div class="flex items-center justify-between mb-4">
                    <span class="text-sky-600 font-bold text-xs uppercase tracking-widest block">
                        {{ $tagline }}
                    </span>
                    <div class="w-12 h-12 rounded-2xl bg-sky-50 text-sky-600 flex items-center justify-center text-xl">
                        <i class="{{ $serviceIcon }}"></i>
                    </div>
                </div>
                
                <h1 class="text-2xl sm:text-3xl md:text-4xl font-black text-slate-900 leading-tight mb-4">
                    {{ $headline }}
                </h1>
                <p class="text-gray-600 text-base md:text-lg leading-relaxed">
                    {{ $mainDescription }}
                </p>
            </header>

            <!-- Suitable Items Checklist -->
            @if(!empty($suitableItems))
            <section class="space-y-4 pt-2">
                <h2 class="text-lg md:text-xl font-bold text-slate-900 {{ $isRtl ? 'border-r-4 pr-3 border-sky-600' : 'border-l-4 pl-3 border-sky-600' }}">
                    {{ $suitableTitle }}
                </h2>
                <ul class="grid grid-cols-1 md:grid-cols-2 gap-3 text-gray-700">
                    @foreach($suitableItems as $item)
                        <li class="flex items-center gap-2">
                            <i class="fa-solid fa-check text-sky-600 text-sm"></i>
                            <span>{{ $item }}</span>
                        </li>
                    @endforeach
                </ul>
            </section>
            @endif

            <!-- Custom Content Sections -->
            @if(!empty($sections))
                @foreach($sections as $sec)
                <section class="space-y-4">
                    <h2 class="text-lg md:text-xl font-bold text-slate-900 {{ $isRtl ? 'border-r-4 pr-3 border-sky-600' : 'border-l-4 pl-3 border-sky-600' }}">
                        {{ $sec['title'] }}
                    </h2>
                    <p class="text-gray-600 leading-relaxed">
                        {{ $sec['description'] }}
                    </p>
                </section>
                @endforeach
            @endif

            <!-- FAQ Section -->
            @if(!empty($faqs))
            <section class="space-y-4 pt-4 border-t border-gray-100">
                <h2 class="text-lg md:text-xl font-bold text-slate-900 mb-4">
                    {{ $isRtl ? 'الأسئلة الشائعة حول الخدمة' : 'Frequently Asked Questions' }}
                </h2>
                <div class="space-y-3">
                    @foreach($faqs as $faq)
                    <details class="bg-gray-50 p-4 rounded-2xl border border-gray-200 group">
                        <summary class="font-bold text-slate-800 cursor-pointer list-none flex items-center justify-between">
                            <span>{{ $faq['q'] }}</span>
                            <i class="fa-solid fa-chevron-down text-xs text-gray-400 group-open:rotate-180 transition-transform"></i>
                        </summary>
                        <p class="mt-3 text-sm text-gray-600 leading-relaxed border-t border-gray-100 pt-3">
                            {{ $faq['a'] }}
                        </p>
                    </details>
                    @endforeach
                </div>
            </section>
            @endif

            <!-- CTA Actions -->
            <div class="text-center pt-6 flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="https://cleanstation.app.link/?channel=website" data-app-cta class="w-full sm:w-auto inline-flex items-center justify-center gap-3 bg-gradient-to-r from-sky-500 to-sky-700 text-white px-8 py-4 rounded-full font-bold text-base shadow-lg hover:shadow-sky-500/30 transition hover:-translate-y-0.5">
                    <i class="fa-solid fa-mobile-screen text-lg"></i>
                    <span>{{ $isRtl ? 'اطلب خدمتك عبر التطبيق الآن' : 'Order via App Now' }}</span>
                </a>
                <a href="{{ route('pricing') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-slate-100 text-slate-700 hover:bg-slate-200 px-8 py-4 rounded-full font-bold text-base transition">
                    <i class="fa-solid fa-tags text-sky-600"></i>
                    <span>{{ $isRtl ? 'قائمة الأسعار' : 'View Pricing' }}</span>
                </a>
            </div>
        </article>

        <!-- 3 Highlights Bar -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-8 {{ $isRtl ? 'text-right' : 'text-left' }}">
            <div class="bg-white p-4 rounded-2xl border border-sky-100 shadow-sm flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-sky-100 text-sky-600 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-truck-fast"></i>
                </div>
                <div>
                    <div class="font-bold text-slate-900 text-xs md:text-sm">{{ $isRtl ? 'استلام وتوصيل مجاني' : 'Free Pickup & Delivery' }}</div>
                    <div class="text-[11px] text-gray-500">{{ $isRtl ? 'للطلبات 100 ر.س وفوق' : 'For orders 100 SAR+' }}</div>
                </div>
            </div>
            <div class="bg-white p-4 rounded-2xl border border-sky-100 shadow-sm flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-stopwatch-20"></i>
                </div>
                <div>
                    <div class="font-bold text-slate-900 text-xs md:text-sm">{{ $isRtl ? 'تسليم سريع' : 'Fast Turnaround' }}</div>
                    <div class="text-[11px] text-gray-500">{{ $isRtl ? 'من 21 إلى 24 ساعة للملابس' : '21-24h for clothes' }}</div>
                </div>
            </div>
            <div class="bg-white p-4 rounded-2xl border border-sky-100 shadow-sm flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-violet-100 text-violet-600 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <div>
                    <div class="font-bold text-slate-900 text-xs md:text-sm">{{ $isRtl ? 'غسيل منفصل 100%' : '100% Separate Wash' }}</div>
                    <div class="text-[11px] text-gray-500">{{ $isRtl ? 'في غسالة مستقلة لكل طلب' : 'Dedicated machine per order' }}</div>
                </div>
            </div>
        </div>
    </main>
</div>
