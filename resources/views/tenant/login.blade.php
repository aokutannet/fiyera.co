<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ __('Giriş Yap') }} - {{ config('app.name', 'fiyera.co') }}</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
        <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
        <script src="https://cdn.tailwindcss.com"></script>
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
        <style>
            body { 
                font-family: 'Plus Jakarta Sans', sans-serif;
                background-color: #fafafa;
                letter-spacing: -0.01em; 
            }
        </style>
    </head>
    <body class="min-h-screen flex flex-col bg-[#fafafa]">


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
                    <h1 class="text-xl font-bold text-slate-900">{{ __('Tekrar Hoşgeldiniz') }}</h1>
                    <p class="text-slate-500 text-sm mt-2 font-medium">{{ __('Hesabınıza giriş yaparak tekliflerinizi yönetin.') }}</p>
                </div>

                <!-- Card -->
                <div class="bg-white p-8 rounded-2xl border border-slate-100 shadow-xl shadow-slate-200/40">
                    <form action="{{ route('login') }}" method="POST" class="space-y-5">
                        @csrf
                        <div>
                            <label for="email" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">{{ __('Email Adresiniz') }}</label>
                            <input type="email" id="email" name="email" required 
                                class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all duration-200 placeholder:text-slate-400"
                                placeholder="ornek@sirket.com">
                        </div>

                        <div x-data="{ show: false }">
                            <div class="flex items-center justify-between mb-2">
                                <label for="password" class="block text-xs font-bold text-slate-500 uppercase tracking-wider">{{ __('Şifreniz') }}</label>
                                <a href="{{ route('password.request') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-700 transition-colors">{{ __('Şifremi Unuttum?') }}</a>
                            </div>
                            <div class="relative">
                                <input :type="show ? 'text' : 'password'" id="password" name="password" required 
                                    class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all duration-200 placeholder:text-slate-400"
                                    placeholder="••••••••">
                                <button type="button" @click="show = !show" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition-colors">
                                    <i class='bx text-xl' :class="show ? 'bx-show' : 'bx-hide'"></i>
                                </button>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <input type="checkbox" id="remember" name="remember" class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                            <label for="remember" class="text-sm font-medium text-slate-600">{{ __('Beni Hatırla') }}</label>
                        </div>

                        <button type="submit" 
                            class="w-full bg-slate-950 text-white font-bold py-3.5 rounded-xl text-sm hover:bg-slate-800 transition-all duration-200 shadow-lg shadow-slate-900/20 active:scale-[0.98] flex items-center justify-center gap-2">
                            <span>{{ __('Giriş Yap') }}</span>
                            <i class='bx bx-right-arrow-alt text-xl'></i>
                        </button>

                        <div class="relative py-2">
                            <div class="absolute inset-0 flex items-center">
                                <div class="w-full border-t border-slate-100"></div>
                            </div>
                            <div class="relative flex justify-center">
                                <span class="bg-white px-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ __('Veya') }}</span>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <a href="{{ route('auth.google') }}" class="flex w-full items-center justify-center gap-3 px-4 py-3 border border-slate-200 rounded-xl hover:bg-slate-50 transition-all duration-200 group relative overflow-hidden bg-white shadow-sm hover:shadow-md hover:border-slate-300">
                                <div class="absolute inset-0 bg-slate-50 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                                <i class='bx bxl-google text-2xl text-slate-500 group-hover:text-[#ea4335] transition-colors relative z-10'></i>
                                <span class="text-sm font-bold text-slate-700 group-hover:text-slate-900 relative z-10">{{ __('Google ile Giriş Yap') }}</span>
                            </a>
                        </div>
                        
                     
                    
                </div>

            </form>
                </div>

                <!-- Footer -->
                <div class="mt-8 text-center text-sm">
                    <p class="text-slate-500 font-medium">{{ __('Hesabınız yok mu?') }} <a href="{{ route('register') }}" class="font-bold text-slate-900 hover:text-indigo-600 transition-colors">
                        {{ __('Hemen Oluşturun') }}
                    </a></p>
                </div>
            </div>
        </div>
        
        <div class="w-full py-6 flex flex-col items-center gap-4 z-50 bg-[#fafafa] md:fixed md:bottom-0 md:left-0">
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
