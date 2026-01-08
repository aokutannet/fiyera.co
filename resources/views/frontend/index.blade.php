<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Çok Yakında - {{ config('app.name', 'fiyera.co') }}</title>
    
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
<body class="h-full bg-slate-50 text-slate-900 antialiased selection:bg-indigo-500 selection:text-white overflow-hidden">

    <div class="min-h-screen flex flex-col relative">
        
        <!-- Decorative Background Elements -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-[10%] -left-[10%] w-[40%] h-[40%] bg-indigo-200/20 rounded-full blur-3xl"></div>
            <div class="absolute top-[20%] right-[10%] w-[30%] h-[30%] bg-emerald-100/30 rounded-full blur-3xl"></div>
            <div class="absolute bottom-[10%] left-[20%] w-[25%] h-[25%] bg-sky-100/30 rounded-full blur-3xl"></div>
        </div>

        <!-- Header -->
        <header class="w-full py-6 px-6 lg:px-12 flex items-center justify-between relative z-50">
            <!-- Brand -->
            <a href="/" class="flex items-center gap-2 group">
                <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-indigo-600/20 group-hover:scale-110 transition-transform duration-300">
                    <i class='bx bx-layer text-2xl'></i>
                </div>
                <span class="text-xl font-extrabold tracking-tight text-slate-900">fiyera<span class="text-indigo-600">.co</span></span>
            </a>

            <!-- Auth Actions -->
            <div class="flex items-center gap-3">
                <a href="{{ route('login') }}" class="hidden sm:flex h-10 px-5 items-center justify-center rounded-xl text-sm font-bold text-slate-600 hover:text-indigo-600 hover:bg-white hover:shadow-sm border border-transparent hover:border-slate-100 transition-all">
                    {{ __('Giriş Yap') }}
                </a>
                <a href="{{ route('register') }}" class="h-10 px-5 flex items-center justify-center rounded-xl bg-indigo-600 text-white text-sm font-bold shadow-lg shadow-indigo-600/30 hover:shadow-indigo-600/50 hover:-translate-y-0.5 transition-all">
                    {{ __('Fiyera\'ya Katıl') }}
                </a>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 flex flex-col items-center justify-center -mt-20 px-6 text-center relative z-10">
            
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white border border-slate-200 shadow-sm mb-8 animate-[bounce_3s_infinite]">
                <span class="relative flex h-3 w-3">
                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                  <span class="relative inline-flex rounded-full h-3 w-3 bg-indigo-500"></span>
                </span>
                <span class="text-xs font-bold text-slate-600 tracking-wide uppercase">{{ __('Çok Yakında Yayındayız') }}</span>
            </div>

            <h1 class="text-5xl md:text-7xl font-black text-slate-900 tracking-tight mb-6 leading-tight max-w-4xl">
                İşinizi büyütmek için <br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-violet-600">yeni bir dönem</span> başlıyor.
            </h1>

            <p class="text-lg md:text-xl text-slate-500 font-medium max-w-2xl mx-auto leading-relaxed mb-10">
                Fiyera, tekliflerinizi, müşterilerinizi ve iş süreçlerinizi tek bir yerden yönetebilmeniz için tasarlandı. Modern, hızlı ve tamamen size özel.
            </p>

            <!-- Feature Highlights (Mini) -->
            <div class="flex flex-wrap justify-center gap-4 text-sm font-bold text-slate-600 mb-12">
                <div class="flex items-center gap-2 bg-white px-4 py-2.5 rounded-xl border border-slate-100 shadow-sm">
                    <i class='bx bx-check-circle text-emerald-500 text-lg'></i>
                    <span>Sınırsız Teklif</span>
                </div>
                <div class="flex items-center gap-2 bg-white px-4 py-2.5 rounded-xl border border-slate-100 shadow-sm">
                    <i class='bx bx-check-circle text-emerald-500 text-lg'></i>
                    <span>Müşteri Yönetimi</span>
                </div>
                <div class="flex items-center gap-2 bg-white px-4 py-2.5 rounded-xl border border-slate-100 shadow-sm">
                    <i class='bx bx-check-circle text-emerald-500 text-lg'></i>
                    <span>Detaylı Raporlar</span>
                </div>
            </div>

            <!-- Notify Form (Optional Placeholder) -->
            <div class="w-full max-w-md relative group">
                <div class="absolute -inset-1 bg-gradient-to-r from-indigo-500 to-violet-500 rounded-2xl blur opacity-20 group-hover:opacity-40 transition duration-1000 group-hover:duration-200"></div>
                <div class="relative flex p-2 bg-white rounded-2xl border border-slate-100 shadow-xl">
                    <input type="email" placeholder="E-posta adresinizi bırakın..." class="flex-1 px-4 py-3 bg-transparent text-slate-900 placeholder:text-slate-400 font-medium focus:outline-none">
                    <button class="px-6 py-3 bg-slate-900 text-white rounded-xl font-bold hover:bg-indigo-600 transition-colors">
                        {{ __('Haberdar Et') }}
                    </button>
                </div>
                <p class="text-xs text-slate-400 font-medium mt-3">{{ __('Spam yok. Sadece önemli güncellemeler.') }}</p>
            </div>

        </main>

        <!-- Footer -->
        <footer class="py-8 text-center relative z-10">
            <p class="text-sm font-semibold text-slate-400">
                &copy; {{ date('Y') }} fiyera.co &bull; {{ __('Tüm hakları saklıdır.') }}
            </p>
        </footer>

    </div>

</body>
</html>
