@extends('tenant.layouts.onboarding')

@section('content')
<!-- Decorative BG -->
<div class="fixed inset-0 z-0 bg-white"></div>

<!-- Progress Bar -->
<div class="fixed top-0 left-0 w-full h-1 z-[60]">
    <div class="h-full bg-slate-900 w-full" style="width: 100%"></div>
</div>

<div class="relative z-10 min-h-screen py-16 px-4 font-['Plus_Jakarta_Sans']"
     x-data="{ 
        billing: 'monthly',
        period() { return this.billing === 'yearly' ? '/yıl' : '/ay'; }
     }">
    
    @if(!$selectedPlan)
        <!-- ============================================== -->
        <!-- PLAN SELECTION STATE (Matches onboarding/plans.blade.php) -->
        <!-- ============================================== -->

        <!-- Hero Header -->
        <div class="text-center max-w-4xl mx-auto mb-16">
            <h1 class="text-xl md:text-xl font-extrabold text-[#2e2e2e] mb-6 tracking-tight">
                Paketinizi Yükseltin
            </h1>
            
            <p class="text-slate-500 font-medium  text-lg">
               İhtiyacınıza en uygun planı seçerek hemen kullanmaya başlayın.
            </p>
             <p class="text-slate-500 font-medium mb-10 text-lg">
               <span class="font-bold text-indigo-600">İstediğiniz zaman iptal edebilirsiniz.</span> 
            </p>
    
            <!-- Hostinger-style Toggle -->
            <div class="inline-flex bg-slate-100 p-1.5 rounded-lg">
                <button 
                    @click="billing = 'monthly'"
                    class="px-6 py-2.5 rounded-md text-sm font-bold transition-all"
                    :class="billing === 'monthly' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-700'"
                >
                    Aylık
                </button>
                <button 
                    @click="billing = 'yearly'"
                    class="px-6 py-2.5 rounded-md text-sm font-bold transition-all relative"
                    :class="billing === 'yearly' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-700'"
                >
                    Tasarruf Edin (Yıllık)
                </button>
            </div>
        </div>

        <!-- Plans Grid -->
        <div class="max-w-[1400px] mx-auto grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 items-start">
            @foreach($plans as $plan)
                <!-- Card -->
                <div class="relative flex flex-col bg-white rounded-2xl transition-all duration-300
                    {{ $plan->is_popular 
                        ? 'border-2 border-indigo-600 shadow-xl overflow-hidden pt-0 mt-0 z-10' 
                        : 'border border-slate-200 pt-8 hover:shadow-lg' 
                    }}
                ">
                    <!-- Popular Header -->
                    @if($plan->is_popular)
                        <div class="bg-indigo-600 text-white text-center py-2.5 text-xs font-bold uppercase tracking-widest mb-6">
                            En Çok Tercih Edilen
                        </div>
                    @endif
    
                    <div class="px-6 pb-8 flex flex-col">
                        
                        <!-- Discount Badge -->
                        <div class="mb-4 flex flex-col gap-1 min-h-[50px]">
                            @php
                                $monthlyTotal = $plan->price_monthly * 12;
                                $discountPct = $monthlyTotal > 0 ? round((($monthlyTotal - $plan->price_yearly) / $monthlyTotal) * 100) : 0;
                            @endphp
                            
                            @if($discountPct > 0)
                                <div class="self-start bg-[#D9F99D] text-slate-900 px-3 py-1 rounded-full text-[11px] font-extrabold uppercase tracking-wide" x-show="billing === 'yearly'">
                                    %{{ $discountPct }} İNDİRİM
                                </div>
                            @else
                               <div class="min-h-[24px]"></div>
                            @endif
    
                            <h3 class="text-xl font-bold text-slate-800">{{ $plan->name }}</h3>
                            <p class="text-xs text-slate-500 font-medium leading-relaxed">{{ $plan->description }}</p>
                        </div>
    
                        <!-- Price Block -->
                        <div class="mb-8">
                            <!-- Old Price -->
                            <div class="text-slate-400 text-xs font-medium line-through mb-0.5" x-text="billing === 'yearly' ? '₺' + ({{ floatval($plan->price_monthly) }} * 1.5).toFixed(2) : ''" style="min-h:16px"></div>
                            
                            <!-- Current Price -->
                            <div class="flex items-baseline gap-1">
                                <span class="text-xs font-bold text-slate-700">₺</span>
                                <span class="text-5xl font-extrabold text-slate-900 tracking-tight" 
                                      x-text="billing === 'monthly' ? '{{ floatval($plan->price_monthly) }}' : '{{ floatval($plan->price_yearly) }}'">
                                </span>
                                <span class="text-lg font-bold text-slate-700" x-text="period()"></span>
                            </div>
                            
                            <!-- Bonus Text -->
                            <p class="text-indigo-600 text-xs font-bold mt-2" x-show="billing === 'yearly'">
                                +3 ay ücretsiz
                            </p>
                        </div>
    
                        <!-- CTA Button -->
                        <a :href="'{{ route('subscription.upgrade') }}?plan={{ $plan->id }}&billing=' + billing" class="w-full py-3.5 rounded-lg font-bold text-sm text-center transition-all duration-200 transform hover:scale-[1.02] block mb-8
                            {{ $plan->is_popular 
                                ? 'bg-indigo-600 text-white hover:bg-indigo-700 shadow-md shadow-indigo-200' 
                                : 'bg-white border-2 border-slate-900 text-slate-900 hover:bg-slate-50' 
                            }}">
                            Paketi Seç
                        </a>
    
                        <!-- Features List -->
                        <div x-data="{ expanded: false }" class="flex-grow">
                             <!-- Limits Grid -->
                            @php $limits = $plan->limits ?? []; @endphp
                            <ul class="mb-6 space-y-2 text-sm text-slate-700">
                                 @if(isset($limits['user_count']))
                                    <li class="flex items-center gap-2 font-bold">
                                        <i class='bx bx-user text-slate-400'></i>
                                        {{ $limits['user_count'] == -1 ? 'Sınırsız' : $limits['user_count'] }} Kullanıcı
                                    </li>
                                @endif
                            </ul>
    
                            <div class="h-px bg-slate-100 mb-6"></div>
    
                            <!-- Main Features -->
                            <ul class="space-y-3">
                                @php 
                                    $featureLabels = \App\Models\Plan::getAvailableFeatures();
                                    $planFeatures = $plan->features ?? [];
                                @endphp
    
                                @foreach($planFeatures as $loopIndex => $featureKey)
                                    @if(isset($featureLabels[$featureKey]))
                                        <li class="flex items-start gap-3"
                                            x-show="expanded || {{ $loopIndex }} < 5"
                                            x-transition
                                        >
                                            <i class='bx bx-check text-green-500 text-lg shrink-0 mt-0.5'></i>
                                            <span class="text-sm font-medium text-slate-600 leading-tight">
                                                {{ $featureLabels[$featureKey] }}
                                                @if(in_array($featureKey, ['ai_creation', 'netgsm_integration']))
                                                    <span class="ml-1 text-[10px] bg-indigo-100 text-indigo-600 px-1.5 py-0.5 rounded font-bold uppercase">YENİ</span>
                                                @endif
                                            </span>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
    
                             @if(count($planFeatures) > 5)
                                <button @click="expanded = !expanded" class="mt-4 flex items-center gap-1 text-sm font-bold text-slate-400 hover:text-indigo-600 transition-colors">
                                    <span x-text="expanded ? 'Daha az göster' : 'Tüm özellikleri gör'"></span>
                                    <i class='bx' :class="expanded ? 'bx-chevron-up' : 'bx-chevron-down'"></i>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

    @else
        <!-- ============================================== -->
        <!-- CHECKOUT STATE (Centered & Clean) -->
        <!-- ============================================== -->
        <div class="max-w-5xl mx-auto">
            <a href="{{ route('subscription.upgrade') }}" class="inline-flex items-center gap-2 text-slate-400 hover:text-slate-900 font-bold text-sm mb-8 transition-colors group">
                <i class='bx bx-arrow-back text-lg group-hover:-translate-x-1 transition-transform'></i>
                <span>Paketlere Geri Dön</span>
            </a>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-start">
                
                <!-- Left: Payment Form -->
                <div class="lg:col-span-8 space-y-6">
                    <div class="bg-white rounded-2xl border border-slate-200 shadow-lg overflow-hidden">
                        <div class="p-4 md:p-6 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                            <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                                <i class='bx bxs-credit-card text-indigo-600 text-xl'></i>
                                Güvenli Ödeme
                            </h2>
                            <div class="flex gap-2">
                                <i class='bx bxl-visa text-2xl text-slate-400'></i>
                                <i class='bx bxl-mastercard text-2xl text-slate-400'></i>
                            </div>
                        </div>
                        
                        <div class="p-4 md:p-8">
                            <form id="payment-form" action="{{ route('subscription.store') }}" method="POST" class="space-y-6">
                                @csrf
                                <input type="hidden" name="plan_id" value="{{ $selectedPlan->id }}">
                                <input type="hidden" name="billing_period" value="{{ request('billing', 'monthly') }}">
                                
                                <div class="grid grid-cols-1 gap-6">
                                    <div class="space-y-1">
                                        <label class="text-xs font-bold text-slate-700 uppercase tracking-wide ml-1">Kart Üzerindeki İsim</label>
                                        <input type="text" placeholder="Ad Soyad" class="w-full bg-white border border-slate-200 rounded-lg px-4 py-3 text-slate-900 font-bold focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 transition-all outline-none placeholder:text-slate-300">
                                    </div>
                                    
                                    <div class="space-y-1">
                                        <label class="text-xs font-bold text-slate-700 uppercase tracking-wide ml-1">Kart Numarası</label>
                                        <div class="relative group">
                                            <input type="text" placeholder="0000 0000 0000 0000" class="w-full bg-white border border-slate-200 rounded-lg px-4 py-3 pl-12 text-slate-900 font-bold focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 transition-all outline-none placeholder:text-slate-300">
                                            <div class="absolute left-4 top-3 text-slate-400 group-focus-within:text-indigo-600 transition-colors">
                                                <i class='bx bxl-mastercard text-xl'></i>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div class="space-y-1">
                                            <label class="text-xs font-bold text-slate-700 uppercase tracking-wide ml-1">Son Kullanma</label>
                                            <input type="text" placeholder="AA / YY" class="w-full bg-white border border-slate-200 rounded-lg px-4 py-3 text-slate-900 font-bold focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 transition-all outline-none placeholder:text-slate-300">
                                        </div>
                                        <div class="space-y-1">
                                            <label class="text-xs font-bold text-slate-700 uppercase tracking-wide ml-1">CVC / CVV</label>
                                            <div class="relative group">
                                                <input type="text" placeholder="123" class="w-full bg-white border border-slate-200 rounded-lg px-4 py-3 text-slate-900 font-bold focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 transition-all outline-none placeholder:text-slate-300">
                                                <i class='bx bx-help-circle absolute right-4 top-3 text-slate-300 hover:text-slate-500 cursor-help'></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <button onclick="document.getElementById('payment-form').submit()" class="w-full py-4 bg-slate-900 text-white font-bold rounded-xl hover:bg-slate-800 transition-all shadow-xl shadow-slate-900/20 flex items-center justify-center gap-2 group">
                        <i class='bx bx-lock-alt text-xl'></i>
                        <span>Güvenli Ödeme Yap ve Başla</span>
                    </button>
                </div>

                <!-- Right: Summary Sidebar -->
                <div class="lg:col-span-4 sticky top-6 space-y-6">
                    <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm">
                        <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-6">Sipariş Özeti</h3>
                        
                        <div class="space-y-4">
                            <div class="flex justify-between items-start">
                                <div>
                                    <div class="font-bold text-base text-slate-900">{{ $selectedPlan->name }}</div>
                                    <div class="text-xs text-slate-500">{{ request('billing') === 'yearly' ? 'Yıllık Plan' : 'Aylık Plan' }}</div>
                                </div>
                                <div class="font-bold text-slate-900">
                                    ₺{{ request('billing') === 'yearly' ? $selectedPlan->price_yearly : $selectedPlan->price_monthly }}
                                </div>
                            </div>
                            
                            @if(request('billing') === 'yearly')
                                <div class="flex justify-between items-center text-xs text-emerald-600 font-bold bg-emerald-50 p-2 rounded-md">
                                    <span>Yıllık İndirim</span>
                                    <span>-%20</span>
                                </div>
                            @endif

                            <div class="border-t border-slate-100 my-2"></div>

                            <div class="flex justify-between items-center">
                                <span class="text-sm font-bold text-slate-700">Toplam Tutar</span>
                                <span class="text-2xl font-black text-slate-900 tracking-tight">
                                    ₺{{ request('billing') === 'yearly' ? $selectedPlan->price_yearly : $selectedPlan->price_monthly }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="text-center">
                        <div class="flex items-center justify-center gap-2 text-slate-400 mb-2">
                             <i class='bx bxs-check-shield text-emerald-500'></i>
                             <span class="text-xs font-medium">SSL Korumalı Güvenli Ödeme</span>
                        </div>
                        <p class="text-[10px] text-slate-400">
                             Ödemeyi onaylayarak Fiyato Kullanım Koşulları'nı ve Gizlilik Politikası'nı kabul etmiş olursunuz.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
