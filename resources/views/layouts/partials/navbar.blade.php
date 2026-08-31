<nav class="fixed w-full z-50 transition-all duration-300 bg-white/90 backdrop-blur-md border-b border-gray-100" id="navbar">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-20">
            
            <a href="{{ route('home') }}" class="flex-shrink-0 flex items-center gap-2">
                @if(config('app.logo'))
                    <img src="{{ config('app.logo') }}" alt="Logo" class="h-10 w-auto">
                @else
                    <div class="w-10 h-10 bg-brand-600 text-white rounded-xl flex items-center justify-center text-xl shadow-lg"><i class="fa-solid fa-soap"></i></div>
                    <span class="font-black text-xl tracking-tighter text-gray-900 hidden sm:block">{{ app()->getLocale() === 'ar' ? 'كلين ستيشن' : 'Clean Station' }}</span>
                @endif
            </a>

            <div class="hidden lg:flex items-center space-x-1 rtl:space-x-reverse bg-gray-50 px-2 py-1.5 rounded-full border border-gray-200/50">
                <a href="{{ route('home') }}" class="px-3.5 py-2 rounded-full text-xs font-bold {{ Route::is('home') ? 'bg-white text-brand-600 shadow-sm' : 'text-gray-600 hover:text-brand-600' }} transition-all">{{ trans('home') }}</a>
                <a href="{{ route('services') }}" class="px-3.5 py-2 rounded-full text-xs font-bold {{ Route::is('services*') ? 'bg-white text-brand-600 shadow-sm' : 'text-gray-600 hover:text-brand-600' }} transition-all">{{ trans('services') }}</a>
                <a href="{{ route('pricing') }}" class="px-3.5 py-2 rounded-full text-xs font-bold {{ Route::is('pricing') ? 'bg-white text-brand-600 shadow-sm' : 'text-gray-600 hover:text-brand-600' }} transition-all">{{ app()->getLocale() === 'ar' ? 'التسعير' : 'Pricing' }}</a>
                <a href="{{ route('coverage') }}" class="px-3.5 py-2 rounded-full text-xs font-bold {{ Route::is('coverage') ? 'bg-white text-brand-600 shadow-sm' : 'text-gray-600 hover:text-brand-600' }} transition-all">{{ app()->getLocale() === 'ar' ? 'التغطية' : 'Coverage' }}</a>
                <a href="{{ route('why-us') }}" class="px-3.5 py-2 rounded-full text-xs font-bold {{ Route::is('why-us') ? 'bg-white text-brand-600 shadow-sm' : 'text-gray-600 hover:text-brand-600' }} transition-all">{{ trans('why_us') }}</a>
                <a href="{{ route('b2b') }}" class="px-3.5 py-2 rounded-full text-xs font-bold {{ Route::is('b2b') ? 'bg-white text-brand-600 shadow-sm' : 'text-gray-600 hover:text-brand-600' }} transition-all">{{ trans('business') }}</a>
                <a href="{{ route('blog') }}" class="px-3.5 py-2 rounded-full text-xs font-bold {{ Route::is('blog*') || Route::is('blogs*') ? 'bg-white text-brand-600 shadow-sm' : 'text-gray-600 hover:text-brand-600' }} transition-all">{{ trans('blog') }}</a>
                <a href="{{ route('faq') }}" class="px-3.5 py-2 rounded-full text-xs font-bold {{ Route::is('faq') ? 'bg-white text-brand-600 shadow-sm' : 'text-gray-600 hover:text-brand-600' }} transition-all">{{ trans('faq') }}</a>
            </div>

            <div class="flex items-center gap-2 md:gap-3">
                {{-- Direct Language Switcher to the other language --}}
                @php
                    $otherLocale = LaravelLocalization::getCurrentLocale() === 'ar' ? 'en' : 'ar';
                @endphp
                <a rel="alternate" hreflang="{{ $otherLocale }}" href="{{ LaravelLocalization::getLocalizedURL($otherLocale, null, [], true) }}" class="w-9 h-9 rounded-full bg-gray-100 hover:bg-brand-50 hover:text-brand-600 border border-gray-200/60 flex items-center justify-center transition-all text-xs font-black uppercase text-gray-700 shadow-sm" title="{{ $otherLocale === 'ar' ? 'العربية' : 'English' }}">
                    {{ $otherLocale }}
                </a>

                @if(Route::is('b2b'))
                    <a href="{{ route('client.login') }}" class="hidden md:flex bg-brand-600 text-white px-4 py-2.5 rounded-xl font-bold text-xs hover:bg-brand-700 transition-all shadow-lg shadow-brand-200">
                        {{ trans('login') }}
                    </a>
                @endif
                <a href="{{ route('contact') }}" class="hidden md:flex bg-brand-600 text-white px-4 py-2.5 rounded-xl font-bold text-xs hover:bg-brand-700 transition-all shadow-lg shadow-brand-200">
                    {{ trans('contact') }}
                </a>
                
                <button onclick="toggleMobileMenu()" class="lg:hidden w-10 h-10 flex items-center justify-center text-gray-600 hover:text-brand-600 transition-colors focus:outline-none" aria-label="Toggle Menu">
                    <i class="fa-solid fa-bars text-2xl"></i>
                </button>
            </div>
        </div>
    </div>

    <div id="mobile-menu" class="hidden lg:hidden bg-white border-t border-gray-100 absolute w-full left-0 shadow-2xl origin-top animate-fade-in-down h-screen overflow-y-auto pb-20">
        <div class="px-4 pt-4 pb-6 space-y-2">
            <a href="{{ route('home') }}" class="block px-4 py-3 rounded-xl text-base font-bold text-gray-700 hover:bg-brand-50"><i class="fa-solid fa-house w-6 text-center text-brand-500"></i> {{ trans('home') }}</a>
            <a href="{{ route('services') }}" class="block px-4 py-3 rounded-xl text-base font-bold text-gray-700 hover:bg-brand-50"><i class="fa-solid fa-layer-group w-6 text-center text-brand-500"></i> {{ trans('services') }}</a>
            <a href="{{ route('pricing') }}" class="block px-4 py-3 rounded-xl text-base font-bold text-gray-700 hover:bg-brand-50"><i class="fa-solid fa-tags w-6 text-center text-brand-500"></i> {{ app()->getLocale() === 'ar' ? 'التسعير' : 'Pricing' }}</a>
            <a href="{{ route('coverage') }}" class="block px-4 py-3 rounded-xl text-base font-bold text-gray-700 hover:bg-brand-50"><i class="fa-solid fa-map-location-dot w-6 text-center text-brand-500"></i> {{ app()->getLocale() === 'ar' ? 'التغطية' : 'Coverage' }}</a>
            <a href="{{ route('why-us') }}" class="block px-4 py-3 rounded-xl text-base font-bold text-gray-700 hover:bg-brand-50"><i class="fa-solid fa-crown w-6 text-center text-brand-500"></i> {{ trans('why_us') }}</a>
            <a href="{{ route('b2b') }}" class="block px-4 py-3 rounded-xl text-base font-bold text-gray-700 hover:bg-brand-50"><i class="fa-solid fa-briefcase w-6 text-center text-brand-500"></i> {{ trans('business') }}</a>
            <a href="{{ route('blog') }}" class="block px-4 py-3 rounded-xl text-base font-bold text-gray-700 hover:bg-brand-50"><i class="fa-solid fa-newspaper w-6 text-center text-brand-500"></i> {{ trans('blog') }}</a>
            <a href="{{ route('faq') }}" class="block px-4 py-3 rounded-xl text-base font-bold text-gray-700 hover:bg-brand-50"><i class="fa-solid fa-circle-question w-6 text-center text-brand-500"></i> {{ trans('faq') }}</a>
            @if(Route::is('b2b'))
            <a href="{{ route('client.login') }}" class="block px-4 py-3 rounded-xl text-base font-bold text-gray-700 hover:bg-brand-50"><i class="fa-solid fa-right-to-bracket w-6 text-center text-brand-500"></i> {{ trans('login') }}</a>
            @endif
            <a rel="alternate" hreflang="{{ $otherLocale }}" href="{{ LaravelLocalization::getLocalizedURL($otherLocale, null, [], true) }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-base font-bold text-brand-600 hover:bg-brand-50 border-t border-gray-100 mt-2">
                <i class="fa-solid fa-globe w-6 text-center text-brand-500"></i>
                <span>{{ $otherLocale === 'ar' ? 'العربية' : 'English' }} ({{ strtoupper($otherLocale) }})</span>
            </a>
        </div>
    </div>
</nav>

<script>
    // Mobile Menu Toggle
    function toggleMobileMenu() {
        const menu = document.getElementById('mobile-menu');
        if(menu) {
            menu.classList.toggle('hidden');
        }
    }
</script>
