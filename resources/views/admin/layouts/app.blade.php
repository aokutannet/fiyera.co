<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Super Admin - {{ config('app.name', 'fiyera.co') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <meta name="robots" content="noindex, nofollow">
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
<body class="bg-[#fafafa] text-slate-950 font-medium">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside id="sidebar" class="w-[var(--sidebar-width)] bg-white flex flex-col fixed h-full border-r border-gray-100 z-50 sidebar-transition">
            <div class="p-6 flex flex-col h-full sidebar-content">
                 <!-- Brand -->
                <div class="flex items-center justify-between mb-8 flex-shrink-0 relative">
                    <div class="flex items-center gap-2.5 px-2">
                        <div class="w-9 h-9 bg-slate-950 rounded-xl flex items-center justify-center shadow-lg shadow-slate-200 flex-shrink-0">
                            <i class='bx bxs-bolt text-white text-xl'></i>
                        </div>
                        <span class="font-extrabold text-xl tracking-tight text-slate-950 sidebar-brand-text sidebar-transition whitespace-nowrap">fiyera<span class="text-indigo-600">.co</span></span>
                    </div>
                    <button id="sidebarToggle" class="sidebar-toggle-btn w-8 h-8 flex items-center justify-center rounded-lg hover:bg-slate-50 text-slate-400 hover:text-slate-950 transition-all">
                        <i class='bx bx-chevron-left text-xl' id="toggle-icon"></i>
                    </button>
                </div>
                <!-- Main Nav -->
                <div class="space-y-6 flex-1">
                    <div>
                        <p class="px-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-4 sidebar-section-label sidebar-transition">Yönetim</p>
                        <nav class="space-y-1">
                            <a id="nav-dashboard" href="{{ route('admin.dashboard') }}" class="nav-link flex items-center gap-3 px-4 py-2.5 text-sm {{ request()->routeIs('admin.dashboard') ? 'sidebar-link-active' : 'text-slate-500 hover:text-slate-950 hover:bg-slate-50' }} rounded-xl transition-all duration-200">
                                <i class='bx bxs-dashboard text-xl'></i> 
                                <span class="sidebar-item-text sidebar-transition truncate">Genel Bakış</span>
                            </a>
                            <a id="nav-tenants" href="{{ route('admin.tenants.index') }}" class="nav-link flex items-center gap-3 px-4 py-2.5 text-sm {{ request()->routeIs('admin.tenants.*') ? 'sidebar-link-active' : 'text-slate-500 hover:text-slate-950 hover:bg-slate-50' }} rounded-xl transition-all duration-200">
                                <i class='bx bxs-business text-xl'></i>
                                <span class="sidebar-item-text sidebar-transition truncate">Kiracılar (Tenants)</span>
                            </a>
                            <a id="nav-plans" href="{{ route('admin.plans.index') }}" class="nav-link flex items-center gap-3 px-4 py-2.5 text-sm {{ request()->routeIs('admin.plans.*') ? 'sidebar-link-active' : 'text-slate-500 hover:text-slate-950 hover:bg-slate-50' }} rounded-xl transition-all duration-200">
                                <i class='bx bxs-package text-xl'></i>
                                <span class="sidebar-item-text sidebar-transition truncate">Paket Yönetimi</span>
                            </a>
                            <a id="nav-orders" href="{{ route('admin.orders.index') }}" class="nav-link flex items-center gap-3 px-4 py-2.5 text-sm {{ request()->routeIs('admin.orders.*') ? 'sidebar-link-active' : 'text-slate-500 hover:text-slate-950 hover:bg-slate-50' }} rounded-xl transition-all duration-200">
                                <i class='bx bx-receipt text-xl'></i>
                                <span class="sidebar-item-text sidebar-transition truncate">Siparişler</span>
                            </a>
                        </nav>
                    </div>
                </div>

                <!-- Profile & Footer -->
                <div class="mt-auto flex-shrink-0">
                    <div class="relative" x-data="{ userMenuOpen: false }">
                        <div @click="userMenuOpen = !userMenuOpen" class="flex items-center gap-3 p-2 rounded-xl hover:bg-slate-50 cursor-pointer transition-all border border-transparent hover:border-slate-100 nav-link">
                            <div class="relative flex-shrink-0">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->guard('super_admin')->user()->name ?? 'Admin') }}&background=e11d48&color=fff" class="w-10 h-10 rounded-xl object-cover" alt="Admin">
                                <div class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-emerald-500 border-2 border-white rounded-full"></div>
                            </div>
                            <div class="flex-1 min-w-0 sidebar-profile-info">
                                <p class="text-sm font-bold text-slate-950 truncate">{{ auth()->guard('super_admin')->user()->name ?? 'Administrator' }}</p>
                                <p class="text-[11px] text-slate-400 truncate">{{ auth()->guard('super_admin')->user()->email ?? '' }}</p>
                            </div>
                            <i class='bx bx-dots-vertical-rounded text-slate-400 sidebar-profile-dots'></i>
                        </div>

                        <!-- User Settings Dropdown -->
                        <div x-show="userMenuOpen" 
                             @click.away="userMenuOpen = false"
                             class="fixed left-[calc(var(--sidebar-width)+8px)] bottom-6 w-64 bg-white rounded-2xl shadow-[0_10px_40px_-10px_rgba(0,0,0,0.1)] border border-slate-100 p-2 z-[9999] sidebar-profile-dropdown"
                             x-cloak>
                            <form method="POST" action="{{ route('admin.logout') }}">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-3 px-3 py-2.5 text-sm text-rose-500 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition-all group">
                                    <div class="w-8 h-8 rounded-lg bg-rose-50 flex items-center justify-center group-hover:bg-white border border-transparent group-hover:border-rose-100 transition-all">
                                        <i class='bx bx-log-out text-lg'></i>
                                    </div>
                                    <span class="font-bold">Çıkış Yap</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main id="main-content" class="flex-1 ml-[var(--sidebar-width)] sidebar-transition">
            <!-- Top Header -->
            <header class="h-20 bg-white/80 backdrop-blur-md border-b border-gray-100 sticky top-0 z-40 px-8 flex items-center justify-between">
                <div class="flex items-center gap-4 bg-slate-50 px-4 py-2 rounded-xl border border-slate-100 w-96">
                    <i class='bx bx-search text-slate-400 text-xl'></i>
                    <input type="text" placeholder="Firma Ara..." class="bg-transparent border-none outline-none text-sm font-medium w-full placeholder:text-slate-400">
                    <span class="text-[10px] font-bold text-slate-400 bg-white px-1.5 py-0.5 rounded border border-slate-100">⌘K</span>
                </div>

                <div class="flex items-center gap-3">
                    <button class="w-10 h-10 flex items-center justify-center rounded-xl bg-white border border-slate-100 text-slate-500 hover:text-slate-950 hover:bg-slate-50 transition-all relative">
                        <i class='bx bx-bell text-xl'></i>
                        <span class="absolute top-2.5 right-2.5 w-2 h-2 bg-rose-500 rounded-full border-2 border-white"></span>
                    </button>
                </div>
            </header>

            <!-- Page Content -->
            <div class="p-8 max-w-[1500px] mx-auto">
                @yield('content')
            </div>
        </main>
    </div>

    @if(session('success'))
    <div x-data="{ show: true }" 
         x-show="show" 
         x-init="setTimeout(() => show = false, 3000)"
         class="fixed inset-0 z-[9999] flex items-center justify-center p-4 pointer-events-none" x-cloak>
        <div class="bg-white rounded-3xl shadow-[0_20px_70px_rgba(0,0,0,0.15)] border border-slate-100 p-8 flex flex-col items-center max-w-sm w-full pointer-events-auto">
            <div class="w-20 h-20 bg-emerald-50 rounded-full flex items-center justify-center mb-6">
                <i class='bx bx-check text-4xl text-emerald-600'></i>
            </div>
            <h3 class="text-xl font-black text-slate-950 mb-2">Başarılı!</h3>
            <p class="text-slate-500 font-bold text-center leading-relaxed">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    @if($errors->any())
    <div x-data="{ show: true }" 
         x-show="show" 
         x-init="setTimeout(() => show = false, 5000)"
         class="fixed inset-0 z-[9999] flex items-center justify-center p-4 pointer-events-none" x-cloak>
        <div class="bg-white rounded-3xl shadow-[0_20px_70px_rgba(0,0,0,0.15)] border border-rose-100 p-8 flex flex-col items-center max-w-sm w-full pointer-events-auto">
            <div class="w-20 h-20 bg-rose-50 rounded-full flex items-center justify-center mb-6">
                <i class='bx bx-x text-4xl text-rose-600'></i>
            </div>
            <h3 class="text-xl font-black text-slate-950 mb-2">Hata!</h3>
            <ul class="text-slate-500 font-bold text-center leading-relaxed list-none">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif

    <script>
        const sidebarToggle = document.getElementById('sidebarToggle');
        const toggleIcon = document.getElementById('toggle-icon');
        const sidebar = document.getElementById('sidebar');
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

        if (localStorage.getItem('sidebarCollapsed') === 'true') {
            applyCollapsedState();
        }

        sidebarToggle.addEventListener('click', () => {
            if (body.classList.contains('sidebar-collapsed')) {
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
