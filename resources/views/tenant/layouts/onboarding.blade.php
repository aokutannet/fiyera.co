<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Onboarding - {{ config('app.name', 'fiyera.co') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; letter-spacing: -0.01em; }
        .glass-card { background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(12px); }
    </style>
</head>
<body class="bg-white text-slate-950 font-medium antialiased">
    
    <!-- Main Content -->
    <main class="min-h-screen flex flex-col w-full">
        <!-- Minimal Header -->
        <header class="h-24 flex items-center justify-center sticky top-0 z-50 bg-white/90 backdrop-blur-sm">
            <div class="flex items-center gap-2.5">
                <div class="w-10 h-10 bg-black rounded-xl flex items-center justify-center">
                    <i class='bx bxs-bolt text-white text-xl'></i>
                </div>
                <span class="font-extrabold text-2xl tracking-tight text-black">fiyera<span class="text-slate-300">.co</span></span>
            </div>
        </header>
        
        <div class="flex-1 flex flex-col">
            @yield('content')
        </div>

        <!-- Minimal Footer -->
        <footer class="py-8 text-center text-xs font-bold text-slate-300">
            &copy; {{ date('Y') }} fiyera.co
        </footer>
    </main>

</body>
</html>
