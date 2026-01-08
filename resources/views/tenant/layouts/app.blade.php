<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard - {{ config('app.name', 'fiyera.co') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
        :root { --sidebar-width: 280px; }
        .sidebar-collapsed { --sidebar-width: 80px; }

        body { font-family: 'Plus Jakarta Sans', sans-serif; letter-spacing: -0.01em; }
        .sidebar-link-active { background: #f8fafc; color: #0f172a; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
        .glass-card { background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(12px); }
        
        /* Sidebar Transitions */
        .sidebar-transition { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        
        /* Base Sidebar State */
        .sidebar-content { overflow-y: auto; overflow-x: hidden; scrollbar-width: none; -ms-overflow-style: none; }
        .sidebar-content::-webkit-scrollbar { display: none; }
        
        /* Collapsed State Overrides */
        .sidebar-collapsed .sidebar-content { overflow: visible !important; }
        .sidebar-collapsed #sidebar { overflow: visible !important; }
        
        .sidebar-collapsed .sidebar-item-text { 
            opacity: 0; 
            width: 0; 
            margin: 0; 
            position: absolute; 
            pointer-events: none;
            white-space: nowrap;
        }
        
        .sidebar-collapsed .sidebar-section-label { opacity: 0; height: 0; margin: 0; overflow: hidden; }
        .sidebar-collapsed .sidebar-brand-text { opacity: 0; width: 0; display: inline-block; overflow: hidden; white-space: nowrap; }
        .sidebar-collapsed .sidebar-promo { display: none; }
        .sidebar-collapsed .sidebar-profile-info { display: none; }
        .sidebar-collapsed .sidebar-profile-dots { display: none; }
        
        /* Tooltip behavior */
        .sidebar-collapsed .nav-link { justify-content: center; padding-left: 0; padding-right: 0; position: relative; }
        .sidebar-collapsed .nav-link i { font-size: 1.4rem; }
        
        /* Tooltip on Hover when Collapsed */
        .sidebar-collapsed .nav-link:hover .sidebar-item-text {
            display: flex !important;
            opacity: 1;
            width: auto;
            position: absolute;
            left: calc(100% + 15px);
            top: 50%;
            transform: translateY(-50%);
            background: #0f172a;
            color: white;
            padding: 8px 16px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 600;
            z-index: 9999;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.2);
            pointer-events: none;
        }

        .sidebar-collapsed .nav-link:hover .sidebar-item-text::before {
            content: '';
            position: absolute;
            left: -5px;
            top: 50%;
            transform: translateY(-50%);
            border-top: 5px solid transparent;
            border-bottom: 5px solid transparent;
            border-right: 5px solid #0f172a;
        }

        /* Toggle Button Collapsed Style */
        .sidebar-collapsed .sidebar-toggle-btn {
            position: absolute;
            right: -16px;
            top: 24px;
            background: white;
            border: 1px solid #f1f5f9;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            z-index: 100;
            width: 32px;
            height: 32px;
        }
    </style>
</head>
<body class="bg-[#fafafa] text-slate-950 font-medium" x-data="{ mobileSidebarOpen: false }">
    <div class="flex min-h-screen">
        <!-- Mobile Overlay -->
        <div x-show="mobileSidebarOpen" 
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-slate-900/50 z-[60] lg:hidden backdrop-blur-sm" 
             @click="mobileSidebarOpen = false" 
             x-cloak></div>

        <!-- Sidebar -->
        <aside id="sidebar" 
               :class="mobileSidebarOpen ? 'translate-x-0 shadow-2xl' : '-translate-x-full lg:translate-x-0 lg:shadow-none'"
               class="w-[280px] lg:w-[var(--sidebar-width)] bg-white flex flex-col fixed inset-y-0 left-0 h-full border-r border-gray-100 z-[70] sidebar-transition transition-transform duration-300 ease-in-out -translate-x-full lg:translate-x-0">
            <div class="p-6 flex flex-col h-full sidebar-content">
                <!-- Brand -->
                 <a href="{{ route('dashboard') }}">
                <div class="flex items-center justify-between mb-8 flex-shrink-0 relative">
                    <div class="flex items-center gap-2.5 px-2">
                        <div class="w-9 h-9 bg-slate-950 rounded-xl flex items-center justify-center shadow-lg shadow-slate-200 flex-shrink-0">
                            <i class='bx bxs-bolt text-white text-xl'></i>
                        </div>
                        <span class="font-extrabold text-xl tracking-tight text-slate-950 sidebar-brand-text sidebar-transition whitespace-nowrap">fiyera<span class="text-indigo-600">.co</span></span>
                    </div>
                    <button id="sidebarToggle" class="hidden lg:flex sidebar-toggle-btn w-8 h-8 items-center justify-center rounded-lg hover:bg-slate-50 text-slate-400 hover:text-slate-950 transition-all">
                        <i class='bx bx-chevron-left text-xl' id="toggle-icon"></i>
                    </button>
                    <!-- Mobile Close Button -->
                    <button @click="mobileSidebarOpen = false" class="lg:hidden w-8 h-8 flex items-center justify-center rounded-lg hover:bg-slate-50 text-slate-400 hover:text-slate-950 transition-all">
                        <i class='bx bx-x text-2xl'></i>
                    </button>
                </div>
                </a>

                <!-- Main Nav -->
                <div class="space-y-6 flex-1">
                    <div>
                        <p class="px-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-4 sidebar-section-label sidebar-transition">{{ __('Ana Menu') }}</p>
                        <nav class="space-y-1">
                            <a id="nav-dashboard" href="{{ route('dashboard') }}" class="nav-link flex items-center gap-3 px-4 py-2.5 text-sm {{ request()->routeIs('dashboard') ? 'sidebar-link-active' : 'text-slate-500 hover:text-slate-950 hover:bg-slate-50' }} rounded-xl transition-all duration-200">
                                <i class='bx bxs-dashboard text-xl'></i> 
                                <span class="sidebar-item-text sidebar-transition truncate">{{ __('Genel Bakış') }}</span>
                            </a>
                            <a id="nav-proposals" href="{{ route('proposals.index') }}" class="nav-link flex items-center gap-3 px-4 py-2.5 text-sm {{ request()->routeIs('proposals.*') ? 'sidebar-link-active' : 'text-slate-500 hover:text-slate-950 hover:bg-slate-50' }} rounded-xl transition-all duration-200">
                                <i class='bx bx-file text-xl'></i>
                                <span class="sidebar-item-text sidebar-transition truncate">{{ __('Teklifler') }}</span>
                            </a>
                            <a id="nav-customers" href="{{ route('customers.index') }}" class="nav-link flex items-center gap-3 px-4 py-2.5 text-sm {{ request()->routeIs('customers.index') ? 'sidebar-link-active' : 'text-slate-500 hover:text-slate-950 hover:bg-slate-50' }} rounded-xl transition-all duration-200">
                                <i class='bx bx-group text-xl'></i>
                                <span class="sidebar-item-text sidebar-transition truncate">{{ __('Müşteriler') }}</span>
                            </a>
                            <a id="nav-products" href="{{ route('products.index') }}" class="nav-link flex items-center gap-3 px-4 py-2.5 text-sm {{ request()->routeIs('products.*') ? 'sidebar-link-active' : 'text-slate-500 hover:text-slate-950 hover:bg-slate-50' }} rounded-xl transition-all duration-200">
                                <i class='bx bx-category text-xl'></i>
                                <span class="sidebar-item-text sidebar-transition truncate">{{ __('Ürün / Hizmetler') }}</span>
                            </a>
                            <a id="nav-reports" href="{{ route('reports.index') }}" class="nav-link flex items-center gap-3 px-4 py-2.5 text-sm {{ request()->routeIs('reports.*') ? 'sidebar-link-active' : 'text-slate-500 hover:text-slate-950 hover:bg-slate-50' }} rounded-xl transition-all duration-200">
                                <i class='bx bx-bar-chart-alt-2 text-xl'></i>
                                <span class="sidebar-item-text sidebar-transition truncate">{{ __('Raporlar') }}</span>
                            </a>
                        </nav>
                    </div>

                    <div>
                        <p class="px-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-4 sidebar-section-label sidebar-transition">{{ __('Sistem') }}</p>
                        <nav class="space-y-1">
                            <a href="{{ route('users.index') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('users.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                            <svg class="mr-3 h-5 w-5 {{ request()->routeIs('users.*') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-gray-500' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            {{ __('Kullanıcılar') }}
                        </a>

                        <a href="{{ route('activity-logs.index') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('activity-logs.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                            <svg class="mr-3 h-5 w-5 {{ request()->routeIs('activity-logs.*') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-gray-500' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ __('İşlem Geçmişi') }}
                        </a>    
                            <a id="nav-settings" href="{{ route('settings.index') }}" class="nav-link flex items-center gap-3 px-4 py-2.5 text-sm {{ request()->routeIs('settings.*') ? 'sidebar-link-active' : 'text-slate-500 hover:text-slate-950 hover:bg-slate-50' }} rounded-xl transition-all">
                                <i class='bx bx-cog text-xl'></i>
                                <span class="sidebar-item-text sidebar-transition truncate">{{ __('Sistem Ayarları') }}</span>
                            </a>
                            <a id="nav-subscription" href="{{ route('subscription.index') }}" class="nav-link flex items-center gap-3 px-4 py-2.5 text-sm {{ request()->routeIs('subscription.*') ? 'sidebar-link-active' : 'text-slate-500 hover:text-slate-950 hover:bg-slate-50' }} rounded-xl transition-all">
                                <i class='bx bx-credit-card text-xl'></i>
                                <span class="sidebar-item-text sidebar-transition truncate">{{ __('Abonelik & Paketim') }}</span>
                            </a>
                        </nav>
                    </div>
                </div>

                <!-- Profile & Footer -->
                <div class="mt-auto flex-shrink-0">
                    @php
                        $tenant = auth()->user()->tenant;
                        $remainingTrial = $tenant->trial_ends_at && $tenant->trial_ends_at->isFuture() ? $tenant->trial_ends_at->diffInDays(now()) : 0;
                        $isTrial = $remainingTrial > 0;
                    @endphp

                    @if($isTrial)
                    <div class="sidebar-promo bg-indigo-50/50 rounded-2xl p-4 mb-6 border border-indigo-100/50 sidebar-transition">
                        <div class="flex items-center justify-between mb-2">
                            <p class="text-[11px] font-bold text-indigo-600 uppercase tracking-wider">{{ __('Deneme Sürümü') }}</p>
                            <span class="text-[10px] font-bold text-indigo-500 bg-white px-1.5 py-0.5 rounded border border-indigo-100">{{ ceil($remainingTrial) }} {{ __('Gün') }}</span>
                        </div>
                        <p class="text-xs text-indigo-900/70 leading-relaxed mb-3">{{ __('Tüm özelliklere erişiminiz var. Kesintisiz kullanım için paketinizi seçin.') }}</p>
                        <a href="{{ route('subscription.plans') }}" class="block w-full py-2 bg-indigo-600 text-white text-xs font-bold rounded-lg hover:bg-indigo-700 transition-colors text-center">
                            {{ __('Paketi Yükselt') }}
                        </a>
                    </div>
                    @elseif($tenant->subscription_plan_id != 4) <!-- Assuming 4 is top tier -->
                    <div class="sidebar-promo bg-slate-50 rounded-2xl p-4 mb-6 border border-slate-200 sidebar-transition">
                        <p class="text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2">{{ __('Limitleri Arttır') }}</p>
                        <p class="text-xs text-slate-500 leading-relaxed mb-3">{{ __('İşletmenizi büyütmek için daha üst bir pakete geçin.') }}</p>
                        <a href="{{ route('subscription.plans') }}" class="block w-full py-2 border border-slate-300 text-slate-600 text-xs font-bold rounded-lg hover:bg-white hover:text-indigo-600 hover:border-indigo-200 transition-all text-center">
                            {{ __('Paketleri İncele') }}
                        </a>
                    </div>
                    @endif

                    <div class="relative" x-data="{ userMenuOpen: false }">
                        <div @click="userMenuOpen = !userMenuOpen" class="flex items-center gap-3 p-2 rounded-xl hover:bg-slate-50 cursor-pointer transition-all border border-transparent hover:border-slate-100 nav-link">
                            <div class="relative flex-shrink-0">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?? 'User') }}&background=0f172a&color=fff" class="w-10 h-10 rounded-xl object-cover" alt="User">
                                <div class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-emerald-500 border-2 border-white rounded-full"></div>
                            </div>
                            <div class="flex-1 min-w-0 sidebar-profile-info">
                                <p class="text-sm font-bold text-slate-950 truncate">{{ auth()->user()->name ?? 'Guest' }}</p>
                                <p class="text-[11px] text-slate-400 truncate">{{ auth()->user()->email ?? 'demo@fiyera.co' }}</p>
                            </div>
                            <i class='bx bx-dots-vertical-rounded text-slate-400 sidebar-profile-dots'></i>
                        </div>

                        <!-- User Settings Dropdown -->
                        <div x-show="userMenuOpen" 
                             @click.away="userMenuOpen = false"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 translate-x-4 scale-95"
                             x-transition:enter-end="opacity-100 translate-x-0 scale-100"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 translate-x-0 scale-100"
                             x-transition:leave-end="opacity-0 translate-x-4 scale-95"
                             class="fixed left-4 lg:left-[calc(var(--sidebar-width)+8px)] bottom-20 lg:bottom-6 w-60 lg:w-64 bg-white rounded-2xl shadow-[0_10px_40px_-10px_rgba(0,0,0,0.1)] border border-slate-100 p-2 z-[9999] sidebar-profile-dropdown"
                             x-cloak>
                             <div class="px-3 py-2 border-b border-slate-50 mb-1">
                                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">{{ __('Hesap Ayarları') }}</p>
                            </div>
                            <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm text-slate-600 hover:text-slate-950 hover:bg-slate-50 rounded-xl transition-all group">
                                <div class="w-8 h-8 rounded-lg bg-slate-50 flex items-center justify-center group-hover:bg-white border border-transparent group-hover:border-slate-100 transition-all">
                                    <i class='bx bx-user-circle text-lg'></i>
                                </div>
                                <span class="font-semibold">{{ __('Kullanıcı Ayarları') }}</span>
                            </a>
                            <a href="#" class="flex items-center gap-3 px-3 py-2.5 text-sm text-slate-600 hover:text-slate-950 hover:bg-slate-50 rounded-xl transition-all group">
                                <div class="w-8 h-8 rounded-lg bg-slate-50 flex items-center justify-center group-hover:bg-white border border-transparent group-hover:border-slate-100 transition-all">
                                    <i class='bx bx-bell text-lg'></i>
                                </div>
                                <span class="font-semibold">{{ __('Bildirim Ayarları') }}</span>
                            </a>
                            <div class="h-px bg-slate-50 my-1"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-3 px-3 py-2.5 text-sm text-rose-500 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition-all group">
                                    <div class="w-8 h-8 rounded-lg bg-rose-50 flex items-center justify-center group-hover:bg-white border border-transparent group-hover:border-rose-100 transition-all">
                                        <i class='bx bx-log-out text-lg'></i>
                                    </div>
                                    <span class="font-bold">{{ __('Çıkış Yap') }}</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main id="main-content" class="flex-1 w-full ml-0 lg:ml-[var(--sidebar-width)] sidebar-transition min-w-0">
            <!-- Top Header -->
            <header class="h-16 lg:h-20 bg-white/80 backdrop-blur-md border-b border-gray-100 sticky top-0 z-40 px-4 lg:px-8 flex items-center justify-between">
                <div class="flex items-center gap-2 lg:gap-4">
                    <!-- Mobile Menu Button & Brand -->
                    <div class="flex items-center gap-3 lg:hidden">
                         <div class="w-9 h-9 bg-slate-950 rounded-xl flex items-center justify-center shadow-lg shadow-slate-200 flex-shrink-0">
                            <i class='bx bxs-bolt text-white text-xl'></i>
                        </div>
                        <button @click="mobileSidebarOpen = true" class="w-10 h-10 flex items-center justify-center rounded-xl text-slate-500 hover:bg-slate-50 active:bg-slate-100 transition-colors">
                            <i class='bx bx-menu text-2xl'></i>
                        </button>
                       
                    </div>

                    <div id="header-search" class="hidden md:flex items-center gap-4 bg-slate-50 px-4 py-2 rounded-xl border border-slate-100 w-64 lg:w-96 transition-all">
                        <i class='bx bx-search text-slate-400 text-xl'></i>
                        <input type="text" placeholder="{{ __('Teklif veya Müşteri Ara...') }}" class="bg-transparent border-none outline-none text-sm font-medium w-full placeholder:text-slate-400">
                        <span class="text-[10px] font-bold text-slate-400 bg-white px-1.5 py-0.5 rounded border border-slate-100 hidden lg:inline">⌘K</span>
                    </div>
                </div>

                <div class="flex items-center gap-2 lg:gap-3">
                    <!-- Language Switcher -->
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" class="h-10 px-3 flex items-center gap-2 rounded-xl bg-white border border-slate-100 text-slate-500 hover:text-slate-950 hover:bg-slate-50 transition-all font-bold text-xs">
                            @if(app()->getLocale() == 'tr')
                                <img src="https://flagcdn.com/w20/tr.png" class="w-4 rounded-sm" alt="Türkçe">
                                <span>TR</span>
                            @else
                                <img src="https://flagcdn.com/w20/us.png" class="w-4 rounded-sm" alt="English">
                                <span>EN</span>
                            @endif
                            <i class='bx bx-chevron-down text-base opacity-50'></i>
                        </button>
                        <div x-show="open" 
                             @click.away="open = false"
                             x-transition.origin.top.right
                             class="absolute right-0 mt-2 w-32 bg-white rounded-xl shadow-xl border border-slate-100 overflow-hidden py-1 z-50">
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

                    <!-- Tour Trigger -->
                    <button onclick="if(window.startTour) window.startTour(true)" class="w-10 h-10 flex items-center justify-center rounded-xl bg-white border border-slate-100 text-slate-500 hover:text-indigo-600 hover:bg-slate-50 transition-all" title="{{ __('Turu Başlat') }}">
                        <i class='bx bx-help-circle text-xl'></i>
                    </button>

                    <button class="w-10 h-10 flex items-center justify-center rounded-xl bg-white border border-slate-100 text-slate-500 hover:text-slate-950 hover:bg-slate-50 transition-all relative">
                        <i class='bx bx-bell text-xl'></i>
                        <span class="absolute top-2.5 right-2.5 w-2 h-2 bg-rose-500 rounded-full border-2 border-white"></span>
                    </button>
                    <a href="{{ route('proposals.create') }}" class="h-10 px-3 lg:px-4 flex items-center gap-2 rounded-xl bg-slate-950 text-white text-sm font-bold hover:bg-slate-800 transition-all shadow-lg shadow-slate-200">
                        <i class='bx bx-plus text-xl'></i> <span class="hidden sm:inline">{{ __('Yeni Teklif Oluştur') }}</span>
                    </a>
                </div>
            </header>

            <!-- Page Content -->
            <div class="p-4 lg:p-8 max-w-[1500px] mx-auto">
                @yield('content')
            </div>
        </main>
    </div>
    <!-- Success Notification Modal -->
    @if(session('success'))
    <div x-data="{ show: true }" 
         x-show="show" 
         x-init="setTimeout(() => show = false, 3000)"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-90"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-90"
         class="fixed inset-0 z-[9999] flex items-center justify-center p-4 pointer-events-none" x-cloak>
        <div class="bg-white rounded-3xl shadow-[0_20px_70px_rgba(0,0,0,0.15)] border border-slate-100 p-8 flex flex-col items-center max-w-sm w-full pointer-events-auto">
            <div class="w-20 h-20 bg-emerald-50 rounded-full flex items-center justify-center mb-6">
                <div class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center animate-bounce">
                    <i class='bx bx-check text-4xl text-emerald-600'></i>
                </div>
            </div>
            <h3 class="text-xl font-black text-slate-950 mb-2">Başarılı!</h3>
            <p class="text-slate-500 font-bold text-center leading-relaxed">
                {{ session('success') }}
            </p>
        </div>
    </div>
    @endif

    <!-- Access Denied Modal -->
    @if(session('permission_denied'))
    <div x-data="{ show: true }" 
         x-show="show" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm" x-cloak>
        <div @click.away="show = false" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             class="bg-white rounded-3xl shadow-2xl border border-slate-100 p-8 max-w-sm w-full relative overflow-hidden">
            
            <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-rose-500 to-orange-500"></div>
            
            <button @click="show = false" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 transition-colors">
                <i class='bx bx-x text-2xl'></i>
            </button>

            <div class="w-16 h-16 bg-rose-50 rounded-2xl flex items-center justify-center mb-6 mx-auto">
                <i class='bx bx-shield-x text-4xl text-rose-600'></i>
            </div>
            
            <h3 class="text-xl font-black text-slate-950 mb-2 text-center">Erişim Engellendi</h3>
            <p class="text-slate-500 text-sm font-medium text-center leading-relaxed mb-6">
                {{ session('permission_denied') }}
            </p>
            
            <button @click="show = false" class="w-full py-3 bg-slate-950 text-white rounded-xl font-bold text-sm hover:bg-slate-800 transition-colors">
                Tamam, Anlaşıldı
            </button>
        </div>
    </div>
    @endif

    <!-- Upgrade Required Modal -->
    @if(session('upgrade_required'))
    <div x-data="{ show: true }" 
         x-show="show" 
         class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm" x-cloak>
        <div @click.away="show = false" 
             class="bg-white rounded-3xl shadow-2xl border border-slate-100 p-0 max-w-md w-full relative overflow-hidden">
            
            <div class="bg-indigo-600 p-8 text-center relative overflow-hidden">
                <div class="absolute inset-0 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-20"></div>
                <!-- Sparkle Icon -->
                <div class="w-16 h-16 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center mb-4 mx-auto border border-white/30">
                    <i class='bx bxs-rocket text-4xl text-white'></i>
                </div>
                <h3 class="text-2xl font-black text-white mb-2">Paketinizi Yükseltin</h3>
                <p class="text-indigo-100 text-sm font-medium">Bu özelliği kullanmak için üst pakete geçin.</p>
            </div>
            
            <div class="p-8">
                <div class="bg-indigo-50 border border-indigo-100 rounded-xl p-4 mb-6 flex items-start gap-4">
                    <i class='bx bxs-lock-alt text-2xl text-indigo-600'></i>
                    <div>
                        <h4 class="font-bold text-indigo-950 text-sm mb-1">Kısıtlı Özellik</h4>
                        <p class="text-xs text-indigo-800/80 leading-relaxed">
                            Erişmeye çalıştığınız <strong>{{ session('feature_name') }}</strong> özelliği mevcut paketinizde bulunmamaktadır.
                        </p>
                    </div>
                </div>

                <div class="flex gap-3">
                    <button @click="show = false" class="flex-1 py-3 bg-slate-100 text-slate-600 rounded-xl font-bold text-sm hover:bg-slate-200 transition-colors">
                        Belki Daha Sonra
                    </button>
                    <a href="{{ route('subscription.plans') }}" class="flex-1 py-3 bg-indigo-600 text-white rounded-xl font-bold text-sm hover:bg-indigo-700 transition-colors text-center shadow-lg shadow-indigo-200">
                        Paketleri İncele
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif

    <script>
        const sidebarToggle = document.getElementById('sidebarToggle');
        const toggleIcon = document.getElementById('toggle-icon');
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('main-content');
        const body = document.body;

        function applyCollapsedState() {
            body.classList.add('sidebar-collapsed');
            toggleIcon.classList.remove('bx-chevron-left');
            toggleIcon.classList.add('bx-chevron-right');
        }

        function applyExpandedState() {
            body.classList.remove('sidebar-collapsed');
            toggleIcon.classList.remove('bx-chevron-right');
            toggleIcon.classList.add('bx-chevron-left');
        }

        // Check for saved state
        if (localStorage.getItem('sidebarCollapsed') === 'true') {
            applyCollapsedState();
        }

        sidebarToggle.addEventListener('click', () => {
            const currentStatus = body.classList.contains('sidebar-collapsed');
            if (currentStatus) {
                applyExpandedState();
                localStorage.setItem('sidebarCollapsed', 'false');
            } else {
                applyCollapsedState();
                localStorage.setItem('sidebarCollapsed', 'true');
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
