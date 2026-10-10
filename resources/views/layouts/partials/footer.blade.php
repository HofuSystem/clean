@php
    $isRtl = app()->getLocale() === 'ar';
@endphp

<footer id="footer" class="bg-gray-950 text-white pt-20 pb-12 border-t border-gray-800/80 mt-0">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10 lg:gap-12 mb-16 text-center {{ $isRtl ? 'md:text-right' : 'md:text-left' }}">
            {{-- Brand info --}}
            <div class="space-y-6 flex flex-col items-center {{ $isRtl ? 'md:items-start' : 'md:items-start' }}">
                <div class="flex items-center gap-3 justify-center md:justify-start">
                    @if(config('app.logo'))
                        <x-website-image :src="config('app.logo')" sizes="120px" width="1503" height="826" alt="Logo" class="h-10 w-auto mx-auto md:mx-0 footer-logo" />
                    @else
                        <div class="text-2xl font-black text-white tracking-tight">{{ config('app.name') }}</div>
                    @endif
                </div>
                <p class="text-gray-400 text-sm leading-relaxed max-w-sm">{{ config('app.description') }}</p>
                <div class="flex flex-nowrap items-center gap-2 justify-center md:justify-start max-w-full">
                    @if(setting('twitter'))
                    <a href="{{ setting('twitter') }}" target="_blank" rel="noopener" 
                       class="social-btn-twitter w-11 h-11 min-w-[44px] min-h-[44px] flex-shrink-0 rounded-xl bg-gray-900 border border-gray-800 hover:border-sky-500 hover:bg-sky-600 text-gray-300 hover:text-white flex items-center justify-center transition-all duration-200 shadow-sm hover:scale-105" 
                       aria-label="X (Twitter)" title="X (Twitter)">
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                    </a>
                    @endif
                    @if(setting('instagram'))
                    <a href="{{ setting('instagram') }}" target="_blank" rel="noopener" 
                       class="social-btn-instagram w-11 h-11 min-w-[44px] min-h-[44px] flex-shrink-0 rounded-xl bg-gray-900 border border-gray-800 hover:border-pink-500 hover:bg-pink-600 text-gray-300 hover:text-white flex items-center justify-center transition-all duration-200 shadow-sm hover:scale-105" 
                       aria-label="Instagram" title="Instagram">
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                    </a>
                    @endif
                    @if(setting('tiktok'))
                    <a href="{{ setting('tiktok') }}" target="_blank" rel="noopener" 
                       class="social-btn-tiktok w-11 h-11 min-w-[44px] min-h-[44px] flex-shrink-0 rounded-xl bg-gray-900 border border-gray-800 hover:border-neutral-400 hover:bg-black text-gray-300 hover:text-white flex items-center justify-center transition-all duration-200 shadow-sm hover:scale-105" 
                       aria-label="TikTok" title="TikTok">
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.2 1.74 2.89 2.89 0 0 1 2.31-4.64 2.93 2.93 0 0 1 .88.13V9.4a6.84 6.84 0 0 0-1-.05A6.33 6.33 0 0 0 5 20.1a6.34 6.34 0 0 0 10.86-4.43v-7a8.16 8.16 0 0 0 4.77 1.52v-3.4a4.85 4.85 0 0 1-1.04-.1z"/></svg>
                    </a>
                    @endif
                    @if(setting('snapchat'))
                    <a href="{{ setting('snapchat') }}" target="_blank" rel="noopener" 
                       class="social-btn-snapchat w-11 h-11 min-w-[44px] min-h-[44px] flex-shrink-0 rounded-xl bg-gray-900 border border-gray-800 hover:border-yellow-400 hover:bg-yellow-500 text-gray-300 hover:text-gray-900 flex items-center justify-center transition-all duration-200 shadow-sm hover:scale-105" 
                       aria-label="Snapchat" title="Snapchat">
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M12.206.793c.99 0 4.347.276 5.93 3.821.529 1.193.403 3.219.299 4.847l-.003.06c-.012.18-.022.345-.03.51.075.045.203.09.401.09.3-.016.659-.12 1.033-.301.165-.088.344-.104.464-.104.182 0 .359.029.509.09.45.149.734.479.734.838.015.449-.39.839-1.213 1.168-.089.029-.209.075-.344.119-.45.135-1.139.36-1.333.81-.09.224-.061.524.12.868l.015.015c.06.136 1.526 3.475 4.791 4.014.255.044.435.27.42.509 0 .075-.015.149-.045.225-.24.569-1.273.988-3.146 1.271-.059.091-.12.375-.164.57-.029.179-.074.36-.134.553-.076.271-.27.405-.555.405h-.03c-.135 0-.313-.031-.538-.074-.36-.075-.765-.135-1.273-.135-.3 0-.599.015-.913.074-.6.104-1.123.464-1.723.884-.853.599-1.826 1.288-3.294 1.288-.06 0-.119-.015-.18-.015h-.149c-1.468 0-2.427-.675-3.279-1.288-.599-.42-1.107-.779-1.707-.884-.314-.045-.629-.074-.928-.074-.54 0-.958.089-1.272.149-.211.043-.391.074-.54.074-.374 0-.523-.224-.583-.42-.061-.192-.09-.389-.135-.567-.046-.181-.105-.494-.166-.57-1.918-.222-2.95-.642-3.189-1.226-.031-.063-.052-.15-.055-.225-.015-.243.165-.465.42-.509 3.264-.54 4.73-3.879 4.791-4.02l.016-.029c.18-.345.224-.645.119-.869-.195-.434-.884-.658-1.332-.809-.121-.029-.24-.074-.346-.119-1.107-.435-1.257-.93-1.197-1.273.09-.479.674-.793 1.168-.793.146 0 .27.029.383.074.42.194.789.3 1.104.3.234 0 .384-.06.465-.105l-.046-.569c-.098-1.626-.225-3.651.307-4.837C7.392 1.077 10.739.807 11.727.807l.419-.015h.06z"/></svg>
                    </a>
                    @endif
                    @if(setting('youtube'))
                    <a href="{{ setting('youtube') }}" target="_blank" rel="noopener" 
                       class="social-btn-youtube w-11 h-11 min-w-[44px] min-h-[44px] flex-shrink-0 rounded-xl bg-gray-900 border border-gray-800 hover:border-red-500 hover:bg-red-600 text-gray-300 hover:text-white flex items-center justify-center transition-all duration-200 shadow-sm hover:scale-105" 
                       aria-label="YouTube" title="YouTube">
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                    </a>
                    @endif
                    @if(setting('facebook'))
                    <a href="{{ setting('facebook') }}" target="_blank" rel="noopener" 
                       class="social-btn-facebook w-11 h-11 min-w-[44px] min-h-[44px] flex-shrink-0 rounded-xl bg-gray-900 border border-gray-800 hover:border-blue-500 hover:bg-blue-600 text-gray-300 hover:text-white flex items-center justify-center transition-all duration-200 shadow-sm hover:scale-105" 
                       aria-label="Facebook" title="Facebook">
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                    </a>
                    @endif
                </div>
            </div>

            {{-- Quick Links --}}
            <div class="flex flex-col items-center md:items-start">
                <h3 class="font-extrabold text-white mb-5 text-sm uppercase tracking-wider">{{ trans('quick_links') }}</h3>
                <ul class="space-y-2.5 text-sm text-gray-400">
                    <li><a href="{{ route('home') }}" class="hover:text-white transition-colors">{{ trans('home') }}</a></li>
                    <li><a href="{{ route('services') }}" class="hover:text-white transition-colors">{{ trans('services') }}</a></li>
                    <li><a href="{{ route('pricing') }}" class="hover:text-white transition-colors">{{ $isRtl ? 'قائمة الأسعار' : 'Pricing List' }}</a></li>
                    <li><a href="{{ route('coverage') }}" class="hover:text-white transition-colors">{{ $isRtl ? 'تغطية المدن والأحياء' : 'Cities & Districts Coverage' }}</a></li>
                    <li><a href="{{ route('b2b') }}" class="hover:text-white transition-colors">{{ $isRtl ? 'خدمات الأعمال والشركات' : 'Corporate & B2B Solutions' }}</a></li>
                    <li><a href="{{ route('why-us') }}" class="hover:text-white transition-colors">{{ $isRtl ? 'لماذا كلين ستيشن' : 'Why Clean Station' }}</a></li>
                    <li><a href="{{ route('faq') }}" class="hover:text-white transition-colors">{{ trans('faq') }}</a></li>
                </ul>
            </div>

            {{-- Support Links --}}
            <div class="flex flex-col items-center md:items-start">
                <h3 class="font-extrabold text-white mb-5 text-sm uppercase tracking-wider">{{ trans('support') }}</h3>
                <ul class="space-y-2.5 text-sm text-gray-400">
                    <li><a href="https://wa.me/{{ setting('whatsapp') }}" target="_blank" rel="noopener" class="hover:text-white transition-colors">{{ trans('help_center') }}</a></li>
                    @if(route('privacy'))<li><a href="{{ route('privacy') }}" class="hover:text-white transition-colors">{{ trans('privacy_policy') }}</a></li>@endif
                    @if(route('terms'))<li><a href="{{ route('terms') }}" class="hover:text-white transition-colors">{{ trans('terms_and_conditions') }}</a></li>@endif
                </ul>
            </div>

            {{-- Contact & Newsletter --}}
            <div class="flex flex-col items-center md:items-start">
                <h3 class="font-extrabold text-white mb-5 text-sm uppercase tracking-wider">{{ trans('subscribe') }}</h3>
                <form action="{{ route('newsletter') }}" method="POST" class="relative mb-5 w-full max-w-xs mx-auto md:mx-0">
                    @csrf
                    <label for="newsletter-email-input" class="sr-only">{{ trans('email') }}</label>
                    <input id="newsletter-email-input" type="email" name="email" aria-label="{{ trans('email') }}" placeholder="{{ trans('email') }}" class="w-full bg-gray-900 border border-gray-800 rounded-xl py-2.5 px-4 text-sm text-white focus:border-brand-500 focus:outline-none" required>
                    <button type="submit" aria-label="{{ trans('subscribe') ?: 'Subscribe' }}" class="absolute top-1/2 transform -translate-y-1/2 {{ $isRtl ? 'left-1' : 'right-1' }} w-11 h-11 flex items-center justify-center text-brand-400 hover:text-white transition-colors">
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/></svg>
                    </button>
                </form>
                <div class="text-xs text-gray-400 flex flex-col gap-2.5 items-center md:items-start">
                    <div class="flex items-center gap-2 justify-center md:justify-start">
                        <svg class="w-4 h-4 text-brand-500 shrink-0 fill-current" viewBox="0 0 24 24"><path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/></svg>
                        <span dir="ltr">{{ setting('phone') }}</span>
                    </div>
                    <div class="flex items-center gap-2 justify-center md:justify-start">
                        <svg class="w-4 h-4 text-brand-500 shrink-0 fill-current" viewBox="0 0 24 24"><path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>
                        <span>{{ setting('email') }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Footer bottom --}}
        <div class="border-t border-gray-900 pt-8 flex flex-col md:flex-row justify-center md:justify-between items-center gap-6 text-center">
            <p class="text-gray-400 text-xs md:text-sm">© {{ date('Y') }} {{ config('app.name') }}. {{ trans('all_rights_reserved') }}</p>
            <div class="flex flex-row items-center gap-3 justify-center">
                <a href="https://cleanstation.app.link/?channel=footer" target="_blank" rel="noopener" 
                   onclick="typeof gtag === 'function' && gtag('event', 'click_download', { app_store: 'apple', campaign_source: 'website_footer' });" 
                   class="inline-flex items-center justify-center min-h-[48px] hover:scale-105 transition-transform duration-200" aria-label="App Store">
                    <img width="144" height="48" src="{{ asset($isRtl ? 'assets/store-badges/app-store-ar.svg' : 'assets/store-badges/app-store.svg') }}" 
                         class="store-badge store-badge-apple" alt="{{ $isRtl ? 'حمله من App Store' : 'App Store' }}">
                </a>
                <a href="https://cleanstation.app.link/?channel=footer" target="_blank" rel="noopener" 
                   onclick="typeof gtag === 'function' && gtag('event', 'click_download', { app_store: 'google', campaign_source: 'website_footer' });" 
                   class="inline-flex items-center justify-center min-h-[48px] hover:scale-105 transition-transform duration-200" aria-label="Google Play">
                    <img width="161" height="48" src="{{ asset($isRtl ? 'assets/store-badges/google-play-ar.png' : 'assets/store-badges/google-play.svg') }}" 
                         class="store-badge store-badge-google" alt="{{ $isRtl ? 'احصل عليه من Google Play' : 'Google Play' }}">
                </a>
            </div>
        </div>
    </div>
</footer>
