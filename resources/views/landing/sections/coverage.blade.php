@php
    $isRtl = app()->getLocale() == 'ar';

    $totalDistrictsCount = $cities->sum(function($c) {
        return $c->districts->count();
    });

    $cityIcons = [
        'الرياض' => 'fa-city',
        'Riyadh' => 'fa-city',
        'المبرز' => 'fa-tree-city',
        'الأحساء' => 'fa-tree-city',
        'Al Mubarraz' => 'fa-tree-city',
        'Al-Mubarraz' => 'fa-tree-city',
        'جدة' => 'fa-water',
        'Jeddah' => 'fa-water',
        'الدمام' => 'fa-building-columns',
        'Dammam' => 'fa-building-columns',
    ];

    function getCityIcon($cityName, $icons) {
        foreach ($icons as $key => $icon) {
            if (str_contains($cityName, $key)) {
                return $icon;
            }
        }
        return 'fa-city';
    }

    function getCityDisplayName($city) {
        $name = $city->name;
        if (str_contains($name, 'المبرز') || str_contains($name, 'Mubarraz')) {
            return app()->getLocale() == 'ar' ? 'المبرز - الأحساء' : 'Al-Mubarraz - Al-Ahsa';
        }
        return $name;
    }
@endphp

<div class="bg-slate-50 font-['Tajawal']">
    <!-- Hero Section -->
    <section class="bg-gradient-to-b from-sky-50 to-white py-10 md:py-14 text-center">
        <div class="max-w-5xl mx-auto px-4">
            <nav class="text-xs text-gray-500 mb-3 flex items-center justify-center gap-2 font-medium">
                <a href="{{ url('/') }}" class="hover:text-sky-600">{{ $isRtl ? 'الرئيسية' : 'Home' }}</a> <span class="text-gray-300">/</span>
                <span class="text-gray-800 font-bold">{{ $isRtl ? 'تغطية الأحياء والمدن' : 'Coverage' }}</span>
            </nav>
            <h1 class="text-3xl md:text-5xl font-black text-slate-900 leading-tight mb-3">
                {{ $isRtl ? 'تغطية الأحياء والمدن' : 'Districts & Cities Coverage' }}
            </h1>
            <p class="text-gray-600 text-sm md:text-base max-w-2xl mx-auto leading-relaxed mb-6">
                @if($isRtl)
                    نخدمك في أكثر من <strong>{{ $totalDistrictsCount }} حياً نشطاً بالرياض والمبرز (الأحساء)</strong> مع استلام وتوصيل لباب بيتك، وقريباً في جدة والدمام.
                @else
                    Serving you in over <strong>{{ $totalDistrictsCount }} active districts across Riyadh & Al-Mubarraz (Al-Ahsa)</strong> with door-to-door delivery, and coming soon to Jeddah & Dammam.
                @endif
            </p>

            <!-- City Filters -->
            <div class="flex flex-wrap items-center justify-center gap-2 mb-6" id="cityTabs">
                <button class="city-tab active px-5 py-2.5 rounded-full font-bold text-xs md:text-sm border shadow-sm flex items-center gap-1.5" data-city="all">
                    <i class="fa-solid fa-map-location-dot {{ $isRtl ? 'ml-1' : 'mr-1' }}"></i> {{ $isRtl ? 'جميع الأحياء' : 'All Districts' }} (<span id="totalCount">{{ $totalDistrictsCount }}</span>)
                </button>

                @foreach($cities as $c)
                    <button class="city-tab px-5 py-2.5 rounded-full font-bold text-xs md:text-sm border shadow-sm flex items-center gap-1.5" data-city="{{ $c->id }}">
                        <i class="fa-solid {{ getCityIcon($c->name, $cityIcons) }} {{ $isRtl ? 'ml-1' : 'mr-1' }}"></i> {{ getCityDisplayName($c) }} ({{ $c->districts->count() }})
                    </button>
                @endforeach

                <button class="city-tab px-5 py-2.5 rounded-full font-bold text-xs md:text-sm border shadow-sm flex items-center gap-1.5" data-city="soon" data-city-name="{{ $isRtl ? 'جدة والدمام' : 'Jeddah & Dammam' }}">
                    <i class="fa-solid fa-sparkles {{ $isRtl ? 'ml-1' : 'mr-1' }}"></i> {{ $isRtl ? 'قريباً (جدة والدمام)' : 'Coming Soon (Jeddah & Dammam)' }}
                </button>
            </div>

            <!-- Instant Search -->
            <div class="relative max-w-xl mx-auto">
                <i class="fa-solid fa-magnifying-glass absolute {{ $isRtl ? 'right-4' : 'left-4' }} top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text" id="districtSearch" placeholder="{{ $isRtl ? 'ابحث عن اسم حيّك مباشرة... (مثال: الياسمين، الملقا، المبرز، العارض)' : 'Search your district directly... (e.g. Al-Yasmin, Al-Malqa, Al-Mubarraz)' }}" class="w-full {{ $isRtl ? 'pr-12 pl-4' : 'pl-12 pr-4' }} py-3.5 rounded-2xl border-2 border-gray-200 bg-white text-sm outline-none font-medium focus:border-sky-600 transition shadow-sm">
            </div>
        </div>
    </section>

    <!-- Highlights Bar -->
    <section class="max-w-6xl mx-auto px-4 mb-8">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 {{ $isRtl ? 'text-right' : 'text-left' }}">
            <div class="bg-white p-5 rounded-3xl border border-sky-100 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-sky-100 text-sky-600 flex items-center justify-center text-xl flex-shrink-0">
                    <i class="fa-solid fa-truck-ramp-box"></i>
                </div>
                <div>
                    <div class="font-bold text-slate-900 text-sm">{{ $isRtl ? 'استلام وتوصيل مجاني' : 'Free Pickup & Delivery' }}</div>
                    <div class="text-xs text-gray-500">{{ $isRtl ? 'للطلبات بـ 100 ر.س وفوق وعروض مستمرة' : 'For orders 100 SAR+ and ongoing deals' }}</div>
                </div>
            </div>
            <div class="bg-white p-5 rounded-3xl border border-sky-100 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-xl flex-shrink-0">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                </div>
                <div>
                    <div class="font-bold text-slate-900 text-sm">{{ $isRtl ? 'الملابس في 21 إلى 24 ساعة' : 'Clothes in 21 to 24 Hours' }}</div>
                    <div class="text-xs text-gray-500">{{ $isRtl ? 'استلام سريع وتسليم بالوقت المحدد' : 'Fast pickup & on-time delivery' }}</div>
                </div>
            </div>
            <div class="bg-white p-5 rounded-3xl border border-sky-100 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-indigo-100 text-indigo-600 flex items-center justify-center text-xl flex-shrink-0">
                    <i class="fa-solid fa-clock"></i>
                </div>
                <div>
                    <div class="font-bold text-slate-900 text-sm">{{ $isRtl ? 'سردات كل ساعتين' : 'Slots Every 2 Hours' }}</div>
                    <div class="text-xs text-gray-500">{{ $isRtl ? 'من 10 صباحاً إلى 11:59 ليلاً' : 'From 10:00 AM to 11:59 PM' }}</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Districts Grid & Content -->
    <section class="max-w-6xl mx-auto px-4 pb-12">
        
        <!-- Expansion Banner (Coming Soon) -->
        <div id="soonBanner" class="bg-gradient-to-r from-amber-500 to-orange-500 text-white rounded-3xl p-6 mb-8 shadow-sm hidden">
            <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-white/20 flex items-center justify-center text-2xl flex-shrink-0">
                        <i class="fa-solid fa-plane-departure"></i>
                    </div>
                    <div class="{{ $isRtl ? 'text-right' : 'text-left' }}">
                        <h3 class="font-black text-lg md:text-xl" id="soonBannerTitle">
                            {{ $isRtl ? 'قريباً في جدة والدمام!' : 'Coming soon to Jeddah & Dammam!' }}
                        </h3>
                        <p class="text-xs md:text-sm text-amber-100">
                            {{ $isRtl ? 'نعمل على توسيع أسطول كلين ستيشن لنصلكم في المنطقة الغربية والمنطقة الشرقية قريباً جداً.' : 'We are expanding Clean Station fleet to reach you in the Western and Eastern regions very soon.' }}
                        </p>
                    </div>
                </div>
                <a href="https://cleanstation.app.link/?channel=website" data-app-cta data-placement="coverage_soon" class="bg-white text-orange-600 px-6 py-2.5 rounded-full font-bold text-xs shadow hover:bg-amber-50 transition whitespace-nowrap">
                    {{ $isRtl ? 'حمّل التطبيق وكن أول المستفيدين' : 'Download App & Be the First' }}
                </a>
            </div>
        </div>

        <!-- List of Districts -->
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3" id="districtsGrid">
            @foreach($cities as $city)
                @foreach($city->districts as $district)
                    <div class="district-card bg-white p-3.5 rounded-2xl border border-gray-200 shadow-sm {{ $isRtl ? 'text-right' : 'text-left' }}" data-city="{{ $city->id }}" data-name="{{ $district->name }}">
                        <div class="flex items-center justify-between mb-1">
                            <span class="font-bold text-slate-900 text-xs">{{ $district->name }}</span>
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        </div>
                        <span class="text-[10px] text-gray-500">{{ $city->name }} • {{ $isRtl ? 'نشط' : 'Active' }}</span>
                    </div>
                @endforeach
            @endforeach
        </div>

        <!-- Didn't find your district CTA -->
        <div class="mt-8 bg-sky-50 border border-sky-200 rounded-3xl p-6 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-4 {{ $isRtl ? 'text-right' : 'text-left' }}">
                <div class="w-12 h-12 bg-sky-100 text-sky-600 rounded-2xl flex items-center justify-center flex-shrink-0 text-xl">
                    <i class="fa-solid fa-map-pin"></i>
                </div>
                <div>
                    <h4 class="font-bold text-slate-900 text-base mb-0.5">
                        {{ $isRtl ? 'مالقيت حيّك مسجل بالقائمة؟' : "Didn't find your district listed?" }}
                    </h4>
                    <p class="text-xs text-gray-600">
                        {{ $isRtl ? 'اختر "غير موجود بالقائمة" في التطبيق وبنتواصل معك ونوصل لك لباب بيتك!' : 'Select "Not in the list" in the app and we will contact you and deliver to your door!' }}
                    </p>
                </div>
            </div>
            <a href="https://cleanstation.app.link/?channel=website" data-app-cta data-placement="coverage_not_found" class="bg-sky-600 text-white px-6 py-2.5 rounded-full font-bold text-xs shadow hover:bg-sky-700 transition whitespace-nowrap">
                {{ $isRtl ? 'اطلب بالتطبيق الحين' : 'Order via App Now' }}
            </a>
        </div>

        <!-- Bottom CTA -->
        <div class="text-center py-10">
            <p class="text-gray-500 text-sm mb-4 font-medium">
                {{ $isRtl ? 'كل أحياء الرياض والمبرز مغطاة بالكامل' : 'All districts in Riyadh and Al-Mubarraz are fully covered' }}
            </p>
            <a href="https://cleanstation.app.link/?channel=website" data-app-cta data-placement="coverage_bottom_cta" class="inline-block bg-sky-600 text-white px-10 py-3.5 rounded-full font-bold text-base shadow-lg hover:bg-sky-700 transition hover:-translate-y-0.5">
                <i class="fa-solid fa-mobile-screen {{ $isRtl ? 'ml-2' : 'mr-2' }}"></i> {{ $isRtl ? 'اطلب الآن عبر التطبيق' : 'Order Now via App' }}
            </a>
        </div>
    </section>
</div>

@push('scripts')
<style>
    .district-card { transition: all 0.2s ease; }
    .district-card:hover { transform: translateY(-2px); border-color: #0284c7; box-shadow: 0 8px 20px -6px rgba(2,132,199,0.15); }
    .city-tab { transition: all 0.2s ease; background-color: #ffffff; color: #475569; border: 1px solid #e2e8f0; cursor: pointer; }
    .city-tab.active { background-color: #0284c7 !important; color: #ffffff !important; box-shadow: 0 4px 12px rgba(2,132,199,0.25); border-color: #0284c7 !important; }
    .city-tab:not(.active):hover { background-color: #f8fafc; color: #0284c7; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const isRtl = {{ $isRtl ? 'true' : 'false' }};
    const tabs = document.querySelectorAll('.city-tab');
    const soonBanner = document.getElementById('soonBanner');
    const soonBannerTitle = document.getElementById('soonBannerTitle');
    const grid = document.getElementById('districtsGrid');
    const searchInput = document.getElementById('districtSearch');
    const allTab = document.querySelector('.city-tab[data-city="all"]');

    tabs.forEach(function(tab) {
        tab.addEventListener('click', function() {
            tabs.forEach(function(t) { t.classList.remove('active'); });
            tab.classList.add('active');
            
            var c = tab.dataset.city;
            var cityName = tab.dataset.cityName || '';

            if (c === 'soon') {
                if (soonBannerTitle && cityName) {
                    soonBannerTitle.textContent = isRtl ? ('قريباً في ' + cityName + '!') : ('Coming soon to ' + cityName + '!');
                } else if (soonBannerTitle) {
                    soonBannerTitle.textContent = isRtl ? 'قريباً في جدة والدمام!' : 'Coming soon to Jeddah & Dammam!';
                }
                soonBanner.classList.remove('hidden');
                grid.classList.add('hidden');
            } else {
                soonBanner.classList.add('hidden');
                grid.classList.remove('hidden');
                
                var cards = document.querySelectorAll('.district-card');
                cards.forEach(function(card) {
                    card.style.display = (c === 'all' || card.dataset.city === c) ? '' : 'none';
                });
            }

            if (searchInput) {
                searchInput.value = '';
            }
        });
    });

    // District Live Search
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            var q = e.target.value.trim().toLowerCase();
            var cards = document.querySelectorAll('.district-card');
            
            // Activate "All Districts" tab whenever user searches
            if (allTab) {
                tabs.forEach(function(t) { t.classList.remove('active'); });
                allTab.classList.add('active');
            }

            soonBanner.classList.add('hidden');
            grid.classList.remove('hidden');

            cards.forEach(function(card) {
                var name = (card.dataset.name || '').toLowerCase();
                var text = card.textContent.toLowerCase();
                card.style.display = (q === '' || name.includes(q) || text.includes(q)) ? '' : 'none';
            });
        });
    }
});
</script>
@endpush
