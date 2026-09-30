@php
    $comparisons = \Core\Pages\Models\Comparison::with('translations')->get();
@endphp
<section id="why-us" class="page-section bg-white py-16 md:py-24">
    <div class="max-w-7xl mx-auto px-4">
        
        <div class="text-center mb-16" data-aos="fade-up">
            <span class="text-brand-600 font-bold uppercase tracking-widest text-xs mb-2 block">{{ $section->small_title }}</span>
            <h2 class="text-3xl md:text-5xl font-black text-gray-900 mb-6">{{ $section->title }}</h2>
            <p class="text-gray-500 max-w-2xl mx-auto">{!! $section->description !!}</p>
        </div>

        <div class="mt-16 bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden" data-aos="zoom-in">
            <div class="bg-brand-900 text-white p-6 text-center">
                <h3 class="text-xl font-bold">{{__('we provide amazing services and quality')}}</h3>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full min-w-[600px]"> 
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="py-4 px-6 text-start text-sm text-gray-500 w-1/3">{{__('feature')}}</th>
                            <th class="py-4 px-6 text-center text-lg font-bold text-brand-600 w-1/3 bg-brand-50/50">{{__('clean station')}}</th>
                            <th class="py-4 px-6 text-center text-base font-bold text-gray-500 w-1/3 bg-gray-100/40">{{__('traditional laundry')}}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($comparisons as $comp)
                        <tr class="hover:bg-slate-50/80 transition-colors group">
                            <td class="py-4 px-6 font-bold text-slate-800 text-sm sm:text-base">{{ $comp->point }}</td>
                            <td class="py-4 px-6 bg-sky-50/40 group-hover:bg-sky-50/70 transition-colors">
                                <div class="flex items-center justify-center gap-2 text-sky-800 font-bold text-xs sm:text-sm">
                                    <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-emerald-100 text-emerald-600 shrink-0 shadow-xs">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    </span>
                                    <span>{{ $comp->us_text }}</span>
                                </div>
                            </td>
                            <td class="py-4 px-6 bg-slate-50/30 group-hover:bg-slate-100/50 transition-colors">
                                <div class="flex items-center justify-center gap-2 text-slate-600 font-medium text-xs sm:text-sm">
                                    <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-rose-100 text-rose-600 shrink-0 shadow-xs">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </span>
                                    <span>{{ $comp->them_text }}</span>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-3 text-center text-xs text-slate-400 bg-slate-50 md:hidden flex items-center justify-center gap-1.5">
                <svg class="w-4 h-4 shrink-0 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                <span>{{__('drag the table right and left to view')}}</span>
            </div>
        </div>

    </div>
</section>
