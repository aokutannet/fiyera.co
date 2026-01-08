@extends('admin.layouts.app')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Siparişler</h1>
            <p class="text-slate-500 font-medium mt-1">Tüm abonelik satın alımları ve ödeme geçmişi.</p>
        </div>
        <div class="flex items-center gap-3">
             <div class="bg-white border border-slate-200 rounded-xl px-4 py-2 text-sm font-bold text-slate-600 shadow-sm">
                Toplam: {{ $orders->total() }} Sipariş
             </div>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100 text-xs font-bold text-slate-500 uppercase tracking-wider">
                        <th class="px-6 py-4">Sipariş ID</th>
                        <th class="px-6 py-4">Firma</th>
                        <th class="px-6 py-4">Paket</th>
                        <th class="px-6 py-4">Tutar</th>
                        <th class="px-6 py-4">Periyot</th>
                        <th class="px-6 py-4">Ödeme Kanalı</th>
                        <th class="px-6 py-4">Tarih</th>
                        <th class="px-6 py-4">Durum</th>
                        <th class="px-6 py-4"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($orders as $order)
                    <tr class="hover:bg-slate-50/80 transition-colors group">
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.orders.show', $order->id) }}" class="font-mono text-xs text-indigo-600 hover:text-indigo-800 font-bold hover:underline">
                                #{{ substr($order->payment_id ?? $order->id, -8) }}
                            </a>
                        </td>
                        <td class="px-6 py-4">
                            @if($order->tenant)
                                <a href="{{ route('admin.tenants.show', $order->tenant->id) }}" class="flex items-center gap-3 group-hover:text-indigo-600 transition-colors">
                                    <div class="w-8 h-8 rounded-lg bg-slate-900 text-white flex items-center justify-center font-bold text-xs">
                                        {{ substr($order->tenant->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900 text-sm">{{ $order->tenant->name }}</div>
                                        <div class="text-xs text-slate-400">{{ $order->tenant->domain ?? 'N/A' }}</div>
                                    </div>
                                </a>
                            @else
                                <span class="text-slate-400 italic">Silinmiş Firma</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold bg-indigo-50 text-indigo-700">
                                {{ $order->plan->name ?? 'Bilinmeyen Paket' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="font-black text-slate-900">₺{{ number_format($order->price, 2) }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-xs font-bold text-slate-500 uppercase">
                                {{ $order->billing_period === 'yearly' ? 'Yıllık' : 'Aylık' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2 text-sm font-medium text-slate-600">
                                @if($order->payment_provider === 'stripe')
                                    <i class='bx bxl-stripe text-indigo-600 text-lg'></i> Stripe
                                @elseif($order->payment_provider === 'mock_gateway')
                                    <i class='bx bx-credit-card text-emerald-600 text-lg'></i> Kredi Kartı
                                @else
                                    <i class='bx bx-wallet text-slate-400 text-lg'></i> {{ $order->payment_provider ?? 'Diğer' }}
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-bold text-slate-900">{{ $order->created_at->format('d.m.Y') }}</div>
                            <div class="text-xs text-slate-400">{{ $order->created_at->format('H:i') }}</div>
                        </td>
                        <td class="px-6 py-4">
                            @if($order->status === 'active')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase bg-emerald-50 text-emerald-600">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Başarılı
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase bg-slate-100 text-slate-500">
                                    {{ $order->status }}
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                             <a href="{{ route('admin.orders.show', $order->id) }}" class="text-slate-400 hover:text-indigo-600 text-xl transition-colors">
                                <i class='bx bx-chevron-right'></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-6 py-12 text-center text-slate-400">
                            <div class="flex flex-col items-center gap-2">
                                <i class='bx bx-receipt text-3xl opacity-50'></i>
                                <span class="text-sm font-medium">Henüz hiç sipariş bulunmuyor.</span>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($orders->hasPages())
        <div class="px-6 py-4 border-t border-slate-100">
            {{ $orders->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
