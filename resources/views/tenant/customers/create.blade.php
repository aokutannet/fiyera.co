@extends('tenant.layouts.app')

@section('content')
<div class="max-w-7xl mx-auto space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-500">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('customers.index') }}" class="w-10 h-10 flex items-center justify-center rounded-xl bg-white border border-slate-100 text-slate-400 hover:text-slate-900 transition-all shadow-sm">
                <i class='bx bx-chevron-left text-2xl'></i>
            </a>
            <div>
                <h1 class="text-xl font-extrabold text-slate-950 tracking-tight">Yeni Müşteri Ekle</h1>
                <p class="text-slate-500 text-sm mt-1">Sisteme yeni bir müşteri veya tüzel kişilik kaydedin.</p>
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

    <form action="{{ route('customers.store') }}" method="POST" class="space-y-6">
        @csrf
        
        <!-- Tür Seçimi Card -->
        <div class="bg-white rounded-md p-4 md:p-8 border border-slate-100 shadow-sm space-y-6">
            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Müşteri Türü</label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <label class="relative flex-1 cursor-pointer group">
                        <input type="radio" name="type" value="legal" checked class="hidden peer">
                        <div class="p-6 rounded-xl border-2 border-slate-50 bg-slate-50/50 peer-checked:border-indigo-600 peer-checked:bg-indigo-50 transition-all duration-300">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-xl bg-white border border-slate-100 flex items-center justify-center text-indigo-600 shadow-sm">
                                    <i class='bx bx-buildings text-2xl'></i>
                                </div>
                                <div>
                                    <p class="text-sm font-black text-slate-900">Tüzel Kişi</p>
                                    <p class="text-[11px] text-slate-400 font-bold mt-0.5">LTD, AŞ, Şirketler vb.</p>
                                </div>
                            </div>
                        </div>
                        <div class="absolute top-4 right-4 text-indigo-600 opacity-0 peer-checked:opacity-100 transition-opacity">
                            <i class='bx bxs-check-circle text-xl'></i>
                        </div>
                    </label>

                    <label class="relative flex-1 cursor-pointer group">
                        <input type="radio" name="type" value="individual" class="hidden peer">
                        <div class="p-6 rounded-xl border-2 border-slate-50 bg-slate-50/50 peer-checked:border-indigo-600 peer-checked:bg-indigo-50 transition-all duration-300">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-xl bg-white border border-slate-100 flex items-center justify-center text-amber-600 shadow-sm">
                                    <i class='bx bx-user text-2xl'></i>
                                </div>
                                <div>
                                    <p class="text-sm font-black text-slate-900">Gerçek Kişi</p>
                                    <p class="text-[11px] text-slate-400 font-bold mt-0.5">Şahıs Şirketleri, Bireyler</p>
                                </div>
                            </div>
                        </div>
                        <div class="absolute top-4 right-4 text-indigo-600 opacity-0 peer-checked:opacity-100 transition-opacity">
                            <i class='bx bxs-check-circle text-xl'></i>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        <!-- Temel Bilgiler Card -->
        <div class="bg-white rounded-md p-4 md:p-8 border border-slate-100 shadow-sm">
            <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-8 border-b border-slate-50 pb-4">Temel Bilgiler</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="col-span-2">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 px-1">Firma Adı <span class="text-rose-500">*</span></label>
                    <input type="text" name="company_name" required value="{{ old('company_name') }}"
                        class="w-full h-12 px-5 rounded-2xl bg-slate-50 border border-slate-100 text-sm font-bold focus:outline-none focus:ring-4 focus:ring-indigo-600/5 focus:border-indigo-600 transition-all"
                        placeholder="Örn: ABC Teknoloji A.Ş.">
                </div>

                <div class="col-span-2">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 px-1">Firma Ünvanı</label>
                    <input type="text" name="legal_title" value="{{ old('legal_title') }}"
                        class="w-full h-12 px-5 rounded-2xl bg-slate-50 border border-slate-100 text-sm font-bold focus:outline-none focus:ring-4 focus:ring-indigo-600/5 focus:border-indigo-600 transition-all"
                        placeholder="Örn: ABC Yazılım ve Pazarlama Ticaret Limited Şirketi">
                </div>

                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 px-1">İlgili Kişi</label>
                    <input type="text" name="contact_person" value="{{ old('contact_person') }}"
                        class="w-full h-12 px-5 rounded-2xl bg-slate-50 border border-slate-100 text-sm font-bold focus:outline-none focus:ring-4 focus:ring-indigo-600/5 focus:border-indigo-600 transition-all"
                        placeholder="Örn: Ahmet Yılmaz">
                </div>

                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 px-1">Firma E-Posta</label>
                    <input type="email" name="company_email" value="{{ old('company_email') }}"
                        class="w-full h-12 px-5 rounded-2xl bg-slate-50 border border-slate-100 text-sm font-bold focus:outline-none focus:ring-4 focus:ring-indigo-600/5 focus:border-indigo-600 transition-all"
                        placeholder="Örn: info@firma.com">
                </div>

                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 px-1">Kategori</label>
                    <input type="text" name="category" value="{{ old('category') }}"
                        class="w-full h-12 px-5 rounded-2xl bg-slate-50 border border-slate-100 text-sm font-bold focus:outline-none focus:ring-4 focus:ring-indigo-600/5 focus:border-indigo-600 transition-all"
                        placeholder="Örn: Mimar, Tedarikçi vb.">
                </div>
            </div>
        </div>

        <!-- İletişim & Vergi Bilgileri Card -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-8">
            <div class="bg-white rounded-md p-4 md:p-8 border border-slate-100 shadow-sm h-full">
                <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-6 border-b border-slate-50 pb-4">İletişim</h3>
                <div class="space-y-6">
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 px-1">Mobil Tel</label>
                        <input type="text" name="mobile_phone" value="{{ old('mobile_phone') }}"
                            class="w-full h-12 px-5 rounded-2xl bg-slate-50 border border-slate-100 text-sm font-bold focus:outline-none focus:ring-4 focus:ring-indigo-600/5 focus:border-indigo-600 transition-all"
                            placeholder="05xx xxx xx xx">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 px-1">Sabit Tel</label>
                        <input type="text" name="landline_phone" value="{{ old('landline_phone') }}"
                            class="w-full h-12 px-5 rounded-2xl bg-slate-50 border border-slate-100 text-sm font-bold focus:outline-none focus:ring-4 focus:ring-indigo-600/5 focus:border-indigo-600 transition-all"
                            placeholder="02xx xxx xx xx">
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-md p-4 md:p-8 border border-slate-100 shadow-sm h-full">
                <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-6 border-b border-slate-50 pb-4">Vergi Bilgileri</h3>
                <div class="space-y-6">
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 px-1">VKN / TCKN</label>
                        <input type="text" name="tax_number" value="{{ old('tax_number') }}"
                            class="w-full h-12 px-5 rounded-2xl bg-slate-50 border border-slate-100 text-sm font-bold focus:outline-none focus:ring-4 focus:ring-indigo-600/5 focus:border-indigo-600 transition-all"
                            placeholder="10 haneli VKN veya 11 haneli TCKN">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 px-1">Vergi Dairesi</label>
                        <input type="text" name="tax_office" value="{{ old('tax_office') }}"
                            class="w-full h-12 px-5 rounded-2xl bg-slate-50 border border-slate-100 text-sm font-bold focus:outline-none focus:ring-4 focus:ring-indigo-600/5 focus:border-indigo-600 transition-all"
                            placeholder="Örn: Erenköy Vergi Dairesi">
                    </div>
                </div>
            </div>
        </div>

        <!-- Adres Bilgileri Card -->
        <div class="bg-white rounded-md p-4 md:p-8 border border-slate-100 shadow-sm">
            <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-8 border-b border-slate-50 pb-4">Adres Bilgileri</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 px-1">Ülke</label>
                    <input type="text" name="country" value="{{ old('country', 'Türkiye') }}"
                        class="w-full h-12 px-5 rounded-2xl bg-slate-50 border border-slate-100 text-sm font-bold focus:outline-none focus:ring-4 focus:ring-indigo-600/5 focus:border-indigo-600 transition-all">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 px-1">İl</label>
                    <input type="text" name="city" value="{{ old('city') }}"
                        class="w-full h-12 px-5 rounded-2xl bg-slate-50 border border-slate-100 text-sm font-bold focus:outline-none focus:ring-4 focus:ring-indigo-600/5 focus:border-indigo-600 transition-all"
                        placeholder="Örn: İstanbul">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 px-1">İlçe</label>
                    <input type="text" name="district" value="{{ old('district') }}"
                        class="w-full h-12 px-5 rounded-2xl bg-slate-50 border border-slate-100 text-sm font-bold focus:outline-none focus:ring-4 focus:ring-indigo-600/5 focus:border-indigo-600 transition-all"
                        placeholder="Örn: Kadıköy">
                </div>
            </div>

            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 px-1">Detaylı Adres</label>
                <textarea name="address" rows="3" 
                    class="w-full p-5 rounded-2xl bg-slate-50 border border-slate-100 text-sm font-bold focus:outline-none focus:ring-4 focus:ring-indigo-600/5 focus:border-indigo-600 transition-all"
                    placeholder="Mahalle, Cadde, Sokak, Kapı No vb.">{{ old('address') }}</textarea>
            </div>
        </div>

        <!-- Submit -->
        <div class="flex items-center justify-end gap-4 pt-4">
            <a href="{{ route('customers.index') }}" class="px-8 py-4 rounded-2xl text-sm font-bold text-slate-500 hover:text-slate-900 hover:bg-slate-100 transition-all">Vazgeç</a>
            <button type="submit" class="px-10 py-4 rounded-2xl bg-indigo-600 text-white text-sm font-black hover:bg-indigo-700 transition-all shadow-xl shadow-indigo-100 active:scale-[0.98]">
                MÜŞTERİYİ KAYDET
            </button>
        </div>
    </form>
</div>
@endsection
