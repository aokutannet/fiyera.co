@extends('tenant.layouts.app')

@section('content')
<div class="space-y-10" x-data="{ 
    billing: 'monthly',
    period() { return this.billing === 'yearly' ? '/yıl' : '/ay'; }
}">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900 tracking-tight">Abonelik Yönetimi</h1>
            <p class="text-slate-500 mt-2 font-medium">Paket detaylarınız, kullanım limitleriniz ve yükseltme seçenekleri.</p>
            
           
        </div>
        
        
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Plan Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Current Plan Card -->
            <div class="bg-white p-4 md:p-8 rounded-md border border-slate-100 shadow-sm relative overflow-hidden">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-8">
                    <div>
                         <div class="flex items-center gap-3 mb-2">
                            <span class="bg-slate-100 text-slate-600 px-2.5 py-1 rounded text-[11px] font-bold uppercase tracking-wider">
                                Mevcut Paket
                            </span>
                            @if($remainingTrialDays > 0)
                                <span class="bg-indigo-50 text-indigo-600 px-2.5 py-1 rounded text-[11px] font-bold uppercase tracking-wider animate-pulse">
                                    Deneme Sürümü
                                </span>
                            @endif
                        </div>
                        <h2 class="text-3xl font-black text-slate-900 capitalize">{{ $plan->name }}</h2>
                        <div class="mt-2 text-slate-500 font-medium flex items-center gap-2">
                             <i class='bx bx-calendar'></i>
                             @if($remainingTrialDays > 0)
                                Deneme süresi bitiş: <span class="text-slate-900 font-bold">{{ $trialEndDate->format('d.m.Y') }}</span>
                            @else
                                @if($activeSubscription)
                                    @if($activeSubscription->price == 0)
                                        Yenilenme: <span class="text-slate-900 font-bold">Süresiz</span>
                                    @else
                                        Yenilenme: <span class="text-slate-900 font-bold">{{ $activeSubscription->ends_at ? $activeSubscription->ends_at->format('d.m.Y') : '-' }}</span>
                                    @endif
                                @else
                                    Yenilenme: <span class="text-slate-900 font-bold">
                                        {{ $tenant->trial_ends_at ? $tenant->trial_ends_at->format('d.m.Y') : '-' }}
                                    </span>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Trial Progress Bar -->
                @if($remainingTrialDays > 0)
                <div class="bg-slate-50 rounded-xl p-6 border border-slate-100">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-sm font-bold text-slate-700">Deneme Süresi</span>
                        <span class="text-sm font-black text-indigo-600">{{ $remainingTrialDays }} Gün Kaldı</span>
                    </div>
                    <!-- Custom Progress Bar -->
                    <div class="h-3 bg-slate-200 rounded-full overflow-hidden">
                         @php 
                            $totalDays = 14;
                            $usedDays = $totalDays - $remainingTrialDays;
                            $percent = max(0, min(100, ($usedDays / $totalDays) * 100)); 
                        @endphp
                        <div style="width: {{ $percent }}%" class="h-full bg-indigo-600 rounded-full transition-all duration-500"></div>
                    </div>
                    <div class="flex justify-between mt-2 text-[11px] text-slate-400 font-bold uppercase">
                        <span>0 Gün</span>
                        <span>14 Gün</span>
                    </div>
                    <p class="text-xs text-slate-500 mt-4 leading-relaxed">
                        Deneme süreniz dolmadan paketinizi seçerek verilerinizi koruyun ve kesintisiz kullanıma devam edin.
                    </p>
                </div>
                @else
                    @if(isset($isReadOnly) && $isReadOnly)
                        <div class="prose prose-sm text-rose-600 font-medium max-w-none mb-6">
                            <p>Paket kullanım süreniz dolmuştur. Hizmeti kullanmaya devam etmek için lütfen ödeme yapınız.</p>
                        </div>
                        
                        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 p-4 rounded-xl border border-rose-200 bg-rose-50/50">
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0 w-10 h-10 bg-white rounded-lg border border-rose-100 flex items-center justify-center text-rose-500 shadow-sm">
                                    <i class='bx bx-lock-alt text-xl'></i>
                                </div>
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <h3 class="text-sm font-bold text-rose-950">Erişim Kısıtlı</h3>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-700 uppercase tracking-wide">Salt Okunur</span>
                                    </div>
                                    <p class="text-xs font-medium text-rose-600/80 leading-relaxed max-w-xl">
                                        Deneme süreciniz sona ermiştir. Paketinizi kullanmaya devam etmek ve diğer paketlere geçiş yapabilmek için ödeme yapmanız gerekmektedir.
                                    </p>
                                </div>
                            </div>
                            <a href="{{ route('subscription.upgrade') }}" class="w-full md:w-auto flex-shrink-0 inline-flex items-center justify-center gap-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold px-5 py-2.5 rounded-lg transition-all shadow-sm shadow-rose-200 hover:shadow-rose-300 transform hover:-translate-y-0.5">
                                <span>Ödeme Yap ve Aktifleştir</span>
                                <i class='bx bx-credit-card text-base'></i>
                            </a>
                        </div>
                    @else
                        <div class="prose prose-sm text-slate-500 max-w-none">
                            <p>Aboneliğiniz aktif durumda. Tüm özelliklere erişiminiz devam ediyor.</p>
                        </div>
                    @endif
                @endif

            </div>
        </div>

        <!-- Right Side: Usage & Actions -->
        <div class="space-y-6">
            <!-- Usage Summary -->
            <div class="bg-white p-6 rounded-md border border-slate-100 shadow-sm">
                <h3 class="font-bold text-slate-900 mb-6 flex items-center gap-2">
                    <i class='bx bx-pie-chart-alt-2 text-slate-400'></i> Kullanım Özeti
                </h3>
                <div class="space-y-6">
                    @foreach($usage as $key => $metric)
                    <div>
                            <div class="flex justify-between text-xs font-bold mb-2">
                            <span class="text-slate-500 uppercase tracking-wider">{{ $metric['label'] }}</span>
                            <span class="text-slate-900">{{ $metric['used'] }} / {{ $metric['limit'] == -1 ? '∞' : $metric['limit'] }}</span>
                        </div>

                        @php
                            $usagePercent = $metric['limit'] > 0 ? ($metric['used'] / $metric['limit']) * 100 : 0;
                            $isUnlimited = $metric['limit'] == -1;
                        @endphp
                        <div class="h-2 bg-slate-100 rounded-full overflow-hidden relative">
                            @if($isUnlimited)
                                <div class="absolute inset-0 bg-emerald-500/20 w-full h-full striped-bar"></div>
                            @else
                                <div style="width: {{ $usagePercent }}%" class="h-full rounded-full {{ $usagePercent > 90 ? 'bg-rose-500' : 'bg-indigo-600' }}"></div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                
                <div class="mt-8 pt-6 border-t border-slate-50">
                     <a href="{{ route('subscription.upgrade') }}" class="w-full py-4 rounded-xl bg-slate-900 text-white font-bold hover:bg-slate-800 transition-all flex items-center justify-center gap-2 shadow-lg shadow-slate-900/20">
                        @if($remainingTrialDays > 0)
                            Tam Sürüme Geç <i class='bx bx-party'></i>
                        @else
                            Paketi Yükselt <i class='bx bx-credit-card'></i>
                        @endif
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment History (Full Width) -->
    <div class="mt-8">
        <h3 class="font-bold text-slate-900 mb-6 flex items-center gap-2">
            <i class='bx bx-history text-slate-400'></i> Ödeme Geçmişi
        </h3>
        <div class="bg-white rounded-md border border-slate-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50/50 text-xs font-bold text-slate-500 uppercase tracking-wider">
                            <th class="px-4 md:px-6 py-4">Paket</th>
                            <th class="px-4 md:px-6 py-4">Tutar</th>
                            <th class="px-4 md:px-6 py-4">Dönem</th>
                            <th class="px-4 md:px-6 py-4">Tarih</th>
                            <th class="px-4 md:px-6 py-4">Durum</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($attempts as $attempt)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-4 md:px-6 py-4">
                                <span class="text-sm font-bold text-slate-900">{{ $attempt->plan->name ?? 'Bilinmiyor' }}</span>
                            </td>
                            <td class="px-4 md:px-6 py-4">
                                <span class="text-sm font-bold text-slate-900">₺{{ number_format($attempt->price, 2) }}</span>
                            </td>
                            <td class="px-4 md:px-6 py-4">
                                <span class="text-xs font-bold text-slate-500 uppercase">{{ $attempt->billing_period === 'yearly' ? 'Yıllık' : 'Aylık' }}</span>
                            </td>
                            <td class="px-4 md:px-6 py-4">
                                <div class="text-xs font-bold text-slate-900">{{ $attempt->created_at->format('d.m.Y') }}</div>
                                <div class="text-[10px] text-slate-400">{{ $attempt->created_at->format('H:i') }}</div>
                            </td>
                            <td class="px-4 md:px-6 py-4">
                                @if($attempt->status === 'success')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-lg text-[10px] font-black uppercase bg-emerald-50 text-emerald-600 border border-emerald-100">
                                        <i class='bx bxs-check-circle'></i> Başarılı
                                    </span>
                                @elseif($attempt->status === 'pending')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-lg text-[10px] font-black uppercase bg-amber-50 text-amber-600 border border-amber-100">
                                        <i class='bx bxs-watch'></i> Bekliyor
                                    </span>
                                @else
                                    <div class="flex flex-col">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-lg text-[10px] font-black uppercase bg-red-50 text-red-600 border border-red-100 w-fit">
                                            <i class='bx bxs-error-circle'></i> Başarısız
                                        </span>
                                        @if($attempt->error_message)
                                            <span class="text-[10px] text-red-400 mt-1 max-w-[150px] truncate" title="{{ $attempt->error_message }}">{{ $attempt->error_message }}</span>
                                        @endif
                                    </div>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-slate-400 text-sm">
                                Henüz işlem geçmişi bulunmuyor.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Success Modal -->
@if(session('plan_upgraded'))
<div class="fixed inset-0 z-50 flex items-center justify-center p-4" x-data="{ show: true }" x-show="show">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="show = false"></div>
    <div class="relative bg-white rounded-3xl p-8 max-w-sm w-full text-center shadow-2xl animate-[bounce_0.5s_ease-out]">
        <div class="w-20 h-20 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-6 text-emerald-600">
            <i class='bx bx-party text-4xl'></i>
        </div>
        <h3 class="text-2xl font-black text-slate-900 mb-2">Tebrikler! 🎉</h3>
        <p class="text-slate-500 font-medium mb-8">Paketiniz başarıyla yükseltildi. Yeni özelliklerin keyfini çıkarın!</p>
        <button @click="show = false" class="w-full py-3 bg-indigo-600 text-white font-bold rounded-xl hover:bg-indigo-700 transition-colors">
            Harika!
        </button>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        var duration = 3 * 1000;
        var animationEnd = Date.now() + duration;
        var defaults = { startVelocity: 30, spread: 360, ticks: 60, zIndex: 60 };

        function random(min, max) { return Math.random() * (max - min) + min; }

        var interval = setInterval(function() {
            var timeLeft = animationEnd - Date.now();
            if (timeLeft <= 0) return clearInterval(interval);
            var particleCount = 50 * (timeLeft / duration);
            confetti(Object.assign({}, defaults, { particleCount, origin: { x: random(0.1, 0.3), y: Math.random() - 0.2 } }));
            confetti(Object.assign({}, defaults, { particleCount, origin: { x: random(0.7, 0.9), y: Math.random() - 0.2 } }));
        }, 250);
    });
</script>
@endif

<style>
.striped-bar {
    background-image: linear-gradient(45deg,rgba(16, 185, 129, 0.1) 25%,transparent 25%,transparent 50%,rgba(16, 185, 129, 0.1) 50%,rgba(16, 185, 129, 0.1) 75%,transparent 75%,transparent);
    background-size: 1rem 1rem;
}
</style>
@endsection
