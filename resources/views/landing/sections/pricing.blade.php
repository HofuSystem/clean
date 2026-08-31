@php
    $isRtl = app()->getLocale() == 'ar';

    // Icons mapping for categories
    $catIcons = [
        'economic-bags' => 'fa-suitcase',
        'mens-laundry' => 'fa-user-tie',
        'womens-laundry' => 'fa-person-dress',
        'medical-military' => 'fa-user-nurse',
        'carpets-furnishings' => 'fa-couch',
        'flowers-and-gifts' => 'fa-gift',
    ];

    function getCatIcon($slug, $icons) {
        return $icons[$slug] ?? 'fa-tags';
    }

    if (!function_exists('getCatColorHex')) {
        function getCatColorHex($slug) {
            if ($slug == 'womens-laundry') return '#db2777'; // pink-600
            if ($slug == 'medical-military') return '#0a829f'; // teal
            if ($slug == 'carpets-furnishings') return '#4f46e5'; // indigo-600
            if ($slug == 'gifts-and-flowers') return '#f43f5e'; // rose-500
            return '#008bd2'; // sky blue default (mens-laundry, economic-bags)
        }
    }
@endphp

<div class="bg-slate-50 font-['Tajawal']">
    <!-- Hero Section -->
    <section class="bg-gradient-to-b from-sky-50 to-white py-10 md:py-14 text-center">
        <div class="max-w-5xl mx-auto px-4">
            <nav class="text-xs text-gray-500 mb-3 flex items-center justify-center gap-2 font-medium">
                <a href="{{ url('/') }}" class="hover:text-sky-600">{{ $isRtl ? 'الرئيسية' : 'Home' }}</a> <span class="text-gray-300">/</span>
                <span class="text-gray-800 font-bold">{{ $isRtl ? 'الأسعار' : 'Pricing' }}</span>
            </nav>
            <h1 class="text-3xl md:text-5xl font-black text-slate-900 leading-tight mb-3">
                {{ $isRtl ? 'قائمة الأسعار' : 'Pricing List' }}
            </h1>
            <p class="text-gray-600 text-sm md:text-base max-w-2xl mx-auto leading-relaxed mb-6">
                @if($isRtl)
                    غسيل ملابسك يرجع لك جاهز ومرتب من <strong>21 إلى 24 ساعة فقط</strong>. استلام وتوصيل مجاني لباب بيتك للطلبات بـ <strong>100 ريال وفوق</strong> مع عروض مستمرة أسبوعياً.
                @else
                    Your laundry is delivered back clean and organized within <strong>21 to 24 hours only</strong>. Free pickup and delivery for orders of <strong>100 SAR and above</strong> with weekly offers.
                @endif
            </p>

            <!-- 3 Top Highlights -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 max-w-4xl mx-auto {{ $isRtl ? 'text-right' : 'text-left' }}">
                <div class="bg-white p-4 rounded-2xl border border-sky-100 shadow-sm flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-sky-100 text-sky-600 flex items-center justify-center flex-shrink-0 text-lg">
                        <i class="fa-solid fa-truck-fast"></i>
                    </div>
                    <div>
                        <div class="font-bold text-slate-900 text-xs md:text-sm">{{ $isRtl ? 'استلام وتوصيل مجاني' : 'Free Pickup & Delivery' }}</div>
                        <div class="text-[11px] text-gray-500">{{ $isRtl ? 'للطلبات 100 ر.س وفوق وعروض مستمرة' : 'For orders 100 SAR+ and ongoing deals' }}</div>
                    </div>
                </div>
                <div class="bg-white p-4 rounded-2xl border border-sky-100 shadow-sm flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center flex-shrink-0 text-lg">
                        <i class="fa-solid fa-stopwatch-20"></i>
                    </div>
                    <div>
                        <div class="font-bold text-slate-900 text-xs md:text-sm">{{ $isRtl ? 'الملابس من 21 إلى 24 ساعة' : 'Clothes in 21 to 24 Hours' }}</div>
                        <div class="text-[11px] text-gray-500">{{ $isRtl ? 'غسيل وكي، كوي فقط، أو غسيل جاف' : 'Wash & iron, iron only, or dry clean' }}</div>
                    </div>
                </div>
                <div class="bg-white p-4 rounded-2xl border border-sky-100 shadow-sm flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-violet-100 text-violet-600 flex items-center justify-center flex-shrink-0 text-lg">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                    <div>
                        <div class="font-bold text-slate-900 text-xs md:text-sm">{{ $isRtl ? 'غسيل منفصل 100%' : '100% Separate Washing' }}</div>
                        <div class="text-[11px] text-gray-500">{{ $isRtl ? 'كل طلب يغسل في غسالة مستقلة' : 'Each order washed in dedicated machine' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content Section -->
    <section class="max-w-5xl mx-auto px-4 mb-8">
        <!-- Time Slots Section -->
        <div class="bg-slate-900 text-white rounded-3xl p-5 md:p-6 shadow-sm mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-3">
                <h3 class="font-bold text-sm md:text-base flex items-center gap-2">
                    <i class="fa-solid fa-calendar-check text-sky-400"></i> {{ $isRtl ? 'فترات الاستلام والتوصيل (كل ساعتين)' : 'Pickup & Delivery Slots (Every 2 Hours)' }}
                </h3>
                <span class="text-xs text-sky-300 font-medium">{{ $isRtl ? 'اختر الوقت اللي يناسبك بالتطبيق:' : 'Choose convenient time in app:' }}</span>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-7 gap-2 text-center text-xs font-medium" dir="ltr">
                <div class="bg-slate-800 p-2.5 rounded-xl border border-slate-700">{{ $isRtl ? '10:00 ص – 12:00 م' : '10:00 AM – 12:00 PM' }}</div>
                <div class="bg-slate-800 p-2.5 rounded-xl border border-slate-700">{{ $isRtl ? '12:00 م – 02:00 م' : '12:00 PM – 02:00 PM' }}</div>
                <div class="bg-slate-800 p-2.5 rounded-xl border border-slate-700">{{ $isRtl ? '02:00 م – 04:00 م' : '02:00 PM – 04:00 PM' }}</div>
                <div class="bg-slate-800 p-2.5 rounded-xl border border-slate-700">{{ $isRtl ? '04:00 م – 06:00 م' : '04:00 PM – 06:00 PM' }}</div>
                <div class="bg-slate-800 p-2.5 rounded-xl border border-slate-700">{{ $isRtl ? '06:00 م – 08:00 م' : '06:00 PM – 08:00 PM' }}</div>
                <div class="bg-slate-800 p-2.5 rounded-xl border border-slate-700">{{ $isRtl ? '08:00 م – 10:00 م' : '08:00 PM – 10:00 PM' }}</div>
                <div class="bg-slate-800 p-2.5 rounded-xl border border-slate-700 col-span-2 sm:col-span-1">{{ $isRtl ? '10:00 م – 11:59 م' : '10:00 PM – 11:59 PM' }}</div>
            </div>
        </div>

        <!-- Live Search -->
        <div class="relative mb-6">
            <i class="fa-solid fa-magnifying-glass absolute {{ $isRtl ? 'right-4' : 'left-4' }} top-1/2 -translate-y-1/2 text-gray-400"></i>
            <input type="text" id="priceSearch" placeholder="{{ $isRtl ? 'ابحث عن أي قطعة أو خدمة... (مثال: ثوب، عباية، سكراب، بدلة عسكرية، حقيبة، بطانية)' : 'Search for any item or service... (e.g. Thobe, Abaya, Scrubs, Military uniform, Bag, Blanket)' }}" class="w-full {{ $isRtl ? 'pr-12 pl-4' : 'pl-12 pr-4' }} py-3.5 rounded-2xl border-2 border-gray-200 bg-white text-sm outline-none font-medium focus:border-sky-600 transition shadow-sm">
        </div>

        <!-- Tabs Container -->
        <div class="flex flex-wrap gap-2 mb-6" id="priceTabs">
            @foreach($categories as $index => $category)
                <button class="pricing-tab {{ $index === 0 ? 'active' : '' }} px-4 py-2.5 rounded-xl font-bold text-xs md:text-sm border shadow-sm flex items-center gap-1.5" 
                        data-tab="{{ $category->slug }}"
                        data-color="{{ getCatColorHex($category->slug) }}"
                        @if($index === 0) style="background-color: {{ getCatColorHex($category->slug) }}; border-color: {{ getCatColorHex($category->slug) }};" @endif>
                    <i class="fa-solid {{ getCatIcon($category->slug, $catIcons) }}"></i> <span>{{ $category->name }}</span>
                </button>
            @endforeach
        </div>

        <!-- Tab Panels -->
        @foreach($categories as $index => $category)
            @php
                $durationText = $isRtl ? 'المدة: 24 ساعة فقط' : 'Duration: 24 Hours Only';
                if ($category->slug == 'carpets-furnishings') {
                    $hours = $settings['carpets_hours'] ?? 96;
                    $days = max(1, round($hours / 24));
                    $durationText = $isRtl ? "المدة: $days أيام" : "Duration: $days Days";
                } elseif ($category->type == 'clothes') {
                    $hours = $settings['clothes_hours'] ?? 24;
                    $durationText = $isRtl ? "المدة: $hours ساعة فقط" : "Duration: $hours Hours Only";
                }
            @endphp

            <div class="price-panel w-full {{ $index === 0 ? '' : 'hidden' }} fade-in" id="panel-{{ $category->slug }}">
                
                @if($category->slug == 'carpets-furnishings' || str_contains($category->name, 'سجاد') || str_contains($category->slug, 'carpet'))
                <!-- Carpet Calculator -->
                @php
                    $carpetPrice = 18; 
                    $carpetHours = $settings['carpets_hours'] ?? 96;
                    $carpetDays = max(1, round($carpetHours / 24));
                    $carpetDuration = $isRtl ? "المدة {$carpetDays} أيام" : "Duration: {$carpetDays} Days";
                @endphp
                <div class="w-full bg-white rounded-3xl border border-gray-200 overflow-hidden shadow-sm mb-6 p-6 md:p-8 {{ $isRtl ? 'text-right' : 'text-left' }}">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                        <div>
                            <h3 class="font-black text-xl text-[#0c1f38] mb-1">
                                {{ $isRtl ? 'حاسبة تسعير السجاد والموكيت' : 'Carpets & Rugs Price Calculator' }}
                            </h3>
                            <p class="text-sm text-gray-400">
                                {{ $isRtl ? "حساب فوري بالمتر المربع ($carpetPrice ر.س / م٢)" : "Instant calculation per square meter ($carpetPrice SAR / m²)" }}
                            </p>
                        </div>
                        <div class="bg-[#e6faee] text-[#00b050] px-4 py-2 rounded-full text-sm font-bold border border-[#b2f5d0]">
                            {{ $isRtl ? "$carpetPrice ر.س / م٢" : "$carpetPrice SAR / m²" }}
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-8">
                        <div>
                            <label class="block text-xs text-gray-500 mb-2 font-bold px-1">{{ $isRtl ? 'الطول (متر)' : 'Length (meters)' }}</label>
                            <input type="number" id="carpetLength" value="3" min="1" class="w-full bg-gray-50/50 border border-gray-200 rounded-2xl px-5 py-4 outline-none focus:border-[#00b050] focus:ring-4 focus:ring-[#00b050]/10 transition {{ $isRtl ? 'text-left' : 'text-left' }} text-lg font-bold text-gray-700" dir="ltr">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 mb-2 font-bold px-1">{{ $isRtl ? 'العرض (متر)' : 'Width (meters)' }}</label>
                            <input type="number" id="carpetWidth" value="2" min="1" class="w-full bg-gray-50/50 border border-gray-200 rounded-2xl px-5 py-4 outline-none focus:border-[#00b050] focus:ring-4 focus:ring-[#00b050]/10 transition {{ $isRtl ? 'text-left' : 'text-left' }} text-lg font-bold text-gray-700" dir="ltr">
                        </div>
                    </div>
                    <div class="flex justify-between items-center bg-gray-50/80 rounded-2xl p-5 border border-gray-100">
                        <div class="text-sm text-gray-500 font-medium" id="carpetAreaText">
                            {{ $isRtl ? "المساحة: 6.00 م٢ ($carpetDuration)" : "Area: 6.00 m² ($carpetDuration)" }}
                        </div>
                        <div class="text-[#00b050] font-black text-3xl" id="carpetPriceText">
                            {{ $carpetPrice * 6 }} <span class="text-sm font-normal text-gray-500">{{ $isRtl ? 'ر.س' : 'SAR' }}</span>
                        </div>
                    </div>
                </div>
                @endif

                @if($category->slug == 'economic-bags')
                    <!-- Economic Bags Layout -->
                    <div class="mb-10 w-full">
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 mb-6">
                            @foreach($category->products->filter(fn($p) => $p->is_package) as $bag)
                                @php
                                    $bagName = mb_strtolower($bag->name);
                                    $enName = $bag->translate('en') ? mb_strtolower($bag->translate('en')->name) : '';
                                    $icon = 'fa-suitcase';
                                    $color = 'text-blue-600';
                                    $bg = 'bg-blue-50';
                                    $borderColor = 'border border-gray-200';
                                    $badge = null;
                                    
                                    if (str_contains($bagName, 'دراي') || str_contains($enName, 'dry')) {
                                        $icon = 'fa-crown';
                                        $color = 'text-violet-600';
                                        $bg = 'bg-violet-50';
                                    } elseif (str_contains($bagName, 'طي') || str_contains($enName, 'fold')) {
                                        $icon = 'fa-shirt';
                                        $color = 'text-indigo-600';
                                        $bg = 'bg-indigo-50';
                                        $badge = $isRtl ? 'الأكثر طلباً' : 'Most Popular';
                                    } elseif (str_contains($bagName, 'كوي') || str_contains($bagName, 'كي') || str_contains($enName, 'iron')) {
                                        $icon = 'fa-wand-magic-sparkles';
                                        $color = 'text-emerald-600';
                                        $bg = 'bg-emerald-50';
                                    }
                                @endphp
                                <div class="bg-white rounded-3xl p-6 {{ $borderColor }} shadow-sm relative flex flex-col justify-between {{ $isRtl ? 'text-right' : 'text-left' }}">
                                    @if($badge)
                                    <span class="absolute top-4 {{ $isRtl ? 'left-4' : 'right-4' }} bg-sky-100 text-sky-700 text-[11px] font-bold px-2.5 py-0.5 rounded-full">{{ $badge }}</span>
                                    @endif
                                    <div>
                                        <div class="w-12 h-12 rounded-2xl {{ $bg }} {{ $color }} flex items-center justify-center mb-4 text-xl">
                                            <i class="fa-solid {{ $icon }}"></i>
                                        </div>
                                        <h3 class="font-bold text-slate-900 text-base mb-1">{{ $bag->name }}</h3>
                                        <p class="text-xs text-gray-500 mb-4">{{ $bag->desc }}</p>
                                    </div>
                                    <div class="border-t pt-4 flex items-baseline justify-between">
                                        <span class="text-xs text-gray-500">
                                            @if(str_contains($bag->name, '25') || str_contains($bagName, '25'))
                                                {{ $isRtl ? '25 قطعة' : '25 Items' }}
                                            @elseif(str_contains($bag->name, '10') || str_contains($bagName, '10'))
                                                {{ $isRtl ? '10 قطع' : '10 Items' }}
                                            @else
                                                {{ $isRtl ? 'حقيبة واحدة' : '1 Bag' }}
                                            @endif
                                        </span>
                                        <span class="text-2xl font-black {{ $color }}">{{ $bag->price }} <span class="text-xs font-normal text-gray-500">{{ $isRtl ? 'ر.س' : 'SAR' }}</span></span>
                                    </div>
                                </div>
                            @endforeach

                            <!-- Extra items add-on -->
                            <div class="bg-sky-50 rounded-3xl p-6 border border-sky-200 col-span-1 sm:col-span-2 md:col-span-2 flex flex-col sm:flex-row items-center justify-between gap-4 {{ $isRtl ? 'text-right' : 'text-left' }}">
                                <div>
                                    <h4 class="font-bold text-slate-900 text-sm mb-1">{{ $isRtl ? 'قطع إضافية فوق سعة الحقيبة:' : 'Extra items beyond bag capacity:' }}</h4>
                                    <p class="text-xs text-gray-600">{{ $isRtl ? 'تقدر تضيف أي عدد قطع إضافية على الحقيبة الاقتصادية بسعر رمزي:' : 'You can add any extra pieces to your economic bag at a nominal price:' }}</p>
                                </div>
                                <div class="flex gap-4">
                                    <div class="bg-white px-4 py-2.5 rounded-2xl border text-center">
                                        <div class="text-[11px] text-gray-500">{{ $isRtl ? 'إضافي باقة A' : 'Extra Package A' }}</div>
                                        <div class="font-bold text-sky-700 text-sm">{{ $isRtl ? '5 ر.س / قطعة' : '5 SAR / piece' }}</div>
                                    </div>
                                    <div class="bg-white px-4 py-2.5 rounded-2xl border text-center">
                                        <div class="text-[11px] text-gray-500">{{ $isRtl ? 'إضافي باقة B' : 'Extra Package B' }}</div>
                                        <div class="font-bold text-sky-700 text-sm">{{ $isRtl ? '4 ر.س / قطعة' : '4 SAR / piece' }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                <!-- Category Table -->
                <div class="w-full bg-white rounded-3xl border border-gray-200 overflow-hidden shadow-md">
                    <!-- Panel Header -->
                    <div class="text-white px-6 py-5 flex justify-between items-center relative overflow-hidden" 
                         style="background-color: {{ getCatColorHex($category->slug) }}">
                        
                        <div class="flex items-center gap-4 relative z-10">
                            <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid {{ getCatIcon($category->slug, $catIcons) }} text-xl"></i>
                            </div>
                            <div class="{{ $isRtl ? 'text-right' : 'text-left' }}">
                                <h2 class="font-bold text-xl mb-0.5">
                                    @if($category->slug == 'carpets-furnishings')
                                        {{ $isRtl ? 'المفروشات والبطانيات' : 'Furnishings & Blankets' }}
                                    @else
                                        {{ $category->name }}
                                    @endif
                                </h2>
                                <p class="text-xs text-sky-100" style="color: rgba(255,255,255,0.85)">
                                    @if($category->slug == 'flowers-and-gifts' || $category->type == 'sales')
                                        {{ $isRtl ? 'أجمل باقات الورد والهدايا لكل المناسبات وتوصيل سريع' : 'Finest flowers and gifts for all occasions with fast delivery' }}
                                    @elseif($category->slug == 'carpets-furnishings')
                                        {{ $isRtl ? 'تعقيم حراري وتغليف محكم' : 'Thermal sanitization and secure packaging' }}
                                    @else
                                        {{ $isRtl ? 'غسيل منفصل 100%، كوي بخار وتغليف معاليق' : '100% Separate wash, steam iron and hanger packaging' }}
                                    @endif
                                </p>
                            </div>
                        </div>

                        <!-- Duration Badge -->
                        @if($category->slug != 'flowers-and-gifts' && $category->type != 'sales')
                        <div class="{{ $category->slug == 'carpets-furnishings' ? 'bg-indigo-700/60' : 'bg-black/10 border border-white/20' }} px-4 py-1.5 rounded-full text-xs font-bold shadow-inner relative z-10 hidden sm:block">
                            @if($category->slug == 'carpets-furnishings')
                                {{ $isRtl ? 'تنفيذ بعناية' : 'Executed with Care' }}
                            @else
                                {{ $durationText }}
                            @endif
                        </div>
                        @endif
                    </div>
                    
                    <!-- Table -->
                    <div class="overflow-x-auto w-full">
                        <table class="w-full text-sm {{ $isRtl ? 'text-right' : 'text-left' }} price-table" data-slug="{{ $category->slug }}">
                            <thead class="bg-white text-gray-500 border-b border-gray-100 font-bold sticky top-0 shadow-sm">
                                <tr>
                                    <th class="px-6 py-4">{{ $isRtl ? 'القطعة' : 'Item' }}</th>
                                    <th class="px-6 py-4 text-center {{ $category->slug == 'carpets-furnishings' ? 'text-indigo-700' : 'text-[#008bd2]' }}">{{ $isRtl ? 'غسيل وكي' : 'Wash & Iron' }}</th>
                                    @if($category->slug != 'carpets-furnishings')
                                    <th class="px-6 py-4 text-center text-gray-600">{{ $isRtl ? 'كوي فقط' : 'Iron Only' }}</th>
                                    @endif
                                    <th class="px-6 py-4 text-center text-purple-600">{{ $isRtl ? 'غسيل جاف (Dry Clean)' : 'Dry Clean' }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-[13px] pagination-container">
                                @php
                                    $filteredProducts = $category->products->filter(function($p) {
                                        $n = mb_strtolower($p->name);
                                        return $p->price > 0 && !str_contains($n, 'استرداد') && !str_contains($n, 'رسوم') && !str_contains($n, 'fee');
                                    });

                                    $groupedProducts = $filteredProducts->groupBy(function($p) use ($isRtl) {
                                        if ($isRtl) {
                                            $arName = $p->translate('ar') ? $p->translate('ar')->name : $p->name;
                                            $s = mb_strtolower(trim($arName));
                                            $s = preg_replace('/[أإآ]/u', 'ا', $s);
                                            $s = preg_replace('/[ة]/u', 'ه', $s);
                                            $s = preg_replace('/[ى]/u', 'ي', $s);
                                            $s = preg_replace('/\s+/u', ' ', $s);
                                            return $s;
                                        } else {
                                            $enName = $p->translate('en') ? $p->translate('en')->name : $p->name;
                                            return strtolower(trim(preg_replace('/\s+/', ' ', $enName)));
                                        }
                                    });
                                @endphp
                                
                                @foreach($groupedProducts as $groupKey => $products)
                                    @php
                                        $productName = $products->first()->name;
                                        $washIron = $products->first(function($p) {
                                            if (!$p->subCategory) return true;
                                            $slug = strtolower($p->subCategory->slug);
                                            $name = strtolower($p->subCategory->name ?? '');
                                            return ($slug === 'carpet' || str_contains($slug, 'wash') || str_contains($name, 'غسيل') || str_contains($name, 'wash')) && !str_contains($slug, 'dry') && !str_contains($name, 'جاف');
                                        });
                                        $ironOnly = $products->first(function($p) {
                                            if (!$p->subCategory) return false;
                                            $slug = strtolower($p->subCategory->slug);
                                            $name = strtolower($p->subCategory->name ?? '');
                                            return (str_contains($slug, 'iron') || str_contains($name, 'كوي') || str_contains($name, 'كي')) && !str_contains($slug, 'wash') && !str_contains($name, 'غسيل');
                                        });
                                        $dryClean = $products->first(function($p) {
                                            if (!$p->subCategory) return false;
                                            $slug = strtolower($p->subCategory->slug);
                                            $name = strtolower($p->subCategory->name ?? '');
                                            return str_contains($slug, 'dry') || str_contains($name, 'جاف') || str_contains($name, 'dry');
                                        });
                                        
                                        if (!$washIron && !$ironOnly && !$dryClean) {
                                            $washIron = $products->first();
                                        }
                                        $currency = $isRtl ? 'ر.س' : 'SAR';
                                    @endphp
                                    <tr class="hover:bg-sky-50/30 transition group pagination-item" data-search="{{ mb_strtolower($productName) }}">
                                        <td class="px-6 py-4 font-medium text-slate-800">{{ $productName }}</td>
                                        
                                        <td class="px-6 py-4 text-center {{ $category->slug == 'carpets-furnishings' ? 'text-indigo-700' : 'text-[#008bd2]' }} font-bold">
                                            {{ $washIron ? $washIron->price . ' ' . $currency : '-' }}
                                        </td>
                                        @if($category->slug != 'carpets-furnishings')
                                        <td class="px-6 py-4 text-center text-gray-600 font-bold">
                                            {{ $ironOnly ? $ironOnly->price . ' ' . $currency : '-' }}
                                        </td>
                                        @endif
                                        <td class="px-6 py-4 text-center text-purple-600 font-bold">
                                            {{ $dryClean ? $dryClean->price . ' ' . $currency : '-' }}
                                        </td>
                                    </tr>
                                @endforeach
                                @if($category->products->isEmpty())
                                    <tr>
                                        <td colspan="{{ $category->slug == 'carpets-furnishings' ? 3 : 4 }}" class="px-6 py-8 text-center text-gray-400">
                                            {{ $isRtl ? 'لا توجد منتجات حالياً في هذا القسم' : 'No products available in this category' }}
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination Controls -->
                    <div class="px-6 py-4 bg-gray-50/70 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-3 pagination-controls" data-target="{{ $category->slug }}">
                        <span class="text-xs text-gray-500 font-medium pagination-info">
                            {{ $isRtl ? 'عرض 1 إلى 10 من 24 عنصر' : 'Showing 1 to 10 of 24 items' }}
                        </span>
                        <div class="flex items-center gap-1.5 pagination-buttons">
                            <button class="w-8 h-8 rounded-lg border border-gray-200 bg-white flex items-center justify-center text-gray-600 hover:bg-sky-50 hover:text-sky-600 transition disabled:opacity-30 disabled:pointer-events-none prev-page">
                                <i class="fa-solid fa-chevron-{{ $isRtl ? 'right' : 'left' }} text-xs"></i>
                            </button>
                            <div class="flex items-center gap-1 page-numbers"></div>
                            <button class="w-8 h-8 rounded-lg border border-gray-200 bg-white flex items-center justify-center text-gray-600 hover:bg-sky-50 hover:text-sky-600 transition disabled:opacity-30 disabled:pointer-events-none next-page">
                                <i class="fa-solid fa-chevron-{{ $isRtl ? 'left' : 'right' }} text-xs"></i>
                            </button>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        @endforeach

        <!-- Bottom CTA -->
        <div class="text-center py-12 mt-6">
            <a href="https://cleanstation.app.link/?channel=website" data-app-cta class="inline-flex items-center gap-3 bg-[#008bd2] text-white px-10 py-4 rounded-full font-bold text-base shadow-lg hover:bg-sky-600 transition hover:-translate-y-1">
                <i class="fa-solid fa-mobile-screen"></i>
                {{ $isRtl ? 'اطلب عبر التطبيق الحين' : 'Order via App Now' }}
            </a>
        </div>
    </section>
</div>

@push('scripts')
<style>
    .pricing-tab { cursor: pointer; transition: all 0.2s ease; background-color: #ffffff; color: #475569; border: 1px solid #e2e8f0; }
    .pricing-tab:not(.active):hover { background-color: #f8fafc; color: #0284c7; }
    .pricing-tab.active { color: #ffffff !important; box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
    .search-hidden { display: none !important; }
    .page-hidden { display: none !important; }
</style>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const isRtl = {{ $isRtl ? 'true' : 'false' }};
        const tabs = document.querySelectorAll('.pricing-tab');
        const panels = document.querySelectorAll('.price-panel');

        function setActiveTab(tab) {
            tabs.forEach(t => {
                t.classList.remove('active');
                t.style.backgroundColor = '';
                t.style.borderColor = '';
                t.style.color = '';
            });
            tab.classList.add('active');
            tab.style.backgroundColor = tab.dataset.color;
            tab.style.borderColor = tab.dataset.color;
            tab.style.color = '#ffffff';

            panels.forEach(p => p.classList.add('hidden'));
            const targetId = 'panel-' + tab.dataset.tab;
            const panel = document.getElementById(targetId);
            if(panel) panel.classList.remove('hidden');
        }

        tabs.forEach(tab => {
            tab.addEventListener('click', function() {
                setActiveTab(this);
            });
        });

        // Carpet Calculator Live Calculation
        const lInput = document.getElementById('carpetLength');
        const wInput = document.getElementById('carpetWidth');
        const pText = document.getElementById('carpetPriceText');
        const aText = document.getElementById('carpetAreaText');
        const pricePerMeter = 18;
        const durationText = isRtl ? 'المدة 4 أيام' : 'Duration: 4 Days';
        const currency = isRtl ? 'ر.س' : 'SAR';

        function calcCarpet() {
            if(!lInput || !wInput) return;
            const l = parseFloat(lInput.value) || 0;
            const w = parseFloat(wInput.value) || 0;
            const area = l * w;
            const price = area * pricePerMeter;
            if(aText) aText.textContent = isRtl ? `المساحة: ${area.toFixed(2)} م٢ (${durationText})` : `Area: ${area.toFixed(2)} m² (${durationText})`;
            if(pText) pText.innerHTML = `${Math.round(price)} <span class="text-sm font-normal text-gray-500">${currency}</span>`;
        }
        if(lInput) lInput.addEventListener('input', calcCarpet);
        if(wInput) wInput.addEventListener('input', calcCarpet);

        // Live Search logic
        const searchInput = document.getElementById('priceSearch');
        if(searchInput) {
            searchInput.addEventListener('input', function(e) {
                const query = e.target.value.toLowerCase().trim();
                
                if(query.length > 0) {
                    panels.forEach(p => p.classList.remove('hidden'));
                    document.querySelectorAll('.pagination-controls').forEach(c => c.style.display = 'none');
                } else {
                    panels.forEach(p => p.classList.add('hidden'));
                    const activeTab = document.querySelector('.pricing-tab.active');
                    if(activeTab) {
                        const targetId = 'panel-' + activeTab.dataset.tab;
                        const panel = document.getElementById(targetId);
                        if(panel) panel.classList.remove('hidden');
                    }
                    initAllPaginations();
                }

                document.querySelectorAll('.price-table tbody tr.pagination-item').forEach(row => {
                    const searchStr = row.dataset.search || row.textContent.toLowerCase();
                    if(query === '' || searchStr.includes(query)) {
                        row.classList.remove('search-hidden');
                    } else {
                        row.classList.add('search-hidden');
                    }
                });
            });
        }

        // Pagination Logic
        const PAGE_SIZE = 10;

        function initPagination(slug) {
            const table = document.querySelector(`.price-table[data-slug="${slug}"]`);
            if (!table) return;

            const controls = document.querySelector(`.pagination-controls[data-target="${slug}"]`);
            if (!controls) return;

            const items = table.querySelectorAll('tbody tr.pagination-item');
            const totalItems = items.length;
            const totalPages = Math.ceil(totalItems / PAGE_SIZE);

            if (totalItems <= PAGE_SIZE) {
                controls.style.display = 'none';
                items.forEach(i => i.classList.remove('page-hidden'));
                return;
            }

            controls.style.display = 'flex';
            let currentPage = 1;

            const infoEl = controls.querySelector('.pagination-info');
            const pagesContainer = controls.querySelector('.page-numbers');
            const prevBtn = controls.querySelector('.prev-page');
            const nextBtn = controls.querySelector('.next-page');

            function renderPage(page) {
                currentPage = page;
                const start = (page - 1) * PAGE_SIZE;
                const end = Math.min(start + PAGE_SIZE, totalItems);

                items.forEach((item, idx) => {
                    if (idx >= start && idx < end) {
                        item.classList.remove('page-hidden');
                    } else {
                        item.classList.add('page-hidden');
                    }
                });

                if (infoEl) {
                    infoEl.textContent = isRtl
                        ? `عرض ${start + 1} إلى ${end} من ${totalItems} عنصر`
                        : `Showing ${start + 1} to ${end} of ${totalItems} items`;
                }

                prevBtn.disabled = (currentPage === 1);
                nextBtn.disabled = (currentPage === totalPages);

                pagesContainer.innerHTML = '';
                for (let i = 1; i <= totalPages; i++) {
                    const btn = document.createElement('button');
                    btn.className = `w-8 h-8 rounded-lg text-xs font-bold transition ${i === currentPage ? 'bg-sky-600 text-white shadow-sm' : 'bg-white border border-gray-200 text-gray-700 hover:bg-sky-50 hover:text-sky-600'}`;
                    btn.textContent = i;
                    btn.addEventListener('click', () => renderPage(i));
                    pagesContainer.appendChild(btn);
                }
            }

            const newPrevBtn = prevBtn.cloneNode(true);
            const newNextBtn = nextBtn.cloneNode(true);
            prevBtn.parentNode.replaceChild(newPrevBtn, prevBtn);
            nextBtn.parentNode.replaceChild(newNextBtn, nextBtn);

            newPrevBtn.addEventListener('click', () => {
                if (currentPage > 1) renderPage(--currentPage);
            });
            newNextBtn.addEventListener('click', () => {
                if (currentPage < totalPages) renderPage(++currentPage);
            });

            renderPage(1);
        }

        function initAllPaginations() {
            document.querySelectorAll('.pagination-controls').forEach(controls => {
                controls.style.display = 'flex';
                initPagination(controls.dataset.target);
            });
        }

        initAllPaginations();
    });
</script>
@endpush
