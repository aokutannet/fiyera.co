<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Super Admin Giriş - {{ config('app.name', 'fiyera.co') }}</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
        <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
        <script src="https://cdn.tailwindcss.com"></script>
        <style>
            body { 
                font-family: 'Plus Jakarta Sans', sans-serif;
                background-color: #f8fafc;
                letter-spacing: -0.01em; 
            }
        </style>
    </head>
    <body class="antialiased text-slate-950 flex flex-col min-h-screen">
        <div class="flex-1 flex flex-col items-center justify-center px-6 py-12">
            <div class="w-full max-w-[400px]">
                <!-- Logo -->
                <!-- Logo -->
                <div class="mb-10 text-center">
                    <div class="flex items-center justify-center gap-2.5 mb-6">
                        <div class="w-10 h-10 bg-slate-950 rounded-xl flex items-center justify-center shadow-lg shadow-slate-200">
                            <i class='bx bxs-bolt text-white text-2xl'></i>
                        </div>
                        <span class="text-2xl font-extrabold tracking-tight text-slate-950">fiyera<span class="text-indigo-600">.co</span></span>
                    </div>
                    <h1 class="text-xl font-bold text-slate-900">Tekrar Hoşgeldiniz</h1>
                    <p class="text-slate-500 text-sm mt-2 font-medium">Hesabınıza giriş yaparak tekliflerinizi yönetin.</p>
                </div>

                <!-- Card -->
                <div class="bg-white p-8 rounded-2xl border border-slate-100 shadow-xl shadow-slate-200/40">
                    <form action="{{ route('admin.login') }}" method="POST" class="space-y-5">
                        @csrf
                        <div>
                            <label for="email" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Email Adresiniz</label>
                            <input type="email" id="email" name="email" required 
                                class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-rose-500/10 focus:border-rose-500 transition-all duration-200 placeholder:text-slate-400"
                                placeholder="admin@fiyera.co" value="{{ old('email') }}">
                            @error('email')
                                <p class="text-rose-500 text-xs font-bold mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Şifreniz</label>
                            <input type="password" id="password" name="password" required 
                                class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-rose-500/10 focus:border-rose-500 transition-all duration-200 placeholder:text-slate-400"
                                placeholder="••••••••">
                        </div>

                        <button type="submit" 
                            class="w-full bg-slate-950 text-white font-bold py-3.5 rounded-xl text-sm hover:bg-slate-800 transition-all duration-200 shadow-lg shadow-slate-900/20 active:scale-[0.98] flex items-center justify-center gap-2">
                            <span>Panele Eriş</span>
                            <i class='bx bx-right-arrow-alt text-xl'></i>
                        </button>
                    </form>
                </div>
                
                <div class="mt-8 text-center text-sm">
                    <a href="{{ route('login') }}" class="text-slate-400 hover:text-slate-600 font-bold transition-colors">
                        <i class='bx bx-arrow-back'></i> Normal Üye Girişi
                    </a>
                </div>
            </div>
        </div>
        
        <div class="py-6 text-center text-[11px] font-bold text-slate-400 uppercase tracking-widest">
            &copy; {{ date('Y') }} Fiyera.co
        </div>
    </body>
</html>
