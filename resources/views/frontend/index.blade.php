<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'fiyera.co') }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    
    <!-- Scripts & Styles -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="h-full bg-white text-slate-900 antialiased selection:bg-slate-900 selection:text-white overflow-hidden">

    <div class="min-h-screen flex flex-col relative">

        <!-- Header -->
        <header class="w-full py-8 px-6 lg:px-12 flex items-center justify-center">
            <!-- Brand -->
            <a href="/" class="flex items-center gap-3 group">
                <div class="w-8 h-8 flex items-center justify-center bg-black rounded-xl">
                     <i class='bx bxs-bolt text-white text-xl'></i>
                </div>
                                <span class="font-extrabold text-2xl tracking-tight text-black">fiyera<span class="text-slate-300">.co</span></span>

            </a>

           
        </header>

        <!-- Main Content -->
        <main class="flex-1 flex flex-col items-center justify-center -mt-20 px-6 text-center max-w-4xl mx-auto">
            
            <div class="mb-8">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-widest border-b border-slate-200 pb-1">{{ __('Çok Yakında Yayındayız') }}</span>
            </div>

            <h1 class="text-5xl md:text-7xl font-bold text-slate-900 tracking-tight mb-8 leading-tight">
                İşinizi büyütmek için <br>
                <span class="text-slate-900">yeni bir dönem</span> başlıyor.
            </h1>

            <p class="text-lg md:text-xl text-slate-500 font-medium max-w-2xl mx-auto leading-relaxed mb-12">
                Fiyera, tekliflerinizi, müşterilerinizi ve iş süreçlerinizi tek bir yerden yönetebilmeniz için tasarlandı. Modern, hızlı ve tamamen size özel.
            </p>

            <!-- Feature Highlights (Mini) -->
            <div class="flex flex-wrap justify-center gap-8 text-sm font-semibold text-slate-600 mb-16">
                <div class="flex items-center gap-2">
                    <i class='bx bx-check text-slate-400 text-lg'></i>
                    <span>Sınırsız Teklif</span>
                </div>
                <div class="flex items-center gap-2">
                    <i class='bx bx-check text-slate-400 text-lg'></i>
                    <span>Müşteri Yönetimi</span>
                </div>
                <div class="flex items-center gap-2">
                    <i class='bx bx-check text-slate-400 text-lg'></i>
                    <span>Detaylı Raporlar</span>
                </div>
            </div>

             <!-- Auth Actions -->
            <div class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto mt-8">
                <a href="{{ route('login') }}" class="w-full sm:w-auto h-12 px-8 flex items-center justify-center rounded-xl text-sm font-bold text-slate-900 border-2 border-slate-100 hover:border-slate-900 hover:bg-white transition-all duration-300">
                    {{ __('Giriş Yap') }}
                </a>
                <a href="{{ route('register') }}" class="w-full sm:w-auto h-12 px-8 flex items-center justify-center rounded-xl bg-slate-900 text-white text-sm font-bold shadow-xl shadow-slate-900/10 hover:shadow-slate-900/20 hover:-translate-y-0.5 transition-all duration-300">
                    {{ __('Fiyera\'ya Katıl') }}
                </a>
            </div>
        </main>

         <div class="w-full py-6 flex flex-col items-center gap-4 z-50  md:fixed md:bottom-0 md:left-0">
             <!-- Language Switcher -->
             <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" class="flex items-center gap-2 bg-white px-3 py-1.5 rounded-full border border-slate-200 shadow-sm hover:shadow text-xs font-bold text-slate-600 transition-all">
                    @if(app()->getLocale() == 'tr')
                        <img src="https://flagcdn.com/w20/tr.png" class="w-4 rounded-sm" alt="Türkçe">
                        <span>Türkçe</span>
                    @else
                        <img src="https://flagcdn.com/w20/us.png" class="w-4 rounded-sm" alt="English">
                        <span>English</span>
                    @endif
                    <i class='bx bx-chevron-down text-lg text-slate-400'></i>
                </button>
                <div x-show="open" 
                     @click.away="open = false"
                     x-transition.origin.bottom.center
                     class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 w-32 bg-white rounded-lg shadow-xl border border-slate-100 overflow-hidden py-1 z-50">
                    <a href="{{ route('locale.switch', 'tr') }}" class="flex items-center gap-3 px-4 py-2.5 text-xs font-medium hover:bg-slate-50 transition-colors {{ app()->getLocale() == 'tr' ? 'text-indigo-600 bg-indigo-50' : 'text-slate-700' }}">
                        <img src="https://flagcdn.com/w20/tr.png" class="w-3.5 rounded-sm" alt="Türkçe">
                        Türkçe
                    </a>
                    <a href="{{ route('locale.switch', 'en') }}" class="flex items-center gap-3 px-4 py-2.5 text-xs font-medium hover:bg-slate-50 transition-colors {{ app()->getLocale() == 'en' ? 'text-indigo-600 bg-indigo-50' : 'text-slate-700' }}">
                        <img src="https://flagcdn.com/w20/us.png" class="w-3.5 rounded-sm" alt="English">
                        English
                    </a>
                </div>
            </div>

            <div class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">
                &copy; {{ date('Y') }} Fiyera.co
            </div>
        </div>

    </div>

</body>
</html>
