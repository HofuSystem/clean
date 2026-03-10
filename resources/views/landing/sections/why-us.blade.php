@php
    $comparisons = \Core\Pages\Models\Comparison::all();
@endphp
<section id="why-us" class="page-section bg-white py-16 md:py-24">
    <div class="max-w-7xl mx-auto px-4">
        
        <div class="text-center mb-16" data-aos="fade-up">
            <span class="text-brand-600 font-bold uppercase tracking-widest text-xs mb-2 block"><span class="lang-ar">{{ $section->translate('ar')->small_title }}</span><span class="lang-en">{{ $section->translate('en')->small_title }}</span></span>
            <h2 class="text-3xl md:text-5xl font-black text-gray-900 mb-6"><span class="lang-ar">{{ $section->translate('ar')->title }}</span><span class="lang-en">{{ $section->translate('en')->title }}</span></h2>
            <p class="text-gray-500 max-w-2xl mx-auto"><span class="lang-ar">{!! $section->translate('ar')->description !!}</span><span class="lang-en">{!! $section->translate('en')->description !!}</span></p>
        </div>

        <div class="mt-16 bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden" data-aos="zoom-in">
            <div class="bg-brand-900 text-white p-6 text-center">
                <h3 class="text-xl font-bold">{{__('we provide amazing services and quality')}}</h3>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full min-w-[600px]"> 
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="py-4 px-6 text-start text-sm text-gray-500 w-1/3"><span class="lang-ar">{{__('feature')}}</span><span class="lang-en">{{__('feature')}}</span></th>
                            <th class="py-4 px-6 text-center text-lg font-bold text-brand-600 w-1/3 bg-brand-50/50"><span class="lang-ar">{{__('clean station')}}</span><span class="lang-en">{{__('clean station')}}</span></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($comparisons as $comp)
                        <tr class="hover:bg-gray-50 transition-colors group">
                            <td class="py-4 px-6 font-bold text-gray-700"><span class="lang-ar">{{ $comp->translate('ar')->point }}</span><span class="lang-en">{{ $comp->translate('en')->point }}</span></td>
                            <td class="py-4 px-6 text-center text-brand-700 font-bold bg-brand-50/30 group-hover:bg-brand-100/50 transition-colors">
                                <i class="fa-solid fa-check-circle text-green-500 ml-1"></i> <span class="lang-ar">{{ $comp->translate('ar')->us_text }}</span><span class="lang-en">{{ $comp->translate('en')->us_text }}</span>
                            </td>
                          
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-4 text-center text-xs text-gray-400 bg-gray-50 md:hidden">
                <i class="fa-solid fa-arrows-left-right"></i> <span class="lang-ar">{{__('drag the table right and left to view')}}</span><span class="lang-en">{{__('drag the table right and left to view')}}</span>
            </div>
        </div>

    </div>
</section>
