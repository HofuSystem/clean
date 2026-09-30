@php
    $isRtl = app()->getLocale() === 'ar';
    $b2bSectors = \Core\Pages\Models\Feature::with('translations')->where('section', 'b2b')->get();
@endphp
<section id="b2b" class="page-section bg-gradient-to-b from-slate-50/70 via-white to-sky-50/30 text-slate-800 py-16 md:py-24 relative overflow-hidden">
    
    {{-- Subtle decorative background elements --}}
    <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-sky-100/40 rounded-full blur-[120px] pointer-events-none -mr-40 -mt-20"></div>
    <div class="absolute bottom-1/3 left-0 w-[500px] h-[500px] bg-brand-50/50 rounded-full blur-[120px] pointer-events-none -ml-40"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        
        {{-- Section Header --}}
        <div class="text-center max-w-3xl mx-auto mb-16 md:mb-20" data-aos="fade-up">
            <span class="inline-flex items-center gap-2 py-1.5 px-4 rounded-full bg-sky-50 border border-sky-200/80 text-sky-700 text-xs font-bold tracking-wide uppercase mb-4 shadow-xs">
                <span class="w-2 h-2 rounded-full bg-sky-500 animate-pulse"></span>
                <span>{{ $section->small_title ?? ($isRtl ? 'حلول الشركات والأعمال B2B' : 'B2B Corporate Solutions') }}</span>
            </span>
            <h1 class="text-3xl sm:text-4xl md:text-5xl font-black text-slate-900 mb-6 leading-tight tracking-tight">
                {{ $section->title ?? ($isRtl ? 'حلول غسيل متكاملة لقطاع الأعمال والمنشآت' : 'Integrated Laundry Solutions for Enterprises') }}
            </h1>
            <p class="text-slate-600 text-base md:text-lg leading-relaxed">
                {!! $section->description ?? ($isRtl ? 'نقدم حلول غسيل وتعقيم متطورة بالتعاون مع الفنادق، الشقق المخدومة، المستشفيات، المراكز الرياضية، وصالونات التجميل مع نظام رقمي دقيق لإدارة الطلبات.' : 'Advanced laundering and sanitization solutions tailored for hotels, serviced apartments, clinics, wellness clubs, and salons with full digital lifecycle tracking.') !!}
            </p>
        </div>

        {{-- Enterprise Sectors Grid (4 Pillars) --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-24">
            @php
                $sectorMeta = [
                    10 => [
                        'color' => 'from-sky-500 to-blue-600',
                        'bg' => 'bg-sky-50 text-sky-600 border-sky-100',
                        'bullets' => $isRtl ? ['بياضات ومفارش فندقية', 'عناية خاصة بملابس النزلاء', 'تسليم مجدول يومياً'] : ['Hotel linens & bedding', 'Guest garment care', 'Daily scheduled delivery'],
                        'svg' => '<path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>'
                    ],
                    11 => [
                        'color' => 'from-emerald-500 to-teal-600',
                        'bg' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                        'bullets' => $isRtl ? ['تعقيم حراري ومكافحة عدوى', 'أرواب الأطباء والتمريض', 'تغليف صحي معزول'] : ['Thermal sanitization & hygiene', 'Medical scrubs & lab coats', 'Sealed hygienic packaging'],
                        'svg' => '<path stroke-linecap="round" stroke-linejoin="round" d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v14a2 2 0 01-2 2zM12 7v10m-5-5h10"/>'
                    ],
                    12 => [
                        'color' => 'from-purple-500 to-pink-600',
                        'bg' => 'bg-purple-50 text-purple-600 border-purple-100',
                        'bullets' => $isRtl ? ['مناشف فائقة النعومة', 'معالجة بقع الزيوت والصبغات', 'روائح منعشة تدوم'] : ['Ultra-soft luxury towels', 'Dye & oil stain removal', 'Long-lasting fresh scents'],
                        'svg' => '<path stroke-linecap="round" stroke-linejoin="round" d="M14.121 14.121L19 19m-7-7l7-7m-7 7l-2.879 2.879a3 3 0 11-4.242-4.242L10.758 10.5m1.242 1.5L9.121 9.121a3 3 0 10-4.242 4.242L7.758 16.242"/>'
                    ],
                    13 => [
                        'color' => 'from-amber-500 to-orange-600',
                        'bg' => 'bg-amber-50 text-amber-600 border-amber-100',
                        'bullets' => $isRtl ? ['مناشف المشتركين الرياضية', 'تعقيم مضاد للروائح', 'تسليم دفعات سريعة'] : ['Member workout towels', 'Anti-odor sanitization', 'High-frequency batch turnaround'],
                        'svg' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 10v4m3-6v8m0-4h12m0-4v8m3-6v4M7 8h1m8 0h1m-9 8h1m8 0h1"/>'
                    ],
                ];
            @endphp

            @foreach($b2bSectors as $sector)
            @php
                $meta = $sectorMeta[$sector->id] ?? [
                    'color' => 'from-sky-500 to-blue-600',
                    'bg' => 'bg-sky-50 text-sky-600 border-sky-100',
                    'bullets' => $isRtl ? ['خدمة مخصصة للمنشآت', 'تسليم مرن ومجدول', 'فواتير ضريبية موحدة'] : ['Tailored B2B service', 'Flexible schedule', 'Unified tax invoicing'],
                    'svg' => '<path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/>'
                ];
            @endphp
            <div class="group bg-white rounded-2xl p-7 border border-slate-200/80 shadow-xs hover:shadow-xl hover:border-sky-300 hover:-translate-y-1.5 transition-all duration-300 flex flex-col justify-between" data-aos="fade-up">
                <div>
                    <div class="w-14 h-14 rounded-2xl {{ $meta['bg'] }} border flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300 shadow-xs">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.9">
                            {!! $meta['svg'] !!}
                        </svg>
                    </div>
                    <h3 class="font-extrabold text-xl text-slate-900 mb-2 group-hover:text-sky-600 transition-colors">
                        {{ $sector->title }}
                    </h3>
                    <p class="text-xs text-slate-500 leading-relaxed mb-6">
                        {{ $sector->description }}
                    </p>
                </div>
                <div class="pt-4 border-t border-slate-100 space-y-2">
                    @foreach($meta['bullets'] as $bullet)
                    <div class="flex items-center gap-2 text-xs font-semibold text-slate-700">
                        <span class="w-4 h-4 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                            <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        </span>
                        <span>{{ $bullet }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>

        {{-- Enterprise LMS & Real Dashboard Showcase --}}
        <div class="bg-white rounded-3xl border border-slate-200/90 shadow-lg p-8 md:p-14 mb-24 overflow-hidden relative" data-aos="fade-up">
            <div class="grid lg:grid-cols-12 gap-12 items-center">
                
                {{-- Left/Right Narrative --}}
                <div class="lg:col-span-5 space-y-6 {{ $isRtl ? 'text-right' : 'text-left' }}">
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-sky-50 text-sky-700 border border-sky-100 text-xs font-bold">
                        <svg class="w-4 h-4 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        <span>{{ $isRtl ? 'بوابة إدارة الغسيل المؤسسي LMS' : 'Clean Station Enterprise LMS' }}</span>
                    </div>
                    
                    <h2 class="text-2xl sm:text-3xl md:text-4xl font-black text-slate-900 leading-tight">
                        {{ trans('Integrated Laundry Management System') }}
                    </h2>
                    
                    <p class="text-slate-600 text-base leading-relaxed">
                        {{ trans('Comprehensive technical solution. Real-time tracking, high financial accuracy, multi-branch management, and elimination of operational waste.') }}
                    </p>

                    <div class="space-y-4 pt-2">
                        <div class="flex items-start gap-3.5">
                            <div class="w-10 h-10 rounded-xl bg-sky-100/80 text-sky-700 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-900 text-base">{{ trans('Centralized Branch Management') }}</h3>
                                <p class="text-xs text-slate-500 leading-relaxed">{{ trans('Control branches, staff, and pricing from one screen.') }}</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3.5">
                            <div class="w-10 h-10 rounded-xl bg-emerald-100/80 text-emerald-700 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-900 text-base">{{ trans('Financial Accuracy & Waste Control') }}</h3>
                                <p class="text-xs text-slate-500 leading-relaxed">{{ trans('Detailed reports detecting waste with 100% accurate invoices.') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="pt-2">
                        <a href="#b2b-form" class="inline-flex items-center gap-2 bg-slate-900 hover:bg-slate-800 text-white px-6 py-3.5 rounded-xl font-bold text-sm shadow-md hover:shadow-lg transition-all duration-200">
                            <span>{{ trans('Request System Demo') }}</span>
                            <svg class="w-4 h-4 {{ $isRtl ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                    </div>
                </div>

                {{-- Right/Left Realistic LMS Dashboard UI Preview --}}
                <div class="lg:col-span-7">
                    <div class="bg-slate-900 text-slate-100 rounded-2xl shadow-2xl border border-slate-800 overflow-hidden">
                        {{-- Mockup Window Chrome --}}
                        <div class="bg-slate-950 px-4 py-3 border-b border-slate-800 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-rose-500"></span>
                                <span class="w-3 h-3 rounded-full bg-amber-500"></span>
                                <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                            </div>
                            <div class="text-[11px] font-mono text-slate-400 bg-slate-900 px-3 py-1 rounded-md border border-slate-800">
                                portal.cleanstation.app/enterprise
                            </div>
                            <div class="w-12"></div>
                        </div>

                        {{-- Dashboard Content --}}
                        <div class="p-6 space-y-6">
                            {{-- Top Metrics Row --}}
                            <div class="grid grid-cols-3 gap-3.5">
                                <div class="bg-slate-800/80 p-3.5 rounded-xl border border-slate-700/60">
                                    <div class="text-[10px] text-slate-400 font-bold uppercase">{{ $isRtl ? 'إجمالي القطع (الشهر)' : 'Monthly Items' }}</div>
                                    <div class="text-xl md:text-2xl font-black text-white mt-1">18,450</div>
                                    <div class="text-[10px] text-emerald-400 font-bold mt-0.5">↑ 14% {{ $isRtl ? 'نمو تشغيلي' : 'growth' }}</div>
                                </div>
                                <div class="bg-slate-800/80 p-3.5 rounded-xl border border-slate-700/60">
                                    <div class="text-[10px] text-slate-400 font-bold uppercase">{{ $isRtl ? 'الشحنات النشطة' : 'Active Batches' }}</div>
                                    <div class="text-xl md:text-2xl font-black text-sky-400 mt-1">3 {{ $isRtl ? 'دفعات' : 'Batches' }}</div>
                                    <div class="text-[10px] text-slate-300 font-medium mt-0.5">{{ $isRtl ? 'قيد التوصيل اليوم' : 'En route today' }}</div>
                                </div>
                                <div class="bg-slate-800/80 p-3.5 rounded-xl border border-slate-700/60">
                                    <div class="text-[10px] text-slate-400 font-bold uppercase">{{ $isRtl ? 'التزام SLA' : 'SLA Compliance' }}</div>
                                    <div class="text-xl md:text-2xl font-black text-emerald-400 mt-1">99.8%</div>
                                    <div class="text-[10px] text-slate-300 font-medium mt-0.5">{{ $isRtl ? 'تسليم بالوقت المحدد' : 'On-time rate' }}</div>
                                </div>
                            </div>

                            {{-- Live Order Batches Table --}}
                            <div class="bg-slate-950/60 rounded-xl p-4 border border-slate-800/80 space-y-3">
                                <div class="flex items-center justify-between pb-2 border-b border-slate-800">
                                    <span class="text-xs font-bold text-slate-200">{{ $isRtl ? 'أحدث دفعات الغسيل المستلمة' : 'Recent Laundry Batches' }}</span>
                                    <span class="text-[10px] font-mono text-slate-400">{{ $isRtl ? 'تحديث لحظي' : 'Live Sync' }}</span>
                                </div>
                                
                                <div class="space-y-2">
                                    <div class="flex items-center justify-between text-xs py-1.5 px-2.5 rounded-lg bg-slate-800/50">
                                        <div class="flex items-center gap-2">
                                            <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                                            <span class="font-bold text-slate-100">#B2B-4081</span>
                                            <span class="text-slate-400 text-[11px]">{{ $isRtl ? 'بياضات وأغطية فندقية (420 قطعة)' : 'Hotel Linens (420 pcs)' }}</span>
                                        </div>
                                        <span class="text-[10px] font-bold text-emerald-400 bg-emerald-950/80 px-2 py-0.5 rounded-full border border-emerald-800/50">
                                            {{ $isRtl ? 'تم التسليم' : 'Delivered' }}
                                        </span>
                                    </div>

                                    <div class="flex items-center justify-between text-xs py-1.5 px-2.5 rounded-lg bg-slate-800/50">
                                        <div class="flex items-center gap-2">
                                            <span class="w-2 h-2 rounded-full bg-sky-400 animate-ping"></span>
                                            <span class="font-bold text-slate-100">#B2B-4088</span>
                                            <span class="text-slate-400 text-[11px]">{{ $isRtl ? 'مناشف مراكز سبا (280 قطعة)' : 'Spa Towels (280 pcs)' }}</span>
                                        </div>
                                        <span class="text-[10px] font-bold text-sky-400 bg-sky-950/80 px-2 py-0.5 rounded-full border border-sky-800/50">
                                            {{ $isRtl ? 'في الطريق للتسليم' : 'Out for Delivery' }}
                                        </span>
                                    </div>

                                    <div class="flex items-center justify-between text-xs py-1.5 px-2.5 rounded-lg bg-slate-800/50">
                                        <div class="flex items-center gap-2">
                                            <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                                            <span class="font-bold text-slate-100">#B2B-4093</span>
                                            <span class="text-slate-400 text-[11px]">{{ $isRtl ? 'أزياء طبية وأرواب (195 قطعة)' : 'Medical Scrubs (195 pcs)' }}</span>
                                        </div>
                                        <span class="text-[10px] font-bold text-amber-400 bg-amber-950/80 px-2 py-0.5 rounded-full border border-amber-800/50">
                                            {{ $isRtl ? 'جاري التعقيم والمعالجة' : 'Sterilizing' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- Super Host / Short-Term Rentals Section (Elevated UI) --}}
        <div class="bg-gradient-to-br from-indigo-50/80 via-white to-purple-50/80 rounded-3xl p-8 md:p-12 mb-24 border border-indigo-100 shadow-sm relative overflow-hidden" data-aos="fade-up">
            <div class="relative z-10 grid lg:grid-cols-12 gap-8 items-center">
                <div class="lg:col-span-8 text-center lg:text-start space-y-4">
                    <span class="inline-flex items-center gap-1.5 bg-indigo-100 text-indigo-700 px-3 py-1 rounded-full text-xs font-bold">
                        <span>✨</span>
                        <span>{{ trans('Short-term Rental Hosts') }}</span>
                    </span>
                    <h2 class="text-2xl sm:text-3xl md:text-4xl font-black text-slate-900">
                        {{ $isRtl ? 'خدمات مخصصة لمضيفي الشقق والعطلات (Airbnb & جاذر إن)' : 'Tailored Services for Vacation & Airbnb Hosts' }}
                    </h2>
                    <p class="text-slate-600 text-base leading-relaxed max-w-2xl">
                        {{ trans('Boost your ratings with premium laundry service. Offer exclusive discounts to guests and earn commission on every order.') }}
                    </p>
                    
                    <div class="grid sm:grid-cols-2 gap-4 pt-2 max-w-lg mx-auto lg:mx-0">
                        <div class="bg-white p-4 rounded-xl border border-indigo-100 shadow-xs flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center font-bold text-lg">★</div>
                            <div class="text-start">
                                <div class="font-black text-slate-900 text-base">5.0 {{ $isRtl ? 'تقييم فندقي' : 'Rating' }}</div>
                                <div class="text-xs text-slate-500">{{ trans('Boost Ratings') }}</div>
                            </div>
                        </div>
                        <div class="bg-white p-4 rounded-xl border border-indigo-100 shadow-xs flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold text-lg">%</div>
                            <div class="text-start">
                                <div class="font-black text-slate-900 text-base">{{ $isRtl ? 'عمولة نقدية' : 'Host Commission' }}</div>
                                <div class="text-xs text-slate-500">{{ trans('Earn Commission') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                {{-- Host Promo Card --}}
                <div class="lg:col-span-4 flex flex-col items-center">
                    <div class="bg-white p-6 rounded-2xl shadow-xl border border-indigo-200/80 w-full max-w-xs text-center space-y-4">
                        <div class="w-16 h-16 mx-auto rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        </div>
                        <div>
                            <div class="text-slate-900 font-extrabold text-base">{{ trans('Super Host Partner') }}</div>
                            <p class="text-xs text-slate-500 mt-0.5">{{ $isRtl ? 'كود الشريك المعتمد لضيوفك' : 'Approved Partner Code for Guests' }}</p>
                        </div>
                        <div class="bg-indigo-50/80 border border-dashed border-indigo-300 text-indigo-800 text-sm py-2.5 px-4 rounded-xl font-mono font-bold tracking-wider">
                            CODE: HOST2026
                        </div>
                        <p class="text-[11px] text-slate-400 font-medium">{{ trans('Works with Gathern, Airbnb, Booking') }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- B2B Partnership Lead Generation Form --}}
        <div id="b2b-form" class="max-w-3xl mx-auto bg-white rounded-3xl overflow-hidden shadow-xl border border-slate-200/80 relative" data-aos="fade-up">
            <div class="h-2 w-full bg-gradient-to-r from-sky-500 via-blue-600 to-indigo-600"></div>
            
            <div class="p-8 sm:p-12">
                <div class="text-center mb-8">
                    <span class="text-xs font-bold text-sky-600 uppercase tracking-wider">{{ $isRtl ? 'طلب شراكة وعقد مؤسسي' : 'Corporate Contract Request' }}</span>
                    <h2 class="text-2xl sm:text-3xl font-black text-slate-900 mt-1 mb-2">{{ trans('register your data') }}</h2>
                    <p class="text-slate-500 text-sm max-w-lg mx-auto">{{ trans('Register now for a system demo.') }}</p>
                </div>

                @if(session('success'))
                    <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-bold text-center">
                        {{ session('success') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm text-center">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form action="{{ route('client.register.store') }}" method="POST" class="space-y-5">
                    @csrf

                    <div class="grid md:grid-cols-2 gap-5">
                        <div class="space-y-1.5">
                            <label class="text-xs font-extrabold text-slate-700 uppercase tracking-wide">{{ trans('Facility Name') }} *</label>
                            <input type="text" name="company_name" value="{{ old('company_name') }}" placeholder="{{ $isRtl ? 'مثال: فندق العنوان / مجمع عيادات' : 'e.g. Grand Hotel / Medical Center' }}" 
                                   class="w-full bg-slate-50 border @error('company_name') border-rose-500 @else border-slate-200 @enderror p-3.5 rounded-xl focus:bg-white focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10 transition-all outline-none text-sm font-semibold text-slate-900" required>
                            @error('company_name')
                                <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-xs font-extrabold text-slate-700 uppercase tracking-wide">{{ trans('Contact Person') }} *</label>
                            <input type="text" name="contact_person" value="{{ old('contact_person') }}" placeholder="{{ $isRtl ? 'اسم المسؤول / مدير المشتريات' : 'Contact Person / Procurement Manager' }}" 
                                   class="w-full bg-slate-50 border @error('contact_person') border-rose-500 @else border-slate-200 @enderror p-3.5 rounded-xl focus:bg-white focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10 transition-all outline-none text-sm font-semibold text-slate-900" required>
                            @error('contact_person')
                                <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="grid md:grid-cols-2 gap-5">
                        <div class="space-y-1.5">
                            <label class="text-xs font-extrabold text-slate-700 uppercase tracking-wide">{{ trans('Mobile Number') }} *</label>
                            <input type="tel" name="phone" value="{{ old('phone') }}" placeholder="05xxxxxxxx" 
                                   class="w-full bg-slate-50 border @error('phone') border-rose-500 @else border-slate-200 @enderror p-3.5 rounded-xl focus:bg-white focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10 transition-all outline-none text-sm font-semibold text-slate-900 text-start" dir="ltr" required>
                            @error('phone')
                                <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-xs font-extrabold text-slate-700 uppercase tracking-wide">{{ trans('Business Type') }} *</label>
                            <select name="type" class="w-full bg-slate-50 border @error('type') border-rose-500 @else border-slate-200 @enderror p-3.5 rounded-xl focus:bg-white focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10 transition-all outline-none text-sm font-semibold text-slate-900">
                                <option value="Hotel" {{ old('type') == 'Hotel' ? 'selected' : '' }}>{{ $isRtl ? 'فنادق ومنتجعات' : 'Hotels & Resorts' }}</option>
                                <option value="Apartments" {{ old('type') == 'Apartments' ? 'selected' : '' }}>{{ $isRtl ? 'شقق مخدومة وعقارات مفروشة' : 'Serviced Apartments' }}</option>
                                <option value="Host" {{ old('type') == 'Host' ? 'selected' : '' }}>{{ $isRtl ? 'مضيف (جاذر إن / Airbnb)' : 'Host (Gathern / Airbnb)' }}</option>
                                <option value="Medical" {{ old('type') == 'Medical' ? 'selected' : '' }}>{{ $isRtl ? 'مستشفيات ومراكز طبية' : 'Hospitals & Medical Centers' }}</option>
                                <option value="Salon" {{ old('type') == 'Salon' ? 'selected' : '' }}>{{ $isRtl ? 'صالونات تجميل وسبا' : 'Beauty Salons & Spas' }}</option>
                                <option value="Gym" {{ old('type') == 'Gym' ? 'selected' : '' }}>{{ $isRtl ? 'نوادي رياضية ولياقة' : 'Gyms & Fitness Centers' }}</option>
                            </select>
                            @error('type')
                                <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-xs font-extrabold text-slate-700 uppercase tracking-wide">{{ trans('Est. Monthly Items') }} *</label>
                        <select name="monthly_items" class="w-full bg-slate-50 border @error('monthly_items') border-rose-500 @else border-slate-200 @enderror p-3.5 rounded-xl focus:bg-white focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10 transition-all outline-none text-sm font-semibold text-slate-900">
                            <option value="<1000" {{ old('monthly_items') == '<1000' ? 'selected' : '' }}>{{ $isRtl ? 'أقل من 1,000 قطعة شهرياً' : 'Less than 1,000 items / mo' }}</option>
                            <option value="1000-5000" {{ old('monthly_items') == '1000-5000' ? 'selected' : '' }}>{{ $isRtl ? '1,000 - 5,000 قطعة شهرياً' : '1,000 - 5,000 items / mo' }}</option>
                            <option value="5000-10000" {{ old('monthly_items') == '5000-10000' ? 'selected' : '' }}>{{ $isRtl ? '5,000 - 10,000 قطعة شهرياً' : '5,000 - 10,000 items / mo' }}</option>
                            <option value=">10000" {{ old('monthly_items') == '>10000' ? 'selected' : '' }}>{{ $isRtl ? 'أكثر من 10,000 قطعة (عقد مؤسسي كبير)' : 'More than 10,000 items (Enterprise Retainer)' }}</option>
                        </select>
                        @error('monthly_items')
                            <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="w-full bg-gradient-to-r from-sky-600 via-sky-700 to-blue-700 hover:from-sky-700 hover:to-blue-800 text-white font-extrabold text-base py-4 rounded-xl shadow-lg shadow-sky-600/25 hover:shadow-xl hover:-translate-y-0.5 transition-all duration-200 flex items-center justify-center gap-2">
                        <span>{{ trans('Submit Partnership Request') }}</span>
                        <svg class="w-4 h-4 {{ $isRtl ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </button>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-4 text-center text-xs text-slate-500 border-t border-slate-100">
                        <div class="flex items-center justify-center gap-1.5">
                            <span class="text-emerald-500">✓</span>
                            <span>{{ $isRtl ? 'رد سريع خلال ساعتين عمل' : 'Response within 2h' }}</span>
                        </div>
                        <div class="flex items-center justify-center gap-1.5">
                            <span class="text-emerald-500">✓</span>
                            <span>{{ $isRtl ? 'غسيل تجريبي لتقييم الجودة' : 'Free sample wash test' }}</span>
                        </div>
                        <div class="flex items-center justify-center gap-1.5">
                            <span class="text-emerald-500">✓</span>
                            <span>{{ $isRtl ? 'عقود رسمية واتفاقيات SLA' : 'Official SLA contracts' }}</span>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var b2bForm = document.querySelector('form[action="{{ route('client.register.store') }}"]');
    if (b2bForm) {
        b2bForm.addEventListener('submit', function() {
            var sector = b2bForm.querySelector('select[name="type"]');
            var volume = b2bForm.querySelector('select[name="monthly_items"]');
            window.cleanTrack && window.cleanTrack.b2bLeadSubmit(
                sector ? sector.value : 'unknown',
                volume ? volume.value : 'unknown'
            );
        });
    }
});
</script>
