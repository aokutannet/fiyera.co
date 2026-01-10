@extends('tenant.layouts.app')

@section('content')
<!-- Tom Select CSS & JS -->
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<script src="https://unpkg.com/imask"></script>

<div class="max-w-8xl mx-auto space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-500" 
     x-data="proposalForm()">
    
    <!-- Header -->
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('proposals.index') }}" class="w-10 h-10 flex items-center justify-center rounded-xl bg-white border border-slate-100 text-slate-400 hover:text-slate-900 transition-all shadow-sm flex-shrink-0">
                <i class='bx bx-chevron-left text-2xl'></i>
            </a>
            <div>
                <h1 class="text-xl font-extrabold text-slate-950 tracking-tight">Yeni Teklif Oluştur</h1>
                <p class="text-slate-500 text-sm mt-1">Müşteriniz için profesyonel bir teklif hazırlayın.</p>
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

    <form action="{{ route('proposals.store') }}" method="POST" class="space-y-8" id="proposalForm">
        @csrf
        <input type="hidden" name="status" x-model="status">
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Sol Kolon: Teklif Bilgileri -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Temel Bilgiler -->
                <div class="bg-white rounded-md p-5 md:p-8 border border-slate-100 shadow-sm space-y-6">
                    <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-6 border-b border-slate-50 pb-4">Teklif Başlığı & Müşteri</h3>

                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="col-span-2">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 px-1">Teklif Başlığı <span class="text-rose-500">*</span></label>
                            <input type="text" name="title" required value="{{ old('title') }}"
                                class="w-full h-12 px-5 rounded-2xl bg-slate-50 border border-slate-100 text-sm font-bold focus:outline-none focus:ring-4 focus:ring-indigo-600/5 focus:border-indigo-600 transition-all"
                                placeholder="Örn: 2024 Yazılım Geliştirme Hizmeti">
                        </div>

                        <div class="relative">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 px-1">MÜŞTERİ SEÇİMİ <span class="text-rose-500">*</span></label>
                            
                            <select name="customer_id" id="customer_select" required class="w-full">
                                <option value="">Müşteri Seçin...</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                        {{ $customer->company_name }} ({{ $customer->contact_person }})
                                    </option>
                                @endforeach
                            </select>

                            <button type="button" @click="$dispatch('open-modal', 'new-customer-modal')" 
                                class="w-full mt-3 sm:mt-0 sm:w-auto sm:absolute sm:top-0 sm:right-0 justify-center flex items-center gap-1.5 px-3 py-1 rounded-lg bg-indigo-50 text-indigo-600 text-[10px] font-bold hover:bg-indigo-100 transition-all border border-indigo-100">
                                <i class='bx bx-plus-circle'></i> YENİ MÜŞTERİ EKLE
                            </button>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 px-1">Para Birimi <span class="text-rose-500">*</span></label>
                            <select name="currency" required class="w-full h-12 px-5 rounded-2xl bg-slate-50 border border-slate-100 text-sm font-bold focus:outline-none focus:ring-4 focus:ring-indigo-600/5 focus:border-indigo-600 transition-all appearance-none">
                                <option value="TRY" {{ old('currency', 'TRY') == 'TRY' ? 'selected' : '' }}>Türk Lirası (₺)</option>
                                <option value="USD" {{ old('currency') == 'USD' ? 'selected' : '' }}>Amerikan Doları ($)</option>
                                <option value="EUR" {{ old('currency') == 'EUR' ? 'selected' : '' }}>Euro (€)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Teklif Kalemleri -->
                <div class="bg-white rounded-md p-5 md:p-8 border border-slate-100 shadow-sm">
                    <div class="flex items-center justify-between mb-8 border-b border-slate-50 pb-4">

                        <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest">Teklif Kalemleri</h3>
                        <button type="button" @click="addItem()" class="h-9 px-4 rounded-xl bg-indigo-50 text-indigo-600 text-xs font-bold hover:bg-indigo-100 transition-all flex items-center gap-2">
                            <i class='bx bx-plus'></i> Kalem Ekle
                        </button>
                    </div>

                    <div class="space-y-4">
                        <template x-for="(item, index) in items" :key="index">
                            <div class="p-4 md:p-6 rounded-3xl bg-slate-50/50 border border-slate-100 relative group animate-in fade-in slide-in-from-left-4 space-y-4">
                                <!-- Ana Satır -->
                                <div class="grid grid-cols-12 gap-3 md:gap-5">
                                    <div class="col-span-12 lg:col-span-5">

                                        <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1.5 px-1">Ürün / Açıklama</label>
                                        <div class="relative">
                                            <input type="text" x-init="initProductSelect($el, index)" 
                                                class="w-full" placeholder="Ürün ara veya yeni ekle...">
                                            
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
                                    <div class="col-span-6 md:col-span-3 lg:col-span-3">
                                        <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1.5 px-1">Miktar</label>
                                        <div class="grid grid-cols-2 gap-2">
                                            <input type="number" step="0.01" :name="`items[${index}][quantity]`" x-model="item.quantity" required
                                                class="w-full h-10 px-3 rounded-xl bg-white border border-slate-200 text-xs font-bold focus:outline-none focus:ring-2 focus:ring-indigo-600/5 focus:border-indigo-600 transition-all text-center">
                                            <div>
                                                <select :name="`items[${index}][unit]`" x-model="item.unit" x-init="initUnitSelect($el, index)" class="w-full" style="display: none;">
                                                    <option value="Adet">Adet</option>
                                                    <option value="Saat">Saat</option>
                                                    <option value="Ay">Ay</option>
                                                    <option value="Gün">Gün</option>
                                                    <option value="Yıl">Yıl</option>
                                                    <option value="Çift">Çift</option>
                                                    <option value="Kg">Kg</option>
                                                    <option value="Gram">Gr</option>
                                                    <option value="Metre">Mt</option>
                                                    <option value="mt">mt</option>
                                                    <option value="Paket">Pkt</option>
                                                    <option value="Litre">Lt</option>
                                                    <option value="m2">m2</option>
                                                </select>
                                            </div>
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
                                    
                                    <div class="col-span-4 md:col-span-2 lg:col-span-1">
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
                                     class="flex flex-col sm:flex-row items-start sm:items-center gap-4 sm:gap-6 p-4 rounded-2xl bg-indigo-50/50 border border-indigo-100/50">
                                    <div class="flex items-center justify-between w-full sm:w-auto gap-3">
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
                                    <div class="w-full sm:flex-1 sm:max-w-[200px]">
                                        <div class="relative">
                                            <input type="number" step="0.01" :name="`items[${index}][discount_value]`" x-model="item.discount_value"
                                                class="w-full h-10 px-4 rounded-xl bg-white border border-indigo-100 text-xs font-bold focus:outline-none focus:ring-4 focus:ring-indigo-600/5 focus:border-indigo-600 transition-all"
                                                placeholder="İndirim miktarı...">
                                            <div class="absolute right-3 top-1/2 -translate-y-1/2 text-[10px] font-black text-indigo-300" x-text="item.discount_type === 'percentage' ? '%' : '₺'"></div>
                                        </div>
                                    </div>
                                    <div class="w-full sm:w-auto text-right sm:text-left text-[10px] font-bold text-slate-400">
                                        Satır İndirimi: <span class="text-indigo-600 font-black" x-text="formatCurrency(calculateLineDiscount(item))"></span>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Notlar -->
                <div class="bg-white rounded-md p-5 md:p-8 border border-slate-100 shadow-sm">
                    <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-6 border-b border-slate-50 pb-4">Teklif Notları & Şartlar</h3>

                    <textarea name="notes" rows="4" 
                        class="w-full p-5 rounded-2xl bg-slate-50 border border-slate-100 text-sm font-bold focus:outline-none focus:ring-4 focus:ring-indigo-600/5 focus:border-indigo-600 transition-all"
                        placeholder="Örn: Ödeme teklif onayından sonra 7 iş günü içerisinde peşin olarak yapılacaktır.">{{ old('notes') }}</textarea>
                </div>
            </div>

            <!-- Sağ Kolon: Tarihler ve Özet -->
            <div class="space-y-8">
                <!-- Tarih ve Ek Bilgiler -->
                <div class="bg-white rounded-md p-5 md:p-8 border border-slate-100 shadow-sm space-y-6">
                    <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-6 border-b border-slate-50 pb-4">Tarih & Ödeme</h3>

                    
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 px-1">Teklif Tarihi <span class="text-rose-500">*</span></label>
                        <input type="date" name="proposal_date" required value="{{ old('proposal_date', date('Y-m-d')) }}"
                            class="w-full h-12 px-5 rounded-2xl bg-slate-50 border border-slate-100 text-sm font-bold focus:outline-none focus:ring-4 focus:ring-indigo-600/5 focus:border-indigo-600 transition-all">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 px-1">Geçerlilik Tarihi</label>
                            <input type="date" name="valid_until" value="{{ old('valid_until') }}"
                                class="w-full h-12 px-5 rounded-2xl bg-slate-50 border border-slate-100 text-sm font-bold focus:outline-none focus:ring-4 focus:ring-indigo-600/5 focus:border-indigo-600 transition-all">
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 px-1">Teslim Tarihi</label>
                            <input type="date" name="delivery_date" value="{{ old('delivery_date') }}"
                                class="w-full h-12 px-5 rounded-2xl bg-slate-50 border border-slate-100 text-sm font-bold focus:outline-none focus:ring-4 focus:ring-indigo-600/5 focus:border-indigo-600 transition-all">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 px-1">Ödeme Türü</label>
                        <div class="payment-type-wrapper">
                            <select name="payment_type" x-init="initPaymentSelect($el)" class="w-full">
                                <option value="">Seçiniz...</option>
                                <option value="Nakit">Nakit</option>
                                <option value="Havale/EFT">Havale/EFT</option>
                                <option value="Kredi Kartı">Kredi Kartı</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Özet Bilgiler -->
                <div class="bg-slate-900 rounded-2xl p-6 md:p-8 shadow-2xl shadow-slate-200 relative overflow-hidden">
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

                    <div class="grid grid-cols-2 gap-3 mt-10 relative z-10">
                        <button type="button" @click="submitAs('draft')" class="py-4 rounded-2xl bg-white/10 text-white text-xs font-bold border border-white/10 hover:bg-white/20 transition-all">
                            TASLAK KAYDET
                        </button>
                        <button type="button" @click="submitAs('pending')" class="py-4 rounded-2xl bg-indigo-600 text-white text-xs font-black hover:bg-indigo-700 transition-all shadow-xl shadow-indigo-900/40">
                            TEKLİF OLUŞTUR
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
        <div class="bg-white rounded-3xl md:rounded-[40px] shadow-2xl w-full max-w-lg relative z-10 overflow-hidden border border-slate-100 p-6 md:p-10">
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
        status: 'pending',
        items: [{ 
            product_id: null,
            description: '', 
            quantity: 1, 
            unit: 'Adet', 
            unit_price: 0, 
            discount_type: 'fixed',
            discount_value: 0,
            tax_rate: 20,
            showDiscount: false
        }],
        discount_type: 'fixed',
        discount_value: 0,

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

        initUnitSelect(el, index) {
            new TomSelect(el, {
                create: true,
                sortField: {field: 'text', direction: 'asc'},
                placeholder: 'Birim',
                maxOptions: 50,
                plugins: ['no_backspace_delete'],
                onItemAdd: function(){
                    this.setTextboxValue('');
                    this.refreshOptions();
                },
                render: {
                    option: function(data, escape) {
                        return '<div>' + escape(data.text) + '</div>';
                    },
                    item: function(data, escape) {
                        return '<div>' + escape(data.text) + '</div>';
                    }
                },
                onChange: (value) => {
                    this.items[index].unit = value;
                }
            });
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

        initProductSelect(el, index) {
            new TomSelect(el, {
                valueField: 'id',
                labelField: 'name',
                searchField: ['name', 'code'],
                create: true,
                placeholder: 'Ürün ara...',
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
                       
                       // Check if it's a real product from backend (has price property)
                       // distinct check for 'undefined' to allow 0 price
                       if (option && option.price !== undefined && option.price !== null) {
                           // Existing Product Selected
                           this.items[index].product_id = option.id;
                           this.items[index].description = option.name;
                           this.items[index].unit_price = parseFloat(option.price);
                           this.items[index].tax_rate = option.vat_rate;
                           this.items[index].unit = option.unit;
                           
                           // Update mask
                           const priceInput = document.querySelectorAll('.unit-price-mask')[index];
                           if(priceInput && priceInput._imask) {
                               priceInput._imask.value = option.price.toString();
                           }
                           
                       } else {
                           // New Product (Created) or text fallback
                           this.items[index].product_id = null;
                           
                           // Use option name/text if available, otherwise value
                           // This prevents ID from being used as description if option lookup fails partially
                           this.items[index].description = option ? (option.name || option.text || value) : value;
                       }
                    } else {
                        // Cleared
                        this.items[index].product_id = null;
                        this.items[index].description = '';
                    }
                }
            });
        },

        initPaymentSelect(el) {
            new TomSelect(el, {
                create: true,
                sortField: {field: 'text', direction: 'asc'},
                placeholder: 'Ödeme Türü Seçin veya Yazın...',
                maxOptions: 50,
                plugins: ['no_backspace_delete'],
                render: {
                    option: function(data, escape) {
                        return '<div>' + escape(data.text) + '</div>';
                    },
                    item: function(data, escape) {
                        return '<div>' + escape(data.text) + '</div>';
                    },
                    option_create: function(data, escape) {
                        return '<div class="create"><b>' + escape(data.input) + '</b> Ekle...</div>';
                    }
                }
            });
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

            // Store instance on element to access later
            el._imask = mask;

            mask.on('accept', () => {
                this.items[index].unit_price = mask.unmaskedValue;
            });
            
            // Initial value or Update when model changes
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
            
            // Adjust tax proportionally for global discount
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
.ts-wrapper {
    width: 100% !important;
    min-width: 0 !important; /* Fix flex child constraints */
}
.ts-control {
    border: 1px solid #e2e8f0 !important;
    background-color: #ffffff !important;
    padding: 0 12px !important;
    padding-right: 24px !important;
    border-radius: 12px !important;
    font-size: 12px !important; /* Match adjacent text-xs */
    font-weight: 700 !important;
    color: #334155 !important;
    min-height: 40px !important;
    height: 40px !important;
    display: flex !important;
    align-items: center !important;
    box-shadow: none !important;
}

/* Specific Style for Payment Type (Big Select) */
.payment-type-wrapper .ts-control {
    min-height: 48px !important; /* h-12 */
    height: 48px !important;
    background-color: #f8fafc !important; /* bg-slate-50 */
    border-radius: 1rem !important; /* rounded-2xl */
    font-size: 14px !important; /* text-sm */
}
.ts-control .item {
    font-size: 12px !important;
    font-weight: 700 !important;
    color: #334155 !important;
    vertical-align: middle !important;
}
.ts-wrapper.focus .ts-control {
    border-color: #4f46e5 !important;
    box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.05) !important;
}
.ts-dropdown {
    border-radius: 16px !important;
    border: 1px solid #e2e8f0 !important;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
    padding: 6px !important;
    font-size: 12px !important;
    z-index: 50 !important;
    margin-top: 8px !important;
}
.ts-dropdown .option {
    padding: 10px 12px !important;
    border-radius: 10px !important;
    font-weight: 600 !important;
    color: #64748b !important; 
}
.ts-dropdown .option.active {
    background-color: #f5f3ff !important;
    color: #4f46e5 !important;
}
/* Fix Input Input Group Alignment */
.ts-wrapper.single .ts-control:after {
    display: none !important; /* We will use background image if needed or just clean look */
}
/* Custom Arrow */
.ts-control {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%2394a3b8' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e") !important;
    background-position: right 0.5rem center !important;
    background-size: 1.5em 1.5em !important;
    background-repeat: no-repeat !important;
}
</style>
@endsection
