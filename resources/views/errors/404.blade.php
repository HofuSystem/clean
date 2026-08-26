<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 - الصفحة غير موجودة | Clean Station</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700&family=Tajawal:wght@300;400;500;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --brand-color: #0ea5e9;
            --brand-hover: #0284c7;
            --text-main: #1f2937;
            --text-muted: #6b7280;
            --bg-color: #f8fafc;
        }
        body {
            margin: 0;
            padding: 0;
            font-family: 'Tajawal', 'Cairo', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-main);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            text-align: center;
            direction: {{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }};
        }
        .container {
            max-width: 600px;
            padding: 40px 20px;
            background: transparent;
        }
        .logo-container {
            margin-bottom: 40px;
            display: flex;
            justify-content: center;
        }
        .logo {
            height: 95px;
            object-fit: contain;
            opacity: 0.95;
        }
        .error-code {
            font-size: 140px;
            font-weight: 300;
            line-height: 1;
            margin: 0;
            color: var(--brand-color);
            letter-spacing: -4px;
        }
        .error-title {
            font-size: 26px;
            font-weight: 700;
            margin: 24px 0 12px;
            color: var(--text-main);
        }
        .error-desc {
            font-size: 16px;
            line-height: 1.6;
            color: var(--text-muted);
            margin-bottom: 40px;
            max-width: 450px;
            margin-left: auto;
            margin-right: auto;
        }
        .actions {
            display: flex;
            gap: 16px;
            justify-content: center;
            flex-wrap: wrap;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 12px 32px;
            font-size: 15px;
            font-weight: 600;
            text-decoration: none;
            border-radius: 50px; /* Pill shape for a modern look */
            transition: all 0.3s ease;
            cursor: pointer;
            border: 2px solid transparent;
            font-family: inherit;
        }
        .btn-primary {
            background-color: var(--brand-color);
            color: #ffffff;
            box-shadow: 0 4px 14px rgba(14, 165, 233, 0.25);
        }
        .btn-primary:hover {
            background-color: var(--brand-hover);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(14, 165, 233, 0.4);
        }
        .btn-secondary {
            background-color: transparent;
            color: var(--text-muted);
            border-color: #e5e7eb;
        }
        .btn-secondary:hover {
            border-color: #d1d5db;
            color: var(--text-main);
            background-color: #ffffff;
            transform: translateY(-2px);
        }
        @media (max-width: 480px) {
            .error-code { font-size: 90px; letter-spacing: -2px; }
            .error-title { font-size: 22px; }
            .btn { width: 100%; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo-container">
            @if(config('app.logo'))
                <img src="{{ config('app.logo') }}" alt="Clean Station" class="logo">
            @else
                <h2 style="font-size: 24px; font-weight: 700; color: var(--brand-color); margin: 0;">Clean Station</h2>
            @endif
        </div>

        <h1 class="error-code">404</h1>
        
        <h3 class="error-title">عفواً، الصفحة غير موجودة</h3>
        
        <p class="error-desc">
            يبدو أن الصفحة التي تحاول الوصول إليها قد تم نقلها أو أنها غير متاحة حالياً. لا تقلق، دعنا نعود بك إلى بر الأمان.
        </p>

        <div class="actions">
            <a href="{{ url('/') }}" class="btn btn-primary">العودة للرئيسية</a>
            <button onclick="window.history.back()" class="btn btn-secondary">الرجوع للخلف</button>
        </div>
    </div>
</body>
</html>
