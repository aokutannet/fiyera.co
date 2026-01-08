@extends('tenant.layouts.app')

@section('content')
<!-- Tom Select CSS & JS -->
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<script src="https://unpkg.com/imask"></script>

<div class="max-w-7xl mx-auto space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-500" 
     x-data="proposalForm()">
    
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('proposals.index') }}" class="w-10 h-10 flex items-center justify-center rounded-xl bg-white border border-slate-100 text-slate-400 hover:text-slate-900 transition-all shadow-sm">
                <i class='bx bx-chevron-left text-2xl'></i>
            </a>
            <div>
                <h1 class="text-xl font-extrabold text-slate-950 tracking-tight">Teklifi Düzenle</h1>
                <p class="text-slate-500 text-sm mt-1">Teklif detaylarını güncelleyin ve kaydedin.</p>
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

    <form action="{{ route('proposals.update', $proposal) }}" method="POST" class="space-y-8" id="proposalForm">
        @csrf
        @method('PUT')
        <input type="hidden" name="status" x-model="status">
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Sol Kolon: Teklif Bilgileri -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Temel Bilgiler -->
                <div class="bg-white rounded-md p-8 border border-slate-100 shadow-sm space-y-6">
                    <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-6 border-b border-slate-50 pb-4">Teklif Başlığı & Müşteri</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="col-span-2">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 px-1">Teklif Başlığı <span class="text-rose-500">*</span></label>
                            <input type="text" name="title" required value="{{ old('title', $proposal->title) }}"
                                class="w-full h-12 px-5 rounded-2xl bg-slate-50 border border-slate-100 text-sm font-bold focus:outline-none focus:ring-4 focus:ring-indigo-600/5 focus:border-indigo-600 transition-all"
                                placeholder="Örn: 2024 Yazılım Geliştirme Hizmeti">
                        </div>

                        <div>
                            <div class="flex items-center justify-between mb-2 px-1">
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">MÜŞTERİ SEÇİMİ <span class="text-rose-500">*</span></label>
                                <button type="button" @click="$dispatch('open-modal', 'new-customer-modal')" 
                                    class="flex items-center gap-1.5 px-3 py-1 rounded-lg bg-indigo-50 text-indigo-600 text-[10px] font-bold hover:bg-indigo-100 transition-all border border-indigo-100">
                                    <i class='bx bx-plus-circle'></i> YENİ MÜŞTERİ EKLE
                                </button>
                            </div>
                            <select name="customer_id" id="customer_select" required class="w-full">
                                <option value="">Müşteri Seçin...</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}" {{ old('customer_id', $proposal->customer_id) == $customer->id ? 'selected' : '' }}>
                                        {{ $customer->company_name }} ({{ $customer->contact_person }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 px-1">Para Birimi <span class="text-rose-500">*</span></label>
                            <select name="currency" required class="w-full h-12 px-5 rounded-2xl bg-slate-50 border border-slate-100 text-sm font-bold focus:outline-none focus:ring-4 focus:ring-indigo-600/5 focus:border-indigo-600 transition-all appearance-none">
                                <option value="TRY" {{ old('currency', $proposal->currency) == 'TRY' ? 'selected' : '' }}>Türk Lirası (₺)</option>
                                <option value="USD" {{ old('currency', $proposal->currency) == 'USD' ? 'selected' : '' }}>Amerikan Doları ($)</option>
                                <option value="EUR" {{ old('currency', $proposal->currency) == 'EUR' ? 'selected' : '' }}>Euro (€)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Teklif Kalemleri -->
                <div class="bg-white rounded-3xl p-8 border border-slate-100 shadow-sm">
                    <div class="flex items-center justify-between mb-8 border-b border-slate-50 pb-4">
                        <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest">Teklif Kalemleri</h3>
                        <button type="button" @click="addItem()" class="h-9 px-4 rounded-xl bg-indigo-50 text-indigo-600 text-xs font-bold hover:bg-indigo-100 transition-all flex items-center gap-2">
                            <i class='bx bx-plus'></i> Kalem Ekle
                        </button>
                    </div>

                    <div class="space-y-4">
                        <template x-for="(item, index) in items" :key="index">
                            <div class="p-6 rounded-3xl bg-slate-50/50 border border-slate-100 relative group animate-in fade-in slide-in-from-left-4 space-y-4">
                                <!-- Ana Satır -->
                                <div class="grid grid-cols-12 gap-5">
                                    <div class="col-span-12 lg:col-span-5">
                                        <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1.5 px-1">Ürün / Açıklama</label>
                                        <div class="relative">
                                            <input type="text" x-init="initProductSelect($el, index)" 
                                                class="w-full" placeholder="Ürün ara veya yeni ekle..."
                                                :value="item.product_id ? item.product_id : item.description">
                                            
                                            <!-- Hidden inputs to carry values -->
                                            <input type="hidden" :name="`items[${index}][product_id]`" x-model="item.product_id">
                                            <input type="hidden" :name="`items[${index}][description]`" x-model="item.description">
                                            
                                            <div x-show="item.product_id === null && item.description !== ''" 
                                                 class="absolute top-1 right-8 -translate-y-1/2 mt-3 pointer-events-none">
                                                <span class="bg-indigo-100 text-indigo-700 text-[10px] font-bold px-2 py-0.5 rounded-full border border-indigo-200 shadow-sm animate-in fade-in zoom-in">
                                                    YENİ EKLENDİ
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-span-6 md:col-span-3 lg:col-span-2">
                                        <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1.5 px-1">Miktar</label>
                                        <div class="flex items-center gap-1.5">
                                            <input type="number" step="0.01" :name="`items[${index}][quantity]`" x-model="item.quantity" required
                                                class="w-16 h-10 px-3 rounded-xl bg-white border border-slate-200 text-xs font-bold focus:outline-none focus:ring-2 focus:ring-indigo-600/5 focus:border-indigo-600 transition-all">
                                            <select :name="`items[${index}][unit]`" x-model="item.unit" class="flex-1 h-10 px-2 rounded-xl bg-white border border-slate-200 text-[10px] font-bold focus:outline-none focus:ring-2 focus:ring-indigo-600/5 focus:border-indigo-600 transition-all text-center appearance-none">
                                                <option value="Adet">Adet</option>
                                                <option value="Saat">Saat</option>
                                                <option value="Ay">Ay</option>
                                                <option value="Kg">Kg</option>
                                                <option value="Gram">Gr</option>
                                                <option value="Metre">Mt</option>
                                                <option value="Paket">Pkt</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-span-6 md:col-span-3 lg:col-span-2">
                                        <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1.5 px-1">Birim Fiyat</label>
                                        <div class="relative">
                                            <input type="text" 
                                                x-init="initMask($el, index)"
                                                required
                                                class="w-full h-10 px-4 rounded-xl bg-white border border-slate-200 text-xs font-bold focus:outline-none focus:ring-2 focus:ring-indigo-600/5 focus:border-indigo-600 transition-all unit-price-mask"
                                                placeholder="0,00">
                                            <input type="hidden" :name="`items[${index}][unit_price]`" x-model="item.unit_price">
                                        </div>
                                    </div>
                                    
                                    <div class="col-span-4 md:col-span-2 lg:col-span-2">
                                        <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1.5 px-1">KDV%</label>
                                        <select :name="`items[${index}][tax_rate]`" x-model="item.tax_rate" required
                                            class="w-full h-10 px-2 rounded-xl bg-white border border-slate-200 text-xs font-bold focus:outline-none focus:ring-2 focus:ring-indigo-600/5 focus:border-indigo-600 transition-all appearance-none text-center">
                                            <option value="0">0</option>
                                            <option value="1">1</option>
                                            <option value="10">10</option>
                                            <option value="15">15</option>
                                            <option value="18">18</option>
                                            <option value="20">20</option>
                                        </select>
                                    </div>

                                    <div class="col-span-8 md:col-span-4 lg:col-span-1 flex items-end justify-end gap-2 pb-0.5">
                                        <button type="button" @click="item.showDiscount = !item.showDiscount" 
                                            :class="item.showDiscount ? 'bg-indigo-600 text-white' : 'bg-white text-slate-400 border-slate-200'"
                                            class="w-10 h-10 flex items-center justify-center rounded-xl border transition-all hover:shadow-md">
                                            <i class='bx bx-minus' x-show="item.showDiscount"></i>
                                            <i class='bx bx-plus' x-show="!item.showDiscount"></i>
                                        </button>
                                        <button type="button" @click="removeItem(index)" class="w-10 h-10 flex items-center justify-center text-slate-300 hover:text-rose-500 hover:bg-rose-50 border border-transparent hover:border-rose-100 rounded-xl transition-all">
                                            <i class='bx bx-trash text-lg'></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- İndirim Satırı (Opsiyonel) -->
                                <div x-show="item.showDiscount" 
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0 -translate-y-2"
                                     x-transition:enter-end="opacity-100 translate-y-0"
                                     class="flex items-center gap-6 p-4 rounded-2xl bg-indigo-50/50 border border-indigo-100/50">
                                    <div class="flex items-center gap-3">
                                        <span class="text-[10px] font-black text-indigo-400 uppercase tracking-widest">İndirim Türü:</span>
                                        <div class="flex bg-white rounded-lg p-1 border border-indigo-100">
                                            <button type="button" @click="item.discount_type = 'fixed'" 
                                                :class="item.discount_type === 'fixed' ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-400 hover:bg-slate-50'"
                                                class="px-3 py-1 rounded-md text-[10px] font-black transition-all uppercase">Tutar</button>
                                            <button type="button" @click="item.discount_type = 'percentage'" 
                                                :class="item.discount_type === 'percentage' ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-400 hover:bg-slate-50'"
                                                class="px-3 py-1 rounded-md text-[10px] font-black transition-all uppercase">Yüzde (%)</button>
                                            <input type="hidden" :name="`items[${index}][discount_type]`" x-model="item.discount_type">
                                        </div>
                                    </div>
                                    <div class="flex-1 max-w-[200px]">
                                        <div class="relative">
                                            <input type="number" step="0.01" :name="`items[${index}][discount_value]`" x-model="item.discount_value"
                                                class="w-full h-10 px-4 rounded-xl bg-white border border-indigo-100 text-xs font-bold focus:outline-none focus:ring-4 focus:ring-indigo-600/5 focus:border-indigo-600 transition-all"
                                                placeholder="İndirim miktarı...">
                                            <div class="absolute right-3 top-1/2 -translate-y-1/2 text-[10px] font-black text-indigo-300" x-text="item.discount_type === 'percentage' ? '%' : '₺'"></div>
                                        </div>
                                    </div>
                                    <div class="text-[10px] font-bold text-slate-400">
                                        Satır İndirimi: <span class="text-indigo-600 font-black" x-text="formatCurrency(calculateLineDiscount(item))"></span>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Notlar -->
                <div class="bg-white rounded-md p-8 border border-slate-100 shadow-sm">
                    <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-6 border-b border-slate-50 pb-4">Teklif Notları & Şartlar</h3>
                    <textarea name="notes" rows="4" 
                        class="w-full p-5 rounded-2xl bg-slate-50 border border-slate-100 text-sm font-bold focus:outline-none focus:ring-4 focus:ring-indigo-600/5 focus:border-indigo-600 transition-all"
                        placeholder="Örn: Ödeme teklif onayından sonra 7 iş günü içerisinde peşin olarak yapılacaktır.">{{ old('notes', $proposal->notes) }}</textarea>
                </div>
            </div>

            <!-- Sağ Kolon: Tarihler ve Özet -->
            <div class="space-y-8">
                <!-- Tarih ve Ek Bilgiler -->
                <div class="bg-white rounded-md p-8 border border-slate-100 shadow-sm space-y-6">
                    <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-6 border-b border-slate-50 pb-4">Tarih & Ödeme</h3>
                    
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 px-1">Teklif Tarihi <span class="text-rose-500">*</span></label>
                        <input type="date" name="proposal_date" required value="{{ old('proposal_date', $proposal->proposal_date->format('Y-m-d')) }}"
                            class="w-full h-12 px-5 rounded-2xl bg-slate-50 border border-slate-100 text-sm font-bold focus:outline-none focus:ring-4 focus:ring-indigo-600/5 focus:border-indigo-600 transition-all">
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 px-1">Geçerlilik Tarihi</label>
                        <input type="date" name="valid_until" value="{{ old('valid_until', $proposal->valid_until ? $proposal->valid_until->format('Y-m-d') : '') }}"
                            class="w-full h-12 px-5 rounded-2xl bg-slate-50 border border-slate-100 text-sm font-bold focus:outline-none focus:ring-4 focus:ring-indigo-600/5 focus:border-indigo-600 transition-all">
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 px-1">Teslim Tarihi</label>
                        <input type="date" name="delivery_date" value="{{ old('delivery_date', $proposal->delivery_date ? $proposal->delivery_date->format('Y-m-d') : '') }}"
                            class="w-full h-12 px-5 rounded-2xl bg-slate-50 border border-slate-100 text-sm font-bold focus:outline-none focus:ring-4 focus:ring-indigo-600/5 focus:border-indigo-600 transition-all">
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 px-1">Ödeme Türü</label>
                        <select name="payment_type" class="w-full h-12 px-5 rounded-2xl bg-slate-50 border border-slate-100 text-sm font-bold focus:outline-none focus:ring-4 focus:ring-indigo-600/5 focus:border-indigo-600 transition-all appearance-none">
                            <option value="Peşin" {{ old('payment_type', $proposal->payment_type) == 'Peşin' ? 'selected' : '' }}>Peşin</option>
                            <option value="30 Gün Vadeli" {{ old('payment_type', $proposal->payment_type) == '30 Gün Vadeli' ? 'selected' : '' }}>30 Gün Vadeli</option>
                            <option value="60 Gün Vadeli" {{ old('payment_type', $proposal->payment_type) == '60 Gün Vadeli' ? 'selected' : '' }}>60 Gün Vadeli</option>
                            <option value="Kredi Kartı" {{ old('payment_type', $proposal->payment_type) == 'Kredi Kartı' ? 'selected' : '' }}>Kredi Kartı</option>
                        </select>
                    </div>
                </div>

                <!-- Özet Bilgiler -->
                <div class="bg-slate-900 rounded-2xl p-8 shadow-2xl shadow-slate-200 relative overflow-hidden">
                    <div class="absolute top-0 right-0 -translate-y-1/2 translate-x-1/4 opacity-10">
                        <i class='bx bx-receipt text-[160px] text-white rotate-12'></i>
                    </div>

                    <h3 class="text-sm font-black text-slate-400 uppercase tracking-widest mb-8 relative z-10">Teklif Özeti</h3>
                    
                    <div class="space-y-4 relative z-10">
                        <div class="flex items-center justify-between text-slate-400">
                            <span class="text-xs font-bold">Brüt Toplam</span>
                            <span class="text-sm font-black text-white" x-text="formatCurrency(totals.raw_subtotal)"></span>
                        </div>

                        <div x-show="totals.line_discount > 0" class="flex items-center justify-between text-rose-400">
                            <span class="text-xs font-bold">Satır İndirimleri</span>
                            <span class="text-sm font-black" x-text="'-' + formatCurrency(totals.line_discount)"></span>
                        </div>

                        <div class="flex items-center justify-between text-slate-400 pt-2 border-t border-slate-800">
                            <span class="text-xs font-bold">Ara Toplam</span>
                            <span class="text-sm font-black text-white" x-text="formatCurrency(totals.subtotal)"></span>
                        </div>

                        <!-- Global İndirim Alanı -->
                        <div class="py-4 border-y border-slate-800 space-y-3">
                            <div class="flex items-center justify-between text-slate-400 mb-1">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-bold tracking-widest uppercase">Genel İndirim</span>
                                    <select name="discount_type" x-model="discount_type" class="text-[10px] bg-slate-800 text-slate-300 font-bold px-2 py-0.5 rounded-lg border-none outline-none focus:ring-1 focus:ring-indigo-500">
                                        <option value="fixed">Tutar</option>
                                        <option value="percentage">%</option>
                                    </select>
                                </div>
                                <input type="number" step="0.01" name="discount_value" x-model="discount_value" 
                                    class="w-20 bg-slate-800 border-none text-right text-white text-xs font-bold rounded-lg px-2 py-1 outline-none focus:ring-1 focus:ring-indigo-500">
                            </div>
                            <div x-show="totals.discount > 0" class="flex items-center justify-between text-rose-400">
                                <span class="text-[10px] font-bold">Uygulanan İndirim</span>
                                <span class="text-sm font-black" x-text="'-' + formatCurrency(totals.discount)"></span>
                            </div>
                        </div>

                        <div class="flex items-center justify-between text-slate-400">
                            <span class="text-xs font-bold">Toplam KDV</span>
                            <span class="text-sm font-black text-white" x-text="formatCurrency(totals.tax)"></span>
                        </div>
                        
                        <div class="h-px bg-slate-800 my-4"></div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-black text-indigo-400 uppercase tracking-widest">GENEL TOPLAM</span>
                            <span class="text-2xl font-black text-white" x-text="formatCurrency(totals.total)"></span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-3 mt-10 relative z-10">
                        <button type="button" @click="submitAs(status)" class="py-4 rounded-2xl bg-indigo-600 text-white text-sm font-black hover:bg-indigo-700 transition-all shadow-xl shadow-indigo-900/40">
                            GÜNCELLE VE KAYDET
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- New Customer Modal -->
<div x-data="{ open: false, loading: false }" 
     x-show="open" 
     @open-modal.window="if($event.detail === 'new-customer-modal') open = true"
     @close-modal.window="open = false"
     x-cloak
     class="fixed inset-0 z-[100] overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="open = false"></div>
        <div class="bg-white rounded-[40px] shadow-2xl w-full max-w-lg relative z-10 overflow-hidden border border-slate-100 p-10">
            <div class="flex items-center justify-between mb-8">
                <h3 class="text-2xl font-black text-slate-950 tracking-tight">Hızlı Müşteri Ekle</h3>
                <button @click="open = false" class="w-10 h-10 flex items-center justify-center rounded-full hover:bg-slate-50 text-slate-400 transition-all">
                    <i class='bx bx-x text-2xl'></i>
                </button>
            </div>

            <form @submit.prevent="
                loading = true;
                const formData = new FormData($el);
                fetch('{{ route('customers.store') }}', {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(res => res.json())
                .then(data => {
                    if(data.id) {
                        window.ts_customer.addOption({value: data.id, text: data.company_name + ' (' + data.contact_person + ')'});
                        window.ts_customer.setValue(data.id);
                        open = false;
                    }
                })
                .finally(() => loading = false)
            " class="space-y-6">
                @csrf
                <input type="hidden" name="type" value="legal">
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 px-1">Firma Adı <span class="text-rose-500">*</span></label>
                    <input type="text" name="company_name" required class="w-full h-12 px-5 rounded-2xl bg-slate-50 border border-slate-100 text-sm font-bold focus:outline-none focus:ring-4 focus:ring-indigo-600/5 focus:border-indigo-600 transition-all">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 px-1">İlgili Kişi</label>
                    <input type="text" name="contact_person" class="w-full h-12 px-5 rounded-2xl bg-slate-50 border border-slate-100 text-sm font-bold focus:outline-none focus:ring-4 focus:ring-indigo-600/5 focus:border-indigo-600 transition-all">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 px-1">E-Posta</label>
                    <input type="email" name="company_email" class="w-full h-12 px-5 rounded-2xl bg-slate-50 border border-slate-100 text-sm font-bold focus:outline-none focus:ring-4 focus:ring-indigo-600/5 focus:border-indigo-600 transition-all">
                </div>

                <div class="flex gap-4 pt-4">
                    <button type="button" @click="open = false" class="flex-1 py-4 rounded-2xl bg-slate-50 text-slate-500 text-sm font-bold hover:bg-slate-100 transition-all">VAZGEÇ</button>
                    <button type="submit" :disabled="loading" class="flex-1 py-4 rounded-2xl bg-indigo-600 text-white text-sm font-black hover:bg-indigo-700 transition-all shadow-xl shadow-indigo-100 disabled:opacity-50">
                        <span x-show="!loading">KAYDET</span>
                        <span x-show="loading">KAYDEDİLİYOR...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function proposalForm() {
    return {
        status: '{{ $proposal->status }}',
        items: {!! $proposal->items->map(fn($item) => [
            'product_id' => $item->product_id,
            'description' => $item->description,
            'quantity' => (float)$item->quantity,
            'unit' => $item->unit,
            'unit_price' => (float)$item->unit_price,
            'discount_type' => $item->discount_type,
            'discount_value' => (float)$item->discount_value,
            'tax_rate' => (float)$item->tax_rate,
            'showDiscount' => $item->discount_value > 0
        ])->toJson() !!},
        discount_type: '{{ $proposal->discount_type }}',
        discount_value: {{ (float)$proposal->discount_value }},

        init() {
            window.ts_customer = new TomSelect('#customer_select', {
                create: false,
                sortField: {field: 'text', direction: 'asc'},
                placeholder: 'Müşteri ara veya seç...',
                maxOptions: 100
            });
        },

        addItem() {
            this.items.push({ 
                product_id: null,
                description: '', 
                quantity: 1, 
                unit: 'Adet', 
                unit_price: 0, 
                discount_type: 'fixed',
                discount_value: 0,
                tax_rate: 20,
                showDiscount: false
            });
        },
        
        initProductSelect(el, index) {
            new TomSelect(el, {
                valueField: 'id',
                labelField: 'name',
                searchField: ['name', 'code'],
                create: true,
                placeholder: 'Ürün ara veya yeni ekle...',
                load: function(query, callback) {
                    var url = '{{ route("products.search") }}?q=' + encodeURIComponent(query);
                    fetch(url)
                        .then(response => response.json())
                        .then(json => {
                            callback(json);
                        }).catch(()=>{
                            callback();
                        });
                },
                maxItems: 1,
                render: {
                    option: function(item, escape) {
                        return `<div>
                            <span class="font-bold">${escape(item.name)}</span>
                            <div class="text-[10px] text-slate-400">
                                ${item.code ? `Kod: ${escape(item.code)} | ` : ''} 
                                Fiyat: ${parseFloat(item.price).toFixed(2)} |
                                Stok: ${item.stock_tracking ? item.stock : '∞'}
                            </div>
                        </div>`;
                    },
                    item: function(item, escape) {
                         return `<div>${escape(item.name)}</div>`;
                    },
                    no_results: function(data, escape) {
                        return '<div class="no-results">Bu ürün bulunamadı</div>';
                    },
                    option_create: function(data, escape) {
                        return '<div class="create"><b>' + escape(data.input) + '</b> Ekle...</div>';
                    },
                },
                onChange: (value) => {
                    if (value) {
                       const ts = el.tomselect;
                       const option = ts.options[value];
                       
                       // Check if it's a real product (has price property)
                       if (option && option.price !== undefined) {
                           this.items[index].product_id = option.id;
                           this.items[index].description = option.name;
                           this.items[index].unit_price = parseFloat(option.price);
                           this.items[index].tax_rate = option.vat_rate;
                           this.items[index].unit = option.unit;
                           
                           const priceInput = document.querySelectorAll('.unit-price-mask')[index];
                           if(priceInput && priceInput._imask) {
                               priceInput._imask.value = option.price.toString();
                           }
                           
                       } else {
                           // New Product (Created)
                           this.items[index].product_id = null;
                           this.items[index].description = value;
                       }
                    } else {
                        this.items[index].product_id = null;
                        this.items[index].description = '';
                    }
                }
            });
            
            // Note: For existing items, setting the init value is tricky via x-init because TomSelect replaces default behavior.
            // We might need to manually set it if the item already has a product_id.
            // However, with `load` remote data, the options aren't there yet.
            // It's cleaner to just let it be text for existing items unless we pre-fetch options.
            // For now, if it's text, it just shows text. If we want it to be a "selected product", we'd need to addOption first.
            const item = this.items[index];
            if(item.product_id) {
                // We add the option manually regarding current item so TomSelect knows about it
                el.tomselect.addOption({
                    id: item.product_id,
                    name: item.description,
                    price: item.unit_price, // potentially outdated but serves for initial display
                    code: '', // we don't have code here without backend change
                    vat_rate: item.tax_rate,
                    unit: item.unit,
                });
                el.tomselect.setValue(item.product_id);
            } else if (item.description) {
                // It was a free text or a created product.
                // If created, it's just text until synced?
                // `create: true` allows arbitrary text.
                el.tomselect.createItem(item.description);
            }
        },

        calculateLineDiscount(item) {
            const qty = parseFloat(item.quantity) || 0;
            const price = parseFloat(item.unit_price) || 0;
            const dValue = parseFloat(item.discount_value) || 0;
            const linePrice = qty * price;

            if (item.discount_type === 'percentage') {
                return (linePrice * dValue) / 100;
            }
            return dValue;
        },

        initMask(el, index) {
            const mask = IMask(el, {
                mask: Number,
                scale: 2,
                signed: false,
                thousandsSeparator: '.',
                padFractionalZeros: true,
                normalizeZeros: true,
                radix: ',',
                mapToRadix: ['.']
            });

            el._imask = mask;

            mask.on('accept', () => {
                this.items[index].unit_price = mask.unmaskedValue;
            });
            
            // Watch for external model changes (e.g., from product selection)
            this.$watch('items[' + index + '].unit_price', (value) => {
                 if (mask.unmaskedValue != value) {
                     mask.value = value ? value.toString() : '';
                 }
            });

            if(this.items[index].unit_price) {
                mask.value = this.items[index].unit_price.toString();
            }
        },

        removeItem(index) {
            if (this.items.length > 1) {
                this.items.splice(index, 1);
            }
        },

        get totals() {
            let rawSubtotal = 0;
            let lineDiscountTotal = 0;
            let totalTax = 0;
            
            this.items.forEach(item => {
                const qty = parseFloat(item.quantity) || 0;
                const price = parseFloat(item.unit_price) || 0;
                const dValue = parseFloat(item.discount_value) || 0;
                
                let linePrice = qty * price;
                let lineDiscount = 0;
                
                if (item.discount_type === 'percentage') {
                    lineDiscount = (linePrice * dValue) / 100;
                } else {
                    lineDiscount = dValue;
                }
                
                const linePriceAfterDiscount = Math.max(0, linePrice - lineDiscount);
                const lineTax = (linePriceAfterDiscount * (parseFloat(item.tax_rate) || 0)) / 100;
                
                rawSubtotal += linePrice;
                lineDiscountTotal += lineDiscount;
                totalTax += lineTax;
            });

            const netSubtotal = Math.max(0, rawSubtotal - lineDiscountTotal);

            // Global Discount
            let globalDiscount = 0;
            const gValue = parseFloat(this.discount_value) || 0;
            if (this.discount_type === 'percentage') {
                globalDiscount = (netSubtotal * gValue) / 100;
            } else {
                globalDiscount = gValue;
            }

            const taxableAmount = Math.max(0, netSubtotal - globalDiscount);
            
            if(netSubtotal > 0) {
                totalTax = totalTax * (taxableAmount / netSubtotal);
            }

            return { 
                raw_subtotal: rawSubtotal,
                line_discount: lineDiscountTotal,
                subtotal: netSubtotal, 
                discount: globalDiscount,
                tax: totalTax, 
                total: taxableAmount + totalTax 
            };
        },

        submitAs(status) {
            this.status = status;
            this.$nextTick(() => {
                document.getElementById('proposalForm').submit();
            });
        },

        formatCurrency(number) {
            return new Intl.NumberFormat('tr-TR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(number);
        }
    }
}
</script>

<style>
/* Tom Select Customization */
.ts-control {
    border: 1px solid #f1f5f9 !important;
    background-color: #f8fafc !important;
    padding: 10px 20px !important;
    border-radius: 16px !important;
    font-size: 14px !important;
    font-weight: 700 !important;
    color: #0f172a !important;
    box-shadow: none !important;
}
.ts-wrapper.focus .ts-control {
    border-color: #4f46e5 !important;
    ring: 4px rgba(79, 70, 229, 0.05) !important;
}
.ts-dropdown {
    border-radius: 20px !important;
    border: 1px solid #f1f5f9 !important;
    box-shadow: 0 10px 40px -10px rgba(0,0,0,0.1) !important;
    padding: 10px !important;
}
.ts-dropdown .option.active {
    background-color: #f5f3ff !important;
    color: #4f46e5 !important;
    border-radius: 12px !important;
}
.ts-dropdown .no-results {
    padding: 10px 20px;
    color: #94a3b8;
    font-size: 12px;
    font-weight: 600;
}
.ts-dropdown .create {
    padding: 10px 20px;
    color: #4f46e5;
    font-size: 13px;
    font-weight: 700;
}
</style>
@endsection
