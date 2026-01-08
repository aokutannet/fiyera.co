<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('Yükleniyor...') }} | {{ config('app.name') }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    
    <!-- Icons -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    
    <!-- Scripts -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="h-full relative overflow-hidden flex items-center justify-center p-4">

    <!-- Decorative Background Elements -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-[20%] -left-[10%] w-[70vw] h-[70vw] rounded-full  "></div>
        <div class="absolute -bottom-[20%] -right-[10%] w-[70vw] h-[70vw] rounded-full bg-indigo-200/30 blur-3xl"></div>
    </div>

    <div 
        x-data="loadingProcess()"
        x-init="start()"
        class="relative max-w-md w-full bg-white/80 backdrop-blur-xl rounded-3xl shadow-2xl shadow-indigo-500/10 p-10 text-center border border-white/50"
    >
        <!-- Icon Container -->
        <div class="mb-10 relative flex justify-center">
            <div class="w-24 h-24 bg-gradient-to-tr from-indigo-500 to-purple-600 rounded-2xl flex items-center justify-center relative z-10 shadow-lg shadow-indigo-500/30 transform transition-all duration-500 ease-out"
                 :class="{'rotate-180': progress >= 90}">
                <i class='bx text-white text-5xl transition-all duration-300' 
                   :class="currentIcon"></i>
            </div>
            
            <!-- Pulse Effect -->
            <div class="absolute inset-0 bg-indigo-500/20 rounded-2xl blur-xl animate-pulse"></div>
        </div>

        <!-- Status Text -->
        <div class="space-y-2 mb-10">
            <h2 class="text-2xl font-bold text-slate-800 transition-all duration-300" x-text="currentMessage"></h2>
            <p class="text-slate-500 text-sm font-medium">{{ __('Hesabınız hazırlanıyor, lütfen bekleyin...') }}</p>
        </div>

        <!-- Progress Indicator -->
        <div class="relative">
            <div class="flex justify-between text-xs font-semibold text-slate-400 mb-2">
                <span>{{ __('İşlem Durumu') }}</span>
                <span x-text="Math.round(progress) + '%'"></span>
            </div>
            <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
                <div 
                    class="h-full bg-gradient-to-r from-indigo-500 to-purple-600 rounded-full transition-all duration-300 ease-out shadow-[0_0_10px_rgba(99,102,241,0.5)]"
                    :style="`width: ${progress}%`"
                ></div>
            </div>
        </div>
    </div>

    <script>
        function loadingProcess() {
            return {
                progress: 0,
                currentMessage: '{{ __('Modüller Kuruluyor...') }}',
                currentIcon: 'bx-layer bx-flashing',
                
                start() {
                    const totalDuration = 4000; // 4 seconds total
                    const intervalTime = 50;
                    const steps = totalDuration / intervalTime;
                    const increment = 100 / steps;
                    
                    const timer = setInterval(() => {
                        this.progress += increment;
                        
                        // Stage 1: 0% - 50%
                        if (this.progress < 50) {
                            this.currentMessage = '{{ __('Modüller Kuruluyor...') }}';
                            this.currentIcon = 'bx-layer bx-flashing';
                        } 
                        // Stage 2: 50% - 90%
                        else if (this.progress < 90) {
                            this.currentMessage = '{{ __('Dashboard Hazırlanıyor...') }}';
                            this.currentIcon = 'bx-layout bx-fade-right';
                        }
                        // Stage 3: 90% - 100%
                        else {
                            this.currentMessage = '{{ __('Hazır!') }}';
                            this.currentIcon = 'bx-check-circle bx-tada';
                        }

                        if (this.progress >= 100) {
                            this.progress = 100;
                            clearInterval(timer);
                            setTimeout(() => {
                                window.location.href = "{{ route('dashboard') }}";
                            }, 800);
                        }
                    }, intervalTime);
                }
            }
        }
    </script>
</body>
</html>
