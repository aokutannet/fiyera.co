<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Hesap Durumu - {{ config('app.name', 'fiyera.co') }}</title>
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
            <div class="w-full max-w-[480px]">
                <!-- Logo -->
                <div class="mb-10 text-center">
                    <div class="flex items-center justify-center gap-2.5 mb-6">
                        <div class="w-10 h-10 bg-slate-950 rounded-xl flex items-center justify-center shadow-lg shadow-slate-200">
                            <i class='bx bxs-bolt text-white text-2xl'></i>
                        </div>
                        <span class="text-2xl font-extrabold tracking-tight text-slate-950">fiyera<span class="text-indigo-600">.co</span></span>
                    </div>
                </div>

                <!-- Card -->
                <div class="bg-white p-8 rounded-2xl border border-slate-100 shadow-xl shadow-slate-200/40 text-center">
                    
                    @if($reason === 'suspended')
                        <div class="w-20 h-20 bg-rose-50 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class='bx bx-block text-4xl text-rose-500'></i>
                        </div>
                        <h1 class="text-xl font-black text-slate-900 mb-3">Hesabınız Askıya Alındı</h1>
                        <p class="text-slate-500 text-sm leading-relaxed mb-8">
                            Hesabınız geçici olarak askıya alınmıştır. Hesabınızı tekrar aktif etmek için yeni bir paket seçebilir veya yönetici ile iletişime geçebilirsiniz.
                        </p>
                        <a href="{{ route('subscription.plans') }}" class="inline-flex items-center justify-center gap-2 w-full py-3.5 bg-indigo-600 text-white font-bold rounded-xl hover:bg-indigo-700 transition-colors shadow-lg shadow-indigo-200 mb-4">
                            <i class='bx bx-up-arrow-circle text-xl'></i>
                            Paketleri İncele / Yenile
                        </a>
                    @elseif($reason === 'expired')
                        <div class="w-20 h-20 bg-amber-50 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class='bx bx-time-five text-4xl text-amber-500'></i>
                        </div>
                        <h1 class="text-xl font-black text-slate-900 mb-3">Abonelik Süreniz Doldu</h1>
                        <p class="text-slate-500 text-sm leading-relaxed mb-8">
                            Deneme süreniz veya mevcut abonelik paketinizin süresi sona ermiştir. Kesintisiz hizmet almaya devam etmek için lütfen paketinizi yenileyin.
                        </p>
                        <a href="{{ route('subscription.plans') }}" class="inline-flex items-center justify-center gap-2 w-full py-3.5 bg-indigo-600 text-white font-bold rounded-xl hover:bg-indigo-700 transition-colors shadow-lg shadow-indigo-200">
                            <i class='bx bx-up-arrow-circle text-xl'></i>
                            Paketi Şimdi Yükselt
                        </a>
                    @else
                        <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class='bx bx-error-circle text-4xl text-slate-400'></i>
                        </div>
                        <h1 class="text-xl font-black text-slate-900 mb-3">Hesap Erişimi Kısıtlandı</h1>
                        <p class="text-slate-500 text-sm leading-relaxed mb-8">
                            Hesabınıza şu anda erişilemiyor. Lütfen daha sonra tekrar deneyin veya destek ekibiyle iletişime geçin.
                        </p>
                    @endif

                    @if($reason !== 'expired')
                        <div class="mt-6 pt-6 border-t border-slate-50">
                            <a href="mailto:support@fiyera.co" class="text-indigo-600 font-bold text-sm hover:underline">
                                Destek Ekibi ile İletişime Geçin
                            </a>
                        </div>
                    @endif
                </div>
                
                <div class="mt-8 text-center text-sm">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-slate-400 hover:text-slate-600 font-bold transition-colors flex items-center justify-center gap-2 mx-auto">
                            <i class='bx bx-log-out'></i> Çıkış Yap
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
