@extends('b2b::web.layouts.app')

@section('content')
<div id="view-coming_soon" class="view-section active flex items-center justify-center"
    style="min-height: calc(100vh - 200px);">
    <div class="text-center max-w-lg mx-auto px-6">
        <!-- Animated Icon -->
        <div class="relative w-28 h-28 mx-auto mb-8">
            <div class="absolute inset-0 bg-blue-100 rounded-full animate-ping opacity-20"></div>
            <div
                class="relative w-28 h-28 bg-gradient-to-br from-[#1c75bc] to-[#155a91] rounded-full flex items-center justify-center shadow-xl shadow-blue-500/20">
                <svg class="w-14 h-14 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z">
                    </path>
                </svg>
            </div>
        </div>

        <!-- Arabic Content -->
        <h1 class="text-3xl md:text-4xl font-black text-gray-900 mb-4 tracking-tight leading-tight">
            <span class="bg-gradient-to-l from-[#1c75bc] to-[#155a91] bg-clip-text text-transparent"
                data-i18n="coming_soon_title">ميزات جديدة قادمة!</span>
        </h1>
        <p class="text-gray-500 font-bold text-base md:text-lg leading-relaxed mb-10 max-w-md mx-auto"
            data-i18n="coming_soon_desc">
            نحن نطور نظام النقاط والدعم الفني لخدمتكم بشكل أفضل.. انتظرونا قريباً..
        </p>

        <!-- Progress Indicator -->
        <div class="max-w-xs mx-auto mb-10">
            <div class="flex justify-between items-center mb-2">
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest"
                    data-i18n="development_progress">نسبة التطوير</span>
                <span class="text-xs font-black text-[#1c75bc]">65%</span>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden">
                <div class="bg-gradient-to-r from-[#1c75bc] to-[#4da3e0] h-2.5 rounded-full transition-all duration-1000"
                    style="width: 65%;"></div>
            </div>
        </div>

        <!-- CTA -->
        <a href="/"
            class="inline-flex items-center gap-2 bg-gray-900 text-white px-8 py-4 rounded-2xl font-black shadow-lg hover:bg-black transition-transform hover:-translate-y-0.5 text-sm">
            <svg class="w-4 h-4 rtl-rotate" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18">
                </path>
            </svg>
            <span data-i18n="back_to_dashboard">العودة للوحة التحكم</span>
        </a>
    </div>
</div>
@endsection