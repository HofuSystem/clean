@php
    $b2bSectors = \Core\Pages\Models\Feature::where('section', 'b2b')->get();
@endphp
<section id="b2b" class="page-section bg-gray-900 text-white py-20 md:py-32 relative overflow-hidden">
    
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none z-0">
        <div class="absolute top-[-10%] right-[-10%] w-[600px] h-[600px] bg-brand-900/30 rounded-full blur-[120px]"></div>
        <div class="absolute bottom-[-10%] left-[-10%] w-[600px] h-[600px] bg-accent-600/10 rounded-full blur-[120px]"></div>
        <div class="absolute inset-0 opacity-[0.03]" style="background-image: linear-gradient(#ffffff 1px, transparent 1px), linear-gradient(90deg, #ffffff 1px, transparent 1px); background-size: 40px 40px;"></div>
    </div>

    <div class="max-w-7xl mx-auto px-4 relative z-10">
        
        <div class="text-center mb-20" data-aos="fade-up">
            <span class="inline-block py-1 px-3 rounded-full bg-brand-500/10 border border-brand-500/20 text-brand-400 text-xs font-bold tracking-widest uppercase mb-4">{{ $section->small_title }}</span>
            <h2 class="text-3xl md:text-5xl lg:text-6xl font-black mb-6 leading-tight">{{ $section->title }}</h2>
            <p class="text-gray-400 max-w-3xl mx-auto text-lg md:text-xl leading-relaxed">{!! $section->description !!}</p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-8 mb-32">
            @foreach($b2bSectors as $sector)
            <div class="group relative bg-white/5 backdrop-blur-md p-6 rounded-2xl border border-white/10 hover:border-brand-500/50 hover:bg-white/10 transition-all duration-300 text-center cursor-default">
                <div class="absolute inset-0 bg-gradient-to-b from-brand-500/10 to-transparent opacity-0 group-hover:opacity-100 rounded-2xl transition-opacity"></div>
                <div class="relative z-10">
                    <i class="{{ $sector->icon }} text-3xl md:text-5xl text-gray-400 group-hover:text-brand-400 mb-4 transition-colors block transform group-hover:scale-110 duration-300"></i>
                    <h4 class="font-bold text-lg text-white mb-2"><span class="lang-ar">{{ $sector->translate('ar')->title }}</span><span class="lang-en">{{ $sector->translate('en')->title }}</span></h4>
                    <p class="text-xs text-gray-500 group-hover:text-gray-300"><span class="lang-ar">{{ $sector->translate('ar')->description }}</span><span class="lang-en">{{ $sector->translate('en')->description }}</span></p>
                </div>
            </div>
            @endforeach
        </div>

        <div class="flex flex-col lg:flex-row items-center gap-16 mb-32">
            <div class="lg:w-1/2 space-y-8" data-aos="fade-right">
                <div>
                    <h3 class="text-3xl md:text-4xl font-bold mb-4 text-white">{{ trans('Integrated Laundry Management System') }}</h3>
                    <p class="text-gray-400 text-lg leading-relaxed">{{ trans('Comprehensive technical solution. Real-time tracking, high financial accuracy, multi-branch management, and elimination of operational waste.') }}</p>
                </div>
                <div class="space-y-6">
                    <div class="flex gap-4 group">
                        <div class="w-12 h-12 rounded-xl bg-brand-900/50 flex items-center justify-center text-brand-400 text-xl border border-brand-500/20 group-hover:border-brand-500 transition-colors shrink-0"><i class="fa-solid fa-network-wired"></i></div>
                        <div>
                            <h4 class="font-bold text-white text-lg">{{ trans('Centralized Branch Management') }}</h4>
                            <p class="text-sm text-gray-500">{{ trans('Control branches, staff, and pricing from one screen.') }}</p>
                        </div>
                    </div>
                    <div class="flex gap-4 group">
                        <div class="w-12 h-12 rounded-xl bg-green-900/50 flex items-center justify-center text-green-400 text-xl border border-green-500/20 group-hover:border-green-500 transition-colors shrink-0"><i class="fa-solid fa-chart-pie"></i></div>
                        <div>
                            <h4 class="font-bold text-white text-lg">{{ trans('Financial Accuracy & Waste Control') }}</h4>
                            <p class="text-sm text-gray-500">{{ trans('Detailed reports detecting waste with 100% accurate invoices.') }}</p>
                        </div>
                    </div>
                </div>
                <div class="pt-4"><a href="#b2b-form" class="inline-flex items-center gap-2 text-brand-400 font-bold hover:text-brand-300 transition-colors border-b border-brand-400/30 pb-1 hover:border-brand-300">{{ trans('Request System Demo') }} <i class="fa-solid fa-arrow-left rtl:rotate-0 ltr:rotate-180"></i></a></div>
            </div>
            
            <div class="lg:w-1/2 w-full" data-aos="fade-left">
                <div class="relative rounded-2xl bg-gray-800 border border-gray-700 shadow-2xl p-2 transform md:rotate-2 hover:rotate-0 transition-all duration-700 ease-out">
                    <div class="h-8 bg-gray-900 rounded-t-lg border-b border-gray-700 flex items-center px-4 gap-2">
                        <div class="w-3 h-3 rounded-full bg-red-500"></div><div class="w-3 h-3 rounded-full bg-yellow-500"></div><div class="w-3 h-3 rounded-full bg-green-500"></div>
                    </div>
                    <div class="bg-gray-900 p-6 rounded-b-lg">
                        <div class="flex gap-6">
                            <div class="hidden md:block w-12 space-y-4">
                                <div class="w-8 h-8 rounded bg-brand-600"></div>
                                <div class="w-8 h-8 rounded bg-gray-800 border border-gray-700"></div>
                            </div>
                            <div class="flex-1 space-y-4">
                                <div class="grid grid-cols-3 gap-3">
                                    <div class="bg-gray-800 p-3 rounded-lg border border-gray-700"><div class="h-2 w-12 bg-gray-600 rounded mb-1"></div><div class="h-4 w-8 bg-white rounded"></div></div>
                                    <div class="bg-gray-800 p-3 rounded-lg border border-gray-700"><div class="h-2 w-12 bg-gray-600 rounded mb-1"></div><div class="h-4 w-16 bg-white rounded"></div></div>
                                    <div class="bg-gray-800 p-3 rounded-lg border border-gray-700"><div class="h-2 w-12 bg-gray-600 rounded mb-1"></div><div class="h-4 w-10 bg-white rounded"></div></div>
                                </div>
                                <div class="bg-gray-800 rounded-lg border border-gray-700 p-4 space-y-3">
                                    <div class="flex justify-between items-center pb-2 border-b border-gray-700"><div class="h-3 w-20 bg-gray-600 rounded"></div></div>
                                    <div class="flex justify-between items-center"><div class="h-2 w-24 bg-gray-700 rounded"></div><div class="h-2 w-8 bg-green-500/50 rounded"></div></div>
                                    <div class="flex justify-between items-center"><div class="h-2 w-32 bg-gray-700 rounded"></div><div class="h-2 w-8 bg-yellow-500/50 rounded"></div></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="relative bg-gradient-to-br from-purple-900/50 to-indigo-900/50 rounded-3xl p-8 md:p-12 mb-32 border border-purple-500/30 overflow-hidden" data-aos="zoom-in">
            <div class="absolute top-0 right-0 w-[400px] h-[400px] bg-purple-500/20 rounded-full blur-[100px] -mr-20 -mt-20"></div>
            
            <div class="relative z-10 flex flex-col lg:flex-row items-center gap-12">
                <div class="lg:w-3/5 text-center lg:text-start">
                    <div class="inline-block bg-purple-500/20 text-purple-300 px-3 py-1 rounded-full text-xs font-bold mb-4 border border-purple-500/30">Short-term Rental Hosts</div>
                    <h3 class="text-3xl md:text-4xl font-bold mb-4 text-white">{{ trans('Short-term Rental Hosts') }}</h3>
                    <p class="text-gray-300 text-lg mb-8 leading-relaxed">{{ trans('Boost your ratings with premium laundry service. Offer exclusive discounts to guests and earn commission on every order.') }}</p>
                    
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div class="bg-black/20 p-4 rounded-xl border border-purple-500/20">
                            <div class="text-purple-400 font-bold text-xl mb-1"><i class="fa-solid fa-star"></i> 5.0</div>
                            <div class="text-sm text-gray-400">{{ trans('Boost Ratings') }}</div>
                        </div>
                        <div class="bg-black/20 p-4 rounded-xl border border-green-500/20">
                            <div class="text-green-400 font-bold text-xl mb-1"><i class="fa-solid fa-money-bill-wave"></i> $$$</div>
                            <div class="text-sm text-gray-400">{{ trans('Earn Commission') }}</div>
                        </div>
                    </div>
                </div>
                
                <div class="lg:w-2/5 flex flex-col items-center">
                    <div class="bg-white p-6 rounded-2xl shadow-2xl transform rotate-3 hover:rotate-0 transition-all duration-300 w-full max-w-xs text-center">
                        <i class="fa-solid fa-house-chimney-user text-6xl text-purple-600 mb-4"></i>
                        <div class="text-gray-900 font-bold text-lg mb-2">{{ trans('Super Host Partner') }}</div>
                        <div class="bg-purple-50 text-purple-700 text-sm py-2 rounded-lg font-mono">CODE: HOST2025</div>
                    </div>
                    <p class="mt-4 text-xs text-purple-300">{{ trans('Works with Gathern, Airbnb, Booking') }}</p>
                </div>
            </div>
        </div>

        <div class="flex flex-col lg:flex-row-reverse items-center gap-16 mb-32">
            <div class="lg:w-1/2 space-y-6" data-aos="fade-left">
                <div>
                    <h3 class="text-3xl md:text-4xl font-bold mb-4 text-white">{{ trans('100% Digital Guest Exp') }}</h3>
                    <p class="text-gray-400 text-lg leading-relaxed">{{ trans('Scan, order, and charge to room or pay online.') }}</p>
                </div>
                
                <div class="bg-white/5 border border-white/10 rounded-2xl p-6 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-20 h-20 bg-brand-500/20 rounded-full blur-2xl"></div>
                    <div class="relative z-10 flex gap-4 items-start">
                        <i class="fa-solid fa-mobile-screen-button text-4xl text-brand-400 mt-1"></i>
                        <div>
                            <h4 class="font-bold text-white text-lg mb-1">{{ trans('100% Digital Guest Exp') }}</h4>
                            <p class="text-sm text-gray-400">{{ trans('Scan, order, and charge to room or pay online.') }}</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="lg:w-1/2 flex justify-center" data-aos="fade-right">
                <div class="relative w-64 h-64 md:w-80 md:h-80 bg-white rounded-[2rem] p-6 shadow-[0_0_60px_-15px_rgba(14,165,233,0.3)] flex items-center justify-center transform hover:scale-105 transition-transform duration-500">
                    <i class="fa-solid fa-qrcode text-[180px] md:text-[220px] text-gray-900"></i>
                </div>
            </div>
        </div>

        <div id="b2b-form" class="max-w-4xl mx-auto bg-white rounded-3xl overflow-hidden shadow-2xl relative">
            <div class="h-2 w-full bg-gradient-to-r from-brand-500 to-accent-500"></div>
            <div class="p-8 md:p-12 text-gray-900">
                <div class="text-center mb-10">
                    <h3 class="text-3xl font-black mb-2">{{ trans('register your data') }}</h3>
                    <p class="text-gray-500">{{ trans('Register now for a system demo.') }}</p>
                </div>

                <form action="{{ route('client.register.store') }}" method="POST" class="space-y-6">
                    @csrf

                    <div class="grid md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">{{ trans('Facility Name') }}</label>
                            <input type="text" name="company_name" value="{{ old('company_name') }}" placeholder="اسم المنشأة" class="w-full bg-gray-50 border @error('company_name') border-red-500 @else border-gray-200 @enderror p-4 rounded-xl focus:bg-white focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 transition-all outline-none font-bold" required>
                            @error('company_name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">{{ trans('Contact Person') }}</label>
                            <input type="text" name="contact_person" value="{{ old('contact_person') }}" placeholder="اسم المسؤول" class="w-full bg-gray-50 border @error('contact_person') border-red-500 @else border-gray-200 @enderror p-4 rounded-xl focus:bg-white focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 transition-all outline-none font-bold" required>
                            @error('contact_person')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="grid md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">{{ trans('Mobile Number') }}</label>
                            <input type="tel" name="phone" value="{{ old('phone') }}" placeholder="05xxxxxxxx" class="w-full bg-gray-50 border @error('phone') border-red-500 @else border-gray-200 @enderror p-4 rounded-xl focus:bg-white focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 transition-all outline-none font-bold text-end" dir="ltr" required>
                            @error('phone')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">{{ trans('Business Type') }}</label>
                            <select name="type" class="w-full bg-gray-50 border @error('type') border-red-500 @else border-gray-200 @enderror p-4 rounded-xl focus:bg-white focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 transition-all outline-none font-bold">
                                <option value="Hotel" {{ old('type') == 'Hotel' ? 'selected' : '' }}>Hotel / فندق</option>
                                <option value="Apartments" {{ old('type') == 'Apartments' ? 'selected' : '' }}>Apartments / شقق مخدومة</option>
                                <option value="Host" {{ old('type') == 'Host' ? 'selected' : '' }}>Host / مضيف (جاذر إن)</option>
                                <option value="Medical" {{ old('type') == 'Medical' ? 'selected' : '' }}>Medical / مركز طبي</option>
                                <option value="Salon" {{ old('type') == 'Salon' ? 'selected' : '' }}>Salon / صالون</option>
                                <option value="Gym" {{ old('type') == 'Gym' ? 'selected' : '' }}>Gym / نادي رياضي</option>
                            </select>
                            @error('type')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">{{ trans('Est. Monthly Items') }}</label>
                        <select name="monthly_items" class="w-full bg-gray-50 border @error('monthly_items') border-red-500 @else border-gray-200 @enderror p-4 rounded-xl focus:bg-white focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 transition-all outline-none font-bold">
                            <option value="<1000" {{ old('monthly_items') == '<1000' ? 'selected' : '' }}>أقل من 1000 قطعة</option>
                            <option value="1000-5000" {{ old('monthly_items') == '1000-5000' ? 'selected' : '' }}>1000 - 5000 قطعة</option>
                            <option value="5000-10000" {{ old('monthly_items') == '5000-10000' ? 'selected' : '' }}>5000 - 10000 قطعة</option>
                            <option value=">10000" {{ old('monthly_items') == '>10000' ? 'selected' : '' }}>أكثر من 10000 قطعة (Enterprise)</option>
                        </select>
                        @error('monthly_items')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="w-full bg-brand-900 text-white font-black text-lg py-5 rounded-xl hover:bg-brand-800 hover:shadow-lg hover:-translate-y-1 transition-all duration-300 shadow-xl shadow-brand-900/20">
                        {{ trans('Submit Partnership Request') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>
