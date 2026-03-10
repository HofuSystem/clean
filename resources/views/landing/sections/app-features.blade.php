@php
 $appFeatures = \Core\Pages\Models\Feature::where('section', 'b2c')->get();
 $defaultFeatureImage = $appFeatures->first()?->image_url ?? $section->image_url ?? '';
@endphp
<section id="app-features" class="bg-gray-900 text-white py-24 relative overflow-hidden">
    <div class="absolute inset-0 bg-brand-900/20"></div>
    <div class="max-w-7xl mx-auto px-4 relative z-10">
        <div class="grid lg:grid-cols-2 gap-16 items-center">
            <div class="order-2 lg:order-1 flex justify-center" data-aos="fade-right">
                <div class="relative w-[300px] bg-gray-800 rounded-[3rem] border-[8px] border-gray-700 shadow-2xl overflow-hidden transform hover:rotate-1 transition duration-500">
                    <img id="app-feature-screen" src="{{ $defaultFeatureImage }}" alt="" class="w-full h-full object-cover transition-opacity duration-300">
                </div>
            </div>
            <div class="order-1 lg:order-2 text-center lg:text-start" data-aos="fade-left">
                <h2 class="text-3xl md:text-5xl font-black mb-6"><span class="lang-ar">{{ $section->translate('ar')->title ?? 'تطبيق كلين ستيشن' }}</span><span class="lang-en">{{ $section->translate('en')->title ?? 'Clean Station App' }}</span></h2>
                <div class="space-y-6 mt-8" id="app-features-list">
                    @foreach($appFeatures as $index => $feature)
                    @php $featureImage = $feature->image_url ?? $section->image_url ?? ''; @endphp
                    <div class="app-feature-item flex items-center gap-4 bg-white/5 p-4 rounded-xl border border-white/10 hover:border-brand-500 transition-colors cursor-pointer {{ $index === 0 ? '!border-brand-500 bg-white/10' : '' }}"
                         role="button"
                         tabindex="0"
                         data-image="{{ $featureImage }}"
                         onclick="window.appFeatureSelect(this)"
                         onkeydown="if(event.key==='Enter'||event.key===' ') { event.preventDefault(); window.appFeatureSelect(this); }">
                        <i class="{{ $feature->icon }} text-2xl text-brand-400"></i>
                        <div class="text-start">
                            <h4 class="font-bold"><span class="lang-ar">{{ $feature->translate('ar')->title }}</span><span class="lang-en">{{ $feature->translate('en')->title }}</span></h4>
                            <p class="text-xs text-gray-400"><span class="lang-ar">{{ $feature->translate('ar')->description }}</span><span class="lang-en">{{ $feature->translate('en')->description }}</span></p>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="mt-6 flex flex-row xs:flex-row gap-3 justify-center lg:justify-start w-full">
                    @if(setting('app_store_app'))<a href="{{ setting('app_store_app') }}" target="_blank" rel="noopener" onclick="typeof gtag === 'function' && gtag('event', 'click_download', { app_store: 'apple', campaign_source: 'website_app_features' });" class="hover:scale-105 transition-transform"><img src="https://tools.applemediaservices.com/api/badges/download-on-the-app-store/black/en-us?size=250x83" class="h-12"></a>@endif
                    @if(setting('g_play_app'))<a href="{{ setting('g_play_app') }}" target="_blank" rel="noopener" onclick="typeof gtag === 'function' && gtag('event', 'click_download', { app_store: 'google', campaign_source: 'website_app_features' });" class="hover:scale-105 transition-transform"><img src="https://upload.wikimedia.org/wikipedia/commons/7/78/Google_Play_Store_badge_EN.svg" class="h-12"></a>@endif
                </div>
            </div>
        </div>
    </div>
    <script>
        window.appFeatureSelect = function(el) {
            var img = el.getAttribute('data-image');
            if (!img) return;
            var screenEl = document.getElementById('app-feature-screen');
            if (screenEl) {
                screenEl.style.opacity = '0';
                setTimeout(function() {
                    screenEl.src = img;
                    screenEl.style.opacity = '1';
                }, 150);
            }
            document.querySelectorAll('#app-features-list .app-feature-item').forEach(function(item) {
                item.classList.remove('!border-brand-500', 'bg-white/10');
            });
            el.classList.add('!border-brand-500', 'bg-white/10');
        };
    </script>
</section>
