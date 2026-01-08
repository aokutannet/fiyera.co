@extends('admin.layouts.app')

@section('content')
<div class="space-y-10">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900 tracking-tight">Yönetim Paneli</h1>
            <p class="text-slate-500 mt-2 font-medium">Sistem genelindeki tüm aktivite ve istatistikler.</p>
        </div>
        
        <div class="flex items-center gap-3">
            <button class="h-12 px-6 rounded-2xl bg-white border border-slate-200 text-sm font-bold text-slate-700 hover:text-rose-600 hover:border-rose-100 transition-all shadow-sm hover:shadow-md flex items-center gap-2 group">
                <i class='bx bx-cloud-download text-xl group-hover:scale-110 transition-transform'></i> Yedek Al
            </button>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Tenants -->
        <div class="bg-white p-6 rounded-md border border-slate-100 shadow-sm relative overflow-hidden">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest relative z-10">Toplam Firma</p>
            <div class="flex items-end justify-between mt-4 relative z-10">
                <h3 class="text-3xl font-black text-slate-900">{{ $totalTenants }}</h3>
                <div class="flex items-center gap-1 text-[11px] font-bold text-slate-600 bg-slate-50 px-2.5 py-1 rounded-xl">
                    <i class='bx bxs-business'></i> Kayıtlı
                </div>
            </div>
        </div>

        <!-- Active Tenants -->
        <div class="bg-white p-6 rounded-md border border-slate-100 shadow-sm relative overflow-hidden">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest relative z-10">Aktif Firma</p>
            <div class="flex items-end justify-between mt-4 relative z-10">
                <h3 class="text-3xl font-black text-slate-900">{{ $activeTenants }}</h3>
                <div class="flex items-center gap-1 text-[11px] font-bold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-xl">
                    <i class='bx bx-check-circle'></i> Çalışıyor
                </div>
            </div>
        </div>

        <!-- Total Users -->
        <div class="bg-white p-6 rounded-md border border-slate-100 shadow-sm relative overflow-hidden">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest relative z-10">Toplam Kullanıcı</p>
            <div class="flex items-end justify-between mt-4 relative z-10">
                <h3 class="text-3xl font-black text-slate-900">{{ $totalUsers }}</h3>
                <div class="flex items-center gap-1 text-[11px] font-bold text-sky-600 bg-sky-50 px-2.5 py-1 rounded-xl">
                    <i class='bx bx-user'></i> Global
                </div>
            </div>
        </div>

        <!-- MRR Estimate -->
        <div class="bg-rose-600 p-6 rounded-md shadow-xl shadow-rose-600/20 group cursor-default relative overflow-hidden">
            <div class="absolute -right-4 -top-4 w-28 h-28 bg-white/10 rounded-full blur-2xl"></div>
            <p class="text-[10px] font-black text-white/60 uppercase tracking-widest relative z-10">Bu Ay Gelir</p>
            <div class="flex items-end justify-between mt-4 relative z-10">
                <h3 class="text-3xl font-black text-white">₺{{ number_format($monthlyRevenue, 2) }}</h3>
                <div class="flex items-center gap-1 text-[11px] font-bold text-white bg-white/20 px-2.5 py-1 rounded-xl backdrop-blur-md">
                    <i class='bx bx-trending-up'></i> Bu Ay
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Recent Tenants -->
        <div class="bg-white rounded-md border border-slate-100 shadow-sm overflow-hidden flex flex-col">
            <div class="px-6 py-4 border-b border-slate-50 flex items-center justify-between bg-slate-50/30">
                <h2 class="text-base font-black text-slate-900 flex items-center gap-2">
                    <i class='bx bx-buildings text-slate-400'></i> Son Eklenen Firmalar
                </h2>
                <a href="{{ route('admin.tenants.index') }}" class="text-xs font-bold text-slate-500 hover:text-indigo-600 transition-colors">
                    Tümünü Gör
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                     <tbody class="divide-y divide-slate-50">
                        @forelse($recentTenants as $tenant)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-slate-900 text-white flex items-center justify-center font-bold text-xs">
                                        {{ substr($tenant->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900 text-sm">{{ $tenant->name }}</div>
                                        <div class="text-xs text-slate-400">{{ $tenant->domain ?? 'N/A' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="text-xs font-bold text-slate-500">{{ $tenant->created_at->diffForHumans() }}</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.tenants.show', $tenant->id) }}" class="text-slate-400 hover:text-indigo-600 transition-colors">
                                    <i class='bx bx-chevron-right text-xl'></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-6 py-8 text-center text-slate-400 text-sm">
                                Henüz firma kaydı bulunmuyor.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="bg-white rounded-md border border-slate-100 shadow-sm overflow-hidden flex flex-col">
            <div class="px-6 py-4 border-b border-slate-50 flex items-center justify-between bg-slate-50/30">
                <h2 class="text-base font-black text-slate-900 flex items-center gap-2">
                    <i class='bx bx-money text-emerald-500'></i> Son Siparişler
                </h2>
                <a href="{{ route('admin.orders.index') }}" class="text-xs font-bold text-slate-500 hover:text-indigo-600 transition-colors">
                    Tümünü Gör
                </a>
            </div>
             <div class="overflow-x-auto">
                <table class="w-full text-left">
                     <tbody class="divide-y divide-slate-50">
                        @forelse($recentOrders as $order)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <span class="font-black text-slate-900 text-sm">₺{{ number_format($order->price, 2) }}</span>
                                <div class="text-xs text-slate-400 font-medium">{{ $order->billing_period === 'yearly' ? 'Yıllık' : 'Aylık' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                 @if($order->tenant)
                                    <div class="text-sm font-bold text-slate-900">{{ $order->tenant->name }}</div>
                                    <div class="text-xs text-slate-400">{{ $order->plan->name ?? '-' }}</div>
                                @else
                                    <span class="text-sm text-slate-400 italic">Silinmiş</span>
                                @endif
                            </td>
                             <td class="px-6 py-4 text-right">
                                <span class="text-xs font-bold text-slate-500">{{ $order->created_at->diffForHumans() }}</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="text-slate-400 hover:text-indigo-600 transition-colors">
                                    <i class='bx bx-chevron-right text-xl'></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-6 py-8 text-center text-slate-400 text-sm">
                                Henüz sipariş bulunmuyor.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
