<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 - الصفحة غير موجودة | Clean Station</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body {
            font-family: 'Tajawal', 'Cairo', sans-serif;
        }
        @keyframes blob {
            0% { transform: translate(0px, 0px) scale(1); }
            33% { transform: translate(30px, -50px) scale(1.1); }
            66% { transform: translate(-20px, 20px) scale(0.9); }
            100% { transform: translate(0px, 0px) scale(1); }
        }
        .animate-blob {
            animation: blob 7s infinite;
        }
        .animation-delay-2000 {
            animation-delay: 2s;
        }
        .animation-delay-4000 {
            animation-delay: 4s;
        }
    </style>
</head>
<body class="antialiased bg-brand-50 min-h-screen flex items-center justify-center relative overflow-hidden">
    
    <!-- Decorative background elements -->
    <div class="absolute top-[-10%] left-[-10%] w-96 h-96 bg-brand-200 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-blob"></div>
    <div class="absolute top-[-10%] right-[-10%] w-96 h-96 bg-accent-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-2000"></div>
    <div class="absolute bottom-[-20%] left-[20%] w-96 h-96 bg-brand-500 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-4000"></div>

    <div class="relative z-10 text-center px-4 md:px-0 w-full max-w-4xl mx-auto">
        
        <!-- Logo -->
        <div class="mb-12 flex justify-center">
            @if(config('app.logo'))
                <img src="{{ config('app.logo') }}" alt="Clean Station Logo" class="h-20 md:h-24 object-contain drop-shadow-md">
            @else
                <h2 class="text-4xl font-extrabold text-brand-800 tracking-wider">Clean Station</h2>
            @endif
        </div>

        <!-- 404 Text -->
        <div class="relative inline-block">
            <h1 class="text-9xl md:text-[14rem] font-black tracking-tighter mb-2 text-transparent bg-clip-text bg-gradient-to-br from-brand-600 to-brand-400 drop-shadow-2xl select-none" style="line-height: 1;">
                404
            </h1>
            <div class="absolute -bottom-4 left-1/2 transform -translate-x-1/2 w-full h-4 bg-black opacity-10 blur-md rounded-[100%]"></div>
        </div>
        
        <!-- Messages -->
        <div class="space-y-5 mb-12 mt-8">
            <h3 class="text-3xl md:text-5xl font-extrabold text-dark-900 drop-shadow-sm">
                عفواً! يبدو أن هذه المحطة غير موجودة.
            </h3>
            <p class="text-lg md:text-xl text-gray-600 max-w-2xl mx-auto leading-relaxed">
                الصفحة التي تبحث عنها ربما تم إزالتها، أو تغير اسمها، أو غير متاحة مؤقتاً. دعنا نعود بك إلى المحطة الرئيسية.
            </p>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4 sm:gap-6">
            <a href="{{ url('/') }}" class="group relative inline-flex items-center justify-center w-full sm:w-auto px-10 py-4 text-lg font-bold text-white transition-all duration-300 bg-brand-600 border border-transparent rounded-2xl hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-600 shadow-[0_8px_30px_rgb(14,165,233,0.3)] hover:shadow-[0_8px_30px_rgb(14,165,233,0.5)] transform hover:-translate-y-1">
                <span>العودة للرئيسية</span>
                <svg class="w-5 h-5 ml-2 mr-3 transition-transform duration-300 group-hover:translate-x-1 rtl:group-hover:-translate-x-1 rtl:rotate-180" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
            </a>
            
            <button onclick="window.history.back()" class="inline-flex items-center justify-center w-full sm:w-auto px-10 py-4 text-lg font-bold text-brand-700 transition-all duration-300 bg-white border border-brand-200 rounded-2xl hover:bg-brand-50 hover:border-brand-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-200 shadow-sm transform hover:-translate-y-1">
                الرجوع للخلف
            </button>
        </div>
    </div>
</body>
</html>
