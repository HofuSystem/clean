@php
    $counters = \Core\Pages\Models\Counter::get();
    $steps = \Core\Pages\Models\WorkStep::get();
@endphp
@if($section)
<section id="home" class="page-section bg-white overflow-hidden pt-8 pb-16 lg:pt-20 lg:pb-24 relative">
    
    <div class="absolute top-[-10%] right-[-10%] w-[500px] h-[500px] bg-brand-100/50 rounded-full blur-[100px] pointer-events-none"></div>
    <div class="absolute bottom-[10%] left-[-10%] w-[500px] h-[500px] bg-accent-100/40 rounded-full blur-[100px] pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        
        <div class="grid lg:grid-cols-2 gap-10 lg:gap-16 items-center mb-20 lg:mb-32">
            
            <div class="text-center lg:text-start space-y-6 order-2 lg:order-1" data-aos="fade-up">
                <div class="inline-flex items-center gap-2 bg-white border border-brand-100 shadow-sm px-3 py-1.5 rounded-full">
                    <span class="flex h-2.5 w-2.5 relative"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span><span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-green-500"></span></span>
                    <span class="text-[10px] md:text-xs font-bold text-gray-600 tracking-wide"><span class="lang-ar">{{ $section->translate('ar')->small_title ?? $section->small_title }}</span><span class="lang-en">{{ $section->translate('en')->small_title ?? $section->small_title }}</span></span>
                </div>
                @php
                    $titleArray = explode('..', $section->title);
                @endphp
                <h1 class="text-4xl md:text-6xl lg:text-7xl font-black text-gray-900 leading-[1.1] tracking-tight">
                    <span>{{  $titleArray[0] ?? '' }}... 
                    <br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-brand-600 to-brand-400">{{ $titleArray[1] ?? '' }}</span>
                </h1>

                <p class="text-base md:text-lg text-gray-500 leading-relaxed max-w-lg mx-auto lg:mx-0">
                    <span >{!!  $section->description !!}</span>
                </p>

                <div class="flex flex-col xs:flex-row gap-3 justify-center lg:justify-start w-full">
                    @if(setting('app_store_app'))<a href="{{ setting('app_store_app') }}" target="_blank" rel="noopener" onclick="typeof gtag === 'function' && gtag('event', 'click_download', { app_store: 'apple', campaign_source: 'website_hero' });" class="w-full xs:w-auto"><img src="https://tools.applemediaservices.com/api/badges/download-on-the-app-store/black/en-us?size=250x83" class="h-12 w-auto mx-auto shadow-md rounded-lg hover:-translate-y-1 transition-transform"></a>@endif
                    @if(setting('g_play_app'))<a href="{{ setting('g_play_app') }}" target="_blank" rel="noopener" onclick="typeof gtag === 'function' && gtag('event', 'click_download', { app_store: 'google', campaign_source: 'website_hero' });" class="w-full xs:w-auto"><img src="https://upload.wikimedia.org/wikipedia/commons/7/78/Google_Play_Store_badge_EN.svg" class="h-12 w-auto mx-auto shadow-md rounded-lg hover:-translate-y-1 transition-transform"></a>@endif
                </div>

                <div class="flex items-center justify-center lg:justify-start gap-4 pt-2">
                    <div class="flex -space-x-3 space-x-reverse">
                        <img class="w-8 h-8 rounded-full border-2 border-white" src="https://i.pravatar.cc/100?img=11">
                        <img class="w-8 h-8 rounded-full border-2 border-white" src="https://i.pravatar.cc/100?img=12">
                        <div class="w-8 h-8 rounded-full border-2 border-white bg-gray-900 text-white flex items-center justify-center text-[10px] font-bold">+11k</div>
                    </div>
                    <div class="text-start">
                        <div class="flex text-yellow-400 text-xs mb-0.5"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i></div>
                        <div class="text-[10px] font-bold text-gray-500">{{ trans('trusted clients') }}</div>
                    </div>
                </div>
            </div>

            <div class="relative order-1 lg:order-2 flex justify-center perspective-1000 mt-8 lg:mt-0" data-aos="zoom-in">
                <div class="relative w-[260px] md:w-[300px] h-[540px] md:h-[600px] bg-black rounded-[45px] border-[8px] border-gray-900 shadow-2xl overflow-hidden transform rotate-[-3deg] hover:rotate-0 transition-transform duration-500 z-20">
                    @if($section->image_url)
                        <img src="{{ $section->image_url }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full bg-gray-100 flex items-center justify-center">{{ trans('app screen') }}</div>
                    @endif
                </div>
                <div class="absolute top-[15%] -right-4 md:-right-8 bg-white/90 backdrop-blur p-3 rounded-xl shadow-lg border border-white/50 z-30 animate-float hidden md:flex gap-3 items-center">
                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center text-green-600"><i class="fa-solid fa-clock"></i></div>
                    <div><div class="text-[10px] text-gray-400 font-bold uppercase">{{ trans('time') }}</div><div class="text-xs font-bold">{{ trans('on time') }}</div></div>
                </div>
                <div class="absolute bottom-[20%] -left-4 md:-left-8 bg-white/90 backdrop-blur p-3 rounded-xl shadow-lg border border-white/50 z-30 animate-float hidden md:flex gap-3 items-center" style="animation-delay: 1.5s">
                    <div class="w-8 h-8 bg-brand-100 rounded-full flex items-center justify-center text-brand-600"><i class="fa-solid fa-bolt"></i></div>
                    <div><div class="text-[10px] text-gray-400 font-bold uppercase">{{ trans('the service') }}</div><div class="text-xs font-bold">{{ trans('quick') }}</div></div>
                </div>
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-6 mb-24" data-aos="fade-up">
            
            <div class="group relative bg-white rounded-3xl p-8 border border-gray-100 shadow-lg hover:shadow-2xl hover:border-brand-200 transition-all duration-300 overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-brand-50 rounded-full blur-2xl -mr-10 -mt-10 transition-all group-hover:bg-brand-100"></div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-brand-600 text-white rounded-xl flex items-center justify-center text-xl shadow-lg shadow-brand-500/30"><i class="fa-solid fa-bolt"></i></div>
                        <span class="bg-brand-50 text-brand-600 text-[10px] font-bold px-2 py-1 rounded-full">{{ trans('fastest') }}</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">{{ trans('fast order') }}</h3>
                    
                    <div class="flex items-center gap-2 text-xs text-gray-500 mt-4 bg-gray-50 p-3 rounded-xl">
                        <div class="flex flex-col items-center"><i class="fa-solid fa-location-dot text-brand-500 mb-1"></i><span>{{ trans('location') }}</span></div>
                        <div class="h-px w-4 bg-gray-300"></div>
                        <div class="flex flex-col items-center"><i class="fa-regular fa-clock text-brand-500 mb-1"></i><span>{{ trans('time') }}</span></div>
                        <div class="h-px w-4 bg-gray-300"></div>
                        <div class="flex flex-col items-center"><i class="fa-solid fa-check-circle text-green-500 mb-1"></i><span>{{ trans('confirmation') }}</span></div>
                    </div>
                </div>
            </div>

            <div class="group relative bg-white rounded-3xl p-8 border border-gray-100 shadow-lg hover:shadow-2xl hover:border-purple-200 transition-all duration-300 overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-purple-50 rounded-full blur-2xl -mr-10 -mt-10 transition-all group-hover:bg-purple-100"></div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-purple-600 text-white rounded-xl flex items-center justify-center text-xl shadow-lg shadow-purple-500/30"><i class="fa-solid fa-list-check"></i></div>
                        <span class="bg-purple-50 text-purple-600 text-[10px] font-bold px-2 py-1 rounded-full">{{ trans('detailed') }}</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">{{ trans('detailed') }}</h3>
                    
                    <div class="flex items-center gap-2 text-xs text-gray-500 mt-4 bg-gray-50 p-3 rounded-xl overflow-x-auto">
                        <div class="flex flex-col items-center min-w-[30px]"><i class="fa-solid fa-shirt text-purple-500 mb-1"></i><span>{{ trans('pieces') }}</span></div>
                        <div class="h-px w-3 bg-gray-300"></div>
                        <div class="flex flex-col items-center min-w-[30px]"><i class="fa-solid fa-location-dot text-purple-500 mb-1"></i><span>{{ trans('location') }}</span></div>
                        <div class="h-px w-3 bg-gray-300"></div>
                        <div class="flex flex-col items-center min-w-[30px]"><i class="fa-regular fa-clock text-purple-500 mb-1"></i><span>{{ trans('time') }}</span></div>
                        <div class="h-px w-3 bg-gray-300"></div>
                        <div class="flex flex-col items-center min-w-[30px]"><i class="fa-solid fa-check-circle text-green-500 mb-1"></i><span>{{ trans('confirmation') }}</span></div>
                    </div>
                </div>
            </div>
        </div>

       
        <div class="max-w-3xl mx-auto mb-20" data-aos="fade-up">
            <div class="text-center mb-12">
                <span class="text-brand-600 font-extrabold tracking-widest text-[10px] uppercase bg-brand-50 px-3 py-1 rounded-full">{{ trans('workflow') }}</span>
                <h2 class="text-3xl font-black text-gray-900 mt-2">{{ trans('The Ultimate Care Journey') }}</h2>
            </div>
            
            <div class="relative pl-4 md:pl-0">
                <div class="absolute left-4 md:left-1/2 top-0 bottom-0 w-0.5 bg-gradient-to-b from-brand-200 via-brand-400 to-green-400 md:transform md:-translate-x-1/2"></div>

                <div class="space-y-8">
                    @foreach($steps as $index => $step)
                        @php $isEven = $index % 2 == 0; @endphp
                        <div class="relative flex items-center justify-between md:justify-center">
                            <div class="hidden md:block w-5/12 text-end pr-8 {{ $isEven ? 'opacity-100' : 'opacity-0' }}">
                                @if($isEven)
                                <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100">
                                    <h4 class="font-bold text-gray-900">{{ $step->title }}</h4>
                                    <p class="text-xs text-gray-500 mt-1">{!! $step->description !!}</p>
                                </div>
                                @endif
                            </div>

                            <div class="absolute left-0 md:left-1/2 w-8 h-8 md:w-10 md:h-10 bg-brand-600 border-4 border-white rounded-full flex items-center justify-center text-white z-10 md:transform md:-translate-x-1/2 shadow-md">
                                <i class="{{ $step->icon }} text-xs md:text-sm"></i>
                            </div>

                            <div class="w-[calc(100%-2.5rem)] md:w-5/12 pl-4 md:pl-8 text-start {{ $isEven ? 'md:opacity-0' : 'md:opacity-100' }}">
                                <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 md:hidden"> <h4 class="font-bold text-gray-900">{{ $step->title }}</h4>
                                    <p class="text-xs text-gray-500 mt-1">{!! $step->description !!}</p>
                                </div>
                                <div class="hidden md:block bg-white p-4 rounded-2xl shadow-sm border border-gray-100"> @if(!$isEven)
                                    <h4 class="font-bold text-gray-900">{{ $step->title }}</h4>
                                    <p class="text-xs text-gray-500 mt-1">{!! $step->description !!}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="bg-gray-900 text-white py-10 rounded-2xl shadow-xl relative overflow-hidden" data-aos="fade-up">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center relative z-10 divide-x-0 md:divide-x divide-white/10 rtl:divide-x-reverse">
                @foreach($counters as $counter)
                <div>
                    <div class="text-2xl md:text-3xl font-black mb-1 text-brand-400">{{ $counter->count }}</div>
                    <div class="text-[10px] text-gray-400 uppercase tracking-widest">{{ $counter->title }}</div>
                </div>
                @endforeach
            </div>
        </div>

    </div>
    
    <style>
        .animate-float { animation: float 4s ease-in-out infinite; }
        @keyframes float { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-10px); } }
    </style>
</section>
@endif
