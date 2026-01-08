@extends('tenant.layouts.app')

@section('content')
<div class="space-y-10">
    <!-- minimalist Header with welcome message -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 animate-in slide-in-from-top duration-500">
        <div>
           
            <h1 class="text-xl font-extrabold text-slate-900 tracking-tight" id="header-welcome">{{ __('Esenlikler') }}, {{ explode(' ', auth()->user()->name ?? 'Ahmet')[0] }}! 👋</h1>
            <p class="text-slate-500 mt-2 font-medium">{{ __('Bugün') }} {{ now()->translatedFormat('d F Y') }} • {{ __('İşte senin için güncel özet.') }}</p>
        </div>
        
        <!-- Trial Status Banner (Only if trial is active) -->
        @if(isset($remainingTrialDays) && $remainingTrialDays > 0)
        <div class="hidden lg:flex items-center gap-4 bg-indigo-50 border border-indigo-100 px-4 py-2 rounded-xl" id="trial-status-widget">
            <div class="flex flex-col">
                <span class="text-[10px] uppercase font-bold text-indigo-400 tracking-wider">{{ __('Deneme Sürümü') }}</span>
                <span class="text-sm font-black text-indigo-900">
                    {{ $remainingTrialDays }} {{ __('Gün Kaldı') }}
                </span>
            </div>
            <div class="w-px h-8 bg-indigo-200"></div>
            <div class="flex flex-col">
                 <span class="text-[10px] uppercase font-bold text-indigo-400 tracking-wider">{{ __('Teklif Limiti') }}</span>
                 <span class="text-xs font-bold text-indigo-700">
                    {{ $usage['proposals']['used'] }} / {{ $usage['proposals']['limit'] == -1 ? '∞' : $usage['proposals']['limit'] }}
                 </span>
            </div>
             <a href="{{ route('subscription.plans') }}" class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-lg transition-colors shadow-sm shadow-indigo-200">
                {{ __('Paketi Yükselt') }}
            </a>
        </div>
        @endif

        <div class="flex items-center gap-3">
            <button class="h-12 px-6 rounded-2xl bg-white border border-slate-200 text-sm font-bold text-slate-700 hover:text-indigo-600 hover:border-indigo-100 transition-all shadow-sm hover:shadow-md flex items-center gap-2 group">
                <i class='bx bx-export text-xl group-hover:scale-110 transition-transform'></i> {{ __('Dışa Aktar') }}
            </button>
            
        </div>
    </div>
    
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/driver.js@1.0.1/dist/driver.js.iife.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/driver.js@1.0.1/dist/driver.css"/>
    
    <style>
        /* Custom Driver.js Theme for Fiyera */
        .driver-popover.driverjs-theme {
            background-color: #ffffff;
            color: #0f172a;
            border-radius: 16px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
            padding: 0;
            border: 1px solid #e2e8f0;
            font-family: 'Plus Jakarta Sans', sans-serif;
            max-width: 320px;
        }

        .driver-popover.driverjs-theme .driver-popover-title {
            font-size: 16px;
            font-weight: 800;
            color: #0f172a;
            padding: 20px 20px 0 20px;
            margin-bottom: 8px;
            line-height: 1.4;
        }

        .driver-popover.driverjs-theme .driver-popover-description {
            font-size: 13px;
            line-height: 1.6;
            color: #64748b;
            padding: 0 20px 20px 20px;
            font-weight: 500;
        }

        .driver-popover.driverjs-theme .driver-popover-footer {
            background-color: #f8fafc;
            padding: 12px 20px;
            border-top: 1px solid #f1f5f9;
            border-radius: 0 0 16px 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .driver-popover.driverjs-theme button {
            flex: 1;
            text-align: center;
            background-color: #ffffff;
            color: #475569;
            border: 1px solid #e2e8f0;
            text-shadow: none;
            font-size: 12px;
            padding: 8px 14px;
            border-radius: 8px;
            font-weight: 700;
            transition: all 0.2s;
            cursor: pointer;
        }

        .driver-popover.driverjs-theme button:hover {
            background-color: #f1f5f9;
            color: #0f172a;
        }

        .driver-popover.driverjs-theme button.driver-popover-next-btn {
            background-color: #4f46e5;
            color: #ffffff;
            border: 1px solid #4f46e5;
            box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.2);
        }

        .driver-popover.driverjs-theme button.driver-popover-next-btn:hover {
            background-color: #4338ca;
        }
        
        .driver-popover.driverjs-theme button.driver-popover-prev-btn {
             margin-right: 8px;
        }
    </style>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const driver = window.driver.js.driver;
            
            const driverObj = driver({
                showProgress: true,
                animate: true,
                doneBtnText: 'Tur\'u Tamamla',
                nextBtnText: 'İlerle',
                prevBtnText: 'Geri',
                popoverClass: 'driverjs-theme',
                steps: [
                    { 
                        element: '#header-welcome', 
                        popover: { 
                            title: '{{ __("Fiyera\'ya Hoş Geldin! 👋") }}', 
                            description: '{{ __("Tüm işlerini tek ekrandan yönetebileceğin modern panele merhaba de. Senin için kısa bir tur hazırladık.") }}' 
                        } 
                    },
                    {
                        element: '#sidebar',
                        popover: {
                            title: '{{ __("Ana Menü") }}',
                            description: '{{ __("Soldaki bu menüden Teklifler, Müşteriler ve Ürünlerinize hızlıca ulaşabilirsiniz.") }}',
                            side: 'right',
                            align: 'start'
                        }
                    },
                    { 
                        element: '#nav-proposals', 
                        popover: { 
                            title: '{{ __("Teklif Yönetimi") }}', 
                            description: '{{ __("Tüm tekliflerinizi buradan listeleyebilir, durumlarını takip edebilir ve düzenleyebilirsiniz.") }}' 
                        } 
                    },
                    { 
                        element: '#nav-customers', 
                        popover: { 
                            title: '{{ __("Müşteri Veritabanı") }}', 
                            description: '{{ __("Müşterilerinizi kaydedin ve kolayca yönetin. Teklif oluştururken buradan hızlıca seçebilirsiniz.") }}' 
                        } 
                    },
                    { 
                        element: '#nav-settings', 
                        popover: { 
                            title: '{{ __("Sistem Ayarları") }}', 
                            description: '{{ __("Logo, firma bilgileri ve diğer genel ayarları buradan yapılandırabilirsiniz.") }}' 
                        } 
                    },
                    { 
                        element: '#trial-status-widget', 
                        popover: { 
                            title: '{{ __("Paket & Limit Yönetimi") }}', 
                            description: '{{ __("Deneme sürenizi ve aylık teklif oluşturma limitlerinizi buradan anlık takip edebilirsiniz.") }}' 
                        } 
                    },
                    { 
                        element: '#stats-grid', 
                        popover: { 
                            title: '{{ __("Özet Durum Paneli") }}', 
                            description: '{{ __("Bu kartlar size işletmenizin genel sağlığını gösterir: Toplam müşteri, ürün sayısı, bekleyen işler ve ciro.") }}' 
                        } 
                    },
                    { 
                        element: '#header-search', 
                        popover: { 
                            title: '{{ __("Akıllı Arama") }}', 
                            description: '{{ __("Sadece bir müşteri adı veya teklif numarası yazarak istediğiniz kayda saniyeler içinde ulaşın. (Kısayol: ⌘K)") }}' 
                        } 
                    },
                    { 
                        element: '#btn-new-record', 
                        popover: { 
                            title: '{{ __("Hemen Başlayın! 🚀") }}', 
                            description: '{{ __("Her şey hazır! Şimdi ilk kaydınızı oluşturarak sistemi denemeye başlayın.") }}' 
                        } 
                    },
                ],
                onDestroyStarted: () => {
                    localStorage.setItem('fiyato_dashboard_tour_seen_v7', 'true');
                    driverObj.destroy();
                },
            });

            // Global function to start tour manually
            window.startTour = function(force = false) {
                if (force) {
                    driverObj.drive();
                    return;
                }

                const tourSeen = localStorage.getItem('fiyato_dashboard_tour_seen_v7');
                const isMobile = window.innerWidth < 1024;
                
                if (!tourSeen && !isMobile) {
                    setTimeout(() => {
                        driverObj.drive();
                    }, 1000);
                }
            };

            // Auto-start check
            window.startTour();
        });
    </script>
    @endpush

    <!-- Business Overview Grid -->
    <div id="stats-grid" class="grid grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6">
        <!-- Total Customers -->
        <div class="bg-white p-6 rounded-md border border-slate-100 shadow-sm hover:shadow-xl hover:shadow-emerald-500/5 transition-all group cursor-default relative overflow-hidden">
            <div class="absolute -right-6 -top-6 w-24 h-24 bg-emerald-50 rounded-full group-hover:bg-emerald-100/50 transition-colors"></div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest relative z-10">{{ __('Toplam Müşteri') }}</p>
            
            @if($totalCustomers > 0)
                <div class="flex items-end justify-between mt-4 relative z-10">
                    <h3 class="text-3xl font-black text-slate-900">{{ $totalCustomers }}</h3>
                    <div class="flex items-center gap-1 text-[11px] font-bold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-xl">
                        <i class='bx bx-group'></i> {{ __('Aktif') }}
                    </div>
                </div>
            @else
                 <!-- Empty State -->
                 <div class="flex items-end justify-between mt-4 relative z-10">
                    <a href="{{ route('customers.index') }}" class="flex items-center gap-2 text-emerald-600 hover:text-emerald-700 transition-colors group/link">
                        <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center group-hover/link:scale-110 transition-transform">
                            <i class='bx bx-plus text-lg'></i>
                        </div>
                        <span class="text-sm font-bold underline decoration-dotted underline-offset-4">{{ __('Müşteri Ekle') }}</span>
                    </a>
                    <span class="text-[10px] font-bold text-slate-300 bg-slate-50 px-2 py-1 rounded-lg">{{ __('Henüz Yok') }}</span>
                </div>
            @endif
        </div>

        <!-- Active Products -->
        <div class="bg-white p-6 rounded-md border border-slate-100 shadow-sm hover:shadow-xl hover:shadow-sky-500/5 transition-all group cursor-default relative overflow-hidden">
            <div class="absolute -right-6 -top-6 w-24 h-24 bg-sky-50 rounded-full group-hover:bg-sky-100/50 transition-colors"></div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest relative z-10">{{ __('Ürün / Hizmet') }}</p>
            
            @if($totalProducts > 0)
                <div class="flex items-end justify-between mt-4 relative z-10">
                    <h3 class="text-3xl font-black text-slate-900">{{ $totalProducts }}</h3>
                    <div class="flex items-center gap-1 text-[11px] font-bold text-sky-600 bg-sky-50 px-2.5 py-1 rounded-xl">
                        <i class='bx bx-cube'></i> {{ __('Stokta') }}
                    </div>
                </div>
            @else
                <!-- Empty State -->
                <div class="flex items-end justify-between mt-4 relative z-10">
                    <a href="{{ route('products.index') }}" class="flex items-center gap-2 text-sky-600 hover:text-sky-700 transition-colors group/link">
                        <div class="w-8 h-8 rounded-lg bg-sky-50 flex items-center justify-center group-hover/link:scale-110 transition-transform">
                            <i class='bx bx-plus text-lg'></i>
                        </div>
                        <span class="text-sm font-bold underline decoration-dotted underline-offset-4">{{ __('Ürün Ekle') }}</span>
                    </a>
                    <span class="text-[10px] font-bold text-slate-300 bg-slate-50 px-2 py-1 rounded-lg">{{ __('Henüz Yok') }}</span>
                </div>
            @endif
        </div>

        <!-- Pending Proposals -->
        <div class="bg-white p-6 rounded-md border border-slate-100 shadow-sm hover:shadow-xl hover:shadow-amber-500/5 transition-all group cursor-default relative overflow-hidden">
            <div class="absolute -right-6 -top-6 w-24 h-24 bg-amber-50 rounded-full group-hover:bg-amber-100/50 transition-colors"></div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest relative z-10">{{ __('Bekleyen Teklifler') }}</p>
            
            @if($pendingProposals > 0)
                <div class="flex items-end justify-between mt-4 relative z-10">
                    <h3 class="text-3xl font-black text-slate-900">{{ $pendingProposals }}</h3>
                    <div class="flex items-center gap-1 text-[11px] font-bold text-amber-600 bg-amber-50 px-2.5 py-1 rounded-xl">
                        <i class='bx bx-file'></i> {{ __('İşlemde') }}
                    </div>
                </div>
            @else
                <!-- Empty State -->
                <div class="flex items-end justify-between mt-4 relative z-10">
                    <span class="text-2xl font-black text-slate-200">-</span>
                    <div class="flex items-center gap-1 text-[11px] font-bold text-slate-400 bg-slate-50 px-2.5 py-1 rounded-xl">
                        <i class='bx bx-sleep-y'></i> {{ __('Bekleyen Yok') }}
                    </div>
                </div>
            @endif
        </div>

        <!-- Monthly Revenue -->
        <div class="bg-indigo-600 p-6 rounded-md shadow-xl shadow-indigo-600/20 group cursor-default relative overflow-hidden">
            <div class="absolute -right-4 -top-4 w-28 h-28 bg-white/10 rounded-full blur-2xl"></div>
            <p class="text-[10px] font-black text-white/60 uppercase tracking-widest relative z-10">{{ __('Aylık Ciro') }}</p>
            
            @if($monthlyVolume > 0)
                <div class="flex items-end justify-between mt-4 relative z-10">
                    <h3 class="text-3xl font-black text-white">₺{{ number_format($monthlyVolume, 2) }}</h3>
                    <div class="flex items-center gap-1 text-[11px] font-bold text-white bg-white/20 px-2.5 py-1 rounded-xl backdrop-blur-md">
                        <i class='bx bx-wallet'></i> {{ __('Bu Ay') }}
                    </div>
                </div>
            @else
                <!-- Empty State -->
                <div class="flex items-end justify-between mt-4 relative z-10">
                   <div class="flex flex-col">
                        <span class="text-2xl font-black text-white/40">₺0.00</span>
                   </div>
                   <div class="flex items-center gap-1 text-[11px] font-bold text-white/60 bg-white/10 px-2.5 py-1 rounded-xl backdrop-blur-sm">
                        <i class='bx bx-wallet'></i> {{ __('Hedefleniyor') }}
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Recent Proposals Table -->
        <div class="lg:col-span-8 bg-white rounded-md border border-slate-100 shadow-sm overflow-hidden flex flex-col">
            <div class="px-8 py-6 border-b border-slate-50 flex items-center justify-between bg-slate-50/30">
                <div>
                    <h2 class="text-base font-black text-slate-900">{{ __('Güncel Teklifler') }}</h2>
                    <p class="text-[11px] text-slate-500 font-medium">{{ __('Son 30 gün içerisindeki tüm hareketlilik') }}</p>
                </div>
                <div class="flex gap-2">
                    <button class="w-10 h-10 flex items-center justify-center hover:bg-white hover:shadow-sm rounded-xl transition-all text-slate-400 hover:text-slate-900 border border-transparent hover:border-slate-100">
                        <i class='bx bx-filter text-xl'></i>
                    </button>
                    <button class="w-10 h-10 flex items-center justify-center hover:bg-white hover:shadow-sm rounded-xl transition-all text-slate-400 hover:text-slate-900 border border-transparent hover:border-slate-100">
                        <i class='bx bx-search text-xl'></i>
                    </button>
                </div>
            </div>
            
            @if($recentProposals->isEmpty())
                <!-- EMPTY STATE: Recent Proposals -->
                <div class="flex flex-col items-center justify-center py-20 px-6 text-center">
                    <div class="w-24 h-24 bg-indigo-50 rounded-full flex items-center justify-center mb-6 animate-pulse">
                        <i class='bx bx-plus-circle text-5xl text-indigo-200'></i>
                    </div>
                    <h3 class="text-lg font-black text-slate-900 mb-2">{{ __('Henüz hiç işlem yok') }}</h3>
                    <p class="text-slate-500 max-w-sm mx-auto mb-8 text-sm leading-relaxed">
                        {{ __('fiyera.co ile ilk teklifini oluşturup müşterilerine göndermeye başla. Profesyonel teklifler ile satışlarını artır.') }}
                    </p>
                    <a href="{{ route('proposals.create') }}" class="px-8 py-4 bg-indigo-600 text-white rounded-xl font-bold shadow-xl shadow-indigo-600/30 hover:shadow-indigo-600/50 hover:-translate-y-1 transition-all flex items-center gap-2">
                        <i class='bx bx-paper-plane'></i> {{ __('İlk Teklifi Hazırla') }}
                    </a>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-50/50">
                                <th class="px-8 py-5">{{ __('Ref / Müşteri') }}</th>
                                <th class="px-4 py-5">{{ __('Teklif') }}</th>
                                <th class="px-4 py-5">{{ __('Tutar') }}</th>
                                <th class="px-4 py-5">{{ __('Durum') }}</th>
                                <th class="px-8 py-5 text-right"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50/50">
                            @foreach($recentProposals as $proposal)
                            <tr class="hover:bg-slate-50/50 transition-all group cursor-pointer" onclick="window.location='{{ route('proposals.show', $proposal) }}'">
                                <td class="px-8 py-5">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center group-hover:bg-indigo-50 transition-colors">
                                            <i class='bx bx-file text-xl text-slate-400 group-hover:text-indigo-600'></i>
                                        </div>
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <p class="text-[14px] font-bold text-slate-900 group-hover:text-indigo-600 transition-colors">{{ $proposal->customer->company_name ?? $proposal->customer->contact_person }}</p>
                                            </div>
                                            <p class="text-[10px] font-bold text-slate-400 mt-0.5" title="{{ $proposal->title }}">
                                                {{ $proposal->proposal_number }} • {{ $proposal->created_at->diffForHumans() }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-5">
                                    <span class="text-[13px] font-bold text-slate-700">{{ Str::limit($proposal->title, 30) }}</span>
                                </td>
                                <td class="px-4 py-5">
                                    <span class="text-[14px] font-black text-slate-800">{{ number_format($proposal->total_amount, 2) }} {{ $proposal->currency }}</span>
                                </td>
                                <td class="px-4 py-5">
                                    @php
                                        $statusColors = [
                                            'approved' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-600'],
                                            'pending' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-600'],
                                            'waiting' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-600'],
                                            'sent' => ['bg' => 'bg-indigo-50', 'text' => 'text-indigo-600'],
                                            'rejected' => ['bg' => 'bg-rose-50', 'text' => 'text-rose-600'],
                                            'draft' => ['bg' => 'bg-slate-100', 'text' => 'text-slate-600'],
                                        ];
                                        $color = $statusColors[$proposal->status] ?? $statusColors['draft'];
                                        
                                        $statusLabels = [
                                            'approved' => 'ONAYLANDI',
                                            'pending' => 'BEKLİYOR',
                                            'waiting' => 'BEKLİYOR',
                                            'sent' => 'GÖNDERİLDİ',
                                            'rejected' => 'REDDEDİLDİ',
                                            'draft' => 'TASLAK',
                                        ];
                                        $label = $statusLabels[$proposal->status] ?? strtoupper($proposal->status);
                                    @endphp
                                    <span class="text-[10px] font-black px-3 py-1 rounded-lg {{ $color['bg'] }} {{ $color['text'] }}">
                                        {{ $label }}
                                    </span>
                                </td>
                                <td class="px-8 py-5 text-right">
                                    <button class="w-8 h-8 flex items-center justify-center rounded-lg opacity-0 group-hover:opacity-100 hover:bg-white hover:shadow-sm border border-transparent hover:border-slate-100 transition-all text-slate-400">
                                        <i class='bx bx-right-arrow-alt text-xl'></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-6 bg-slate-50/30 text-center border-t border-slate-50">
                    <a href="{{ route('proposals.index') }}" class="text-[11px] font-black text-indigo-600 hover:text-indigo-700 hover:underline transition-all tracking-widest uppercase">{{ __('Tüm Teklifleri İncele') }}</a>
                </div>
            @endif
        </div>

        <!-- Right Side: Pipeline & Activities -->
        <div class="lg:col-span-4 space-y-8">
            <!-- Pipeline Card -->
            <div class="bg-white p-8 rounded-md border border-slate-100 shadow-sm relative overflow-hidden">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-sm font-black text-slate-900 uppercase tracking-wider">{{ __('Huni Analizi') }}</h3>
                    <i class='bx bx-info-circle text-slate-300 hover:text-slate-600 cursor-help transition-colors'></i>
                </div>
                
                @if($recentProposals->isEmpty())
                     <!-- EMPTY STATE: Pipeline -->
                    <div class="py-4">
                        <div class="flex items-center gap-4 mb-6 opacity-40 grayscale">
                            <div class="w-12 h-12 rounded-full bg-slate-100"></div>
                            <div class="flex-1 space-y-2">
                                <div class="h-3 bg-slate-100 rounded w-3/4"></div>
                                <div class="h-2 bg-slate-50 rounded w-1/2"></div>
                            </div>
                        </div>
                        <div class="text-center">
                            <p class="text-xs text-slate-500 mb-4">{{ __('Veri toplandıkça burası dolacak.') }}</p>
                             <div class="inline-flex items-center gap-1 text-[10px] font-bold text-indigo-600 bg-indigo-50 px-2 py-1 rounded">
                                <i class='bx bx-loader-alt animate-spin'></i> {{ __('Bekleniyor') }}
                            </div>
                        </div>
                    </div>
                @else
                    <div class="space-y-6">
                        <div class="group">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-[11px] font-bold text-slate-500 group-hover:text-slate-900 transition-colors">{{ __('Hasılat Beklentisi') }}</span>
                                <span class="text-[11px] font-black text-slate-900">₺{{ number_format($revenueExpectation) }}</span>
                            </div>
                            <div class="overflow-hidden h-2.5 flex rounded-full bg-slate-100">
                                <div style="width:75%" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-indigo-500 rounded-full animate-pulse transition-all"></div>
                            </div>
                        </div>
                        <div class="group">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-[11px] font-bold text-slate-500 group-hover:text-slate-900 transition-colors">{{ __('Dosya / Teklif Oranı') }}</span>
                                <span class="text-[11px] font-black text-slate-900">%{{ number_format($acceptanceRate, 0) }}</span>
                            </div>
                            <div class="overflow-hidden h-2.5 flex rounded-full bg-slate-100">
                                <div style="width:{{ $acceptanceRate }}%" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-sky-400 rounded-full transition-all duration-500"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-8 pt-6 border-t border-slate-50 flex items-center justify-between">
                        <div>
                            <p class="text-[10px] font-bold text-slate-400">{{ __('AYLIK HEDEF') }}</p>
                            <p class="text-xs font-black text-slate-900">₺1.2M / %72 {{ __('Başarı') }}</p>
                        </div>
                        <button class="w-8 h-8 rounded-full bg-slate-50 flex items-center justify-center text-slate-400 hover:bg-slate-100 transition-colors">
                            <i class='bx bx-chevron-right'></i>
                        </button>
                    </div>
                @endif
            </div>

            <!-- Elegant Timeline -->
            <div class="bg-white p-8 rounded-md border border-slate-100 shadow-sm">
                <h3 class="text-sm font-black text-slate-900 mb-6 uppercase tracking-wider">{{ __('Son Etkileşimler') }}</h3>
                
                @if($recentActivities->isEmpty())
                     <!-- EMPTY STATE: Timeline -->
                    <div class="flex flex-col items-center justify-center py-6 text-center text-slate-400">
                        <i class='bx bx-time-five text-3xl mb-3 opacity-30'></i>
                        <p class="text-xs">{{ __('Henüz bir aktivite yok.') }}</p>
                    </div>
                @else
                    <div class="space-y-6 relative before:absolute before:left-[7px] before:top-2 before:bottom-2 before:w-px before:bg-slate-100">
                        @foreach($recentActivities as $activity)
                        <div class="flex gap-4 relative z-10">
                            @php
                                $colors = ['bg-emerald-500', 'bg-sky-500', 'bg-slate-200', 'bg-amber-500'];
                                // Map activity types to colors
                                if ($activity->activity_type == 'created') $color = 'bg-slate-200';
                                elseif ($activity->activity_type == 'status_changed' && $activity->new_value == 'approved') $color = 'bg-emerald-500';
                                elseif ($activity->activity_type == 'status_changed' && $activity->new_value == 'rejected') $color = 'bg-rose-500';
                                elseif ($activity->activity_type == 'sent') $color = 'bg-indigo-500';
                                else $color = 'bg-sky-500';
                            @endphp
                            <div class="w-4 h-4 rounded-full {{ $color }} border-4 border-white shadow-sm mt-1"></div>
                            <div>
                                <p class="text-[13px] font-bold text-slate-900 leading-none">
                                    {{ $activity->user->name ?? __('Sistem') }} 
                                    <span class="font-normal text-slate-500 text-[11px]">{{ $activity->description }}</span>
                                </p>
                                <p class="text-[10px] text-slate-400 font-bold mt-1 uppercase">
                                    {{ $activity->created_at->diffForHumans() }} • 
                                    @if($activity->proposal)
                                        <a href="{{ route('proposals.show', $activity->proposal) }}" class="hover:text-indigo-500 transition-colors">
                                            {{ $activity->proposal->proposal_number }}
                                        </a>
                                    @endif
                                </p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection


