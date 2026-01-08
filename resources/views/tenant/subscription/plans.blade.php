@extends('tenant.layouts.app')

@section('content')
<div class="max-w-7xl mx-auto space-y-8" x-data="{ 
    billing: 'monthly',
    period() { return this.billing === 'yearly' ? '/yıl' : '/ay'; }
}">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <a href="{{ route('subscription.index') }}" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <i class='bx bx-arrow-back text-xl'></i>
                </a>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">Paket Seçimi</h1>
            </div>
            <p class="text-slate-500 font-medium ml-8">İşletmeniz için en uygun paketi seçin ve hemen kullanmaya başlayın.</p>
        </div>
        
        <!-- Billing Cycle Toggle -->
        <div class="inline-flex bg-white p-1.5 rounded-xl border border-slate-200 shadow-sm ml-8 md:ml-0">
            <button 
                @click="billing = 'monthly'"
                class="px-6 py-2.5 rounded-lg text-sm font-bold transition-all"
                :class="billing === 'monthly' ? 'bg-slate-900 text-white shadow-md' : 'text-slate-500 hover:text-slate-900'"
            >
                Aylık
            </button>
            <button 
                @click="billing = 'yearly'"
                class="px-6 py-2.5 rounded-lg text-sm font-bold transition-all flex items-center gap-2"
                :class="billing === 'yearly' ? 'bg-slate-900 text-white shadow-md' : 'text-slate-500 hover:text-slate-900'"
            >
                Yıllık
                <span class="text-[10px] bg-emerald-100 text-emerald-700 px-1.5 py-0.5 rounded font-extrabold uppercase tracking-wide">
                    %20 İndirim
                </span>
            </button>
        </div>
    </div>

    <!-- Plans Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 items-start pt-4">
        @foreach($plans as $p)
            @php 
                $isCurrent = $p->id === $tenant->subscription_plan_id;
                $isPopular = $p->is_popular;
            @endphp
            
            <!-- Plan Card -->
            <div class="relative flex flex-col bg-white rounded-2xl transition-all duration-300
                {{ $isPopular ? 'border-2 border-indigo-600 shadow-xl pt-0 mt-0 z-10' : 'border border-slate-200 pt-8 hover:shadow-lg' }}
                {{ $isCurrent ? 'opacity-75 bg-slate-50' : '' }}
            ">
                @if($isPopular)
                    <div class="bg-indigo-600 text-white text-center py-2 text-[10px] font-bold uppercase tracking-widest mb-6 rounded-t-lg">
                        Önerilen
                    </div>
                @endif

                <div class="px-6 pb-8 flex flex-col h-full">
                    <!-- Header -->
                    <div class="mb-4">
                            @if($isCurrent)
                            <div class="inline-block bg-slate-200 text-slate-600 px-3 py-1 rounded-full text-[11px] font-extrabold uppercase tracking-wide mb-3">
                                Mevcut Paket
                            </div>
                        @else
                            <div class="h-[24px]"></div>
                        @endif
                        <h3 class="text-xl font-bold text-slate-900">{{ $p->name }}</h3>
                        <p class="text-xs text-slate-500 font-medium leading-relaxed mt-1 min-h-[40px]">{{ $p->description }}</p>
                    </div>

                    <!-- Price -->
                    <div class="mb-6">
                        <div class="text-slate-400 text-xs font-bold line-through ml-1" x-show="billing === 'yearly'">
                            ₺{{ number_format($p->price_monthly * 1.5, 2) }}
                        </div>
                        <div class="flex items-baseline gap-0.5">
                            <span class="text-xs font-bold text-slate-700">₺</span>
                            <span class="text-4xl font-black text-slate-900 tracking-tight" 
                                    x-text="billing === 'monthly' ? '{{ $p->price_monthly }}' : '{{ $p->price_yearly }}'">
                            </span>
                            <span class="text-sm font-bold text-slate-500" x-text="period()"></span>
                        </div>
                        <p class="text-emerald-600 text-[10px] font-bold mt-2" x-show="billing === 'yearly'">
                            +3 ay ücretsiz
                        </p>
                    </div>

                    <!-- Action -->
                    <form action="{{ route('onboarding.subscribe') }}" method="POST" class="mb-6">
                        @csrf
                        <input type="hidden" name="plan" value="{{ $p->slug }}">
                        <input type="hidden" name="billing_cycle" :value="billing">
                        
                        <input type="hidden" name="from_subscription_page" value="1">
                        @if(isset($tenant->trial_ends_at) && $tenant->trial_ends_at->isPast())
                             <input type="hidden" name="force_payment" value="1">
                        @endif
                        
                        @if($isCurrent)
                            <button type="button" disabled class="w-full py-3 rounded-xl border border-slate-200 bg-slate-100 text-slate-400 font-bold text-sm cursor-not-allowed">
                                Kullanılıyor
                            </button>
                        @else
                            <button type="submit" class="w-full py-3 rounded-xl font-bold text-sm transition-all duration-200 transform hover:scale-[1.02]
                                {{ $isPopular 
                                    ? 'bg-indigo-600 text-white hover:bg-indigo-700 shadow-lg shadow-indigo-200' 
                                    : 'bg-white border-2 border-slate-900 text-slate-900 hover:bg-slate-50' 
                                }}">
                                @if(isset($tenant->trial_ends_at) && $tenant->trial_ends_at->isPast())
                                                                    Ödeme Yap ve Başla
                                                                @else
                                                                    {{ $p->price_monthly > $plan->price_monthly ? 'Yükselt' : 'Seç' }}
                                                                @endif
                            </button>
                        @endif
                    </form>

                    <!-- Features -->
                    <div class="space-y-3 pt-6 border-t border-slate-100">
                            <!-- Limits -->
                        @php $limits = $p->limits ?? []; @endphp
                        <ul class="space-y-2 text-xs text-slate-700 mb-4">
                            <li class="flex items-center gap-2 font-bold">
                                <i class='bx bx-file text-slate-400'></i>
                                {{ $limits['proposal_monthly'] == -1 ? 'Sınırsız' : $limits['proposal_monthly'] }} Teklif / ay
                            </li>
                            <li class="flex items-center gap-2 font-bold">
                                <i class='bx bx-user text-slate-400'></i>
                                {{ $limits['user_count'] == -1 ? 'Sınırsız' : $limits['user_count'] }} Kullanıcı
                            </li>
                        </ul>
                        
                        <!-- Highlight Features -->
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
                                        <i class='bx bx-check text-green-500 text-lg shrink-0 mt-0.5'></i>
                                        <span class="text-xs font-medium text-slate-600 leading-tight">
                                            {{ $featureLabels[$featureKey] }}
                                            @if(in_array($featureKey, ['ai_creation', 'netgsm_integration']))
                                                <span class="ml-1 text-[10px] bg-indigo-100 text-indigo-600 px-1.5 py-0.5 rounded font-bold uppercase">YENİ</span>
                                            @endif
                                        </span>
                                    </li>
                                @endif
                            @endforeach

                            @if(count($planFeatures) > 5)
                                <li class="pt-2">
                                    <button type="button" @click="expanded = !expanded" class="flex items-center gap-1 text-xs font-bold text-slate-400 hover:text-indigo-600 transition-colors">
                                        <span x-text="expanded ? 'Daha az göster' : 'Tüm özellikleri gör'"></span>
                                        <i class='bx' :class="expanded ? 'bx-chevron-up' : 'bx-chevron-down'"></i>
                                    </button>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
