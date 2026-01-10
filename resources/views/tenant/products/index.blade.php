@extends('tenant.layouts.app')

@section('content')
<div class="space-y-8" x-data="{ 
    showFilters: {{ request()->anyFilled(['search', 'category', 'status']) ? 'true' : 'false' }},
    deleteProduct: null,
    selected: [],
    get allSelected() {
        return this.selected.length === {{ $products->count() }} && this.selected.length > 0;
    },
    confirmDelete(product) {
        this.deleteProduct = product;
        $dispatch('open-modal', 'delete-product-confirm');
    },
    toggleAll() {
        if (this.allSelected) {
            this.selected = [];
        } else {
            this.selected = {{ $products->pluck('id') }};
        }
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

                <button @click="showFilters = !showFilters" type="button" :class="{'bg-slate-50 text-indigo-600 ring-2 ring-indigo-600/10': showFilters, 'bg-white text-slate-600': !showFilters}" class="h-11 px-6 flex items-center justify-center gap-2 rounded-xl border border-slate-200 text-sm font-bold hover:bg-slate-50 hover:text-indigo-600 transition-all shadow-sm whitespace-nowrap">
                    <i class='bx bx-filter-alt text-xl'></i> Filtre
                </button>

                <!-- Export Dropdown -->
                <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                    <button @click="open = !open" type="button" class="h-11 px-6 flex items-center justify-center gap-2 rounded-xl bg-white border border-slate-200 text-slate-600 text-sm font-bold hover:bg-slate-50 hover:text-indigo-600 transition-all shadow-sm whitespace-nowrap">
                        <i class='bx bx-export text-xl'></i> Dışa Aktar
                    </button>
                    <div x-show="open" 
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 translate-y-1"
                        class="absolute right-0 top-full mt-2 w-48 bg-white rounded-xl shadow-xl border border-slate-100 z-50 overflow-hidden"
                        style="display: none;">
                        <div class="p-1">
                            <a href="{{ route('products.export', array_merge(request()->query(), ['format' => 'excel'])) }}" class="flex items-center gap-3 px-4 py-3 text-sm font-bold text-slate-600 hover:bg-slate-50 hover:text-indigo-600 rounded-lg transition-colors">
                                <i class='bx bx-spreadsheet text-emerald-600 text-lg'></i> Excel
                            </a>
                            <a href="{{ route('products.export', array_merge(request()->query(), ['format' => 'csv'])) }}" class="flex items-center gap-3 px-4 py-3 text-sm font-bold text-slate-600 hover:bg-slate-50 hover:text-indigo-600 rounded-lg transition-colors">
                                <i class='bx bx-file text-slate-500 text-lg'></i> CSV
                            </a>
                            <a href="{{ route('products.export', array_merge(request()->query(), ['format' => 'xml'])) }}" class="flex items-center gap-3 px-4 py-3 text-sm font-bold text-slate-600 hover:bg-slate-50 hover:text-indigo-600 rounded-lg transition-colors">
                                <i class='bx bx-code-alt text-amber-600 text-lg'></i> XML
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Import Button -->
                @if(auth()->user()->hasPermission('products.create'))
                <button @click="$dispatch('open-modal', 'import-products')" class="h-11 px-6 flex items-center justify-center gap-2 rounded-xl bg-white border border-slate-200 text-slate-600 text-sm font-bold hover:bg-slate-50 hover:text-indigo-600 transition-all shadow-sm whitespace-nowrap">
                    <i class='bx bx-import text-xl'></i> İçe Aktar
                </button>
                @endif


                @if(auth()->user()->hasPermission('products.create'))
                <a href="{{ route('products.create') }}" class="h-11 px-6 flex items-center justify-center gap-2 rounded-xl bg-indigo-600 text-white text-sm font-bold hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-100 whitespace-nowrap">
                    <i class='bx bx-plus text-xl'></i> Yeni Ürün / Hizmet Ekle
                </a>
                @endif
            </div>
        </div>

        <!-- Filters -->
        <div x-show="showFilters" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm"
             style="display: none;">
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

    <!-- Bulk Actions (Floating) -->
    <div x-show="selected.length > 0" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-full"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-full"
         class="fixed bottom-8 left-1/2 -translate-x-1/2 z-40 w-full max-w-2xl px-4"
         style="display: none;">
        
        <div class="bg-indigo-50/90 backdrop-blur-xl border border-indigo-100 rounded-full p-2 shadow-2xl shadow-indigo-900/10 flex items-center justify-between gap-4">
            <div class="flex items-center gap-3 pl-4">
                <div class="flex items-center justify-center w-8 h-8 rounded-full bg-indigo-200/50 text-indigo-700 font-black text-sm">
                    <span x-text="selected.length"></span>
                </div>
                <span class="text-sm font-bold text-slate-700">Adet ürün seçildi</span>
            </div>
            
            <div class="flex items-center gap-2 pr-2">
                <button @click="selected = []" class="h-10 px-6 rounded-full text-xs font-bold text-slate-500 hover:text-slate-800 hover:bg-white/50 transition-all flex items-center justify-center leading-none">
                    Vazgeç
                </button>
                
                <form action="{{ route('products.bulk-destroy') }}" method="POST" class="flex items-center m-0" onsubmit="return confirm('Seçili ürünleri silmek istediğinize emin misiniz?');">
                    @csrf
                    @method('DELETE')
                    <template x-for="id in selected" :key="id">
                        <input type="hidden" name="ids[]" :value="id">
                    </template>
                    <button type="submit" class="h-10 px-6 rounded-full bg-rose-600 text-white text-xs font-black hover:bg-rose-700 transition-all shadow-lg shadow-rose-200 hover:shadow-rose-300 transform active:scale-95 flex items-center justify-center gap-2 leading-none">
                        <i class='bx bx-trash text-base'></i>
                        <span>SEÇİLENLERİ SİL</span>
                    </button>
                </form>
            </div>
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
                    <th class="pl-6 py-4 w-4">
                        <input type="checkbox" @click="toggleAll()" :checked="allSelected" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-600 focus:ring-offset-0 w-4 h-4 cursor-pointer">
                    </th>
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
                <tr class="hover:bg-slate-50/50 transition-colors" :class="{'bg-indigo-50/50 hover:bg-indigo-50/80': selected.includes({{ $product->id }})}">
                    <td class="pl-6 py-4">
                        <input type="checkbox" value="{{ $product->id }}" x-model="selected" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-600 focus:ring-offset-0 w-4 h-4 cursor-pointer">
                    </td>
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
                    <td colspan="7" class="px-6 py-12 text-center">
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

<!-- Import Modal -->
<template x-teleport="body">
    <div x-data="{ 
        open: false,
        step: 1, // 1: Upload, 2: Analyzing, 2.5: Mapping 3: Summary, 4: Success
        file: null,
        progress: 0,
        stats: { new_count: 0, update_count: 0 },
        importKey: null,
        error: null,
        headers: [],
        requiredFields: {},
        mapping: {},

        handleFile(e) {
            this.file = e.target.files[0];
            this.step = 2;
            this.progress = 0;
            this.analyze();
        },
        
        analyze() {
            let formData = new FormData();
            formData.append('file', this.file);
            formData.append('_token', '{{ csrf_token() }}');

            let xhr = new XMLHttpRequest();
            
            xhr.upload.addEventListener('progress', (e) => {
                if (e.lengthComputable) {
                    this.progress = Math.round((e.loaded / e.total) * 90);
                }
            });

            xhr.open('POST', '{{ route('products.import.analyze') }}', true);
            
            xhr.onload = () => {
                if (xhr.status === 200) {
                    this.progress = 100;
                    let response = JSON.parse(xhr.responseText);
                    
                    if (response.status === 'mapping_required') {
                        this.importKey = response.key;
                        this.headers = response.headers;
                        this.requiredFields = response.required_fields;
                        // Pre-fill mapping if names match exactly
                        this.mapping = {};
                        Object.keys(this.requiredFields).forEach(field => {
                            // Find closest match or empty
                            let exactMatch = this.headers.find(h => h.toLowerCase() === field.toLowerCase());
                            this.mapping[field] = exactMatch || '';
                        });
                        setTimeout(() => { this.step = 2.5; }, 500);
                    } else {
                        // Standard flow
                        this.stats = response.stats;
                        this.importKey = response.key;
                        setTimeout(() => { this.step = 3; }, 500);
                    }
                } else {
                    this.error = 'Dosya yüklenirken bir hata oluştu.';
                    this.step = 1;
                }
            };

            xhr.onerror = () => {
                this.error = 'Bağlantı hatası oluştu.';
                this.step = 1;
            };

            xhr.send(formData);
        },

        submitMapping() {
            if (!this.mapping['name']) {
                this.error = 'Devam etmek için Ürün Adı (Zorunlu) alanını eşleştirmeniz gerekmektedir.';
                return;
            }
            this.error = null;
            this.step = 2; // Show processing again
            
            fetch('{{ route('products.import.map') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ 
                    key: this.importKey,
                    mapping: this.mapping
                })
            })
            .then(response => response.json())
            .then(data => {
                this.stats = data.stats;
                this.importKey = data.key; // Update key to processed one
                this.step = 3;
            })
            .catch(error => {
                this.error = 'Eşleştirme sırasında hata oluştu.';
                this.step = 1;
            });
        },

        completeImport() {
            this.step = 2;
            this.progress = 100;
            
            fetch('{{ route('products.import.execute') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ key: this.importKey })
            })
            .then(response => response.json())
            .then(data => {
                this.step = 4;
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            })
            .catch(error => {
                this.error = 'İçe aktarma sırasında bir hata oluştu.';
                this.step = 1;
            });
        },

        reset() {
            this.step = 1;
            this.file = null;
            this.progress = 0;
            this.error = null;
            this.mapping = {};
        }
    }" 
         x-show="open" 
         @open-modal.window="if($event.detail === 'import-products') { open = true; reset(); }"
         @close-modal.window="open = false"
         class="fixed inset-0 z-[100] overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="open = false"></div>
            <div class="bg-white rounded-3xl shadow-2xl w-full max-w-lg relative z-10 overflow-hidden border border-slate-100 p-8 flex flex-col max-h-[90vh]">
                
                <!-- Step 1: Upload -->
                <div x-show="step === 1">
                    <div class="flex flex-col items-center text-center">
                        <div class="w-20 h-20 bg-indigo-50 rounded-full flex items-center justify-center mb-6">
                            <i class='bx bx-cloud-upload text-4xl text-indigo-600'></i>
                        </div>
                        <h3 class="text-xl font-black text-slate-950 mb-2">Ürünleri İçe Aktar</h3>
                        <p class="text-slate-500 text-sm mb-8">
                            Excel veya CSV dosyalarınızı yükleyerek ürünlerinizi toplu olarak ekleyebilir veya güncelleyebilirsiniz.
                        </p>

                        <div x-show="error" class="w-full bg-rose-50 text-rose-600 p-4 rounded-xl mb-6 text-sm font-bold flex items-center gap-2">
                            <i class='bx bx-error-circle text-lg'></i>
                            <span x-text="error"></span>
                        </div>

                        <label class="w-full h-48 border-2 border-dashed border-slate-200 rounded-2xl flex flex-col items-center justify-center cursor-pointer hover:border-indigo-600 hover:bg-indigo-50/10 transition-all group relative overflow-hidden">
                            <input type="file" class="hidden" accept=".csv, .xlsx, .xls, .xml" @change="handleFile">
                            <i class='bx bx-import text-4xl text-slate-300 group-hover:text-indigo-600 transition-colors mb-2'></i>
                            <span class="text-slate-500 font-bold group-hover:text-indigo-600 transition-colors">Dosya Seçin veya Sürükleyin</span>
                            <span class="text-xs text-slate-400 mt-1">.xlsx, .csv, .xml</span>
                        </label>
                    </div>
                </div>

                <!-- Step 2: Processing -->
                <div x-show="step === 2">
                    <div class="flex flex-col items-center text-center py-8">
                        <div class="w-full bg-slate-100 rounded-full h-4 mb-4 overflow-hidden relative">
                            <div class="bg-indigo-600 h-full rounded-full transition-all duration-300 relative overflow-hidden" :style="'width: ' + progress + '%'">
                                <div class="absolute inset-0 bg-white/20 animate-[shimmer_2s_infinite]"></div>
                            </div>
                        </div>
                        <span class="text-indigo-600 font-black text-2xl mb-1" x-text="progress + '%'"></span>
                        <p class="text-slate-500 font-bold animate-pulse">Dosya Analiz Ediliyor...</p>
                    </div>
                </div>

                <!-- Step 2.5: Mapping -->
                <div x-show="step === 2.5" class="flex flex-col h-full overflow-hidden">
                    <div class="text-center mb-4 flex-shrink-0">
                         <div class="w-12 h-12 bg-amber-50 rounded-full flex items-center justify-center mx-auto mb-2">
                            <i class='bx bx-list-check text-2xl text-amber-600'></i>
                        </div>
                         <h3 class="text-lg font-black text-slate-950">Sütun Eşleştirme</h3>
                         <p class="text-slate-500 text-xs mt-1">Dosya sütunlarını sistem alanlarıyla eşleştirin.</p>
                    </div>

                    <div x-show="error" class="w-full bg-rose-50 text-rose-600 p-3 rounded-xl mb-4 text-xs font-bold flex items-center gap-2 flex-shrink-0">
                        <i class='bx bx-error-circle text-lg'></i>
                        <span x-text="error"></span>
                    </div>

                    <div class="flex-1 overflow-y-auto custom-scrollbar px-1 -mx-1 mb-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <template x-for="(label, key) in requiredFields" :key="key">
                                <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-100" :class="{'ring-1 ring-rose-400 bg-rose-50': key === 'name' && error}">
                                    <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1 truncate" x-text="label" :title="label"></label>
                                    <select x-model="mapping[key]" class="w-full h-8 px-2 rounded-lg bg-white border-slate-200 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-600/10 focus:border-indigo-600 transition-all">
                                        <option value="">Seçiniz...</option>
                                        <template x-for="header in headers" :key="header">
                                            <option :value="header" x-text="header"></option>
                                        </template>
                                    </select>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div class="flex-shrink-0 pt-2 border-t border-slate-50">
                        <button @click="submitMapping" class="w-full py-3.5 rounded-xl bg-indigo-600 text-white text-sm font-black hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-100 active:scale-[0.98]">
                            ANALİZİ TAMAMLA
                        </button>
                    </div>
                </div>

                <!-- Step 3: Summary -->
                <div x-show="step === 3">
                    <div class="flex flex-col items-center text-center">
                        <div class="w-20 h-20 bg-emerald-50 rounded-full flex items-center justify-center mb-6">
                            <i class='bx bx-check text-4xl text-emerald-600'></i>
                        </div>
                        <h3 class="text-xl font-black text-slate-950 mb-6">Analiz Tamamlandı</h3>
                        
                        <div class="grid grid-cols-2 gap-4 w-full mb-8">
                            <div class="bg-indigo-50 rounded-2xl p-4 border border-indigo-100">
                                <span class="block text-3xl font-black text-indigo-700 mb-1" x-text="stats.new_count"></span>
                                <span class="text-xs font-bold text-indigo-400 uppercase tracking-wider">Yeni Ürün Eklenecek</span>
                            </div>
                            <div class="bg-amber-50 rounded-2xl p-4 border border-amber-100">
                                <span class="block text-3xl font-black text-amber-700 mb-1" x-text="stats.update_count"></span>
                                <span class="text-xs font-bold text-amber-400 uppercase tracking-wider">Ürün Güncellenecek</span>
                            </div>
                        </div>

                        <div class="flex flex-col w-full gap-3">
                            <button @click="completeImport" class="w-full py-4 rounded-2xl bg-indigo-600 text-white text-sm font-black hover:bg-indigo-700 transition-all shadow-xl shadow-indigo-100 active:scale-[0.98]">
                                İŞLEMİ TAMAMLA
                            </button>
                            <button @click="open = false" class="w-full py-4 rounded-2xl bg-slate-50 text-slate-500 text-sm font-bold hover:bg-slate-100 transition-all">
                                VAZGEÇ
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Step 4: Success -->
                <div x-show="step === 4">
                    <div class="flex flex-col items-center text-center py-8">
                        <div class="w-20 h-20 bg-emerald-50 rounded-full flex items-center justify-center mb-6 animate-[bounce_1s_infinite]">
                            <i class='bx bx-party text-4xl text-emerald-600'></i>
                        </div>
                        <h3 class="text-xl font-black text-slate-950 mb-2">Başarılı!</h3>
                        <p class="text-slate-500 text-sm">Ürünler başarıyla içe aktarıldı. Sayfa yenileniyor...</p>
                    </div>
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

