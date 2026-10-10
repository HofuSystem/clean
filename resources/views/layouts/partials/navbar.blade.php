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
    #navbar {
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        width: 100% !important;
        z-index: 50 !important;
        background-color: rgba(255, 255, 255, 0.98) !important;
        backdrop-filter: blur(12px) !important;
        -webkit-backdrop-filter: blur(12px) !important;
        border-bottom: 1px solid #E2E8F0 !important;
        transition: all 0.25s ease !important;
    }
    #navbar .h-10, #navbar img.h-10, #navbar a[href*="home"] img {
        height: 52px !important;
        max-height: 52px !important;
        width: auto !important;
        object-fit: contain !important;
        transition: all 0.2s ease;
    }
    .mobile-action-btn {
        width: 44px;
        height: 44px;
        min-width: 44px;
        min-height: 44px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #F8FAFC;
        border: 1px solid #E2E8F0;
        color: #1E293B;
        transition: all 0.2s ease;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
    }
    .mobile-action-btn:hover {
        background: #F1F5F9;
        color: #0284c7;
        border-color: #CBD5E1;
    }
    .mobile-action-btn:active {
        transform: scale(0.95);
    }

    /* Mobile Menu Toggle Button: STRICTLY hidden on desktop (>=1280px), visible only on mobile/tablet (<1280px) */
    #mobile-menu-toggle-btn {
        display: none !important;
    }
    @media (max-width: 1279px) {
        #mobile-menu-toggle-btn {
            display: inline-flex !important;
        }
    }

    /* Mobile Navbar (screens < 768px) */
    @media (max-width: 768px) {
        #navbar {
            height: 60px !important;
        }
        #navbar .h-20 {
            height: 60px !important;
            padding-left: 14px !important;
            padding-right: 14px !important;
        }
        #navbar .h-10, #navbar img.h-10, #navbar a[href*="home"] img {
            height: 38px !important;
            max-height: 38px !important;
            width: auto !important;
        }
        .mobile-action-btn {
            width: 44px !important;
            height: 44px !important;
            min-width: 44px !important;
            min-height: 44px !important;
            border-radius: 10px !important;
            font-size: 12.5px !important;
        }
    }

    /* Full-width Mobile Menu Drawer (never cut off) */
    #mobile-menu {
        position: fixed !important;
        top: 60px !important;
        left: 0 !important;
        right: 0 !important;
        width: 100vw !important;
        max-width: 100% !important;
        box-sizing: border-box !important;
        margin: 0 !important;
        background: #ffffff !important;
        border-top: 1px solid #E2E8F0 !important;
        border-bottom: 1px solid #E2E8F0 !important;
        box-shadow: 0 20px 40px -10px rgba(15, 23, 42, 0.2) !important;
        z-index: 99999 !important;
        max-height: calc(100vh - 60px) !important;
        overflow-y: auto !important;
        -webkit-overflow-scrolling: touch !important;
        direction: {{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }} !important;
        text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }} !important;
    }

    @media (min-width: 769px) and (max-width: 1279px) {
        #mobile-menu {
            top: 80px !important;
            max-height: calc(100vh - 80px) !important;
        }
    }

    #mobile-menu-backdrop {
        position: fixed !important;
        top: 60px !important;
        left: 0 !important;
        right: 0 !important;
        bottom: 0 !important;
        width: 100vw !important;
        height: calc(100vh - 60px) !important;
        background: rgba(15, 23, 42, 0.45) !important;
        backdrop-filter: blur(4px) !important;
        -webkit-backdrop-filter: blur(4px) !important;
        z-index: 99998 !important;
        transition: opacity 0.25s ease !important;
    }
    @media (min-width: 769px) {
        #mobile-menu-backdrop {
            top: 80px !important;
            height: calc(100vh - 80px) !important;
        }
    }
</style>

<nav class="fixed top-0 left-0 right-0 w-full z-50 transition-all duration-300 bg-white/95 backdrop-blur-md border-b border-gray-100" id="navbar" aria-label="{{ app()->getLocale() === 'ar' ? 'التنقل الرئيسي' : 'Main Navigation' }}">
    <div class="w-full max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-20" style="direction: ltr !important;">
            
            <a href="{{ route('home') }}" class="flex-shrink-0 flex items-center gap-2" aria-label="{{ app()->getLocale() === 'ar' ? 'الصفحة الرئيسية لكلين ستيشن' : 'Clean Station Homepage' }}">
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

            <div class="flex items-center gap-2 sm:gap-2 xl:gap-2.5 shrink-0">
                {{-- Direct Language Switcher to the other language --}}
                @php
                    $otherLocale = LaravelLocalization::getCurrentLocale() === 'ar' ? 'en' : 'ar';
                    $waText = app()->getLocale() === 'ar' ? 'أبغى أطلب استلام غسيل، الحي:' : 'I want to request laundry pickup, District:';
                @endphp
                <a rel="alternate" hreflang="{{ $otherLocale }}" href="{{ LaravelLocalization::getLocalizedURL($otherLocale, null, [], true) }}" class="mobile-action-btn sm:w-9 sm:h-9 sm:rounded-full bg-gray-100 hover:bg-brand-50 hover:text-brand-600 border border-gray-200/60 flex items-center justify-center transition-all text-xs font-black uppercase text-gray-700 shadow-sm shrink-0" title="{{ $otherLocale === 'ar' ? 'العربية' : 'English' }}" aria-label="{{ $otherLocale === 'ar' ? 'التبديل إلى العربية' : 'Switch to English' }}">
                    {{ $otherLocale }}
                </a>

                {{-- WhatsApp Order Button (Task 6) --}}
                <a href="https://wa.me/966559098685?text={{ urlencode($waText) }}" 
                   target="_blank" 
                   rel="noopener" 
                   id="navbar-whatsapp-order-btn"
                   aria-label="{{ app()->getLocale() === 'ar' ? 'اطلب عبر واتساب' : 'Order via WhatsApp' }}"
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
                   aria-label="{{ app()->getLocale() === 'ar' ? 'تحميل تطبيق كلين ستيشن' : 'Download Clean Station App' }}"
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
                
                {{-- Hamburger / Close Toggle Button --}}
                <button onclick="toggleMobileMenu()" id="mobile-menu-toggle-btn" class="xl:hidden mobile-action-btn flex items-center justify-center transition-colors focus:outline-none" aria-label="{{ app()->getLocale() === 'ar' ? 'فتح القائمة الرئيسية' : 'Toggle navigation menu' }}" aria-expanded="false" aria-controls="mobile-menu">
                    <svg id="mobile-menu-burger-icon" class="w-5 h-5 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="4" y1="6" x2="20" y2="6"></line>
                        <line x1="4" y1="12" x2="20" y2="12"></line>
                        <line x1="4" y1="18" x2="20" y2="18"></line>
                    </svg>
                    <svg id="mobile-menu-close-icon" class="w-5 h-5 text-slate-700 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Backdrop Overlay --}}
    <div id="mobile-menu-backdrop" class="hidden" onclick="toggleMobileMenu()"></div>

    {{-- Full Width Mobile Menu Drawer --}}
    <div id="mobile-menu" class="hidden xl:hidden" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
        <div class="px-4 pt-4 pb-8 max-w-lg mx-auto">
            {{-- Top CTA buttons in sleek 2-column grid --}}
            <div class="grid grid-cols-2 gap-2.5 mb-3.5">
                <a href="https://wa.me/966559098685?text={{ urlencode($waText) }}" target="_blank" rel="noopener" class="flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-sm transition-all text-center">
                    <svg class="w-4 h-4 shrink-0 fill-current" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.711 2.598 2.669-.699c.969.586 1.861.882 2.79.882 3.182 0 5.768-2.587 5.768-5.766.001-3.181-2.585-5.768-5.767-5.768zm0-2c4.284 0 7.768 3.484 7.768 7.768 0 4.285-3.484 7.768-7.768 7.768-1.229 0-2.427-.29-3.513-.843l-4.518 1.185 1.206-4.41c-.636-1.127-.975-2.404-.975-3.7 0-4.284 3.484-7.768 7.768-7.768zm3.435 11.053c-.15.422-.767.771-1.077.818-.31.047-.704.062-2.313-.578-1.928-.767-3.174-2.736-3.27-2.864-.096-.129-.778-1.036-.778-1.975 0-.94.492-1.402.668-1.593.176-.191.385-.239.513-.239.129 0 .257.001.369.006.118.006.276-.045.432.329.16.385.546 1.332.594 1.428.048.096.08.209.016.337-.064.129-.096.209-.193.321-.096.113-.203.252-.289.339-.096.096-.197.201-.085.393.112.193.498.823 1.07 1.332.736.657 1.356.86 1.549.957.193.096.305.08.417-.048.113-.129.482-.562.61-.755.129-.193.257-.161.433-.096.177.064 1.124.53 1.317.626.193.096.321.144.369.225.048.08.048.466-.102.888z"/></svg>
                    <span>{{ app()->getLocale() === 'ar' ? 'اطلب عبر واتساب' : 'Order via WhatsApp' }}</span>
                </a>
                <a href="https://cleanstation.app.link/?channel=mobile_menu" target="_blank" rel="noopener" class="flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl text-xs font-bold text-white bg-brand-600 hover:bg-brand-700 shadow-sm transition-all text-center">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    <span>{{ app()->getLocale() === 'ar' ? 'تحميل التطبيق' : 'Download App' }}</span>
                </a>
            </div>

            {{-- Compact Nav Links with Crisp Inline SVGs --}}
            <div class="divide-y divide-slate-100">
                <a href="{{ route('home') }}" class="flex items-center gap-3 py-2.5 px-2 rounded-lg text-sm font-semibold {{ Route::is('home') ? 'text-brand-600 bg-brand-50/60' : 'text-slate-700 hover:text-brand-600 hover:bg-slate-50' }} transition-colors">
                    <svg class="w-4 h-4 text-brand-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l9-9 9 9M5 10v10a1 1 0 001 1h3a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1h3a1 1 0 001-1V10"/></svg>
                    <span>{{ trans('home') }}</span>
                </a>
                <a href="{{ route('services') }}" class="flex items-center gap-3 py-2.5 px-2 rounded-lg text-sm font-semibold {{ Route::is('services*') ? 'text-brand-600 bg-brand-50/60' : 'text-slate-700 hover:text-brand-600 hover:bg-slate-50' }} transition-colors">
                    <svg class="w-4 h-4 text-brand-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    <span>{{ trans('services') }}</span>
                </a>
                <a href="{{ route('pricing') }}" class="flex items-center gap-3 py-2.5 px-2 rounded-lg text-sm font-semibold {{ Route::is('pricing') ? 'text-brand-600 bg-brand-50/60' : 'text-slate-700 hover:text-brand-600 hover:bg-slate-50' }} transition-colors">
                    <svg class="w-4 h-4 text-brand-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                    <span>{{ app()->getLocale() === 'ar' ? 'الأسعار' : 'Pricing' }}</span>
                </a>
                <a href="{{ route('coverage') }}" class="flex items-center gap-3 py-2.5 px-2 rounded-lg text-sm font-semibold {{ Route::is('coverage') ? 'text-brand-600 bg-brand-50/60' : 'text-slate-700 hover:text-brand-600 hover:bg-slate-50' }} transition-colors">
                    <svg class="w-4 h-4 text-brand-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span>{{ app()->getLocale() === 'ar' ? 'التغطية' : 'Coverage' }}</span>
                </a>
                <a href="{{ route('why-us') }}" class="flex items-center gap-3 py-2.5 px-2 rounded-lg text-sm font-semibold {{ Route::is('why-us') ? 'text-brand-600 bg-brand-50/60' : 'text-slate-700 hover:text-brand-600 hover:bg-slate-50' }} transition-colors">
                    <svg class="w-4 h-4 text-brand-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ trans('why_us') }}</span>
                </a>
                <a href="{{ route('b2b') }}" class="flex items-center gap-3 py-2.5 px-2 rounded-lg text-sm font-semibold {{ Route::is('b2b') ? 'text-brand-600 bg-brand-50/60' : 'text-slate-700 hover:text-brand-600 hover:bg-slate-50' }} transition-colors">
                    <svg class="w-4 h-4 text-brand-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    <span>{{ trans('business') }}</span>
                </a>
                <a href="{{ route('gifts') }}" class="flex items-center justify-between py-2.5 px-2 rounded-lg text-sm font-semibold {{ Route::is('gifts*') ? 'text-brand-600 bg-brand-50/70' : 'text-slate-700 hover:text-brand-600 hover:bg-slate-50' }} transition-colors">
                    <span class="flex items-center gap-3">
                        <svg class="w-4 h-4 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V6a2 2 0 10-2 2h2zm0 0H7a2 2 0 00-2 2v3a2 2 0 002 2h10a2 2 0 002-2v-3a2 2 0 00-2-2h-5z"/></svg>
                        <span>{{ app()->getLocale() === 'ar' ? 'الورود والهدايا' : 'Flowers & Gifts' }}</span>
                    </span>
                    <span class="nav-new-badge">{{ app()->getLocale() === 'ar' ? 'جديد' : 'New' }}</span>
                </a>
                <a href="{{ route('blog') }}" class="flex items-center gap-3 py-2.5 px-2 rounded-lg text-sm font-semibold {{ Route::is('blog*') || Route::is('blogs*') ? 'text-brand-600 bg-brand-50/60' : 'text-slate-700 hover:text-brand-600 hover:bg-slate-50' }} transition-colors">
                    <svg class="w-4 h-4 text-brand-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                    <span>{{ trans('blog') }}</span>
                </a>
                <a href="{{ route('faq') }}" class="flex items-center gap-3 py-2.5 px-2 rounded-lg text-sm font-semibold {{ Route::is('faq') ? 'text-brand-600 bg-brand-50/60' : 'text-slate-700 hover:text-brand-600 hover:bg-slate-50' }} transition-colors">
                    <svg class="w-4 h-4 text-brand-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ trans('faq') }}</span>
                </a>
                @if(Route::is('b2b'))
                <a href="{{ route('client.login') }}" class="flex items-center gap-3 py-2.5 px-2 rounded-lg text-sm font-semibold text-slate-700 hover:text-brand-600 hover:bg-slate-50 transition-colors">
                    <svg class="w-4 h-4 text-brand-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                    <span>{{ trans('login') }}</span>
                </a>
                @endif
                <a href="{{ route('contact') }}" class="flex sm:hidden items-center gap-3 py-2.5 px-2 rounded-lg text-sm font-semibold text-brand-600 hover:bg-brand-50 transition-colors">
                    <svg class="w-4 h-4 text-brand-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    <span>{{ trans('contact') }}</span>
                </a>
            </div>

            {{-- Bottom Language Switcher --}}
            <div class="mt-4 pt-3 border-t border-slate-200/80">
                <a rel="alternate" hreflang="{{ $otherLocale }}" href="{{ LaravelLocalization::getLocalizedURL($otherLocale, null, [], true) }}" class="flex items-center justify-between px-3 py-2.5 rounded-xl bg-slate-50 hover:bg-brand-50 text-slate-700 hover:text-brand-600 transition-colors text-sm font-bold">
                    <span class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-brand-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                        <span>{{ $otherLocale === 'ar' ? 'العربية' : 'English' }}</span>
                    </span>
                    <span class="text-xs uppercase bg-white border border-slate-200 px-2 py-0.5 rounded-md text-slate-600 font-extrabold">{{ $otherLocale }}</span>
                </a>
            </div>
        </div>
    </div>
</nav>

<script>
    // Mobile Menu Toggle
    function toggleMobileMenu() {
        const menu = document.getElementById('mobile-menu');
        const backdrop = document.getElementById('mobile-menu-backdrop');
        const burgerIcon = document.getElementById('mobile-menu-burger-icon');
        const closeIcon = document.getElementById('mobile-menu-close-icon');
        const toggleBtn = document.getElementById('mobile-menu-toggle-btn');
        const isRtl = document.documentElement.dir === 'rtl' || document.documentElement.lang === 'ar';
        const ctas = document.querySelectorAll('.sticky-mobile-cta, #smart-mobile-cta, .mobile-sticky-bar');
        
        if (menu) {
            const isClosed = menu.classList.contains('hidden');
            if (isClosed) {
                menu.classList.remove('hidden');
                if (backdrop) backdrop.classList.remove('hidden');
                if (burgerIcon) burgerIcon.classList.add('hidden');
                if (closeIcon) closeIcon.classList.remove('hidden');
                if (toggleBtn) {
                    toggleBtn.setAttribute('aria-expanded', 'true');
                    toggleBtn.setAttribute('aria-label', isRtl ? 'إغلاق القائمة الرئيسية' : 'Close navigation menu');
                }
                ctas.forEach(el => el.style.setProperty('display', 'none', 'important'));
                document.body.style.overflow = 'hidden';
            } else {
                menu.classList.add('hidden');
                if (backdrop) backdrop.classList.add('hidden');
                if (burgerIcon) burgerIcon.classList.remove('hidden');
                if (closeIcon) closeIcon.classList.add('hidden');
                if (toggleBtn) {
                    toggleBtn.setAttribute('aria-expanded', 'false');
                    toggleBtn.setAttribute('aria-label', isRtl ? 'فتح القائمة الرئيسية' : 'Open navigation menu');
                }
                ctas.forEach(el => el.style.removeProperty('display'));
                document.body.style.overflow = '';
            }
        }
    }
</script>
