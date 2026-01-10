<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'fiyera.co') }} - {{ __('İşletmenizin Yeni Gücü') }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    
    <!-- Scripts & Styles -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .glass-nav { background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(12px); border-bottom: 1px solid rgba(226, 232, 240, 0.8); }
        .hero-pattern { 
            background-color: #f8fafc;
            background-image: radial-gradient(#6366f1 0.5px, transparent 0.5px), radial-gradient(#6366f1 0.5px, #f8fafc 0.5px);
            background-size: 20px 20px;
            background-position: 0 0, 10px 10px;
            opacity: 0.1;
        }
    </style>
</head>
<body class="bg-white text-slate-900 antialiased selection:bg-indigo-600 selection:text-white" x-data="{ mobileMenuOpen: false }">

    <!-- Rounded Frame Wrapper (Optional 'App' feel) -->
    <div class="bg-slate-50 min-h-screen pt-4 px-4 pb-4">
        <div class="bg-white rounded-[2rem] shadow-2xl shadow-slate-200/50 min-h-[calc(100vh-2rem)] relative overflow-hidden">
            
            <!-- Navbar -->
            <header class="absolute top-0 left-0 right-0 z-50 h-24 flex items-center">
                <div class="container mx-auto px-8 lg:px-12 grid grid-cols-12 items-center">
                    
                    <!-- Left: Navigation -->
                    <nav class="hidden md:flex col-span-5 items-center gap-8 text-sm font-semibold text-slate-500">
                        <a href="#features" class="hover:text-slate-900 transition-colors">{{ __('Ürün') }}</a>
                        <a href="#features" class="hover:text-slate-900 transition-colors">{{ __('Özellikler') }}</a>
                        <a href="#references" class="hover:text-slate-900 transition-colors">{{ __('Müşteriler') }}</a>
                        <a href="#pricing" class="hover:text-slate-900 transition-colors">{{ __('Fiyatlar') }}</a>
                    </nav>

                    <!-- Center: Logo -->
                    <div class="col-span-12 md:col-span-2 flex justify-center md:justify-center justify-between w-full">
                        <!-- Mobile Menu Button (Left on mobile) -->
                         <button class="md:hidden text-2xl text-slate-600" @click="mobileMenuOpen = true">
                            <i class='bx bx-menu'></i>
                        </button>

                        <a href="/" class="flex items-center gap-3 group">
                            <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center shadow-lg shadow-indigo-200 group-hover:scale-105 transition-transform">
                                <i class='bx bxs-bolt text-white text-2xl'></i>
                            </div>
                            <span class="font-extrabold text-xl tracking-tight text-slate-950">fiyera<span class="text-indigo-600">.co</span></span>
                        </a>

                        <!-- Hidden placeholder for mobile flex balance -->
                        <div class="w-8 md:hidden"></div>
                    </div>

                    <!-- Right: Actions -->
                    <div class="hidden md:flex col-span-5 justify-end items-center gap-4">
                        
                        <!-- Language Switcher -->
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" class="flex items-center gap-1 text-sm font-bold text-slate-500 hover:text-slate-900 transition-colors">
                                <i class='bx bx-globe text-xl'></i>
                                <span class="uppercase">{{ app()->getLocale() }}</span>
                            </button>
                            <div x-show="open" 
                                    @click.away="open = false" 
                                    class="absolute top-full right-0 mt-4 w-24 bg-white rounded-xl shadow-xl border border-slate-100 overflow-hidden z-50 py-1" 
                                    x-transition:enter="transition ease-out duration-100"
                                    x-transition:enter-start="opacity-0 translate-y-2"
                                    x-transition:enter-end="opacity-100 translate-y-0"
                                    x-cloak>
                                <a href="{{ route('locale.switch', 'tr') }}" class="block px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-50 hover:text-indigo-600 text-center">Türkçe</a>
                                <a href="{{ route('locale.switch', 'en') }}" class="block px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-50 hover:text-indigo-600 text-center">English</a>
                            </div>
                        </div>

                        <a href="{{ route('login') }}" class="px-5 py-2.5 rounded-full bg-slate-100 text-slate-600 text-sm font-bold hover:bg-slate-200 transition-all">
                            {{ __('Giriş Yap') }}
                        </a>
                        <a href="{{ route('register') }}" class="px-6 py-2.5 rounded-full bg-slate-950 text-white text-sm font-bold hover:bg-slate-800 transition-all shadow-lg hover:shadow-xl">
                            {{ __('Ücretsiz Başla') }}
                        </a>
                    </div>
                </div>
            </header>
            <!-- Mobile Menu Overlay -->
            <div x-show="mobileMenuOpen" 
                 class="fixed inset-0 z-[100] px-4 py-6 pointer-events-none flex items-center justify-center" 
                 role="dialog" 
                 aria-modal="true"
                 style="display: none;"
                 x-cloak>
                
                <!-- Backdrop -->
                <div x-show="mobileMenuOpen" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     @click="mobileMenuOpen = false"
                     class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm pointer-events-auto">
                </div>
        
                <!-- Menu Content -->
                <div x-show="mobileMenuOpen"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                     x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                     x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                     class="w-full bg-white rounded-2xl shadow-2xl relative z-10 overflow-hidden pointer-events-auto max-w-sm mx-auto">
                    
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-8">
                            <span class="font-extrabold text-xl tracking-tight text-slate-950">fiyera<span class="text-indigo-600">.co</span></span>
                            <button @click="mobileMenuOpen = false" class="text-slate-400 hover:text-slate-600 transition-colors">
                                <i class='bx bx-x text-3xl'></i>
                            </button>
                        </div>
        
                        <nav class="space-y-2">
                            <a href="#features" @click="mobileMenuOpen = false" class="block px-4 py-3 rounded-xl text-slate-600 font-bold hover:bg-slate-50 hover:text-indigo-600 transition-colors">{{ __('Ürün & Özellikler') }}</a>
                            <a href="#references" @click="mobileMenuOpen = false" class="block px-4 py-3 rounded-xl text-slate-600 font-bold hover:bg-slate-50 hover:text-indigo-600 transition-colors">{{ __('Müşteriler') }}</a>
                            <a href="#pricing" @click="mobileMenuOpen = false" class="block px-4 py-3 rounded-xl text-slate-600 font-bold hover:bg-slate-50 hover:text-indigo-600 transition-colors">{{ __('Fiyatlar') }}</a>
                        </nav>
        
                        <div class="mt-8 pt-8 border-t border-slate-100 space-y-3">
                            <a href="{{ route('login') }}" class="block w-full py-3 rounded-xl bg-slate-100 text-slate-700 font-bold text-center hover:bg-slate-200 transition-colors">
                                {{ __('Giriş Yap') }}
                            </a>
                            <a href="{{ route('register') }}" class="block w-full py-3 rounded-xl bg-slate-900 text-white font-bold text-center hover:bg-slate-800 transition-colors shadow-lg shadow-slate-900/20">
                                {{ __('Ücretsiz Başla') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Hero Section -->
            <section class="relative pt-40 pb-20 lg:pt-48 lg:pb-32 overflow-hidden">
                <!-- Background Glow -->
                <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[800px] bg-indigo-50/50 rounded-full blur-[100px] -mt-64 z-0"></div>
                
                <div class="container mx-auto px-6 lg:px-12 relative z-10 text-center">
                    
                    <!-- Badge -->
                    <div class="inline-flex items-center gap-2 mb-8 animate-[fadeIn_0.5s_ease-out]">
                        <i class='bx bxs-bolt text-indigo-600 text-lg'></i>
                        <span class="text-sm font-bold text-indigo-600 tracking-wide">{{ __('Hızlı, Kolay, Profesyonel') }}</span>
                    </div>

                    <!-- Headline -->
                    <h1 class="text-4xl lg:text-6xl font-black text-slate-900 tracking-tight leading-[1.1] mb-6 max-w-4xl mx-auto">
                        {{ __('Profesyonel Teklifler Hazırlayın,') }} <br>
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-violet-600">{{ __('Satışlarınızı Hızlandırın.') }}</span>
                    </h1>

                    <!-- Subhead -->
                    <p class="text-lg text-slate-500 font-medium max-w-2xl mx-auto leading-relaxed mb-10">
                        {{ __('Excel dosyalarıyla vakit kaybetmeyin. Fiyera ile saniyeler içinde etkileyici teklifler oluşturun ve onay süreçlerini tek panelden yönetin.') }}
                    </p>

                    <!-- CTA Button -->
                    <div class="flex flex-col items-center justify-center gap-4 mb-16 animate-[slideUp_0.8s_ease-out]">
                        <a href="{{ route('register') }}" class="px-10 py-4 bg-slate-900 text-white rounded-full font-bold text-lg shadow-xl shadow-slate-900/20 hover:bg-slate-800 hover:-translate-y-1 transition-all flex items-center justify-center gap-2">
                            {{ __('Hemen Ücretsiz Dene') }}
                            <i class='bx bx-right-arrow-alt'></i>
                        </a>
                        <p class="text-xs font-semibold text-slate-400">{{ __('Kredi kartı gerekmez · 14 gün ücretsiz deneme') }}</p>
                    </div>

                    <!-- Dashboard Image Placeholder -->
                    <div class="relative max-w-6xl mx-auto animate-[slideUp_1s_ease-out]">
                        <div class="bg-white rounded-[2rem] shadow-2xl shadow-slate-200 border-8 border-white p-1">
                            <!-- User Image Here -->
                            <img src="{{ asset('uploads/panel.png') }}" 
                                 class="w-full h-auto rounded-3xl bg-slate-50" 
                                 alt="Fiyera Dashboard">
                        </div>
                        
                        <!-- Floating Elements (Premium Deco) -->
                        <div class="absolute -right-8 top-12 hidden lg:flex items-center gap-4 p-4 pr-6 bg-white/95 backdrop-blur-sm rounded-2xl shadow-[0_20px_40px_rgba(0,0,0,0.08)] border border-white/40 ring-1 ring-slate-50 animate-bounce" style="animation-duration: 6s;">
                            <div class="relative">
                                <div class="w-12 h-12 bg-emerald-500 rounded-xl flex items-center justify-center text-white shadow-lg shadow-emerald-200">
                                    <i class='bx bxs-check-shield text-2xl'></i>
                                </div>
                                <div class="absolute -bottom-1 -right-1 w-5 h-5 bg-white rounded-full flex items-center justify-center">
                                     <i class='bx bxs-check-circle text-emerald-500 text-sm'></i>
                                </div>
                            </div>
                            <div>
                                <div class="text-[10px] uppercase font-extrabold text-slate-400 tracking-wider mb-0.5">{{ __('SON İŞLEM') }}</div>
                                <div class="text-sm font-bold text-slate-900 leading-tight">{{ __('Teklif Onaylandı') }}</div>
                                <div class="text-xs font-bold text-emerald-600 mt-0.5">+₺145,000.00</div>
                            </div>
                        </div>
                        
                         <div class="absolute -left-8 bottom-16 hidden lg:flex flex-col gap-3 p-5 bg-slate-900/95 backdrop-blur-sm rounded-2xl shadow-[0_20px_40px_rgba(0,0,0,0.2)] border border-white/10 animate-bounce" style="animation-duration: 8s;">
                            <div class="flex items-center justify-between gap-8">
                                <span class="text-slate-400 text-[10px] font-bold uppercase tracking-wider">{{ __('AKTİF SATIŞ EKİBİ') }}</span>
                                <span class="flex h-2 w-2 rounded-full bg-emerald-500 shadow-[0_0_10px_rgba(16,185,129,0.5)] animate-pulse"></span>
                            </div>
                            <div class="flex items-center gap-4">
                                <div class="flex -space-x-3">
                                     <img src="https://i.pravatar.cc/100?img=33" class="w-9 h-9 rounded-full border-2 border-slate-800" alt="User">
                                     <img src="https://i.pravatar.cc/100?img=47" class="w-9 h-9 rounded-full border-2 border-slate-800" alt="User">
                                     <img src="https://i.pravatar.cc/100?img=12" class="w-9 h-9 rounded-full border-2 border-slate-800" alt="User">
                                </div>
                                <div class="text-white text-sm font-bold">
                                    +12 <span class="text-slate-500 font-normal ml-1">{{ __('çevrimiçi') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

    <!-- References Section -->
    <!-- Integrations Section (Scrolling Marquee) -->
    <section id="integrations" class="py-16 bg-white overflow-hidden border-t border-slate-100">
        <div class="container mx-auto px-6 lg:px-8 mb-10 text-center">
            <p class="text-sm font-bold text-slate-400 uppercase tracking-widest">{{ __('Favori Araçlarınızla Tam Entegre') }}</p>
        </div>
        
        <div class="relative flex overflow-x-hidden">
            <div class="flex space-x-12 animate-loop-scroll py-4 items-center">
                <!-- Integration Items (Duplicated for Seamless Loop) -->
                @foreach(range(1, 12) as $i)
                    <div class="flex items-center gap-3 bg-slate-50 px-6 py-3 rounded-2xl border border-slate-100 flex-shrink-0 group hover:shadow-lg transition-all cursor-default">
                        <!-- Colorful Icons based on index -->
                        @php
                            $icons = [
                                ['bx bxl-gmail', 'bg-red-500', 'Gmail'],
                                ['bx bxl-slack', 'bg-purple-500', 'Slack'],
                                ['bx bxl-whatsapp', 'bg-green-500', 'WhatsApp'],
                                ['bx bxl-zoom', 'bg-blue-500', 'Zoom'],
                                ['bx bxl-stripe', 'bg-indigo-500', 'Stripe'],
                                ['bx bxl-dropbox', 'bg-blue-600', 'Dropbox'],
                                ['bx bxl-trello', 'bg-sky-500', 'Trello'],
                                ['bx bxl-mailchimp', 'bg-yellow-400', 'Mailchimp'],
                                ['bx bxs-file-pdf', 'bg-rose-500', 'Adobe Sign'],
                                ['bx bxl-telegram', 'bg-sky-400', 'Telegram'],
                                ['bx bxl-microsoft-teams', 'bg-indigo-600', 'Teams'],
                                ['bx bxl-google-cloud', 'bg-blue-500', 'Drive']
                            ];
                            $icon = $icons[($i-1) % count($icons)];
                        @endphp
                        <div class="w-10 h-10 rounded-xl {{ $icon[1] }} text-white flex items-center justify-center text-xl shadow-lg relative overflow-hidden group-hover:scale-110 transition-transform">
                             <div class="absolute inset-0 bg-white/20 blur-sm rounded-full scale-0 group-hover:scale-150 transition-transform duration-500"></div>
                             <i class='{{ $icon[0] }} relative z-10'></i>
                        </div>
                        <span class="font-bold text-slate-700 text-sm">{{ $icon[2] }}</span>
                    </div>
                @endforeach
                 <!-- Duplicate for seamlessly loop -->
                 @foreach(range(1, 12) as $i)
                    <div class="flex items-center gap-3 bg-slate-50 px-6 py-3 rounded-2xl border border-slate-100 flex-shrink-0 group hover:shadow-lg transition-all cursor-default">
                        @php
                            $icons = [
                                ['bx bxl-gmail', 'bg-red-500', 'Gmail'],
                                ['bx bxl-slack', 'bg-purple-500', 'Slack'],
                                ['bx bxl-whatsapp', 'bg-green-500', 'WhatsApp'],
                                ['bx bxl-zoom', 'bg-blue-500', 'Zoom'],
                                ['bx bxl-stripe', 'bg-indigo-500', 'Stripe'],
                                ['bx bxl-dropbox', 'bg-blue-600', 'Dropbox'],
                                ['bx bxl-trello', 'bg-sky-500', 'Trello'],
                                ['bx bxl-mailchimp', 'bg-yellow-400', 'Mailchimp'],
                                ['bx bxs-file-pdf', 'bg-rose-500', 'Adobe Sign'],
                                ['bx bxl-telegram', 'bg-sky-400', 'Telegram'],
                                ['bx bxl-microsoft-teams', 'bg-indigo-600', 'Teams'],
                                ['bx bxl-google-cloud', 'bg-blue-500', 'Drive']
                            ];
                            $icon = $icons[($i-1) % count($icons)];
                        @endphp
                        <div class="w-10 h-10 rounded-xl {{ $icon[1] }} text-white flex items-center justify-center text-xl shadow-lg relative overflow-hidden group-hover:scale-110 transition-transform">
                             <div class="absolute inset-0 bg-white/20 blur-sm rounded-full scale-0 group-hover:scale-150 transition-transform duration-500"></div>
                             <i class='{{ $icon[0] }} relative z-10'></i>
                        </div>
                         <span class="font-bold text-slate-700 text-sm">{{ $icon[2] }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Features Section (3-Column Layout) -->
    <section id="features" class="py-24 bg-slate-50">
        <div class="container mx-auto px-6 lg:px-8">
             <div class="text-center max-w-2xl mx-auto mb-16">
                <h2 class="text-3xl md:text-5xl font-black text-slate-900 tracking-tight leading-[1.1] mb-6">
                    {{ __('Bütün işlerinizi tek bir') }} <br>
                    <span class="text-indigo-600">{{ __('platformdan yönetin.') }}</span>
                </h2>
                <p class="text-lg text-slate-500 font-medium">{{ __('Karmaşadan kurtulun. Fiyera ile teklif, satış ve finans süreçlerini birleştirin.') }}</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                
                <!-- Feature 1: AI Proposal Creation -->
                <div class="bg-white rounded-[2rem] p-8 pb-0 flex flex-col h-full relative overflow-hidden group hover:shadow-2xl hover:-translate-y-2 transition-all duration-500 border border-slate-100">
                    <div class="mb-10 relative z-10 pointer-events-none">
                        <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-2xl mb-6 group-hover:scale-110 group-hover:rotate-3 transition-transform duration-500">
                             <i class='bx bxs-magic-wand'></i>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 mb-3 leading-tight">
                            {{ __('Yapay Zeka ile Teklif') }}
                        </h3>
                        <p class="text-slate-500 text-sm leading-relaxed">
                            {{ __('Sadece başlıkları girin, yapay zeka sizin için ikna edici ve profesyonel teklif metinlerini saniyeler içinde yazsın.') }}
                        </p>
                    </div>
                    
                    <!-- Visual: Signature Animation (Result) -->
                    <div class="mt-auto relative w-full h-56 bg-gradient-to-t from-slate-50 to-white border-t border-slate-50">
                        <div class="absolute inset-0 flex items-center justify-center">
                            <!-- Document -->
                            <div class="relative w-48 h-60 bg-white shadow-xl shadow-slate-200/50 border border-slate-100 rounded-lg p-5 group-hover:translate-y-[-10px] transition-transform duration-700 top-8">
                                <!-- Doc Lines -->
                                <div class="space-y-3 mb-8 opacity-40">
                                    <div class="w-1/3 h-2 bg-slate-800 rounded-full"></div>
                                    <div class="w-full h-1.5 bg-slate-300 rounded-full"></div>
                                    <div class="w-full h-1.5 bg-slate-300 rounded-full"></div>
                                </div>
                                <div class="space-y-3 mb-6 opacity-40">
                                    <div class="w-full h-1.5 bg-slate-300 rounded-full"></div>
                                </div>
                                <!-- Signature Area -->
                                <div class="relative h-12 border-b-2 border-indigo-100 flex items-end pb-1">
                                    <span class="text-[10px] text-indigo-300 font-bold uppercase tracking-wider absolute -bottom-5 left-0">Yapay Zeka Onaylı</span>
                                    <svg class="w-32 h-12 text-indigo-600" viewBox="0 0 150 60" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path class="signature-path" d="M10,50 C20,40 25,55 35,45 C45,35 50,55 60,40 C70,25 65,55 80,45 C95,35 100,50 110,40 C120,30 115,55 130,45" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-dasharray="200" stroke-dashoffset="200" />
                                    </svg>
                                </div>
                                <!-- Robot/AI Icon Badge -->
                                <div class="absolute top-[-10px] right-[-10px] w-8 h-8 rounded-full bg-indigo-600 border-2 border-white shadow-lg flex items-center justify-center text-white text-sm animate-bounce">
                                    <i class='bx bxs-bot'></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Feature 2: Live Proposal Link -->
                <div class="bg-white rounded-[2rem] p-8 pb-0 flex flex-col h-full relative overflow-hidden group hover:shadow-2xl hover:-translate-y-2 transition-all duration-500 border border-slate-100">
                     <div class="mb-10 relative z-10 pointer-events-none">
                         <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-2xl mb-6 group-hover:scale-110 group-hover:rotate-3 transition-transform duration-500">
                             <i class='bx bx-link-external'></i>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 mb-3 leading-tight">
                            {{ __('Canlı Teklif Linki') }}
                        </h3>
                        <p class="text-slate-500 text-sm leading-relaxed">
                            {{ __('PDF dosyalarıyla boğuşmayın. Müşterinize interaktif bir link gönderin, teklif görüntülendiğinde anında bildirim alın.') }}
                        </p>
                    </div>

                    <!-- Visual: Pipeline/Stages Animation -->
                    <div class="mt-auto relative w-full h-56 bg-slate-50/50 rounded-tl-[2rem] p-5 overflow-hidden">
                        <div class="flex gap-3 h-full items-end opacity-80 group-hover:opacity-100 transition-opacity">
                            <!-- Col 1: Sent -->
                            <div class="w-1/3 bg-slate-100 rounded-t-xl h-[90%] p-2 space-y-2 flex flex-col items-center pt-4">
                                <i class='bx bx-envelope text-slate-300 text-2xl mb-2'></i>
                                <div class="h-1.5 w-12 bg-slate-300 rounded-full"></div>
                            </div>
                             <!-- Col 2: Viewed (Active) -->
                            <div class="w-1/3 bg-slate-100 rounded-t-xl h-[100%] p-2 space-y-2 relative border-t-4 border-amber-400">
                                <div class="absolute -top-3 left-1/2 -translate-x-1/2 bg-amber-400 text-white text-[8px] font-bold px-2 py-0.5 rounded-full">GÖRÜNTÜLENDİ</div>
                                <!-- Moving Card -->
                                <div class="absolute top-16 left-2 w-[85%] bg-white p-3 rounded shadow-lg border-l-4 border-amber-500 z-20 group-hover:translate-y-[-10px] transition-transform duration-500">
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-full bg-slate-100 overflow-hidden"><img src="https://i.pravatar.cc/100?img=12"></div>
                                        <div class="h-2 w-12 bg-slate-200 rounded-full"></div>
                                    </div>
                                </div>
                            </div>
                             <!-- Col 3: Accepted -->
                            <div class="w-1/3 bg-slate-100 rounded-t-xl h-[90%] p-2 space-y-2 flex flex-col items-center pt-4 opacity-50">
                                <i class='bx bx-check-circle text-emerald-300 text-2xl mb-2'></i>
                                <div class="h-1.5 w-10 bg-emerald-200 rounded-full"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Feature 3: Advanced Reporting -->
                <div class="bg-white rounded-[2rem] p-8 pb-0 flex flex-col h-full relative overflow-hidden group hover:shadow-2xl hover:-translate-y-2 transition-all duration-500 border border-slate-100">
                     <div class="mb-10 relative z-10 pointer-events-none">
                         <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-2xl mb-6 group-hover:scale-110 group-hover:rotate-3 transition-transform duration-500">
                             <i class='bx bxs-pie-chart-alt-2'></i>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 mb-3 leading-tight">
                            {{ __('Gelişmiş Raporlama') }}
                        </h3>
                        <p class="text-slate-500 text-sm leading-relaxed">
                            {{ __('Satış performansınızı derinlemesine analiz edin. Hangi personel ne kadar satış yaptı, anlık görün.') }}
                        </p>
                    </div>

                    <!-- Visual: Money Stream/Data Animation -->
                    <div class="mt-auto relative w-full h-56 bg-slate-900 rounded-tl-[2rem] p-6 overflow-hidden">
                        <!-- BG Graph -->
                        <div class="absolute bottom-0 left-0 w-full h-24 bg-gradient-to-t from-emerald-500/20 to-transparent"></div>
                        <svg class="absolute bottom-0 left-0 w-full h-20 text-emerald-500/30" viewBox="0 0 100 20" preserveAspectRatio="none">
                            <path d="M0,20 L0,15 Q20,5 40,12 T80,8 T100,15 L100,20 Z" fill="currentColor"></path>
                        </svg>

                        <!-- Floating Pills -->
                        <div class="absolute inset-x-0 bottom-0 h-full flex flex-col justify-end items-center pb-4 space-y-3">
                            <!-- Pill 1 -->
                            <div class="bg-slate-800/90 backdrop-blur border border-slate-700 rounded-full px-4 py-2 flex items-center gap-3 w-48 transition-all duration-700 group-hover:-translate-y-24 group-hover:opacity-100 opacity-60 translate-y-4">
                                <div class="w-6 h-6 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-xs"><i class='bx bx-trending-up'></i></div>
                                <div>
                                    <div class="text-[10px] text-slate-400">Teklif Dönüşüm</div>
                                    <div class="text-xs font-bold text-white">%28 Artış</div>
                                </div>
                            </div>
                            <!-- Pill 2 -->
                            <div class="bg-slate-800/90 backdrop-blur border border-slate-700 rounded-full px-4 py-2 flex items-center gap-3 w-48 transition-all duration-700 delay-100 group-hover:-translate-y-24 group-hover:opacity-100 opacity-40 translate-y-12">
                                <div class="w-6 h-6 rounded-full bg-indigo-500/20 text-indigo-400 flex items-center justify-center text-xs"><i class='bx bx-user'></i></div>
                                <div>
                                    <div class="text-[10px] text-slate-400">En İyi Satışçı</div>
                                    <div class="text-xs font-bold text-white">Ahmet Y.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            
            <!-- Second Row of Features -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mt-8">
                
                <!-- Feature 4: Advanced Integration (Restored) -->
                <div class="bg-white rounded-[2rem] p-8 pb-0 flex flex-col h-full relative overflow-hidden group hover:shadow-2xl hover:-translate-y-2 transition-all duration-500 border border-slate-100">
                     <div class="mb-10 relative z-10 pointer-events-none">
                         <div class="w-12 h-12 rounded-2xl bg-sky-50 text-sky-600 flex items-center justify-center text-2xl mb-6 group-hover:scale-110 group-hover:rotate-3 transition-transform duration-500">
                             <i class='bx bx-plug'></i>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 mb-3 leading-tight">
                            {{ __('Gelişmiş Entegrasyon') }}
                        </h3>
                        <p class="text-slate-500 text-sm leading-relaxed">
                            {{ __('CRM, Muhasebe ve Ödeme sistemlerinizi tek çatı altında toplayın. İş akışınızı kopukluk olmadan yönetin.') }}
                        </p>
                    </div>

                    <!-- Visual: Connectivity Animation -->
                    <div class="mt-auto relative w-full h-56 bg-slate-900 rounded-tl-[2rem] overflow-hidden flex items-center justify-center">
                        <div class="absolute inset-0 bg-gradient-to-br from-indigo-900 to-slate-900"></div>
                        
                        <!-- Center Hub -->
                        <div class="relative z-10 w-16 h-16 bg-white rounded-2xl shadow-xl shadow-indigo-500/20 flex items-center justify-center text-3xl text-indigo-600">
                            <i class='bx bxl-sketch'></i>
                            <div class="absolute inset-0 rounded-2xl border-2 border-white/50 animate-ping opacity-20"></div>
                        </div>
                        
                        <!-- Satellites -->
                        <div class="absolute bg-slate-800 p-2 rounded-lg border border-slate-700 text-white text-xl shadow-lg top-10 left-10 group-hover:translate-x-2 group-hover:translate-y-2 transition-transform duration-1000 ease-in-out">
                            <i class='bx bxl-google'></i>
                        </div>
                        <div class="absolute bg-slate-800 p-2 rounded-lg border border-slate-700 text-white text-xl shadow-lg top-12 right-12 group-hover:-translate-x-2 group-hover:translate-y-2 transition-transform duration-1000 ease-in-out delay-100">
                             <i class='bx bxl-slack'></i>
                        </div>
                        <div class="absolute bg-slate-800 p-2 rounded-lg border border-slate-700 text-white text-xl shadow-lg bottom-10 left-16 group-hover:translate-x-2 group-hover:-translate-y-2 transition-transform duration-1000 ease-in-out delay-200">
                             <i class='bx bxl-stripe'></i>
                        </div>
                        <div class="absolute bg-slate-800 p-2 rounded-lg border border-slate-700 text-white text-xl shadow-lg bottom-12 right-20 group-hover:-translate-x-2 group-hover:-translate-y-2 transition-transform duration-1000 ease-in-out delay-300">
                             <i class='bx bxl-whatsapp'></i>
                        </div>
                        <!-- Lines -->
                        <svg class="absolute inset-0 w-full h-full pointer-events-none opacity-20" stroke="white" stroke-width="1" stroke-dasharray="4 4">
                            <line x1="50%" y1="50%" x2="20%" y2="25%" class="group-hover:opacity-100 transition-opacity" />
                            <line x1="50%" y1="50%" x2="80%" y2="30%" class="group-hover:opacity-100 transition-opacity" />
                            <line x1="50%" y1="50%" x2="30%" y2="80%" class="group-hover:opacity-100 transition-opacity" />
                            <line x1="50%" y1="50%" x2="70%" y2="75%" class="group-hover:opacity-100 transition-opacity" />
                        </svg>
                    </div>
                </div>

                <!-- Feature 5: Brand Identity (New) -->
                <div class="bg-white rounded-[2rem] p-8 pb-0 flex flex-col h-full relative overflow-hidden group hover:shadow-2xl hover:-translate-y-2 transition-all duration-500 border border-slate-100">
                     <div class="mb-10 relative z-10 pointer-events-none">
                         <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center text-2xl mb-6 group-hover:scale-110 group-hover:rotate-3 transition-transform duration-500">
                             <i class='bx bxs-palette'></i>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 mb-3 leading-tight">
                            {{ __('Kurumsal Kimlik') }}
                        </h3>
                        <p class="text-slate-500 text-sm leading-relaxed">
                            {{ __('Logonuzu yükleyin, renklerinizi seçin. Size özel şablonlarla markanızın kurumsallığını yansıtın.') }}
                        </p>
                    </div>

                    <!-- Visual: Branding Animation -->
                    <div class="mt-auto relative w-full h-56 bg-slate-50 border-t border-slate-100 flex items-center justify-center overflow-hidden">
                        <!-- BG Circles -->
                        <div class="absolute top-0 right-0 w-32 h-32 bg-rose-100 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2"></div>
                        
                        <!-- Mini Proposal -->
                        <div class="relative w-32 h-40 bg-white shadow-xl rounded border border-slate-200 p-3 group-hover:scale-105 transition-transform duration-500">
                            <!-- Header Bar (Changes Color) -->
                            <div class="h-3 w-full rounded mb-3 bg-slate-200 group-hover:animate-pulse group-hover:bg-rose-500 transition-colors duration-1000"></div>
                            <div class="h-1.5 w-3/4 bg-slate-200 rounded mb-2"></div>
                            <div class="h-1.5 w-full bg-slate-100 rounded mb-1"></div>
                            <div class="h-1.5 w-full bg-slate-100 rounded mb-1"></div>
                            <div class="h-1.5 w-2/3 bg-slate-100 rounded mb-4"></div>
                            <!-- Table Row -->
                            <div class="flex gap-1 mb-1">
                                <div class="h-1.5 w-1/4 bg-slate-100 rounded"></div>
                                <div class="h-1.5 w-1/4 bg-slate-100 rounded"></div>
                                <div class="h-1.5 w-1/4 bg-slate-100 rounded"></div>
                            </div>
                        </div>
                        
                        <!-- Floating Palette -->
                        <div class="absolute bottom-6 right-10 bg-white p-2 rounded-full shadow-lg border border-slate-100 flex gap-2 rotate-12 group-hover:rotate-0 group-hover:translate-x-2 transition-all duration-500">
                            <div class="w-3 h-3 rounded-full bg-rose-500"></div>
                            <div class="w-3 h-3 rounded-full bg-indigo-500 opacity-30"></div>
                            <div class="w-3 h-3 rounded-full bg-amber-500 opacity-30"></div>
                        </div>
                    </div>
                </div>

                <!-- Feature 6: Mobile Responsive (New) -->
                <div class="bg-white rounded-[2rem] p-8 pb-0 flex flex-col h-full relative overflow-hidden group hover:shadow-2xl hover:-translate-y-2 transition-all duration-500 border border-slate-100">
                     <div class="mb-10 relative z-10 pointer-events-none">
                         <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center text-2xl mb-6 group-hover:scale-110 group-hover:rotate-3 transition-transform duration-500">
                             <i class='bx bxs-devices'></i>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 mb-3 leading-tight">
                            {{ __('%100 Mobil Uyumlu') }}
                        </h3>
                        <p class="text-slate-500 text-sm leading-relaxed">
                            {{ __('Ofise bağlı kalmayın. Teklifleriniz telefonda, tablette ve bilgisayarda kusursuz görünür.') }}
                        </p>
                    </div>

                    <!-- Visual: Mobile Animation -->
                    <div class="mt-auto relative w-full h-56 bg-slate-900 rounded-tl-[2rem] flex items-end justify-center overflow-hidden pb-0">
                         <div class="absolute inset-0 bg-gradient-to-br from-purple-900/50 to-slate-900"></div>
                         
                         <!-- Phone Frame -->
                         <div class="relative w-28 h-48 bg-slate-800 rounded-t-[2rem] border-4 border-slate-700 border-b-0 shadow-2xl translate-y-4 group-hover:translate-y-0 transition-transform duration-500">
                             <!-- Notch -->
                             <div class="absolute top-2 left-1/2 -translate-x-1/2 w-8 h-1 bg-slate-900 rounded-full z-20"></div>
                             
                             <!-- Screen -->
                             <div class="w-full h-full bg-white rounded-t-[1.7rem] overflow-hidden relative">
                                 <!-- Mini App Header -->
                                 <div class="h-8 bg-indigo-600 w-full"></div>
                                 <!-- App Content -->
                                 <div class="p-2 space-y-2">
                                     <div class="h-16 bg-slate-100 rounded-lg flex items-center justify-center text-[8px] text-slate-400 font-bold border border-dashed border-slate-300">
                                         Teklif Önizleme
                                     </div>
                                     <div class="h-2 w-full bg-slate-100 rounded-full"></div>
                                     <div class="h-2 w-2/3 bg-slate-100 rounded-full"></div>
                                     
                                     <!-- Action Button -->
                                     <div class="mt-4 h-6 w-full bg-emerald-500 rounded-md flex items-center justify-center text-[6px] text-white font-bold group-hover:scale-105 transition-transform duration-300">
                                         ONAYLA
                                     </div>
                                 </div>
                             </div>
                         </div>
                    </div>
                </div>

            </div>
            
            <style>
                .group:hover .signature-path {
                    stroke-dashoffset: 0;
                    transition: stroke-dashoffset 1.5s ease-in-out 0.2s;
                }
            </style>
        </div>
    </section>

   

    <!-- Pricing Section -->
    <section id="pricing" class="bg-slate-50 relative overflow-hidden">
        <div class="container mx-auto px-4 lg:px-8 relative z-10" x-data="{ 
            billing: 'yearly',
            period() { return this.billing === 'yearly' ? '/ay' : '/ay'; }
        }">
            <!-- Header & Toggle -->
            <div class="text-center max-w-3xl mx-auto mb-16">
                <h2 class="text-4xl md:text-5xl font-black text-slate-900 tracking-tight mb-6">{{ __('İhtiyacınıza Uygun Planı Seçin') }}</h2>
                <p class="text-lg text-slate-500 mb-8">{{ __('30 gün para iade garantisi ile risk almadan başlayın.') }}</p>

                <!-- Billing Cycle Toggle -->
                <div class="inline-flex bg-slate-100 p-1.5 rounded-xl border border-slate-200">
                    <button 
                        @click="billing = 'monthly'"
                        class="px-6 py-2.5 rounded-lg text-sm font-bold transition-all"
                        :class="billing === 'monthly' ? 'bg-white text-slate-900 shadow-sm ring-1 ring-slate-200' : 'text-slate-500 hover:text-slate-900'"
                    >
                        {{ __('Aylık Öde') }}
                    </button>
                    <button 
                        @click="billing = 'yearly'"
                        class="px-6 py-2.5 rounded-lg text-sm font-bold transition-all inline-flex items-center gap-2"
                        :class="billing === 'yearly' ? 'bg-white text-slate-900 shadow-sm ring-1 ring-slate-200' : 'text-slate-500 hover:text-slate-900'"
                    >
                        {{ __('Yıllık Öde') }}
                        <span class="text-[10px] bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded-full font-extrabold uppercase tracking-wide">
                            {{ __('%20 Tasarruf Et') }}
                        </span>
                    </button>
                </div>
            </div>

            <!-- Plans Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 items-start">
               @foreach($plans as $p)
                @php 
                    $isPopular = $p->is_popular;
                    $discountRate = 20; // Example static discount for display
                    $monthlyPrice = $p->price_monthly;
                    $yearlyPricePerMonth = $p->price_yearly / 12; // Show effective monthly cost logic if needed, or simply use stored monthly price
                    
                    // For display, we simulate a "higher" strike-through price to show value
                    $fakeOriginalPrice = $monthlyPrice * 1.5;
                @endphp
                
                <div class="relative flex flex-col {{ $isPopular ? 'mt-0' : 'mt-8' }}">
                    <!-- Popular Header -->
                    @if($isPopular)
                        <div class="bg-indigo-600 text-white text-center py-2.5 text-sm font-bold uppercase tracking-widest rounded-t-2xl relative z-10 w-full">
                            {{ __('En Popüler') }}
                        </div>
                    @endif

                    <div class="flex flex-col bg-white {{ $isPopular ? 'rounded-b-2xl border-2 border-indigo-600 shadow-2xl relative z-10' : 'rounded-2xl border border-slate-200 hover:shadow-xl transition-shadow' }} p-6 lg:p-8">
                        
                        <!-- Discount Badge -->
                        <!-- Discount Badge -->
                        <div class="absolute top-6 right-6" x-show="billing === 'yearly'" x-cloak>
                            <span class="bg-yellow-200 text-yellow-900 text-[11px] font-black px-2.5 py-1 rounded-full uppercase tracking-wide">
                                %{{ $discountRate }} İNDİRİM
                            </span>
                        </div>

                        <!-- Header -->
                        <div class="mb-4 pr-16">
                            <h3 class="text-xl font-bold text-slate-900 mb-2">{{ $p->name }}</h3>
                            <p class="text-xs text-slate-500 font-medium leading-relaxed min-h-[40px]">
                                {{ $p->description }}
                            </p>
                        </div>
                        
                        <!-- Pricing -->
                        <div class="mb-6">
                            <div class="flex items-center gap-2 mb-1" x-show="billing === 'yearly'">
                                <span class="text-slate-400 text-sm font-bold line-through">₺{{ number_format($fakeOriginalPrice, 2) }}</span>
                            </div>
                            
                            <div class="flex items-baseline gap-1">
                                <span class="text-lg font-bold text-slate-900">₺</span>
                                <span class="text-5xl font-black text-slate-900 tracking-tight" 
                                      x-text="billing === 'monthly' ? '{{ number_format($p->price_monthly, 0) }}' : '{{ number_format($p->price_yearly / 12, 0) }}'">
                                </span>
                                <span class="text-lg font-medium text-slate-500">/ay</span>
                            </div>
                            
                            <p class="text-indigo-600 text-xs font-bold mt-2 h-4" x-show="billing === 'yearly'">
                                +3 ay ücretsiz
                            </p>
                        </div>

                        <!-- Action -->
                        <div class="mb-6">
                            <a href="{{ route('register') }}" class="block w-full py-3.5 rounded-lg text-center font-bold text-base transition-all
                                {{ $isPopular 
                                    ? 'bg-slate-900 text-white hover:bg-slate-800 shadow-lg shadow-indigo-200/50' 
                                    : 'bg-white border-2 border-slate-900 text-slate-900 hover:bg-slate-50' 
                                }}">
                                {{ __('14 Gün Ücretsiz Deneyim') }}
                            </a>
                            <p class="text-[10px] text-slate-400 text-center mt-3 leading-tight" x-show="billing === 'yearly'">
                                {{ __('Yenileme ücreti:') }} <span x-text="'₺' + (billing === 'monthly' ? '{{ $p->price_monthly }}' : '{{ $p->price_yearly }}') + '/dönem'"></span>
                            </p>
                        </div>

                        <!-- Features -->
                        <div class="space-y-4 pt-6 mt-auto">
                            @php $limits = $p->limits ?? []; @endphp
                            
                            <!-- Key Limits -->
                            <ul class="space-y-3 pb-4 border-b border-slate-100">
                                <li class="flex items-center gap-3">
                                    <i class='bx bx-globe text-slate-400 text-lg shrink-0'></i>
                                    <span class="text-xs font-bold text-slate-700">
                                        <span class="font-extrabold text-slate-900">{{ $limits['proposal_monthly'] == -1 ? 'Sınırsız' : $limits['proposal_monthly'] }}</span> {{ __('Teklif') }}
                                    </span>
                                </li>
                                <li class="flex items-center gap-3">
                                    <i class='bx bx-user text-slate-400 text-lg shrink-0'></i>
                                    <span class="text-xs font-bold text-slate-700">
                                        <span class="font-extrabold text-slate-900">{{ $limits['user_count'] == -1 ? 'Sınırsız' : $limits['user_count'] }}</span> {{ __('Kullanıcı') }}
                                    </span>
                                </li>
                                <li class="flex items-center gap-3">
                                    <i class='bx bx-hdd text-slate-400 text-lg shrink-0'></i>
                                    <span class="text-xs font-bold text-slate-700">
                                        <span class="font-extrabold text-slate-900">{{ $limits['customer_count'] == -1 ? 'Sınırsız' : $limits['customer_count'] }}</span> {{ __('Müşteri') }}
                                    </span>
                                </li>
                            </ul>

                            <div>
                                <h4 class="text-[11px] font-extrabold text-slate-900 uppercase tracking-widest mb-3">{{ __('Özellikler:') }}</h4>
                                
                                <ul class="space-y-3" x-data="{ expanded: false }">
                                    @php 
                                        $featureLabels = \App\Models\Plan::getAvailableFeatures();
                                        $planFeatures = $p->features ?? [];
                                    @endphp

                                    @foreach($planFeatures as $loopIndex => $featureKey)
                                        @if(isset($featureLabels[$featureKey]))
                                            <li class="flex items-start gap-3"
                                                x-show="expanded || {{ $loopIndex }} < 5"
                                                x-transition
                                            >
                                                <i class='bx bxs-check-circle text-emerald-500 text-base shrink-0 mt-0.5'></i>
                                                <span class="text-xs font-medium text-slate-600 leading-tight">
                                                    {{ $featureLabels[$featureKey] }}
                                                    @if(in_array($featureKey, ['ai_creation', 'netgsm_integration']))
                                                        <span class="ml-1 text-[9px] bg-indigo-50 text-indigo-600 border border-indigo-100 px-1.5 py-0.5 rounded font-bold uppercase">{{ __('YENİ') }}</span>
                                                    @endif
                                                </span>
                                            </li>
                                        @endif
                                    @endforeach

                                    @if(count($planFeatures) > 5)
                                        <li class="pt-2">
                                            <button type="button" @click="expanded = !expanded" class="flex items-center gap-1 text-xs font-bold text-slate-400 hover:text-indigo-600 transition-colors">
                                                <span x-text="expanded ? '{{ __('Daha az göster') }}' : '{{ __('Tüm özellikleri gör') }}'"></span>
                                                <i class='bx' :class="expanded ? 'bx-chevron-up' : 'bx-chevron-down'"></i>
                                            </button>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                        
                    </div>
                </div>
               @endforeach
            </div>
        </div>
    <!-- Testimonials Section (Minimal) -->
    <!-- Testimonials (Infinite Marquee) -->
    <style>
        @keyframes loop-scroll {
            from { transform: translateX(0); }
            to { transform: translateX(-100%); }
        }
        .animate-loop-scroll {
            animation: loop-scroll 40s linear infinite;
        }
    </style>
    <section id="testimonials" class="py-24 bg-slate-50 overflow-hidden">
        <div class="container mx-auto px-6 mb-16">
            <div class="text-center max-w-3xl mx-auto">
                <div class="flex items-center justify-center gap-2 mb-6">
                    <span class="font-bold text-slate-900">Mükemmel</span>
                    <div class="flex text-emerald-500 text-lg">
                        <i class='bx bxs-star'></i><i class='bx bxs-star'></i><i class='bx bxs-star'></i><i class='bx bxs-star'></i><i class='bx bxs-star'></i>
                    </div>
                </div>
                <h2 class="text-4xl md:text-5xl font-medium text-slate-900 tracking-tight leading-[1.2] mb-6">
                    Onlar çevrimiçi dünyada <br>
                    başarıya ulaştı, şimdi sıra sizde
                </h2>
            </div>
        </div>

        <!-- Marquee Slider -->
        <div class="flex overflow-hidden space-x-8 pb-10">
            <!-- First Set -->
            <div class="flex space-x-8 animate-loop-scroll">
                
                <!-- Card 1 -->
                <div class="w-[400px] flex-shrink-0 bg-white p-8 rounded-[2rem] shadow-sm hover:shadow-xl transition-shadow border border-slate-100 flex flex-col justify-between h-[420px]">
                    <div>
                        <div class="w-12 h-12 bg-indigo-600 rounded-xl flex items-center justify-center text-white mb-8 shadow-lg shadow-indigo-200">
                            <i class='bx bxs-quote-alt-left text-2xl'></i>
                        </div>
                        <p class="text-slate-800 font-medium text-lg leading-relaxed mb-6">
                            "Karmaşık CRM programlarından bıkmıştık. Fiyera'nın sadeliği ve hızı ekibimiz için tam bir nefes oldu. Artık herkes işine odaklanabiliyor."
                        </p>
                        <div class="flex flex-wrap gap-2 mb-6">
                             <span class="px-3 py-1 bg-slate-50 text-slate-600 text-xs font-bold rounded-full border border-slate-100">CRM</span>
                             <span class="px-3 py-1 bg-slate-50 text-slate-600 text-xs font-bold rounded-full border border-slate-100">Ekip Yönetimi</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-4 pt-6 border-t border-slate-50">
                        <img src="https://i.pravatar.cc/100?img=33" class="w-10 h-10 rounded-full object-cover">
                        <div>
                            <div class="font-bold text-slate-900 text-sm">Ahmet Yılmaz</div>
                            <div class="text-slate-400 text-xs font-medium">CEO, Yılmaz Tech</div>
                        </div>
                    </div>
                </div>

                <!-- Card 2 -->
                <div class="w-[400px] flex-shrink-0 bg-white p-8 rounded-[2rem] shadow-sm hover:shadow-xl transition-shadow border border-slate-100 flex flex-col justify-between h-[420px]">
                    <div>
                        <div class="w-12 h-12 bg-rose-500 rounded-xl flex items-center justify-center text-white mb-8 shadow-lg shadow-rose-200">
                             <i class='bx bxs-file-pdf text-2xl'></i>
                        </div>
                        <p class="text-slate-800 font-medium text-lg leading-relaxed mb-6">
                            "Teklif hazırlamak eskiden saatlerimizi alırdı. Şimdi şablonlar sayesinde dakikalar içinde profesyonel PDF'ler oluşturup müşteriye sunuyoruz."
                        </p>
                        <div class="flex flex-wrap gap-2 mb-6">
                             <span class="px-3 py-1 bg-slate-50 text-slate-600 text-xs font-bold rounded-full border border-slate-100">Teklifler</span>
                             <span class="px-3 py-1 bg-slate-50 text-slate-600 text-xs font-bold rounded-full border border-slate-100">Otomasyon</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-4 pt-6 border-t border-slate-50">
                        <img src="https://i.pravatar.cc/100?img=47" class="w-10 h-10 rounded-full object-cover">
                        <div>
                            <div class="font-bold text-slate-900 text-sm">Elif Demir</div>
                            <div class="text-slate-400 text-xs font-medium">Satış Müdürü</div>
                        </div>
                    </div>
                </div>

                <!-- Card 3 -->
                <div class="w-[400px] flex-shrink-0 bg-white p-8 rounded-[2rem] shadow-sm hover:shadow-xl transition-shadow border border-slate-100 flex flex-col justify-between h-[420px]">
                    <div>
                        <div class="w-12 h-12 bg-emerald-500 rounded-xl flex items-center justify-center text-white mb-8 shadow-lg shadow-emerald-200">
                            <i class='bx bxs-mobile text-2xl'></i>
                        </div>
                        <p class="text-slate-800 font-medium text-lg leading-relaxed mb-6">
                            "Mobil uyumluluğu sayesinde sahadayken bile işimi yönetebiliyorum. Müşterinin yanındayken stok bakmak ve fiyat vermek paha biçilemez."
                        </p>
                        <div class="flex flex-wrap gap-2 mb-6">
                             <span class="px-3 py-1 bg-slate-50 text-slate-600 text-xs font-bold rounded-full border border-slate-100">Mobil</span>
                             <span class="px-3 py-1 bg-slate-50 text-slate-600 text-xs font-bold rounded-full border border-slate-100">Saha Satış</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-4 pt-6 border-t border-slate-50">
                        <img src="https://i.pravatar.cc/100?img=12" class="w-10 h-10 rounded-full object-cover">
                        <div>
                            <div class="font-bold text-slate-900 text-sm">Murat Kaya</div>
                            <div class="text-slate-400 text-xs font-medium">Saha Operasyon</div>
                        </div>
                    </div>
                </div>

                <!-- Card 4 (New) -->
                <div class="w-[400px] flex-shrink-0 bg-white p-8 rounded-[2rem] shadow-sm hover:shadow-xl transition-shadow border border-slate-100 flex flex-col justify-between h-[420px]">
                     <div>
                        <div class="w-12 h-12 bg-amber-500 rounded-xl flex items-center justify-center text-white mb-8 shadow-lg shadow-amber-200">
                            <i class='bx bxs-bar-chart-alt-2 text-2xl'></i>
                        </div>
                        <p class="text-slate-800 font-medium text-lg leading-relaxed mb-6">
                            "Fiyera'nın raporlama araçları sayesinde hangi ay ne kadar büyüdüğümüzü net görebiliyoruz. Önümüzü görmek bize güven veriyor."
                        </p>
                        <div class="flex flex-wrap gap-2 mb-6">
                             <span class="px-3 py-1 bg-slate-50 text-slate-600 text-xs font-bold rounded-full border border-slate-100">Raporlama</span>
                             <span class="px-3 py-1 bg-slate-50 text-slate-600 text-xs font-bold rounded-full border border-slate-100">Finans</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-4 pt-6 border-t border-slate-50">
                        <img src="https://i.pravatar.cc/100?img=59" class="w-10 h-10 rounded-full object-cover">
                        <div>
                            <div class="font-bold text-slate-900 text-sm">Selin Yurt</div>
                            <div class="text-slate-400 text-xs font-medium">Finans Direktörü</div>
                        </div>
                    </div>
                </div>

                 <!-- Card 5 (New) -->
                <div class="w-[400px] flex-shrink-0 bg-white p-8 rounded-[2rem] shadow-sm hover:shadow-xl transition-shadow border border-slate-100 flex flex-col justify-between h-[420px]">
                    <div>
                        <div class="w-12 h-12 bg-sky-500 rounded-xl flex items-center justify-center text-white mb-8 shadow-lg shadow-sky-200">
                             <i class='bx bxs-check-shield text-2xl'></i>
                        </div>
                        <p class="text-slate-800 font-medium text-lg leading-relaxed mb-6">
                            "Müşteri verilerimizin güvende olduğunu bilmek çok önemli. Fiyera'nın güvenlik önlemleri ve yedekleme sistemi sayesinde içimiz çok rahat."
                        </p>
                        <div class="flex flex-wrap gap-2 mb-6">
                             <span class="px-3 py-1 bg-slate-50 text-slate-600 text-xs font-bold rounded-full border border-slate-100">Güvenlik</span>
                             <span class="px-3 py-1 bg-slate-50 text-slate-600 text-xs font-bold rounded-full border border-slate-100">Veri Koruma</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-4 pt-6 border-t border-slate-50">
                        <img src="https://i.pravatar.cc/100?img=68" class="w-10 h-10 rounded-full object-cover">
                        <div>
                            <div class="font-bold text-slate-900 text-sm">Caner Erkin</div>
                            <div class="text-slate-400 text-xs font-medium">IT Müdürü</div>
                        </div>
                    </div>
                </div>

            </div>

             <!-- Second Set (Clone for Loop) -->
            <div class="flex space-x-8 animate-loop-scroll" aria-hidden="true">
                
                <!-- Card 1 Clone -->
                <div class="w-[400px] flex-shrink-0 bg-white p-8 rounded-[2rem] shadow-sm hover:shadow-xl transition-shadow border border-slate-100 flex flex-col justify-between h-[420px]">
                    <div>
                        <div class="w-12 h-12 bg-indigo-600 rounded-xl flex items-center justify-center text-white mb-8 shadow-lg shadow-indigo-200">
                            <i class='bx bxs-quote-alt-left text-2xl'></i>
                        </div>
                        <p class="text-slate-800 font-medium text-lg leading-relaxed mb-6">
                            "Karmaşık CRM programlarından bıkmıştık. Fiyera'nın sadeliği ve hızı ekibimiz için tam bir nefes oldu. Artık herkes işine odaklanabiliyor."
                        </p>
                         <div class="flex flex-wrap gap-2 mb-6">
                             <span class="px-3 py-1 bg-slate-50 text-slate-600 text-xs font-bold rounded-full border border-slate-100">CRM</span>
                             <span class="px-3 py-1 bg-slate-50 text-slate-600 text-xs font-bold rounded-full border border-slate-100">Ekip Yönetimi</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-4 pt-6 border-t border-slate-50">
                        <img src="https://i.pravatar.cc/100?img=33" class="w-10 h-10 rounded-full object-cover">
                        <div>
                            <div class="font-bold text-slate-900 text-sm">Ahmet Yılmaz</div>
                            <div class="text-slate-400 text-xs font-medium">CEO, Yılmaz Tech</div>
                        </div>
                    </div>
                </div>

                <!-- Card 2 Clone -->
                <div class="w-[400px] flex-shrink-0 bg-white p-8 rounded-[2rem] shadow-sm hover:shadow-xl transition-shadow border border-slate-100 flex flex-col justify-between h-[420px]">
                     <div>
                        <div class="w-12 h-12 bg-rose-500 rounded-xl flex items-center justify-center text-white mb-8 shadow-lg shadow-rose-200">
                             <i class='bx bxs-file-pdf text-2xl'></i>
                        </div>
                        <p class="text-slate-800 font-medium text-lg leading-relaxed mb-6">
                            "Teklif hazırlamak eskiden saatlerimizi alırdı. Şimdi şablonlar sayesinde dakikalar içinde profesyonel PDF'ler oluşturup müşteriye sunuyoruz."
                        </p>
                        <div class="flex flex-wrap gap-2 mb-6">
                             <span class="px-3 py-1 bg-slate-50 text-slate-600 text-xs font-bold rounded-full border border-slate-100">Teklifler</span>
                             <span class="px-3 py-1 bg-slate-50 text-slate-600 text-xs font-bold rounded-full border border-slate-100">Otomasyon</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-4 pt-6 border-t border-slate-50">
                        <img src="https://i.pravatar.cc/100?img=47" class="w-10 h-10 rounded-full object-cover">
                        <div>
                            <div class="font-bold text-slate-900 text-sm">Elif Demir</div>
                            <div class="text-slate-400 text-xs font-medium">Satış Müdürü</div>
                        </div>
                    </div>
                </div>

                <!-- Card 3 Clone -->
                <div class="w-[400px] flex-shrink-0 bg-white p-8 rounded-[2rem] shadow-sm hover:shadow-xl transition-shadow border border-slate-100 flex flex-col justify-between h-[420px]">
                     <div>
                        <div class="w-12 h-12 bg-emerald-500 rounded-xl flex items-center justify-center text-white mb-8 shadow-lg shadow-emerald-200">
                            <i class='bx bxs-mobile text-2xl'></i>
                        </div>
                        <p class="text-slate-800 font-medium text-lg leading-relaxed mb-6">
                            "Mobil uyumluluğu sayesinde sahadayken bile işimi yönetebiliyorum. Müşterinin yanındayken stok bakmak ve fiyat vermek paha biçilemez."
                        </p>
                        <div class="flex flex-wrap gap-2 mb-6">
                             <span class="px-3 py-1 bg-slate-50 text-slate-600 text-xs font-bold rounded-full border border-slate-100">Mobil</span>
                             <span class="px-3 py-1 bg-slate-50 text-slate-600 text-xs font-bold rounded-full border border-slate-100">Saha Satış</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-4 pt-6 border-t border-slate-50">
                        <img src="https://i.pravatar.cc/100?img=12" class="w-10 h-10 rounded-full object-cover">
                        <div>
                            <div class="font-bold text-slate-900 text-sm">Murat Kaya</div>
                            <div class="text-slate-400 text-xs font-medium">Saha Operasyon</div>
                        </div>
                    </div>
                </div>

                <!-- Card 4 Clone -->
                <div class="w-[400px] flex-shrink-0 bg-white p-8 rounded-[2rem] shadow-sm hover:shadow-xl transition-shadow border border-slate-100 flex flex-col justify-between h-[420px]">
                    <div>
                        <div class="w-12 h-12 bg-amber-500 rounded-xl flex items-center justify-center text-white mb-8 shadow-lg shadow-amber-200">
                            <i class='bx bxs-bar-chart-alt-2 text-2xl'></i>
                        </div>
                        <p class="text-slate-800 font-medium text-lg leading-relaxed mb-6">
                            "Fiyera'nın raporlama araçları sayesinde hangi ay ne kadar büyüdüğümüzü net görebiliyoruz. Önümüzü görmek bize güven veriyor."
                        </p>
                        <div class="flex flex-wrap gap-2 mb-6">
                             <span class="px-3 py-1 bg-slate-50 text-slate-600 text-xs font-bold rounded-full border border-slate-100">Raporlama</span>
                             <span class="px-3 py-1 bg-slate-50 text-slate-600 text-xs font-bold rounded-full border border-slate-100">Finans</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-4 pt-6 border-t border-slate-50">
                        <img src="https://i.pravatar.cc/100?img=59" class="w-10 h-10 rounded-full object-cover">
                        <div>
                            <div class="font-bold text-slate-900 text-sm">Selin Yurt</div>
                            <div class="text-slate-400 text-xs font-medium">Finans Direktörü</div>
                        </div>
                    </div>
                </div>

                 <!-- Card 5 Clone -->
                <div class="w-[400px] flex-shrink-0 bg-white p-8 rounded-[2rem] shadow-sm hover:shadow-xl transition-shadow border border-slate-100 flex flex-col justify-between h-[420px]">
                    <div>
                        <div class="w-12 h-12 bg-sky-500 rounded-xl flex items-center justify-center text-white mb-8 shadow-lg shadow-sky-200">
                             <i class='bx bxs-check-shield text-2xl'></i>
                        </div>
                        <p class="text-slate-800 font-medium text-lg leading-relaxed mb-6">
                            "Müşteri verilerimizin güvende olduğunu bilmek çok önemli. Fiyera'nın güvenlik önlemleri ve yedekleme sistemi sayesinde içimiz çok rahat."
                        </p>
                        <div class="flex flex-wrap gap-2 mb-6">
                             <span class="px-3 py-1 bg-slate-50 text-slate-600 text-xs font-bold rounded-full border border-slate-100">Güvenlik</span>
                             <span class="px-3 py-1 bg-slate-50 text-slate-600 text-xs font-bold rounded-full border border-slate-100">Veri Koruma</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-4 pt-6 border-t border-slate-50">
                        <img src="https://i.pravatar.cc/100?img=68" class="w-10 h-10 rounded-full object-cover">
                        <div>
                            <div class="font-bold text-slate-900 text-sm">Caner Erkin</div>
                            <div class="text-slate-400 text-xs font-medium">IT Müdürü</div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

   

    <!-- FAQ Section (Clean Minimal Layout) -->
    <section id="faq" class="py-24 bg-white">
        <div class="container mx-auto px-6 lg:px-12">
            <div class="flex flex-col lg:flex-row gap-16 lg:gap-32">
                
                <!-- Left: Sticky Header -->
                <div class="lg:w-1/3">
                    <div class="lg:sticky lg:top-32">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-100 border border-slate-200 text-slate-600 text-[11px] font-bold uppercase tracking-wider mb-8">
                            <i class='bx bx-question-mark text-sm'></i> {{ __('Merak Edilenler') }}
                        </div>
                        <h2 class="text-4xl font-black text-slate-900 leading-[1.1] mb-6 tracking-tight">
                            {{ __('Aklınıza takılan sorular mı var?') }}
                        </h2>
                        <p class="text-lg text-slate-500 mb-10 font-medium leading-relaxed">
                            {{ __('Teklif süreçlerinizi dijitalleştirmekle ilgili tüm detayları burada bulabilirsiniz. Başka sorunuz varsa bize her an yazabilirsiniz.') }}
                        </p>
                        
                        <div class="flex flex-col gap-4">
                            <a href="mailto:destek@fiyera.co" class="group flex items-center gap-4 p-4 rounded-2xl border border-slate-100 hover:border-indigo-100 hover:shadow-lg hover:shadow-indigo-50 transition-all bg-slate-50 hover:bg-white">
                                <div class="w-10 h-10 rounded-full bg-indigo-600 text-white flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
                                    <i class='bx bx-support'></i>
                                </div>
                                <div>
                                    <div class="font-bold text-slate-900 text-sm">{{ __('Destek Ekibine Yazın') }}</div>
                                    <div class="text-xs text-slate-400 font-medium md:group-hover:text-indigo-600 transition-colors">destek@fiyera.co</div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Right: Accordion List -->
                <div class="lg:w-2/3 divide-y divide-slate-100" x-data="{ active: 0 }">
                    @php
                    $faqs = [
                        ['q' => 'Fiyera ile teklif hazırlamak ne kadar sürer?', 'a' => 'Sürükle-bırak editörümüz ve hazır şablon kütüphanemiz sayesinde, sıfırdan başlayan bir teklifi ortalama 2 dakika içinde hazırlayıp müşterinize gönderebilirsiniz. Excel ile saatler harcamanıza gerek kalmaz.'],
                        ['q' => 'Müşterim teklifi nasıl imzalar? Üye olması gerekir mi?', 'a' => 'Hayır, müşterinizin üye olmasına gerek yoktur. Gönderdiğiniz linke tıkladığında teklifi görüntüler ve "Onayla" butonuna basarak parmağıyla (mobilde) veya mouse ile dijital imzasını atabilir.'],
                        ['q' => 'Teklifimin görüntülendiğini anlayabilir miyim?', 'a' => 'Kesinlikle. Müşteriniz teklif linkini açtığı anda size anlık bildirim (push notification) ve e-posta gelir. Böylece müşterinizi aramak için en doğru zamanı yakalarsınız.'],
                        ['q' => 'Mevcut ürün ve fiyat listemi içeri aktarabilir miyim?', 'a' => 'Evet. Binlerce kalemden oluşan ürün/hizmet listenizi Excel veya XML formatında tek tıkla sisteme yükleyebilirsiniz. Teklif hazırlarken bu listeden akıllı arama yapabilirsiniz.'],
                        ['q' => 'Oluşturulan teklifler yasal olarak geçerli midir?', 'a' => 'Evet. Fiyera üzerinden alınan dijital onaylar, müşterinin IP adresi, zaman damgası ve imza verisiyle birlikte loglanır. Bu kayıtlar, sözleşmesel uyuşmazlıklarda delil niteliği taşır.'],
                        ['q' => 'Yurt dışına dövizli teklif verebilir miyim?', 'a' => 'Sistemimiz çoklu para birimini destekler. Dolar, Euro, Sterlin gibi dilediğiniz para biriminde teklif hazırlayabilir, kur çevrimlerini otomatik yapabilirsiniz.'],
                        ['q' => 'Kendi kurumsal tasarımımı kullanabilir miyim?', 'a' => 'Şablon motorumuz ile logonuzu, kurumsal renklerinizi ve yazı fontlarınızı sisteme tanımlayabilirsiniz. Böylece çıkan her teklif %100 markanızı yansıtır.'],
                    ];
                    @endphp

                    @foreach($faqs as $i => $item)
                        <div class="py-6 group">
                            <button @click="active = active === {{ $i }} ? null : {{ $i }}" class="w-full flex items-center justify-between text-left focus:outline-none">
                                <span class="text-lg font-bold text-slate-800 group-hover:text-indigo-600 transition-colors pr-8">{{ $item['q'] }}</span>
                                <span class="relative flex-shrink-0 w-8 h-8 rounded-full bg-slate-50 flex items-center justify-center border border-slate-100 group-hover:border-indigo-200 group-hover:bg-indigo-50 transition-all duration-300">
                                    <!-- Plus Icon -->
                                    <i class='bx bx-plus text-slate-400 group-hover:text-indigo-600 text-xl transition-transform duration-300 absolute' 
                                       :class="active === {{ $i }} ? 'rotate-90 opacity-0' : 'rotate-0 opacity-100'"></i>
                                    <!-- Minus Icon -->
                                    <i class='bx bx-minus text-indigo-600 text-xl transition-transform duration-300 absolute'
                                       :class="active === {{ $i }} ? 'rotate-0 opacity-100' : '-rotate-90 opacity-0'"></i>
                                </span>
                            </button>
                            <div x-show="active === {{ $i }}" x-collapse x-cloak>
                                <div class="pt-4 pb-2 text-slate-500 font-medium leading-relaxed pl-1 border-l-2 border-indigo-100 ml-1">
                                    {{ $item['a'] }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <!-- Modern Footer CTA & Links Section (Light Theme) -->
    <footer class="pt-24 pb-12 bg-white border-t border-slate-100">
        <div class="container mx-auto px-6 lg:px-12">
            
            <!-- Top CTA Section -->
            <div class="flex flex-col lg:flex-row items-start lg:items-end justify-between gap-10 mb-24">
                <div class="max-w-2xl">
                    <div class="flex items-center gap-2 mb-6">
                        <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center shadow-lg shadow-indigo-200">
                            <i class='bx bxs-bolt text-white text-xl'></i>
                        </div>
                        <span class="font-extrabold text-xl tracking-tight text-slate-950">fiyera<span class="text-indigo-600">.co</span></span>
                    </div>

                    <h2 class="text-4xl md:text-5xl font-black text-slate-900 tracking-tight leading-[1.1] mb-6">
                        İş süreçlerinizi <br>
                        <span class="text-indigo-600">güçlendirin.</span>
                    </h2>
                    <p class="text-lg text-slate-500 font-medium">Saniyeler içinde başlayın — kredi kartı gerekmez.</p>
                </div>
                <div class="flex flex-col sm:flex-row items-center gap-4 w-full lg:w-auto">
                    <a href="{{ route('register') }}" class="w-full sm:w-auto px-8 py-4 bg-indigo-600 text-white rounded-xl font-bold shadow-xl shadow-indigo-600/20 hover:bg-indigo-700 hover:-translate-y-1 transition-all text-center">
                        {{ __('Ücretsiz Başla') }}
                    </a>
                    <a href="#" class="w-full sm:w-auto px-8 py-4 bg-white text-slate-700 border border-slate-200 rounded-xl font-bold hover:border-indigo-200 hover:text-indigo-600 transition-all text-center">
                        {{ __('Demo Talep Et') }}
                    </a>
                </div>
            </div>

            <!-- Links Grid -->
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-8 lg:gap-12 mb-20">
                
                <!-- Col 1 -->
                <div class="flex flex-col gap-4">
                    <h4 class="font-bold text-slate-900">{{ __('Ürün') }}</h4>
                    <a href="#" class="text-sm font-medium text-slate-500 hover:text-indigo-600 transition-colors">{{ __('Teklif Yönetimi') }}</a>
                    <a href="#" class="text-sm font-medium text-slate-500 hover:text-indigo-600 transition-colors">{{ __('Müşteri CRM') }}</a>
                    <a href="#" class="text-sm font-medium text-slate-500 hover:text-indigo-600 transition-colors">{{ __('Stok & Hizmet') }}</a>
                    <a href="#" class="text-sm font-medium text-slate-500 hover:text-indigo-600 transition-colors">{{ __('Raporlama') }}</a>
                    <a href="#" class="text-sm font-medium text-slate-500 hover:text-indigo-600 transition-colors">{{ __('Mobil Uygulama') }}</a>
                </div>

                <!-- Col 2 -->
                <div class="flex flex-col gap-4">
                    <h4 class="font-bold text-slate-900">{{ __('Çözümler') }}</h4>
                    <a href="#" class="text-sm font-medium text-slate-500 hover:text-indigo-600 transition-colors">{{ __('Freelancerlar') }}</a>
                    <a href="#" class="text-sm font-medium text-slate-500 hover:text-indigo-600 transition-colors">{{ __('Ajanslar') }}</a>
                    <a href="#" class="text-sm font-medium text-slate-500 hover:text-indigo-600 transition-colors">{{ __('Satış Ekipleri') }}</a>
                    <a href="#" class="text-sm font-medium text-slate-500 hover:text-indigo-600 transition-colors">{{ __('Danışmanlar') }}</a>
                    <a href="#" class="text-sm font-medium text-slate-500 hover:text-indigo-600 transition-colors">{{ __('KOBİ\'ler') }}</a>
                </div>

                <!-- Col 3 -->
                <div class="flex flex-col gap-4">
                    <h4 class="font-bold text-slate-900">{{ __('Kaynaklar') }}</h4>
                    <a href="#" class="text-sm font-medium text-slate-500 hover:text-indigo-600 transition-colors">{{ __('Yardım Merkezi') }}</a>
                    <a href="#" class="text-sm font-medium text-slate-500 hover:text-indigo-600 transition-colors">{{ __('Blog') }}</a>
                    <a href="#" class="text-sm font-medium text-slate-500 hover:text-indigo-600 transition-colors">{{ __('Topluluk') }}</a>
                    <a href="#" class="text-sm font-medium text-slate-500 hover:text-indigo-600 transition-colors">{{ __('Geliştiriciler (API)') }}</a>
                    <a href="#" class="text-sm font-medium text-slate-500 hover:text-indigo-600 transition-colors">{{ __('Sistem Durumu') }}</a>
                </div>

                 <!-- Col 4 -->
                 <div class="flex flex-col gap-4">
                    <h4 class="font-bold text-slate-900">{{ __('Şirket') }}</h4>
                    <a href="#" class="text-sm font-medium text-slate-500 hover:text-indigo-600 transition-colors">{{ __('Hakkımızda') }}</a>
                    <a href="#" class="text-sm font-medium text-slate-500 hover:text-indigo-600 transition-colors">{{ __('Kariyer') }} <span class="text-[10px] font-bold text-indigo-600 bg-indigo-50 px-1.5 py-0.5 rounded ml-1">{{ __('Alım Var!') }}</span></a>
                    <a href="#" class="text-sm font-medium text-slate-500 hover:text-indigo-600 transition-colors">{{ __('Medya Kiti') }}</a>
                    <a href="#" class="text-sm font-medium text-slate-500 hover:text-indigo-600 transition-colors">{{ __('İletişim') }}</a>
                    <a href="#" class="text-sm font-medium text-slate-500 hover:text-indigo-600 transition-colors">{{ __('Partner Programı') }}</a>
                </div>

                 <!-- Col 5 -->
                 <div class="flex flex-col gap-4">
                    <h4 class="font-bold text-slate-900">{{ __('Yasal') }}</h4>
                    <a href="#" class="text-sm font-medium text-slate-500 hover:text-indigo-600 transition-colors">{{ __('Gizlilik Politikası') }}</a>
                    <a href="#" class="text-sm font-medium text-slate-500 hover:text-indigo-600 transition-colors">{{ __('Kullanım Şartları') }}</a>
                    <a href="#" class="text-sm font-medium text-slate-500 hover:text-indigo-600 transition-colors">{{ __('Çerez Politikası') }}</a>
                    <a href="#" class="text-sm font-medium text-slate-500 hover:text-indigo-600 transition-colors">{{ __('KVKK') }}</a>
                    <a href="#" class="text-sm font-medium text-slate-500 hover:text-indigo-600 transition-colors">{{ __('Güvenlik') }}</a>
                </div>
            </div>

            <!-- Downloads & Socials -->
            <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-8 py-8 border-t border-b border-slate-100 mb-8">
                
                <div class="flex flex-col sm:flex-row gap-6 items-start sm:items-center">
                    <span class="text-sm font-bold text-slate-900">{{ __('Uygulamayı İndir') }}</span>
                    <div class="flex flex-wrap gap-3">
                        <button class="flex items-center gap-2 px-4 py-2 bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-lg transition-colors group">
                            <i class='bx bxl-apple text-xl text-slate-700 group-hover:text-black'></i>
                            <div class="text-left">
                                <p class="text-[9px] font-bold text-slate-400 leading-none">Download on the</p>
                                <p class="text-xs font-bold text-slate-700 group-hover:text-black">App Store</p>
                            </div>
                        </button>
                        <button class="flex items-center gap-2 px-4 py-2 bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-lg transition-colors group">
                           <i class='bx bxl-play-store text-xl text-slate-700 group-hover:text-black'></i>
                            <div class="text-left">
                                <p class="text-[9px] font-bold text-slate-400 leading-none">GET IT ON</p>
                                <p class="text-xs font-bold text-slate-700 group-hover:text-black">Google Play</p>
                            </div>
                        </button>
                        <button class="flex items-center gap-2 px-4 py-2 bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-lg transition-colors group">
                             <i class='bx bxl-chrome text-xl text-slate-700 group-hover:text-black'></i>
                             <span class="text-xs font-bold text-slate-700 group-hover:text-black">Chrome Extension</span>
                        </button>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <a href="#" class="w-10 h-10 flex items-center justify-center rounded-full bg-slate-50 text-slate-500 hover:bg-slate-900 hover:text-white transition-all">
                        <i class='bx bxl-twitter text-xl'></i>
                    </a>
                    <a href="#" class="w-10 h-10 flex items-center justify-center rounded-full bg-slate-50 text-slate-500 hover:bg-rose-500 hover:text-white transition-all">
                        <i class='bx bxl-instagram text-xl'></i>
                    </a>
                    <a href="#" class="w-10 h-10 flex items-center justify-center rounded-full bg-slate-50 text-slate-500 hover:bg-blue-700 hover:text-white transition-all">
                        <i class='bx bxl-linkedin text-xl'></i>
                    </a>
                    <a href="#" class="w-10 h-10 flex items-center justify-center rounded-full bg-slate-50 text-slate-500 hover:bg-red-600 hover:text-white transition-all">
                        <i class='bx bxl-youtube text-xl'></i>
                    </a>
                </div>

            </div>

            <!-- Bottom Legal -->
            <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                 <!-- Language Switcher -->
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" class="flex items-center gap-2 text-sm font-bold text-slate-600 hover:text-slate-900 transition-colors">
                        <i class='bx bx-globe text-lg'></i>
                        @if(app()->getLocale() == 'tr')
                            <span>Türkçe</span>
                        @else
                            <span>English</span>
                        @endif
                        <i class='bx bx-chevron-down'></i>
                    </button>
                    <div x-show="open" 
                            @click.away="open = false"
                            class="absolute bottom-full left-0 mb-2 w-32 bg-white rounded-lg shadow-xl border border-slate-100 overflow-hidden py-1 z-50">
                        <a href="{{ route('locale.switch', 'tr') }}" class="block px-4 py-2 text-xs font-medium hover:bg-slate-50 transition-colors text-slate-700">
                            Türkçe (TR)
                        </a>
                        <a href="{{ route('locale.switch', 'en') }}" class="block px-4 py-2 text-xs font-medium hover:bg-slate-50 transition-colors text-slate-700">
                            English (EN)
                        </a>
                    </div>
                </div>

                <div class="flex flex-col md:flex-row items-center gap-4 md:gap-8">
                     <p class="text-xs font-medium text-slate-400">&copy; {{ date('Y') }} Fiyera.co Inc.</p>
                     <div class="flex gap-6">
                        <a href="#" class="text-xs font-bold text-slate-500 hover:text-slate-900 transition-colors">{{ __('Gizlilik') }}</a>
                        <a href="#" class="text-xs font-bold text-slate-500 hover:text-slate-900 transition-colors">{{ __('Şartlar') }}</a>
                        <a href="#" class="text-xs font-bold text-slate-500 hover:text-slate-900 transition-colors">{{ __('Site Haritası') }}</a>
                     </div>
                </div>
            </div>
            
        </div>
    </footer>

</body>
</html>

