@extends('tenant.layouts.app')

@section('content')
<div class="max-w-7xl mx-auto space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-500">
    <!-- Header/Navigation -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('products.index') }}" class="w-10 h-10 flex items-center justify-center rounded-xl bg-white border border-slate-100 text-slate-400 hover:text-slate-900 transition-all shadow-sm">
                <i class='bx bx-chevron-left text-2xl'></i>
            </a>
            <div>
                <h1 class="text-xl font-extrabold text-slate-950 tracking-tight">Ürün Detayı</h1>
                <p class="text-slate-500 text-sm mt-1">Ürün performansını ve geçmişini görüntüleyin.</p>
            </div>
        </div>
        
         <div class="flex items-center gap-3">
             <a href="{{ route('products.edit', $product) }}" class="px-6 py-2.5 rounded-xl bg-indigo-600 text-white text-sm font-bold hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-100 flex items-center gap-2">
                <i class='bx bx-edit-alt text-lg'></i>
                Ürünü Düzenle
            </a>
         </div>
    </div>

    <!-- Main Detail Card -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 md:p-8">
        <div class="flex flex-col md:flex-row gap-8">
            <!-- Product Image -->
            <div class="w-full md:w-64 h-64 flex-shrink-0 bg-slate-50 rounded-2xl border border-slate-100 p-2">
                @if($product->image_path)
                <img src="{{ Storage::disk('uploads')->url($product->image_path) }}" class="w-full h-full object-cover rounded-xl" alt="{{ $product->name }}">
                @else
                <div class="w-full h-full flex flex-col items-center justify-center text-slate-300">
                    <i class='bx bx-image text-4xl mb-2'></i>
                    <span class="text-xs font-bold">Görsel Yok</span>
                </div>
                @endif
            </div>

            <!-- Product Info -->
            <div class="flex-1 space-y-6">
                <div class="flex items-start justify-between">
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <h2 class="text-2xl font-black text-slate-950 tracking-tight">{{ $product->name }}</h2>
                            @if($product->status === 'active')
                            <span class="px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-600 text-[10px] font-bold border border-emerald-100">AKTİF</span>
                            @else
                            <span class="px-2.5 py-0.5 rounded-full bg-slate-50 text-slate-500 text-[10px] font-bold border border-slate-100">PASİF</span>
                            @endif
                        </div>
                        <p class="text-sm font-bold text-slate-400">{{ $product->code ?? 'Kod Yok' }} • {{ $product->category ?? 'Kategori Yok' }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Satış Fiyatı</p>
                        <p class="text-3xl font-black text-slate-900">{{ number_format($product->price, 2) }} <span class="text-sm text-slate-400">{{ $product->selling_currency }}</span></p>
                        <p class="text-xs font-bold text-slate-400 mt-1">+ %{{ $product->vat_rate }} KDV</p>
                    </div>
                </div>

                <p class="text-sm text-slate-500 leading-relaxed font-medium bg-slate-50 p-4 rounded-xl border border-slate-100/50">
                    {{ $product->description ?? 'Açıklama girilmemiş.' }}
                </p>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Maliyet</p>
                         <p class="text-lg font-black text-slate-900">{{ number_format($product->buying_price, 2) }} <span class="text-xs">{{ $product->buying_currency }}</span></p>
                    </div>
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Stok Durumu</p>
                        @if($product->stock_tracking)
                            <p class="text-lg font-black text-slate-900">{{ $product->stock }} <span class="text-xs font-bold text-slate-500">{{ $product->unit }}</span></p>
                        @else
                            <p class="text-lg font-black text-emerald-600">Sınırsız</p>
                        @endif
                    </div>
                     <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Toplam Satış</p>
                         <p class="text-lg font-black text-slate-900">{{ $history->count() }} <span class="text-xs font-bold text-slate-500">İşlem</span></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- History Tabs -->
    <div x-data="{ tab: 'proposals' }">
        <div class="flex items-center gap-2 mb-6 border-b border-slate-100">
            <button @click="tab = 'proposals'" :class="tab === 'proposals' ? 'text-indigo-600 border-indigo-600 bg-indigo-50/50' : 'text-slate-500 border-transparent hover:text-slate-900'" class="px-6 py-3 text-sm font-bold border-b-2 transition-all rounded-t-lg">
                Teklif Geçmişi
            </button>
            <button @click="tab = 'stock'" :class="tab === 'stock' ? 'text-indigo-600 border-indigo-600 bg-indigo-50/50' : 'text-slate-500 border-transparent hover:text-slate-900'" class="px-6 py-3 text-sm font-bold border-b-2 transition-all rounded-t-lg">
                Stok Hareketleri
            </button>
        </div>

        <!-- Proposals Content -->
        <div x-show="tab === 'proposals'" class="bg-white rounded-md border border-slate-100 shadow-sm overflow-hidden">
             <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50/50 border-b border-slate-100">
                            <th class="px-4 md:px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Tarih</th>
                            <th class="px-4 md:px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Teklif No</th>
                            <th class="px-4 md:px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Müşteri</th>
                            <th class="px-4 md:px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Adet / Tutar</th>
                            <th class="px-4 md:px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider text-right">Durum</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($history as $item)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-4 md:px-6 py-4">
                                <!-- Assumes Proposal has created_at -->
                                <p class="text-sm font-bold text-slate-700">{{ $item->proposal->created_at->format('d.m.Y') }}</p>
                            </td>
                             <td class="px-4 md:px-6 py-4">
                                <a href="{{ route('proposals.show', $item->proposal) }}" class="text-sm font-bold text-indigo-600 hover:text-indigo-700 hover:underline">
                                    #{{ $item->proposal->proposal_no }}
                                </a>
                            </td>
                             <td class="px-4 md:px-6 py-4">
                                <p class="text-sm font-bold text-slate-700">{{ $item->proposal->customer->company_name ?? '-' }}</p>
                            </td>
                            <td class="px-4 md:px-6 py-4">
                                <p class="text-sm font-bold text-slate-900">{{ $item->quantity }} {{ $product->unit }}</p>
                                <span class="text-xs text-slate-500 font-medium">{{ number_format($item->price * $item->quantity, 2) }} {{ $item->currency }}</span>
                            </td>
                            <td class="px-4 md:px-6 py-4 text-right">
                                 <!-- Simple status badge logic (can be refined based on Proposal model) -->
                                <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-600 text-[10px] font-bold uppercase">{{ $item->proposal->status ?? 'Taslak' }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-400 text-sm font-medium">
                                Bu ürünle ilgili henüz bir teklif yok.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
             </div>
        </div>

        <!-- Stock Content (Placeholder) -->
        <div x-show="tab === 'stock'" style="display: none;">
            <div class="bg-white rounded-md border border-slate-100 shadow-sm p-12 flex flex-col items-center justify-center text-center">
                 <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-4">
                    <i class='bx bx-history text-3xl text-slate-300'></i>
                 </div>
                 <h3 class="text-slate-900 font-bold mb-1">Stok Geçmişi Henüz Yok</h3>
                 <p class="text-slate-500 text-sm">Ürün stok hareketleri burada listelenecek.</p>
            </div>
        </div>
    </div>
</div>
@endsection
