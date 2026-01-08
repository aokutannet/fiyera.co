<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>İki Adımlı Doğrulama - {{ config('app.name', 'fiyera.co') }}</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
        <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
        <script src="https://cdn.tailwindcss.com"></script>
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
                            <i class='bx bxs-check-shield text-white text-2xl'></i>
                        </div>
                        <span class="text-2xl font-extrabold tracking-tight text-slate-950">fiyera<span class="text-indigo-600">.co</span></span>
                    </div>
                    <h1 class="text-xl font-bold text-slate-900">Doğrulama Kodu</h1>
                    <p class="text-slate-500 text-sm mt-2 font-medium">Lütfen e-posta adresinize gönderilen doğrulama kodunu giriniz.</p>
                </div>

                <!-- Card -->
                <div class="bg-white p-8 rounded-2xl border border-slate-100 shadow-xl shadow-slate-200/40">
                    @if (session('status'))
                        <div class="mb-4 p-4 rounded-xl bg-green-50 border border-green-100 text-green-700 text-sm font-bold">
                            {{ session('status') }}
                        </div>
                    @endif

                    @error('two_factor_code')
                        <div class="mb-4 p-4 rounded-xl bg-red-50 border border-red-100 text-red-700 text-sm font-bold">
                            {{ $message }}
                        </div>
                    @enderror

                    <form action="{{ route('verify.store') }}" method="POST" class="space-y-5">
                        @csrf
                        <div>
                            <label for="two_factor_code" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Doğrulama Kodu</label>
                            <input type="text" id="two_factor_code" name="two_factor_code" required autofocus autocomplete="one-time-code" inputmode="numeric"
                                class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-center text-2xl font-bold tracking-[0.5em] focus:outline-none focus:ring-2 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all duration-200 placeholder:text-slate-300"
                                placeholder="••••••">
                        </div>

                        <button type="submit" 
                            class="w-full bg-slate-950 text-white font-bold py-3.5 rounded-xl text-sm hover:bg-slate-800 transition-all duration-200 shadow-lg shadow-slate-900/20 active:scale-[0.98] flex items-center justify-center gap-2">
                            <span>Doğrula</span>
                            <i class='bx bx-right-arrow-alt text-xl'></i>
                        </button>
                    </form>

                    <form action="{{ route('verify.resend') }}" method="GET" class="mt-6">
                         <div class="relative py-2">
                            <div class="absolute inset-0 flex items-center">
                                <div class="w-full border-t border-slate-100"></div>
                            </div>
                            <div class="relative flex justify-center">
                                <span class="bg-white px-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Kod Gelmedi mi?</span>
                            </div>
                        </div>
                        <button type="submit" class="w-full mt-2 text-indigo-600 font-bold text-sm hover:text-indigo-800 transition-colors">
                            Tekrar Gönder
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="py-6 text-center text-[11px] font-bold text-slate-400 uppercase tracking-widest">
            &copy; {{ date('Y') }} Fiyera.co
        </div>
    </body>
</html>
