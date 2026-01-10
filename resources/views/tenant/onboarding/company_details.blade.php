@extends('tenant.layouts.onboarding')

@section('content')


<!-- Progress Bar -->
<div class="fixed top-0 left-0 w-full h-1 z-[60]">
    <div class="h-full bg-indigo-600 w-full"></div>
</div>
<div class="w-full max-w-2xl mx-auto px-6 py-12">
    
    <div class="mb-10 text-center">
        <h1 class="text-3xl font-black text-slate-900 tracking-tight mb-2">Firma Bilgileri</h1>
        <p class="text-slate-500 font-medium">Teklif ve faturalarınız için gerekli resmi bilgiler.</p>
    </div>

    <!-- Progress Steps -->
    <div class="flex items-center justify-center gap-2 mb-12">
        <div class="h-1.5 w-8 rounded-full bg-slate-200"></div>
        <div class="h-1.5 w-8 rounded-full bg-black"></div>
        <div class="h-1.5 w-8 rounded-full bg-slate-200"></div>
        <span class="ml-3 text-xs font-bold text-slate-400 uppercase tracking-wider">Adım 2/3</span>
    </div>

    <div class="bg-white rounded-xl border border-slate-100 shadow-md shadow-slate-200/50 p-8 sm:p-10">
        <form action="{{ route('onboarding.company-details.store') }}" method="POST" class="space-y-6">
            @csrf
            
            <div class="space-y-5">
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Firma Adı <span class="text-red-500">*</span></label>
                    <input type="text" name="company_name" required value="{{ old('company_name', auth()->user()->tenant->name) }}" class="w-full h-12 px-4 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-black focus:ring-0 transition-all font-bold text-slate-900 placeholder-slate-400" placeholder="Örn: Acme A.Ş.">
                </div>

                <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Firma Ünvanı <span class="text-red-500">*</span></label>
                        <input type="text" name="tax_title" required value="{{ old('tax_title') }}" class="w-full h-12 px-4 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-black focus:ring-0 transition-all font-bold text-slate-900 placeholder-slate-400" placeholder="Resmi evraklarda geçecek tam ünvan">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Vergi Dairesi <span class="text-red-500">*</span></label>
                        <input type="text" name="tax_office" required value="{{ old('tax_office') }}" class="w-full h-12 px-4 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-black focus:ring-0 transition-all font-bold text-slate-900 placeholder-slate-400" placeholder="Vergi Dairesi">
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Vergi No <span class="text-red-500">*</span></label>
                        <input type="text" name="tax_number" required value="{{ old('tax_number') }}" class="w-full h-12 px-4 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-black focus:ring-0 transition-all font-bold text-slate-900 placeholder-slate-400" placeholder="Vergi No">
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Adres <span class="text-red-500">*</span></label>
                    <textarea name="company_address" required rows="3" class="w-full p-4 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-black focus:ring-0 transition-all font-bold text-slate-900 placeholder-slate-400 resize-none font-sans" placeholder="Açık adresiniz...">{{ old('company_address') }}</textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide">İl <span class="text-red-500">*</span></label>
                        <input type="text" name="province" required value="{{ old('province') }}" class="w-full h-12 px-4 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-black focus:ring-0 transition-all font-bold text-slate-900 placeholder-slate-400" placeholder="İl">
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide">İlçe <span class="text-red-500">*</span></label>
                        <input type="text" name="district" required value="{{ old('district') }}" class="w-full h-12 px-4 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-black focus:ring-0 transition-all font-bold text-slate-900 placeholder-slate-400" placeholder="İlçe">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Telefon <span class="text-red-500">*</span></label>
                        <input type="tel" name="company_phone" required value="{{ old('company_phone', auth()->user()->tenant->phone) }}" oninput="this.value = this.value.replace(/[^0-9\s\+\-]/g, '')" class="w-full h-12 px-4 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-black focus:ring-0 transition-all font-bold text-slate-900 placeholder-slate-400" placeholder="0555 555 55 55">
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide">E-posta <span class="text-red-500">*</span></label>
                        <input type="email" name="company_email" required value="{{ old('company_email', auth()->user()->tenant->email) }}" class="w-full h-12 px-4 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-black focus:ring-0 transition-all font-bold text-slate-900 placeholder-slate-400" placeholder="info@ornek.com">
                    </div>
                </div>
            </div>

            <div class="pt-8 border-t border-slate-100 flex items-center justify-between">
                <button type="button" onclick="history.back()" class="text-slate-400 text-sm font-bold hover:text-slate-600 transition-colors">Geri Dön</button>
                <button type="submit" class="px-8 py-4 bg-black text-white rounded-xl font-bold shadow-lg shadow-slate-200 hover:scale-105 hover:shadow-xl transition-all text-sm flex items-center gap-2">
                    Devam Et <i class='bx bx-right-arrow-alt text-lg'></i>
                </button>
            </div>

        </form>
    </div>
</div>
@endsection
