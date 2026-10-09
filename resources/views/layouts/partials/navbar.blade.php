<style>
    .nav-new-badge {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        font-size: 10px !important;
        font-weight: 700 !important;
        line-height: 1.5 !important;
        background-color: #E5484D !important;
        color: #ffffff !important;
        border-radius: 999px !important;
        padding: 1px 7px !important;
        margin-inline-start: 5px !important;
        vertical-align: middle !important;
        white-space: nowrap !important;
        letter-spacing: 0 !important;
        box-shadow: 0 1px 3px rgba(229, 72, 77, 0.3) !important;
    }
    #navbar .h-10, #navbar img.h-10, #navbar a[href*="home"] img {
        height: 58px !important;
        max-height: 58px !important;
        width: auto !important;
        object-fit: contain !important;
        transition: all 0.2s ease;
    }
    #navbar {
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
    }
    @media (max-width: 640px) {
        #navbar .h-10, #navbar img.h-10, #navbar a[href*="home"] img {
            height: 46px !important;
            max-height: 46px !important;
        }
    }
</style>

<nav class="fixed top-0 left-0 right-0 w-full z-50 transition-all duration-300 bg-white/95 backdrop-blur-md border-b border-gray-100" id="navbar">
    <div class="w-full max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-20" style="direction: ltr !important;">
            
            <a href="{{ route('home') }}" class="flex-shrink-0 flex items-center gap-2">
                @if(config('app.logo'))
                    <x-website-image :src="config('app.logo')" sizes="104px" width="1503" height="826" alt="Logo" class="h-10 w-auto" />
                @else
                    <div class="w-10 h-10 bg-brand-600 text-white rounded-xl flex items-center justify-center text-xl shadow-lg"><i class="fa-solid fa-soap"></i></div>
                    <span class="font-black text-xl tracking-tighter text-gray-900 hidden sm:block">{{ app()->getLocale() === 'ar' ? 'كلين ستيشن' : 'Clean Station' }}</span>
                @endif
            </a>

            <div id="nav-pill-menu" class="hidden xl:flex items-center gap-1 2xl:gap-1.5 bg-gray-50/90 px-2 py-1 2xl:px-2.5 2xl:py-1.5 rounded-full border border-gray-200/60 shadow-sm shrink-0" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}" style="direction: {{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }} !important;">
                <a href="{{ route('home') }}" class="px-2.5 2xl:px-3 py-1.5 rounded-full text-xs font-bold {{ Route::is('home') ? 'bg-white text-brand-600 shadow-sm' : 'text-gray-600 hover:text-brand-600' }} transition-all whitespace-nowrap">{{ trans('home') }}</a>
                <a href="{{ route('services') }}" class="px-2.5 2xl:px-3 py-1.5 rounded-full text-xs font-bold {{ Route::is('services*') ? 'bg-white text-brand-600 shadow-sm' : 'text-gray-600 hover:text-brand-600' }} transition-all whitespace-nowrap">{{ trans('services') }}</a>
                <a href="{{ route('pricing') }}" class="px-2.5 2xl:px-3 py-1.5 rounded-full text-xs font-bold {{ Route::is('pricing') ? 'bg-white text-brand-600 shadow-sm' : 'text-gray-600 hover:text-brand-600' }} transition-all whitespace-nowrap">{{ app()->getLocale() === 'ar' ? 'الأسعار' : 'Pricing' }}</a>
                <a href="{{ route('coverage') }}" class="px-2.5 2xl:px-3 py-1.5 rounded-full text-xs font-bold {{ Route::is('coverage') ? 'bg-white text-brand-600 shadow-sm' : 'text-gray-600 hover:text-brand-600' }} transition-all whitespace-nowrap">{{ app()->getLocale() === 'ar' ? 'التغطية' : 'Coverage' }}</a>
                <a href="{{ route('why-us') }}" class="px-2.5 2xl:px-3 py-1.5 rounded-full text-xs font-bold {{ Route::is('why-us') ? 'bg-white text-brand-600 shadow-sm' : 'text-gray-600 hover:text-brand-600' }} transition-all whitespace-nowrap">{{ trans('why_us') }}</a>
                <a href="{{ route('b2b') }}" class="px-2.5 2xl:px-3 py-1.5 rounded-full text-xs font-bold {{ Route::is('b2b') ? 'bg-white text-brand-600 shadow-sm' : 'text-gray-600 hover:text-brand-600' }} transition-all whitespace-nowrap">{{ trans('business') }}</a>
                <a href="{{ route('gifts') }}" class="px-2.5 2xl:px-3 py-1.5 rounded-full text-xs font-bold inline-flex items-center gap-1 {{ Route::is('gifts*') ? 'bg-white text-brand-600 shadow-sm' : 'text-gray-600 hover:text-brand-600' }} transition-all whitespace-nowrap">
                    <span>{{ app()->getLocale() === 'ar' ? 'الورود والهدايا' : 'Flowers & Gifts' }}</span>
                    <span class="nav-new-badge" style="background-color: #E5484D !important; color: #ffffff !important; font-size: 10px !important; font-weight: 700 !important; border-radius: 999px !important; padding: 1px 7px !important; margin-inline-start: 5px !important; display: inline-flex !important; align-items: center !important; line-height: 1.5 !important; white-space: nowrap !important;">{{ app()->getLocale() === 'ar' ? 'جديد' : 'New' }}</span>
                </a>
                <a href="{{ route('blog') }}" class="px-2.5 2xl:px-3 py-1.5 rounded-full text-xs font-bold {{ Route::is('blog*') || Route::is('blogs*') ? 'bg-white text-brand-600 shadow-sm' : 'text-gray-600 hover:text-brand-600' }} transition-all whitespace-nowrap">{{ trans('blog') }}</a>
                <a href="{{ route('faq') }}" class="px-2.5 2xl:px-3 py-1.5 rounded-full text-xs font-bold {{ Route::is('faq') ? 'bg-white text-brand-600 shadow-sm' : 'text-gray-600 hover:text-brand-600' }} transition-all whitespace-nowrap">{{ trans('faq') }}</a>
            </div>

            <div class="flex items-center gap-1.5 sm:gap-2 xl:gap-2.5 shrink-0">
                {{-- Direct Language Switcher to the other language --}}
                @php
                    $otherLocale = LaravelLocalization::getCurrentLocale() === 'ar' ? 'en' : 'ar';
                    $waText = app()->getLocale() === 'ar' ? 'أبغى أطلب استلام غسيل، الحي:' : 'I want to request laundry pickup, District:';
                @endphp
                <a rel="alternate" hreflang="{{ $otherLocale }}" href="{{ LaravelLocalization::getLocalizedURL($otherLocale, null, [], true) }}" class="w-9 h-9 rounded-full bg-gray-100 hover:bg-brand-50 hover:text-brand-600 border border-gray-200/60 flex items-center justify-center transition-all text-xs font-black uppercase text-gray-700 shadow-sm shrink-0" title="{{ $otherLocale === 'ar' ? 'العربية' : 'English' }}">
                    {{ $otherLocale }}
                </a>

                {{-- WhatsApp Order Button (Task 6) --}}
                <a href="https://wa.me/966559098685?text={{ urlencode($waText) }}" 
                   target="_blank" 
                   rel="noopener" 
                   id="navbar-whatsapp-order-btn"
                   onclick="window.cleanTrack && window.cleanTrack.contact('whatsapp_header')"
                   class="hidden sm:inline-flex items-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 text-white px-3 2xl:px-3.5 py-2 rounded-xl font-bold text-xs transition-all shadow-md shadow-emerald-200 whitespace-nowrap shrink-0">
                    <svg class="w-4 h-4 shrink-0 fill-current" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.711 2.598 2.669-.699c.969.586 1.861.882 2.79.882 3.182 0 5.768-2.587 5.768-5.766.001-3.181-2.585-5.768-5.767-5.768zm0-2c4.284 0 7.768 3.484 7.768 7.768 0 4.285-3.484 7.768-7.768 7.768-1.229 0-2.427-.29-3.513-.843l-4.518 1.185 1.206-4.41c-.636-1.127-.975-2.404-.975-3.7 0-4.284 3.484-7.768 7.768-7.768zm3.435 11.053c-.15.422-.767.771-1.077.818-.31.047-.704.062-2.313-.578-1.928-.767-3.174-2.736-3.27-2.864-.096-.129-.778-1.036-.778-1.975 0-.94.492-1.402.668-1.593.176-.191.385-.239.513-.239.129 0 .257.001.369.006.118.006.276-.045.432.329.16.385.546 1.332.594 1.428.048.096.08.209.016.337-.064.129-.096.209-.193.321-.096.113-.203.252-.289.339-.096.096-.197.201-.085.393.112.193.498.823 1.07 1.332.736.657 1.356.86 1.549.957.193.096.305.08.417-.048.113-.129.482-.562.61-.755.129-.193.257-.161.433-.096.177.064 1.124.53 1.317.626.193.096.321.144.369.225.048.08.048.466-.102.888z"/></svg>
                    <span>{{ app()->getLocale() === 'ar' ? 'اطلب عبر واتساب' : 'Order via WhatsApp' }}</span>
                </a>

                {{-- Download App Branch Button (Task 7 & 11) --}}
                <a href="https://cleanstation.app.link/?channel=navbar" 
                   target="_blank" 
                   rel="noopener" 
                   id="navbar-download-btn"
                   class="hidden lg:inline-flex items-center gap-1.5 bg-brand-600 hover:bg-brand-700 text-white px-3 2xl:px-3.5 py-2 rounded-xl font-bold text-xs transition-all shadow-md shadow-brand-200 whitespace-nowrap shrink-0">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    <span>{{ app()->getLocale() === 'ar' ? 'تحميل التطبيق' : 'Download App' }}</span>
                </a>

                @if(Route::is('b2b'))
                    <a href="{{ route('client.login') }}" class="hidden sm:inline-flex items-center justify-center bg-gray-900 text-white px-3 2xl:px-3.5 py-2 rounded-xl font-bold text-xs hover:bg-gray-800 transition-all shadow-sm whitespace-nowrap shrink-0">
                        {{ trans('login') }}
                    </a>
                @endif
                <a href="{{ route('contact') }}" class="hidden sm:inline-flex items-center justify-center border border-brand-600 text-brand-600 hover:bg-brand-50 px-3 2xl:px-3.5 py-2 rounded-xl font-bold text-xs transition-all whitespace-nowrap shrink-0">
                    {{ trans('contact') }}
                </a>
                
                <button onclick="toggleMobileMenu()" class="xl:hidden w-10 h-10 flex items-center justify-center text-gray-600 hover:text-brand-600 transition-colors focus:outline-none" aria-label="Toggle Menu">
                    <i class="fa-solid fa-bars text-2xl"></i>
                </button>
            </div>
        </div>
    </div>

    <div id="mobile-menu" class="hidden xl:hidden bg-white border-t border-slate-100 fixed inset-x-0 top-20 shadow-2xl origin-top animate-fade-in-down max-h-[calc(100vh-5rem)] overflow-y-auto pb-24 z-[99999]">
        <div class="px-3.5 pt-3.5 pb-6">
            {{-- Top CTA buttons in sleek 2-column grid --}}
            <div class="grid grid-cols-2 gap-2 mb-2.5">
                <a href="https://wa.me/966559098685?text={{ urlencode($waText) }}" target="_blank" rel="noopener" class="flex items-center justify-center gap-1.5 px-2.5 py-2.5 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-sm transition-all text-center">
                    <svg class="w-4 h-4 shrink-0 fill-current" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.711 2.598 2.669-.699c.969.586 1.861.882 2.79.882 3.182 0 5.768-2.587 5.768-5.766.001-3.181-2.585-5.768-5.767-5.768zm0-2c4.284 0 7.768 3.484 7.768 7.768 0 4.285-3.484 7.768-7.768 7.768-1.229 0-2.427-.29-3.513-.843l-4.518 1.185 1.206-4.41c-.636-1.127-.975-2.404-.975-3.7 0-4.284 3.484-7.768 7.768-7.768zm3.435 11.053c-.15.422-.767.771-1.077.818-.31.047-.704.062-2.313-.578-1.928-.767-3.174-2.736-3.27-2.864-.096-.129-.778-1.036-.778-1.975 0-.94.492-1.402.668-1.593.176-.191.385-.239.513-.239.129 0 .257.001.369.006.118.006.276-.045.432.329.16.385.546 1.332.594 1.428.048.096.08.209.016.337-.064.129-.096.209-.193.321-.096.113-.203.252-.289.339-.096.096-.197.201-.085.393.112.193.498.823 1.07 1.332.736.657 1.356.86 1.549.957.193.096.305.08.417-.048.113-.129.482-.562.61-.755.129-.193.257-.161.433-.096.177.064 1.124.53 1.317.626.193.096.321.144.369.225.048.08.048.466-.102.888z"/></svg>
                    <span class="truncate">{{ app()->getLocale() === 'ar' ? 'اطلب عبر واتساب' : 'Order via WhatsApp' }}</span>
                </a>
                <a href="https://cleanstation.app.link/?channel=mobile_menu" target="_blank" rel="noopener" class="flex items-center justify-center gap-1.5 px-2.5 py-2.5 rounded-xl text-xs font-bold text-white bg-brand-600 hover:bg-brand-700 shadow-sm transition-all text-center">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    <span class="truncate">{{ app()->getLocale() === 'ar' ? 'تحميل التطبيق' : 'Download App' }}</span>
                </a>
            </div>

            {{-- Compact Nav Links --}}
            <div class="space-y-0.5">
                <a href="{{ route('home') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-semibold text-slate-700 hover:text-brand-600 hover:bg-slate-50 transition-colors">
                    <i class="fa-solid fa-house w-5 text-center text-xs text-brand-500"></i>
                    <span>{{ trans('home') }}</span>
                </a>
                <a href="{{ route('services') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-semibold text-slate-700 hover:text-brand-600 hover:bg-slate-50 transition-colors">
                    <i class="fa-solid fa-layer-group w-5 text-center text-brand-500"></i>
                    <span>{{ trans('services') }}</span>
                </a>
                <a href="{{ route('pricing') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-semibold text-slate-700 hover:text-brand-600 hover:bg-slate-50 transition-colors">
                    <i class="fa-solid fa-tags w-5 text-center text-brand-500"></i>
                    <span>{{ app()->getLocale() === 'ar' ? 'الأسعار' : 'Pricing' }}</span>
                </a>
                <a href="{{ route('coverage') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-semibold text-slate-700 hover:text-brand-600 hover:bg-slate-50 transition-colors">
                    <i class="fa-solid fa-map-location-dot w-5 text-center text-brand-500"></i>
                    <span>{{ app()->getLocale() === 'ar' ? 'التغطية' : 'Coverage' }}</span>
                </a>
                <a href="{{ route('why-us') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-semibold text-slate-700 hover:text-brand-600 hover:bg-slate-50 transition-colors">
                    <i class="fa-solid fa-crown w-5 text-center text-brand-500"></i>
                    <span>{{ trans('why_us') }}</span>
                </a>
                <a href="{{ route('b2b') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-semibold text-slate-700 hover:text-brand-600 hover:bg-slate-50 transition-colors">
                    <i class="fa-solid fa-briefcase w-5 text-center text-brand-500"></i>
                    <span>{{ trans('business') }}</span>
                </a>
                <a href="{{ route('gifts') }}" class="flex items-center justify-between px-3 py-2 rounded-lg text-sm font-semibold {{ Route::is('gifts*') ? 'text-brand-600 bg-brand-50' : 'text-slate-700 hover:text-brand-600 hover:bg-slate-50' }} transition-colors">
                    <span class="flex items-center gap-2.5">
                        <i class="fa-solid fa-gift w-5 text-center text-rose-500"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'الورود والهدايا' : 'Flowers & Gifts' }}</span>
                    </span>
                    <span class="nav-new-badge" style="background-color: #E5484D !important; color: #ffffff !important; font-size: 10px !important; font-weight: 700 !important; border-radius: 999px !important; padding: 1px 7px !important; display: inline-flex !important; align-items: center !important; line-height: 1.4 !important;">{{ app()->getLocale() === 'ar' ? 'جديد' : 'New' }}</span>
                </a>
                <a href="{{ route('blog') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-semibold text-slate-700 hover:text-brand-600 hover:bg-slate-50 transition-colors">
                    <i class="fa-solid fa-newspaper w-5 text-center text-brand-500"></i>
                    <span>{{ trans('blog') }}</span>
                </a>
                <a href="{{ route('faq') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-semibold text-slate-700 hover:text-brand-600 hover:bg-slate-50 transition-colors">
                    <i class="fa-solid fa-circle-question w-5 text-center text-brand-500"></i>
                    <span>{{ trans('faq') }}</span>
                </a>
                @if(Route::is('b2b'))
                <a href="{{ route('client.login') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-semibold text-slate-700 hover:text-brand-600 hover:bg-slate-50 transition-colors">
                    <i class="fa-solid fa-right-to-bracket w-5 text-center text-brand-500"></i>
                    <span>{{ trans('login') }}</span>
                </a>
                @endif
                <a href="{{ route('contact') }}" class="flex sm:hidden items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-semibold text-brand-600 bg-brand-50 transition-colors">
                    <i class="fa-solid fa-envelope w-5 text-center text-brand-500"></i>
                    <span>{{ trans('contact') }}</span>
                </a>
                <a rel="alternate" hreflang="{{ $otherLocale }}" href="{{ LaravelLocalization::getLocalizedURL($otherLocale, null, [], true) }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-sm font-semibold text-brand-600 hover:bg-brand-50 border-t border-slate-100 mt-2 pt-2.5 transition-colors">
                    <i class="fa-solid fa-globe w-5 text-center text-xs text-brand-500"></i>
                    <span>{{ $otherLocale === 'ar' ? 'العربية' : 'English' }} ({{ strtoupper($otherLocale) }})</span>
                </a>
            </div>
        </div>
    </div>
</nav>

<script>
    // Mobile Menu Toggle
    function toggleMobileMenu() {
        const menu = document.getElementById('mobile-menu');
        const ctas = document.querySelectorAll('.sticky-mobile-cta, #smart-mobile-cta, .mobile-sticky-bar');
        if(menu) {
            const isClosed = menu.classList.contains('hidden');
            if (isClosed) {
                menu.classList.remove('hidden');
                ctas.forEach(el => el.style.setProperty('display', 'none', 'important'));
            } else {
                menu.classList.add('hidden');
                ctas.forEach(el => el.style.removeProperty('display'));
            }
        }
    }
</script>
