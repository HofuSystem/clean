
<footer id="footer" class="bg-gray-900 text-white pt-20 pb-10 border-t border-gray-800 mt-0">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-16 text-center md:text-left">
            <div class="space-y-6 flex flex-col items-center md:items-start">
                <div class="flex items-center gap-3 justify-center md:justify-start">
                    @if(config('app.logo'))
                        <img src="{{ config('app.logo') }}" alt="Logo" class="h-10 w-auto mx-auto md:mx-0">
                    @else
                        <div class="text-2xl font-black text-white">{{ config('app.name') }}</div>
                    @endif
                </div>
                <p class="text-gray-400 text-sm leading-relaxed">{{ config('app.description') }}</p>
                <div class="flex gap-4 justify-center md:justify-start">
                    @if(setting('twitter'))<a href="{{ setting('twitter') }}" class="text-gray-400 hover:text-brand-400 transition-colors"><i class="fa-brands fa-x-twitter text-xl"></i></a>@endif
                    @if(setting('instagram'))<a href="{{ setting('instagram') }}" class="text-gray-400 hover:text-brand-400 transition-colors"><i class="fa-brands fa-instagram text-xl"></i></a>@endif
                    @if(setting('linkedin'))<a href="{{ setting('linkedin') }}" class="text-gray-400 hover:text-brand-400 transition-colors"><i class="fa-brands fa-linkedin text-xl"></i></a>@endif
                    @if(setting('tiktok'))<a href="{{ setting('tiktok') }}" class="text-gray-400 hover:text-brand-400 transition-colors"><i class="fa-brands fa-tiktok text-xl"></i></a>@endif
                    @if(setting('youtube'))<a href="{{ setting('youtube') }}" class="text-gray-400 hover:text-brand-400 transition-colors"><i class="fa-brands fa-youtube text-xl"></i></a>@endif
                    @if(setting('facebook'))<a href="{{ setting('facebook') }}" class="text-gray-400 hover:text-brand-400 transition-colors"><i class="fa-brands fa-facebook text-xl"></i></a>@endif
                    @if(setting('twitter'))<a href="{{ setting('twitter') }}" class="text-gray-400 hover:text-brand-400 transition-colors"><i class="fa-brands fa-twitter text-xl"></i></a>@endif
                </div>
            </div>

            <div class="flex flex-col items-center md:items-start">
                <h4 class="font-bold text-white mb-6 text-lg">{{ trans('quick_links') }}</h4>
                <ul class="space-y-3 text-sm text-gray-400">
                    <li><a href="{{ route('home') }}"  class="hover:text-brand-400 transition-colors">{{ trans('home') }}</a></li>
                    <li><a href="{{ route('services') }}"  class="hover:text-brand-400 transition-colors">{{ trans('services') }}</a></li>
                    <li><a href="{{ route('b2b') }}"  class="hover:text-brand-400 transition-colors">{{ trans('business') }}</a></li>
                    <li><a href="{{ route('faq') }}"  class="hover:text-brand-400 transition-colors">{{ trans('faq') }}</a></li>
                </ul>
            </div>

            <div class="flex flex-col items-center md:items-start">
                <h4 class="font-bold text-white mb-6 text-lg">{{ trans('support') }}</h4>
                <ul class="space-y-3 text-sm text-gray-400">
                    <li><a href="https://wa.me/{{ setting('whatsapp') }}" target="_blank" class="hover:text-brand-400 transition-colors">{{ trans('help_center') }}</a></li>
                    @if(route('privacy'))<li><a href="{{ route('privacy') }}" class="hover:text-brand-400 transition-colors">{{ trans('privacy_policy') }}</a></li>@endif
                    @if(route('terms'))<li><a href="{{ route('terms') }}" class="hover:text-brand-400 transition-colors">{{ trans('terms_and_conditions') }}</a></li>@endif
                </ul>
            </div>

            <div class="flex flex-col items-center md:items-start">
                <h4 class="font-bold text-white mb-6 text-lg">{{ trans('subscribe') }}</h4>
                <form action="{{ route('newsletter') }}" method="POST" class="relative mb-6 w-full max-w-xs mx-auto md:mx-0">
                    @csrf
                    <input type="email" name="email" placeholder="{{ trans('email') }}" class="w-full bg-gray-800 border border-gray-700 rounded-lg py-3 px-4 text-sm text-white focus:border-brand-500 focus:outline-none" required>
                    <button type="submit" class="absolute top-1/2 transform -translate-y-1/2 left-2 rtl:left-2 ltr:right-2 text-brand-400 hover:text-white transition-colors"><i class="fa-solid fa-paper-plane"></i></button>
                </form>
                <div class="text-sm text-gray-400 flex flex-col gap-2 items-center md:items-start">
                    <div class="flex items-center gap-2 justify-center md:justify-start"><i class="fa-solid fa-phone text-brand-500"></i> <span dir="ltr">{{ setting('phone') }}</span></div>
                    <div class="flex items-center gap-2 justify-center md:justify-start"><i class="fa-solid fa-envelope text-brand-500"></i> <span>{{ setting('email') }}</span></div>
                </div>
            </div>
        </div>

        <div class="border-t border-gray-800 pt-8 flex flex-col md:flex-row justify-center md:justify-between items-center gap-4 text-center">
            <p class="text-gray-500 text-sm">© {{ date('Y') }} {{ config('app.name') }}. {{ trans('all_rights_reserved') }}</p>
            <div class="flex gap-2 opacity-70 grayscale hover:grayscale-0 transition-all justify-center">
                <div class="mt-6 flex flex-row xs:flex-row gap-3 justify-center lg:justify-start w-full">
                    @if(setting('app_store_app'))<a href="{{ setting('app_store_app') }}" target="_blank" rel="noopener" onclick="typeof gtag === 'function' && gtag('event', 'click_download', { app_store: 'apple', campaign_source: 'website_app_features' });" class="hover:scale-105 transition-transform"><img src="https://tools.applemediaservices.com/api/badges/download-on-the-app-store/black/en-us?size=250x83" class="h-12"></a>@endif
                    @if(setting('g_play_app'))<a href="{{ setting('g_play_app') }}" target="_blank" rel="noopener" onclick="typeof gtag === 'function' && gtag('event', 'click_download', { app_store: 'google', campaign_source: 'website_app_features' });" class="hover:scale-105 transition-transform"><img src="https://upload.wikimedia.org/wikipedia/commons/7/78/Google_Play_Store_badge_EN.svg" class="h-12"></a>@endif
                </div>
            </div>
        </div>
    </div>
</footer>
