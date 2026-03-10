@php
    if (!isset($posts)) {
        $posts = \Core\Blog\Models\Blog::where('status', 'publish')->latest()->take(6)->get();
    }
@endphp
<section id="blogs" class="page-section bg-gray-50 py-20 md:py-32">
    <div class="max-w-7xl mx-auto px-4">

        <div class="flex flex-col md:flex-row justify-between items-end mb-16 gap-4 text-center md:text-start" data-aos="fade-up">
            <div class="w-full">
                <span class="text-brand-600 font-bold uppercase tracking-widest text-xs mb-2 block">{{ $section->title }}</span>
                <h2 class="text-3xl md:text-5xl font-black text-gray-900">{{ $section->title }}</h2>
            </div>
        </div>

        @if ($posts->count() > 0)
            <div class="grid lg:grid-cols-2 gap-8 mb-12" data-aos="fade-up">
                <a href="{{ route('blogs-single', $posts[0]->slug) }}" class="relative h-[300px] lg:h-[400px] rounded-3xl overflow-hidden group shadow-xl cursor-pointer block">
                    <div class="absolute inset-0 bg-black/40 group-hover:bg-black/20 transition-all z-10"></div>
                    <img src="{{ $posts[0]->image_url }}"
                        class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-700">
                    <div class="absolute bottom-0 left-0 p-8 z-20 w-full">
                        <span
                            class="bg-brand-600 text-white px-3 py-1 rounded-full text-xs font-bold mb-3 inline-block">{{ trans('featured') }}</span>
                        <h3 class="text-2xl md:text-4xl font-bold text-white mb-2 leading-tight drop-shadow-lg"><span
                                class="lang-ar">{{ $posts[0]->translate('ar')->title }}</span><span
                                class="lang-en">{{ $posts[0]->translate('en')->title }}</span></h3>
                        <div class="text-gray-200 text-sm flex items-center gap-4">
                            <span><i class="fa-regular fa-calendar"></i>
                                {{ $posts[0]->created_at->format('M d, Y') }}</span>
                        </div>
                    </div>
                </a>
                <div class="space-y-6">
                    @foreach ($posts->skip(1)->take(1) as $post)
                        <a href="{{ route('blogs-single', $post->slug) }}"
                            class="bg-white rounded-3xl p-6 flex flex-col md:flex-row gap-6 shadow-md hover:shadow-xl transition-all group cursor-pointer border border-gray-100 h-full block">
                            <div class="w-full md:w-1/3 h-48 md:h-full rounded-2xl overflow-hidden relative">
                                <img src="{{ $post->image_url }}"
                                    class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-500">
                            </div>
                            <div class="flex-1 flex flex-col justify-center">
                                <div class="text-xs text-brand-600 font-bold mb-2 uppercase tracking-wide">
                                    {{ trans('tips and tricks') }}
                                </div>
                                <h4
                                    class="text-xl font-bold text-gray-900 mb-3 group-hover:text-brand-600 transition-colors line-clamp-2">
                                    <span class="lang-ar">{{ $post->translate('ar')->title }}</span><span
                                        class="lang-en">{{ $post->translate('en')->title }}</span>
                                </h4>
                                <p class="text-gray-500 text-sm line-clamp-2 mb-4"><span
                                        class="lang-ar">{!! Str::limit(strip_tags($post->translate('ar')->content), 80) !!}</span><span
                                        class="lang-en">{!! Str::limit(strip_tags($post->translate('en')->content), 80) !!}</span></p>
                                <div class="text-xs text-gray-400 mt-auto">{{ $post->created_at->diffForHumans() }}
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
        <div class="grid md:grid-cols-3 gap-8">
            @foreach ($posts->skip(1) as $post)
                <a href="{{ route('blogs-single', $post->slug) }}" class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all group border border-gray-100 block"
                    data-aos="fade-up">
                    <div class="h-48 overflow-hidden relative">
                        <div
                            class="absolute top-4 right-4 bg-white/90 backdrop-blur px-2 py-1 rounded-lg text-xs font-bold z-10 shadow-sm">
                            <i class="fa-solid fa-bolt text-brand-500"></i> {{ trans('new') }}
                        </div>
                        <img src="{{ $post->image_url }}"
                            class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-500">
                    </div>
                    <div class="p-6">
                        <h4
                            class="font-bold text-lg text-gray-900 mb-2 line-clamp-2 group-hover:text-brand-600 transition-colors">
                            <span class="lang-ar">{{ $post->translate('ar')->title }}</span><span
                                class="lang-en">{{ $post->translate('en')->title }}</span>
                        </h4>
                        <div class="flex items-center justify-between mt-4 pt-4 border-t border-gray-50">
                            <span class="text-xs text-gray-400">{{ $post->created_at->format('M d') }}</span>
                            <span class="text-brand-600 text-xs font-bold cursor-pointer hover:underline"><span
                                    class="lang-ar">{{ trans('read more') }}</span><span
                                    class="lang-en">{{ trans('read more') }}</span></span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
        @if (isset($has_pagination) && $has_pagination)
            <div class="flex justify-center mt-10">
                {{ $posts->links('pagination::tailwind') }}
            </div>
        @endif
    </div>
</section>
