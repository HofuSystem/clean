@php
        $faqs = \Core\Pages\Models\Faq::all();
@endphp
<section id="faq-section" class="page-section bg-gray-50 py-16 md:py-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-3 gap-12">
            <div class="lg:col-span-1" data-aos="fade-left">
                <span class="text-brand-600 font-bold uppercase tracking-widest text-xs mb-2 block">{{ $section->small_title }}</span>
                <h2 class="text-4xl font-black text-gray-900 mb-6">{{ $section->title }}</h2>
                <p class="text-gray-500 mb-8 leading-relaxed">{!! $section->description !!}</p>
            </div>
            <div class="lg:col-span-2 space-y-4" data-aos="fade-right">
                @foreach($faqs as $faq)
                <details class="bg-white p-6 rounded-xl border border-gray-100 group">
                    <summary class="font-bold cursor-pointer flex justify-between items-center">
                        <span><i class="fa-solid fa-circle-question text-brand-300 ml-3"></i> {{ $faq->question }}</span>
                        <span class="transition group-open:rotate-180"><i class="fa-solid fa-chevron-down"></i></span>
                    </summary>
                    <p class="mt-4 text-gray-600 leading-relaxed">{{ $faq->answer }}</p>
                </details>
                @endforeach
            </div>
        </div>
    </div>
</section>
