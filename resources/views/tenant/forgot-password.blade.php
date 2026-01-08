<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ __('Şifremi Unuttum') }} - {{ config('app.name', 'fiyera.co') }}</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
        <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
        <script src="https://cdn.tailwindcss.com"></script>
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
        <style>
            body { 
                font-family: 'Plus Jakarta Sans', sans-serif;
                background-color: #fafafa;
                letter-spacing: -0.01em; 
            }
        </style>
    </head>
    <body class="antialiased text-slate-950 flex flex-col min-h-screen">
        <div class="flex-1 flex flex-col items-center justify-center px-6 py-12">
            <div class="w-full max-w-[400px]">
                <!-- Logo -->
                <div class="mb-10 text-center">
                    <div class="flex items-center justify-center gap-2.5 mb-6">
                        <div class="w-10 h-10 bg-slate-950 rounded-xl flex items-center justify-center shadow-lg shadow-slate-200">
                            <i class='bx bxs-bolt text-white text-2xl'></i>
                        </div>
                        <span class="text-2xl font-extrabold tracking-tight text-slate-950">fiyera<span class="text-indigo-600">.co</span></span>
                    </div>
                    <h1 class="text-xl font-bold text-slate-900">{{ __('Şifrenizi Kurtarın') }}</h1>
                    <p class="text-slate-500 text-sm mt-2 font-medium">{{ __('Endişelenmeyin! E-posta adresinizi girin, size sıfırlama bağlantısı gönderelim.') }}</p>
                </div>

                @if (session('status'))
                    <div class="mb-6 p-4 bg-emerald-50 text-emerald-700 text-sm font-bold rounded-xl border border-emerald-100 flex items-start gap-3">
                        <i class='bx bx-check-circle text-xl mt-0.5'></i>
                        {{ session('status') }}
                    </div>
                @endif

                <!-- Card -->
                <div class="bg-white p-8 rounded-2xl border border-slate-100 shadow-xl shadow-slate-200/40">
                    <form action="{{ route('password.email') }}" method="POST" class="space-y-5">
                        @csrf
                        <div>
                            <label for="email" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">{{ __('Email Adresiniz') }}</label>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" required 
                                class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all duration-200 placeholder:text-slate-400"
                                placeholder="ornek@sirket.com">
                            @error('email') <p class="text-rose-500 text-xs mt-1 font-bold">{{ $message }}</p> @enderror
                        </div>

                        <button type="submit" 
                            class="w-full bg-slate-950 text-white font-bold py-3.5 rounded-xl text-sm hover:bg-slate-800 transition-all duration-200 shadow-lg shadow-slate-900/20 active:scale-[0.98] flex items-center justify-center gap-2">
                            <i class='bx bx-envelope text-xl'></i>
                            <span>{{ __('Sıfırlama Bağlantısı Gönder') }}</span>
                        </button>
                    </form>
                </div>

                <!-- Footer -->
                <div class="mt-8 text-center text-sm">
                    <a href="{{ route('login') }}" class="inline-flex items-center gap-2 font-bold text-slate-500 hover:text-slate-950 transition-colors duration-200">
                        <i class='bx bx-left-arrow-alt text-lg'></i>
                        {{ __('Giriş Ekranına Dön') }}
                    </a>
                </div>
            </div>
        </div>
        
           <div class="fixed bottom-0 left-0 w-full py-6 flex flex-col items-center gap-4 z-50 bg-[#fafafa]">
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
    </body>
</html>
