<nav class="fixed w-full z-50 transition-all duration-300 bg-white/90 backdrop-blur-md border-b border-gray-100" id="navbar">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-20">
            
            <a href="{{ route('home') }}" class="flex-shrink-0 flex items-center gap-2">
                @if(config('app.logo'))
                    <img src="{{ config('app.logo') }}" alt="Logo" class="h-10 w-auto">
                @else
                    <div class="w-10 h-10 bg-brand-600 text-white rounded-xl flex items-center justify-center text-xl shadow-lg"><i class="fa-solid fa-soap"></i></div>
                    <span class="font-black text-xl tracking-tighter text-gray-900 hidden sm:block"><span class="lang-ar">كلين ستيشن</span><span class="lang-en">Clean Station</span></span>
                @endif
            </a>

            <div class="hidden lg:flex items-center space-x-1 rtl:space-x-reverse bg-gray-50 px-2 py-1.5 rounded-full border border-gray-200/50">
                <a href="{{ route('home') }}" class="px-4 py-2 rounded-full text-xs font-bold {{ Route::is('home') ? 'bg-white text-brand-600 shadow-sm' : 'text-gray-600 hover:text-brand-600' }} transition-all">{{   trans('home') }}</a>
                <a href="{{ route('services') }}" class="px-4 py-2 rounded-full text-xs font-bold {{ Route::is('services') ? 'bg-white text-brand-600 shadow-sm' : 'text-gray-600 hover:text-brand-600' }} transition-all">{{ trans('services') }}</a>
                <a href="{{ route('why-us') }}" class="px-4 py-2 rounded-full text-xs font-bold {{ Route::is('why-us') ? 'bg-white text-brand-600 shadow-sm' : 'text-gray-600 hover:text-brand-600' }} transition-all">{{ trans('why_us') }}</a>
                <a href="{{ route('b2b') }}" class="px-4 py-2 rounded-full text-xs font-bold {{ Route::is('b2b') ? 'bg-white text-brand-600 shadow-sm' : 'text-gray-600 hover:text-brand-600' }} transition-all">{{ trans('business') }}</a>
                <a href="{{ route('blog') }}" class="px-4 py-2 rounded-full text-xs font-bold {{ Route::is('blog') ? 'bg-white text-brand-600 shadow-sm' : 'text-gray-600 hover:text-brand-600' }} transition-all">{{ trans('blog') }}</a>
                <a href="{{ route('faq') }}" class="px-4 py-2 rounded-full text-xs font-bold {{ Route::is('faq') ? 'bg-white text-brand-600 shadow-sm' : 'text-gray-600 hover:text-brand-600' }} transition-all">{{ trans('faq') }}</a>
            </div>

            <div class="flex items-center gap-2 md:gap-3">
                {{-- Language Switcher using Laravel Localization --}}
                <div class="relative" id="lang-switcher">
                    <button onclick="toggleLangMenu()" class="w-9 h-9 rounded-full bg-gray-100 hover:bg-gray-200 flex items-center justify-center transition-colors text-xs font-bold uppercase">
                        {{ LaravelLocalization::getCurrentLocale() }}
                    </button>
                    <div id="lang-menu" class="hidden absolute right-0 rtl:right-auto rtl:left-0 mt-2 w-36 bg-white rounded-xl shadow-lg border border-gray-100 py-1 z-50 animate-fade-in-down">
                        @foreach(LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                            <a rel="alternate" hreflang="{{ $localeCode }}" href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}" class="flex items-center gap-2 px-4 py-2.5 text-sm font-medium {{ LaravelLocalization::getCurrentLocale() == $localeCode ? 'bg-brand-50 text-brand-600' : 'text-gray-700 hover:bg-gray-50' }} transition-colors">
                                <span class="w-6 h-6 rounded-full {{ LaravelLocalization::getCurrentLocale() == $localeCode ? 'bg-brand-100' : 'bg-gray-100' }} flex items-center justify-center text-xs font-bold uppercase">{{ $localeCode }}</span>
                                <span>{{ $properties['native'] }}</span>
                                @if(LaravelLocalization::getCurrentLocale() == $localeCode)
                                    <i class="fa-solid fa-check text-brand-600 ml-auto rtl:ml-0 rtl:mr-auto text-xs"></i>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>
                @if(Route::is('b2b'))
                    <a href="{{ route('client.login') }}" class="hidden md:flex bg-brand-600 text-white px-4 py-2.5 rounded-xl font-bold text-xs hover:bg-brand-700 transition-all shadow-lg shadow-brand-200">
                        {{ trans('login') }}
                    </a>
                @endif
                <a href="{{ route('contact') }}" class="hidden md:flex bg-brand-600 text-white px-4 py-2.5 rounded-xl font-bold text-xs hover:bg-brand-700 transition-all shadow-lg shadow-brand-200">
                    {{ trans('contact') }}
                </a>
                
                <button onclick="toggleMobileMenu()" class="lg:hidden w-10 h-10 flex items-center justify-center text-gray-600 hover:text-brand-600 transition-colors focus:outline-none">
                    <i class="fa-solid fa-bars text-2xl"></i>
                </button>
            </div>
        </div>
    </div>

    <div id="mobile-menu" class="hidden lg:hidden bg-white border-t border-gray-100 absolute w-full left-0 shadow-2xl origin-top animate-fade-in-down h-screen overflow-y-auto pb-20">
        <div class="px-4 pt-4 pb-6 space-y-2">
            <a href="{{ route('home') }}" class="block px-4 py-3 rounded-xl text-base font-bold text-gray-700 hover:bg-brand-50"><i class="fa-solid fa-house w-6 text-center text-brand-500"></i> {{ trans('home') }}</a>
            <a href="{{ route('services') }}" class="block px-4 py-3 rounded-xl text-base font-bold text-gray-700 hover:bg-brand-50"><i class="fa-solid fa-layer-group w-6 text-center text-brand-500"></i> {{  trans('services') }}</a>
            <a href="{{ route('why-us') }}" class="block px-4 py-3 rounded-xl text-base font-bold text-gray-700 hover:bg-brand-50"><i class="fa-solid fa-crown w-6 text-center text-brand-500"></i> {{ trans('why_us') }}</a>
            <a href="{{ route('b2b') }}" class="block px-4 py-3 rounded-xl text-base font-bold text-gray-700 hover:bg-brand-50"><i class="fa-solid fa-briefcase w-6 text-center text-brand-500"></i> {{ trans('business') }}</a>
            <a href="{{ route('blog') }}" class="block px-4 py-3 rounded-xl text-base font-bold text-gray-700 hover:bg-brand-50"><i class="fa-solid fa-newspaper w-6 text-center text-brand-500"></i> {{ trans('blog') }}</a>
            <a href="{{ route('faq') }}" class="block px-4 py-3 rounded-xl text-base font-bold text-gray-700 hover:bg-brand-50"><i class="fa-solid fa-circle-question w-6 text-center text-brand-500"></i> {{ trans('faq') }}</a>
            @if(Route::is('b2b'))
            <a href="{{ route('client.login') }}" class="block px-4 py-3 rounded-xl text-base font-bold text-gray-700 hover:bg-brand-50"><i class="fa-solid fa-envelope w-6 text-center text-brand-500"></i> {{ trans('login') }}</a>
                @endif
        </div>
    </div>
</nav>

<script>
    // Language Switcher Toggle
    function toggleLangMenu() {
        const menu = document.getElementById('lang-menu');
        menu.classList.toggle('hidden');
    }

  

    // Mobile Menu Toggle
    function toggleMobileMenu() {
        const menu = document.getElementById('mobile-menu');
        menu.classList.toggle('hidden');
    }
</script>
