<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ __('Sayfa Bulunamadı') }} - {{ config('app.name', 'fiyera.co') }}</title>
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
    <body class="min-h-screen flex flex-col bg-[#fafafa]">
        <div class="flex-1 flex flex-col items-center justify-center px-6 py-12">
            <div class="w-full max-w-[400px] text-center">
                
               
                <!-- 404 Illustration/Icon -->
                <div class="w-24 h-24 bg-slate-100 rounded-3xl flex items-center justify-center mx-auto mb-6 relative overflow-hidden group">
                    <div class="absolute inset-0 bg-slate-200/50 translate-y-full group-hover:translate-y-0 transition-transform duration-500 ease-out"></div>
                    <i class='bx bx-error text-5xl text-slate-400 group-hover:text-slate-600 transition-colors relative z-10 duration-300'></i>
                </div>

                <!-- Text Content -->
                <h1 class="text-3xl font-extrabold text-slate-950 tracking-tight mb-3">404</h1>
                <h2 class="text-lg font-bold text-slate-900 mb-2">{{ __('Sayfa Bulunamadı') }}</h2>
                <p class="text-slate-500 text-sm font-medium leading-relaxed mb-8 px-4">
                    {{ __('Aradığınız sayfa silinmiş, taşınmış veya hiç var olmamış olabilir.') }}
                </p>

                <!-- Actions -->
                <div class="flex flex-col gap-3">
                    <a href="{{ auth()->check() ? route('dashboard') : url('/') }}" 
                       class="w-full bg-slate-950 text-white font-bold py-3.5 rounded-xl text-sm hover:bg-slate-800 transition-all duration-200 shadow-lg shadow-slate-900/20 active:scale-[0.98] flex items-center justify-center gap-2">
                        <i class='bx bx-arrow-back text-xl'></i>
                        <span>{{ auth()->check() ? __('Panele Dön') : __('Anasayfaya Dön') }}</span>
                    </a>
                </div>

                <!-- Footer -->
                <div class="mt-12">
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">
                        &copy; {{ date('Y') }} Fiyera.co
                    </p>
                </div>
            </div>
        </div>
    </body>
</html>
