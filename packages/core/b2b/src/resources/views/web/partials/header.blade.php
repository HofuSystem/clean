<!-- Header -->
<header
    class="bg-white/80 backdrop-blur-xl h-[90px] border-b border-gray-200/50 flex items-center justify-between px-4 md:px-8 z-30 sticky top-0 print-hidden transition-all">
    <div class="flex items-center gap-4">
        <button id="mobile-menu-btn" class="md:hidden p-2 bg-gray-50 rounded-lg text-gray-600 border border-gray-200">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16">
                </path>
            </svg>
        </button>
        <div class="flex items-center gap-3 cursor-pointer group" >
            <div
                class="w-12 h-12 bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm group-hover:border-[#1c75bc] transition-colors shrink-0">
              
                <img src="{{Auth::user()->avatar_url}}"
                    alt="Hotel" class="w-full h-full object-cover" />
            </div>
            <div class="hidden sm:block text-right dir-dependent-text">
                <div class="font-black text-gray-900 text-sm group-hover:text-[#1c75bc] transition-colors">
                    {{ Auth::user()->fullname }}
                </div>
                <div
                    class="text-[10px] text-green-600 font-bold mt-0.5 uppercase tracking-widest flex items-center gap-1.5 justify-start">
                    <span
                        class="w-2 h-2 rounded-full bg-green-500 shadow-[0_0_8px_rgba(34,197,94,0.8)] animate-pulse"></span>
                    <span data-i18n="active_account">{{ trans('active_account') }}</span>
                </div>
            </div>
        </div>
    </div>
    <div class="flex items-center gap-4 md:gap-6">
        <button onclick="openModal('order-modal')"
            class="hidden sm:flex bg-gray-900 hover:bg-black text-white px-6 py-3 rounded-2xl text-sm font-bold shadow-lg shadow-black/10 transition-transform hover:-translate-y-0.5 items-center gap-2 shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            <span data-i18n="new_order">{{ trans('create_order') }}</span>
        </button>

        @php
            $currentLocale = app()->getLocale();
            $targetLocale = $currentLocale == 'ar' ? 'en' : 'ar';
            $targetUrl = LaravelLocalization::getLocalizedURL($targetLocale);
        @endphp
        
        <a href="{{ $targetUrl }}" title="{{ $targetLocale == 'ar' ? 'العربية' : 'English' }}"
            class="p-2.5 text-gray-600 hover:bg-gray-100 rounded-xl border border-transparent hover:border-gray-200 transition-colors hidden md:flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"></path>
            </svg>
            <span class="text-xs font-black uppercase tracking-widest">{{ $targetLocale }}</span>
        </a>

        <button onclick="handleLogout()" title="تسجيل الخروج"
            class="p-2.5 text-red-500 hover:bg-red-50 rounded-xl border border-transparent hover:border-red-100 transition-colors hidden md:flex">
            <svg class="w-5 h-5 rtl-rotate" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                </path>
            </svg>
        </button>
        <div class="h-8 w-px bg-gray-200 hidden md:block"></div>
        <div class="flex items-center gap-3 cursor-pointer group shrink-0" >
            <div class="hidden sm:flex flex-col text-left dir-dependent-text-reverse">
                <span class="font-black text-gray-900 leading-tight text-lg md:text-xl tracking-tight"
                    data-i18n="app_name">{{ config('app.name') }}</span>
                <span class="text-[10px] text-gray-400 font-bold uppercase tracking-[0.2em]">B2B Portal</span>
            </div>
            <img src="https://i.postimg.cc/gxGfY6Z7/lwqw-msttyl-2-(1).png" alt="Clean Station"
                class="h-10 w-auto object-contain group-hover:scale-105 transition-transform shrink-0">
        </div>
    </div>
</header>