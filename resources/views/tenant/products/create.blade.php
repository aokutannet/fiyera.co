@extends('tenant.layouts.app')

@section('content')
<div class="max-w-7xl mx-auto space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-500">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('products.index') }}" class="w-10 h-10 flex items-center justify-center rounded-xl bg-white border border-slate-100 text-slate-400 hover:text-slate-900 transition-all shadow-sm">
                <i class='bx bx-chevron-left text-2xl'></i>
            </a>
            <div>
                <h1 class="text-xl font-extrabold text-slate-950 tracking-tight">Yeni Ürün/Hizmet Ekle</h1>
                <p class="text-slate-500 text-sm mt-1">Sisteme yeni bir ürün veya hizmet kaydedin.</p>
            </div>
        </div>
    </div>

    @if($errors->any())
    <div class="bg-rose-50 border border-rose-100 text-rose-700 px-6 py-4 rounded-2xl text-sm font-medium">
        <div class="flex items-center gap-3 mb-2">
            <i class='bx bx-error-circle text-lg'></i>
            <span>Lütfen aşağıdaki hataları düzeltin:</span>
        </div>
        <ul class="list-disc list-inside opacity-80 space-y-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6"
        x-data="{
            vatRate: 20,
            buyingPriceExcl: 0,
            buyingPriceIncl: 0,
            sellingPriceExcl: 0,
            sellingPriceIncl: 0,
            stockTracking: false,
            criticalStockAlert: false,
            
            calculatePrices(type, source) {
                // source: 'excl' or 'incl'
                // type: 'buying' or 'selling'
                
                let rate = 1 + (this.vatRate / 100);
                
                if (type === 'buying') {
                    if (source === 'excl') {
                        this.buyingPriceIncl = (this.buyingPriceExcl * rate).toFixed(2);
                    } else {
                        this.buyingPriceExcl = (this.buyingPriceIncl / rate).toFixed(2);
                    }
                } else if (type === 'selling') {
                    if (source === 'excl') {
                        this.sellingPriceIncl = (this.sellingPriceExcl * rate).toFixed(2);
                    } else {
                        this.sellingPriceExcl = (this.sellingPriceIncl / rate).toFixed(2);
                    }
                }
            },
            
            updateVat() {
                // Recalculate incl prices based on current excl prices
                this.calculatePrices('buying', 'excl');
                this.calculatePrices('selling', 'excl');
            }
        }">
        @csrf
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column: Main Info -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Temel Bilgiler Section -->
                <div class="bg-white rounded-md p-4 md:p-8 border border-slate-100 shadow-sm">
                    <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-8 border-b border-slate-50 pb-4">Temel Bilgiler</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="col-span-2">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 px-1">Ürün Adı <span class="text-rose-500">*</span></label>
                            <input type="text" name="name" required value="{{ old('name') }}"
                                class="w-full h-12 px-5 rounded-2xl bg-slate-50 border border-slate-100 text-sm font-bold focus:outline-none focus:ring-4 focus:ring-indigo-600/5 focus:border-indigo-600 transition-all font-bold placeholder:font-medium"
                                placeholder="Örn: Profesyonel Web Tasarım Hizmeti">
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 px-1">Ürün Kodu</label>
                            <input type="text" name="code" value="{{ old('code') }}"
                                class="w-full h-12 px-5 rounded-2xl bg-slate-50 border border-slate-100 text-sm font-bold focus:outline-none focus:ring-4 focus:ring-indigo-600/5 focus:border-indigo-600 transition-all font-bold placeholder:font-medium"
                                placeholder="Örn: WEB-001">
                        </div>

                        <div x-data="{
                            open: false,
                            search: '',
                            selectedId: '{{ old('category_id') }}',
                            options: {{ $categories->map(fn($c) => ['id' => $c->id, 'name' => $c->name])->values()->toJson() }},
                            get selectedName() {
                                return this.options.find(o => o.id == this.selectedId)?.name || '';
                            },
                            get filteredOptions() {
                                if (this.search === '') return this.options;
                                return this.options.filter(option => option.name.toLowerCase().includes(this.search.toLowerCase()));
                            }
                        }" class="relative">
                            <input type="hidden" name="category_id" x-model="selectedId">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 px-1">Kategori</label>
                            
                            <!-- Custom Select Trigger -->
                            <div @click="open = !open; if(open) $nextTick(() => $refs.searchInput.focus())" 
                                class="w-full h-12 px-5 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-between cursor-pointer hover:border-indigo-600/30 transition-all select-none">
                                <span x-text="selectedName || 'Kategori Seçin'" :class="selectedName ? 'text-slate-900 font-bold text-sm' : 'text-slate-400 font-medium text-sm'"></span>
                                <i class='bx bx-chevron-down text-xl text-slate-400 transition-transform duration-300' :class="open ? 'rotate-180' : ''"></i>
                            </div>

                            <!-- Dropdown -->
                            <div x-show="open" 
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 translate-y-1"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100 translate-y-0"
                                 x-transition:leave-end="opacity-0 translate-y-1"
                                 @click.outside="open = false" 
                                 class="absolute z-20 w-full mt-2 bg-white rounded-xl shadow-xl border border-slate-100 overflow-hidden">
                                
                                <div class="p-2 border-b border-slate-50">
                                    <div class="relative">
                                        <i class='bx bx-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400'></i>
                                        <input x-ref="searchInput" x-model="search" type="text" 
                                            class="w-full h-9 pl-9 pr-3 rounded-lg bg-slate-50 border-none text-xs font-bold focus:ring-2 focus:ring-indigo-600/10 text-slate-900 placeholder:font-medium placeholder:text-slate-400" 
                                            placeholder="Kategori ara...">
                                    </div>
                                </div>

                                <ul class="max-h-60 overflow-y-auto py-1">
                                    <template x-for="option in filteredOptions" :key="option.id">
                                        <li @click="selectedId = option.id; open = false; search = ''" 
                                            class="px-4 py-2.5 hover:bg-indigo-50 cursor-pointer text-sm font-bold text-slate-700 hover:text-indigo-700 flex items-center justify-between group transition-colors">
                                            <span x-text="option.name"></span>
                                            <i x-show="selectedId == option.id" class='bx bx-check text-indigo-600 text-lg'></i>
                                        </li>
                                    </template>
                                    <li x-show="filteredOptions.length === 0" class="px-4 py-3 text-xs font-medium text-slate-400 text-center">
                                        Sonuç bulunamadı
                                    </li>
                                </ul>
                            </div>
                        </div>
                        
                        <div class="col-span-2">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 px-1">Açıklama</label>
                            <textarea name="description" rows="3" 
                                class="w-full p-5 rounded-2xl bg-slate-50 border border-slate-100 text-sm font-bold focus:outline-none focus:ring-4 focus:ring-indigo-600/5 focus:border-indigo-600 transition-all font-bold placeholder:font-medium"
                                placeholder="Ürün hakkında detaylı açıklama...">{{ old('description') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Fiyatlandırma Section -->
                <div class="bg-white rounded-md p-4 md:p-8 border border-slate-100 shadow-sm">
                    <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-8 border-b border-slate-50 pb-4">Fiyatlandırma</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-8">
                        <!-- Common Settings -->
                        <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6">
                             <div>
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 px-1">KDV Oranı (%) <span class="text-rose-500">*</span></label>
                                <input type="number" name="vat_rate" x-model="vatRate" @input="updateVat" required
                                    class="w-full h-12 px-5 rounded-2xl bg-slate-50 border border-slate-100 text-sm font-bold focus:outline-none focus:ring-4 focus:ring-indigo-600/5 focus:border-indigo-600 transition-all font-bold"
                                    placeholder="20">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 px-1">Birim</label>
                                <input type="text" name="unit" required value="{{ old('unit', 'Adet') }}"
                                    class="w-full h-12 px-5 rounded-2xl bg-slate-50 border border-slate-100 text-sm font-bold focus:outline-none focus:ring-4 focus:ring-indigo-600/5 focus:border-indigo-600 transition-all font-bold"
                                    placeholder="Örn: Adet">
                            </div>
                        </div>

                        <!-- Buying Price -->
                        <div class="col-span-2 border-t border-slate-50 pt-6 mt-2">
                             <h4 class="text-xs font-bold text-slate-500 mb-4">Alış Fiyatı (Maliyet)</h4>
                             <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 px-1">Vergiler Hariç</label>
                                    <div class="relative">
                                        <input type="number" step="0.01" name="buying_price" x-model="buyingPriceExcl" @input="calculatePrices('buying', 'excl')"
                                            class="w-full h-12 pl-4 pr-16 rounded-2xl bg-slate-50 border border-slate-100 text-sm font-bold focus:outline-none focus:ring-4 focus:ring-indigo-600/5 focus:border-indigo-600 transition-all font-bold">
                                         <select name="buying_currency" class="absolute right-0 top-0 h-full w-14 bg-transparent border-none text-xs font-bold text-slate-500 focus:ring-0 text-center">
                                            <option value="TRY">TRY</option>
                                            <option value="USD">USD</option>
                                            <option value="EUR">EUR</option>
                                        </select>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 px-1">Vergiler Dahil</label>
                                    <div class="relative">
                                        <input type="number" step="0.01" x-model="buyingPriceIncl" @input="calculatePrices('buying', 'incl')"
                                            class="w-full h-12 pl-4 pr-12 rounded-2xl bg-slate-50 border border-slate-100 text-sm font-bold focus:outline-none focus:ring-4 focus:ring-indigo-600/5 focus:border-indigo-600 transition-all font-bold">
                                        <div class="absolute right-0 top-0 h-full px-4 flex items-center pointer-events-none text-slate-400 font-bold text-xs">TRY</div>
                                    </div>
                                </div>
                             </div>
                        </div>

                         <!-- Selling Price -->
                        <div class="col-span-2 border-t border-slate-50 pt-6 mt-2">
                             <h4 class="text-xs font-bold text-slate-500 mb-4">Satış Fiyatı</h4>
                             <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 px-1">Vergiler Hariç <span class="text-rose-500">*</span></label>
                                    <div class="relative">
                                        <input type="number" step="0.01" name="price" required x-model="sellingPriceExcl" @input="calculatePrices('selling', 'excl')"
                                            class="w-full h-12 pl-4 pr-16 rounded-2xl bg-slate-50 border border-slate-100 text-sm font-bold focus:outline-none focus:ring-4 focus:ring-indigo-600/5 focus:border-indigo-600 transition-all font-bold">
                                        <select name="selling_currency" class="absolute right-0 top-0 h-full w-14 bg-transparent border-none text-xs font-bold text-slate-500 focus:ring-0 text-center">
                                            <option value="TRY">TRY</option>
                                            <option value="USD">USD</option>
                                            <option value="EUR">EUR</option>
                                        </select>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 px-1">Vergiler Dahil</label>
                                    <div class="relative">
                                        <input type="number" step="0.01" x-model="sellingPriceIncl" @input="calculatePrices('selling', 'incl')"
                                            class="w-full h-12 pl-4 pr-12 rounded-2xl bg-slate-50 border border-slate-100 text-sm font-bold focus:outline-none focus:ring-4 focus:ring-indigo-600/5 focus:border-indigo-600 transition-all font-bold">
                                        <div class="absolute right-0 top-0 h-full px-4 flex items-center pointer-events-none text-slate-400 font-bold text-xs">TRY</div>
                                    </div>
                                </div>
                             </div>
                        </div>

                    </div>
                </div>

                <!-- Stok Yönetimi Section -->
                <div class="bg-white rounded-md p-4 md:p-8 border border-slate-100 shadow-sm">
                    <div class="flex items-center justify-between mb-8 border-b border-slate-50 pb-4">
                        <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest">Stok Yönetimi</h3>
                        <div class="flex items-center gap-3">
                            <span class="text-xs font-bold text-slate-500">Stok Takibi Yapılsın</span>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="stock_tracking" value="1" class="sr-only peer" x-model="stockTracking">
                                <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                            </label>
                        </div>
                    </div>
                    
                    <div x-show="stockTracking" x-collapse>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 px-1">Mevcut Stok</label>
                                <input type="number" name="stock" value="{{ old('stock', 0) }}"
                                    class="w-full h-12 px-5 rounded-2xl bg-slate-50 border border-slate-100 text-sm font-bold focus:outline-none focus:ring-4 focus:ring-indigo-600/5 focus:border-indigo-600 transition-all font-bold"
                                    placeholder="0">
                            </div>
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                     <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest px-1">Kritik Stok Uyarısı</label>
                                     <input type="checkbox" name="critical_stock_alert" value="1" class="accent-indigo-600 w-4 h-4 rounded border-gray-300" x-model="criticalStockAlert">
                                </div>
                                <input type="number" name="critical_stock_quantity" x-bind:disabled="!criticalStockAlert" 
                                    class="w-full h-12 px-5 rounded-2xl bg-slate-50 border border-slate-100 text-sm font-bold focus:outline-none focus:ring-4 focus:ring-indigo-600/5 focus:border-indigo-600 transition-all font-bold disabled:opacity-50"
                                    placeholder="Örn: 10">
                            </div>
                        </div>
                    </div>
                    <div x-show="!stockTracking" class="text-center py-6">
                        <p class="text-sm text-slate-400 font-medium">Stok takibi kapalı. Ürün stoğu sınırsız olarak kabul edilecektir.</p>
                    </div>
                </div>
            </div>

            <!-- Right Column: Image & Status -->
            <div class="space-y-6">
                 <!-- Image Upload -->
                <div class="bg-white rounded-md p-4 md:p-8 border border-slate-100 shadow-sm">
                    <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-6">Ürün Görseli</h3>
                    <div class="flex flex-col items-center justify-center w-full" x-data="{ imagePreview: null }">
                         <label for="dropzone-file" class="flex flex-col items-center justify-center w-full h-64 border-2 border-slate-200 border-dashed rounded-2xl cursor-pointer bg-slate-50 hover:bg-slate-100 transition-colors relative overflow-hidden group">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6" x-show="!imagePreview">
                                <div class="w-16 h-16 bg-white rounded-full shadow-sm flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300">
                                    <i class='bx bx-cloud-upload text-3xl text-indigo-500'></i>
                                </div>
                                <p class="mb-2 text-sm text-slate-500 font-bold"><span class="font-black text-slate-700">Yüklemek için tıklayın</span></p>
                                <p class="text-xs text-slate-400 font-bold">PNG, JPG veya GIF (Max. 2MB)</p>
                            </div>
                            <img x-show="imagePreview" :src="imagePreview" class="w-full h-full object-cover">
                            <input id="dropzone-file" type="file" name="image" class="hidden" accept="image/*" @change="const file = $event.target.files[0]; if(file){ const reader = new FileReader(); reader.onload = (e) => imagePreview = e.target.result; reader.readAsDataURL(file); }" />
                        </label>
                        <button type="button" x-show="imagePreview" @click="imagePreview = null; document.getElementById('dropzone-file').value = ''" class="mt-3 text-xs font-bold text-rose-500 hover:text-rose-700 flex items-center gap-1 transition-colors">
                            <i class='bx bx-trash'></i> Görseli Kaldır
                        </button>
                    </div>
                </div>

                <!-- Status -->
                <div class="bg-white rounded-md p-4 md:p-8 border border-slate-100 shadow-sm">
                    <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-6">Yayın Durumu</h3>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 px-1">Durum</label>
                    <select name="status" class="w-full h-12 px-5 rounded-2xl bg-slate-50 border border-slate-100 text-sm font-bold focus:outline-none focus:ring-4 focus:ring-indigo-600/5 focus:border-indigo-600 transition-all appearance-none cursor-pointer">
                        <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Aktif (Yayında)</option>
                        <option value="passive" {{ old('status') === 'passive' ? 'selected' : '' }}>Pasif (Gizli)</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Submit -->
        <div class="flex items-center justify-end gap-4 pt-4 border-t border-slate-100 mt-8">
            <a href="{{ route('products.index') }}" class="px-8 py-4 rounded-2xl text-sm font-bold text-slate-500 hover:text-slate-900 hover:bg-slate-100 transition-all">Vazgeç</a>
            <button type="submit" class="px-10 py-4 rounded-2xl bg-indigo-600 text-white text-sm font-black hover:bg-indigo-700 transition-all shadow-xl shadow-indigo-100 active:scale-[0.98]">
                ÜRÜNÜ KAYDET
            </button>
        </div>
    </form>
</div>
@endsection
