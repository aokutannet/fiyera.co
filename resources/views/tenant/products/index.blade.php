@extends('tenant.layouts.app')

@section('content')
<div class="space-y-8" x-data="{ 
    deleteProduct: null,
    confirmDelete(product) {
        this.deleteProduct = product;
        $dispatch('open-modal', 'delete-product-confirm');
    }
}">
    <!-- Header & Filters -->
    <div class="flex flex-col gap-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-extrabold text-slate-950 tracking-tight">Ürün / Hizmet Yönetimi</h1>
                <p class="text-slate-500 text-sm mt-1">Ürünlerinizi veya hizmetlerinizi listeleyin ve yeni ürün / hizmet ekleyin.</p>
            </div>
            
            <div class="flex items-center gap-2">
                <a href="{{ route('categories.index') }}" class="h-11 px-6 flex items-center justify-center gap-2 rounded-xl bg-white border border-slate-200 text-slate-600 text-sm font-bold hover:bg-slate-50 hover:text-indigo-600 transition-all shadow-sm whitespace-nowrap">
                    <i class='bx bx-category text-xl'></i> Kategoriler
                </a>
                @if(auth()->user()->hasPermission('products.create'))
                <a href="{{ route('products.create') }}" class="h-11 px-6 flex items-center justify-center gap-2 rounded-xl bg-indigo-600 text-white text-sm font-bold hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-100 whitespace-nowrap">
                    <i class='bx bx-plus text-xl'></i> Yeni Ürün / Hizmet Ekle
                </a>
                @endif
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm">
            <form action="{{ route('products.index') }}" method="GET" class="flex flex-col md:flex-row items-end md:items-center gap-4">
                <!-- Search -->
                <div class="relative w-full md:w-64">
                    <label class="text-xs font-bold text-slate-500 uppercase mb-1 block">Arama</label>
                    <div class="relative">
                        <i class='bx bx-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-lg'></i>
                        <input type="text" name="search" value="{{ request('search') }}" 
                            placeholder="Ürün adı, kodu..." 
                            class="h-10 w-full pl-10 pr-4 rounded-xl bg-slate-50 border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-600/10 focus:border-indigo-600 transition-all">
                    </div>
                </div>

                <!-- Category Filter -->
                <div class="w-full md:w-48">
                    <label class="text-xs font-bold text-slate-500 uppercase mb-1 block">Kategori</label>
                    <select name="category" class="h-10 w-full px-3 rounded-xl bg-slate-50 border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-600/10 focus:border-indigo-600 transition-all">
                        <option value="">Tümü</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Status Filter -->
                <div class="w-full md:w-40">
                    <label class="text-xs font-bold text-slate-500 uppercase mb-1 block">Durum</label>
                    <select name="status" class="h-10 w-full px-3 rounded-xl bg-slate-50 border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-600/10 focus:border-indigo-600 transition-all">
                        <option value="">Tümü</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="passive" {{ request('status') == 'passive' ? 'selected' : '' }}>Pasif</option>
                    </select>
                </div>

                <!-- Filter Actions -->
                <div class="flex items-center gap-2">
                    <button type="submit" class="h-10 px-4 rounded-xl bg-indigo-600 text-white text-sm font-bold hover:bg-indigo-700 transition-all">
                        Filtrele
                    </button>
                    @if(request()->anyFilled(['search', 'category', 'status']))
                        <a href="{{ route('products.index') }}" class="h-10 px-4 rounded-xl bg-slate-100 text-slate-600 text-sm font-bold hover:bg-slate-200 transition-all flex items-center">
                            Temizle
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Mobile Product Cards -->
    <div class="grid grid-cols-1 gap-4 md:hidden mt-6">
        @foreach($products as $product)
        <div class="bg-white rounded-2xl p-4 border border-slate-100 shadow-sm relative overflow-hidden group">
            <div class="flex items-start gap-4">
                <!-- Image -->
                <a href="{{ route('products.show', $product) }}" class="w-16 h-16 rounded-xl bg-indigo-50 border border-slate-100 flex items-center justify-center text-indigo-600 overflow-hidden flex-shrink-0">
                    @if($product->image_path)
                        <img src="{{ Storage::disk('uploads')->url($product->image_path) }}" class="w-full h-full object-cover">
                    @else
                        <i class='bx bx-package text-2xl'></i>
                    @endif
                </a>
                
                <!-- Details -->
                <div class="flex-1 min-w-0">
                    <div class="flex justify-between items-start">
                        <div>
                             <a href="{{ route('products.show', $product) }}" class="text-sm font-bold text-slate-900 block truncate pr-6">{{ $product->name }}</a>
                             <p class="text-xs text-slate-400 font-bold">{{ $product->code ?? 'Kod Yok' }}</p>
                        </div>
                    </div>

                    {{-- Category --}}
                    <div class="mt-2">
                         <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 text-slate-600">
                            {{ $product->productCategory->name ?? 'Kategori Yok' }}
                        </span>
                    </div>
                </div>
                
                 <!-- Status Badge (Absolute Top Right) -->
                <div class="absolute top-4 right-4">
                     @if($product->status === 'active')
                        <span class="w-2 h-2 rounded-full bg-emerald-500 block shadow-sm shadow-emerald-200"></span>
                    @else
                        <span class="w-2 h-2 rounded-full bg-slate-300 block"></span>
                    @endif
                </div>
            </div>

            <div class="mt-4 pt-4 border-t border-slate-50 flex items-center justify-between">
                <div class="flex flex-col">
                     <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Fiyat</span>
                     <span class="text-sm font-black text-slate-900">{{ number_format($product->price, 2) }} {{ $product->selling_currency }}</span>
                </div>
                <div class="flex flex-col text-right">
                     <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Stok</span>
                     @if($product->stock_tracking)
                        <span class="text-sm font-bold {{ $product->stock <= ($product->critical_stock_quantity ?? 0) ? 'text-rose-600' : 'text-slate-700' }}">
                            {{ $product->stock }} {{ $product->unit }}
                        </span>
                    @else
                        <span class="text-xs font-bold text-slate-400">Limitsiz</span>
                    @endif
                </div>
            </div>

            <!-- Actions -->
            <div class="mt-4 flex gap-2">
                 <a href="{{ route('products.show', $product) }}" class="flex-1 h-10 flex items-center justify-center rounded-xl bg-slate-50 text-slate-600 text-xs font-bold hover:bg-slate-100 transition-all">
                    Detay
                </a>
                @if(auth()->user()->hasPermission('products.edit'))
                <a href="{{ route('products.edit', $product) }}" class="h-10 w-10 flex items-center justify-center rounded-xl bg-amber-50 text-amber-600 hover:bg-amber-100 transition-all">
                    <i class='bx bx-edit-alt text-lg'></i>
                </a>
                @endif
                 @if(auth()->user()->hasPermission('products.delete'))
                <button @click="confirmDelete({{ json_encode($product) }})" class="h-10 w-10 flex items-center justify-center rounded-xl bg-rose-50 text-rose-600 hover:bg-rose-100 transition-all">
                    <i class='bx bx-trash text-lg'></i>
                </button>
                @endif
            </div>
        </div>
        @endforeach
        
         @if($products->isEmpty())
            <div class="flex flex-col items-center justify-center p-8 bg-white rounded-2xl border border-slate-100 text-center">
                <i class='bx bx-package text-4xl text-slate-200 mb-2'></i>
                <p class="text-slate-400 text-sm font-bold">Ürün bulunamadı.</p>
            </div>
        @endif
    </div>

    <!-- Desktop Table -->
    <div class="hidden md:block bg-white rounded-md border border-slate-100 shadow-sm overflow-x-auto mt-6">
        <table class="w-full text-left whitespace-nowrap">
            <thead>
                <tr class="bg-slate-50/50 border-b border-slate-100">
                    <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Ürün / Hizmet Kodu</th>
                    <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Kategori</th>
                    <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Stok</th>
                    <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Birim Fiyat</th>
                    <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Durum</th>
                    <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider text-right">İşlemler</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @foreach($products as $product)
                <tr class="hover:bg-slate-50/50 transition-colors {{ $product->status === 'passive' ? 'opacity-60' : '' }}">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <a href="{{ route('products.show', $product) }}" class="w-10 h-10 rounded-xl bg-indigo-50 border border-slate-100 flex items-center justify-center text-indigo-600 overflow-hidden flex-shrink-0">
                                @if($product->image_path)
                                    <img src="{{ Storage::disk('uploads')->url($product->image_path) }}" class="w-full h-full object-cover">
                                @else
                                    <i class='bx bx-package text-xl'></i>
                                @endif
                            </a>
                            <div>
                                <a href="{{ route('products.show', $product) }}" class="text-sm font-bold text-slate-900 hover:text-indigo-600 hover:underline transition-colors">{{ $product->name }}</a>
                                <p class="text-xs text-slate-500 font-medium">{{ $product->code ?? 'Kod Yok' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-bold bg-slate-100 text-slate-600">
                            {{ $product->productCategory->name ?? 'Kategori Yok' }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        @if($product->stock_tracking)
                            <div class="flex flex-col">
                                <span class="text-sm font-bold {{ $product->stock <= ($product->critical_stock_quantity ?? 0) ? 'text-rose-600' : 'text-slate-700' }}">
                                    {{ $product->stock }} {{ $product->unit }}
                                </span>
                                @if($product->stock <= ($product->critical_stock_quantity ?? 0))
                                    <span class="text-[10px] font-bold text-rose-600 uppercase">Kritik Stok</span>
                                @endif
                            </div>
                        @else
                            <span class="text-xs font-medium text-slate-400">Takip Yok</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex flex-col">
                            <span class="text-sm font-bold text-slate-700">{{ number_format($product->price, 2) }} {{ $product->selling_currency }}</span>
                            <span class="text-xs text-slate-400">+%{{ $product->vat_rate }} KDV</span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        @if($product->status === 'active')
                        <span class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-600">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            Aktif
                        </span>
                        @else
                        <span class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-400">
                            <span class="w-1.5 h-1.5 rounded-full bg-slate-300"></span>
                            Pasif
                        </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('products.show', $product) }}" class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 transition-all border border-transparent hover:border-indigo-100" data-tooltip="Görüntüle">
                                <i class='bx bx-show text-lg'></i>
                            </a>
                            @if(auth()->user()->hasPermission('products.edit'))
                            <a href="{{ route('products.edit', $product) }}" class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:text-amber-600 hover:bg-amber-50 transition-all border border-transparent hover:border-amber-100" data-tooltip="Düzenle">
                                <i class='bx bx-edit-alt text-lg'></i>
                            </a>
                            <form action="{{ route('products.toggle-status', $product) }}" method="POST" class="inline-block">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 {{ $product->status === 'active' ? 'hover:text-amber-600 hover:bg-amber-50 border-transparent hover:border-amber-100' : 'hover:text-emerald-600 hover:bg-emerald-50 border-transparent hover:border-emerald-100' }} transition-all border" data-tooltip="{{ $product->status === 'active' ? 'Pasife Al' : 'Aktif Et' }}">
                                    <i class='bx {{ $product->status === 'active' ? 'bx-pause-circle' : 'bx-play-circle' }} text-lg'></i>
                                </button>
                            </form>
                            @endif
                            @if(auth()->user()->hasPermission('products.delete'))
                            <button type="button" @click="confirmDelete({{ json_encode($product) }})" class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-all border border-transparent hover:border-rose-100" data-tooltip="Sil">
                                <i class='bx bx-trash text-lg'></i>
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
                @if($products->isEmpty())
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center gap-3">
                            <i class='bx bx-package text-4xl text-slate-200'></i>
                            <p class="text-slate-400 text-sm font-medium">Kriterlere uygun ürün bulunamadı.</p>
                        </div>
                    </td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $products->links() }}
    </div>

<!-- Delete Confirmation Modal -->
<template x-teleport="body">
    <div x-data="{ open: false }" 
         x-show="open" 
         @open-modal.window="if($event.detail === 'delete-product-confirm') open = true"
         @close-modal.window="open = false"
         class="fixed inset-0 z-[100] overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="open = false"></div>
            <div class="bg-white rounded-3xl shadow-2xl w-full max-w-sm relative z-10 overflow-hidden border border-slate-100 p-8 flex flex-col items-center">
                <div class="w-20 h-20 bg-rose-50 rounded-full flex items-center justify-center mb-6">
                    <div class="w-16 h-16 bg-rose-100 rounded-full flex items-center justify-center animate-pulse">
                        <i class='bx bx-trash text-4xl text-rose-600'></i>
                    </div>
                </div>
                <h3 class="text-xl font-black text-slate-950 mb-2">Emin misiniz?</h3>
                <p class="text-slate-500 font-bold text-center leading-relaxed mb-8">
                    <span class="text-slate-900" x-text="deleteProduct ? deleteProduct.name : ''"></span> isimli ürünü silmek istediğinize emin misiniz? Bu işlem geri alınamaz.
                </p>
                <div class="flex flex-col w-full gap-3">
                    <form :action="'{{ url('products') }}/' + (deleteProduct ? deleteProduct.id : '')" method="POST" class="w-full">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full py-4 rounded-2xl bg-rose-600 text-white text-sm font-black hover:bg-rose-700 transition-all shadow-xl shadow-rose-100 active:scale-[0.98]">
                            EVET, SİL
                        </button>
                    </form>
                    <button @click="open = false" class="w-full py-4 rounded-2xl bg-slate-50 text-slate-500 text-sm font-bold hover:bg-slate-100 transition-all">
                        VAZGEÇ
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
</div>
@endsection

<style>
    [data-tooltip] {
        position: relative;
    }
    [data-tooltip]::before {
        content: attr(data-tooltip);
        position: absolute;
        bottom: 100%;
        left: 50%;
        transform: translateX(-50%) translateY(-8px);
        background: #0f172a;
        color: white;
        padding: 5px 10px;
        border-radius: 8px;
        font-size: 10px;
        font-weight: 800;
        white-space: nowrap;
        opacity: 0;
        visibility: hidden;
        transition: all 0.2s ease;
        z-index: 50;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }
    [data-tooltip]:hover::before {
        opacity: 1;
        visibility: visible;
        transform: translateX(-50%) translateY(-12px);
    }
    /* Arrow */
    [data-tooltip]::after {
        content: '';
        position: absolute;
        bottom: 100%;
        left: 50%;
        transform: translateX(-50%) translateY(0);
        border: 5px solid transparent;
        border-top-color: #0f172a;
        opacity: 0;
        visibility: hidden;
        transition: all 0.2s ease;
        z-index: 50;
    }
    [data-tooltip]:hover::after {
        opacity: 1;
        visibility: visible;
        transform: translateX(-50%) translateY(-2px);
    }
</style>

